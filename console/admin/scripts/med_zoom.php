<?php

	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	include_once("./base/med_module.php");
	$objModule		= new MedModule(); 				
	
	
	$strMiddle		= "./middle/med_zoom.htm";
	$strIndex 		= $strMiddle  ;
	
	
	$strLabel		= $objPage->getRequest("labelName");
	$blnControlFlag	= $objPage->getRequest("ControlFlag");
	$strControlName	= $objPage->getRequest("contName");
	$intHelpCode	= $objPage->getRequest('code');

	
	if(!empty($intHelpCode) || $intHelpCode == "")
	{
		$rsHelpDesc		= $objModule->getHelpRecord($intHelpCode);
		if(count($rsHelpDesc)>0)
		{
			$strHelpTitle 	= $rsHelpDesc[0]['help_title'];
			$strHelpDesc 	= $rsHelpDesc[0]['help_desc'];
		}
	}	
	
	$strTextArea 	= $objPage->getTextArea("Ta","zoom_desc","Description",":90:8","class='comn-input'","",0,"");

	
	$localValues = array("strTextArea"=>$strTextArea,"strLabel"=>$strLabel,"strControlName"=>$strControlName,"strHelpDesc"=>$strHelpDesc,"strHelpTitle"=>$strHelpTitle,"blnControlFlag"=>$blnControlFlag);
?>

