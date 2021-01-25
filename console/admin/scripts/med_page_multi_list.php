<?php

	
	
	$objPage->objGeneral->checkAuth(1);
	
	
	include_once('./../base/meditab/med_quicklist.php');
		
	$strMiddle="./middle/med_page_multi_list.htm";
	
	
	$intTableId =  $objPage->getRequest('hid_table_id');
	$strPageType = $objPage->getRequest('hid_page_type');
	$intModuleId = $objPage->getRequest('hidin_module_id');
	$intButtonId = $objPage->getRequest('hid_button_id');
	$strFile = $objPage->getRequest('file');
	$intParentId 	=	$objPage->getRequest('parent_id');
	$strPage = $objPage->getHtmlPage(30,L);
	
	$strAction=$objPage->getRequest('hid_page_type'); 
	
	
	  $arrSearch = $objPage->generateSearch(2);
	

	
	if(!empty($strAction))
	{
		$objData = new MedData(); 
		$issearch = $objPage->getRequest('h_issearchid');
		$isalpha = $objPage->getRequest('h_isalphaid');
		$ispaging = $objPage->getRequest('h_ispagingid');
		$isselector = $objPage->getRequest('h_isselectorid');
		$issort = $objPage->getRequest('h_issortid');
		$page_title = $objPage->getRequest('page_title');
		$iscolumnheading = $objPage->getRequest('h_iscolumnheadingid');
		
		
		switch($strAction)
		{	
			case "A" :
							$field_id = implode(",",$objPage->getRequest('fields'));
							$button_id = implode(",",$objPage->getRequest('button'));
							
							$objData->setProperty("tbl_table_multi","table_multi_id",null,null);
							$objData->setFieldValue("field_id",$field_id);
							$objData->setFieldValue("button_id",$button_id);
							$objData->setFieldValue("issearch",$issearch);
							$objData->setFieldValue("isalpha",$isalpha);
							$objData->setFieldValue("ispaging",$ispaging);
							$objData->setFieldValue("isselector",$isselector);
							$objData->setFieldValue("issort",$issort);
							$objData->setFieldValue("iscolumnheading",$iscolumnheading);
							$objData->setFieldValue("page_title",$page_title);
							
							$objData->performAction($strAction,$strWhere,true);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_ADD_MSG));
							
							$strUrl="index.php?file=med_page_multi_list&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;
							break;
			case "E" :
							$field_id = implode(",",$objPage->getRequest('fields'));
							$button_id = implode(",",$objPage->getRequest('button'));
							
							$objData->setProperty("tbl_table_multi","table_multi_id",null);
							$objData->setFieldValue("field_id",$field_id);
							$objData->setFieldValue("button_id",$button_id);
							$objData->setFieldValue("issearch",$issearch);
							$objData->setFieldValue("isalpha",$isalpha);
							$objData->setFieldValue("ispaging",$ispaging);
							$objData->setFieldValue("isselector",$isselector);
							$objData->setFieldValue("issort",$issort);
							$objData->setFieldValue("iscolumnheading",$iscolumnheading);
							$objData->setFieldValue("page_title",$page_title);
							
							$strWhere=$objPage->getRequest('table_multi_id');
							if(MedPage::getRequest('strWhere')!='')	$strWhere.=" ".MedPage::getRequest('strWhere');
							@eval("\$strWhere = \"$strWhere\";");	
							
							$objData->performAction($strAction,$strWhere,true);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_MOD_MSG));
							
							$strUrl="index.php?file=med_page_multi_list&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;
							break;
			case "D" :
							
							$_POST[$_POST["hid_listname"]."_cSlcPK"] = explode(",",$_POST[$_POST["hid_listname"]."_cSlcPK"]);
							$objData->setProperty("tbl_table_multi","table_multi_id",null,null);
							$objData->performAction($strAction,"");
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_DEL_MSG));
							$strUrl="index.php?file=med_page_multi_list&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;		
		case "C"  :
							include_once("./base/med_module.php");
							$objModule=new MedModule();
							$intSelValue =  $objPage->getRequest('list30_cSlcPK');
							$intInsertId =  $objPage->getRequest('slt_table_list');
							for($intSelId=0;$intSelId<count($intSelValue);$intSelId++)
							{
								$intTotVal .= "'".$intSelValue[$intSelId]."',";
							}	
								$intTotId = substr($intTotVal,0,strlen($intTotVal)-1);
							$resFields   = $objModule->getSelectedMulti($intTotId);
							$intInsertId = $objModule->InsertSelectedMulti($resFields,$intInsertId);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("Copy Fields SucceesFully."));
							$strUrl="index.php?file=med_page_multi_list&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;
							break;														
		}
	}	
	
	
	$strTitle .= $objPage->getPageTitleByDb($intTableId);
	
	$strModuleName 		= 	$strTitle;

	
	$strMessage = $objPage->objGeneral->getMessage();
	
	
	$localValues = array("arrSearch"=>$arrSearch,"intButtonId"=>$intButtonId,"strFile"=>$strFile,"strPage"=>$strPage,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"intModuleId"=>$intModuleId,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName);


	
	function list89_DataLoaded($rsData)
	{
		for($intData=0;$intData<count($rsData[0]);$intData++)
		{
			if($rsData[0][$intData]["issearch"] == 0) $rsData[0][$intData]["issearch"] = "false";
			else $rsData[0][$intData]["issearch"] = "true";
			
			if($rsData[0][$intData]["isalpha"] == 0) $rsData[0][$intData]["isalpha"] = "false";
			else $rsData[0][$intData]["isalpha"] = "true";
			
			if($rsData[0][$intData]["ispaging"] == 0) $rsData[0][$intData]["ispaging"] = "false";
			else $rsData[0][$intData]["ispaging"] = "true";
			
			if($rsData[0][$intData]["isselector"] == 0) $rsData[0][$intData]["isselector"] = "false";
			else $rsData[0][$intData]["isselector"] = "true";
			
			if($rsData[0][$intData]["issort"] == 0) $rsData[0][$intData]["issort"] = "false";
			else $rsData[0][$intData]["issort"] = "true";
		}
	}

?>
