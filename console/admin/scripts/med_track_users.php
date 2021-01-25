<?php

		
	$objPage->objGeneral->checkAuth(109);

	
	include_once('./../base/meditab/med_grouplist.php');
	
	
	include_once("./base/med_module.php"); 
		
	
	$objModule = new MedModule();

	
	$strPageType 	= L;
	$intTableId		= "109";
	$blnPrint		= $objPage->getRequest('print');
	$strSubmit	 	= $objPage->getRequest('smt_submit');
	$strGroupBy		= $objPage->getRequest('Sr_slt_group_by');
	$intValidUser 	= $objPage->getRequest('Sr_slt_valid_user');
	$intExpCordinate = $objPage->getRequest("btn_export_x");
	$intShowRows	= $objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
	$objModule->checkMaxRowLimit($intShowRows);
	
	$strWhere		= "";
	if($blnPrint == 1)
	{
		$strMiddle	= "./middle/med_report_print.htm";	
		$strIndex  = $strMiddle;
		$objPage->setListPaging(false);
		$objPage->strSetListButtons="false";
	}
	else
	{
		
		$strMiddle	=	"./middle/med_track_users.htm";
	}
	
	include("med_emp_search.php");	
	
	if($strSelEmp!=0)
		$strWhere .= " and emp_master.emp_id in (".$strSelEmp.")";
		
	
	if(!empty($strGroupBy))
	{
		$objPage->strGroupFieldName=$strGroupBy;
		switch(trim(strtoupper($strGroupBy)))
		{
			case "ENTRY_DATE":
				$objPage->strNewOrderByCond="date(visit_date) desc, emp_name asc";
				break;
				
			case "EMP_NAME":
				$objPage->strNewOrderByCond="emp_name, date(visit_date) desc";
				break;
		}
	}

	if($intValidUser != "2" && isset($intValidUser))
		$strWhere	.= " and valid_user= '".$intValidUser."'";

	
	if($strSubmit || $blnPrint==1 || !empty($intExpCordinate) || $objPage->getRequest('hid_paging')==1)
		$strPage 			= 	$objPage->getHtmlAll($intTableId,"",false,true,"",true,true,true,$strWhere);
	else
		$strPage 			=   array();
		
	if(!empty($intExpCordinate))
		include_once("med_export_excel_file.php");
	
	
	$strTitle	= $objPage->getPageTitleByDb($intTableId);
	
	
	$strMessage 	= $objPage->objGeneral->getMessage();
	
	
	$localValues 	= array("strFile"=>$strFile,"intTableId"=>$intTableId,"strMessage"=>$strMessage,"strTitle"=>$strTitle);
	$localValues	= array_merge($localValues,$strPage);
	$localValues	= array_merge($localValues,$arrSearchVar);

	
	function list109_DataLoaded($rsData)
	{
		global $objPage;
		
		for($intData=0;$intData<count($rsData[0]);$intData++)
		{
			if($rsData[0][$intData]["valid_user"])
				$strSrc = "images/daily-log-add.gif";	
			else	
				$strSrc = "images/daily-log-m.gif";	
				
			$rsData[0][$intData]["validuserlink"]	=	$objPage->getImage("user_link",$strSrc,"border='0'");
		}
	}		
?>
