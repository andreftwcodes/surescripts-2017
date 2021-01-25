<?php 
	
	
	set_time_limit(0);
	
	
	include_once('med_config.php');
	
	
	include_once(WEB_ROOT.'base/DB.php');
	
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	
	include_once(WEB_ROOT.'base/MedSimpleMailer.php');
	
	
	define('OUTBOX_MAX_WAIT', 15);
	define('INBOX_MAX_WAIT', 20);
	define('VERIFY_MAX_WAIT', 20);
	
	$strCurrentTime = date("Y-m-d H:i:s");
	
	
	$strSQL = "SELECT COUNT(0) as PENDING_COUNT FROM ".OUTBOX_MESSAGE_TABLE." WHERE message_status = 'Pending' AND 
	(MINUTE(timediff(sent_time ,'".$strCurrentTime."')) > ".OUTBOX_MAX_WAIT." OR HOUR(timediff(sent_time ,'".$strCurrentTime."')) > 1)";

	$rsResult = $medDB->GetRow($strSQL);
	
	if($rsResult['PENDING_COUNT'] > 0)
	{
		$Mailer = new MedSimpleMailer();
		
		
		$strSubject = SPEAKER_ERROR_EMAIL_SUBJECT . " for OutBox Pending Messages";
		$strBody = "Hello Admin," . "<br />" . 
		 "There are " . $rsResult['PENDING_COUNT'] . " Messages Pending since more than ".OUTBOX_MAX_WAIT." minutes to be posted on Surescripts." . "<br />" .
		 "Please connect to Server Console and fix the issue.";

		$Mailer->sendEmail(SPEAKER_ERROR_EMAIL_TO, SPEAKER_ERROR_EMAIL_FROM, $strSubject, $strBody);
	}

	// $strExtraWhere = " AND to_id NOT IN ('0300664', '0355025','4932667','3417854','5901966','4612099','4612126') "; 
	$strExtraWhere = ""; 
	
	$strSQL = "SELECT COUNT(0) as PENDING_COUNT FROM ".INBOX_MESSAGE_TABLE." WHERE message_status = 'Pending' AND (TIMESTAMPDIFF(MINUTE, received_time, '".$strCurrentTime."') > ".INBOX_MAX_WAIT." OR TIMESTAMPDIFF(MINUTE, received_time, '".$strCurrentTime."') > 60)" . $strExtraWhere; //andre-20201104

	$rsResult = $medDB->GetRow($strSQL);
	
	if($rsResult['PENDING_COUNT'] > 0)
	{
		$strSQL = "SELECT var_value FROM settings WHERE var_name LIKE 'SERVER_TYPE'";
		$rsSetting = $medDB->GetRow($strSQL);
		if($rsSetting['var_value'] == 'PHARMACY_PARTNER')
		{
			$strTable = 'pharmacy_master';
			$strNameField = 'store_name as NAME';
			$strPKField = 'ncpdpid';
		}
		else if($rsSetting['var_value'] == 'PRESCRIBER_PARTNER')
		{
			$strTable = 'prescriber_master';
			$strNameField = 'CONCAT(first_name,last_name) AS NAME';
			$strPKField = 'spi';
		}

		$strSQL = "SELECT ".$strNameField." FROM ".$strTable." WHERE ".$strPKField." IN (SELECT to_id FROM ".INBOX_MESSAGE_TABLE." WHERE message_status = 'Pending' AND (TIMESTAMPDIFF(MINUTE, received_time, '".$strCurrentTime."') > ".INBOX_MAX_WAIT." OR TIMESTAMPDIFF(MINUTE, received_time, '".$strCurrentTime."') > 60))"; //andre-20201104

		$rsEntityList = $medDB->GetAll($strSQL);
		
		foreach($rsEntityList as $arrEntity)
		{
			$strEntityList .= " - " . $arrEntity['NAME'] . '<br />';
		}
		
		$Mailer = new MedSimpleMailer();
		
		
		$strSubject = SPEAKER_ERROR_EMAIL_SUBJECT . " for InBox Pending Messages";
		$strBody = "Hello Admin," . "<br />" . 
		 "There are " . $rsResult['PENDING_COUNT'] . " In-Messages Pending since more than ".INBOX_MAX_WAIT." minutes, which are posted by Surescripts on our Server." . "<br />" .
		 "Please see list of Clients, who have pending messages in InBox on Surescripts Server." . "<br />" . 
		$strEntityList;

		$Mailer->sendEmail(PENDING_MESSAGE_ALERT_EMAIL_TO, SPEAKER_ERROR_EMAIL_FROM, $strSubject, $strBody);
		}
		
	$strEntityList = "";
	$strSQL = "SELECT count(0) AS PENDING_COUNT FROM ".INBOX_MESSAGE_TABLE." WHERE message_status = 'Pending' AND TIMESTAMPDIFF(MINUTE, received_time, '".$strCurrentTime."') > '".VERIFY_MAX_WAIT."'"; //andre-20201104


	$rsResult = $medDB->GetRow($strSQL);
	
	if($rsResult['PENDING_COUNT'] > 0)
	{
		$strSQL = "SELECT var_value FROM settings WHERE var_name LIKE 'SERVER_TYPE'";
		$rsSetting = $medDB->GetRow($strSQL);
		if($rsSetting['var_value'] == 'PHARMACY_PARTNER')
		{
			$strTable = 'pharmacy_master';
			$strNameField = 'store_name as NAME';
			$strPKField = 'ncpdpid';
		}
		else if($rsSetting['var_value'] == 'PRESCRIBER_PARTNER')
		{
			$strTable = 'prescriber_master';
			$strNameField = 'CONCAT(first_name,last_name) AS NAME';
			$strPKField = 'spi';
		}
		
		$strSQL = "SELECT a.".$strNameField.", b.message_id, b.to_id, b.received_time FROM ".$strTable." as a INNER JOIN ".INBOX_MESSAGE_TABLE." as b ON a.".$strPKField." = b.to_id WHERE b.message_status = 'Pending' AND TIMESTAMPDIFF(MINUTE, b.received_time, '".$strCurrentTime."') > '".VERIFY_MAX_WAIT."'"; //andre-20201104
					
		$rsEntityList = $medDB->GetAll($strSQL);
		$previousName = "";
		
		$current_date = new DateTime();
		$current_date->setTimezone(new DateTimeZone('America/New_York'));
		$strEntityList .= "Current Time: ". $current_date->format("d M y H:i:s")."<br /><br />";
		foreach($rsEntityList as $arrEntity)
		{
			if($previousName != $arrEntity['NAME'])
			{
				$strEntityList .= "<br /><br /> - " . $arrEntity['NAME'] . '<br />';
				$previousName = $arrEntity['NAME'];
			}
			$received_date = new DateTime($arrEntity['received_time']);
			$received_date->setTimezone(new DateTimeZone('America/New_York'));
			$strEntityList .= $arrEntity['message_id'] . " (" . $received_date->format("d M y H:i:s") .')<br />';
		}
		
		$Mailer = new MedSimpleMailer();
		
		
		$strSubject = SPEAKER_ERROR_EMAIL_SUBJECT . " for InBox Not Verified Messages";
		$strBody = "Hello Admin," . "<br />" . 
		 "There are " . $rsResult['PENDING_COUNT'] . " In-Messages Not Verified since more than ".VERIFY_MAX_WAIT." minutes, which are posted by Surescripts on our Server." . "<br />" .
		 "Please see list of Clients, who have pending messages in InBox on Surescripts Server." . "<br />" . 
		$strEntityList;
	
		$Mailer->sendEmail(PENDING_MESSAGE_ALERT_EMAIL_TO, SPEAKER_ERROR_EMAIL_FROM, $strSubject, $strBody);
	}
	
	
?>