<?php

	
	$strMiddle="./middle/med_login.htm";		
	$strIndex=$strMiddle;						
	
	
	$strUsernameTextBox =	$objPage->getTextBox("Ta","username","Username","class='comn-input' ","",1,"");
	$strPasswordTextBox =	$objPage->getPasswordTextBox("Ta","password","Password","class='comn-input'",1);
	$strLoginUserType	=	$objPage->getRequest('rad_user_type');

	
	$strUsernameTextBoxName=$objPage->generateHtmlControlName('text',true,'Ta','username');
	
	
	$strUserName = $objPage->getRequest($strUsernameTextBoxName);
		
	
	if($objPage->getRequest($strUsernameTextBoxName)!="")
	{	
		
		require_once("./base/med_module.php");
		$objModule=new MedModule();
		
		
		$strPasswordTextBoxName=$objPage->generateHtmlControlName('password',true,'Ta','password');
		
		
		$strPassword = $objPage->getRequest($strPasswordTextBoxName);
		
		$rsLogin=$objModule->checkLogin($strUserName,$strPassword,$strLoginUserType);
		if(count($rsLogin)>0)
		{
			
			if($rsLogin[0]['status'] == 'Active')
			{
				$objPage->objGeneral->setSession("intEmployeeId",$rsLogin[0]['admin_id']);
				$objPage->objGeneral->setSession("intUserId",$rsLogin[0]['admin_id']);
				$objPage->objGeneral->setSession("intAdminId",$rsLogin[0]['admin_id']);
				$objPage->objGeneral->setSession("isMeditabAdmin",$rsLogin[0]['is_meditab_admin']);
				
				
				$strUserName=$rsLogin[0]['firstname']." ".$rsLogin[0]['lastname'];
				
				$objPage->objGeneral->setSession("strUserName",$strUserName);
				$objPage->objGeneral->setSession("intModule_id",1);
				$objPage->objGeneral->setSession("intRights",$strEmployeeRoles);
				$objPage->objGeneral->setSession("intRoleId",$rsLogin[0]['admin_role']);
				$objPage->objGeneral->setSession("intOfficeId",$rsLogin[0]['office_id']);
			
				
				if($rsLogin[0]['office_id'] == "0")
					$strTimeZone=$rsLogin[0]['timezone'];	
				else
					$strTimeZone=$objModule->getTimeZone($rsLogin[0]['office_id']);	 
					
				
				$objPage->objGeneral->setSession("strTimeZone",$strTimeZone);
				
				
				
				
				header("location: index.php?file=med_in_message_transaction");			
				exit;
			}
			else
			{
				
				$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage('GEN_INACTIVE_USR'));
			}
		}
		else
		{	
			
			$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage('GEN_INVALID_USR_PASS'));
		}	
	}

	
	$intAdminId=$objPage->objGeneral->getSessionVarName('intAdminId');
	$strUserName=$objPage->objGeneral->getSessionVarName('strUserName');
	$intModule_id=$objPage->objGeneral->getSessionVarName('intModule_id');

	
	if(isset($_SESSION[$intAdminId])) 		unset($_SESSION[$intAdminId]);
	if(isset($_SESSION[$strUserName]))		unset($_SESSION[$strUserName]);	
	if(isset($_SESSION[$intModule_id]))	unset($_SESSION[$intModule_id]);	

	
	$localValues		=	array(
									"strIndex"				=>	$strIndex,
									"strUsernameTextBox"	=>	$strUsernameTextBox,
									"strPasswordTextBox"	=>	$strPasswordTextBox,
									"objPage"				=>	$objPage,
									"strCheckBox"			=>	$strCheckBox,
									"strLoginUserType"		=>	$strLoginUserType
								);

?>

