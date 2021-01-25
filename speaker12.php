<?php
	
	set_time_limit(0);
	
	include_once('med_config.php');
		
	include_once(WEB_ROOT.'base/MedXmlParser.php');
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	
	include_once(WEB_ROOT.'base/DB.php');
	
	
	include_once(WEB_ROOT.'base/MedOutMessage.php');
	
	
	include(WEB_ROOT.'base/MedRequest.php');
	
	
	include(WEB_ROOT.'base/MedSimpleMailer.php');
	
	if(isset($_GET['TRY']))
	{
		$intAttempt = $_GET['TRY'];
	}
	else if(isset($argv[1]))
	{
		$intAttempt = $argv[1];
	}
	else
	{
		$intAttempt = 0;
	}
	
	
	if($intAttempt <= 1)
	{
		
		$strJobCheckSql = "SELECT job_id, job_start_time, job_termination_time, message_sent_count, last_activity_time, 
							IF (job_termination_time >= job_start_time, 'COMPLETE','IN_PROGRESS') as job_status
							FROM out_message_post_job 
							WHERE type ='FIRST_ATTEMPT_POSTER' ORDER BY job_id DESC LIMIT 0,1";
		$rsJob	=	$medDB->GetRow($strJobCheckSql);

		if(count($rsJob) > 0)
		{
			
			$dtJobStartTime = strtotime($rsJob['last_activity_time']);
			$dtCurrentTime = mktime();

			$dtInterval = $dtCurrentTime - $dtJobStartTime;
			
			
			if($rsJob['job_status'] != "COMPLETE")
			{
				if($dtInterval < (SPEAKER_SERVICE_WAIT_TIMEOUT * 60))
				{
					echo "Another Job is alrady running.";
					exit;	
				}
				else
				{
					
					$Body = "Dear Admin, Surescripts Production has encountered an unusual long duration (more than " . SPEAKER_SERVICE_WAIT_TIMEOUT . " minutes) occupied by SPEAKER (Message Posting) Service.
						As it has exceeded Wait-time-out limit, we are starting another SPEAKER Service, please check on the server to see that it does not start sending duplicate messages.";
					sendEmailAlert($Body);
				}
			}
		}
	}
	
	
	if(SERVER_TYPE == 'PRESCRIBER_PARTNER')
	{
	    $strMessageType = 'PRESCRIBER';
	}
	else
	{
	    $strMessageType = 'PHARMACY';
	}
	
	
	switch($intAttempt)
	{
		CASE '':
		CASE '0':
		CASE '1':
			$intAttempt 	=	'1';
			$strJobType		=	'FIRST_ATTEMPT_POSTER';
			$strSQL			=	"SELECT * FROM " . OUTBOX_MESSAGE_TABLE . " 
								WHERE (message_status = 'Pending' OR message_status = 'Temp_Failed') AND  message_from = '".$strMessageType."' 
								ORDER BY tran_id ASC LIMIT 0,100";
			break;
		CASE '2':
			$strJobType		=	'SECOND_ATTEMPT_POSTER';
			$strSQL			=	"SELECT * FROM " . OUTBOX_MESSAGE_TABLE . " WHERE message_status = 'Error' 
								AND  message_from = '".$strMessageType."' 
								AND (status_code = '600' OR status_code = '602')
								AND  tran_id IN (
											SELECT om_tran_id FROM " . OUT_MESSAGE_RETRY_SCHEDULE_TABLE . " 
											WHERE 
											attempt = 2 AND 
											sent = 'N' AND 
											next_attempt_on <= '" . convertToMySqlDateTime(mktime(), TRUE) . "'
											)";
			break;
		CASE '3':
			$strJobType		=	'THIRD_ATTEMPT_POSTER';
			$strSQL			=	"SELECT * FROM " . OUTBOX_MESSAGE_TABLE . " WHERE message_status = 'Error' 
				
								AND  message_from = '".$strMessageType."' 
								AND (status_code = '600' OR status_code = '602') 
								AND  tran_id IN (
											SELECT om_tran_id FROM " . OUT_MESSAGE_RETRY_SCHEDULE_TABLE . " 
											WHERE 
											attempt = 3 AND 
											sent = 'N' AND 
											next_attempt_on <= '" . convertToMySqlDateTime(mktime(), TRUE) . "'
											)";
			
			break;
		CASE '4':
			$strJobType		=	'FORTH_ATTEMPT_POSTER';
			$strSQL			=	"SELECT * FROM " . OUTBOX_MESSAGE_TABLE . " WHERE message_status = 'Error' 
								AND  message_from = '".$strMessageType."' 
								AND (status_code = '600' OR status_code = '602') 
								AND  tran_id IN (
											SELECT om_tran_id FROM " . OUT_MESSAGE_RETRY_SCHEDULE_TABLE . " 
											WHERE 
											attempt = 4 AND 
											sent = 'N' AND 
											next_attempt_on <= '" . convertToMySqlDateTime(mktime(), TRUE) . "'
											)";
			
			break;
	}
	

	$rsOutMessages	=	$medDB->GetAll($strSQL);

	if(count($rsOutMessages) == 0)
	{
		 
		
		
		
		
		echo '0';
	}
	else
	{
		
		$JOB_ID = logCurrentJob($strJobType);
		
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
				}
				
				$objOutMessage->RelatedToMessageID	=	$arrOutMessage['related_message_id'];
				$objOutMessage->SentTime	=	getUTCTime(strtotime($arrOutMessage['sent_time']));
				$objOutMessage->SmsVersion	=	$arrOutMessage['sms_version'];
				$objOutMessage->VendorName	=	$arrOutMessage['vendor_name'];
				$objOutMessage->AppName		=	$arrOutMessage['app_name'];
				$objOutMessage->AppVersion	=	$arrOutMessage['app_version'];
				$objOutMessage->MessageVersion	=	$arrOutMessage['message_version'];
				
				if($arrOutMessage['message_version'] != MESSAGE_VERSION)
				{
				    
				    $arrOutMessage['edi_message'] = base64_decode($arrOutMessage['edi_message']);

		    
				    $arrOutMessage['edi_message'] = str_replace( array(chr(160), ":AD2:+"), array('', "::+"), $arrOutMessage['edi_message']);

				    

				    $objOutMessage->EDIMessage	=	base64_encode(str_replace(array('<<MESSAGE_ID_PLACE_HOLDER>>'),array($objOutMessage->MessageID),$arrOutMessage['edi_message']));
				}
				else
				{
				    $objOutMessage->EDIMessage	=	$arrOutMessage['edi_message'];
				}
				
				$objOutMessage->OMTranID	=	$arrOutMessage['tran_id'];

				
				$strMessage			=	$objOutMessage->getMessage();
				
				
				
				    $strSureScriptURL = MSG_106_URL;
				
				
				$objMedRequest			=	new MedRequest($strSureScriptURL);

				
				$objMedRequest->addHeader("Authorization: Basic ".base64_encode(MSG_LOGIN_ID.':'.MSG_PASSWORD));
				$objMedRequest->addHeader("Content-Type: application/xml; charset=UTF-16");
				$objMedRequest->addHeader("Content-length: ".(strlen($strMessage)));
				
				
				$strResponse			=	$objMedRequest->Post($strMessage);
				
				
				
				
				
				
				
				if($strResponse != "" && $strResponse != null)
				{
					if( strpos($strResponse, "<Message") !== false)
					{
						
						$objResponseXml				=	new MedXmlParser($strResponse);
						
						if($arrOutMessage['message_version'] != MESSAGE_VERSION)
						{
						    
						    
						    
						    $strResponseEDIMessageBase64 =	$objResponseXml->EDIFACTMessage->Value;
						    
						    
						    $strResponseEDIMessage = base64_decode($strResponseEDIMessageBase64, true);
						    if($strResponseEDIMessage === false)
						    {
							    
							    $strResponseEDIMessage = $strResponseEDIMessageBase64;
						    }
						}
						else
						{
						    $strXML = extractElementData($strResponse, 'Body');
						    $strResponseEDIMessage = str_replace(array("<Body>","<Message>","</Body>","</Message>"),"",$strXML);
						}
						
						
					}
					else
					{
						$strResponseEDIMessage = $strResponse;
					}
	
					
					$objOutMessage->updateOutMessageResponse($strResponse, $strResponseEDIMessage);
					
					if($arrOutMessage['message_version'] != MESSAGE_VERSION)
					{
					    $strStatusCode = getStatusCodeFromEDIFactResponse($strResponseEDIMessage);
					}
					else
					{
					    $strStatusCode = $objResponseXml->CODE->Value;
					}
					
					
					
					if($strStatusCode == '600' || $strStatusCode == '602')
					{
						$arrRecord = array();
						switch($intAttempt)
						{
							CASE '1':
								
								$arrRecord['om_tran_id'] = $arrOutMessage['tran_id'];
								$arrRecord['last_attemptted_on'] = convertToMySqlDateTime(mktime(), TRUE);
								$arrRecord['next_attempt_on'] = convertToMySqlDateTime(mktime() + SECOND_ATTEMPT_OFFSET, TRUE);
								$arrRecord['attempt'] = 2;
								$arrRecord['sent'] = 'N';
								$medDB->AutoExecute(OUT_MESSAGE_RETRY_SCHEDULE_TABLE, $arrRecord, 'INSERT');						
								break;
							CASE '2':
								
								$arrRecord['om_tran_id'] = $arrOutMessage['tran_id'];
								$arrRecord['last_attemptted_on'] = convertToMySqlDateTime(mktime(), TRUE);
								$arrRecord['next_attempt_on'] = convertToMySqlDateTime(mktime() + THIRD_ATTEMPT_OFFSET, TRUE);
								$arrRecord['attempt'] = 3;
								$arrRecord['sent'] = 'N';
								$medDB->AutoExecute(OUT_MESSAGE_RETRY_SCHEDULE_TABLE, $arrRecord, 'INSERT');
								break;
							CASE '3':
								
								$arrRecord['om_tran_id'] = $arrOutMessage['tran_id'];
								$arrRecord['last_attemptted_on'] = convertToMySqlDateTime(mktime(), TRUE);
								$arrRecord['next_attempt_on'] = convertToMySqlDateTime(mktime() + FORTH_ATTEMPT_OFFSET, TRUE);
								$arrRecord['attempt'] = 4;
								$arrRecord['sent'] = 'N';
								$medDB->AutoExecute(OUT_MESSAGE_RETRY_SCHEDULE_TABLE, $arrRecord, 'INSERT');
								break;
							default:
								break;
						}
						unset($arrRecord);
					}
					
					if($intAttempt > 1)
					{
						$arrRecord = array();
						$arrRecord['sent'] = 'Y';
						$medDB->AutoExecute(OUT_MESSAGE_RETRY_SCHEDULE_TABLE, $arrRecord, 'UPDATE', 'om_tran_id = ' . $arrOutMessage['tran_id'] . ' AND attempt = ' . $intAttempt);
						unset($arrRecord);
					}

					
					if(strpos(strtoupper($strResponseEDIMessage),'ERROR') === false && strpos(strtoupper($strResponseEDIMessage),'FAIL') === false && strpos(strtoupper($strResponseEDIMessage),'INVALID') === false)
					{
						$strMessageStatus		=	'Sent';
					}
					else
					{
						$strMessageStatus		=	'Error';
						
						
						
						logErrorAlert($objOutMessage->OMTranID, $objOutMessage->MessageID, $objOutMessage->From, $objOutMessage->To, $strStatusCode, $strResponseEDIMessage);
						
					}
				}
				else
				{
					$strMessageStatus		= 'Temp_Failed';
					$strStatusCode 			= "FAIL";
					$strResponseEDIMessage 	= "CRITICAL AND URGENT: FAILED AS NO RESPONSE RECEIVED FROM SURESCRIPTS GATEWAY UPON POSTING THIS MESSAGE; PLEASE CHECK WITH SERVER IT LOOKS LIKE EITHER DUE TO IP CHANGE OR SOME OTHER REASON GATEWAY HAS BLOCKED OUR SERVER FROM POSTING MESSAGE; CHECK WHETHER YOU ARE ABLE TO OPEN SS-ADMIN-CONSOLE ON SERVER OR NOT...";
				}
				
				$arrRecord = array();
				$arrRecord['tran_id'] = $arrOutMessage['tran_id'];
				$arrRecord['message_id'] = $objOutMessage->MessageID;
				$arrRecord['message_status'] = $strMessageStatus;
				$arrRecord['status_code'] = $strStatusCode;
				$arrRecord['error_note'] = $strResponseEDIMessage;
				$arrRecord['post_attempt'] = $arrOutMessage['post_attempt']+1;
				$medDB->AutoExecute(OUTBOX_MESSAGE_TABLE, $arrRecord, 'UPDATE', 'tran_id = ' . $arrOutMessage['tran_id']);
				unset($arrRecord);
				unset($objOutMessage);
				updateCurrentJob($JOB_ID, '', 1);
			} 
			
		} 
		
		
		completeCurrentJob($JOB_ID);		
	} 

	
	
	exit;

	
	
	function logCurrentJob($strJobType, $blnNothingToDo = FALSE)
	{
		global $medDB;
		$strTimestamp		=	date('Y-m-d H:i:s');
		if($blnNothingToDo === FALSE)
		{
			$strSql		=	'INSERT INTO ' . OUT_MESSAGE_POST_JOB . ' (type,job_start_time,last_activity_time) VALUES (\'' . $strJobType . '\',\'' . $strTimestamp . '\',\'' . $strTimestamp . '\')';
		}
		else
		{
			$strSql		=	'INSERT INTO ' . OUT_MESSAGE_POST_JOB . ' (type,job_start_time,job_termination_time) VALUES (\'' . $strJobType . '\',\'' . $strTimestamp . '\',\'' . $strTimestamp . '\')';
		}

		$medDB->Execute($strSql);
		return $medDB->Insert_ID();
	}
	
	
	function updateCurrentJob($intJobID, $strActivityTime = '', $intIncreaseMessageCounterBy = 1)
	{
		global $medDB;
		
		$strActivityTime		=	date('Y-m-d H:i:s');
		
		$strSql		=	'UPDATE ' . OUT_MESSAGE_POST_JOB . ' SET ' . 
						' last_activity_time = \'' . $strActivityTime . '\', ' .
						' message_sent_count = message_sent_count+' . $intIncreaseMessageCounterBy .
						' WHERE job_id=' . $intJobID;
		$medDB->Execute($strSql);
	}
	
	
	function completeCurrentJob($intJobID)
	{
		global $medDB;
		
		$arrRecord				=	array();
		$arrRecord['job_termination_time']	= date('Y-m-d H:i:s');
		$medDB->AutoExecute(OUT_MESSAGE_POST_JOB, $arrRecord, 'UPDATE', 'job_id = ' . $intJobID);
	}
	
	
	function getStatusCodeFromEDIFactResponse($EDIFactMessage)
	{
		
		$UNA = getDelimitersFromEDIFact($EDIFactMessage);
		
		
		$EDIFact = @explode($UNA[5],$EDIFactMessage);
		
		
		$UIB = @explode($UNA[1],$EDIFact[3]);
		
		
		return $UIB[1];
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
	
	
	function sendEmailAlertForOutMessageError($MTTranID, $MessageID, $MsgFrom, $MsgTo, $ErrorCode, $Response)
	{
		$Body = 'MT Tran ID: ' . $MTTranID . "<br/>";
		$Body .= 'Message ID: ' . $MessageID . "<br/>";
		$Body .= 'Message From: ' . $MsgFrom . "<br/>";
		$Body .= 'Message To: ' . $MsgTo . "<br/>";
		$Body .= 'Error Code: ' . $ErrorCode . "<br/>";
		$Body .= 'Response: ' . $Response . "<br/>";
		$Mailer = new MedSimpleMailer();
		$Mailer->sendEmail(SPEAKER_ERROR_EMAIL_TO, SPEAKER_ERROR_EMAIL_FROM, SPEAKER_ERROR_EMAIL_SUBJECT, $Body);
	}
	
	
	function sendEmailAlert($Body)
	{
		$Mailer = new MedSimpleMailer();
		$Mailer->sendEmail(SPEAKER_ERROR_EMAIL_TO, SPEAKER_ERROR_EMAIL_FROM, SPEAKER_ERROR_EMAIL_SUBJECT, $Body);
	}
	
	
	function logErrorAlert($MTTranID, $MessageID, $MsgFrom, $MsgTo, $ErrorCode, $Response)
	{
		global $medDB;
		
		$arrRecord = array();
		$arrRecord['mt_tran_id']	= $MTTranID;
		$arrRecord['message_id']	= $MessageID;
		$arrRecord['from_id']		= $MsgFrom;
		$arrRecord['to_id']			= $MsgTo;
		$arrRecord['error_code']	= $ErrorCode;
		$arrRecord['response']		= $Response;
		$arrRecord['sent_time']		= date('Y-m-d H:i:s');
		
		$medDB->AutoExecute(OUT_MESSAGE_ERRORS_TABLE, $arrRecord, 'INSERT');
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
?>