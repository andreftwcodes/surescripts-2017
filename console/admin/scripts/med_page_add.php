<?php

	
	$objPage->objGeneral->checkAuth(1);
	
	
	include_once("./../base/meditab/med_quicklist.php");

	
	include_once("./../base/json/json.php");

	
	include_once("./base/med_module.php"); 
	

	
	
	
	$strMiddle="./middle/med_page_add.htm";
	
	
	$intTableId		=	$objPage->getRequest('hid_table_id'); 
	$strPageType 	=	$objPage->getRequest('hid_page_type');
	$intPkId 		=	$objPage->getRequest('hid_table_id');		
	$intButtonId 	=	$objPage->getRequest('hid_button_id');
	$strFile 		=	$objPage->getRequest('file');
	
	$objPage->setRequest('EditTbl',"admin_master");
	$objPage->setRequest('DeleteTbl',"admin_master");
	
	
	$strPage 		=	$objPage->getHtmlPage("1",$strPageType,true);

	
	if($intTableId != NULL) $rsFields=$objModule->getMemTblTable($intTableId);

	
	$module="<input type=\"hidden\" name=\"hidin_module_id\" value=\"0\">";

	
	$strSelected 	=	$rsFields[0]['addedit_table_name'];
	$strSysTbl 		= 	$objPage->generateCombobox("SYSTABLE","SysTable",$strSelected);
	

	
	$strAddVal 		= 	$rsFields[0]['add_key_col'];
	$strAddTbl 		= 	$objPage->generateCombobox("SP_TABLE","AddTbl",$strAddVal);
	
	
	
	$strAddFieldsOfTable= 	$objPage->generateCombobox("SP_TABLE_WITH_SPACE","slt_add_field_table","");

	
	$strDelete 		= 	explode(":",$rsFields[0]['delete_key_col']);
	$strDeleteVal 	= 	$strDelete[0];
	$intPkVal 		= 	$strDelete[1];
	$strDeleteTbl 	= 	$objPage->generateCombobox("SP_TABLE","DeleteTbl",$strDeleteVal,"onchange=\"getChangeDeleteColumn();\"");
	

	
	$strEdit 		=	explode(":",$rsFields[0]['edit_key_col']);
	$strEditVal 	=	$strEdit[0];
	$intPkVal 		= 	$strEdit[1];
	$strEditTbl 	= 	$objPage->generateCombobox("SP_TABLE","EditTbl",$strEditVal,"onchange=\"getChangeEditColumn();\"");
	
	

	
	if($strPageType == "E")
	{
		
	
		if($rsFields[0]['issearch'] != NULL)
		{
			$issearch = explode(":",$rsFields[0]['issearch']);
			$strComboVal 	= $issearch[0];
			$strAlphaVal 	= $issearch[1];
			$strPagingVal 	= $issearch[2];
			$strSelectorVal = $issearch[3];
		}
		
		
		$strCombo = $objPage->generateCombobox("YES/NO","strCombo",$strComboVal);
		
	
		
		$strPaging = $objPage->generateCombobox("YES/NO","strPaging",$strPagingVal);
		
	
		$rsAlpahaSearch = $objModule->getMemTableFields($intTableId,L);
		
		$arrOptionDatas = NULL;
		if(count($rsAlpahaSearch) > 0)
		{ 	
			$objPage->addArray($arrOptionDatas,"","");
			for($intAlphaSearch=0;$intAlphaSearch<count($rsAlpahaSearch);$intAlphaSearch++)
			{
				$objPage->addArray($arrOptionDatas,$rsAlpahaSearch[$intAlphaSearch]['field_name'],$rsAlpahaSearch[$intAlphaSearch]['field_title']);
			}
		}
		
		$strAlphaFields=$objPage->fillCombobox($arrOptionDatas,$strAlphaVal,false,"");
		
		$strSelector = $objPage->generateCombobox("SELECTOR","Selector",$strSelectorVal);
	}
	

	
	if($intTableId > 0 && $intTableId !="")
	{
		$strTitle .= $objPage->getPageTitleByDb($intTableId);
		
		$strModuleName 		= 	$strTitle;
	}
	else 
	{
		$strModuleName		=	"Add Table";
	}
	
	
		
	
	$strMessage = $objPage->objGeneral->getMessage();
	
	
	$localValues = array("strPageType"=>$strPageType,"intPkVal"=>$intPkVal,"rsFields"=>$rsFields,"strSysTbl"=>$strSysTbl,
					"rsAlpMasterjson"=>$rsAlpMasterjson,"intTableId"=>$intTableId,"strPageType"=>$strPageType,
					"intModuleId"=>$intModuleId,"module"=>$module,"strDeleteTbl"=>$strDeleteTbl,"strEditTbl"=>$strEditTbl,
					"strAddTbl"=>$strAddTbl,"intButtonId"=>$intButtonId,"intTableId"=>$intTableId,"strPageType"=>$strPageType,
					"intModuleId"=>$intModuleId,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName,"intPkId"=>$intPkId,
					"strScriptType"=>$strScriptType,"strCombo"=>$strCombo,"strPaging"=>$strPaging,"strAlphaFields"=>$strAlphaFields,
					"strSelector"=>$strSelector,"strAddFieldsOfTable"=>$strAddFieldsOfTable);

	
	$localValues = array_merge($localValues,$strPage);

?>

