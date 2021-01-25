<?php

	
	$objPage->objGeneral->checkAuth(1);
	
	
	include_once("./../base/meditab/med_quicklist.php");

	
	$strMiddle="./middle/med_page_fields_add.htm";		 
	
	
	$intTableId =  $objPage->getRequest('hid_table_id');
	$strPageType = $objPage->getRequest('hid_page_type');
	$intButtonId = $objPage->getRequest('hid_button_id');
	$strFile = $objPage->getRequest('file');
	$intPkId = $objPage->getRequest('id');



	
	$strTitle = $objPage->getPageTitleByDb($intTableId);
	
	$strPage = $objPage->getHtmlPage("2",$strPageType,true);
	if($intPkId != NULL)
	{
		include_once("./base/med_module.php"); 
		$objModule=new MedModule();		
		$rsFields=$objModule->getSpTableFields($intPkId);

	}
	$strSelectedValue = $rsFields[0]['show_in']; 

	
	$strShowIn = $strSelectedValue;

	$showcombo = $objPage->generateCombobox("SP_FIELDS","show_in",$strShowIn,$strEvent=NULL,true);

	
	$strScriptType = $objPage->generateCombobox("FIELD_JAVASCRIPT_TYPE","add_field_type",$strShowIn);
	
	
	$strSelectedValue = $rsFields[0]['addedit_field_html_type']; 
	if(ereg(":",$strSelectedValue))
	{
		$strSelectedValue = explode(":",$strSelectedValue);
		$strAdd_field_type = $strSelectedValue[0];
		$strAdd_field_type_extra = $strSelectedValue[1];
	}
	else
	{
		$strAdd_field_type = $strSelectedValue;
		$strAdd_field_type_extra = "";
	}	
	
	$addedit_field_html_type = $objPage->generateCombobox("ADDEDIT_FIELD_TYPE","add_field_type",$strAdd_field_type); 
	$addedit_field_html_type_extra = $objPage->generateCombobox("ADDEDIT_FIELD_TYPE","add_field_type_extra",$strAdd_field_type_extra);
	
	
	
	
	
	
	$strModuleName = $strTitle;

	
	$strMessage = $objPage->objGeneral->getMessage();
	$strFieldCombo	= $objPage->getComboBox("field_id","Fields","FIELD_COMBO",$intPkId,0," class='comn-input' style='width:300px' onChange=\"window.location.href='index.php?file=med_page_fields_add&id=' + this.value + '&hid_table_id=".$intTableId."&hid_page_type=E'\"");
	
	
	$localValues = array("strFieldCombo"=>$strFieldCombo,"addedit_field_html_type_extra"=>$addedit_field_html_type_extra,"addedit_field_html_type"=>$addedit_field_html_type,"strPageList"=>$strPageList,"intButtonId"=>$intButtonId,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"intModuleId"=>$intModuleId,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName,"intPkId"=>$intPkId,"showcombo"=>$showcombo,"strScriptType"=>$strScriptType,"strShowIn"=>$strShowIn);
	$localValues = array_merge($localValues,$strPage);

?>
