<?php
	
	
	include_once('med_config.php');
	
	include_once(WEB_ROOT.'base/MedXmlParser.php');
	
	include_once(WEB_ROOT.'base/DB.php');
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	include_once(WEB_ROOT.'base/MedResponseBuilder.php');
	
	
	$Xml	=	file_get_contents("php://input");
	
	
	
	
	$Response			=	MedXmlParser::validateXml($Xml);
	if($Response != 1)
	{
		echo $Response;
		exit;
	}
	
	
	
	
	
	
	
	
	
	$objXml	=	new MedXmlParser($Xml);
	
	
	
	$strCompleteFromID		=	$objXml->From->Value;
	$strFromID				=	extractIDFromMailAddress($strCompleteFromID);
	$strFromEntityType		=	getEntityTypeFromMailAddress($strCompleteFromID);
	
	
	
	$strCompleteToID		=	$objXml->To->Value;
	$strToID				=	extractIDFromMailAddress($strCompleteToID);
	$strToEntityType		=	getEntityTypeFromMailAddress($strCompleteToID);
	
	
	$MessageID = $objXml->MessageID->Value;
	file_put_contents('c:/t.txt',$MessageID);
	$RelatedToMessageID = $objXml->RelatesToMessageID->Value;
	
	
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
			file_put_contents(LOG_DIR . $LISTENER_LOG_FILE, $Xml);
			break;
	}
	$ValidateReceiverSQL = "SELECT mt_tran_id FROM " . $MasterTable . " WHERE " . $PkField . " = '" . $strToID . "'";
	$rsReceiver = $medDB->GetRow($ValidateReceiverSQL);
	
	
	file_put_contents(LOG_DIR . 'REC-SQL_MSGID_' . $MessageID . '_' . mktime(). '.txt',$ValidateReceiverSQL . var_export($rsReceiver,true));
	if(count($rsReceiver) > 0)
	{
		$ProcessMessage = TRUE;
		$ERROR = FALSE;
	}
	else
	{
		$ProcessMessage = FALSE;
		$ERROR = TRUE;
		$ErrorCode = '002';
	}
	
	
	if(LISTEN_MESSAGES_FOR_RECEIVERS_WHICH_ARE_NOT_ON_FILE === TRUE)
	{
		$ProcessMessage = TRUE;
	}
	
	if($ProcessMessage === TRUE)
	{
		
		$arrRecord						=	array();
		$arrRecord['to_id']				=	$strToID;
		$arrRecord['from_id']			=	$strFromID;
		$arrRecord['message_id']		=	$MessageID;
		$arrRecord['related_message_id']=	$RelatedToMessageID;
		$arrRecord['sent_time']			=	convertToMySqlDateTime($objXml->SentTime->Value);
		$arrRecord['sms_version']		=	$objXml->SMSVersion->Value;
		$arrRecord['app_name']			=	$objXml->ApplicationName->Value;
		$arrRecord['app_version']		=	$objXml->ApplicationVersion->Value;
		$arrRecord['vendor_name']		=	$objXml->VendorName->Value;
		$arrRecord['sent_time_in_message']	=	convertToMySqlDateTime($objXml->SentTime->Value);
		$arrRecord['received_time']		=	date('Y-m-d H:i:s');
		$arrRecord['edi_message']		=	$objXml->EDIFACTMessage->Value;
		$arrRecord['message_status']	=	'Pending';
		
		
		$medDB->AutoExecute(INBOX_MESSAGE_TABLE,$arrRecord,'INSERT');
		
		
		archiveInboxMessageToFileSystem($objXml->MessageID->Value, $Xml, $strToID);
	}
	
	
	
	
	if($MessageID === 0 || $MessageID === '0')
	{
		file_put_contents(LOG_DIR . 'MSG-ERR_' . $MessageID . '_' . mktime(). '.txt',$XmlResponse);
		exit;
	}
	
	if($ERROR === TRUE)
	{
		$MessageFunction = 'ERROR';
		$StatusCode = $ErrorCode;
	}
	else
	{
		$MessageFunction = 'STATUS';
		$StatusCode = '000';
	}
	$objResponder = new MedResponseBuilder($MessageFunction, USE_DEFINED_DELIMITERS);
	$objResponder->MESSAGE_ID = '0';					
	$objResponder->RELATES_TO_MESSAGE_ID = $MessageID;	
	$objResponder->SENT_TIME = mktime();
	
	
	
	
	$objResponder->STATUS_CODE = $StatusCode;

	
	
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
	
	
	file_put_contents(LOG_DIR . 'RES-OBJ_MSGID_' . $MessageID . '_' . mktime(). '.txt',var_export($objResponder,true));
	file_put_contents(LOG_DIR . 'ECHO_MSGID_' . $MessageID . '_' . mktime(). '.txt',$XmlResponse);
	
?>