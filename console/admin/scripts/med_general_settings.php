<?php

	
	$objPage->objGeneral->checkAuth(25,"L");
	
	
	include_once("./base/med_module.php");
	
	$objModule			=	new MedModule();	
	
	
	$strMiddle			=	"./middle/med_general_settings.htm";
	
	
	$intModuleId		=	$objPage->getRequest('hidin_module_id');					
	$intSubModuleId		=	$objPage->getRequest('hid_sub_module_id');				

	
	$strAddButton		=	$objPage->getButton("add","class='btn'","Add",0,"onclick=\"doAction(this,'list1','1:A:1:med_general_settings_add',0,'')\"");
	$strDeleteButton	=	$objPage->getButton("delete","class='btn'","Delete",0,"onclick='return deleteSettings()'");
	
	
	$rsGeneralSettings	=	$objModule->getGeneralSettings($intModuleId,$intSubModuleId);

	$intCount			=	count($rsGeneralSettings);
	
	for($intCnt = 0; $intCnt < count($rsGeneralSettings); $intCnt++)
	{
		$rsGeneralSettings[$intCnt]['var_desc']	=	substr($rsGeneralSettings[$intCnt]['var_desc'],0,35);
	}
	
	$strModuleName 		= 	"General Settings";
	
	
	$strMessage			=	$objPage->objGeneral->getMessage();	
	
	
	$localValues 		= 	array(
									"objPage"			=>	$objPage,
									"rsGeneralSettings"	=>	$rsGeneralSettings,
									"intCount"			=>	$intCount,
									"intModuleId"		=>	$intModuleId,
									"intSubModuleId"	=>	$intSubModuleId,
									"strModuleName"		=>	$strModuleName,
									"strMessage"		=>	$strMessage,
									"strAddButton"		=>	$strAddButton,
									"strDeleteButton"	=>	$strDeleteButton
								);	
?>