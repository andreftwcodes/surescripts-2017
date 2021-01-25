<?php

	
	include_once('./../base/meditab/med_grouplist.php');
	
	
	include_once('./base/med_html_table.php');
	
	
	include_once("./base/med_module.php"); 
	include_once('../../base/MedServiceLevel.php');
		
	
	$objModule = new MedModule();
	
	
	$intTableId 		=	"9";
	
	$intTranId			=	$objPage->getRequest("mt_tran_id");
	
	$intPrescriberMasterId = $objPage->getRequest('hid_pm_id');
	
	$strAction			=  	$objPage->getRequest("action");
	
	$isResponseBack		=	$objPage->getRequest('response_back');
	
	$isSuccess 			=	$objPage->getRequest('is_success');
	
	$strMiddle			= 	"./middle/med_prescribe_addedit.htm";
	
	
	
	
	if($intTranId != '')
	{
		
		$rsPrescriberMaster		=	$objModule->getPrescriberFromPrescriberRequests($intTranId, '*');
		
		
		$intPrescriberMasterId = $objModule->checkPrescriberMasterExist($rsPrescriberMaster[0]['spi']);
	}
	elseif($intPrescriberMasterId != '')
	{
		
		$rsPrescriberMaster		=	$objModule->getPrescriberFromPresciberMaster($intPrescriberMasterId, '*');
	}
	
	
	switch($strAction)
	{
		CASE 'ADD_PRESCRIBER':
			
			$strPageType = 'A';
			
			if($isResponseBack == 'YES' && $isSuccess != 'YES')
			{
				
				setEditControlsOnPage('ALL', $rsPrescriberMaster);
			}
			
			break;
			
		CASE 'UPDATE_PRESCRIBER':
		
			
			$strPageType = 'E';
			
			$objPage->setRequest('Rrad_action','UPDATE_PRESCRIBER');
			
			setEditControlsOnPage('ALL', $rsPrescriberMaster);
			break;
			
		CASE 'ADD_PRESCRIBER_LOCATION':
			
			
			$strPageType = 'A';
			
			
			$rsPrescriberMaster[0]['spi']	=	substr($rsPrescriberMaster[0]['spi'],0,10);
			
			
			setEditControlsOnPage('BASIC_INFO', $rsPrescriberMaster);
			
			break;
			
		CASE 'UPDATE_PRESCRIBER_LOCATION':
			
			
			$strPageType = 'E';
			
			$objPage->setRequest('Rrad_action','UPDATE_PRESCRIBER_LOCATION');
			
			setEditControlsOnPage('ALL', $rsPrescriberMaster);
			
			break;
	}
	
	

	
	
	
	$strMessage 	=	$objPage->objGeneral->getMessage();
	
	
	$strTitle		= 	$objPage->getPageTitleByDb($intTableId);

	
	if($objPage->getRequest('response_back') != 'YES')
	{
		$strMessage 	=	$objPage->objGeneral->getMessage();		
	}
	else
	{
		$strMessage 	=	$objPage->objGeneral->getSession('PRESCRIBER_SERVICE_MSG');
		$objPage->objGeneral->setSession('PRESCRIBER_SERVICE_MSG','');
	}
	
	
	$strHtmlControl	= 	$objPage->getHtmlAll($intTableId,'A',true,true,NULL,true,true,false,"");
	
	
	if(count($rsPrescriberMaster) > 0)
	{
	    if($rsPrescriberMaster[0]['service_level'] != '')
		$strHtmlControl['strservice_level']	=	$rsPrescriberMaster[0]['service_level'];
	    
	    
	    $strHtmlControl['strselspecialty_type1'] = $rsPrescriberMaster[0]['specialty_type1'];
	    $strHtmlControl['strselspecialty_type2'] = $rsPrescriberMaster[0]['specialty_type2'];
	    $strHtmlControl['strselspecialty_type3'] = $rsPrescriberMaster[0]['specialty_type3'];
	    $strHtmlControl['strselspecialty_type4'] = $rsPrescriberMaster[0]['specialty_type4'];
	    
	    
	}
	
	
	if($strHtmlControl['strservice_level'] != "0")
	{
		
		$objServiceLevel	=	new ServiceLevel($strHtmlControl['strservice_level']);
		
		if($objServiceLevel->ControlledSubstance)
			$strControlledSubstanceChkStatus = 'checked="checked"';			
		else $strControlledSubstanceChkStatus = "";
		
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
	
	$arrServiceLevelChkBoxes[]	=	'<input type="checkbox" name="chk_sl_controlledsubstance" id="chk_sl_controlledsubstance" onclick="enableDisableServiceLevels(this);" '.$strControlledSubstanceChkStatus.' '.$strDisabled.'/>&nbsp;ControlledSubstance';
	
	
	$objServiceLevelTable	=	new HtmlTable(3,"width=90% cellspacing=1 cellpadding=1 ","",'align=left valign=top width=33%');
	
	for($intServiceLevelIndex = 0,$intTotal = count($arrServiceLevelChkBoxes); $intServiceLevelIndex < $intTotal; $intServiceLevelIndex++)
	{
		
		$objServiceLevelTable->addData($arrServiceLevelChkBoxes[$intServiceLevelIndex]);
	}
	
	
	$strServiceLevelTable		=	$objServiceLevelTable->__toString();
	
	
	
	
	$arrAdditionalSpecialty = $objModule->getSpecialtyOptions("D",",all");
	
	
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
	    
	    $strSpecialtyCheckbox = '<input type="checkbox" name="Tachk_specialty_type[]" id="Tachk_specialty_type_'.$intSpecialtyIndex.'" value="'.$strSpecialtyValue.'" '.$strSelSpecialtyProperty.' class="comn-input" />&nbsp;'.$strSpecialtyValue;
		
	    
	    $objSpecialtyTable->addData($strSpecialtyCheckbox);

	    $intSpecialtyIndex++;
	}
	
	
	$strSpecialtyTable = $objSpecialtyTable->getHtml();
	
	
	
	if($objPage->getRequest("hid_pm_id"))
	{
		$strTableName			=	"prescriber_master left join prescriber_mos on prescriber_master.spi = prescriber_mos.spi";
		$strFieldName			=	" prescriber_mos.meditab_id";
		$strWhere				=	" prescriber_master.mt_tran_id = '".$objPage->getRequest("hid_pm_id")."'";
		$rsMeditabIdDetails		=	$objPage->getRecords($strTableName, $strFieldName, $strWhere,"", "","", "");
		
		$strMeditabId			=	$rsMeditabIdDetails[0]['meditab_id'];
	}	
			

	$localValues	=	array(
					"intButtonId" => $intButtonId,
					"strFile" => $strFile,
					"intTableId" => $intTableId,
					"strTitle" => $strTitle,
					"strMessage" => $strMessage,
					"intTranId" => $intTranId,
					"intPrescriberMasterId" => $intPrescriberMasterId,
					"strEligibilityChkStatus" => $strEligibilityChkStatus,
					"strMedicationHistoryChkStatus" => $strMedicationHistoryChkStatus,
					"strCancelRxChkStatus" => $strCancelRxChkStatus,
					"strRxFillChkStatus" => $strRxFillChkStatus,
					"strRxChangeChkStatus" => $strRxChangeChkStatus,
					"strRefillRequestChkStatus" => $strRefillRequestChkStatus,
					"strNewRxChkStatus" => $strNewRxChkStatus,
					"strServiceLevelTable" => $strServiceLevelTable,
					"strAction" => $strAction,
					"strPageType" => $strPageType,
					"intPrescriberMasterId" => $intPrescriberMasterId,
					"strMeditabId" => $strMeditabId,
					"strSpecialtyTable" => $strSpecialtyTable
				    );
							
	$localValues	=	array_merge($localValues,$strHtmlControl);
	
	
	function setEditControlsOnPage($strSectionToSet = 'ALL', $rsPrescriber)
	{
		global $objPage;
		switch($strSectionToSet)
		{
			CASE 'BASIC_INFO':
				$objPage->setRequest('Tatxt_spi',$rsPrescriber[0]['spi']);
				$objPage->setRequest('Tatxt_prefix_name',$rsPrescriber[0]['prefix_name']);
				$objPage->setRequest('TaRtxt_first_name',$rsPrescriber[0]['first_name']);
				$objPage->setRequest('Tatxt_middle_name',$rsPrescriber[0]['middle_name']);
				$objPage->setRequest('TaRtxt_last_name',$rsPrescriber[0]['last_name']);
				$objPage->setRequest('Tatxt_suffix_name',$rsPrescriber[0]['suffix_name']);
				$objPage->setRequest('slt_specialty_qualifier',$rsPrescriber[0]['specialty_qualifier']);
				$objPage->setRequest('Taslt_specialty_code_primary',$rsPrescriber[0]['specialty_code_primary']);
				break;
			
			CASE 'ALL':
				
				setEditControlsOnPage('BASIC_INFO', $rsPrescriber);

				
				$objPage->setRequest('TaRtxt_dea',$rsPrescriber[0]['dea']);
				$objPage->setRequest('TaRtxt_npi',$rsPrescriber[0]['npi']);
				$objPage->setRequest('Tatxt_state_license_number',$rsPrescriber[0]['state_license_number']);
				$objPage->setRequest('Tatxt_file_id',$rsPrescriber[0]['file_id']);
				$objPage->setRequest('Tatxt_medicaid_number',$rsPrescriber[0]['medicaid_number']);
				$objPage->setRequest('Tatxt_medicare_number',$rsPrescriber[0]['medicare_number']);
				$objPage->setRequest('Tatxt_upin',$rsPrescriber[0]['upin']);
				$objPage->setRequest('Tatxt_dentist_license_number',$rsPrescriber[0]['dentist_license_number']);
				$objPage->setRequest('Tatxt_mutually_defined',$rsPrescriber[0]['mutually_defined']);
				$objPage->setRequest('Tatxt_prior_authorization',$rsPrescriber[0]['prior_authorization']);
				$objPage->setRequest('Tatxt_social_security',$rsPrescriber[0]['social_security']);
				$objPage->setRequest('Tatxt_ppo_number',$rsPrescriber[0]['ppo_number']);
				
				
				$objPage->setRequest('Tatxt_clinic_name',$rsPrescriber[0]['clinic_name']);
				$objPage->setRequest('TaRtxt_address_line1',$rsPrescriber[0]['address_line1']);
				$objPage->setRequest('Tatxt_address_line2',$rsPrescriber[0]['address_line2']);
				$objPage->setRequest('TaRtxt_city',$rsPrescriber[0]['city']);
				$objPage->setRequest('Rslt_state',$rsPrescriber[0]['state']);
				$objPage->setRequest('InRtxt_zip',$rsPrescriber[0]['zip']);
				
				
				$objPage->setRequest('TaRtxt_phone_primary',$rsPrescriber[0]['phone_primary']);
				$objPage->setRequest('TaRtxt_fax',$rsPrescriber[0]['fax']);
				$objPage->setRequest('Tatxt_email',$rsPrescriber[0]['email']);
				$objPage->setRequest('Taslt_phone_alt1_qualifier',$rsPrescriber[0]['phone_alt1_qualifier']);
				$objPage->setRequest('Tatxt_phone_alt1',$rsPrescriber[0]['phone_alt1']);
				$objPage->setRequest('Taslt_phone_alt2_qualifier',$rsPrescriber[0]['phone_alt2_qualifier']);
				$objPage->setRequest('Tatxt_phone_alt2',$rsPrescriber[0]['phone_alt2']);
				$objPage->setRequest('Taslt_phone_alt3_qualifier',$rsPrescriber[0]['phone_alt3_qualifier']);
				$objPage->setRequest('Tatxt_phone_alt3',$rsPrescriber[0]['phone_alt3']);
				$objPage->setRequest('Taslt_phone_alt4_qualifier',$rsPrescriber[0]['phone_alt4_qualifier']);
				$objPage->setRequest('Tatxt_phone_alt4',$rsPrescriber[0]['phone_alt4']);
				$objPage->setRequest('Taslt_phone_alt5_qualifier',$rsPrescriber[0]['phone_alt5_qualifier']);
				$objPage->setRequest('Tatxt_phone_alt5',$rsPrescriber[0]['phone_alt5']);
				$objPage->setRequest('Taslt_phone_alt6_qualifier',$rsPrescriber[0]['phone_alt6_qualifier']);
				$objPage->setRequest('Tatxt_phone_alt6',$rsPrescriber[0]['phone_alt6']);
				
				
				
				$DateTime = new DateTime($rsPrescriber[0]['active_start_time']);
				$strActiveStartDate		=	$DateTime->format('m-d-Y');
				$strActiveStartTime		=	$DateTime->format('H:i:s');
				$DateTime = new DateTime($rsPrescriber[0]['active_end_time']);
				$strActiveEndDate		=	$DateTime->format('m-d-Y');
				$strActiveEndTime		=	$DateTime->format('H:i:s');
				
				
				
				$objPage->setRequest('DtRtxt_active_start_time',$strActiveStartDate);
				$objPage->setRequest('Tatxt_active_start_time_one',$strActiveStartTime);
				$objPage->setRequest('DtRtxt_active_end_time',$strActiveEndDate);
				$objPage->setRequest('Tatxt_active_end_time_one',$strActiveEndTime);
				break;
		}
	}
?>