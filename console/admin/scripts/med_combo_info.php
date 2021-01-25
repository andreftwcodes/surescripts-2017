<?php

	
	$objPage->objGeneral->checkAuth("69");
	
	
	include_once('./../base/meditab/med_quicklist.php');
	include_once("./base/med_module.php"); 
	
	
	$objModule = new MedModule();
	
	
	$intTableId 	= "4";
	$strPageType 	= $objPage->getRequest('hid_page_type');
	$intButtonId 	= $objPage->getRequest('hid_button_id');
	$strFile 		= $objPage->getRequest('file');
	$strModuleName	= $objPage->getPageTitleByDb($intTableId);
		
	
	$strMessage 	= $objPage->objGeneral->getMessage();
	
	
	if($objPage->getRequest("hid_page_type")=='L')	
	{
		
		$strMiddle		= "./middle/med_combo_info.htm";
		
		
		$strModuleName	= $objPage->getPageTitleByDb($intTableId);
		
		
		$strPage 		= $objPage->getHtmlPage($intTableId,$strPageType);
				
		
		$localValues = array("intButtonId"=>$intButtonId,"strFile"=>$strFile,"strPage"=>$strPage,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName);
	}
	else 
	{	
		
		$strMiddle	= "./middle/med_add_combo_info.htm";
		
	
	
		if(trim(strtoupper($strPageType))=="E")
		{
			$intComboId		= $objPage->getRequest('combo_id');
			$arrComboDetail	= $objModule->getComboDetail($intComboId);
		}
		if(count($arrComboDetail)<=0)
			$intGenerateRows = 1;
		else
			$intGenerateRows = 	count($arrComboDetail);	
	

		
		$strPage 			= $objPage->getHtmlPage($intTableId,$strPageType,true); 
		
		
		$localValues = array("intButtonId"=>$intButtonId,"strFile"=>$strFile,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName,"intGenerateRows"=>$intGenerateRows,"strGenerateRows"=>$strGenerateRows,"arrComboDetail"=>$arrComboDetail);
		
		
		$localValues =array_merge($localValues ,$strPage);
	}
?>
