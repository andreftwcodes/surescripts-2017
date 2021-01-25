<?php

	
	$objPage->objGeneral->checkAuth($objPage->getRequest('hid_table_id'));
	
	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	include_once("./../base/json/json.php");

	
	include_once("./base/med_module.php"); 
	
	
	$objModule		=	new MedModule();
		
	
	$strMiddle		=	"./middle/med_list_record.htm";
	
	
	$strAction		=	$objPage->getRequest('hid_page_type'); 
	$intTableId 	=	$objPage->getRequest('hid_table_id');
	$intModuleId 	=	$objPage->getRequest('hidin_module_id');
	$intButtonId 	=	$objPage->getRequest('hid_button_id');	
	$intParentId 	=	$objPage->getRequest('parent_id');	
	
	
	if(!empty($strAction))
	{	
		
		$objData = new MedData();

		
		$objPage->setTableProperty($objData); 
		
		if($intTableId == 18)
		{
			if($strAction != "D")
			{
				$strWhere = null;
			}
			else
			{
				$strCode = str_replace(",","','",$_REQUEST['list18_cSlcPK']);
				$strCode = "'".$strCode."'";
				$strWhere = " help_code in(".$strCode.")";
			}
			if($strAction == "A")
			{
				$_REQUEST['TaRtxt_help_code'] = strtoupper($_REQUEST['TaRtxt_help_code']);
			}
			$objData->performAction($strAction,$strWhere);
		}
		else
		{
			$objData->performAction($strAction,null);
		}
		if($objData->arrNotUniqueFields != NULL)
		{
			
			$strMiddle	= "./middle/med_add_record.htm";	
			
			
			$intTableId 	= $objPage->getRequest('hid_table_id');
			$strPageType 	= $objPage->getRequest('hid_page_type');
			$intModuleId 	= $objPage->getRequest('hidin_module_id');
			$intButtonId 	= $objPage->getRequest('hid_button_id');
			$strFile 		= $objPage->getRequest('file');
			$blnExport		= true;
	
			
			if($intTableId==9 || $intTableId==32)
			{
				$objPage->setRequest("arrRestrictedValues",array("1",$objPage->objGeneral->getSession('intAdminId')));
				if($objPage->objGeneral->getSession('intOffice_id')!=0)
					$objPage->setRequest("strWhere","office_id=".$objPage->objGeneral->getSession('intOffice_id'));
			}	

			$strPage 		= $objPage->getHtmlPage($intTableId,$strPageType);
			$intExpCordinate = $objPage->getRequest("btn_export_x");
			if(!empty($intExpCordinate))
				include_once("med_export_excel_file.php");
			$strModuleName	= $objPage->getPageTitleByDb($intTableId);
			
			$objPage->setRequest("strWhere","");

			
			$strMessage 	= $objPage->objGeneral->getMessage();
	
			
			$localValues = array("intButtonId"=>$intButtonId,"strFile"=>$strFile,"strPage"=>$strPage,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName,"blnExport"=>$blnExport);
		}
		else
		{
		
		
		$strPageType 	= "L";
		
				
		
		if($intTableId==9) $objPage->setRequest("arrRestrictedValues",array("1",$objPage->objGeneral->getSession('intAdminId')));	

		 header("Location:index.php?file=med_list_record&hid_table_id=$intTableId&parent_id=".$intParentId."&hid_page_type=L");
		 exit;	
		}
	}
	else
		$objPage->objGeneral->raiseError("WARNING","No actions define","med_action","Do not call med_action file without Action parameters"); 
?>