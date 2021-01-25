<?php

	
	
	
	
	include_once("./base/med_module.php"); 
		
	
	$objModule = new MedModule();

	
	$intAdminId		=	$objPage->getRequest('admin_id');
	$strPageType	=	$objPage->getRequest('hid_page_type');
	$blnFromProfile	=	$objPage->getRequest('blnFromProfile');
	
	
	if(!$objModule->validOffice("admin_master","office_id","admin_id",$intAdminId))
		$strMiddle	= "./middle/med_notauthorize.htm";		 	
	else
		$strMiddle	=	"./middle/med_change_pass.htm";	 

	
	if($strPageType =="A")	
	{
		
		$objData	=	new MedData("admin_master","admin_id","");							
		
		
		$strWhere		=	"admin_id=".$intAdminId;	
		$arrFieldValue	=	array("password"=>md5($objPage->getRequest('TaRpas_password')));
			
		
		$objData->updateRows($arrFieldValue, $strWhere);								
			
		
		$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("CHG_PASS_MSG"));
		
		if($blnFromProfile == '')
			header("Location: index.php?file=med_list_record&hid_table_id=15&hid_page_type=L");
		else
			header("Location: index.php?file=med_profile_edit");
		exit;
	}

	
	$strField2	=	$objPage->getPasswordTextBox("Ta","password","New Password","class='comn-input' size=20",1);
	$strField3	=	$objPage->getPasswordTextBox("","cpass","Confirm Password","class='comn-input' size=20",1);		
	$strField4	=	$objPage->getSubmitButton("submit","class='btn' border='0'","Change Password",0,"onclick='return submit_form(this.form);'");

	
	$strMessage		=	$objPage->objGeneral->getMessage();	
	
	
	$localValues	=	array(
								"strMessage"	=>	$strMessage,
								"strField2"		=>	$strField2,
								"strField3"		=>	$strField3,
								"strField4"		=>	$strField4,
								"intAdminId"	=>	$intAdminId,
								"blnFromProfile"=>	$blnFromProfile
							);

?>