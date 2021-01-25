<?php
	
	set_time_limit(0);
	
	
	include_once('med_config.php');
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	
	include_once(WEB_ROOT.'base/DB.php');
	
	
	include(WEB_ROOT.'base/MedSimpleMailer.php');
	
	$Sql = "SELECT om_error_id, mt_tran_id, message_id, from_id, to_id, error_code, response, sent_time FROM " . OUT_MESSAGE_ERRORS_TABLE . " WHERE notified = 'N'";
	
	$rsAlerts = $medDB->GetAll($Sql);
	
	if(count($rsAlerts) > 0)
	{
		$arrErrorID = array();
		$EmailBody = '<html><body style="font-family:arial">';
		$EmailBody .= '<p>Failed Message Digest for '.date("d-M-Y").'</p>';
		$EmailBody .= '<table border="1" style="font-family:arial;border-collapse:collapse;border:1px solid #ddd"><tr><th>MT TranID</th><th>MessageID</th><th>FromID</th><th>ToID</th><th>Error Code</th><th>Response</th><th>Sent Time</th></tr>';
		foreach($rsAlerts as $Index => $Alert)
		{
			$EmailBody .= '<tr><td>' . $Alert['mt_tran_id'] . '</td><td>' . $Alert['message_id'] . '</td><td>' . 
							$Alert['from_id'] . '</td><td>' . $Alert['to_id'] . '</td><td>' . $Alert['error_code'] . '</td><td>' . $Alert['response'] . '</td>' .
							'<td>' . $Alert['sent_time'] . '</td></tr>';
			
			
			$arrErrorID[] = $Alert['om_error_id'];
		}
		$EmailBody .= '</table></body></html>';
		
		$Mailer = new MedSimpleMailer();
		$Mailer->sendEmail(SPEAKER_ERROR_EMAIL_TO, SPEAKER_ERROR_EMAIL_FROM, SPEAKER_ERROR_EMAIL_SUBJECT . " (Failed Message Digest)", $EmailBody);
		
		
		$arrRecord['notified'] = "Y";
		$medDB->AutoExecute(OUT_MESSAGE_ERRORS_TABLE, $arrRecord, "UPDATE","om_error_id IN (" . @implode(",", $arrErrorID) . ")");
	}
	
