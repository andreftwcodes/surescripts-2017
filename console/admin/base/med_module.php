<?php


class MedModule
{ 
	var $objPage;		
	
	function MedModule()
	{
		global $objPage;
		$this->objPage=$objPage;		
	}
	
	
	function checkLogin($strUsername,$strPassword,$strLoginUserType)
	{
		
		// ini_set("soap.wsdl_cache_enabled", "0"); 
				
		
		// $strURL			=	$this->objPage->objGeneral->getSettings('SOAP_SERVICE_URL');
		
		if($strLoginUserType == 'employee')
			$strLoginModule	=	"MEM";
		else if($strLoginUserType == 'var')
			$strLoginModule	=	"VAR";
		
		// try
		// {
			
			
		// 	$objServer 		=	new SoapClient($strURL."mos.php?wsdl",array("trace"=>1));
			
		// 	$strResult		=	$objServer->checkUserAuthentication($strUsername, base64_encode($strPassword), $strLoginModule, "", "52");
		// 	$arrResult		=	@explode("|",$strResult);
			
		// 	$blnSuccess		=	$arrResult[0];
		// 	$intEmpId		=	$arrResult[1];
			
		// 	if($blnSuccess == "1")			
		// 	{
				
		// 		$rsRecord	=	$this->checkAndCreateAdminUser($strUsername, $strPassword, $strLoginUserType, $arrResult);
		// 	}
		// 	else if($blnSuccess == "-1")	
		// 	{
				
		// 		$this->changeAdminUserStatus($strUsername, $strLoginUserType, $intEmpId, 'Inactive');
		// 		$rsRecord	=	array("0"=>array("status" => "Inactive"));
		// 	}
		// 	else if($blnSuccess == "0")		
		// 	{
		// 		$rsRecord	=	array();
		// 	}
		// }
		// catch(Exception $e)
		// {
		// 	$strTbl_Name	=	"admin_master";
		// 	$strField_Names	=	"admin_id, username, firstname, lastname, office_id, timezone, is_meditab_admin,status";
		// 	$strWhere		=	"status='Active' and username='".$strUsername."' and password='".md5($strPassword)."'";
		// 	$rsRecord		=	$this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
		// }

		$strTbl_Name	=	"admin_master";
		$strField_Names	=	"admin_id, username, firstname, lastname, office_id, timezone, is_meditab_admin,status";
		$strWhere		=	"status='Active' and username='".$strUsername."' and password='".md5($strPassword)."'";
		$rsRecord		=	$this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
		return $rsRecord;
	}
	
	
	function checkAndCreateAdminUser($strUsername, $strPassword, $strLoginUserType, $arrResult)
	{
		$intLoginId		=	$arrResult[1];
		$strTblName		=	"admin_master";
		$strFieldNames	=	"admin_id, username, firstname, lastname, office_id, timezone, is_meditab_admin, status";
		$strWhere		=	"username = '".$strUsername."' AND meditab_emp_id = '".$intLoginId."' AND user_type = '".$strLoginUserType."'";
		$rsRecord		=	$this->objPage->getRecords($strTblName, $strFieldNames, $strWhere, "", "","", "");
		
		if(count($rsRecord) > 0)
		{
			
			if($rsRecord[0]['status'] == 'Inactive')
			{
				$this->changeAdminUserStatus($strUsername, $strLoginUserType, $intLoginId, 'Active');
				
				
				$rsRecord[0]['status']	=	'Active';
			}
			
			return $rsRecord;
		}
		else
		{
			$arrUserDetail	=	array(
										'username'			=>	$strUsername,
										'firstname'			=>	$arrResult[3],
										'lastname'			=>	$arrResult[4],
										'status'			=>	'Active',
										'meditab_emp_id'	=>	$intLoginId,
										'is_meditab_admin'	=>	'N',
										'user_type'			=>	$strLoginUserType
									);
		
			
			$objData = new MedData();
			
			$objData->setProperty("admin_master","admin_id",NULL,NULL);
			$objData->setFieldValue("password",md5($strPassword));
			
			foreach($arrUserDetail as $strFieldName => $strFieldValue)
			{
				$objData->setFieldValue($strFieldName,$strFieldValue);
			}
			
			$objData->insert();
			
			
			$intAdminId					=	$objData->getAutoId();
			$arrUserDetail['admin_id']	=	$intAdminId;
			
			return array($arrUserDetail);
		}
		
	}
	
	
	function changeAdminUserStatus($strUsername, $strLoginUserType, $intEmpId, $strStatus)
	{
		$objMedDb	=	MedDB::getDBObject();
		
		$strSQL		=	"UPDATE admin_master SET status = '".$strStatus."' WHERE username = '".$strUsername."' AND meditab_emp_id = '".$intEmpId."' AND user_type = '".$strLoginUserType."'";
		
		$objMedDb->executeQuery($strSQL);	
	}
	
	
	function getComboValue($strCase,$strKey)
	{
		$rsOptions	=	$this->objPage->getOptions($strCase);
		return $rsOptions[$strKey];
	}
	
	function getSelectedField($intTotId)
	{
		$strTbl_Name="tbl_fields";
		$strField_Names="`table_id` , `field_name` , `field_type` , `field_length` , `add_field_length_show` , `field_title` , `show_in` , `header_width` , `header_align` , `seq_no` , `body_align` , `issort` , `add_field_type` , `ishidden` , `html_link` , `list_field_html_type` , `addedit_field_html_type` , `list_html_text` , `add_html_text` , `isrequired` , `list_event` , `addedit_event` , `list_extra_property` , `add_extra_property` , `sql_field` , `field_desc` , `field_referal`";
		$strWhere="id in(".$intTotId.")";
		return $this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
	}
	
	function InsertSelectedField($resFields,$intInsertId)
	{
		for($intSelId=0;$intSelId<count($resFields);$intSelId++)
		{
				$resFields[$intSelId]['add_extra_property'] = str_replace(array('"','\''),'',$resFields[$intSelId]['add_extra_property']);
				
				$strSql= "INSERT INTO `tbl_fields` (`table_id` , `field_name` , `field_type` , `field_length` , `add_field_length_show` , `field_title` , `show_in` , `header_width` , `header_align` , `seq_no` , `body_align` , `issort` , `add_field_type` , `ishidden` , `html_link` , `list_field_html_type` , `addedit_field_html_type` , `list_html_text` , `add_html_text` , `isrequired` , `list_event` , `addedit_event` , `list_extra_property` , `add_extra_property` , `sql_field` , `field_desc` , `field_referal` ) VALUES
													   (".$intInsertId.",'".$resFields[$intSelId]['field_name']."','".$resFields[$intSelId]['field_type']."','".$resFields[$intSelId]['field_length']."','".$resFields[$intSelId]['add_field_length_show']."','".$resFields[$intSelId]['field_title']."','".$resFields[$intSelId]['show_in']."','".$resFields[$intSelId]['header_width']."','".$resFields[$intSelId]['header_align']."','".$resFields[$intSelId]['seq_no']."','".$resFields[$intSelId]['body_align']."','".$resFields[$intSelId]['issort']."','".$resFields[$intSelId]['add_field_type']."','".$resFields[$intSelId]['ishidden']."','".$resFields[$intSelId]['html_link']."','".$resFields[$intSelId]['list_field_html_type']."','".$resFields[$intSelId]['addedit_field_html_type']."','".$resFields[$intSelId]['list_html_text']."','".$resFields[$intSelId]['add_html_text']."','".$resFields[$intSelId]['isrequired']."','".$resFields[$intSelId]['list_event']."','".$resFields[$intSelId]['addedit_event']."','".$resFields[$intSelId]['list_extra_property']."',
													   \"".$resFields[$intSelId]['add_extra_property']."\",'".$resFields[$intSelId]['sql_field']."','".$resFields[$intSelId]['field_desc']."','".$resFields[$intSelId]['field_referal']."')";
		
				$objMedDb=MedDB::getDBObject();
				$objMedDb->executeQuery($strSql);											   
		}	
	}	
	
	function getSelectedMulti($intTotId)
	{
		$strTbl_Name="tbl_table_multi";
		$strField_Names="`table_id`, `page_title`, `issearch`, `isalpha`, `ispaging`, `isselector`, `table_desc`, `field_id`, `button_id`, `issort`";
		$strWhere="table_multi_id in(".$intTotId.")";
		return $this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
	}
	
	
	function InsertSelectedMulti($resFields,$intInsertId)
	{
		for($intSelId=0;$intSelId<count($resFields);$intSelId++)
		{
				$strSql= "INSERT INTO `tbl_table_multi` (`table_id`, `page_title`, `issearch`, `isalpha`, `ispaging`, `isselector`, `table_desc`, `field_id`, `button_id`, `issort` ) VALUES
												   (".$intInsertId.",'".$resFields[$intSelId]['page_title']."','".$resFields[$intSelId]['issearch']."','".$resFields[$intSelId]['isalpha']."','".$resFields[$intSelId]['ispaging']."','".$resFields[$intSelId]['isselector']."','".$resFields[$intSelId]['table_desc']."','".$resFields[$intSelId]['field_id']."','".$resFields[$intSelId]['button_id']."','".$resFields[$intSelId]['issort']."')";
											   
				$objMedDb=MedDB::getDBObject();
				$objMedDb->executeQuery($strSql);											   
		}	
	}
	function getSelectedButton($intTotId)
	{
		$strTbl_Name="tbl_buttons";
		$strField_Names="`table_id`, `page_type`, `key_col`, `field_name_u`, `confirm`, `action`, `seq_no`, `valign`, `halign`, `check_ref`, `cascade_action`";
		$strWhere="id in(".$intTotId.")";
		return $this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
	}
	function InsertSelectedButton($resFields,$intInsertId)
	{
		for($intSelId=0;$intSelId<count($resFields);$intSelId++)
		{
				$strSql= "INSERT INTO `tbl_buttons` (`table_id`, `page_type`, `key_col`, `field_name_u`, `confirm`, `action`, `seq_no`, `valign`, `halign`, `check_ref`, `cascade_action`) VALUES
												   (".$intInsertId.",'".$resFields[$intSelId]['page_type']."','".$resFields[$intSelId]['key_col']."','".$resFields[$intSelId]['field_name_u']."','".$resFields[$intSelId]['confirm']."','".$resFields[$intSelId]['action']."','".$resFields[$intSelId]['seq_no']."','".$resFields[$intSelId]['valign']."','".$resFields[$intSelId]['halign']."','".$resFields[$intSelId]['check_ref']."','".$resFields[$intSelId]['cascade_action']."')";
											   
				$objMedDb=MedDB::getDBObject();
				$objMedDb->executeQuery($strSql);											   
		}	
	}
	
	function getSelectedSearch($intTotId)
	{
		$strTbl_Name="tbl_search";
		$strField_Names="`table_id` , `field_name` , `field_referal` , `field_type` , `field_length` , `add_field_length_show` , `isrequired` , `add_html_text` , `add_extra_property` , `field_desc` , `seq_no` , `add_field_type` , `addedit_field_html_type`";
		$strWhere="id in(".$intTotId.")";
		return $this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
	}
	function InsertSelectedSearch($resFields,$intInsertId)
	{
		for($intSelId=0;$intSelId<count($resFields);$intSelId++)
		{
				if(ereg("\"",$resFields[$intSelId]['add_extra_property']))
				{
					if($resFields[$intSelId]['add_extra_property'] == "class=\"comn-input\"")
					{
						$resFields[$intSelId]['add_extra_property'] = "class=comn-input";
					}
				}
				if(ereg("\'",$resFields[$intSelId]['add_extra_property']))
				{
					$resFields[$intSelId]['add_extra_property'] = "class=comn-input";
				}
				
												   
				
				$strSql= "INSERT INTO `tbl_search` (`table_id` , `field_name` , `field_referal` , `field_type` , `field_length` , `add_field_length_show` , `isrequired` , `add_html_text` , `add_extra_property` , `field_desc` , `seq_no` , `add_field_type` , `addedit_field_html_type`) VALUES
												   (".$intInsertId.",'".$resFields[$intSelId]['field_name']."','".$resFields[$intSelId]['field_referal']."','".$resFields[$intSelId]['field_type']."','".$resFields[$intSelId]['field_length']."','".$resFields[$intSelId]['add_field_length_show']."','".$resFields[$intSelId]['isrequired']."','".mysql_escape_string($resFields[$intSelId]['add_html_text'])."','".mysql_escape_string($resFields[$intSelId]['add_extra_property'])."','".mysql_escape_string($resFields[$intSelId]['field_desc'])."','".$resFields[$intSelId]['seq_no']."','".$resFields[$intSelId]['add_field_type']."','".$resFields[$intSelId]['addedit_field_html_type']."')";
								   
							   
				$objMedDb=MedDB::getDBObject();
				$objMedDb->executeQuery($strSql);											   
		}	
	}
	

	 function getMemTableFields($intId,$strType=NULL)
	 {
	 	$strTbl_name="tbl_fields";
		$strField_Names	=" *";
		
		if($strType != NULL)
		{
			 $strWhere = "table_id = ".$intId."";
			 $strWhere .= " and show_in like '%".$strType."%'";
		} 
		else
		{
			$strWhere = "id = ".$intId."";
		}
		$rsFields=$this->objPage->getRecords($strTbl_name,$strField_Names,$strWhere,"","","",""); 
		return $rsFields;
	 }
	
	 

	 function getMemTblTable($intId=NULL)
	 {
	 	if($intId != NULL)
		{
			$strTbl_Name	="tbl_table"; 
			$strField_Names	=" *";
			$strWhere = "table_id = ".$intId."";
			$rsFields=$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","",""); 
		}
		else
		{		
		 	$strTblName = "tbl_table";
			$strFieldNames="MAX(table_id)";
			$rsFields = $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		}
		return $rsFields;
	 }
	
	
	 function getMemTblFields($intId=NULL)
	 {
		$rsAlpMaster=array();
		$rsTables = $this->objPage->executeSelect("show tables from `meditab`");
		for($intTable=0; $intTable<count($rsTables); $intTable++)
		{
			$strColQuery="show columns from ".$rsTables[$intTable]['tables_in_meditab'];
			$rsColumns=$this->objPage->executeSelect($strColQuery);
			
			if($intId != NULL)
			{
				$strTblName 	=	"tbl_table";
				$strFieldNames	=	"delete_key_col,edit_key_col";
				$strWhere		=	"table_id=".$intId;
				$rsKeyCol = $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
			}
			
			for($intCol=0; $intCol<count($rsColumns); $intCol++)
			{
				$arrTable=array('table_name'=>$rsTables[$intTable]['tables_in_meditab'],'column_name'=>$rsColumns[$intCol]['field']);
				if($intId != NULL)
				{
					$arr=array("delete_key_col"=>$rsKeyCol[0]['delete_key_col'],"edit_key_col"=>$rsKeyCol[0]['edit_key_col']);					
					$arrTable=array_merge($arrTable,$arr);
				}
				$rsAlpMaster[]=$arrTable;
			}
		}
		return $rsAlpMaster;
	}	
	
	
	
	 function getGeneralSettings($intModuleId,$intSubModuleId)
	 {
	 	$strTbl_name		=	"settings";
		$strField_names	=	"*";
		$strWhere			=	 " status=1";
		$strGroupBy		=	"";
		$strOrderBy			=	"field_order asc, field_group desc, seq_no";
		return $this->objPage->getRecords($strTbl_name, $strField_names, $strWhere, $strGroupBy, "",$strOrderBy, "");
	 }

	
	

	 function getSpTableFields($intId,$strType=NULL)
	 {
	 	$strTbl_name="tbl_fields";
		$strField_Names	=" *";
		
		if($strType != NULL)
		{
			 $strWhere = "table_id = ".$intId."";
			 $strWhere .= " and show_in like '%".$strType."%'";
		} 
		else
		{
			$strWhere = "id = ".$intId."";
		}
		$rsFields=$this->objPage->getRecords($strTbl_name,$strField_Names,$strWhere,"","","",""); 
		return $rsFields;
	 }
	 
	 
	
	function insertIntoTableFields($intNewTableId,$strOldTableName)
	{

		$strQuery	=	"show columns from ".$strOldTableName;
		$objMedDb	=	MedDB::getDBObject();
		$rsRecords	=	$objMedDb->executeSelect($strQuery);
		for($intField=0;$intField<count($rsRecords);$intField++)
		{
			$strSql = "insert into tbl_fields(table_id,field_name,field_referal,field_title) values('".$intNewTableId."','".$rsRecords[$intField]['field']."','".$rsRecords[$intField]['field']."','".$rsRecords[$intField]['field']."')";
			
			$objMedDb=MedDB::getDBObject();
			$objMedDb->executeQuery($strSql);	
		}
	}
	 
	 
	 function getHelpRecord($intHelpCode)
	 {
		$strTbl_Name	= "help";
		$strField_Names	= " help_title,help_desc";
		$strWhere		= " help_code='".$intHelpCode."'";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
		
	 }
	 
	 
	 function getHelpExamples($intHelpCode)
	 {
		$strTbl_Name	= "help_example";
		$strField_Names	= "  help_example";
		$strWhere		= " help_code='".$intHelpCode."'";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
		
	 }
	   
	 
	 function getLinkCount($strTableName,$strWhere)
	 {
		$strTblName=$strTableName;
		$strFieldNnames="count(0) as cnt";
		
		
		$rsCount=$this->objPage->getRecords($strTblName, $strFieldNnames, $strWhere, "", "", "", "");
		
		return $rsCount[0]["cnt"];
	 }
	 
	 
	 function getName($strTableName,$strFieldName,$strWhere)
	 {
		$strTblName		= $strTableName;
		$strFieldNames	= $strFieldName;
		$strWhere		= $strWhere;
		$rsName 		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		return $rsName[0]["$strFieldName"];
	 }
	 
	 
	 function deleteSelRecord($objData,$strWhereCond)
	 {
		return $objData->deleteRows($strWhereCond);	
	 }
	 
	 
	function deleteMultipleTables($objData,$strWhereCond,$strTableNames)
	{
		$rsTables = explode(",",$strTableNames);

		for($intTableId=0;$intTableId<count($rsTables);$intTableId++) 
		{
			$strTableName = $rsTables[$intTableId];
			$objData->setProperty($strTableName);
			$objData->deleteRows($strWhereCond);				
		}
		return true;
	}

	
	 function getFieldsRecord($intTableId,$strShowInType)
	 {
		$strTbl_Name	= "tbl_fields";
		$strField_Names	= " id,field_name,field_referal";
		$strWhere		= " table_id='".$intTableId."' and (show_in like '%".$strShowInType."%' or show_in = '') and ishidden = '0'";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
	 }
	 
	 
	 

	 function getMasterDet($intInoutId)
	 {
		$strTbl_Name	= "inout_master , emp_master";
		$strField_Names	= "  CONCAT_WS( ' ', emp_master.fname, emp_master.lname ) AS name , inout_master.checkin_time , inout_master.checkout_time";
		$strWhere		= " emp_master.emp_id = inout_master.emp_id and inout_master.inout_id='".$intInoutId."'";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");		 	
	 }
	 function getEmp($val,$strEntryDate)
	 {
		$strTbl_Name	= "inout_master";
		$strField_Names	= " emp_id ";
		$strWhere		= " emp_id = '".$val."' and  entry_date='".$strEntryDate."'";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
	}	
	
	
	function updateEntry($intHidInoutId)
	{
		$objMedDb=MedDB::getDBObject();
		$strSql = "update inout_master set user_id=CONCAT(user_id,',','".$this->objPage->objGeneral->getSession("intAdminId")."') , 
			modify_date = CONCAT(modify_date,',','".date('Y-m-d H:i:s')."')
			where inout_id =".$intHidInoutId."";

		$objMedDb->executeQuery($strSql);						

	}
	 function checkEntryAndUpdate($strEntryDate,$strChkInDate,$strChkOutDate,$objData)
	 {
	 	global $objPage;
		
	 	
		$objData->setProperty("inout_master","inout_id",null,null);
		
	 	$strHalfLeave			=	$this->objPage->getRequest('slt_is_half');
		$strLeave				=	$this->objPage->getRequest('slt_is_leave');
		$intInoutId				=	$this->objPage->getRequest('hid_InoutId');
		$intLateTime			=	$this->objPage->getRequest('Tatxt_late_time');
		$intWorkingHours		=	$this->objPage->getRequest('Tatxt_working_hours');
		$intNotWork				=	$this->objPage->getRequest('Tatxt_not_work');
		$intBreakHours			=	$this->objPage->getRequest('Tatxt_break_hours');
		$intOvertimeHours		=	$this->objPage->getRequest('Tatxt_overtime_hours');
		$intEwh					=	$this->objPage->getRequest('Tatxt_ewh');
		$intTotalHours			=	$this->objPage->getRequest('Tatxt_total_hours');
		$strUserId				=	$this->objPage->getRequest('hid_user_id');
		$strModifyDate			=	$this->objPage->getRequest('hid_modify_date');
		$intAdminId				=	$objPage->objGeneral->getSession("intAdminId");
		
		date_default_timezone_set($objPage->objGeneral->getSession('strTimeZone'));
		$strToday				=	date('Y-m-d H:i:s'); 

		$objData->setFieldValue("late_time",$this->covertToMinute($intLateTime));
		$objData->setFieldValue("working_hours",$this->covertToMinute($intWorkingHours));
		$objData->setFieldValue("not_work",$this->covertToMinute($intNotWork));
		$objData->setFieldValue("break_hours",$this->covertToMinute($intBreakHours));
		$objData->setFieldValue("overtime_hours",$this->covertToMinute($intOvertimeHours));
		$objData->setFieldValue("worked_hours",$this->covertToMinute($intEwh));
		$objData->setFieldValue("total_hours",$this->covertToMinute($intTotalHours));
	 	$objData->setFieldValue("entry_date",$strEntryDate);
		
		if(trim($strChkInDate) == "1970-01-01 :00") {}
		else {
			$objData->setFieldValue("checkin_time",$strChkInDate);
		}
		
		
		if($strChkOutDate == '' )
			$strChkOutDate = '0000-00-00 00:00:00';
			
		if(trim($strChkOutDate) == "1970-01-01 :00") {}
		else {
			$objData->setFieldValue("checkout_time",$strChkOutDate);
		}
		
		if(trim($strUserId) != "")
			$strUserId	=	$strUserId . ',' . $intAdminId;
		else
			$strUserId	=	$intAdminId;
		if(trim($strModifyDate) != "")
			$strModifyDate	=	$strModifyDate	.	','	.	$strToday;
		else
			$strModifyDate	=	$strToday;

		$objData->setFieldValue("user_id",$strUserId);
		$objData->setFieldValue("modify_date",$strModifyDate);
		$objData->setFieldValue("is_half",$strHalfLeave);
		$objData->setFieldValue("is_leave",$strLeave);
		$objData->performAction("E","",false);
	 }
	 
	 function covertToMinute($intMin)
	 {
	 	$intMin 	= explode(":",$intMin);
		$intHour 	= $intMin[0];
		$intMins  	= $intMin[1];
		if($intMins == "") $intMins = 0;
		$intHourMin = $intHour * 60;
		$intTotMins = $intHourMin + $intMins;
		return $intTotMins;
	 }
	 
	 function checkEntryAndInsert($rsEmpVal,$strEntryDate,$strChkInDate,$strChkOutDate,$objData)
	 {
		$strHalfLeave	=	$this->objPage->getRequest('slt_is_half');
		$strLeave		=	$this->objPage->getRequest('slt_is_leave');
		
	 	
		$objData->setProperty("inout_master");
		if($rsEmpVal != "")
		{
			$strTbl_Name	= "emp_master";
			$strField_Names	= " emp_id,type_id,shift_rotation ";
			$strWhere		= " emp_id in (".$rsEmpVal.")";
			$resEmp 		= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
						 
			for($intresEmp=0;$intresEmp<count($resEmp);$intresEmp++)
			{
				if($resEmp[$intresEmp]['shift_rotation'] == 'Yes')
				{
						$strTbl_Name	= "emp_shift_master";
						$strField_Names	= "type_id";
						$strWhere		= " emp_id='".$resEmp[$intresEmp]['emp_id']."' and date<='".$strEntryDate."' order by date desc limit 0,1";
						$resRotshift = $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
						if(count($resRotshift) > 0 )
							$intTypeId = $resRotshift[0]['type_id'];
						else
							$intTypeId = $resEmp[$intresEmp]['type_id'];			
						}
				else
				{
						$intTypeId = $resEmp[$intresEmp]['type_id'];
				}
					
				$strDay=date('l',mktime(0, 0, 0, date('m'), date('d')-1, date('Y')));
				$strTbl_Name	= "time_type_detail";
				$strField_Names	= "  time_id ";
				$strWhere		= " type_id = ".$intTypeId." and   day_name='".$strDay."'";
				$resTimeId 		= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");			
				
				
				
					$objData->setFieldValue("emp_id",$resEmp[$intresEmp]['emp_id']);
					$objData->setFieldValue("type_id",$intTypeId);
					$objData->setFieldValue("time_id",$resTimeId[0]['time_id']);
					$objData->setFieldValue("entry_date",$strEntryDate);
					$objData->setFieldValue("checkin_time",$strChkInDate);
					$objData->setFieldValue("checkout_time",$strChkOutDate);
					$objData->setFieldValue("is_half",$strHalfLeave);
					$objData->setFieldValue("is_leave",$strLeave);
					$objData->insert();
			}
		}
	 }
	 
	 function getButtonsRecord($intTableId)
	 {
		$strTbl_Name	= "tbl_buttons";
		$strField_Names	= " id,page_type";
		$strWhere		= " table_id='".$intTableId."'";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
		
	 }
	 
	
	 function getTableMultiRecord($intTableId)
	 {
		$strTbl_Name	= "tbl_table_multi";
		$strField_Names	= " *";
		$strWhere		= " table_multi_id='".$intTableId."'";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
		
	 }
	 
	 
	 function getEmpId($intDesgId,$strDeptId,$intEmpId)
	 {
	 	$strTbl_Name	= " emp_master, emp_dept, desg_master ";
		$strField_Names	= " distinct emp_master.emp_id,emp_master.fname ";
		$strWhere		= " emp_master.emp_id = emp_dept.emp_id AND find_in_set( emp_master.desg_id, desg_master.parent_desg_id )  AND desg_master.desg_id IN (".$intDesgId.") and emp_dept.dept_id IN (".$strDeptId.") AND emp_master.emp_id NOT IN (SELECT emp_master.emp_id FROM emp_report WHERE emp_report.emp_id = '".$intEmpId."' AND emp_report.empr_id = emp_master.emp_id ) AND emp_master.status='Active' ";

		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
	 }
	 
	 function getEmployeeList()
	 {
	  	
		$strTbl_Name	= "emp_master a,emp_dept b";
		$strField_Names	= "DISTINCT a.emp_id as emp_id";
		$strWhere		= "a.emp_id=b.emp_id";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
	 }

	
	function getDeptId()
	{
		$strTbl_Name	= "emp_dept";
		$strField_Names	= "dept_id";
		$strOrderBy		= "dept_id";
		$rs=$this->objPage->getRecords($strTbl_Name,$strField_Names,"","","",$strOrderBy,"");	
		return $rs[0]['dept_id'];

	}
	
	
	function getDeptEmp($intDeptId)
	{
		$strTblName="emp_master,emp_dept";
		$strFieldNames="GROUP_CONCAT(emp_master.emp_id) as emp_id";
		$strWhere="emp_master.emp_id=emp_dept.emp_id and emp_dept.dept_id in ('".$intDeptId."')";
		$rs=$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		$strId=$rs[0]['emp_id'];
		return $strId; 
	}
	function getDeptEmpList($intDeptId,$blnRequire=true)
	{
		$strTblName="emp_master,emp_dept";
		$strFieldNames=" distinct emp_master.emp_id as emp_id";
		$strWhere="emp_master.emp_id=emp_dept.emp_id and emp_dept.dept_id in ('".$intDeptId."')";
		$intOfficeId=$this->objPage->getRequest('slt_office_id');
		if((!empty($intOfficeId)) && ($intOfficeId!=0))
		{
			$strWhere.=" and office_id in ('".$intOfficeId."')";
		}
		
		
		
		if(!$blnRequire)
			$strOrderBy = "fname,lname asc";
		else
			$strOrderBy = "";	
		
		
		
		$rs=$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","",$strOrderBy,"");	
		$strId	= "";
		for($intEmpId=0;$intEmpId<count($rs);$intEmpId++)
		{
			$strId	.= 	$rs[$intEmpId]['emp_id'].",";
		}
		$strId	= substr($strId,0,-1);
		if($blnRequire)
			$strId=str_replace(",","','",$strId);
		return $strId; 
	}
	
	
	function getEmpWorkDetails($intEmpId,$intTimeId)
	{
		
		$strTableName="inout_master";
		$strFieldName="count(0) as cnt";
		$strWhere="emp_id = ".$intEmpId." AND entry_date >= '".MedPage::getRequest(startdate)."' AND entry_date <= '".MedPage::getRequest(enddate)."' AND working_hours < ((SELECT working_hours FROM time_type_detail WHERE time_id = inout_master.time_id )-".MedGeneral::getSettings('LESS_WORKING_TIME_LIMIT').")";
			
		$rsCount=MedPage::getRecords($strTableName, $strFieldName, $strWhere, "", "","", "");
		
		return $rsCount[0]['cnt'];
	}
	
	function getOfficeTiming($intEmpId)
	{
		$strTblName="emp_master";
		$strFieldNames="start_time,end_time";
		$strWhere="emp_id='".$intEmpId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
	}

	
	
	
	function insertTimeTypeDetails($intPkValue,$objData,$strAction)
	{
		
		$objData->setProperty("time_type_detail");
		
		
		$arrDays=array("Monday","Tuesday","Wednesday","Thursday","Friday","Saturday","Sunday");
		
		for($intDay=0;$intDay<7;$intDay++)
		{	
			
			$strCheckInTime = "";
			$strCheckInTime=MedPage::getRequest("Mltxt_".$intDay."_checkin_time");
			if(MedPage::getRequest("Mlslt_".$intDay."_checkin_time")=="PM" && !empty($strCheckInTime))
			{
				$arrCheckIn=explode(":",$strCheckInTime);
				$arrCheckIn[0]=$arrCheckIn[0]+12;
				if($arrCheckIn[0]==24) $arrCheckIn[0]="12";
				if(empty($arrCheckIn[1])) $arrCheckIn[1]="00";
				if(empty($arrCheckIn[2])) $arrCheckIn[2]="00";
				$strCheckInTime=$arrCheckIn[0].":".$arrCheckIn[1].":".$arrCheckIn[2];		
			}
			elseif(MedPage::getRequest("Mlslt_".$intDay."_checkin_time")=="AM" && !empty($strCheckInTime))
			{
				$arrCheckIn=explode(":",$strCheckInTime);
				if($arrCheckIn[0]=="12") $arrCheckIn[0]="00";			
				if(empty($arrCheckIn[1])) $arrCheckIn[1]="00";
				if(empty($arrCheckIn[2])) $arrCheckIn[2]="00";
				$strCheckInTime=$arrCheckIn[0].":".$arrCheckIn[1].":".$arrCheckIn[2];	
			}
			if(empty($strCheckInTime)) $strCheckInTime="00:00:59";
			
			$strCheckOutTime="";
			$strCheckOutTime=MedPage::getRequest("Mltxt_".$intDay."_checkout_time");			
			if(MedPage::getRequest("Mlslt_".$intDay."_checkout_time")=="PM" && !empty($strCheckOutTime))
			{
				$arrCheckOut=explode(":",$strCheckOutTime);
				$arrCheckOut[0]=$arrCheckOut[0]+12;
				if($arrCheckOut[0]==24) $arrCheckOut[0]="12";
				if(empty($arrCheckOut[1])) $arrCheckOut[1]="00";
				if(empty($arrCheckOut[2])) $arrCheckOut[2]="00";
				$strCheckOutTime=$arrCheckOut[0].":".$arrCheckOut[1].":".$arrCheckOut[2];		
			}
			elseif(MedPage::getRequest("Mlslt_".$intDay."_checkout_time")=="AM" && !empty($strCheckOutTime))
			{
				$arrCheckOut=explode(":",$strCheckOutTime);
				if($arrCheckOut[0]==12) $arrCheckOut[0]="00";
				if(empty($arrCheckOut[1])) $arrCheckOut[1]="00";
				if(empty($arrCheckOut[2])) $arrCheckOut[2]="00";
				$strCheckOutTime=$arrCheckOut[0].":".$arrCheckOut[1].":".$arrCheckOut[2];	
		
			}
			if(empty($strCheckOutTime)) $strCheckOutTime="00:00:59";
			
			$strWorkHour=MedPage::getRequest("Mltxt_".$intDay."_working_hours");
			$arrWorkHour=explode(":",$strWorkHour);
			$strWorkHour=$arrWorkHour[0]*60+$arrWorkHour[1];
			
			
			$strHoliday=MedPage::getRequest("Mlchk_".$intDay."_is_holiday");
			if(!empty($strHoliday)) $strHoliday="Yes";
			else $strHoliday="NO";
			
			
			$strBreak=MedPage::getRequest("Mlchk_".$intDay."_is_break");
			if(!empty($strBreak)) $strBreak="Yes";
			else $strBreak="NO";

			if(trim(strtoupper($strAction))=="A")
			{
				
				$objData->setFieldValue("type_id",$intPkValue);
				$objData->setFieldValue("day_name",$arrDays[$intDay]);
				$objData->setFieldValue("checkin_time",$strCheckInTime);
				$objData->setFieldValue("checkout_time",$strCheckOutTime);
				$objData->setFieldValue("working_hours",$strWorkHour);
				$objData->setFieldValue("is_holiday",$strHoliday);
				$objData->setFieldValue("is_break",$strBreak);
				$objData->insert();
			}
			else
			{
				$arrFieldValue=array("checkin_time"=>$strCheckInTime,"checkout_time"=>$strCheckOutTime,"working_hours"=>$strWorkHour,"is_holiday"=>$strHoliday,"is_break"=>$strBreak);
				$strWhere="type_id='".$intPkValue."' and day_name='".$arrDays[$intDay]."'";
				$objData->updateRows($arrFieldValue, $strWhere);
			}
		}
	}
	
	
	function getTimeTypeDetails($intTypeId)
	{
		$strTblName="time_type_detail";
		$strFieldNames="checkin_time,checkout_time,working_hours,is_holiday,is_break";
		$strWhere="type_id=".$intTypeId;
		$strOrderBy="time_id";
		$rsDetails=$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","",$strOrderBy,"");

		$arrDetails=array();
		for($intDetails=0;$intDetails<count($rsDetails);$intDetails++)
		{
			
			$intWorkHours=$rsDetails[$intDetails]['working_hours'];
			$intWorkHrs=$intWorkHours/60;
			$strWorkHours=explode(".",$intWorkHrs);
			$intWorkMins=$intWorkHours%60;
			
			if($intWorkMins>0) $strWorkHours[0].=":".$intWorkMins;
			$arrDetails[$intDetails]['working_hours']=$strWorkHours[0];
			
			
			if($rsDetails[$intDetails]['is_holiday']=="Yes") $arrDetails[$intDetails]['is_holiday']="checked";
			else $arrDetails[$intDetails]['is_holiday']="";
			
			
			if($rsDetails[$intDetails]['is_break']=="Yes") $arrDetails[$intDetails]['is_break']="checked";
			else $arrDetails[$intDetails]['is_break']="";
			
			
			if($rsDetails[$intDetails]['checkin_time']=="00:00:59") $arrDetails[$intDetails]['checkin_time']="";
			else
			{
				$arrCheckIn=explode(":",$rsDetails[$intDetails]['checkin_time']);
				if($arrCheckIn[0]==00)
				{
					$arrCheckIn[0]="12";
					$arrDetails[$intDetails]['checkin_am']="selected";
				}
				elseif($arrCheckIn[0]>12)
				{
					$arrCheckIn[0]=$arrCheckIn[0]-12;
					if($arrCheckIn[0]<10) $arrCheckIn[0]="0".$arrCheckIn[0];
					$arrDetails[$intDetails]['checkin_pm']="selected";
				}
				elseif($arrCheckIn[0]==12)
				{
					$arrCheckIn[0]=12;					
					$arrDetails[$intDetails]['checkin_pm']="selected";
				}
				$arrDetails[$intDetails]['checkin_time']=$arrCheckIn[0].":".$arrCheckIn[1];
			}
			
			
			if($rsDetails[$intDetails]['checkout_time']=="00:00:59") $arrDetails[$intDetails]['checkout_time']="";
			else
			{
				$arrCheckOut=explode(":",$rsDetails[$intDetails]['checkout_time']);
				if($arrCheckOut[0]==00)
				{
					$arrCheckOut[0]="12";
					$arrDetails[$intDetails]['checkout_am']="selected";
				}
				elseif($arrCheckOut[0]>12)
				{
					$arrCheckOut[0]=$arrCheckOut[0]-12;
					if($arrCheckOut[0]<10) $arrCheckOut[0]="0".$arrCheckOut[0];
					$arrDetails[$intDetails]['checkout_pm']="selected";
				}
				elseif($arrCheckOut[0]==12)
				{
					$arrCheckOut[0]=12;
					$arrDetails[$intDetails]['checkout_pm']="selected";
				}
				$arrDetails[$intDetails]['checkout_time']=$arrCheckOut[0].":".$arrCheckOut[1];
			}
			
		}
		return $arrDetails;		
	}
	
	
	function getWorkHourSummary($strWhere,$strGroupBy)
	{
		$strTblName		= "inout_master, emp_master,dept_master, emp_dept ";
		$strFieldNames	= "distinct entry_date,(select is_break from time_type_detail where inout_master.time_id=time_type_detail.time_id) as is_break,inout_master.inout_id as inout_id,date_format(entry_date,'%d %b %y') as enter_date, inout_master.emp_id as emp_id, CONCAT(emp_master.fname,' ',emp_master.lname,' - ',emp_master.emp_code) as emp_name, 	DATE_FORMAT(checkin_time,'%d %b %y %h:%i %p') as time_in, 	if(time(checkout_time) <> '00:00:00',DATE_FORMAT(checkout_time,'%d %b %y %h:%i %p'),'') as time_out,  concat( LPAD( late_time DIV 60 , 2 , 0 ) , ':' , LPAD( late_time MOD 60 , 2 , 0 ) ) as late_time, concat( LPAD( total_hours DIV 60 , 2 , 0 ) , ':' , LPAD( total_hours MOD 60 , 2 , 0 ) ) as total_hours, concat( LPAD( working_hours DIV 60 , 2 , 0 ) , ':' , LPAD( working_hours MOD 60 , 2 , 0 ) ) as working_hours,   concat( LPAD( overtime_hours DIV 60 , 2 , 0 ) , ':' , LPAD( overtime_hours MOD 60 , 2 , 0 ) ) as overtime_hours, concat( LPAD( not_work  DIV 60 , 2 , 0 ) , ':' , LPAD( not_work  MOD 60 , 2 , 0 ) ) as not_work ,concat( LPAD(worked_hours DIV 60 , 2 , 0 ) , ':' , LPAD(worked_hours MOD 60 , 2 , 0 ) ) as ewh ,if(is_half='Y','Yes','No') as is_half , if(is_leave='Y','Yes','No') as is_leave , IFNULL((SELECT concat( LPAD(SUM( TIME_TO_SEC( timediff( comeback_time, goout_time ) ) ) DIV 3600,2,0) , ':', LPAD((SUM( TIME_TO_SEC( timediff( comeback_time, goout_time ) ) ) MOD 3600 ) DIV 60,2,0) ) AS difference FROM inout_detail WHERE inout_detail.inout_id = inout_master.inout_id AND reason_id <> ( SELECT reason_id FROM reason_master WHERE upper( title ) = 'OFFICIAL' ) ),'00:00') AS break_hours  ";
		$strWhere		= "emp_master.emp_id = inout_master.emp_id and dept_master.dept_id = emp_dept.dept_id and emp_master.emp_id= emp_dept.emp_id ".$strWhere;
		
		if(trim(strtoupper($strGroupBy)) == "ENTRY_DATE")
			$strOrderBy="entry_date desc, emp_name asc";
		else if(trim(strtoupper($strGroupBy)) == "EMP_NAME")				
			$strOrderBy="emp_name, entry_date desc";
		
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","",$strOrderBy,"");	
	}
	
	
	function getWorkHourDetail($strWhere)
	{
	
		$strTblName		= "inout_detail LEFT JOIN reason_master ON inout_detail.reason_id = reason_master.reason_id ";
		$strFieldNames	= "inout_detail.inout_id as inout_id,date_format(goout_time,'%h:%i %p') as goout_time, if(time(comeback_time) <> '00:00:00',date_format(comeback_time,'%h:%i %p'),'') as comeback_time, (SELECT title FROM reason_master WHERE inout_detail.reason_id = reason_master.reason_id) as title , indicator ";
		
		$strGroupBy		= " date(goout_time)";
		$strOrderBy		= "goout_time";
		
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,$strGroupBy,"",$strOrderBy,"");	
	}

	
	
	function getAllDepartment()
	{
		$strTblName		= "dept_master";
		$strFieldNames	= "GROUP_CONCAT(dept_id) as dept_id";
		$rsResult		= $this->objPage->getRecords($strTblName,$strFieldNames,"","","","","");	
		$deptId		   	= $rsResult[0]['dept_id'];		
		$deptId			= str_replace(",","','",$deptId);
		
		return $deptId; 	
	}	
	
	function getAllOfficeId()
	{
		$strTblName		= "office_master";
		$strFieldNames	= "GROUP_CONCAT(office_id) as office_id";
		$rsResult		= $this->objPage->getRecords($strTblName,$strFieldNames,"","","","","");	
		$office_id		= $rsResult[0]['office_id'];		
		$office_id		= str_replace(",","','",$office_id);
		
		return $office_id; 	
	}
	
	function getAllProject()
	{
		$strTblName		= "project_master";
		$strFieldNames	= "GROUP_CONCAT(project_id) as project_id";
		$rsResult		= $this->objPage->getRecords($strTblName,$strFieldNames,"","","","","");	
		$projectId		= $rsResult[0]['project_id'];		
		$projectId		= str_replace(",","','",$projectId);
		
		return $projectId; 	
	}	
	
	
	function getAllTaskType()
	{
		$strTblName		= "worklog_master";
		$strFieldNames	= "GROUP_CONCAT(task_type) as task_type";
		$rsResult		= $this->objPage->getRecords($strTblName,$strFieldNames,"","","","","");	
		$taskType		= $rsResult[0]['task_type'];		
		$taskType		= str_replace(",","','",$taskType);
		
		return $taskType; 	
	}	
	
	
	function checkIsHoliday($dtDate,$intOfficeId=NULL)
	{
	
		$strTblName		= "holiday_master ";
		$strFieldNames	= "type_id";
		$strWhere		= "holiday_date='".$dtDate."'";		
		if(!empty($intOfficeId))
			$strWhere	.= 	" and office_id ='".$intOfficeId."'";	
		$rsResult		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		if(count($rsResult)>0)
			return $rsResult[0]["type_id"];
		else	
			return -1;
	}

	
	function checkEmployeeLeave($dtDate,$strTypeIds=NULL)
	{
		
		$intOffice_id	= $this->objPage->getRequest("Sr_slt_office_id1");
		if($intOffice_id ==0)
			$intOffice_id = $this->getAllOfficeId();
		$strTblName		= "emp_master";
		$strFieldNames	= "emp_id,shift_rotation,type_id";		
		$strWhere		= "status='Active' and  date_of_joining <='".$dtDate."' and office_id in ('".$intOffice_id."') and emp_id not in (select emp_id from inout_master where entry_date='".$dtDate."' )";	
		if(!empty($strTypeIds) && $strTypeIds!=-1)
			$strWhere	.= " and type_id not in (".$strTypeIds.")";
		$rsEmployee		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		if(count($rsEmployee)>0)
		{
			for($intEmployeeId=0;$intEmployeeId<count($rsEmployee);$intEmployeeId++)
			{				
				if($rsEmployee[$intEmployeeId]['shift_rotation']=='No' || $rsEmployee[$intEmployeeId]['shift_rotation']=='')				
					$intTypeId=$rsEmployee[$intEmployeeId]['type_id'];
				else
				{
					$strTblName		= "emp_shift_master";
					$strFieldNames	= "type_id";		
					$strWhere		= "emp_id=".$rsEmployee[$intEmployeeId]['emp_id']." and date<='".$dtDate."' 
									  order by date desc limit 0,1";									  
					$rsEmpShift		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");				

					if(count($rsEmp_Shift)>0) $intTypeId=$rsEmpShift[0]['type_id'];
					else $intTypeId=$rsEmployee[$intEmployeeId]['type_id'];	 					
					
				}
				
				$strTblName		= "time_type_detail";
				$strFieldNames	= "time_id,working_hours";		
				$strWhere		= "type_id=".$intTypeId." and day_name=dayname('".$dtDate."') and is_holiday='No'";		
				$rsTypeDetail	= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
				if(count($rsTypeDetail)>0)
				{
					$strSql="insert into inout_master(emp_id,checkin_time, checkout_time,type_id,entry_date,is_leave,time_id, working_hours,missing_daily_log) 
							 values('".$rsEmployee[$intEmployeeId]["emp_id"]."','".date('Y-m-d H:i:s')."','".date('Y-m-d H:i:s')."',".$rsEmployee[$intEmployeeId]["type_id"].",'".$dtDate."','Y',".$rsTypeDetail[0]['time_id'].",".$rsTypeDetail[0]['working_hours'].",'No')";	
					$objMedDb=MedDB::getDBObject();
					$objMedDb->executeQuery($strSql);
				}				
			}
		}
	}	
	
	function getLateComerEmpIds($intLateDays,$dtToDate,$dtFromDate,$strEmpId,$intLateMins)
	{
		if(!empty($strEmpId) && trim(strtoupper($strEmpId)) != "ALL") 
			$strWhere 	= " and emp_id in(".$strEmpId.")";
		if(!empty($intLateMins) || $intLateMins!=NULL)
			$intAllowTimeLate	= $intLateMins;
		else	
			$intAllowTimeLate	= $this->objPage->objGeneral->getSettings("LATE_COMERS_TIME_LIMIT");
			
		$strTblName			= "inout_master";
		$strFieldNames		= "emp_id,count(*) as cnt";		
		$strWhere			= "late_time>'".$intAllowTimeLate."' and entry_date<= '".$dtToDate."' and entry_date>='".$dtFromDate."'".$strWhere;		
		$strGroupBy			= "emp_id";
		$strHavingBy		= "cnt >=".$intLateDays;
		$rsInOutRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,$strGroupBy,$strHavingBy,"","");

		$strEmpId = "";
		for($intRecord=0;$intRecord<count($rsInOutRecord);$intRecord++)
		{
			$strEmpId .= "'".$rsInOutRecord[$intRecord]["emp_id"]."',";
		}
		$strEmpId = substr($strEmpId,0,-1);
		if(!empty($strEmpId))
			return $strEmpId;
		else
			return 0;
	}
	
	function getLessWorkEmpIds($intLessWorkDays,$dtToDate,$dtFromDate,$strEmpId)
	{
		if(!empty($strEmpId) && trim(strtoupper($strEmpId)) != "ALL") 
			$strWhere 	= " and emp_id in(".$strEmpId.")";
	
		$intLessWorkHr		= $this->objPage->objGeneral->getSettings("LESS_WORKING_TIME_LIMIT");
		$strTblName			= "inout_master";
		$strFieldNames		= "emp_id,count(*) as cnt";		
		$strWhere			= "not_work>='".$intLessWorkHr."' and entry_date<= '".$dtToDate."' and entry_date>='".$dtFromDate."'".$strWhere;		
		$strGroupBy			= "emp_id";
		$strHavingBy		= "cnt >=".$intLessWorkDays;
		$rsInOutRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,$strGroupBy,$strHavingBy,"","");
		
		$strEmpId = "";
		for($intRecord=0;$intRecord<count($rsInOutRecord);$intRecord++)
		{
			$strEmpId .= "'".$rsInOutRecord[$intRecord]["emp_id"]."',";
		}
		$strEmpId = substr($strEmpId,0,-1);
		if(!empty($strEmpId))
			return $strEmpId;
		else
			return 0;		
	}
	
	function validOffice($strTablename,$strOfficeFieldname,$strFieldName,$strFieldValue)	
	{
		if(MedGeneral::getSession("intOffice_id")==0)
			return true;
						
		$strTblName			= $strTablename;
		$strFieldNames		= "count(0)as tot";		
		$strWhere			= $strOfficeFieldname.
							  "=".MedGeneral::getSession("intOffice_id")." and ".$strFieldName."=".$strFieldValue;					  

		$rsvalidOffice		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
			
		if($rsvalidOffice[0]['tot']>0)
			return true;
		else
			return false;				
	}
	
	function validOfficeNews($intOfficeId,$intNewsId)
	{
		$strTblName			= "newsletter_master";
		$strFieldNames		= "count(0)as tot";		
		if($intOfficeId != 0)
		$strWhere			= "FIND_IN_SET($intOfficeId , office_id) <> 0 and news_id =".$intNewsId."";					  
		else
		$strWhere			= "news_id =".$intNewsId."";					  
		$rsvalidOffice		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		if($rsvalidOffice[0]['tot'] > 0)
				return 1;
		else
				return 0;	
	}
	
	function checkWhetherTypeId($intTypeId)
	{
		
		$strTblName			= "emp_master";
		$strFieldNames		= "office_id";		
		$strWhere			= "type_id='".$intTypeId."'";
		$rsEmpCount			= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		
		if($this->objPage->objGeneral->getSession('intOffice_id')==0)	
			$intNewOfficeId		= $this->objPage->getRequest('Taslt_office_id');
		else
			$intNewOfficeId		= $this->objPage->getRequest('hidin_office_id');
		
		if($rsEmpCount[0]['office_id']!=$intNewOfficeId)
		{
			$strTblName			= "emp_master";
			$strFieldNames		= "count(0) as cnt";		
			$strWhere			= "type_id='".$intTypeId."'";
			$rsEmpCount			= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
			
			$strTblName			= "emp_shift_master";
			$strFieldNames		= "count(0) as cnt";		
			$strWhere			= "type_id='".$intTypeId."'";
			$rsShiftCount		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
			
			if($rsEmpCount[0]['cnt']>0 || $rsShiftCount[0]['cnt']>0) return true;
			else return false;
		}
		else
			return false;		
		
	}
	
	
	function getShiftType($intOfficeId)
	{
	
		$strTblName		= "time_type_master ";
		$strFieldNames	= "type_id,title";
		$strWhere		= "office_id='".$intOfficeId."'";		
		$rsResult		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		
		return $rsResult;
	}
	
	
	function getEmpIdByType($dtDate)
	{
		$dtDate = explode("-",$dtDate);
		$strDay=date('l',mktime(0, 0, 0, $dtDate[1], $dtDate[2], $dtDate[0]));
		
		$strTblName		= "time_type_detail";
		$strFieldNames	= "group_concat(type_id) as type_id";		
		$strWhere		= "day_name='".$strDay."'and is_holiday='No'";		
		$rsEmpTypeId	= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
		$rsEmpTypeId	= (!empty($rsEmpTypeId[0]["type_id"]))?str_replace(",","','",$rsEmpTypeId[0]["type_id"]):0;
		return $rsEmpTypeId;
	}				
	
	
	function getComboDetail($intComboId)
	{			
		$strTblName		= "combo_detail";
		$strFieldNames	= "*";		
		$strWhere		= "combo_id='".$intComboId."'";	
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
	}		
	
	
	function insertComboDetails($intComboId,$objData,$intTotRows)
	{
		
		$objData->setProperty("combo_detail","combo_id",null,null);

		$strWhere	= " combo_id='".$intComboId."'";
		MedPage::setRequest("strWhere",$strWhere);
		$objData->performAction("D",$strWhere);
		
		for($intCaseId=0;$intCaseId<$intTotRows;$intCaseId++)
		{	
			
			$strKey 		= "";
			$strKey			= MedPage::getRequest("Mltxt_".$intCaseId."_key");
			$strKeyValue	= MedPage::getRequest("Mltxt_".$intCaseId."_key_value");
			$strSeqNo		= MedPage::getRequest("Mltxt_".$intCaseId."_seq_no");
			$strPosition	= MedPage::getRequest("Mltxt_".$intCaseId."_position");
			
			if(!empty($strSeqNo) || $strSeqNo!="")
			{					
				
				$objData->setFieldValue("combo_id",$intComboId);
				$objData->setFieldValue("`key`",$strKey);
				$objData->setFieldValue("key_value",$strKeyValue);
				$objData->setFieldValue("seq_no",$strSeqNo);
				$objData->setFieldValue("position",$strPosition);
				$objData->insert();
			}
		}
	}
	
	function getEmpFromOfficeDept($intOfficeId,$intDeptId)
	{
		$strWhere	= "emp_master.emp_id=emp_dept.emp_id";
		if($intOfficeId != 0)
			$strWhere   .= " and emp_master.office_id in ('".$intOfficeId."')";
		
		if(trim(strtoupper($intDeptId)) != "ALL")
			$strWhere 	.= " and  emp_dept.dept_id in ('".$intDeptId."')";
				
		$strTblName		= "emp_master,emp_dept";
		$strFieldNames	= "emp_master.emp_id as emp_id";		
		$rsEmpId		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
		
		$strEmpId = "";
		
		for($intEmpId=0;$intEmpId<count($rsEmpId);$intEmpId++)
			$strEmpId .= $rsEmpId[$intEmpId]["emp_id"].",";

		$strEmpId = substr($strEmpId,0,-1);
		
		return $strEmpId;
		
	}
	
	
	function getTLFromOfficeDept($intOfficeId,$intDeptId)
	{
		$strWhere	= "emp_master.emp_id = emp_report.empr_id";
		if($intOfficeId != 0)
			$strWhere   .= " and emp_master.office_id in ('".$intOfficeId."')";
		
		if(trim(strtoupper($intDeptId)) != "ALL")
			$strWhere 	.= " and  emp_dept.dept_id in ('".$intDeptId."')";
				
		$strTblName		= "emp_report, emp_master, emp_dept";
		$strFieldNames	= "DISTINCT emp_master.emp_id AS emp_id";		
			
		$rsEmpId		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
		
		$strEmpId = "";
		
		
		for($intEmpId=0;$intEmpId<count($rsEmpId);$intEmpId++)
			$strEmpId .= $rsEmpId[$intEmpId]["emp_id"].",";

		$strEmpId = substr($strEmpId,0,-1);
		
		return $strEmpId;
		
	}
	
	function getNewsInfo($intNewsId)
	{
		$strTblName		= "newsletter_master";
		$strFieldNames	= "office_id,department,designation,subject ";	
		$strWhere		= "news_id = ".$intNewsId;	
		$rsNewsInfo		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		return $rsNewsInfo;
	}
	
	function generateListFields($intTableMultiId)
	{
		$strTbl_Name	=	$this->objPage->pre."tbl_table_multi";
		$strField_Names	=	"table_multi_id, table_id, field_id ";
		$strWhere		=	"table_multi_id = ".$intTableMultiId;
		$rsMulti		=	$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
		if($rsMulti[0]['field_id'] != NULL)		$this->objPage->setListFields($rsMulti[0]['field_id']);
	}
	
		
	function getEmpFromOffice($intOfficeId)
	{
		$strTbl_Name	=	"emp_master";
		$strField_Names	=	"emp_id";
		
		if($intOfficeId == 0)
			$strWhere = "";
		else	
			$strWhere		=	"office_id=".$intOfficeId;
		
		return	$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
	}
	
		
	function getMessageFromOffice($intOfficeId)
	{
		$strTbl_Name	=	"message_master";
		$strField_Names	=	"message_id";
		
		if($intOfficeId == 0)
			$strWhere = "";
		else	
			$strWhere		=	"find_in_set('".$intOfficeId."',office_id)";
		
		return	$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
	}
	
		
	function getSuggestionFromOffice($intOfficeId)
	{
		$strTbl_Name	= "suggestion_master,emp_master";
		$strField_Names	= "suggestion_id";
		$strWhere		= "emp_master.emp_id = suggestion_master.emp_id";
		
		if($intOfficeId != 0)
			$strWhere		.=	" and office_id = ".$intOfficeId;
		
		return	$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
	}
	
		
	function getTaskFromOffice($intOfficeId)
	{
		$strTbl_Name	= "task_master";
		$strField_Names	= "task_id";
		$strWhere		= "";
		
		if($intOfficeId != 0)
			$strWhere		= "assign_by in (select emp_id from emp_master where office_id=".$intOfficeId.") or assign_to in (select emp_id from emp_master where office_id =".$intOfficeId.")";		
		
		return	$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
	}
	
	
	function getEmpAttendance($intEmpId,$strWhere)
	{
		$strTbl_Name	= "emp_master,inout_master,dept_master,emp_dept";
		$strField_Names	= "distinct DATE_FORMAT(entry_date,'%m/%d/%Y') as entry_date, concat(fname,' ',lname,'-',emp_code) as emp_name, emp_master.emp_id,emp_master.type_id,concat(is_leave,':',is_half) as attendance,date_of_joining";
		$strWhere		= "emp_master.emp_id = inout_master.emp_id AND dept_master.dept_id = emp_dept.dept_id AND emp_master.emp_id = emp_dept.emp_id  and emp_master.emp_id=".$intEmpId." ".$strWhere;
		$strOrderBy		= "emp_id asc,entry_date asc";
		
		return	$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","",$strOrderBy,"");
	}
	
	
	function getAdminLinkByRole($intRoleId)
	{
		$strTbl_Name	=	"role_link";
		$strField_Names	=	"adminlink_id";
		$strWhere		=	"role_id=".$intRoleId;
		$rsAdminLink	=	$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");		
		return $rsAdminLink;
	}
	
	
	function getAdminModuleName()
	{
		$strTbl_Name	=	"admin_module_master ";
		$strField_Names	=	"admin_module_id,admin_module_name";
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,"","","","","");
	}
	
	
	function getAdminLinkByModule($intAdminModuleId)
	{
		$strTbl_Name	=	"adminlink_master";
		$strField_Names	=	"display_text,adminlink_id";
		$strWhere		=	"admin_module_id=".$intAdminModuleId;
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");		
	}
	
	
	function getRoleName($intRoleId)
	{
		$strTbl_Name	=	"role_master ";
		$strField_Names	=	"role_name";
		$strWhere		=	"role_id=".$intRoleId;
		return $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");		
	}
	
	
	function getTimeZone($intOfficeId)
	{
		$strTbl_Name="office_master";
		$strField_Names="timezone";
		if($intOfficeId != 0)
			$strWhere="office_id=".$intOfficeId;
		else
			$strWhere="office_id=5";				
		$rsTimeZone	= $this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
		return	$rsTimeZone[0]["timezone"];
	}

		
	function getAttendanceInfo($dtStartDate,$intDt,$rsAttendanceSheet,$arrResDt="",$blnPrint=true)
	{
		$dtDateJoining		= explode("-",$rsAttendanceSheet[0]["date_of_joining"]);
		$strDateJoining		= date("m/d/Y",mktime(0,0,0,$dtDateJoining[1],$dtDateJoining[2],$dtDateJoining[0]));
		$dtDate				= date("m/d/Y",mktime(0,0,0,$dtStartDate[0],$dtStartDate[1]+$intDt,$dtStartDate[2]));
		if(date("l",mktime(0,0,0,$dtStartDate[0],$dtStartDate[1]+$intDt,$dtStartDate[2])) == "Sunday")
		
		$key 				= "";
		
		$key 				= array_search($dtDate,$arrResDt); 
		if($key!=""	)
		{
			if($rsAttendanceSheet[$key-1]["attendance"]	== "N:N")
				$strAttendance = "P"; 
			elseif($rsAttendanceSheet[$key-1]["attendance"]	== "Y:N")
				$strAttendance = "A"; 
			elseif($rsAttendanceSheet[$key-1]["attendance"]	== "N:Y")
				$strAttendance = "HL"; 
		}		
		elseif(strtotime($dtDate) < strtotime($strDateJoining))
		{
			if($blnPrint)
				$strAttendance 	= "&nbsp;"; 
			else
				$strAttendance 	= ""; 
		}
		elseif(date("l",mktime(0,0,0,$dtStartDate[0],$dtStartDate[1]+$intDt,$dtStartDate[2])) == "Sunday")
		{
			$strAttendance 	= "WO"; 
		}
		else
		{
			$strAttendance 	= "H"; 
		}
		return $strAttendance;
	}
	
	function getLeftLinks()
	{
		if($this->objPage->objGeneral->getSession("intAdminRole") > 0)
		{
			$strTblName		= 	"role_link";
			$strFieldName	= 	"GROUP_CONCAT(adminlink_id) as adminlink_id";
			$strWhere		= 	"role_id=".$this->objPage->objGeneral->getSession("intAdminRole");
			$rsAdminRole	=	$this->objPage->getRecords($strTblName, $strFieldName, $strWhere, "", "","", "");	
		}
		$strTblName		= 	"admin_module_master,adminlink_master";
		$strFieldName	= 	"admin_module_master.admin_module_id,
							 admin_module_master.admin_module_name,
							 admin_module_master.admin_icon,
							 adminlink_master.is_admin_role,
							 adminlink_master.is_target_new,
							 adminlink_master.display_text,
							 adminlink_master.admin_link,
							 adminlink_master.adminlink_id,
							 adminlink_master.developer_pass";
		$strWhere		= 	"admin_module_master.admin_module_id = adminlink_master.admin_module_id and adminlink_master.status='Active'";
		if(count($rsAdminRole) > 0)
			$strWhere  .=	" and adminlink_master.adminlink_id in (".$rsAdminRole[0]['adminlink_id'].")";
		$strOrderBy		=	"admin_module_master.seq_no,adminlink_master.seq_no";	 
		
		return	$this->objPage->getRecords($strTblName, $strFieldName, $strWhere, "", "",$strOrderBy, "");
	}

	function getRemarkByInOutId($intInOutId)
	{
		$strTblName		= 	"emp_master,inout_master";
		$strFieldName	= 	"concat(fname,' ',lname,'-',emp_code) as emp_name,remark,DATE_FORMAT(entry_date,'%d %b %Y') as entry_date";
		$strWhere		= 	"emp_master.emp_id=inout_master.emp_id and inout_id=".$intInOutId;
		return $this->objPage->getRecords($strTblName, $strFieldName, $strWhere, "", "","", "");
	}
	
		
	function getTaskInfo($intTaskId)
	{
		$strTblName		= "task_master";
		$strFieldNames	= " long_desc, assign_date,task_file";
		$strWhere		= "task_id =".$intTaskId;
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
	}	
	
		
	function getTaskHistory($intTaskId)
	{
		$strTblName		= "task_master,task_history";
		$strFieldNames	= " distinct assign_date,to_be_done_date,client_name,long_desc,time_spent,task_file,original_file_name,
							(CASE priority WHEN 1 THEN 'Super Urgent' WHEN 2 THEN 'Urgent' WHEN 3 THEN 'Medium' WHEN 4 THEN 'Low' WHEN 5 THEN 'Future Version' END) as priority,
							(select project_name from project_master where project_id=task_master.project_id) as project,
							(select title from module_master where module_id=task_master.module_id) as module";
		$strWhere		= "task_master.task_id = task_history.task_id and task_master.task_id =".$intTaskId;
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
	}
	
		
	function getStatusNote($intHistoryId)
	{
		$strTblName		= "task_history";
		$strFieldNames	= " status_note,(select GROUP_CONCAT( concat(fname,' ',lname) ) from emp_master where FIND_IN_SET( emp_master.emp_id, task_history.to_emp_id )) as assign_to,(select concat(fname,' ',lname) from emp_master where emp_master.emp_id = task_history.from_emp_id) as assign_by,allow_forward,(CASE task_history.status WHEN 1 THEN 'Pending' WHEN 2 THEN 'Assigned' WHEN 3 THEN 'Query' WHEN 4 THEN 'Query Replied' WHEN 5 THEN 'In Progress' WHEN 6 THEN 'Testing In Progress' WHEN 7 THEN 'Testing Done' WHEN 8 THEN 'Done' END) as status, is_first ";
		$strWhere		= "task_history_id =".$intHistoryId;
		
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
	}
	
		
	function getTotalDuration($intTaskId)
	{
		$strTblName		= "task_history, task_master";
		$strFieldNames	= "sum( time_spent ) as total";
		$strWhere		= "task_history.task_id = task_master.task_id";
		$strGroupBy		= "task_history.task_id having task_id = ".$intTaskId;	
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,$strGroupBy,"","","");
	}
	
		
	function getTaskTopFromToEmpId($strOfficeId,$intDeptId,$strFieldName)
	{
		$strTblName		= "emp_master,task_history,emp_dept";
		if($strFieldName == "to_emp_id")
			$strFieldNames	= "distinct concat('U',emp_master.emp_id) as emp_id";
		else
			$strFieldNames	= "distinct (emp_master.emp_id) as emp_id";
		$strWhere		= "emp_master.emp_id = task_history.$strFieldName and emp_master.office_id in ('".$strOfficeId."') and emp_dept.dept_id in ('".$intDeptId."') and emp_master.emp_id = emp_dept.emp_id";
		$strOrderBy		= "fname,lname";
		$strLimit		= " limit 0,1";	
	
		$rsRecords 		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","",$strOrderBy,$strLimit);
		
		return $rsRecords[0]["emp_id"];
	}
	
			
	function getDeptEmpListByTask($intDeptId,$blnTaskAssign)
	{
		$strTblName="emp_master,emp_dept,task_history";
		$strFieldNames="distinct(emp_master.emp_id) as emp_id";
		$strWhere="emp_master.emp_id=emp_dept.emp_id and emp_dept.dept_id in ('".$intDeptId."') ";
		$intOfficeId=$this->objPage->getRequest('slt_office_id');
		if((!empty($intOfficeId)) && ($intOfficeId!=0))
		{
			$strWhere.=" and office_id in ('".$intOfficeId."')";
		}
		
		if($blnTaskAssign ==1)
			$strWhere.=" and emp_master.emp_id=task_history.from_emp_id";
		elseif($blnTaskAssign ==0)
			$strWhere.=" and emp_master.emp_id=task_history.to_emp_id";
		
		$rs=$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		$strId	= "";
		for($intEmpId=0;$intEmpId<count($rs);$intEmpId++)
		{
			$strId	.= 	$rs[$intEmpId]['emp_id']."','";
		}
		$strId	= substr($strId,0,-3);
		return $strId; 
	}
	
	
	function getEmployeeFromGroup($intGroupId,$intTaskId)
	{
		if($intTaskId == "")
		{	
			$strTblName		= "emp_master,emp_dept,dept_master";
			$strWhere		= "find_in_set( emp_master.emp_id, (SELECT emp_id FROM emp_group WHERE group_id = ".$intGroupId." ) ) <>0 AND emp_master.emp_id = emp_dept.emp_id
							   AND emp_dept.dept_id = dept_master.dept_id";
		}	
		else
		{	
			$strTblName		= " emp_master, task_history,emp_dept,dept_master";
			$strWhere		= " emp_master.emp_id = to_emp_id AND task_id = ".$intTaskId." AND to_group_id != 0 AND emp_master.emp_id = emp_dept.emp_id
								AND emp_dept.dept_id = dept_master.dept_id";
		}	
		$strFieldNames		= "concat(fname,' ',lname) as emp_name ,group_concat(title) as title";
		$strGroupBy			= "emp_name asc";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,$strGroupBy,"","","");
	}
	
	
	function getEmployeesOfGroup($intGroupId)
	{
		$strTblName		= "emp_group";
		$strFieldNames	= "emp_id,emp_title";
		$strWhere		= "group_id='".$intGroupId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
	}
	
		
	
	function getPolicy($intPolicyId)
	{
		$strTblName		= "company_policy";
		$strFieldNames	= "*";
		$strWhere		= "policy_id='".$intPolicyId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
	}
	
	
	function getTaskAssignTo()
	{		
		$strTblName		= "((SELECT GROUP_CONCAT(distinct concat('U',emp_master.emp_id)) as emp_id from task_history,emp_dept,emp_master where (from_emp_id=emp_dept.emp_id or find_in_set(to_emp_id,emp_dept.emp_id)) and dept_id in ('".$this->objPage->getRequest("Sr_slt_sdept")."') and emp_master.emp_id=task_history.to_emp_id AND emp_master.emp_id = emp_dept.emp_id and office_id in ('".$this->objPage->getRequest("Sr_slt_soffice_id")."') and to_group_id=0)
union 
(select GROUP_CONCAT(distinct concat('G',group_id)) as emp_id from task_history,emp_group,emp_dept,emp_master where emp_group.group_id=task_history.to_group_id AND emp_master.emp_id = emp_dept.emp_id and emp_group.status='Active' and from_emp_id=emp_dept.emp_id  
and office_id in ('".$this->objPage->getRequest("Sr_slt_soffice_id")."') and emp_dept.dept_id in ('".$this->objPage->getRequest("Sr_slt_sdept")."') and to_group_id!=0)) as tbl";
		$strFieldNames	= "group_concat(emp_id) as to_emp_id";

		$rsAssignToRec	= $this->objPage->getRecords($strTblName,$strFieldNames,"","","","","");	
		 
		echo $strToEmpId	   	= $rsAssignToRec[0]['to_emp_id'];		exit;
		return $strToEmpId;
	}

	
	function getTaskAssignBy()
	{		
		
		$strTblName		= "((SELECT GROUP_CONCAT(distinct concat('U',emp_master.emp_id)) as emp_id from task_history,emp_dept,emp_master where (to_emp_id=emp_dept.emp_id or find_in_set(from_emp_id,emp_dept.emp_id)) and dept_id in ('".$this->objPage->getRequest("Sr_slt_sdept")."') and emp_master.emp_id=task_history.from_emp_id AND emp_master.emp_id = emp_dept.emp_id and office_id in ('".$this->objPage->getRequest("Sr_slt_soffice_id")."') and from_group_id=0)
union 
(select GROUP_CONCAT(distinct concat('G',group_id)) as emp_id from task_history,emp_group,emp_dept,emp_master where emp_group.group_id=task_history.from_group_id AND emp_master.emp_id = emp_dept.emp_id and emp_group.status='Active' and to_emp_id=emp_dept.emp_id  
and office_id in ('".$this->objPage->getRequest("Sr_slt_soffice_id")."') and emp_dept.dept_id in ('".$this->objPage->getRequest("Sr_slt_sdept")."') and from_group_id!=0)) as tbl";
		$strFieldNames	= "group_concat(emp_id) as from_emp_id";

		$rsAssignToRec	= $this->objPage->getRecords($strTblName,$strFieldNames,"","","","","");	
		 
	 	$strFromEmpId	   	= $rsAssignToRec[0]['from_emp_id'];		
		
		return $strFromEmpId;
		
		
	}
	
	function getCheckOffice($intEmpId)
	{
		$strToEmpId		= 0;
		$strTblName		= "emp_master";
		$strFieldNames	= "GROUP_CONCAT(emp_master.emp_id) as emp_id";
		$strWhere		= "office_id in ('".$this->objPage->getRequest("Sr_slt_soffice_id")."') and emp_master.emp_id in ('".$intEmpId."')";
		$rsRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");							
		if(!empty($rsRecord[0]["emp_id"]))
		{
			$strToEmpId	   	= $rsRecord[0]['emp_id'];		
			$strToEmpId		= str_replace(",","','",$strToEmpId);
		}
		return $strToEmpId;		
	}
	
	function allowDesignUpdate($intDesgId)
	{
		$strTblName		= "emp_report,emp_master";
		$strFieldNames	= "count(*) as tot";
		$strWhere		= "emp_master.emp_id = emp_report.empr_id and emp_master.desg_id='".$intDesgId."'";
		$rsRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");											
		return $rsRecord[0]["tot"];
			
	}
	function allowParentDesignation($intDesgId)
	{
		$strTblName		= "desg_master";
		$strFieldNames	= "parent_desg_id";
		$strWhere		= "desg_id='".$intDesgId."'";
		$rsRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");											
		return $rsRecord[0]["parent_desg_id"];
	
	}
	function getNameDesg($intDesgId)
	{
		$strTblName		= "desg_master";
		$strFieldNames	= "title";
		$strWhere		= "desg_id  IN (".$intDesgId.")";
		$rsRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		for($intRecord=0;$intRecord<count($rsRecord);$intRecord++)
		{
			$strTitle.=	$rsRecord[$intRecord]['title']."~|~";
		}
		$strTitle	=	substr($strTitle,0,strlen($strTitle)-3);
		return $strTitle;
	}
	function getNameLead($intEmpId)
	{
		$strTblName		= "emp_master";
		$strFieldNames	= "distinct(CONCAT_WS(' ',fname,lname)) as emp_name";
		$strWhere		= "emp_id='".$intEmpId."'";
		$rsRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		return $rsRecord[0]['emp_name'];
	}
	function checkEntryHoliDateExsist($strHoliDate,$intOfficeId,$strHoliId="")
	{	
		$strTblName		= "holiday_master";
		$strFieldNames	= "count(*) as cnt";
		if($strHoliId)
			$strWhere		= " office_id='".$intOfficeId."' and holiday_date='".$strHoliDate."' and  holiday_id <>".$strHoliId;
		else
			$strWhere		= " office_id='".$intOfficeId."' and holiday_date='".$strHoliDate."'";
			
		$rsRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		return $rsRecord[0]['cnt'];
	}	
	
	
	function getPageTableQuery($intTableId)
	{
		$strTblName		= "tbl_table";
		$strFieldNames	= "*";
		$strWhere		= "table_id='".$intTableId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
	}
	
	function getPageFieldQuery($intTableId)
	{
		$strTblName		= "tbl_fields";
		$strFieldNames	= "*";
		$strWhere		= "table_id='".$intTableId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");			
	}
	
	function getPageMultiQuery($intTableId)
	{
		$strTblName		= "tbl_table_multi";
		$strFieldNames	= "*";
		$strWhere		= "table_id='".$intTableId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
	}
	
	function getPageButtonQuery($intTableId)
	{
		$strTblName		= "tbl_buttons";
		$strFieldNames	= "*";
		$strWhere		= "table_id='".$intTableId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");		
	}
	
	function getPageSearchQuery($intTableId)
	{
		$strTblName		= "tbl_search";
		$strFieldNames	= "*";
		$strWhere		= "table_id='".$intTableId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");			
	}
	
	
	
	function get_time_difference( $start, $end )
	{
		$uts['start']      =    strtotime( $start );
		$uts['end']        =    strtotime( $end );
		if( $uts['start']!==-1 && $uts['end']!==-1 )
		{
			if( $uts['end'] >= $uts['start'] )
			{
				$diff    =    $uts['end'] - $uts['start'];
				if( $days=intval((floor($diff/86400))) )
					$diff = $diff % 86400;
				if( $hours=intval((floor($diff/3600))) )
					$diff = $diff % 3600;
				if( $minutes=intval((floor($diff/60))) )
					$diff = $diff % 60;
				$diff    =    intval( $diff );            
				
				return( array('days'=>$days, 'hours'=>$hours, 'minutes'=>$minutes, 'seconds'=>$diff) );
			}
		}
	}
	
	function getLateEmpName($intEmpId,$wmID)
	{
		$strTblName		=	"emp_master";
		$strFieldNames	=	"emp_id,fname as member_name";
		$strWhere		= 	"emp_id in(".$intEmpId.") order by member_name asc";
		$rsEmp 			= 	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		
		for($intEmp=0;$intEmp<count($rsEmp);$intEmp++)
		{
			$strTableName	=	"meeting_master LEFT JOIN weekly_report 
								ON meeting_master.wm_id = weekly_report.wm_id AND meeting_master.wm_id =".$wmID;
								
			$strFldNames	=	"meeting_master.meeting_date, weekly_report.modified_datetime";
			
			$strWhre		=	"weekly_report.user_id =".$rsEmp[$intEmp]['emp_id']." 
								AND meeting_master.meeting_date < weekly_report.modified_datetime 
								AND meeting_master.status=4";
			$rsLateEmp 			= 	$this->objPage->getRecords($strTableName,$strFldNames,$strWhre,"","","","");
			
			$strEmp.=	$rsEmp[$intEmp]['member_name'];
			
			if(count($rsLateEmp)>0)
			{
				$strEmp.= "<font class='red-text'>*</font>, ";
			}
			else
			{
				$strEmp.= ", ";
			}
		}
		$strEmp	=	substr($strEmp,0,strlen($strEmp)-2);
		return $strEmp;
	}
	
		
	function getLeaveDetail($intLeaveId)
	{
		$strTblName			= 	"leave_master";
		$strFieldNames		= 	"(select concat(fname,' ',lname) from emp_master where emp_master.emp_id = leave_master.emp_id) as emp_name, 
								 from_date,to_date,total_days,approved_total_days,reason,t_emp_id, h_emp_id,
								 (select concat(fname,' ',lname) from emp_master where emp_master.emp_id = leave_master.t_emp_id) as t_emp_name, 
								 (select concat(fname,' ',lname) from emp_master where emp_master.emp_id = leave_master.h_emp_id) as h_emp_name, 
								 is_t_approved,is_h_approved, date , CASE is_granted WHEN 1 then 'Pending' WHEN 2 then 'Approved By TL' WHEN 3 then 'Cancelled By TL' WHEN 4 then 'Approved By HR' WHEN 5 then 'Cancelled By HR' WHEN 6 then 'Approved' END as is_granted	";
		$strWhere			= 	"leave_id = '".$intLeaveId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
	}

	
	function getTeamEmpName($intEmpId)
	{
		
		$strTblName		=	"emp_master";
		$strFieldNames	=	"fname as member_name";
		$strWhere		= 	"emp_id in(".$intEmpId.") order by member_name asc";
		$rsEmp 			= 	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		
		for($intEmp=0;$intEmp<count($rsEmp);$intEmp++)
		{
			$strEmp.=	$rsEmp[$intEmp]['member_name'].", ";
		}
		$strEmp	=	substr($strEmp,0,strlen($strEmp)-2);
		return $strEmp;
	}
	
	
	
	function getMissingReportsBy($intWmId,$intWRId)
	{
		$strTblName		=	"emp_master,weekly_report";
		$strFieldNames	=	" group_concat(DISTINCT(emp_master.fname)) as fname";
		$strWhere		=	"wm_id=70 
							AND emp_master.emp_id in(".$intWRId.") 
							AND emp_master.emp_id not in(select user_id from weekly_report where wm_id=".$intWmId.")";
		$arrRecs		=	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		
		return $arrRecs[0]['fname'];
	}
	
	function checkDevPassword($intParentId,$strCurrentLink)
	{
		if($intParentId!='')
		{
			$strTblName			= 	"adminlink_master";
			$strFieldNames		= 	"developer_pass,admin_link";
			$strWhere			= 	"adminlink_id = '".$intParentId."'";
			$rsDevPassword		=	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
			
			return $rsDevPassword[0]['developer_pass'];
		}
	}
	
	
	function isUniqueModule($strTitle,$intModuleId,$intProjectId)
	{
		$strTblName			= 	"module_master";
		$strFieldNames		= 	"module_id";
		$strWhere			= 	"title in ('".$strTitle."') and project_id = ".$intProjectId;
		if($intModuleId != "")
			$strWhere .= " and module_id != ".$intModuleId;
		 
		$rsUniqueModule		=	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
			
		if(count($rsUniqueModule) > 0)
			return true;
		else
			return false;			
	}
	
	
	function isUniqueVersion($intProjectId,$strVersion,$intVersionId)
	{
		$strTblName		=	"version_master";
		$strFieldNames	=	"project_id";
		if($intVersionId!="" && $intVersionId >0)
		{
			
			$strWhere		=	" version_no = '" . $strVersion . "' and project_id = " . $intProjectId . " and version_id!=" . $intVersionId;
		}
		else
		{
			
			$strWhere		=	" version_no = '" . $strVersion . "' and project_id = " . $intProjectId;
		}
		
		$rsUniqueVersion =	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");

		if(count($rsUniqueVersion) > 0)
			return false;
		else
			return true;
	}
	
	
	function getFirstRightPage($intRoleId)
	{
		$strTblName		=	"adminlink_master , role_link";
		$strFieldNames	=	"role_link.role_id,adminlink_master.admin_link";
		$strWhere		=	"adminlink_master.adminlink_id = role_link.adminlink_id and role_link.role_id = '".$intRoleId."' limit 0,1";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		
	}
	
	
	function getFieldOrder($strFieldGroup)
	{
		$strTblName		=	"settings";
		$strFieldNames	=	"MIN(field_order) as field_order";
		$strWhere		=	"field_group = '" . $strFieldGroup . "'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
	}
	
	
	function checkIsFirst($intEmprId)
	{
		$strTblName		=	"emp_report";
		$strFieldNames	=	"count(0) as cnt";
		$strWhere		=	"emp_id = '".$intEmprId."' & is_first = 1";
		$rsEmpreport    =   $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");	
		return  $rsEmpreport[0]['cnt'];
	}
	
	
	
	function checkMaxRowLimit($intShowRows,&$objNewPage=NULL)
	{
		global $intShowMaxRows;		
		
		if(!empty($intShowRows))
		{
			$blnRowsVal	=	abs($intShowRows);
			if($blnRowsVal!=0)
			{
				if($intShowRows > $intShowMaxRows)
				{
					$strMessage 	= 	$this->objPage->objGeneral->getSiteMessage('SET_MAX_ROW_MSG');
					$strMessage 	= 	str_replace("{intShowMaxRows}",$intShowMaxRows,$strMessage);
					if(!empty($objNewPage))
						$objNewPage->objGeneral->setMessage($strMessage);	
					else
						$this->objPage->objGeneral->setMessage($strMessage);
				}
				else
					if(!empty($objNewPage))
						$objNewPage->intRecordsPerPage = $intShowRows;
					else
						$this->objPage->intRecordsPerPage = $intShowRows;	
			}
			else
			{
				$strMessage 	= 	$this->objPage->objGeneral->getSiteMessage('INT_VALUE_MSG');
				$this->objPage->objGeneral->setMessage($strMessage);			
			}			
		}
	}
	
	
	function getGroupEmp($intGroupId)
	{
		$strTblName		= 	"emp_group";
		$strFieldNames	= 	"emp_id";
		$strWhere		=	"group_id='" . $intGroupId ."'";
		$rsEmp			= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		return $rsEmp[0]['emp_id'];
	}
	
	function getCountField($intTableId)
	{
		$strTblName		= 	"tbl_fields";
		$strFieldNames	= 	"count(*) as cnt";
		$strWhere		=	"table_id='" . $intTableId ."'";
		$rsRecords		=	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		return count($rsRecords);
	}
	
	
	
	function getTypeIdFromEmpId($intEmpId)
	{
		
		$strTblName		= 	"emp_master";
		$strFieldNames	= 	"type_id,shift_rotation";
		$strWhere		=	"emp_id='" . $intEmpId ."' limit 0,1";
		$rsEmpTimeType	=	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");

		
		if($rsEmpTimeType[0]['shift_rotation'] == 'Yes')
		{
			$strTblName		= 	"emp_shift_master";
			$strFieldNames	= 	"type_id";
			$strWhere		=	"emp_id='" . $intEmpId ."' order by date ASC limit 0,1";
			$rsTimeType		=	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
			
			if(count($rsTimeType)>0)				
				$intTypeId	=	$rsTimeType[0]['type_id'];	 
			else									
				$intTypeId	=	$rsEmpTimeType[0]['type_id'];	 
		}
		else			
			$intTypeId	=	$rsEmpTimeType[0]['type_id'];	 
			
		 return $intTypeId;
	}
	
	
		
	function insertBreakEntry($intEntry,$intInOutId)
	 {
	 	global $objPage,$objData;
		
		
		$arrInOutDetailRecord	=	$this->getInOutRecord("*",$intInOutId);

		
		if(count($arrInOutDetailRecord) > 0 && $intInOutId != "")
		{
			
			$objData->setProperty("inout_detail","inoutdt_id",null,null);
	
			$strWhere	= " inout_id='".$intInOutId."'";
			
			MedPage::setRequest("strWhere",$strWhere);
			
			$objData->performAction("D",$strWhere);
		}
		
		
		
		$intTotalBreakEntry		=	$this->objPage->getRequest('hid_break_index_'.$intEntry);
		
		for($intCnt = 1; $intCnt <= $intTotalBreakEntry; $intCnt++)
		{
			
			$objData->setProperty("inout_detail","inoutdt_id",null,null);
		
			
			$arrInTimeDate		=	$this->objPage->getRequest('Dttxt_break_in_time_date'.$intCnt.'_'.$intEntry);
			$arrOutTimeDate		=	$this->objPage->getRequest('Dttxt_break_out_time_date'.$intCnt.'_'.$intEntry);
		 	$arrInTime			=	$this->objPage->getRequest('Tatxt_break_in_time_text'.$intCnt.'_'.$intEntry);
			$arrOutTime			=	$this->objPage->getRequest('Tatxt_break_out_time_text'.$intCnt.'_'.$intEntry);
			$arrInTimeAmPm		=	$this->objPage->getRequest('slt_break_ampm_in_time'.$intCnt.'_'.$intEntry);
			$arrOutTimeAmPm		=	$this->objPage->getRequest('slt_break_ampm_out_time'.$intCnt.'_'.$intEntry);
			$arrReacon			=	$this->objPage->getRequest('slt_break_reason'.$intCnt.'_'.$intEntry);
		
			
			$arrInOutDate	=	$this->getInOutDetail(
														$arrInTimeDate[0],$arrInTime[0],$arrInTimeAmPm[0],
														$arrOutTimeDate[0],$arrOutTime[0],$arrOutTimeAmPm[0]
													  );
														 
			
			
			$objData->setFieldValue("inout_id",	$intInOutId);
			$objData->setFieldValue("goout_time",	$arrInOutDate[0]);
			$objData->setFieldValue("comeback_time",$arrInOutDate[1]);
			$objData->setFieldValue("reason_id",	$arrReacon[0]);
	
			if($arrInTimeDate[0] != "" && $arrOutTimeDate[0] != "" && $arrInTime[0] != "" && $arrOutTime[0] != "")
			{
				$objData->performAction("A","",false);
			}
		}
	 }
	 
		
	function checkMultipleEntryInsert($strEntryDate,$strChkInDate,$strChkOutDate,$objData,$intCnt,$arrEnterDate)
	 {
	 	global $objPage;

		
		$objData->setProperty("inout_master","inout_id",null,null);
		
		
		$arrEmpId			=	$this->objPage->getRequest('slt_employee_'.$intCnt);
		$arrEntryDate		=	$this->objPage->getRequest('Dttxt_entry_date_'.$intCnt);
	 	$arrHalfLeave		=	$this->objPage->getRequest('slt_is_half_'.$intCnt);
		$arrLeave			=	$this->objPage->getRequest('slt_is_leave_'.$intCnt);
		$arrLateTime		=	$this->objPage->getRequest('Tatxt_late_time_'.$intCnt);
		$arrWorkingHours	=	$this->objPage->getRequest('Tatxt_wokring_hours_'.$intCnt);
		$arrNotWork			=	$this->objPage->getRequest('Tatxt_not_work_'.$intCnt);
		$arrBreakHours		=	$this->objPage->getRequest('Tatxt_break_hours_'.$intCnt);
		$arrOvertimeHours	=	$this->objPage->getRequest('Tatxt_overtime_hours_'.$intCnt);
		$arrEwh				=	$this->objPage->getRequest('Tatxt_ewh_'.$intCnt);
		$arrTotalHours		=	$this->objPage->getRequest('Tatxt_total_hours_'.$intCnt);
		$intAdminId			=	$objPage->objGeneral->getSession("intAdminId");				
		
		
		date_default_timezone_set($objPage->objGeneral->getSession('strTimeZone'));

		
		$objData->setFieldValue("emp_id",		 $arrEmpId[0]);		
		
		
		$objData->setFieldValue("late_time",	 $this->covertToMinute($arrLateTime[0]));
		$objData->setFieldValue("working_hours", $this->covertToMinute($arrWorkingHours[0]));
		$objData->setFieldValue("not_work",		 $this->covertToMinute($arrNotWork[0]));
		$objData->setFieldValue("break_hours",	 $this->covertToMinute($arrBreakHours[0]));
		$objData->setFieldValue("overtime_hours",$this->covertToMinute($arrOvertimeHours[0]));
		$objData->setFieldValue("worked_hours",	 $this->covertToMinute($arrEwh[0]));
		$objData->setFieldValue("total_hours",	 $this->covertToMinute($arrTotalHours[0]));
	 	$objData->setFieldValue("entry_date",	 $strEntryDate);
		
		if(trim($strChkInDate) == "1970-01-01 :00"){} else
			$objData->setFieldValue("checkin_time",$arrEnterDate[0]);

		
		if($strChkOutDate == '' && $arrEnterDate[1] == '')
			$arrEnterDate[1] = '0000-00-00 00:00:00';
				
		if(trim($strChkOutDate) == "1970-01-01 :00") {}else
			$objData->setFieldValue("checkout_time",$arrEnterDate[1]);
					
		$objData->setFieldValue("user_id",$intAdminId);
		$objData->setFieldValue("modify_date",date('Y-m-d H:i:s'));
		$objData->setFieldValue("is_half",$arrHalfLeave[0]);
		$objData->setFieldValue("is_leave",$arrLeave[0]);
		
		
		$arrField	=	$this->checkEntryForInsert($arrEmpId[0],$strEntryDate);

		$objData->setFieldValue("type_id",$arrField[0]);
		$objData->setFieldValue("time_id",$arrField[1]);	
		$objData->setFieldValue("shift_time",$arrField[2]);					
		
		$objData->performAction("A","",false);
	 }
	 
	 
	  
	
	function getInOutRecord($strField,$intInOutId)
	{
		global $objPage;
		
		$strTblName		= 	" inout_detail";
		$strFieldNames	= 	$strField;
		$strWhere		=	" inout_id = '" . $intInOutId ."'";
		$rsRecords		=	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		return $rsRecords;
	}
	
	
	 	
	
	 function checkEntryForInsert($rsEmpVal,$strEntryDate)
	 {
		
		if($rsEmpVal != "")
		{
			$strTbl_Name	= "emp_master";
			$strField_Names	= " emp_id,type_id,shift_rotation ";
			$strWhere		= " emp_id in (".$rsEmpVal.")";
			
			$resEmp 		= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
			
			for($intresEmp=0;$intresEmp<count($resEmp);$intresEmp++)
			{
				if($resEmp[$intresEmp]['shift_rotation'] == 'Yes')
				{
						$strTbl_Name	= "emp_shift_master";
						$strField_Names	= "type_id";
						$strWhere		= " emp_id='".$resEmp[$intresEmp]['emp_id']."' and date<='".$strEntryDate."' order by date desc limit 0,1";
						$resRotshift 	= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
						
						if(count($resRotshift) > 0 )
							$intTypeId = $resRotshift[0]['type_id'];
						else
							$intTypeId = $resEmp[$intresEmp]['type_id'];			
				}
				else
				{
						$intTypeId = $resEmp[$intresEmp]['type_id'];
				}
				
				$arrEntryDate	= split("-",$strEntryDate); 
				$strDay			= date('l',mktime(0, 0, 0, $arrEntryDate[1], $arrEntryDate[2], $arrEntryDate[0]));
				$strTbl_Name	= "time_type_detail";
				$strField_Names	= "  time_id,checkin_time,checkout_time ";
				$strWhere		= " type_id = ".$intTypeId." and   day_name='".$strDay."'";
				$resTimeId 		= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");			

				
				$arrField[0]	=	$intTypeId;
				$arrField[1]	=	$resTimeId[0]['time_id'];
				$arrField[2]	=	$resTimeId[0]['checkin_time']."^".$resTimeId[0]['checkout_time'];

				return	$arrField;
			}
		}
	 }
	 
	  
	 
	 function timeDiffBetweenTwoDate($strFirstDate,$strLastDate)
	 {
		
		$intFirstTime	=	strtotime($strFirstDate);
		$intLastDate	=	strtotime($strLastDate);
		
		
		$intTimeDiff	=	$intLastDate-$intFirstTime;
		
		return $intTimeDiff;
	 }
	 
	  
	 
	 function convertMiliSecondToHHMM($intTimeDiff)
	 {
	 	
		$intHours 		= 	floor($intTimeDiff/3600);
        $intDiff 		= 	$intTimeDiff % 3600;

        $intMinutes 	= 	floor($intDiff/60);
        $intDiff 		= 	$intDiff % 60;

        $itnSeconds 	= 	$intDiff;

        $intTimeDiff		=	 str_pad($intHours, 2, '0', STR_PAD_LEFT) . ':' . str_pad($intMinutes, 2, '0', STR_PAD_LEFT);
		
		return $intTimeDiff;
	 }
	 
	  

	 function calculateTotalBreakHour($strIndex)
	 {
		 global $objPage,$objModule;
		 
		 
		$intTotalBreakEntry		=	$objPage->getRequest('hid_break_index_'.$strIndex);

		for($intBCnt = 1 ; $intBCnt <= $intTotalBreakEntry; $intBCnt++)
		{
		
			$strPostFix								=	$intBCnt."_".$strIndex;
			
			$arrBreak['in_time_date'][$intBCnt]		=	$objPage->getRequest('Dttxt_break_in_time_date'.$strPostFix);
			$arrBreak['out_time_date'][$intBCnt]	=	$objPage->getRequest('Dttxt_break_out_time_date'.$strPostFix);
			$arrBreak['in_time'][$intBCnt]			=	$objPage->getRequest('Tatxt_break_in_time_text'.$strPostFix);
			$arrBreak['out_time'][$intBCnt]			=	$objPage->getRequest('Tatxt_break_out_time_text'.$strPostFix);
			$arrBreak['in_time_ampm'][$intBCnt]		=	$objPage->getRequest('slt_break_ampm_in_time'.$strPostFix);
			$arrBreak['out_time_ampm'][$intBCnt]	=	$objPage->getRequest('slt_break_ampm_out_time'.$strPostFix);
			
			
			$arrInOutDate	=	$this->getInOutDetail(
														$arrBreak['in_time_date'][$intBCnt][0],
														$arrBreak['in_time'][$intBCnt][0],
														$arrBreak['in_time_ampm'][$intBCnt][0],
														$arrBreak['out_time_date'][$intBCnt][0],
														$arrBreak['out_time'][$intBCnt][0],
														$arrBreak['out_time_ampm'][$intBCnt][0]
													 );
													 
			$intTime			=	$intTime + $this->timeDiffBetweenTwoDate($arrInOutDate[0],$arrInOutDate[1]);
		}
		
		$arrTime[]	=	$this->convertMiliSecondToHHMM($intTime);

		return $arrTime;
	 }
	 
	 
	

	 
	function calInOutValue($strIndex)
	{		
		global $objPage,$objModule;
	
		
		$arrEmp			=	$objPage->getRequest('slt_employee_'.$strIndex);	
		$arrEntryDate	=	$objPage->getRequest('Dttxt_entry_date_'.$strIndex);					
		$arrInDate		=	$objPage->getRequest('Dttxt_in_time_'.$strIndex);				
		$arrTimeIn		=	$objPage->getRequest('Tatxt_time_in_text_'.$strIndex);
		$arrAmPmIn		=	$objPage->getRequest('slt_ampm_time_in_'.$strIndex);	
		$arrOutDate		=	$objPage->getRequest('Dttxt_out_time_'.$strIndex);				
		$arrTimeOut		=	$objPage->getRequest('Tatxt_time_out_text_'.$strIndex);
		$arrAmPmOut		=	$objPage->getRequest('slt_ampm_time_out_'.$strIndex);
		
		
		$arrInOutDate	=	$this->getInOutDetail($arrInDate[0],$arrTimeIn[0],$arrAmPmIn[0],$arrOutDate[0],$arrTimeOut[0],$arrAmPmOut[0]);
		
		
		$arrInTime		=	@explode(" " ,$arrInOutDate[0]);
		$arrOutTime		=	@explode(" " ,$arrInOutDate[1]);
		
		$arrEntryDate	= 	@split("-",$arrEntryDate[0]); 
		$strEntryDate	= 	@date("m-d-Y",@mktime(0,0,0,$arrEntryDate[0],$arrEntryDate[1],$arrEntryDate[2])); 		
		$strDay			= 	@date('l',mktime(0, 0, 0, $arrEntryDate[0], $arrEntryDate[1], $arrEntryDate[2]));	
		
		$ARRDATE	=	array(
								"ENTRYDATE"			=>	$strEntryDate,
								"ENTRYDAY"			=>	$strDay,
								"CHECKINTIME"		=>	$arrInTime[1],
								"CHECKOUTTIME"		=>	$arrOutTime[1], 	
								"CHECKOUTDATETIME"	=>	$arrInOutDate[1],
								"CHECKINDATETIME"	=>	$arrInOutDate[0]
							  );
		
		$arrInOutValue	=	$this->getInOutValue($arrEmp[0],$strIndex,$ARRDATE);
		
		return $arrInOutValue;
	}
	
	 
	function shiftCheck($intEmpId,$ARRDATE)
	{
		
		$strTbl_Name	= "emp_master";
		$strField_Names	= "type_id,shift_rotation";
		$strWhere		= " emp_id='".$intEmpId."'";
		$resShift 		= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
		
		
		if($resShift[0]['shift_rotation'] == 'Yes')
		{
			$strTbl_Name	= "emp_shift_master";
			$strField_Names	= "type_id";
			$strWhere		= " emp_id='".$intEmpId."' and date<='".$ARRDATE['ENTRYDATE']."' order by date desc limit 0,1";
			$resRotshift 	= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
			
			if(count($resRotshift) > 0 )
				$intTypeId = $resRotshift[0]['type_id'];
			else
				$intTypeId = $resShift[0]['type_id'];	
		}
		else
				$intTypeId = $resShift[0]['type_id'];
			
		

		$strTbl_Name	 =	"time_type_detail";
		$strField_Names	 = 	" time_id ,working_hours ,is_holiday,checkin_time,checkout_time";
		$strWhere		 = 	"  type_id='".$intTypeId."' and day_name = '".$ARRDATE['ENTRYDAY']."'";

		$resPrevCheckDet = 	$this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");			
		
		$strShiftTime 	 = 	$resPrevCheckDet[0]['checkin_time']."^".$resPrevCheckDet[0]['checkout_time'];
		$intTimeId		 =	$resPrevCheckDet[0]['time_id'];
		
		$arrTime = $this->getTimeDetail($intEmpId,$resPrevCheckDet[0]['time_id']);

		
		if(strtotime($ARRDATE['CHECKINTIME']) > strtotime($arrTime[0]['checkin_time'])) 
		{
				$arrTotal_Hours = $this->get_time_difference($arrTime[0]['checkin_time'] ,$ARRDATE['CHECKINTIME']);
				$int_late_time 	= $this->getTimeCal($arrTotal_Hours);
		}		
		else
			$int_late_time = "0";
			
		return array($intTimeId, $intTypeId ,$ARRDATE['CHECKINTIME'],$int_late_time,$strShiftTime);	
	}
	
	
	
	function getTimeDetail($intEmpId,$intTimeId)
	{
		$strTbl_Name	=	"time_type_detail";
		$strField_Names	=	"checkin_time, working_hours,type_id,is_break";
		$strWhere		=	" time_id = ".$intTimeId."";	
		return $this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
	}
	
	
	function getTimeCal($arrTotal_Hours)
	{
		if($arrTotal_Hours['days']== "") 	$arrTotal_Hours['days'] = 0;
		if($arrTotal_Hours['hours']== "") 	$arrTotal_Hours['hours'] = 0;
			
		return	($arrTotal_Hours['days']*24*60)+($arrTotal_Hours['hours']*60)+$arrTotal_Hours['minutes'];
	}
	
	
	function getInOutCalculation($intEmpId,$resInout,$strIndex,$ARRDATE)
	{
		
		$arrTotal_Hours		=	$this->get_time_difference($ARRDATE['CHECKINDATETIME'],$ARRDATE['CHECKOUTDATETIME']);
				
		
		if($arrTotal_Hours['days']== "") 
		{
			$arrTotal_Hours['days'] 	= 	0;
		}
				
		if($arrTotal_Hours['hours']== "")
		{
			 $arrTotal_Hours['hours'] 	= 	0;
		}
			
		$intTotal_Hours 	= 	($arrTotal_Hours['days']*24*60)+($arrTotal_Hours['hours']*60)+$arrTotal_Hours['minutes'];

		
		$strTbl_Name		=	"time_type_detail";
		$strField_Names		=	"working_hours,is_break";
		$strWhere			=	" time_id =".$resInout['time_id'];
		$rsWorkHrs			= 	$this->objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");

		
		$intWork_Hours 		= 	$rsWorkHrs[0]['working_hours'];
		
		$strBH				=	$this->objPage->getRequest('Tatxt_break_hours_'.$strIndex);
		
		if(trim($strBH[0]) != "00:00" && $strBH[0] != "")
		{	
			$arrBH				=	@explode(':',$strBH[0]);
			$intBH				=	($arrBH[0]*60) + $arrBH[1];
			$intPBreak_Hours	= 	$intBH;
		
		}
		else
			$intPBreak_Hours	=	30;	
			
		$intWorked_Hours= 	$intTotal_Hours - $intPBreak_Hours;
		
		if($intWorked_Hours > $intWork_Hours)
			$intOTW_Hours = $intWorked_Hours-$intWork_Hours;
		else
			$intNTW_Hours = $intWork_Hours-$intWorked_Hours;
		
		$arrInOutCalc['checkout_time']	=	$strCdate;
		$arrInOutCalc['late_time']		=	$resInout['late_time'];
		$arrInOutCalc['total_hours']	=	$intTotal_Hours;
		$arrInOutCalc['not_work']		=	$intNTW_Hours;
		$arrInOutCalc['break_hours']	=	$intPBreak_Hours;
		$arrInOutCalc['working_hours']	=	$intWork_Hours;
		$arrInOutCalc['overtime_hours']	=	$intOTW_Hours;
		$arrInOutCalc['worked_hours']	=	$intWorked_Hours;
		
		return $arrInOutCalc;
	}		
	
	
	function getInOutValue($intEmpId,$strIndex,$ARRDATE)
	{
		global $objPage,$objModule;
		
		
		$strInOutDate 	= 	$objPage->getRequest("Dttxt_entry_date_".$strIndex);
		$strDate 		= 	$objPage->getRequest("Dttxt_in_time_".$strIndex);
		$strTimeget 	= 	$objPage->getRequest("Tatxt_time_in_text_".$strIndex);
		$strAmPm 		= 	$objPage->getRequest("slt_ampm_time_in_".$strIndex);
		
		
		if($strDate == "")	
			$strDate 	= 	$strInOutDate;

		
		$arrTimeDet 	= 	$objModule->shiftCheck($intEmpId,$ARRDATE);

		
		$arrInsertInOut['emp_id']		= $intEmpId;
		$arrInsertInOut['time_id']		= $arrTimeDet[0];
		$arrInsertInOut['type_id']		= $arrTimeDet[1];
		$arrInsertInOut['checkin_time']	= $arrTimeDet[2];
		$arrInsertInOut['late_time']	= $arrTimeDet[3];
		$arrInsertInOut['shift_time']	= $arrTimeDet[4];
		$arrInsertInOut['entry_date']	= $ARRDATE['ENTRYDATE'];
		$arrInsertInOut['user_id']		= $this->objPage->objGeneral->getSession("intAdminId");

		
		$arrInOutCalc	=	$objModule->getInOutCalculation($intEmpId,$arrInsertInOut,$strIndex,$ARRDATE);
		
		return $arrInOutCalc;
	}
	
	
function getInOutDetail($strChkInDate1,$strChkInDate2,$strChkInDate3,$strChkOutDate1,$strChkOutDate2,$strChkOutDate3)
 {
		if($strChkInDate1 != "" && $strChkInDate2 != "" && $strChkInDate3 != "")
		{
			$strChkInDate1		= split("-",$strChkInDate1); 
			$strChkInDate1		= @date("Y-m-d",@mktime(0,0,0,$strChkInDate1[0],$strChkInDate1[1],$strChkInDate1[2])); 
			
			if($strChkInDate3 == "PM")
			{
				$strChkInDate2		= split(":",$strChkInDate2); 
				$intCheckInTime 	= $strChkInDate2[0]+12;
				if($intCheckInTime==24) $intCheckInTime="12";
				$intCheckInTime = $intCheckInTime.":".$strChkInDate2[1];
			}	
			else
			{
				$strChkInDate2		= split(":",$strChkInDate2); 
				$strHour			= $strChkInDate2[0]; 
				if($strHour == 12)	$strHour = "00"; 
				$strMin			= $strChkInDate2[1]; 
				$intCheckInTime = $strHour.":".$strMin;
			}	
	
				$strChkInDate = $strChkInDate1." ".$intCheckInTime.":00";

		}
		
		if($strChkOutDate1 != "" && $strChkOutDate2 != "" && $strChkOutDate3 != "")
		{		
			$strChkOutDate1	= split("-",$strChkOutDate1); 
			$strChkOutDate1	= @date("Y-m-d",@mktime(0,0,0,$strChkOutDate1[0],$strChkOutDate1[1],$strChkOutDate1[2])); 
	
			if($strChkOutDate3 == "PM")
			{
				$strChkOutDate2	 = split(":",$strChkOutDate2); 
				$intCheckOutTime = $strChkOutDate2[0]+12;
				if($intCheckOutTime==24) $intCheckOutTime="12";
				$intCheckOutTime = $intCheckOutTime.":".$strChkOutDate2[1];
			}	
			else
			{
				  $strChkOutDate2	= split(":",$strChkOutDate2); 
				  $strHour			= $strChkOutDate2[0]; 
				  if($strHour == 12)	$strHour = "00"; 
				  $strMin			= $strChkOutDate2[1]; 
				  $intCheckOutTime 	= $strHour.":".$strMin;
			}
			$strChkOutDate = $strChkOutDate1." ".$intCheckOutTime.":00";
		}	
 		return array($strChkInDate,$strChkOutDate);
 }
 
	
	
	function getShiftTime($intEmpId,$strEntryDate)
	{
		global $objPage,$objModule;
		
		
		$strTbl_Name	= "emp_master";
		$strField_Names	= " emp_id,type_id,shift_rotation ";
		$strWhere		= " emp_id =".$intEmpId."";
		$resEmp 		= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");

		if($resEmp[0]['type_id'] != "")
		{
			
			if($resEmp[0]['shift_rotation'] == 'Yes')
			{
				$strTbl_Name	= "emp_shift_master";
				$strField_Names	= "type_id";
				$strWhere		= " emp_id='".$resEmp[0]['emp_id']."' and date<='".$strEntryDate."' order by date desc limit 0,1";
				$resRotshift 	= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");	
				
				
				if(count($resRotshift) > 0 )
					$intTypeId = $resRotshift[0]['type_id'];
				else
					$intTypeId = $resEmp[0]['type_id'];			
			}else
				$intTypeId = $resEmp[0]['type_id'];	
			
			
			$arrEntryDate	= 	split("-",$strEntryDate); 
			$strDay			= 	date('l',@mktime(0, 0, 0, $arrEntryDate[0], $arrEntryDate[1], $arrEntryDate[2]));	
		
			
			$strTbl_Name	= "time_type_detail";
			$strField_Names	= " checkin_time ,checkout_time";
			$strWhere		= " type_id =".$intTypeId." AND day_name = '".$strDay."'";
	
			$resShift 		= $this->objPage->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
			
			
			$intShiftInTime	=	$this->convertMinutToTimeAmPm($resShift[0]['checkin_time']);
			$intShiftOutTime=	$this->convertMinutToTimeAmPm($resShift[0]['checkout_time']);
		}
		
		if(count($resShift)>0)
			return $intShiftInTime." - ".$intShiftOutTime;
		else
			return NULL;
	}	
	
	function convertMinutToTimeAmPm($intTime)
	{	
		if($intTime != '')
		{
			$arrTime		= 	@explode(":",$intTime);
			$intAmPmtime	= 	@date("g:i A", mktime($arrTime[0],$arrTime[1]));
			return $intAmPmtime;
		}
		else
			return "";
	}
	
	
	function convertDateToDisplayFormat($strDate)
	{	
		if($strDate != '')
		{
			$arrDate		=	explode('-',$strDate);
			$strDate		=	date("m-d-Y", mktime(0, 0, 0, $arrDate[1], $arrDate[2], $arrDate[0]));
				
			
			return $strDate;
		}
		else
			return "";
	}
	
	function getInOutEntryDetail($intInOutId)
	{
		$strTblName		= "inout_master";
		$strFieldNames	= " *";
		$strWhere		= " inout_id = '".$intInOutId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
	}
	
	function convertMinuteToHHMM($intMinute)
	{
		$intHH		=	$intMinute/60;
		
		$arrHours	=	explode(".",$intHH);
		$intHH		=	$arrHours[0];
		$intMM		=	$intMinute%60;
		
		if(strlen($intHH) == 1)		$intHH = "0".$intHH;
		if(strlen($intMM) == 1)		$intMM = "0".$intMM;
		
		return $intHH.":".$intMM;
	}
	
	function getClientUserPass($intClientId)
	{
		$strTblName		= "client_master";
		$strFieldNames	= " client_username";
		$strWhere		= " client_id = '".$intClientId."'";
		return $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
	}	
	
	function getClient($strClientId)
	{
		$strClient		=	"";
		$strTblName		= 	"client_master";
		$strFieldNames	= 	" client_name";
		$strWhere		= 	" client_id in (".$strClientId.")";
		$strOrderBy		=	"client_name";
		$rsClient		= 	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","",$strOrderBy,"");
		$intTotalClient	=	count($rsClient);
		
		for($intClient=0;$intClient<$intTotalClient;$intClient++)
		{
			$strClient	.=	$rsClient[$intClient]['client_name']."<br>";
		}
	
		return substr($strClient,0,-1);
	}	
	
	function getMissingDailyLogDate($intEmpId,$strDateCondition,$strDateCondition1)
	{
		global $objPage;
		
		$strDateFormat  = $objPage->objGeneral->getSettings("DATE_FORMAT");
		$strTblName		= 	"inout_master ";
		$strField		= 	" entry_date";
		$strWhere		= 	" inout_master.emp_id = '".$intEmpId."'".$strDateCondition1." AND is_leave = 'N' AND inout_master.missing_daily_log = 'Yes'";
		$strOrderBy		=	"entry_date DESC";
		
		
		$rsDate			= 	$this->objPage->getRecords($strTblName,$strField,$strWhere,"","",$strOrderBy,"");

		$intTotalClient	=	count($rsDate);
		
		for($intDate = 0;$intDate < $intTotalClient; $intDate++)
		{
			$strDate 		=	"";
			$strDate		=	@date($strDateFormat,@strtotime($rsDate[$intDate]['entry_date']));
			$arrDateString[] =	$strDate;
		}
		
		return $arrDateString;
	}
	
	
	
	function getShiftName($strField,$strWhere)
	{
		$strShiftName	=	"";
		
		$strTblName		= 	" time_type_master ";

		$rsResult		= 	$this->objPage->getRecords($strTblName,$strField,$strWhere,"","","","");	
		
		for($intCnt = 0; $intCnt < count($rsResult); $intCnt++)
		{
			$strShiftName	.=	$rsResult[$intCnt]['title'].",";
		}
		
		$strShiftName	=	substr($strShiftName,0,strlen($strShiftName)-1);
		
		return $strShiftName;
	}

	
	function getPendingTask()
	{
		$strTblName		= 	" task_master LEFT JOIN task_history ON task_master.task_id = task_history.task_id ";
		$strField		= 	" task_master.task_id, task_history.to_emp_id, task_history.to_group_id, task_master.estimated_time_taken,task_master.estimated_time_taken as time ";
		$strWhere		= 	" task_history.status NOT IN (8)  ";
		
		return 	$this->objPage->getRecords($strTblName,$strField,$strWhere,"","","","");
	}
	
	
	function getOfficeDetail($intEmployeeId)
	{
		$strTblName		= 	" emp_master LEFT JOIN office_master ON emp_master.office_id = office_master.office_id ";
		$strField		= 	" SUM( (SELECT IF( office_master.office_id =3, 1, 0 ) )) AS india_office ,
							  SUM( (SELECT IF( office_master.office_id =4, 1, 0 ) )) AS us_office ,
							  SUM( (SELECT IF( office_master.office_id =5, 1, 0 ) )) AS phi_office ,	
							  SUM( (SELECT IF( office_master.office_id =6, 1, 0 ) )) AS arg_office ,	
		 					  office_master.office_id,office_master.name";
		$strWhere		= 	" emp_master.emp_id IN (".$intEmployeeId.")  ";
		$strGroupBy		=	"office_master.office_id";
		
		return $this->objPage->getRecords($strTblName,$strField,$strWhere,$strGroupBy,"","","");
	}
	function getEmployeeFromAssignment($intAssignmentId)
	{
		$strTableName	= 	"assignments  ";
		$strField		= 	"employee ";
		$strWhere		= 	" assignments.assign_id = $intAssignmentId  ";
		
		$rsData			=	$this->objPage->getRecords($strTableName,$strField,$strWhere,"","","","");
		return 	$rsData[0]['employee'];
	}
	function getAssignmentStatus($intAssignId,$strEmployee="",$intParentId = "" )
	{
		$strTableName	= 	"emp_assignments LEFT JOIN emp_master on emp_master.emp_id = emp_assignments.emp_id ";
		$strField		= 	"emp_assignments.status,CONCAT_WS(' ',emp_master.fname,emp_master.lname) as emp_name";
		$strWhere		= 	" emp_assignments.assign_id = $intAssignId  ";
				
		if($intParentId != "")
			$strEWhere	=	" AND parent_id =  $intParentId";
		
		return 	$this->objPage->getRecords($strTableName,$strField,$strWhere.$strEWhere,"","","","");		
	}
	function getAssignmentDetail($intAssignId)
	{
		$strTableName	= 	"assignments ";
		$strField		= 	"*";
		$strWhere		= 	" assignments.assign_id = $intAssignId ";
	
		
		return 	$this->objPage->getRecords($strTableName,$strField,$strWhere,"","","","");		
	}
	function makeReAssign($strEmployeeAssignId,$strRemark,$intReassignTimes= 1 )
	{
		$objMedDb		=	MedDB::getDBObject();
		
		$intAdminUserId	=	$this->objPage->objGeneral->getSession("intAdminId");
		
		if($intReassignTimes == "" ||  $intReassignTimes == 0)
			$intReassignTimes	= 1;
			
		for($intReassignCnt = 0 ; $intReassignCnt < $intReassignTimes; $intReassignCnt++)
		{
			$strReAssignSql	=	"insert into emp_assignments(assign_id,emp_id,status,parent_id,assign_n_times,
								remark,created_datetime,created_by,modified_datetime,modified_by)";
			$strReAssignSql	.=	"(SELECT assign_id,emp_id,'Pending' AS `status`,emp_assign_id AS parent_id,$intReassignTimes as assign_n_times,
								'".addslashes($strRemark)."' as remark,'".date("Y-m-d H:i:s")."' as created_datetime,$intAdminUserId as created_by,
								'".date("Y-m-d H:i:s")."' as modified_datetime,$intAdminUserId as modified_by FROM  emp_assignments WHERE  emp_assignments.emp_assign_id IN ($strEmployeeAssignId))";
								
			$objMedDb->executeQuery($strReAssignSql);
		}
	}
	function getFilteredPendingAssignmentId($intAssignId)
	{
		$intAdminUserId	=	$this->objPage->objGeneral->getSession("intAdminId");
		
		$strTableName	= 	"emp_assignments ";
		$strField		= 	"emp_assign_id";
		$strWhere		= 	" emp_assignments.assign_id = $intAssignId AND status='Pending' ";
		
		
		$rsData			=	$this->objPage->getRecords($strTableName,$strField,$strWhere,"","","","");	
		
		$strPendingAssignment = "";
		foreach($rsData as $key => $row)
		{
			$strPendingAssignment .= 	$row['emp_assign_id'] .",";
		}
		
		return substr($strPendingAssignment,0,-1);
	}
	
	
	function getEmployeeNamesWithTeamLeader($strGrpId)
	{
		
		$strTblName		= 	"emp_group";
		$strFieldNames	= 	"emp_id,group_leader";
		$strWhere		=	"group_id ='" . $strGrpId . "'";
		$rsEmpId		= 	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");

		
		if(count($rsEmpId) > 0)
		{
			$strTblName		= 	"emp_master";
			$strFieldNames	= 	"emp_id,CONCAT(fname,' ',lname) as empname";
			$strWhere		=	"emp_id IN (" . $rsEmpId[0]["emp_id"] . ") AND emp_master.status = 'Active'";
			$rsEmpName		= 	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");

			
			for($intEmpName = 0; $intEmpName < count($rsEmpName); $intEmpName++)
			{
				if($rsEmpName[$intEmpName]['emp_id'] ==  $rsEmpId[0]["group_leader"])
					$arrEmpName[0]	= 	"<b>".$rsEmpName[$intEmpName]['empname']."</b>";
			}
			
			for($intEmpName = 0; $intEmpName < count($rsEmpName); $intEmpName++)
			{
				if($rsEmpName[$intEmpName]['emp_id'] !=  $rsEmpId[0]["group_leader"])
					$arrEmpName[]	= 	$rsEmpName[$intEmpName]['empname'];
			}
		}
		return $arrEmpName;
	}
	
	
	
	function getEmployeeNames($strEmpId)
	{
		$strTblName		= 	"emp_master";
		$strFieldNames	= 	"CONCAT(fname,' ',lname) as empname";
		$strWhere		=	"emp_id IN (" . $strEmpId . ")";
		$rsEmpName	= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		foreach($rsEmpName as $strEmpName)
		{
			$arrEmpName[]	= 	$strEmpName['empname'];
		}
		return $arrEmpName;
	}
	function getAssignmentDetailRecordCount($intAssignId,$intEmpId)
	{
		if($intAssignId != "" && $intAssignId != 0 && $intEmpId != "" && $intEmpId != 0  ) 
		{
			$strTblName		= 	"emp_assignments";
			$strFieldNames	= 	"count(0) as cnt ";
			$strWhere		=	"assign_id = $intAssignId AND emp_id= $intEmpId";
			$rsData		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
			return $rsData[0]['cnt'];
		}
		return false;
	}
	function deleteFromEmpAssignments($intAssignId,$strNotEmpId)
	{
		if($strNotEmpId != "" )
		{
			$strWhere	= " assign_id ='".$intAssignId."' AND emp_id NOT IN($strNotEmpId) AND status='Pending'";
			$strSql	=	"DELETE FROM emp_assignments WHERE ".$strWhere;	

			$objMedDb=MedDB::getDBObject();
			$objMedDb->executeQuery($strSql);
			
			
			$strTblName		= 	"emp_assignments";
			$strFieldNames	= 	"distinct(emp_id)";
			$strWhere	= " assign_id ='".$intAssignId."' AND emp_id NOT IN($strNotEmpId) AND status!='Pending'";
			
			$rsData		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
			$strNewEmpId  ="";
			foreach($rsData as $key=>$row)
			{
				$strNewEmpId	.= 	$row['emp_id'].",";
			}
			 $strNewEmpId	=	 substr($strNewEmpId,0,strlen($strNewEmpId)-1);
			
			if($strNewEmpId != "")
			{
				$strWhere	= " assign_id ='".$intAssignId."' AND emp_id  IN($strNotEmpId) AND status='Pending'";
				$strSql	=	"UPDATE assignments set employee = CONCAT(employee,',','".$strNewEmpId."') WHERE assign_id =$intAssignId "  ;		
				$objMedDb=MedDB::getDBObject();
				$objMedDb->executeQuery($strSql);				
			}
		}		
	}
	 
	function getEmployeeDetail($intEmpId,$strFields="")
	{
		$strTable	=	"emp_master";
		$strFieldNames	=	($strFields == "") ? "*" : $strFields;
		$strWhere	=	"emp_id in ('".implode("','",explode(",",$intEmpId))."')";
		return $this->objPage->getRecords($strTable,$strFieldNames,$strWhere,"","","","");
	}
	function getVarNewsLetterTemplateDetail($intTemplateId,$strFields="")
	{
		$strFieldNames	=	($strFields == "") ? "*" : $strFields;
		$strWhere		=	"var_newsletter_template_id = ".$intTemplateId."";
		return $this->objPage->getRecords("var_newsletter_template",$strField,$strWhere,"","","","");
	}
	function getUserEmailFromVarNewsLetterId($arrPk)
	{
		$strReturn	=	"";
		if(is_array($arrPk) && count($arrPk) > 0)
		{
			$strFieldNames	=	($strFields == "") ? "*" : $strFields;
			$strTablerName	=	" var_newsletter";
			
			$strWhere		=	"var_newsletter.newsletter_id IN (".implode(",",$arrPk).")";
		
			$rsData			=	 $this->objPage->getRecords($strTablerName,"email,name",$strWhere,"","","","");
			foreach($rsData as $key=> $arrRow)
			{
				$strReturn .=	$arrRow['email'].";";
			}
			return substr($strReturn,0,-1);
		}
		else
			return $strReturn;
	}
	
	function getEmployeeRoles($intEmpId)
	{
		$strTable		=	"emp_roles";
		$strFieldNames	=	"role_id";
		$strWhere		=	"emp_id = '".$intEmpId."'";
		$rsRoles		=	$this->objPage->getRecords($strTable,$strFieldNames,$strWhere,"","","","");
		
		for($intRoles=0;$intRoles<count($rsRoles);$intRoles++)
			$strRoles	.=	$rsRoles[$intRoles]['role_id'].",";
		
		return substr($strRoles,0,-1);
	}

	
	function getInoutMaster($strEntryDate,$intEmpId)
	{
		$strTable	=	"inout_master";
		$strFields	=	"date_format(checkin_time,'%h:%i %p') as checkin_time, 
						date_format(checkout_time,'%h:%i %p') as checkout_time,
						concat(if(break_hours DIV 60 <>0,LPAD(break_hours DIV 60,2,'0'),'00'),':',if(break_hours MOD 60 <>0,LPAD(break_hours MOD 60,2,'0'),'00')) as break_hours, 
						concat(if(worked_hours DIV 60 <>0,LPAD(worked_hours DIV 60,2,'0'),'00'),':',if(worked_hours MOD 60 <>0,LPAD(worked_hours MOD 60,2,'0'),'00')) as worked_hours";
		$strWhere	=	"emp_id = '".$intEmpId."' and entry_date = '".$strEntryDate."'";
		return $this->objPage->getRecords($strTable,$strFields,$strWhere,"","","","");
	}
	
	
	function getTotalDailyLogHours($intEmpId,$strDate)
	{
		$strTblName			=	"worklog_master";
		$strFieldNames		=	"concat(if(sum(duration) DIV 60 <>0,LPAD(sum(duration) DIV 60,2,'0'),'0'),':',if(sum(duration) MOD 60 <>0,LPAD(sum(duration) MOD 60,2,'0'),'00')) as tot_duration";
		$strWhere			=	"emp_id = '".$intEmpId."' and date = '".$strDate."'";
		$rsRecord			=	$this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","",$strOrderBy,""); 
		return $rsRecord[0]['tot_duration'];
	}
	
	
	function checkUniqueFieldValue($strTableName,$strPageType="A",$strCheckFieldName,$strCheckFieldValue,$strExtraWhere="")
	{
		$strFieldName		=	" count(0) as cnt";
		$strTableWhere		=	$strCheckFieldName." = '".$strCheckFieldValue."'";
		if($strExtraWhere != "")
			$strTableWhere	.=	" AND ".$strExtraWhere;		
		
		$rsRecords		=	$this->objPage->getRecords($strTableName, $strFieldName,$strTableWhere,"", "","", "");
		
		$intCount		=	$rsRecords	[0]['cnt'];
		if($intCount == 0 )
			return false;
		else
			return true;
	}	
	
	function getServiceLevelFromDB($intTranId)
	{
		$strTblName		= "prescriber_requests";
		$strFieldNames	= "service_level";
		$strWhere		= " mt_tran_id = '".$intTranId."'";
		$rsRecord		= $this->objPage->getRecords($strTblName,$strFieldNames,$strWhere,"","","","");
		return	$rsRecord[0]['service_level'];		
	}
	
	
	function updatePrescriberSPI($intMtTranId,$SPI)
	{
		$objData	=	new MedData();
		$objData->setProperty('prescriber_requests','mt_tran_id',NULL,NULL);
		$objData->setFieldValue('mt_tran_id',$intMtTranId);
		$objData->setFieldValue('spi',$SPI);
		$objData->update();
	}
	
	
	function formatDateDB($strInputDate)
	{
		$arrInputDate		= explode('-',$strInputDate);
		$strOutputDate		= $arrInputDate[2] . '-' . $arrInputDate[0] . '-' . $arrInputDate[1];
		return $strOutputDate;
	}
	function updatePharmacyUploadedFlag($intMtTranId, $FlagValue)
	{
		$objData	=	new MedData();
		$objData->setProperty('pharmacy_requests','mt_tran_id',NULL,NULL);
		$objData->setFieldValue('mt_tran_id',$intMtTranId);
		$objData->setFieldValue('is_uploaded',$FlagValue);
		if($FlagValue == 'Y')
		{
			$objData->setFieldValue('service_action','UPDATE_PHARMACY');
		}
		
		$objData->update();
	}
	
	function getPharmacyUploadedFlag($intMtTranId)
	{
		$strTable	= "pharmacy_requests";
		$strFields	= "is_uploaded";
		$strWhere	= " mt_tran_id = '".$intMtTranId."'";
		$rsRecord	= $this->objPage->getRecords($strTable,$strFields,$strWhere,"","","","");
		return	$rsRecord[0]['is_uploaded'];
	}
	
	
	function getDetailsFromPharmacyMaster($intMtTranId,$strFieldNames)
	{
		$strTblName		=	"pharmacy_master";
		$strWhere		= " mt_tran_id = '".$intMtTranId."'";
		$rsRecord		=	$this->objPage->getRecords($strTblName, $strFieldNames, $strWhere,"", "",$strOrderBy, "");
		return $rsRecord;
	}
	
	
	function getDetailsFromPharmacyRequests($intMtTranId,$strFieldNames)
	{
		$strTblName		=	"pharmacy_requests";
		$strWhere		= " mt_tran_id = '".$intMtTranId."'";
		$rsRecord		=	$this->objPage->getRecords($strTblName, $strFieldNames, $strWhere,"", "",$strOrderBy, "");
		return $rsRecord;
	}
	
	
	function checkPharmacyRequestExist($intncpdpid)
	{
		$strTblName		=	"pharmacy_requests";
		$strFieldNames	=	"mt_tran_id";
		$strWhere		= " ncpdpid = '".$intncpdpid."'";
		$strOrderBy		=	"mt_tran_id desc";
		$rsRecord		=	$this->objPage->getRecords($strTblName, $strFieldNames, $strWhere,"", "",$strOrderBy, "");
		return $rsRecord[0]['mt_tran_id'];
	}
	
	
	function checkPharmacyMasterExist($intNCPDPId)
	{
		$strTblName		=	"pharmacy_master";
		$strFieldNames	=	"mt_tran_id";
		$strWhere		= " ncpdpid = '".$intNCPDPId."'";
		$strOrderBy		=	"mt_tran_id desc";
		$rsRecord		=	$this->objPage->getRecords($strTblName, $strFieldNames, $strWhere,"", "",$strOrderBy, "");
		return $rsRecord[0]['mt_tran_id'];
	}

	
	function checkPrescriberMasterExist($intSPIValue)
	{
		$strTblName		=	"prescriber_master";
		$strFieldNames	=	"mt_tran_id";
		$strWhere		=	" spi = '".$intSPIValue."'";
		$strOrderBy		=	"mt_tran_id desc";
		$rsRecord		=	$this->objPage->getRecords($strTblName, $strFieldNames, $strWhere,"", "",$strOrderBy, "");
		return $rsRecord[0]['mt_tran_id'];
	}

	
	function updatePharmacyRequest($intMtTranId,$arrUpdateRequest)
	{
		
		$objData = new MedData();
		
		
		$objData->setProperty("pharmacy_requests","mt_tran_id",NULL,NULL);
		$objData->setFieldValue('mt_tran_id',$intMtTranId);
		foreach($arrUpdateRequest as $strKey => $strValue)
		{
			$objData->setFieldValue($strKey,$strValue);
		}
		
		
		$objData->update();
	}
	
	
	
	function getPrescriberFromPresciberMaster($intMtTranId, $strFieldNames)
	{
		$strTableName	=	"prescriber_master";
		$strWhere		= " mt_tran_id = '".$intMtTranId."'";
		$rsRecord		=	$this->objPage->getRecords($strTableName, $strFieldNames, $strWhere, "", "", $strOrderBy, "");
		return $rsRecord;
	}
	
	function getPrescriberFromPrescriberRequests($intMtTranId, $strFieldNames)
	{
		$strTableName 	= 'prescriber_requests';
		$strWhere 		= " mt_tran_id = '".$intMtTranId."'";
		$rsRecord		=	$this->objPage->getRecords($strTableName, $strFieldNames, $strWhere, "", "", $strOrderBy, "");
		return $rsRecord;
	}
	
	
	function getSpecialtyOptions($strEntityType,$strExcludeValues)
	{
	   $objMedDb = MedDB::getDBObject();
	   
	   
	   $strQuery = "SELECT 
			    `key` ,key_value
			FROM 
			    combo_master
			INNER JOIN 
			    combo_detail ON combo_master.combo_id = combo_detail.combo_id 
			    AND combo_master.case_name = 'DIRECTORY_SPECIALTY' 
			    AND combo_detail.position = '".$strEntityType."'
			ORDER BY 
			    combo_detail.seq_no";
	   
	   $rsRecord = $objMedDb->executeSelect($strQuery);
	   
	   
	    $arrStaticOptions		=	array();
	    for($intIndex = 0, $intTotal = count($rsRecord); $intIndex < $intTotal; $intIndex++)
	    {
		$arrStaticOptions[$rsRecord[$intIndex]['key']] = $rsRecord[$intIndex]['key_value'];
	    }
	    
	    
	    $arrExcludeValues = @explode(",",$strExcludeValues);

	    if(count($arrExcludeValues) > 0)
	    {
		foreach($arrStaticOptions as $intKey => $strValue)
		{
		    if(in_array($intKey,$arrExcludeValues))
		    {
			unset($arrStaticOptions[$intKey]);
		    }
		}
	    }
	    
	    
	    return $arrStaticOptions;
	}

}
?>