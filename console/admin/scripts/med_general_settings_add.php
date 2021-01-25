<?php

	
	$objPage->objGeneral->checkAuth(11);
	
	
	include_once("./base/med_module.php"); 
		
	
	$objModule = new MedModule();
		
	
	
	$strMiddle		= "./middle/med_general_settings_add.htm";	

	
	$intTableId 		= 11;
	$strPageType 	= $objPage->getRequest('hid_page_type');
	$intButtonId 	= $objPage->getRequest('hid_button_id');
	
	$strFile 		= "med_general_settings_action";
	
	$strPage 		= $objPage->getHtmlPage($intTableId,$strPageType,true);
	
	
	$strTitle		= $objPage->getPageTitleByDb($intTableId);
	if($strPageType	== "E")
	 	$strButtonTitle = "Update";
	else
	 	$strButtonTitle = "Add";

	$objPage->setRequest("strWhere","");

	
	$strMessage 	= $objPage->objGeneral->getMessage();

	
	$localValues = array("intButtonId"=>$intButtonId,"strFile"=>$strFile,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"strMessage"=>$strMessage,"strTitle"=>$strTitle);
	$localValues = array_merge($localValues,$strPage);	

?>
