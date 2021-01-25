<?php

	
	
	
	
	include_once('./../base/meditab/med_quicklist.php');
	include_once("./base/med_module.php"); 
	
	
	$objModule		=	new MedModule();
		
	
	$strAction		=	$objPage->getRequest('hid_page_type');
	$intTotRows		=	$objPage->getRequest('hid_generateRows');
	$intNewRows		=	$objPage->getRequest("txt_generate_rows");

	
	if(!empty($strAction))
	{	
		
		$objData = new MedData();
		
		
		$objPage->setTableProperty($objData); 
		
		$strCaseName	=	$objPage->getRequest("TaRtxt_case_name");
		$intComboId		=	$objPage->getRequest("hid_combo_id");
		
		
		$objData->setFieldValue("combo_id",$intComboId);
		$objData->setFieldValue("case_name",$strCaseName);
		$objData->setFieldValue("query",$objPage->getRequest("Taara_query"));
		$objData->setFieldValue("key_column",$objPage->getRequest("Tatxt_key_column"));
		$objData->setFieldValue("value_column",$objPage->getRequest("Tatxt_value_column"));
		$objData->setFieldValue("combo_notes",$objPage->getRequest("Taara_combo_notes"));
		if($strAction == 'D')
		{
			
			$_POST[$_POST["hid_listname"]."_cSlcPK"] = explode(",",$_POST[$_POST["hid_listname"]."_cSlcPK"]);
		}
		$objData->performAction($strAction,null,false); 
		
		if($objData->arrNotUniqueFields==NULL)
		{
			
			if($strAction=="A" || $strAction=="E")
			{ 
				if (empty($intComboId)) $intComboId =$objData->getAutoId();
				$objModule->insertComboDetails($intComboId,$objData,$intTotRows);			
				if($strAction=="A")
					$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("REC_ADD_MSG"));	
				else
					$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("REC_MOD_MSG"));	
			}
			header("Location: index.php?file=med_combo_info&hid_page_type=L");
			exit;
		}
		else
		{

			
			$intTableId 	= "4";
			$strPageType 	= $objPage->getRequest('hid_page_type');
			$intButtonId 	= $objPage->getRequest('hid_button_id');
			$strFile 		= $objPage->getRequest('file');
			$strModuleName	= $objPage->getPageTitleByDb($intTableId);
				
			
			$strMessage 	= $objPage->objGeneral->getMessage();

			
			$strMiddle	= "./middle/med_add_combo_info.htm";
		
			if(trim(strtoupper($strPageType))=="E")
			{
				$intComboId		= $objPage->getRequest('combo_id');
				$arrComboDetail	= $objModule->getComboDetail($intComboId);
			}
			$intGenerateRows = 	$intTotRows;
			
			$strPage 			= $objPage->getHtmlPage($intTableId,$strPageType,true); 
			
			
			$localValues = array("intButtonId"=>$intButtonId,"strFile"=>$strFile,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName,"intGenerateRows"=>$intGenerateRows,"strGenerateRows"=>$strGenerateRows,"arrComboDetail"=>$arrComboDetail,"intGenerateRows"=>$intTotRows,"intNewRows"=>$intNewRows);
			$localValues =array_merge($localValues ,$strPage);		
		}
	}
	else
		$objPage->objGeneral->raiseError("WARNING","No actions define","med_action","Do not call med_action file without Action parameters"); 
?>