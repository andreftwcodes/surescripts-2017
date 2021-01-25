<?php


	

	
	
	include_once("./base/med_module.php"); 
	$objModule=new MedModule();															
	
	
	$objData = new MedData(); 	

	
	$intSettingId			=	$objPage->getRequest('hid_setting_id');		
	$intTableId 				= 	$objPage->getRequest("hid_table_id");	
	$strPageType 			=	$objPage->getRequest("hid_page_type");
	$arrSettingsId		 	= 	$objPage->getRequest("chk_setting_id");

	
	$strMiddle = "./middle/med_general_settings_add.htm"; 										

	
	if(!empty($strPageType) && trim(strtoupper($strPageType)) == "D")
	{	
		$strSettingsId =  implode(",",$arrSettingsId);
		
		$objData->setProperty("settings","setting_id",null,null);
		$strWhere = " setting_id  in (".$strSettingsId.")";
		$objPage->setRequest("strWhere",$strWhere);
		$objData->performAction($strPageType,$strWhere,true);
		
		header("Location:index.php?file=med_general_settings");
		exit;
	}
	else if(!empty($strPageType) && (trim(strtoupper($strPageType)) == "A" || trim(strtoupper($strPageType)) == "E")) 
	{
		$objData->setProperty("settings","setting_id",null,null);	
		
		if(strtoupper($strPageType) == "E" ) 
			$strWhere	=	"setting_id	!=	'".$intSettingId."'";
			
		 $intCnt	=	 $objModule->checkUniqueFieldValue("settings",$strPageTYpe,"var_name",trim($objPage->getRequest("TaRtxt_var_name")),$strWhere) ;
		
		if( $intCnt >0  ) 
		{
			$strPageType	=	$objPage->getRequest('hid_page_type');
			$intTableId =  $objPage->getRequest('hid_table_id');
			
			
			$strMiddle		=	"./middle/med_add_record.htm";
		
			$strPage = $objPage->getHtmlPage($intTableId,$strPageType);

			$strMessage = $objPage->objGeneral->getMessage();
		
			
			$strModuleName = "General Settings";
			
			
			$arrHiddens		=	array(	
										"file"						=>	"med_general_settings_add",
										"strModuleName"		=>	$strModuleName,
										"hid_setting_id"		=>	$intSettingId,
										"setting_id"		=>	$intSettingId,
										"hidin_modified_by"	=>	date("Y-m-d H:i:s"),										
										"hid_table_id"				=>	$intTableId,																		
										"hid_page_type"			=>	$strPageType,
									);
									
		
			$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage('UNIQUE_SETTTING'));							
			
			$objPage->restoreSearchResult($arrHiddens);
			exit;
		}
		else
		{
			
			$objData->setFieldValue("var_name",$objPage->getRequest("TaRtxt_var_name"));
			$objData->setFieldValue("var_desc",$objPage->getRequest("Taara_var_desc"));
			$objData->setFieldValue("var_value",$objPage->getRequest("Tatxt_var_value"));
			$objData->setFieldValue("var_type",$objPage->getRequest("Tatxt_var_type"));
			$objData->setFieldValue("var_select",$objPage->getRequest("Tatxt_var_select"));
			$objData->setFieldValue("var_size",$objPage->getRequest("Intxt"));
			$objData->setFieldValue("var_maxlength",$objPage->getRequest("Intxt_var_maxlength"));
			$objData->setFieldValue("seq_no",$objPage->getRequest("InRtxt_seq_no"));
			$objData->setFieldValue("field_group",$objPage->getRequest("Rslt_field_group"));
			
			$objData->setFieldValue("status",$objPage->getRequest("Taslt_status"));
			
			if($strPageType == "E")
			{
				$objData->setFieldValue("setting_id",$objPage->getRequest("hid_setting_id"));				
				$objData->update();
			}
			else
			{
				$objData->insert();
			}
			
			
					
			header("Location:index.php?file=med_general_settings&hidin_module_id=0");
			exit;
		}
		
		
	}
	else
	{					
		
		$strMiddle="./middle/med_general_settings.htm";
		
		
		$strFlagMessage = 1;
		
		
		$rsGeneralSettings=$objModule->GetGeneralSettings($intModuleId,$intSubModuleId);
		
		
		$rsKeycol = $objPage->getTableKeyCol($intTableId);
		$objData->setProperty("settings",null,null);
	
		for($intGeneralSettings=0;$intGeneralSettings<count($rsGeneralSettings);$intGeneralSettings++)
		{
			$strReqVarName=$objPage->generateHtmlControlName($rsGeneralSettings[$intGeneralSettings]['var_type'],"0","",$rsGeneralSettings[$intGeneralSettings]['var_name']);
			$strVarValue=$objPage->getRequest($strReqVarName);
			$strVarValue=str_replace('\\','\\\\',$strVarValue);
			if($rsGeneralSettings[$intGeneralSettings]['var_name'] <> "SITE_LOGO")
			{	
				$whereCondition = "var_name = '".$rsGeneralSettings[$intGeneralSettings]['var_name']."'";
				$fieldValueArray = array("var_value" => $strVarValue);
				$resultObj = $objData->updateRows($fieldValueArray, $whereCondition);
			}
			else
			{	
				if($_FILES[$strReqVarName]['name'] != "")
				{
					$arrLogo = explode(".",$_FILES[$strReqVarName]['name']);
					if(in_array("gif",$arrLogo))
					{	
						chmod($_FILES[$strReqVarName]['tmp_name'],0777);
						@copy($_FILES[$strReqVarName]['tmp_name'],"./../employee/images/".$rsGeneralSettings[$intGeneralSettings]['var_value']);
						$strMedLogo = "./../images/".$rsGeneralSettings[$intGeneralSettings]['var_value'];
					}
					else
						$strFlagMessage = 0; 
				}
			}
		}

		
		$strAddButton		=	$objPage->getButton("add","class='btn'","Add",0,"onclick=\"doAction(this,'list115','69:A:115:med_list_record',0,'')\"");
		$strDeleteButton	=	$objPage->getButton("delete","class='btn'","Delete",0,"onclick='return deleteSettings()'");
		
		if($strFlagMessage == 1)
			$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage('REC_MOD_MSG'));
		else
			$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage('IMG_UPLOAD_MSG'));	
		
		
		
		$rsGeneralSettings=$objModule->getGeneralSettings($intModuleId,$intSubModuleId);
		
		$intCount=count($rsGeneralSettings);
		
		
		$strModuleName = "General Settings";
		
		
		$strMessage=$objPage->objGeneral->getMessage();	
	
		
		$localValues = array("objPage"=>$objPage,"rsGeneralSettings"=>$rsGeneralSettings,"intCount"=>$intCount,"intModuleId"=>$intModuleId,"intSubModuleId"=>$intSubModuleId,"strModuleName"=>$strModuleName,"strMessage"=>$strMessage,"strAddButton"=>$strAddButton,"strDeleteButton"=>$strDeleteButton);	
	}
?>
