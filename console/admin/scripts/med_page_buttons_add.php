<?php

	
	$objPage->objGeneral->checkAuth(1);
	
	
	include_once("./../base/meditab/med_quicklist.php");
	
	
	$strMiddle="./middle/med_page_buttons_add.htm";
	
	
	$intTableId =  $objPage->getRequest('hid_table_id');
	$strPageType = $objPage->getRequest('hid_page_type');
	$intModuleId = $objPage->getRequest('hidin_module_id');
	$intPkId = $objPage->getRequest('id');
	$intButtonId = $objPage->getRequest('hid_button_id');
	$strFile = $objPage->getRequest('file');

	$strPage = $objPage->getHtmlPage(3,$strPageType,true);
		
	
	
	$strTitle = $objPage->getPageTitleByDb($intTableId);
	 
	$strModuleName = $strTitle;
	
	
	
		
	
	
	$strMessage = $objPage->objGeneral->getMessage();
	
	$strFieldCombo	= $objPage->getComboBox("field_id","Fields","BUTTON_FIELD_COMBO",$intPkId,0," class='comn-input' style='width:300px' onChange=\"window.location.href='index.php?file=med_page_buttons_add&id=' + this.value + '&hid_table_id=".$intTableId."&hid_page_type=E'\"");	
	
	$localValues = array("strFieldCombo"=>$strFieldCombo,"intButtonId"=>$intButtonId,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"intModuleId"=>$intModuleId,"strMessage"=>$strMessage,"strModuleName"=>$strModuleName,"intPkId"=>$intPkId);
	$localValues = array_merge($localValues,$strPage);

?>
