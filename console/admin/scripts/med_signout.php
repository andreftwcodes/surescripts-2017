<?php


	
	$intAdminId		=	$objPage->objGeneral->getSessionVarName('intAdminId');
	$strUserName	=	$objPage->objGeneral->getSessionVarName('strUserName');
	$intModule_id	=	$objPage->objGeneral->getSessionVarName('intModule_id');
	$intOffice_id	=	$objPage->objGeneral->getSessionVarName('intOffice_id');
	$strTimeZone	=	$objPage->objGeneral->getSessionVarName('strTimeZone');
	$strDevPass		=	$objPage->objGeneral->getSessionVarName('strDevPass');
	
	
	
	session_destroy();
	
	
	$strMessage = $objPage->objGeneral->getSiteMessage('LOG_OUT_MSG');
	$objPage->objGeneral->setMessage($strMessage);
	
	
	header("location: index.php");
	exit;

?>