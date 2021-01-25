<?php

	
	$objPage->objGeneral->checkAuth(1);
	
	
	include_once("./../base/meditab/med_quicklist.php");
	include_once("./base/med_module.php"); 
	$objModule=new MedModule();		


	
	$strMiddle="./middle/med_page_multi_fields_add.htm";		 
	
	
	$intTableId 	=  $objPage->getRequest('hid_table_id');
	$strPageType 	=  $objPage->getRequest('hid_page_type');
	$intModuleName 	=  $objPage->getRequest('hidin_module_id');
	$strFile = $objPage->getRequest('file');
	$intId 			=  $objPage->getRequest('table_multi_id');
	
	if($strPageType == "E")
	{
		
		$rsTableMulti=$objModule->getTableMultiRecord($intId);
		if(ereg(",",$rsTableMulti[0]['field_id']))
			$intFieldsId = explode(",",$rsTableMulti[0]['field_id']);
		else
			$intFieldsId = array($rsTableMulti[0]['field_id']);

		if(ereg(",",$rsTableMulti[0]['button_id']))
			$intButtonId = explode(",",$rsTableMulti[0]['button_id']);
		else
			$intButtonId = array($rsTableMulti[0]['button_id']);
			
		
	}
	
	
	$rsFields=$objModule->getFieldsRecord($intTableId,L);
	

	
	$rsButtons=$objModule->getButtonsRecord($intTableId);
	

	
	$strModuleName = $objPage->getPageTitleByDb($intTableId);
	
	
	$strMessage = $objPage->objGeneral->getMessage();

	
	$localValues = array("intId"=>$intId,"intButtonId"=>$intButtonId,"intFieldsId"=>$intFieldsId,"rsTableMulti"=>$rsTableMulti,"rsFields"=>$rsFields,"rsButtons"=>$rsButtons,"strFile"=>$strFile,"intButtonId"=>$intButtonId,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"intModuleId"=>$intModuleId,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName,"intPkId"=>$intPkId,"showcombo"=>$showcombo,"strScriptType"=>$strScriptType);
?>
