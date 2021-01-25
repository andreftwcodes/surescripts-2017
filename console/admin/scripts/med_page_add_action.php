<?php

	
	$objPage->objGeneral->checkAuth(1);
	
	
	include_once("./../base/meditab/med_quicklist.php");

	
	$objList		= 	new MedQuickList("search");
	$objData		= 	new MedData();
	
	
	$strMiddle		=	"./middle/med_change_pass.htm";	
	
	
	$strPageType	=	$objPage->getRequest("hid_page_type");
	$intModuleId	=	$objPage->getRequest("hidin_module_id");
	$strAction		=	$objPage->getRequest("hid_page_type");
	$strModuleId	=	$objPage->getRequest("module");
	$strAddKeyCol	=	$objPage->getRequest("AddTbl");
	$strAddFieldsOfTable	=	$objPage->getRequest("slt_add_field_table");
	
	$objData->setArrRPAFields("add_field_table");
	if($objPage->getRequest("hid_table_id") != NULL) 
		$intHidTableId = $objPage->getRequest("hid_table_id");

	if($objPage->getRequest("slt_delete_key_col") != "" && $objPage->getRequest("DeleteTbl")) 
		$strDeleteKeyCol = $objPage->getRequest("DeleteTbl").":".$objPage->getRequest("slt_delete_key_col");
	else 
		$strDeleteKeyCol = NULL;

	if($objPage->getRequest("slt_edit_key_col") != "" && $objPage->getRequest("EditTbl")) 
		$strEditKeyCol = $objPage->getRequest("EditTbl").":".$objPage->getRequest("slt_edit_key_col");
	else 
		$strEditKeyCol = NULL;
	
	if(($objPage->getRequest("strCombo") != NULL)||($objPage->getRequest("searchaplha") != NULL) || ($objPage->getRequest("strPaging") != NULL) || ($objPage->getRequest("Selector") != NULL))
		$strissearch = $objPage->getRequest("strCombo").":".$objPage->getRequest("searchaplha").":".$objPage->getRequest("strPaging").":".$objPage->getRequest("Selector");
	else
		$strissearch = "";

	switch($strAction)
	{
		case "A" :
					$objData->setProperty("tbl_table","table_id",null,null);
					$objData->setFieldValue("module_id",$strModuleId);
					$objData->setFieldValue("add_key_col",$strAddKeyCol);
					$objData->setFieldValue("delete_key_col",$strDeleteKeyCol);
					$objData->setFieldValue("edit_key_col",$strEditKeyCol);
					$objPage->setRequest('slt_edit_key_col',$strEditKeyCol);
					$objPage->setRequest('slt_delete_key_col',$strDeleteKeyCol);
					$objData->setFieldValue('issearch','0::1:-1');	
					$objData->performAction($strAction,"",true);

					$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_ADD_MSG));

					
					include_once("./base/med_module.php"); 
					$objModule=new MedModule();		
					$rsCountRecord=$objModule->getMemTblTable();
					$intHidTableId = $rsCountRecord[0]['max(table_id)'];
					
					
					$intInsertedId =	$objData->getAutoId();
					if($strAddFieldsOfTable != "")
						$objModule->insertIntoTableFields($intInsertedId,$strAddFieldsOfTable);
					
					$strUrl="index.php?file=med_page_fields_add&hid_page_type=".$strPageType."&hidin_module_id=0&hid_table_id=".$intHidTableId."";
					header("Location: $strUrl");
					exit;
					break;

		case "E" :	
					$objData->setProperty("tbl_table",table_id,null,null);
					$objData->setFieldValue("module_id",$strModuleId);
					$objData->setFieldValue("add_key_col",$strAddKeyCol);
					$objData->setFieldValue("delete_key_col",$strDeleteKeyCol);
					$objData->setFieldValue("edit_key_col",$strEditKeyCol);
					$objData->setFieldValue("issearch",$strissearch);
					$objPage->setRequest('slt_edit_key_col',$strEditKeyCol);
					$objPage->setRequest('slt_delete_key_col',$strDeleteKeyCol);
					$objData->performAction($strAction,"",true);
					$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_MOD_MSG));
					
					
					if($strAddFieldsOfTable != "")
						$objModule->insertIntoTableFields($intHidTableId,$strAddFieldsOfTable);
					
					$strUrl="index.php?file=med_page_add&hid_page_type=".$strPageType."&hidin_module_id=0&table_id=".$intHidTableId."&hid_table_id=".$intHidTableId."";
					header("Location: $strUrl");
					exit;
					break;
						
		case "D" : 
					
					
					$_POST[$_POST["hid_listname"]."_cSlcPK"] = explode(",",$_POST[$_POST["hid_listname"]."_cSlcPK"]);
							
					$objData->setProperty("tbl_table","table_id",null,null);
					$objData->performAction($strAction,"");
					 			
					$objData->setProperty("tbl_fields","table_id",null,null);
					$objData->performAction($strAction,"");

					$objData->setProperty("tbl_buttons","table_id",null,null);
					$objData->performAction($strAction,"");
					
					$objData->setProperty("tbl_table_multi","table_id",null,null);
					$objData->performAction($strAction,"");
					
					$objData->setProperty("tbl_search","table_id",null,null);
					$objData->performAction($strAction,"");
					
					$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_DEL_MSG));
					$strUrl="index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0";
					header("Location: $strUrl");
					exit;
					break;
					
		case "C" :
					
					
					for($intSelId=0;$intSelId<count($_POST['search_cSlcPK']);$intSelId++)
					{
						$strSql = "INSERT INTO `tbl_table` (`field_referal`,`list_table_name` , `addedit_table_name` , `module_id` , `page_title` , `where_clause` , `order_clause` , `group_clause` , `having_clause` , `table_type` , `delete_key_col` , `add_key_col` , `edit_key_col` , `addedit_action_link` , `fixed_title` , `issearch` , `table_desc`) select `field_referal`,`list_table_name` , `addedit_table_name` , `module_id` , `page_title` , `where_clause` , `order_clause` , `group_clause` , `having_clause` , `table_type` , `delete_key_col` , `add_key_col` , `edit_key_col` , `addedit_action_link` , `fixed_title` , `issearch` , `table_desc`  from tbl_table where table_id=".$_POST['search_cSlcPK'][$intSelId]."";
						$objMedDb=MedDB::getDBObject();
						$objMedDb->executeQuery($strSql);
					}	
						$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("Copy Table Success Fully."));
						$strUrl="index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0";
						
					break;			
	}
?>



