<?php


	
	$intAdminId		=	$objPage->objGeneral->getSessionVarName('intAdminId');
	$strUserName	=	$objPage->objGeneral->getSessionVarName('strUserName');
	$intModule_id	=	$objPage->objGeneral->getSessionVarName('intModule_id');
	$intOffice_id	=	$objPage->objGeneral->getSessionVarName('intOffice_id');
	$strTimeZone	=	$objPage->objGeneral->getSessionVarName('strTimeZone');
	$strDevPass		=	$objPage->objGeneral->getSessionVarName('strDevPass');
	
	
	if(session_is_registered($intAdminId)) 		session_unregister($intAdminId);
	if(session_is_registered($strUserName))		session_unregister($strUserName);	
	if(session_is_registered($intModule_id))	session_unregister($intModule_id);
	if(session_is_registered($intOffice_id))	session_unregister($intOffice_id);
	if(session_is_registered($strTimeZone))		session_unregister($strTimeZone);
	if(session_is_registered($strDevPass))		session_unregister($strDevPass);
	
	
	$strMessage = $objPage->objGeneral->getSiteMessage('LOG_OUT_MSG');
	$objPage->objGeneral->setMessage($strMessage);
	
	
	header("location: index.php");
	exit;

?>