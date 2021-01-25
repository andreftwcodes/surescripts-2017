<?php

	
	
		
	
	include_once('./../base/meditab/med_quicklist.php');
		
	$strMiddle="./middle/med_page_search_listings.htm";
	
	
	$intTableId 	= 	$objPage->getRequest('hid_table_id');
	$strPageType 	= 	$objPage->getRequest('hid_page_type');
	$intModuleId 	= 	$objPage->getRequest('hidin_module_id');
	$intButtonId 	= 	$objPage->getRequest('hid_button_id');
	$strFile 		= 	$objPage->getRequest('file');
	$intParentId 	= 	$objPage->getRequest('parent_id');
	$arrFieldList	=	$objPage->getRequest("Sr_field_list");
	if(empty($arrFieldList))
	{
		$arrFieldList	=	array("620:field_name",
								  "621:field_referal",
								  "622:field_type",
								  "629:seq_no");
	}
	$arrFieldsUpdate	=	array();
	if(!is_array($arrFieldList))
		$arrFieldList	=	@explode(",",$arrFieldList);
	for($intFieldList = 0; $intFieldList < count($arrFieldList); $intFieldList++)
	{
		$arrFields	=	explode(":",$arrFieldList[$intFieldList]);
		$strFields	.=	$arrFields[0].",";
		$arrFieldsUpdate[]	=	str_replace("`","",$arrFields[1]);
	}
	$strFields	=	trim($strFields,",");
	$objPage->setAddFieldsInList($strFields);
	$strPage 		= $objPage->getHtmlPage(5,L);
	
	$strAction		= $objPage->getRequest('hid_page_type'); 
	
	$objPage->setRequest("hidListTableId",5);
	
	
	  $arrSearch = $objPage->generateSearch(2);
	
	
	
	if(!empty($strAction))
	{
		$objData = new MedData(); 
		switch($strAction)
		{	
			case "A" 		:
							$objData->setProperty("tbl_search","id",null,null);
							$objData->performAction($strAction,$strWhere,true);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_ADD_MSG));
							$strUrl="index.php?file=med_page_search_listings&parent_id=".$intParentId."&hid_page_type=L&hid_table_id=".$intTableId."&hidin_module_id=0";
							header("Location: $strUrl");
							exit;
							break;
			case "E" 		:
							$objData->setProperty("tbl_search","id",null,null);
							$objData->setArrRPAFields("field_id");
							$objData->performAction($strAction,"",true);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_MOD_MSG));
							$strUrl="index.php?file=med_page_search_listings&parent_id=".$intParentId."&hid_page_type=L&hid_table_id=".$intTableId."&hidin_module_id=0";
							header("Location: $strUrl");
							exit;
							break;
			case "D" 		:
							
							$_POST[$_POST["hid_listname"]."_cSlcPK"] = explode(",",$_POST[$_POST["hid_listname"]."_cSlcPK"]);
							$objData->setProperty("tbl_search","id",null,null);
							$objData->performAction($strAction,"");
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_DEL_MSG));
							$strUrl="index.php?file=med_page_search_listings&parent_id=".$intParentId."&hid_page_type=L&hid_table_id=".$intTableId."&hidin_module_id=0";
							header("Location: $strUrl");
							exit;
							break;						
			case "MU" 		:	
							$objData->setProperty("tbl_search","id",null,null);
							$objData->performAction($strAction,"","",$arrFieldsUpdate);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage(REC_MOD_MSG));
							$arrHiddens	=	array("file"			=>	"med_page_search_listings",
												  "hid_page_type"	=>	"L",
												  "hidin_module_id"	=>	"0",
												  "parent_id"		=>	$intParentId,
												  "hid_table_id"	=>	$intTableId,
												  "Sr_field_list"	=>	@implode(",",$arrFieldList));
							$objPage->restoreSearchResult($arrHiddens);
							exit;
							break;		
			case "C"  		:
							include_once("./base/med_module.php");
							$objModule=new MedModule();
							$intSelValue =  $objPage->getRequest('list5_cSlcPK');
							$intInsertId =  $objPage->getRequest('Sr_slt_table_list');
							for($intSelId=0;$intSelId<count($intSelValue);$intSelId++)
							{
								$intTotVal .= "'".$intSelValue[$intSelId]."',";
							}	
								$intTotId = substr($intTotVal,0,strlen($intTotVal)-1);
							$resFields   = $objModule->getSelectedSearch($intTotId);
							$intInsertId = $objModule->InsertSelectedSearch($resFields,$intInsertId);
							$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("Copy Fields SucceesFully."));
							$strUrl="index.php?file=med_page_search_listings&parent_id=".$intParentId."&hid_page_type=L&hidin_module_id=0&hid_table_id=".$intTableId."";
							header("Location: $strUrl");
							exit;
							break;									
		}
	}	

	$strModuleName = $objPage->getPageTitleByDb($intTableId);
	$strFieldListCombo	=	$objPage->generateCombobox("LIST_COMBO_FIELDS","Sr_field_list",$arrFieldList,"class='comn-input'",true);

	
	$strMessage = $objPage->objGeneral->getMessage();
	
	
	$localValues = array("intButtonId"=>$intButtonId,"strFile"=>$strFile,"strPage"=>$strPage,"intTableId"=>$intTableId,
						 "strPageType"=>$strPageType,"intModuleId"=>$intModuleId,"strMessage"=>$strMessage,
						 "strModuleName"=>$strModuleName,"strFieldListCombo"=>$strFieldListCombo);
	$localValues = array_merge($localValues,$arrSearch);
?>
