<?php
	
	
	include_once('med_config.php');
	
	include_once(WEB_ROOT.'base/MedXmlParser.php');
	
	include_once(WEB_ROOT.'base/DB.php');
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	include_once(WEB_ROOT.'base/MedResponseBuilder.php');
	
	
	
	include_once(WEB_ROOT.'base/MedArchiveEpcs.php');
	
	include_once(WEB_ROOT.'base/MedSimpleMailer.php');
	
	
	
	$ERROR = '';
	$ERROR_CODE = '';
	$ERROR_DESCRIPTION_CODE = '';
	$ERROR_DESCRIPTION = '';
	
	
	$Xml	=	file_get_contents("php://input");
	
	
	
	
	
	$Headers = apache_request_headers();
	
	
	$AuthHeader = explode(' ', $Headers['Authorization']);
	$AuthHeader = base64_decode($AuthHeader[1]);
	
	
	if($AuthHeader !== SRX_LOGIN_ID . ':' . SRX_PASSWORD)
	{
	    
	    echo 'Invalid authentication header.';
	    exit;
	}
	
	
	$Response = MedXmlParser::validateXml($Xml);
	if($Response != 1)
	{
	    echo $Response;
	    exit;
	}
	
	
	
	
	
	
	
	
	
	$objXml = new MedXmlParser($Xml);
	
	
	
	$strFromID = $objXml->From->Value;
	
	
	if($objXml->From->attributes['QUALIFIER'] == 'P')
	    $strFromEntityType = 'PHARMACY';
	else
	    $strFromEntityType = 'PRESCRIBER';
	
	
	
	$strToID = $objXml->To->Value;
	
	
	if($objXml->To->attributes['QUALIFIER'] == 'P')
	    $strToEntityType = 'PHARMACY';
	else
	    $strToEntityType = 'PRESCRIBER';
	
	
	$MessageID = $objXml->MessageID->Value;
	$RelatedToMessageID = $objXml->RelatesToMessageID->Value;
	
	
	
	$strMessageType = $objXml->getBodyFirstElement();
	
	
	
	switch($strToEntityType)
	{
	    CASE 'PRESCRIBER':
		    $MasterTable = PRESCRIBER_MASTER_TABLE;
		    $PkField = 'spi';
		    break;
	    CASE 'PHARMACY':
		    $MasterTable = PHARMACY_MASTER_TABLE;
		    $PkField = 'ncpdpid';
		    break;
	    DEFAULT:
		    $LISTENER_LOG_FILE = 'LSTNR_ENTITY_TYPE_ERR_' . mktime();
		    
		    break;
	}

	
	$ValidateReceiverSQL = "SELECT mt_tran_id FROM " . $MasterTable . " WHERE " . $PkField . " = '" . $strToID . "'";
	$rsReceiver = $medDB->GetRow($ValidateReceiverSQL);
	
	if(count($rsReceiver) > 0)
	{
	    $ProcessMessage = TRUE;
	    $ERROR = FALSE;
	}
	else
	{
	    $ProcessMessage = TRUE;
	    $ERROR = FALSE;
	    
	    
	     
	    $ERROR_DESCRIPTION = 'Receiver not found in our directory list. But we have successfully accepted the message.';
	}
	
	
	
	
	$intStatus = isUniqueMessage($MessageID, $strFromID);
	if($intStatus == 0)
	{
	    $ERROR = TRUE;
	    $ERROR_CODE = '900';
	    $ERROR_DESCRIPTION_CODE = '220';
	    $ERROR_DESCRIPTION = 'Message is a duplicate';
	    $ProcessMessage = FALSE;
	}
	else if($intStatus == -1) 
	{
		$ERROR = TRUE;
		$ERROR_CODE = '602';
		$ERROR_DESCRIPTION_CODE = '007';
		$ERROR_DESCRIPTION = 'Internal processing error has occured.';
		$ProcessMessage = FALSE;
		
		
		$strErrorDescriptionForLogging	=	$medDB->ErrorMsg();
		
	}
	
	
	
	
	$blnEpcsArchived	=	false;
	
	$strEpcsSignedText		=	"";
	$strEpcsMessageDigest	=	"";
	$strEpcsPlainText		=	"";
	
	if($ProcessMessage	=== TRUE)
	{				
		
		$blnFillableMessage		=	false;
		if($strMessageType	== 'NEWRX')
		{
			$blnFillableMessage		=	true;
		}
		elseif ($strMessageType == 'REFRES')
		{
			$strRefillResponseStatus	=	$objXml->getRefillResponseStatus();
				
			if($strRefillResponseStatus == 'APPROVED' || $strRefillResponseStatus == 'APPROVEDWITHCHANGES')
			{
				$blnFillableMessage		=	true;
			}
		}
		
		
		$blnControlledSubstance		=	false;
		if(is_array($objXml->DEASchedule) || $objXml->DEASchedule->Value	!=	"")
		{
			$blnControlledSubstance =	true;
		}
		
		
		
		if($blnControlledSubstance === true && $blnFillableMessage === true)
		{
			
			$blnSiFlagPresent	= false;
			if(is_array($objXml->DrugCoverageStatusCode))
			{
				foreach($objXml->DrugCoverageStatusCode as $objDrugCoverageStatusCode)
				{
					if($objDrugCoverageStatusCode->Value == "SI")
					{
						$blnSiFlagPresent	= true;
					}
				}
			}
			else
			{
				if($objXml->DrugCoverageStatusCode->Value == "SI")
				{
					$blnSiFlagPresent	=	true;
				}
			}
			
			
			if($blnSiFlagPresent == false)
			{
				$ERROR			=	true;
				$ERROR_CODE		=	'900';
				$ERROR_DESCRIPTION	=	'Digital Signature Not Supported';
				$ProcessMessage	=	false;
			}
			else
			{
				
				$objArchiveEpcs		=	new MedArchiveEpcs($Xml,EPCS_WEB_SERVICE_URL);
				$intEpcsArchiveResult	=	$objArchiveEpcs->archivePrescription();
				
				if($intEpcsArchiveResult == 1)
				{
					$blnEpcsArchived	=	true;
					
					$strEpcsSignedText	=	$objArchiveEpcs->getSignedText();
					$strEpcsMessageDigest	=	$objArchiveEpcs->getMessageDigest();
					$strEpcsPlainText	=	$objArchiveEpcs->getPlainText();
				}
				else
				{
					$ERROR				=	true;
					$ERROR_CODE 		=	'602';
					$ERROR_DESCRIPTION	=	'Internal processing error has occured.';
					$ProcessMessage		=	false;
					
					$strErrorDescriptionForLogging		=	$objArchiveEpcs->getError();
					
				}
			}
		}
	}
	
	
	
	if($ProcessMessage === TRUE)
	{
	    
	    $arrRecord				=	array();
	    $arrRecord['to_id']			=	$strToID;
	    $arrRecord['from_id']			=	$strFromID;
	    $arrRecord['message_id']		=	$MessageID;
	    $arrRecord['related_message_id']	=	$RelatedToMessageID;
	    $arrRecord['sent_time']			=	convertToMySqlDateTime($objXml->SentTime->Value);
	    $arrRecord['sms_version']		=	$objXml->SMSVersion->Value;
	    $arrRecord['app_name']			=	$objXml->SenderSoftwareProduct->Value;
	    $arrRecord['app_version']		=	$objXml->SenderSoftwareVersionRelease->Value;
	    $arrRecord['vendor_name']		=	$objXml->SenderSoftwareDeveloper->Value;
	    $arrRecord['sent_time_in_message']	=	convertToMySqlDateTime($objXml->SentTime->Value);
	    $arrRecord['received_time']		=	date('Y-m-d H:i:s');
	    $arrRecord['edi_message']		=	base64_encode($Xml);
	    $arrRecord['message_status']	=	'Pending';
	    $arrRecord['message_version']	=	MESSAGE_VERSION;
		$arrRecord['message_type']		=	$objXml->getBodyFirstElement();

		
		if($blnEpcsArchived === true)
		{
			$arrRecord['epcs_signed_text']		=	$strEpcsSignedText;
			$arrRecord['epcs_message_digest']	=	$strEpcsMessageDigest;
			$arrRecord['epcs_plain_text']		=	$strEpcsPlainText;
		}
		
		
	    
	    
	    
	    
	    
	    $medDB->AutoExecute(INBOX_MESSAGE_TABLE,$arrRecord,'INSERT');
	}
	
	
	
	
	if($MessageID === 0 || $MessageID === '0')
	{
	    exit;
	}
	
	if($ERROR === TRUE)
	{
	    $MessageFunction = 'ERROR';
	    $StatusCode = $ERROR_CODE;
	}
	else
	{
	    $MessageFunction = 'STATUS';
	    $StatusCode = '000';
	}
	$objResponder = new MedResponseBuilder($MessageFunction, USE_DEFINED_DELIMITERS);
	$objResponder->MESSAGE_ID = md5(mktime());					
	$objResponder->RELATES_TO_MESSAGE_ID = $MessageID;	
	$objResponder->SENT_TIME = mktime();
	
	
	$objResponder->APP_NAME = APP_NAME;
	$objResponder->APP_VERSION = APP_VERSION;
	$objResponder->VENDOR_NAME = VENDOR_NAME;
	
	
	
	
	
	$objResponder->STATUS_CODE = $StatusCode;
	
	
	if($ERROR === TRUE)
	{
	    $objResponder->ERROR_DESCRIPTION_CODE = $ERROR_DESCRIPTION_CODE;
	    $objResponder->ERROR_DESCRIPTION = $ERROR_DESCRIPTION;
	}
	
	
	if($strFromEntityType == 'PHARMACY')
	{
	    $objResponder->TO_ID = $strFromID;
	    $objResponder->TO_IDENTIFIER = 'P';
	    $objResponder->TO_MAIL_ADDRESS = $strFromID . '.ncpdp@surescripts.com'; 
	}
	else if($strFromEntityType == 'PRESCRIBER')
	{
	    $objResponder->TO_ID = $strFromID;
	    $objResponder->TO_IDENTIFIER = 'D';
	    $objResponder->TO_MAIL_ADDRESS = $strFromID . '.spi@surescripts.com'; 
	}
	else
	{
	    $objResponder->TO_ID = $strFromID;
	    $objResponder->TO_IDENTIFIER = 'ZZZ';
	    $objResponder->TO_MAIL_ADDRESS = $strFromID . '.dp@surescripts.com'; 
	}
	
	if($strToEntityType == 'PHARMACY')
	{
	    $objResponder->FROM_ID = $strToID;
	    $objResponder->FROM_IDENTIFIER = 'P';
	    $objResponder->FROM_MAIL_ADDRESS = $strToID . '.ncpdp@surescripts.com'; 
	}
	else if($strToEntityType == 'PRESCRIBER')
	{
	    $objResponder->FROM_ID = $strToID;
	    $objResponder->FROM_IDENTIFIER = 'D';
	    $objResponder->FROM_MAIL_ADDRESS = $strToID . '.spi@surescripts.com'; 
	}
	else
	{
	    $objResponder->FROM_ID = DATA_PROVIDER_ID;
	    $objResponder->FROM_IDENTIFIER = 'ZZZ';
	    $objResponder->FROM_MAIL_ADDRESS = DATA_PROVIDER_ID . '.dp@surescripts.com'; 
	}
	
	$XmlResponse = $objResponder->getResponse();
	
	
	header("Authorization: Basic ".base64_encode(MSG_LOGIN_ID.':'.MSG_PASSWORD));
	header("Content-Type: application/xml; charset=UTF-8");
	header("Content-length: ".(strlen($XmlResponse)));
	
	
	echo $XmlResponse;
	if($MessageFunction == 'ERROR')
	{
		
		
		if($ERROR_CODE	== '602')
		{
			sendEmailAlert($MessageID,$strErrorDescriptionForLogging,$strMessageType);
		}
		
		
		error_log($StatusCode.' - '.$MessageID);
	}
	
	
	
	function isUniqueMessage($MessageID, $Sender, $CheckInContextOfSender = TRUE)
	{
	    global $medDB;
	    if($CheckInContextOfSender === TRUE)
	    {
		    $strSQLExcerpt = " AND from_id = '" . $Sender . "'"; 
	    }
	    $rsMessage = $medDB->getRow("SELECT tran_id FROM " . INBOX_MESSAGE_TABLE . " WHERE message_id = '" . $MessageID . "'" . $strSQLExcerpt);

		$intResult;
		if(isset($rsMessage) && is_array($rsMessage))
		{
			if(count($rsMessage) == 0)
			{
				$intResult = 1;
			}
			else if(count($rsMessage) > 0)
			{
				$intResult = 0;	
			}
		}
		else
		{
			$intResult = -1;
		}
	    return $intResult;
	}

	
	function getDelimitersFromEDIFact($EDIFactMessage)
	{
	    $intUNAPosition = strpos($EDIFactMessage, 'UNA');
	    $intUIBPosition = strpos($EDIFactMessage, 'UIB');
	    if($intUNAPosition !== FALSE && $intUIBPosition !== FALSE)
	    {
		    $EDIFactMessage = str_replace(array('UNA'), array(''), $EDIFactMessage);
		    $arrEDIFactMessage = explode('UIB',$EDIFactMessage);
		    $UNA = $arrEDIFactMessage[0];
		    if(strlen($UNA) != 6)
		    {
			    return FALSE;
		    }
		    else
		    {
			    $UNA = str_split($UNA);
			    return $UNA;
		    }
	    }
	    else
	    {
		    return FALSE;	
	    }
	}
	
	
	function extractElementData($strXml, $strElement, $blnExcludeElement = true)
	{
	    
	    $intStartPosMargin = $intEndPosMargin = 0;
	    if($blnExcludeElement == true)
	    {
		    $intStartPosMargin = strlen($strElement) + 2;
		    $intEndPosMargin = strlen($strElement) - 3;
	    }
	    
	    $intStartPos = strpos($strXml, '<' . $strElement . '>') + $intStartPosMargin;
	    
	    $intEndPos = strpos($strXml, '</' . $strElement . '>') + $intEndPosMargin; 

	    
	    $strXmlParts = substr($strXml, $intStartPos, $intEndPos);

	    
	    return $strXmlParts;
	}
	
	
	
	function sendEmailAlert($strMessageId,$strErrorDescription,$strMessageType)
	{
		$Mailer = new MedSimpleMailer();
		
		$strBody	=	"Dear Admin, an error occured while receiving ".$strMessageType.".<br /><br />";
		$strBody	.=	"Message ID: ".$strMessageId."<br /><br />";
		$strBody	.=	"Time: ".date("F j, Y, g:i a")."<br /><br />";
		$strBody	.=	"Error Description: ".$strErrorDescription."<br /><br />";
		
		$Mailer->sendEmail(LISTENER_ERROR_EMAIL_TO, LISTENER_ERROR_EMAIL_FROM, LISTENER_ERROR_EMAIL_SUBJECT, $strBody);
	}
	
?>