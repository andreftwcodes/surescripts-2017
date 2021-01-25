<?php

	
	$strMiddle		=	"./middle/med_confirm_delete.htm";
	$strPageType	=	$objPage->getRequest('hid_page_type_confirm');
	$strFile		=	"med_confirm_delete";
	
	if($objPage->getRequest("strKeys") != "")
	{
		$strKeys	= 	$objPage->getRequest("strKeys");
		$strValues	= 	$objPage->getRequest("strValues");
	}
	else
	{
		$arrRequest	=	$_REQUEST;
		$strPkName	=	$_REQUEST["hid_listname"];
		$arrRequest[$strPkName."_cSlcPK"] = @implode(",",$arrRequest[$strPkName."_cSlcPK"]);
		$strKeys	=	@implode("#*#",array_keys($arrRequest));
		$arrRequest['Sr_field_list'] = @implode(",",$arrRequest['Sr_field_list']);
		$strValues	=	@implode("#*#",$arrRequest);
	}
	
	if($strPageType =="A")	
	{
		
		$objData	=	new MedData("admin_master","admin_id","");
									
		
		$intAdminId	=	$objPage->objGeneral->getSession("intAdminId");
		
		
		$strPassword=	md5($objPage->getRequest('TaRpas_password'));							
		
		
		$strWhere	=	"admin_id=".$intAdminId." and password='".$strPassword."'";			

		
		$rsPass=$objData->getAll("password",$strWhere,null,false);	
		
		
		if(count($rsPass)>0)																
		{
			
			$arrKeys	=	explode("#*#",$strKeys);
			$arrValues	=	explode("#*#",$strValues);
			
			
			for($intKeys = 0; $intKeys < count($arrKeys) ; $intKeys++)
				$objPage->addArray($arrHiddens,$arrKeys[$intKeys],$arrValues[$intKeys]);
			
			
			$arrHiddens['file'] 			= 	$arrHiddens['med_delete_url'];
			
			
			$objPage->restoreSearchResult($arrHiddens);
		}
		else
		{
			
			$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage("VALID_PASS"));	
		}
	}
	
	
	$strField1	=	$objPage->getPasswordTextBox("Ta","password","Password","class='comn-input' size=20",1);
	$strField2	=	$objPage->getSubmitButton("submit","class='btn' border='0'","Submit",0,"onclick='return submit_form(this.form);'");

	
	$strMessage	=	$objPage->objGeneral->getMessage();	
	
	
	$localValues = array("strKeys"=>$strKeys,"strValues"=>$strValues,"strField1"=>$strField1,"strField2"=>$strField2,
						 "strMessage"=>$strMessage,"strFile"=>$strFile);
?>