<?php

	
	
	
	include_once("./base/med_module.php"); 
			
	
	$objModule	=	new MedModule();
		
	$intParentId	=	$objPage->getRequest("parent_id");
	$arrCurrentUrl	=	explode("&parent_id",$_SERVER['QUERY_STRING']);
	$strCheckDevPass	=	$objModule->checkDevPassword($intParentId,$arrCurrentUrl[0]);

	if($strCheckDevPass=='Yes' && ($objPage->objGeneral->getSession("strDevPass")=="" || $objPage->objGeneral->getSession("strDevPass")!=$objPage->objGeneral->getSettings('DEV_PASS')))
	{
		
		$strMiddle	=	"./middle/med_dev_pass.htm";	 
	
		
		$strPasswordFieldName	=	$objPage->generateHtmlControlName('password',true,'Ta','password');
		
		
		if($objPage->getRequest($strPasswordFieldName) != "")	
		{
			
			$strReturnUrl	=	$objPage->getRequest('hid_return_url');
			
			
			if(md5($objPage->getRequest($strPasswordFieldName))==$objPage->objGeneral->getSettings('DEV_PASS'))
			{
				
				
				
				$objPage->objGeneral->setSession("strDevPass",md5($objPage->getRequest($strPasswordFieldName)));
				
				header("Location: index.php?".$strReturnUrl);
				exit;
			}						
			else
				$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("INVALID_DEV_PASS"));
		}
		else
			$strReturnUrl	=	$_SERVER['QUERY_STRING'];
	
		
		$strPasswordField	=	$objPage->getPasswordTextBox("Ta","password","Developer Password","class='comn-input' size=20",1);
		$strButton			=	$objPage->getSubmitButton("submit","class='btn' border='0'","Submit",0,"onclick='return submit_form(this.form);'");
	
		
		$strMessage		=	$objPage->objGeneral->getMessage();	
		
		
		$localValues	=	array("strMessage"			=>	$strMessage,
								  "strPasswordField"	=>	$strPasswordField,
								  "strButton"			=>	$strButton,
								  "strReturnUrl"		=>	$strReturnUrl,
								  "intParentId"			=>	$intParentId);
	}
?>