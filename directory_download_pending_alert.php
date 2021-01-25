<?php 

	set_time_limit(0);

	include_once('med_config.php');

	include_once(WEB_ROOT.'base/DB.php');

	include_once(WEB_ROOT.'base/MedCommon.php');

	include_once(WEB_ROOT.'base/MedSimpleMailer.php');

	$strCurrentTime = date("Y-m-d H:i:s");
	$last_download_date = date('Y-m-d', strtotime($strCurrentTime.'last sunday'));

	$fullstrSQL = "SELECT COUNT(0) as PENDING_DOWNLOAD FROM ".DIRECTORY_DOWNLOAD_LOG." WHERE date(start_time) = date('$last_download_date') AND download_type='FULL' AND directory_type='PRESCRIBER'";

	$fullrsResult = $medDB->GetRow($fullstrSQL);

	$nightlystrSQL = "SELECT COUNT(0) as PENDING_DOWNLOAD FROM ".DIRECTORY_DOWNLOAD_LOG." WHERE date(start_time) = date('$strCurrentTime') AND download_type='NIGHTLY' AND directory_type='PRESCRIBER'";

	$nightlyrsResult = $medDB->GetRow($nightlystrSQL);

	$pending = 0;

	if($fullrsResult['PENDING_DOWNLOAD'] == 0) {

		$pending = 1;
		$strBody = "Hello Admin," . "<br />" . 
		 "The full directory download is pending for PRESCRIBER." . "<br />" .
		 "Please connect to Server Console and fix the issue.";
	}

	if($nightlyrsResult['PENDING_DOWNLOAD'] == 0) {

		$pending = 1;
		$strBody = "Hello Admin," . "<br />" . 
		 "The nightly directory download is pending for PRESCRIBER." . "<br />" .
		 "Please connect to Server Console and fix the issue.";
	}

	if($fullrsResult['PENDING_DOWNLOAD'] == 0 && $nightlyrsResult['PENDING_DOWNLOAD'] == 0) {

		$pending = 1;
		$strBody = "Hello Admin," . "<br />" . 
		 "The full and nightly directory download is pending for PRESCRIBER." . "<br />" .
		 "Please connect to Server Console and fix the issue.";
	}

	if( !empty($pending) ) {

		$Mailer = new MedSimpleMailer();

		$strSubject = SPEAKER_ERROR_EMAIL_SUBJECT . " for Directory Download Pending";

		$Mailer->sendEmail('kaushik.parmar@metizsoft.com, dsrtruth051@gmail.com', null, $strSubject, $strBody, 'GMAIL');
	}
?>