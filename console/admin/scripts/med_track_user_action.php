<?php

	
	$objPage->objGeneral->checkAuth(109);
	
	
	include_once('./../base/meditab/med_grouplist.php');
	
	
	include_once("./base/med_module.php"); 
	
	
	$objModule		=	new MedModule();
		
	
	$strMiddle		=	"./middle/med_track_users.htm";
	
	
	$strAction		=	$objPage->getRequest('hid_page_type'); 
	$intTableId 	=	$objPage->getRequest('hid_table_id');
	$intButtonId 	=	$objPage->getRequest('hid_button_id');	
	
	
	if(!empty($strAction))
	{	
		
		$objData 	= 	new MedData();
		
		
		$objPage->setTableProperty($objData); 
		
		
		$objData->performAction($strAction,null); 
		
		
		$strPageType 	= 	L;
	
		$strFile 		= 	$objPage->getRequest('file');
		$strDeptId		= 	$objPage->getRequest('slt_dept');
		$strEmpId		= 	$objPage->getRequest('slt_emp');
		$strStartDate	= 	$objPage->getRequest('Dttxt_start_date');
		$strEndDate		= 	$objPage->getRequest('Dttxt_end_date');
		$blnPrint		= 	$objPage->getRequest('print');
		$strGroupBy		= 	$objPage->getRequest('slt_group_by');
		$intOfficeIdVal = 	$objPage->getRequest('slt_office_id');
	
		
	
		if(empty($intOfficeIdVal))
		{
			if($intOfficeId==0)
			{
				$strOffice 	= 	$objModule->getAllOfficeId();
				
				$objPage->setRequest("slt_office_id",$strOffice);	
			}
			else
			{
				$objPage->setRequest("slt_office_id",$intOfficeId);	
			}
		}
		elseif($intOfficeIdVal==0)
		{
			$strOffice 		= 	$objModule->getAllOfficeId();
			
			$objPage->setRequest("slt_office_id",$strOffice);		
		}
	
		
		if(empty($strDeptId))
		{	
			$intDeptId		= 	$strDeptId;
			$strDeptId 		= 	$objModule->getAllDepartment();
			
			$objPage->setRequest("slt_dept",$strDeptId);		
		}
		elseif(trim(strtoupper($strDeptId))=='ALL')
		{
			$strDeptId 		= 	$objModule->getAllDepartment();
			$intDeptId		= 	"all";
			
			$objPage->setRequest("slt_dept",$strDeptId);		
		}
		else
		{
			$intDeptId		= 	$strDeptId;	
		}
	
		
		if(empty($strStartDate)) 
			$strStartDate	= 	date("m-d-Y",mktime(0, 0, 0, date("m")  , date("d")-7, date("Y")));
		
		
		if(empty($strEndDate)) 
			$strEndDate		= 	date('m-d-Y');
		
		$dtToDate		= 	split("-",$strStartDate); 
		$dtToDate		= 	@date("Y-m-d",@mktime(0,0,0,$dtToDate[0],$dtToDate[1],$dtToDate[2])); 
		
		$dtFromDate		= 	split("-",$strEndDate); 
		$dtFromDate		= 	@date("Y-m-d",@mktime(0,0,0,$dtFromDate[0],$dtFromDate[1],$dtFromDate[2])); 
		
	
		if($strEmpId==NULL) 
		{
			$strSelectedEmp	=	"all";
		}
		else if(is_array($strEmpId))
		{
			$strSelectedEmp	=	implode(",",$strEmpId);
		}
		else
		{
			$strSelectedEmp	=	$strEmpId;
		}
			
		if(!empty($strEmpId))
		{
			if(!empty($strSelectedEmp) && trim(strtoupper(substr($strSelectedEmp,0,3))) != "ALL")	
			{
				$strWhere 	.= 	" and track_user.emp_id in(".$strSelectedEmp.")";
			}	
			else
			{	
				$strSelectedEmp1 = $objModule->getDeptEmpList($strDeptId);
				
				$strWhere 	.= 	" and track_user.emp_id in('".$strSelectedEmp1."')";
			}	
			if(!empty($intDeptId) && trim(strtoupper($intDeptId))!='ALL')
			{	 
				$strWhere 	.= 	" and  emp_dept.dept_id = '".$strDeptId."' AND date(visit_date) >= '".$dtToDate."' AND date(visit_date) <= '".$dtFromDate."'";
			}
			elseif(!empty($intDeptId) && trim(strtoupper($intDeptId))=='ALL')
			{
				$strWhere	.= 	" and date(visit_date) >= '".$dtToDate."' and date(visit_date) <='".$dtFromDate."'";				
			}
		
			$objPage->setRequest("strWhere",$strWhere);
	
			
			if(!empty($strGroupBy))
			{
				$objPage->strGroupFieldName	=	$strGroupBy;
				
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
			
			
			if(!empty($strWhere)) 
				$strPage 		= 	$objPage->getHtmlPage($intTableId,$strPageType); 
		}	
	
		$intExpCordinate = $objPage->getRequest("btn_export_x");
		
		if(!empty($intExpCordinate))
		{
			include_once("med_export_excel_file.php");
		}
		
		
		$strTitle		= 	$objPage->getPageTitleByDb($intTableId);
		
		
		$strMessage 	= 	$objPage->objGeneral->getMessage();
		
		
		$strEvent		= 	"style='width:150px' class='comn-input' onchange='changeEmpList()'";
		
		if($intOfficeId == 0)
			$strOfficeCombo	= 	$objPage->getComboBox("office_id","Office Name","OFFICE_LIST_ALL",$intOfficeIdVal,0,$strEvent);
		else
			$strOfficeCombo	= 	$objPage->getComboBox("office_id","Office Name","OFFICE_ADMIN_LIST",$intOfficeIdVal,0,$strEvent);
	
	
		if($objPage->getRequest("slt_office_id")==0)
		{
			$strOffice 		= 	$objModule->getAllOfficeId();
			
			$objPage->setRequest("slt_office_id",$strOffice);	
		}
		
		$strDepart			= 	$objPage->generateCombobox("DEPT_LIST","slt_dept",$intDeptId,$strEvent);
		
		if(trim(strtoupper($objPage->getRequest('slt_dept')))=='ALL')
		{
			$strDeptId 		= 	$objModule->getAllDepartment();
			
			$objPage->setRequest("slt_dept",$strDeptId);		
		}
		
		$strEmp				= 	$objPage->generateCombobox("DEPT_EMP_LIST","slt_emp",$strSelectedEmp,"class='comn-input' style='width:200px'",true);
		$strFromDate		= 	$objPage->getTextBox("Dt","start_date","","",$strStartDate,0,"size=10 class='comn-input'");
		$strToDate			= 	$objPage->getTextBox("Dt","end_date","","",$strEndDate,0,"size=10 class='comn-input'");
		$strGroupByCombo	= 	$objPage->generateCombobox("GROUP_IN_REPORT","slt_group_by",$strGroupBy,"class='comn-input'");
		
		
		$localValues 		= 	array(
										"intButtonId"		=>	$intButtonId,
										"strFile"			=>	$strFile,
										"strPage"			=>	$strPage,
										"intTableId"		=>	$intTableId,
										"strMessage"		=>	$strMessage,
										"strTitle"			=>	$strTitle,
										"strDepart"			=>	$strDepart,
										"strEmp"			=>	$strEmp,
										"strFromDate"		=>	$strFromDate,
										"strToDate"			=>	$strToDate,
										"strGroupByCombo"	=>	$strGroupByCombo,
										"strOfficeCombo"	=>	$strOfficeCombo
									);
	}
	else
		$objPage->objGeneral->raiseError("WARNING","No actions define","med_action","Do not call med_action file without Action parameters"); 
?>