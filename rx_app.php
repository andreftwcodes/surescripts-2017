<?php
	
	set_time_limit(60);
	
	
	include_once('med_config.php');
	
	
	include(WEB_ROOT.'base/MedRequest.php');

	$Message	=	file_get_contents("php://input");
	
	$objRequest = new MedRequest(RXHUB_APP_SERVER);
	
	
	$objRequest->addHeader("Authorization: Basic ".base64_encode(RXHUB_PARTICIPANT_ID.':'.RXHUB_PARTICIPANT_PASSWORD));
	$objRequest->addHeader("Content-Type: application/xml; charset=UTF-8");
	$objRequest->addHeader("Content-length: ".(strlen($Message)));
	
	
	echo $objRequest->Post($Message);

?>