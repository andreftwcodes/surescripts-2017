<?php


	
	
	include_once("./base/med_module.php");
	$objModule		= new MedModule(); 				
	
	
	$strMiddle="./middle/med_help.htm";		 
	$strIndex		=	$strMiddle;
	
	
	$intModuleId  	= $objPage->objGeneral->getSession("module_id");
	$intHelpCode	= $objPage->getRequest('code');
	
	
	$rsHelpResult	= $objModule->getHelpRecord($intHelpCode);
	if(count($rsHelpResult)>0)
	{
		$strHelpTitle 	= $rsHelpResult[0]['help_title'];
		$strHelpDesc 	= $rsHelpResult[0]['help_desc'];
	}
	else
	{
		$strHelpTitle 	= "Help";
		$strHelpDesc 	= "Sorry It has no Content";
	}
	
	
	
	$localValues = array("strHelpTitle"=>$strHelpTitle,"strHelpDesc"=>$strHelpDesc,"strThemePath"=>$strThemePath,"intModuleId"=>$intModuleId);
?>