<?php
	
	
	set_time_limit(0);

	
	include_once('med_config.php');
	
	include_once(WEB_ROOT.'base/MedXmlParser.php');
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	
	include_once(WEB_ROOT.'base/DB.php');
	
	
	include_once(WEB_ROOT.'base/MedOutMessage.php');
	
	
	include(WEB_ROOT.'base/MedRequest.php');
	
	
	
	
	
	sleep(10);
	
	if(canStartNewJob() == true)
	{
		
		session_start();
		
		$_SESSION['JOB_ID']	=	logCurrentJob();
		
		
		while(true === true)
		{
			$strSQL			=	'SELECT * FROM ' . OUTBOX_MESSAGE_TABLE . ' WHERE message_status = \'Pending\'';
			$rsOutMessages	=	$medDB->GetAll($strSQL);
			if(count($rsOutMessages) == 0)
			{
				
				sleep(5);
				updateCurrentJob($_SESSION['JOB_ID'],'',0);
				continue;
			}
			
			foreach($rsOutMessages as $arrOutMessage)
			{
				if($arrOutMessage['message_from'] == 'PRESCRIBER' || $arrOutMessage['message_from'] == 'PHARMACY')
				{
					
					$objOutMessage				=	new MedOutMessage('MESSAGE_FROM_' . $arrOutMessage['message_from']);
					
					
					$objOutMessage->To			=	$arrOutMessage['to_id'];
					$objOutMessage->From		=	$arrOutMessage['from_id'];
					
					
					if(trim($arrOutMessage['message_id']) != '')
					{
						$objOutMessage->MessageID	=	$arrOutMessage['message_id'];
					}
					else
					{
						
						
						$objOutMessage->MessageID	=	'MID' . $arrOutMessage['meditab_id'] . 'TID' . $arrOutMessage['meditab_tran_id'];
						$strMessageIDUpdate			=	", message_id = '" . $objOutMessage->MessageID . "'";
					}
					
					$objOutMessage->RelatedToMessageID	=	$arrOutMessage['related_message_id'];
					$objOutMessage->SentTime	=	getUTCTime(strtotime($arrOutMessage['sent_time']));
					$objOutMessage->SmsVersion	=	$arrOutMessage['sms_version'];
					$objOutMessage->VendorName	=	$arrOutMessage['vendor_name'];
					$objOutMessage->AppName		=	$arrOutMessage['app_name'];
					$objOutMessage->AppVersion	=	$arrOutMessage['app_version'];
					
					$objOutMessage->EDIMessage	=	base64_encode(str_replace(array('<<MESSAGE_ID_PLACE_HOLDER>>'),array($objOutMessage->MessageID),$arrOutMessage['edi_message']));
					$objOutMessage->OMTranID		=	$arrOutMessage['tran_id'];
					
					$strMessage					=	$objOutMessage->getMessage();
					
					
					$objMedRequest				=	new MedRequest(MSG_URL);
					
					
					$objMedRequest->addHeader("Authorization: Basic ".base64_encode(MSG_LOGIN_ID.':'.MSG_PASSWORD));
					$objMedRequest->addHeader("Content-Type: application/xml; charset=UTF-8");
					$objMedRequest->addHeader("Content-length: ".(strlen($strMessage)));
					
					
					$strResponse				=	$objMedRequest->Post($strMessage);
						
					
					$objOutMessage->updateOutMessageResponse($strResponse,$arrOutMessage['tran_id']);
					
					
					$objResponseXml				=	new MedXmlParser($strResponse);
					
					
					$strResponseEDIMessageBase64=	$objResponseXml->EDIFACTMessage->Value;	
					
					
					if(($strResponseEDIMessage = base64_decode($strResponseEDIMessageBase64)) === false)
					{
						
						$strResponseEDIMessage	=	$strResponseEDIMessageBase64;
					}
					
					
					if(strpos(strtoupper($objResponseXml->To->Value),'ERROR') === false && strpos(strtoupper($strResponseEDIMessage),'ERROR') === false)
						$strMessageStatus		=	'Sent';
					else
						$strMessageStatus		=	'Error';
						
					
					$strSQL				=	"UPDATE " . OUTBOX_MESSAGE_TABLE . 
											" SET post_attempt = ".($arrOutMessage['post_attempt'] + 1) . ", " .
											" error_note = '" . $strResponseEDIMessage . "', " . 
											" message_status='" . $strMessageStatus . "'" . 
											$strMessageIDUpdate . 
											" WHERE tran_id=" . $arrOutMessage['tran_id'];
				
					$blnResult 			= 	$medDB->Execute($strSQL);
					
					unset($objOutMessage);
					updateCurrentJob($_SESSION['JOB_ID'],'',1);
				} 
				
			} 
			
		} 
	}
	else
	{
		echo 'Another Job is already running..';
	}

	
	
	function logCurrentJob()
	{
		global $medDB;
		$strTimestamp		=	date('Y-m-d H:i:s');

		$strSql		=	'INSERT INTO ' . OUT_MESSAGE_POST_JOB . ' (job_start_time,last_activity_time) VALUES (\'' . $strTimestamp . '\',\'' . $strTimestamp . '\')';
		$medDB->Execute($strSql);
		return $medDB->Insert_ID();
	}
	function canStartNewJob()
	{
		global $medDB;
		$strSql		=	'SELECT * FROM ' . OUT_MESSAGE_POST_JOB . ' ORDER BY job_id DESC';
		$rsJob		=	$medDB->GetRow($strSql);
		
		if((strtotime(date('Y-m-d H:i:s')) - (strtotime($rsJob['last_activity_time'])))>10 || count($rsJob) == 0)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	function updateCurrentJob($intJobID,$strActivityTime='',$intIncreaseMessageCounterBy=1)
	{
		global $medDB;
		
		$strActivityTime		=	date('Y-m-d H:i:s');
		
		$strSql		=	'UPDATE ' . OUT_MESSAGE_POST_JOB . ' SET ' . 
						' last_activity_time = \'' . $strActivityTime . '\', ' .
						' message_sent_count = message_sent_count+' . $intIncreaseMessageCounterBy .
						' WHERE job_id=' . $intJobID;
		$medDB->Execute($strSql);
	}
?>