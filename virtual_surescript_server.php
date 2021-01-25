<?php
	
file_put_contents('c:/ah.txt',var_export(apache_request_headers(),true));
	
	include_once('med_config.php');
	
	include_once(WEB_ROOT.'base/MedXmlParser.php');
	
	include_once(WEB_ROOT.'base/DB.php');
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	include_once(WEB_ROOT.'base/MedResponseBuilder.php');
	
	$objResponse = new MedResponseBuilder("ERROR",false);
	
	$objResponse->MESSAGE_ID = '0';
	$objResponse->RELATES_TO_MESSAGE_ID = 'eer383048304090ws98d';
	$objResponse->SENT_TIME = mktime();
	$objResponse->STATUS_CODE = '60x';
	
	echo 'Authentication failed: Invalid UID and password.';exit;
?>