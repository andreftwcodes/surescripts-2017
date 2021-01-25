<?PHP

	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	include_once("./base/med_module.php"); 
	
	
	include_once('../../base/MedServiceLevel.php');
	
	
	include_once('./base/med_html_table.php');
		
	
	$objModule 					=	new MedModule();
	
	
	$intTableId 				=	"8";
	$strPageType 				=	$objPage->getRequest('hid_page_type');
	$intTranId					=	$objPage->getRequest("mt_tran_id");
	$strLastAction				=	$objPage->getRequest("last_service_action");
	$intPharmacyMasterId		=	$objPage->getRequest("hid_pm_id");
	
	$strMiddle					= 	"./middle/med_pharmacy_addedit.htm";
	
	
	if($strPageType != 'V')
	{
		$strPageType 				= 'A';		
		$strAddEditTitle			= 'Add';
		if($intPharmacyMasterId != '')
		{
			$strPageType 			= 'E';
			$strAddEditTitle		= 'Edit';
		}
		else
		{
			if($intTranId != '')
			{
				$arrPharmacyRequestFlags = $objModule->getDetailsFromPharmacyRequests($intTranId, 'service_action,is_uploaded');
	
				
				if(($arrPharmacyRequestFlags[0]['service_action'] == 'UPDATE_PHARMACY') || $arrPharmacyRequestFlags[0]['is_uploaded'] == 'Y')
				{
					$strPageType 	= 'E';
					$strAddEditTitle		= 'Edit';				
				}
			}
		}
	}
	
	
	if($strPageType == 'A')
	{
		$strActiveStartDate		=	date('m/d/Y');
		$objPage->setRequest('TaRtxt_active_start_time',$strActiveStartDate);
		$strActiveStartTime		=	"00:00:00";
		$objPage->setRequest('Tatxt_active_start_time_one',$strActiveStartTime);
		
		
		$arrDate				=	explode("/",$strActiveStartDate);
		$strActiveEndDate		=	date("m/d/Y",@mktime(0,0,0,$arrDate[0],$arrDate[1],$arrDate[2]+2));
		$objPage->setRequest("TaRtxt_active_end_time",$strActiveEndDate);
		$strActiveEndTime		=	"00:00:00";
		$objPage->setRequest('Tatxt_active_end_time_one',$strActiveEndTime);
		
		
		$objPage->setRequest('Tarad_twenty_four_hour_flag','N');
	}
	
	
	
	
	if($intPharmacyMasterId != '')
	{
		
		$rsPharmacyMaster		=	$objModule->getDetailsFromPharmacyMaster($intPharmacyMasterId,'*');
		
		
		
		$DateTime = new DateTime($rsPharmacyMaster[0]['active_start_time']);
		$strActiveStartDate		=	$DateTime->format('m-d-Y');
		$strActiveStartTime		=	$DateTime->format('H:i:s');
		$DateTime = new DateTime($rsPharmacyMaster[0]['active_end_time']);
		$strActiveEndDate		=	$DateTime->format('m-d-Y');
		$strActiveEndTime		=	$DateTime->format('H:i:s');
		
		
		
		$objPage->setRequest('TaRtxt_ncpdpid',$rsPharmacyMaster[0]['ncpdpid']);
		$objPage->setRequest('Tatxt_store_number',$rsPharmacyMaster[0]['store_number']);
		$objPage->setRequest('TaRtxt_store_name',$rsPharmacyMaster[0]['store_name']);
		$objPage->setRequest('TaRtxt_address_line1',$rsPharmacyMaster[0]['address_line1']);
		$objPage->setRequest('Tatxt_address_line2',$rsPharmacyMaster[0]['address_line2']);
		$objPage->setRequest('TaRtxt_city',$rsPharmacyMaster[0]['city']);
		$objPage->setRequest('Rslt_state',$rsPharmacyMaster[0]['state']);
		$objPage->setRequest('InRtxt_zip',$rsPharmacyMaster[0]['zip']);
		$objPage->setRequest('TaRtxt_phone_primary',$rsPharmacyMaster[0]['phone_primary']);
		$objPage->setRequest('TaRtxt_fax',$rsPharmacyMaster[0]['fax']);
		$objPage->setRequest('Tatxt_email',$rsPharmacyMaster[0]['email']);
		$objPage->setRequest('Tatxt_phone_alt1',$rsPharmacyMaster[0]['phone_alt1']);
		$objPage->setRequest('Taslt_phone_alt1_qualifier',$rsPharmacyMaster[0]['phone_alt1_qualifier']);
		$objPage->setRequest('Tatxt_phone_alt2',$rsPharmacyMaster[0]['phone_alt2']);
		$objPage->setRequest('Taslt_phone_alt2_qualifier',$rsPharmacyMaster[0]['phone_alt2_qualifier']);
		$objPage->setRequest('Tatxt_phone_alt3',$rsPharmacyMaster[0]['phone_alt3']);
		$objPage->setRequest('Taslt_phone_alt3_qualifier',$rsPharmacyMaster[0]['phone_alt3_qualifier']);
		$objPage->setRequest('Tatxt_phone_alt4',$rsPharmacyMaster[0]['phone_alt4']);
		$objPage->setRequest('Taslt_phone_alt4_qualifier',$rsPharmacyMaster[0]['phone_alt4_qualifier']);
		$objPage->setRequest('Tatxt_phone_alt5',$rsPharmacyMaster[0]['phone_alt5']);
		$objPage->setRequest('Taslt_phone_alt5_qualifier',$rsPharmacyMaster[0]['phone_alt15_qualifier']);
		$objPage->setRequest('Tatxt_phone_alt6',$rsPharmacyMaster[0]['phone_alt6']);
		$objPage->setRequest('Taslt_phone_alt6_qualifier',$rsPharmacyMaster[0]['phone_alt6_qualifier']);		
		$objPage->setRequest('DtRtxt_active_start_time',$strActiveStartDate);
		$objPage->setRequest('Tatxt_active_start_time_one',$strActiveStartTime);
		$objPage->setRequest('DtRtxt_active_end_time',$strActiveEndDate);
		$objPage->setRequest('Tatxt_active_end_time_one',$strActiveEndTime);
		$objPage->setRequest('Tarad_twenty_four_hour_flag',$rsPharmacyMaster[0]['twenty_four_hour_flag']);
		$objPage->setRequest('Tatxt_cross_street',$rsPharmacyMaster[0]['cross_street']);
		$objPage->setRequest('TaRtxt_npi',$rsPharmacyMaster[0]['npi']);
		$objPage->setRequest('Tatxt_dea',$rsPharmacyMaster[0]['dea']);
		$objPage->setRequest('Tatxt_file_id',$rsPharmacyMaster[0]['file_id']);
		$objPage->setRequest('Tatxt_hin',$rsPharmacyMaster[0]['hin']);
		$objPage->setRequest('Tatxt_bin',$rsPharmacyMaster[0]['bin']);
		$objPage->setRequest('Tatxt_medicaid_number',$rsPharmacyMaster[0]['medicaid_number']);
		$objPage->setRequest('Tatxt_medicare_number',$rsPharmacyMaster[0]['medicare_number']);
		$objPage->setRequest('Tatxt_mutually_defined',$rsPharmacyMaster[0]['mutually_defined']);
		$objPage->setRequest('Tatxt_naic_code',$rsPharmacyMaster[0]['naic_code']);
		$objPage->setRequest('Tatxt_payer_id',$rsPharmacyMaster[0]['payer_id']);
		$objPage->setRequest('Tatxt_ppo_number',$rsPharmacyMaster[0]['ppo_number']);
		$objPage->setRequest('Tatxt_prior_authorization',$rsPharmacyMaster[0]['prior_authorization']);
		$objPage->setRequest('Tatxt_promotion_number',$rsPharmacyMaster[0]['promotion_number']);
		$objPage->setRequest('Tatxt_secondary_coverage',$rsPharmacyMaster[0]['secondary_coverage']);
		$objPage->setRequest('Tatxt_social_security',$rsPharmacyMaster[0]['social_security']);
		$objPage->setRequest('Tatxt_state_license',$rsPharmacyMaster[0]['state_license']);
		$objPage->setRequest('Tatxt_first_name',$rsPharmacyMaster[0]['first_name']);
		$objPage->setRequest('Tatxt_middle_name',$rsPharmacyMaster[0]['middle_name']);
		$objPage->setRequest('Tatxt_last_name',$rsPharmacyMaster[0]['last_name']);
		$objPage->setRequest('Tatxt_suffix',$rsPharmacyMaster[0]['suffix']);
		$objPage->setRequest('Tatxt_prefix',$rsPharmacyMaster[0]['prefix']);
		$objPage->setRequest('Tatxt_fax_portal',$rsPharmacyMaster[0]['fax_portal']);
		
		
		$objPage->setRequest('Rrad_specialty_type1',$rsPharmacyMaster[0]['specialty_type1']);
		
	}
	
	
	
	$strTitle			= 	$objPage->getPageTitleByDb($intTableId);

	
	$strMessage 		=	$objPage->objGeneral->getMessage();
	
	
	if($objPage->getRequest('response_back') != 'YES')
	{
		$strMessage 	=	$objPage->objGeneral->getMessage();		
	}
	else
	{
		$strMessage 	=	$objPage->objGeneral->getSession('PHARMACY_SERVICE_MSG');
		$objPage->objGeneral->setSession('PHARMACY_SERVICE_MSG','');
	}
	
	
	$strActualPageType = 'A';
	if($intTranId != '')
	{
		$strActualPageType = 'E'; 
	}
	$strHtmlControl		= 	$objPage->getHtmlAll($intTableId,$strActualPageType,true,true,NULL,true,true,false,"");
	
	
	if($intPharmacyMasterId != '')
	{
	    if($rsPharmacyMaster[0]['service_level'] != '')
		$strHtmlControl['strservice_level']	=	$rsPharmacyMaster[0]['service_level'];
		
	    
	    $strHtmlControl['strselspecialty_type1'] = $rsPharmacyMaster[0]['specialty_type1'];
	    $strHtmlControl['strselspecialty_type2'] = $rsPharmacyMaster[0]['specialty_type2'];
	    $strHtmlControl['strselspecialty_type3'] = $rsPharmacyMaster[0]['specialty_type3'];
	    $strHtmlControl['strselspecialty_type4'] = $rsPharmacyMaster[0]['specialty_type4'];
	    
	}
	
	if($strPageType=='E' && $strHtmlControl['strservice_level'] != "0")
	{
		
		$objServiceLevel	=	new ServiceLevel($strHtmlControl['strservice_level']);
		
		if($objServiceLevel->ControlledSubstance)
		{
			$strControlledSubstanceChkStatus = 'checked="checked"';			
			$strCSLevel						 = 'Y';
		}
		else 
		{
			$strControlledSubstanceChkStatus = "";
			$strCSLevel						 = 'N';
		}
		
		if($objServiceLevel->Eligibility)
			$strEligibilityChkStatus = 'checked="checked"';			
		else $strEligibilityChkStatus = "";
		
		if($objServiceLevel->MedicationHistory)
			$strMedicationHistoryChkStatus = 'checked="checked"';	
		else $strMedicationHistoryChkStatus = "";
		
		if($objServiceLevel->CancelRx)
			$strCancelRxChkStatus = 'checked="checked"';			
		else $strCancelRxChkStatus = "";
			
		if($objServiceLevel->RxFill)
			$strRxFillChkStatus = 'checked="checked"';				
		else $strRxFillChkStatus = "";
			
		if($objServiceLevel->RxChange)
			$strRxChangeChkStatus = 'checked="checked"';			
		else $strRxChangeChkStatus = "";
			
		if($objServiceLevel->RefillRequest)
			$strRefillRequestChkStatus = 'checked="checked"';		
		else $strRefillRequestChkStatus = "";
			
		if($objServiceLevel->NewRx)
			$strNewRxChkStatus = 'checked="checked"';
		else $strNewRxChkStatus = "";
	}
	
	
	$strPossibleServiceLevels	=	$objPage->objGeneral->getSettings('POSSIBLE_PHARMACY_SERVICE_LEVELS');
	
	
	$arrPossibleServiceLevels	=	@explode(",",$strPossibleServiceLevels);
	
	if($strHtmlControl['strservice_level'] == "0")
		$strDisabledChecked		=	'checked="checked"';
	
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="disabledchkbox" id="disabledchkbox" onclick = "enableDisableServiceLevels(this);" '.$strDisabledChecked.' />&nbsp;Disable';
	
	
	(!in_array('NewRx',$arrPossibleServiceLevels)) ? $strDisabled = "disabled" : $strDisabled = "";
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_newrx" id="chk_sl_newrx" onclick="enableDisableServiceLevels(this);" '.$strNewRxChkStatus.' '.$strDisabled.'/>&nbsp;NewRx';
	
	
	(!in_array('RefillRequest',$arrPossibleServiceLevels)) ? $strDisabled = "disabled" : $strDisabled = "";
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_refillrequest" id="chk_sl_refillrequest" onclick="enableDisableServiceLevels(this);" '.$strRefillRequestChkStatus.' '.$strDisabled.'/>&nbsp;RefillRequest';
	
	
	(!in_array('RxChange',$arrPossibleServiceLevels)) ? $strDisabled = "disabled" : $strDisabled = "";
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_rxchange" id="chk_sl_rxchange" onclick="enableDisableServiceLevels(this);" '.$strRxChangeChkStatus.' '.$strDisabled.'/>&nbsp;RxChange';
	
	
	(!in_array('RxFill',$arrPossibleServiceLevels)) ? $strDisabled = "disabled" : $strDisabled = "";
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_rxfill" id="chk_sl_rxfill" onclick="enableDisableServiceLevels(this);" '.$strRxFillChkStatus.' '.$strDisabled.'/>&nbsp;RxFill';
	
	
	(!in_array('CancelRx',$arrPossibleServiceLevels)) ? $strDisabled = "disabled" : $strDisabled = "";
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_cancelrx" id="chk_sl_cancelrx" onclick="enableDisableServiceLevels(this);" '.$strCancelRxChkStatus.' '.$strDisabled.'/>&nbsp;CancelRx';
	
	
	(!in_array('MedicationHistory',$arrPossibleServiceLevels)) ? $strDisabled = "disabled" : $strDisabled = "";
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_medicationhistory" id="chk_sl_medicationhistory" onclick="enableDisableServiceLevels(this);" '.$strMedicationHistoryChkStatus.' '.$strDisabled.'/>&nbsp;MedicationHistory';
	
	
	(!in_array('Eligibility',$arrPossibleServiceLevels)) ? $strDisabled = "disabled" : $strDisabled = "";
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_eligibility" id="chk_sl_eligibility" onclick="enableDisableServiceLevels(this);" '.$strEligibilityChkStatus.' '.$strDisabled.'/>&nbsp;Eligibility';
	
	
	(!in_array('ControlledSubstance',$arrPossibleServiceLevels)) ? $strDisabled = "disabled" : $strDisabled = "";
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_controlledsubstance" id="chk_sl_controlledsubstance" onclick="enableDisableServiceLevels(this);" disabled '.$strControlledSubstanceChkStatus.' '.$strDisabled.'/>&nbsp;ControlledSubstance';
	
	
	$objServiceLevelTable	=	new HtmlTable(3,"width=90% cellspacing=1 cellpadding=1 ","",'align=left valign=top width=33%');
	
	for($intServiceLevelIndex = 0,$intTotal = count($arrServiceLevelChkBoxes); $intServiceLevelIndex < $intTotal; $intServiceLevelIndex++)
	{
		
		$objServiceLevelTable->addData($arrServiceLevelChkBoxes[$intServiceLevelIndex]);
	}
	
	
	$strServiceLevelTable		=	$objServiceLevelTable->getHtml();
	
	
	
	
	$arrAdditionalSpecialty = $objModule->getSpecialtyOptions("P",",all");
	
	
	$arrSelSpecialtyType = array();
	$arrSelSpecialtyType[] = $strHtmlControl['strselspecialty_type1'];
	$arrSelSpecialtyType[] = $strHtmlControl['strselspecialty_type2'];
	$arrSelSpecialtyType[] = $strHtmlControl['strselspecialty_type3'];
	$arrSelSpecialtyType[] = $strHtmlControl['strselspecialty_type4'];
	
	
	$objSpecialtyTable = new HtmlTable(3,"width=90% cellspacing=1 cellpadding=1 ","",'align=left valign=top width=33%');
	
	$intSpecialtyIndex = 1;
	foreach($arrAdditionalSpecialty as $intSpecialtyKey => $strSpecialtyValue)
	{
	    
	    if(in_array($strSpecialtyValue, $arrSelSpecialtyType))
		$strSelSpecialtyProperty = 'checked = "checked" ';
	    else
		$strSelSpecialtyProperty = '';
	    
	    
	    if($intSpecialtyIndex <= 2)
	    {
		$strSpecialtyRadioBox .=    '<input type="radio" name="Rrad_specialty_type1" id="Rrad_specialty_type1" value="'.$strSpecialtyValue.'" '.$strSelSpecialtyProperty.' class="comn-input" />&nbsp;'.$strSpecialtyValue;
	    }
	    else
	    {
		$strSpecialtyCheckbox = '<input type="checkbox" name="Tachk_specialty_type[]" id="Tachk_specialty_type_'.$intSpecialtyIndex.'" value="'.$strSpecialtyValue.'" '.$strSelSpecialtyProperty.' class="comn-input" />&nbsp;'.$strSpecialtyValue;
		
		 
		$objSpecialtyTable->addData($strSpecialtyCheckbox);
	    }

	    $intSpecialtyIndex++;
	}
	
	
	$strSpecialtyTable = $objSpecialtyTable->getHtml();
	
	
	
	if($objPage->getRequest("hid_pm_id"))
	{
		$strTableName			=	"pharmacy_master left join pharmacy_mos on pharmacy_master.ncpdpid = pharmacy_mos.ncpdpid";
		$strFieldName			=	" pharmacy_mos.meditab_id";
		$strWhere				=	" pharmacy_master.mt_tran_id = '".$objPage->getRequest("hid_pm_id")."'";
		$rsMeditabIdDetails		=	$objPage->getRecords($strTableName, $strFieldName, $strWhere,"", "","", "");
		
		$strMeditabId			=	$rsMeditabIdDetails[0]['meditab_id'];
	}	
		
	
	$localValues = array(
				"intButtonId" => $intButtonId,
				"strFile" => $strFile,
				"intTableId" =>	$intTableId,
				"strTitle" => $strTitle,
				"strPageType" => $strPageType,
				"strMessage" => $strMessage,
				"intTranId" => $intTranId,
				"strServiceLevelTable" => $strServiceLevelTable,
				"strLastAction" => $strLastAction,
				"strAddEditTitle" => $strAddEditTitle,
				"strMeditabId" => $strMeditabId,
				"strSpecialtyTable" => $strSpecialtyTable,
				"strSpecialtyRadioBox" => $strSpecialtyRadioBox,
				"strCSLevel"			=> $strCSLevel
			  );
							
	$localValues = array_merge($localValues,$strHtmlControl);
?>