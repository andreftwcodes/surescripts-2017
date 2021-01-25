<?php

	
	$objPage->objGeneral->checkAuth("15");
	
	
	include_once('./../base/meditab/med_quicklist.php');
		
	
	include_once("./../base/json/json.php");
		
	
	include_once("./base/med_module.php"); 
		
	
	$objModule 		= 	new MedModule();
		
	
	
	if(($objPage->getRequest('hid_page_type')=="E") && (!$objModule->validOffice("admin_master","office_id","admin_id",$objPage->getRequest('admin_id'))))
		$strMiddle	= 	"./middle/med_notauthorize.htm";		 	
	else
		$strMiddle	= 	"./middle/med_admin_addedit.htm";		 
	
	

	
	$intTableId 	= 	15;
	$strPageType 	= 	$objPage->getRequest('hid_page_type');
	$strFile 		= 	$objPage->getRequest('file');	
	
	$objPage->setRequest("strWhere","office_id=".$objPage->objGeneral->getSession('intOffice_id'));
	
	
	$strPage 		= 	$objPage->getHtmlPage($intTableId,$strPageType,true);
	
	$strModuleName	= 	$objPage->getPageTitleByDb($intTableId);
	
	$objPage->setRequest("strWhere","");
	
	
	$strSumbitButton=	$objPage->getSubmitButton("submit","border='0' onclick=\"return submit_form(this.form);\" class=\"btn\"","Submit");


	
	$strMessage 	= 	$objPage->objGeneral->getMessage();

	
	$localValues 	= 	array(
								"intButtonId"		=>	$intButtonId,
								"strFile"			=>	$strFile,
								"intTableId"		=>	$intTableId,
								"strPageType"		=>	$strPageType,
								"strMessage"		=>	$strMessage,
								"strModuleName"		=>	$strModuleName,
								"strSumbitButton"	=>	$strSumbitButton
							);
							
	$localValues	=	array_merge($localValues,$strPage);		
?>