<?php

	
	$objPage->objGeneral->checkAuth("15");

	
	$objData= new MedData();
	
	
	$objPage->setTableProperty($objData);
	
	
	$strAction	=	$objPage->getRequest("hid_page_type");
	
	
	if(!empty($strAction))
	{
		if($objPage->getRequest("hid_admin_id") == 1)
		{
			$objData->setArrRPAFields("admin_role");
			$objData->setArrRPAFields("office_id");
		}
		
		$objData->performAction($strAction,"",true);
		
		if($objData->arrNotUniqueFields==NULL)
		{
			
			include_once('./../base/meditab/med_quicklist.php');
			
			
			$strFile		=	"med_list_record";
			$strPageType	=	"L";
			
			$strMiddle		=	"./middle/med_list_record.htm";
		}
		else
		{
			
			$strPageType	=	$objPage->getRequest('hid_page_type');
			
			$strMiddle		=	"./middle/med_admin_addedit.htm";
			
			
			$strSumbitButton=$objPage->getSubmitButton("submit","border='0' onclick=\"return submit_form(this.form);\" class=\"btn\"","Submit");
		}
	
		
		$intTableId		=	$objPage->getRequest('hid_table_id');
	
		
		if($intTableId==15) 
		{	
			$objPage->setRequest("arrRestrictedValues",array("1",$objPage->objGeneral->getSession('intAdminId')));	
			if($objPage->objGeneral->getSession('intOffice_id')!=0)
				$objPage->setRequest("strWhere","office_id=".$objPage->objGeneral->getSession('intOffice_id'));	
		}	
		
		
		$strModuleName	=	$objPage->getPageTitleByDb($intTableId);	
		
		if($strPageType!='L') $strPage		=	$objPage->getHtmlPage($intTableId,$strPageType,true);		
		else $strPage		=	$objPage->getHtmlPage($intTableId,$strPageType); 
		
		$strMessage		=	$objPage->objGeneral->getMessage();
		 
		
		if($strPageType!='L') 
		{
			$localValues	=	array("strModuleName"=>$strModuleName,"strFile"=>$strFile,"intTableId"=>$intTableId,
			"strPageType"=>$strPageType,"strMessage"=>$strMessage,"strSumbitButton"=>$strSumbitButton
			);
			$localValues=array_merge($localValues,$strPage);
		}				
		else
			$localValues	=	array("strModuleName"=>$strModuleName,"strFile"=>$strFile,"strPage"=>$strPage,"intTableId"=>$intTableId,"strPageType"=>$strPageType,"strMessage"=>$strMessage);			
	}
	else
		$objPage->objGeneral->raiseError("WARNING","No action define","Action Script","Do not call action script without action parameters"); 
?>