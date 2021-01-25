<?php

	
	
	
	
	include_once('./../base/meditab/med_quicklist.php');
		
	$strMiddle="./middle/med_page_fields_listings.htm";
	
	$intTableId 	=  	$objPage->getRequest('hid_table_id');
	$strPageType 	= 	$objPage->getRequest('hid_page_type');
	$intButtonId 	= 	$objPage->getRequest('hid_button_id');
	$intModuleId 	= 	$objPage->getRequest('hidin_module_id');
	$strFile 		= 	$objPage->getRequest('file');
	$arrFieldList	=	$objPage->getRequest("Sr_field_list");
	if(empty($arrFieldList))
	{
		$arrFieldList	=	array("2:field_name",
								  "3:field_type",
								  "7:show_in",
								  "222:field_referal",
								  "6:field_title",
								 "14:seq_no"
								  );
	}
	$arrFieldsUpdate	=	array();
	if(!is_array($arrFieldList))
		$arrFieldList	=	@explode(",",$arrFieldList);
	for($intFieldList = 0; $intFieldList < count($arrFieldList); $intFieldList++)
	{
		$arrFields	=	explode(":",$arrFieldList[$intFieldList]);
		$strFields	.=	$arrFields[0].",";
		$arrFieldsUpdate[]	=	$arrFields[1];
	}
	$strFields	=	trim($strFields,",");

	$objPage->setAddFieldsInList($strFields);
	$strPage 	= $objPage->getHtmlPage(2,"L");
	$strAction	= $objPage->getRequest('hid_page_type'); 
	$intParentId =	$objPage->getRequest('parent_id');
	
	
	$objPage->setRequest("hidListTableId",2);
	
	
	$arrSearch = $objPage->generateSearch(2);
	
		
	
	
	if(!empty($strAction))
	{
		$objData = new MedData(); 
		$add_field_type_extra = $objPage->getRequest('add_field_type_extra');
		
		switch($strAction)
		{	
			case "A" :
							$show_in = implode(",",$objPage->getRequest('show_in'));
							if($add_field_type_extra != "")
								$addedit_field_html_type = ($objPage->getRequest('add_field_type').":".$add_field_type_extra);
							else
								$addedit_field_html_type = $objPage->getRequest('add_field_type');
							$objData->setProperty("tbl_fields","id",null,null);
							$objData->setFieldValue("addedit_field_html_type",$addedit_field_html_type);
							$objData->setFieldValue("show_in",$show_in);							
							$objData->performAction($strAction,$strWhere,true);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_ADD_MSG));
							
							
							
								$strUrl="index.php?file=med_page_fields_listings&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;
							break;
			case "E" :
							
							$strId = $objPage->getRequest('hid_id');
							
							$show_in = implode(",",$objPage->getRequest('show_in'));
							if($add_field_type_extra != "")
								$addedit_field_html_type = ($objPage->getRequest('add_field_type').":".$add_field_type_extra);
							else
								$addedit_field_html_type = $objPage->getRequest('add_field_type');
							$objData->setArrRPAFields("field_id");
							$objData->setProperty("tbl_fields","id",null,null);
							$objData->setFieldValue("addedit_field_html_type",$addedit_field_html_type);
							$objData->setFieldValue("show_in",$show_in);							
							$objData->performAction($strAction,"",true);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_MOD_MSG));
							
							
								$strUrl="index.php?file=med_page_fields_listings&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;
							break;
			case "D" :
							
							$_POST[$_POST["hid_listname"]."_cSlcPK"] = explode(",",$_POST[$_POST["hid_listname"]."_cSlcPK"]);
							
								$objData->setProperty("tbl_fields","id",null,null);
							$objData->performAction($strAction,"");
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_DEL_MSG));
							$strUrl="index.php?file=med_page_fields_listings&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;
							break;						
			case "MU" :	
							$objData->setProperty("tbl_fields","id",null,null);
							$objData->performAction($strAction,"","",$arrFieldsUpdate);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_MOD_MSG));
							
							$arrHiddens	=	array("file"			=>	"med_page_fields_listings",
												  "hid_page_type"	=>	"L",
												  "hidin_module_id"	=>	"0",
												  "parent_id"		=>	$intParentId,
												  "hid_table_id"	=>	$intTableId,
												  "Sr_field_list"	=>	@implode(",",$arrFieldList));
							
							$objPage->restoreSearchResult($arrHiddens);
							exit;
							break;		
							
			case "C"  :
							include_once("./base/med_module.php");
							$objModule=new MedModule();
							$intSelValue =  $objPage->getRequest('list2_cSlcPK');
							$intInsertId =  $objPage->getRequest('Sr_slt_table_list');
							for($intSelId=0;$intSelId<count($intSelValue);$intSelId++)
							{
								$intTotVal .= "'".$intSelValue[$intSelId]."',";
							}	
							$intTotId = substr($intTotVal,0,strlen($intTotVal)-1);

							$resFields   = $objModule->getSelectedField($intTotId);
							

							$intInsertId = $objModule->InsertSelectedField($resFields,$intInsertId);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("Copied fields succeesfully."));
							$strUrl="index.php?file=med_page_fields_listings&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;
							break;					
		}
	}	
	

	
	$strTitle .= $objPage->getPageTitleByDb($intTableId);
	
	$strModuleName 		= 	$strTitle;
	
	$strFieldListCombo	=	$objPage->generateCombobox("LIST_COMBO_FIELDS","Sr_field_list",$arrFieldList,"class='comn-input'",true);

	
	$strMessage = $objPage->objGeneral->getMessage();
	
	
	$localValues = array("intButtonId"=>$intButtonId,"strFile"=>$strFile,"strPage"=>$strPage,"intTableId"=>$intTableId,
						 "strPageType"=>$strPageType,"intModuleId"=>$intModuleId,"strMessage"=>$strMessage,
						 "strModuleName"=>$strModuleName,"strFieldListCombo"=>$strFieldListCombo);
	$localValues = array_merge($localValues,$arrSearch);



?>
