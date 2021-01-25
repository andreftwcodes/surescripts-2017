<?php


	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	include_once('../../base/MedServiceLevel.php');
	
	
	include_once('../../base/MedXmlParser.php');
	
	
	$strAction	 		=	$objPage->getRequest("hid_page_type");
	
	
	$objData			= 	new MedData();
	
	
	$objServiceLevel	=	new ServiceLevel();

	
	$objPage->setTableProperty($objData);
	
	
	$intLoginId			=	$objPage->objGeneral->getSession("intUserId");
	
	
	if($strAction == 'A' || $strAction == 'E') 
	{
		
		$isTest						=	$objPage->getRequest('chk_test');
		$intMtTranId				=	$objPage->getRequest('hid_mt_tran_id');
		$intNCPDPId					=	$objPage->getRequest('TaRtxt_ncpdpid');
		
		
		$arrAdditionalSpecialty = $objPage->getRequest('Tachk_specialty_type');
		
		
		if($isTest != 'Yes')
			$objData->setFieldValue("test",'No');
		
		
		$strActiveStartDate			=  	$objPage->getRequest("DtRtxt_active_start_time");
		$strActiveStartTime			=  	$objPage->getRequest("Tatxt_active_start_time_one");
		
		$arrActiveStartDate			=	explode("-", $strActiveStartDate);
		$strStoreActiveStartDateInDB=	$arrActiveStartDate[2] . "-" . $arrActiveStartDate[0]."-".$arrActiveStartDate[1];
		
		
		$objPage->setRequest("TaRtxt_active_start_time",$strStoreActiveStartDateInDB.' '.$strActiveStartTime);
		
		
		
		$strActiveEndDate			=  	$objPage->getRequest("DtRtxt_active_end_time");
		$strActiveEndTime			=  	$objPage->getRequest("Tatxt_active_end_time_one");
		$arrActiveEndDate			=	explode("-",$strActiveEndDate);
		$strStoreActiveEndDateInDB	=	$arrActiveEndDate[2]."-".$arrActiveEndDate[0]."-".$arrActiveEndDate[1];
		
		
		$objPage->setRequest("TaRtxt_active_end_time",$strStoreActiveEndDateInDB.' '.$strActiveEndTime);
		
		
		
		if($objPage->getRequest('disabledchkbox') != 'on')
		{
			if($objPage->getRequest("chk_sl_eligibility")=="on")
			{
				$objServiceLevel->Eligibility		=	true;
			}
			
			if($objPage->getRequest("chk_sl_medicationhistory")=="on")
			{
				$objServiceLevel->MedicationHistory =	true;
			}
				
			if($objPage->getRequest("chk_sl_cancelrx")=="on")
			{
				$objServiceLevel->CancelRx			=	true;
			}
				
			if($objPage->getRequest("chk_sl_rxfill")=="on")
			{
				$objServiceLevel->RxFill 			=	true;
			}
				
			if($objPage->getRequest("chk_sl_rxchange")=="on")
			{
				$objServiceLevel->RxChange 			=	true;
			}
				
			if($objPage->getRequest("chk_sl_refillrequest")=="on")
			{
				$objServiceLevel->RefillRequest 	=	true;
			}
				
			if($objPage->getRequest("chk_sl_newrx")=="on")
			{
				$objServiceLevel->NewRx 			=	true;
			}
			
			
			if($objPage->getRequest("cs_level") == 'Y')
			{	
				$objServiceLevel->ControlledSubstance 			=	true;
			}
			
			
			
			$strServiceLevel						=	$objServiceLevel->getServiceLevelCode();
		}
		else
		{
			$strServiceLevel						=	0;
		}

		
		$objServiceLevelBits = new ServiceLevel($strServiceLevel);
		$strServiceLevelBits						=	$objServiceLevelBits->getServiceLevelBits();
		$objData->setFieldValue("service_level",$strServiceLevel);
		$objData->setFieldValue("service_level_bits", $strServiceLevelBits);
		
		
		$objPage->setRequest("service_level",$strServiceLevel);
		$objPage->setRequest("service_level_bits", $strServiceLevelBits);
		
		
		
		foreach($_REQUEST as $strKey => $strValue)
		{
			if(substr($strKey,0,strlen('chk_sl_')) == 'chk_sl_')
			{
				$strKey			=	str_replace("chk_","",$strKey);
				$strRPAFields	.=	$strKey.",";
			}
		}
		$strRPAFields	=	substr($strRPAFields,0,strlen($strRPAFields)-1);
		$objData->setArrRPAFields('specialty_type,active_start_time_one,active_end_time_one,'.$strRPAFields);

		
		$objData->setFieldValue("created_by",$intLoginId);
		$objData->setFieldValue("created_datetime",date("Y-m-d H:i:s"));
		$objData->setFieldValue("modified_by",$intLoginId);
		$objData->setFieldValue("modified_datetime",date("Y-m-d H:i:s"));
		
		
		
		$arrAdditionalSpecialtyOptions = $objModule->getSpecialtyOptions("P",",all");

		
		$arrAdditionalSpecialtyOptions = array_flip($arrAdditionalSpecialtyOptions);
		    
		
		$intSpecialtyType1 = $objPage->getRequest("Rrad_specialty_type1");
		
		
		$intSpecialtyIdBitValue = $arrAdditionalSpecialtyOptions[$intSpecialtyType1];
		    
		if(count($arrAdditionalSpecialty) > 0)
		{
		    
		    $intSpecialtyIndex = 2;
		    
		    foreach($arrAdditionalSpecialty as $intKey => $strSpecialtyValue)
		    {
			
			if($intSpecialtyIndex <= 4)
			{
			    $objData->setFieldValue("specialty_type".$intSpecialtyIndex,$strSpecialtyValue);
			    
			    
			    $objPage->setRequest("specialty_type".$intSpecialtyIndex,$strSpecialtyValue);
			    
			    $intSpecialtyIdBitValue += $arrAdditionalSpecialtyOptions[$strSpecialtyValue];
			}
			
			
			$intSpecialtyIndex++;
		    }
		}
		
		 
		 $objData->setFieldValue("specialty_id",$intSpecialtyIdBitValue);
		 $objPage->setRequest("specialty_id",$intSpecialtyIdBitValue);   
		
		
		
		if($strAction == 'A')
		{
			$strServiceAction = 'ADD_PHARMACY';
		}
		else if($strAction == 'E')
		{
			$strServiceAction = 'UPDATE_PHARMACY';
		}
		
		$objData->setFieldValue("service_action", $strServiceAction);
	
		
		$objData->performAction("A",null);	
		
		
		$intMtTranId	=	$objData->getAutoId();
		
		
		$strResponse		=	file_get_contents(SURESCRIPTS_SERVICES_URL.'?ACTION='.$strServiceAction.'&ID='.$intMtTranId);
		
		
		$strRedirectTo	=	"Location:index.php?file=med_pharmacy_addedit&response_back=YES&mt_tran_id=".$intMtTranId;
		
		
		
		$strIsSuccess	=	'N';
		
		if(MedXmlParser::validateXml($strResponse) === true)
		{
			$objXml				=	new MedXmlParser($strResponse);
			$strErrorCode		=	$objXml->Code->Value;
			$strErrorDescription=	$objXml->Description->Value;
			$strNote			=	$objXml->Note->Value;
			
			if($strErrorCode == '000' || $strErrorCode == '')
			{
				$strIsSuccess	=	'Y';
				
				
				$objPage->objGeneral->setSession('PHARMACY_SERVICE_MSG', 'Transaction Successfull.');
				
				
				if($strNote != '')
					$objPage->objGeneral->setSession('PHARMACY_SERVICE_MSG', $strNote);
				
				
				
				
				$intPharmacyRecordId	=	$objModule->checkPharmacyMasterExist($intNCPDPId);
				
				
				if($intPharmacyRecordId > 0)
					$strPharmacyMasterAction	=	"E";
				else
					$strPharmacyMasterAction	=	"A";
				
				
				$rsPhamacy	=	storeIntoPharmacyMaster($strPharmacyMasterAction, $intPharmacyRecordId);
				
				
			}
			else if($strErrorCode != '')
			{
				$objPage->objGeneral->setSession('PHARMACY_SERVICE_MSG','Error Code: ' . $strErrorCode . '&nbsp;&nbsp;Description: ' . $strErrorDescription);
			}
			else if($strNote != '')
			{
				$objPage->objGeneral->setSession('PHARMACY_SERVICE_MSG',$strNote);
			}
		}
		else
		{
			
			$objPage->objGeneral->setSession('PHARMACY_SERVICE_MSG',$strResponse);
			
			
			$strErrorDescription	=	"Invalid XML";
		}
		
		
		$arrUpdateRequest = array(
					    "post_response" => addslashes($strResponse),
					    "post_message" => addslashes($strErrorDescription),
					    "is_uploaded" => $strIsSuccess
					);
		
		
		$objModule->updatePharmacyRequest($intMtTranId,$arrUpdateRequest);
		
		
		
		header($strRedirectTo . '&is_success='.$strIsSuccess . '&last_action='.$strAction.'&last_service_action=' . $strServiceAction);
		exit;
		
	}
	else
	{
		$objPage->objGeneral->raiseError("WARNING","No action define","Action Script","Do not call action script without action parameters"); 
	}
	
	function storeIntoPharmacyMaster($strPharmacyMasterAction, $intPharmacyRecordId)
	{
		global $objPage;
		
		
		$objData = new MedData();
		
		
		$objData->setProperty("pharmacy_master","mt_tran_id",NULL,NULL);
		
		$objData->setFieldValue("ncpdpid",$objPage->getRequest('TaRtxt_ncpdpid'));
		$objData->setFieldValue("store_number",$objPage->getRequest('Tatxt_store_number'));
		$objData->setFieldValue("reference_number_alt1",$objPage->getRequest('Tatxt_phone_alt1'));
		$objData->setFieldValue("reference_number_alt1_qualifier",$objPage->getRequest('Taslt_phone_alt2_qualifier'));
		$objData->setFieldValue("store_name",$objPage->getRequest('TaRtxt_store_name'));
		$objData->setFieldValue("address_line1",$objPage->getRequest('TaRtxt_address_line1'));
		$objData->setFieldValue("address_line2",$objPage->getRequest('Tatxt_address_line2'));
		$objData->setFieldValue("city",$objPage->getRequest('TaRtxt_city'));
		$objData->setFieldValue("state",$objPage->getRequest('Rslt_state'));
		$objData->setFieldValue("zip",$objPage->getRequest('InRtxt_zip'));
		$objData->setFieldValue("phone_primary",$objPage->getRequest('TaRtxt_phone_primary'));
		$objData->setFieldValue("fax",$objPage->getRequest('TaRtxt_fax'));
		$objData->setFieldValue("email",$objPage->getRequest('Tatxt_email'));
		$objData->setFieldValue("phone_alt1",$objPage->getRequest('Tatxt_phone_alt1'));
		$objData->setFieldValue("phone_alt1_qualifier",$objPage->getRequest('Taslt_phone_alt1_qualifier'));
		$objData->setFieldValue("phone_alt2",$objPage->getRequest('Tatxt_phone_alt2'));
		$objData->setFieldValue("phone_alt2_qualifier",$objPage->getRequest('Taslt_phone_alt2_qualifier'));
		$objData->setFieldValue("phone_alt3",$objPage->getRequest('Tatxt_phone_alt3'));
		$objData->setFieldValue("phone_alt3_qualifier",$objPage->getRequest('Taslt_phone_alt3_qualifier'));
		$objData->setFieldValue("phone_alt4",$objPage->getRequest('Tatxt_phone_alt4'));
		$objData->setFieldValue("phone_alt4_qualifier",$objPage->getRequest('Taslt_phone_alt4_qualifier'));
		$objData->setFieldValue("phone_alt5",$objPage->getRequest('Tatxt_phone_alt5'));
		$objData->setFieldValue("phone_alt5_qualifier",$objPage->getRequest('Taslt_phone_alt5_qualifier'));
		$objData->setFieldValue("phone_alt6",$objPage->getRequest('Tatxt_phone_alt6'));
		$objData->setFieldValue("phone_alt6_qualifier",$objPage->getRequest('Taslt_phone_alt6_qualifier'));
		$objData->setFieldValue("active_start_time",$objPage->getRequest('TaRtxt_active_start_time'));
		$objData->setFieldValue("active_end_time",$objPage->getRequest('TaRtxt_active_end_time'));
		$objData->setFieldValue("service_level",$objPage->getRequest('service_level'));
		$objData->setFieldValue("service_level_bits",$objPage->getRequest('service_level_bits'));
		$objData->setFieldValue("last_modified_date",date("Y-m-d H:i:s"));
		$objData->setFieldValue("twenty_four_hour_flag",$objPage->getRequest('Tarad_twenty_four_hour_flag'));
		$objData->setFieldValue("cross_street",$objPage->getRequest('Tatxt_cross_street'));
		$objData->setFieldValue("npi",$objPage->getRequest('TaRtxt_npi'));
		$objData->setFieldValue("dea",$objPage->getRequest('Tatxt_dea'));
		$objData->setFieldValue("file_id",$objPage->getRequest('Tatxt_file_id'));
		$objData->setFieldValue("hin",$objPage->getRequest('Tatxt_hin'));
		$objData->setFieldValue("bin",$objPage->getRequest('Tatxt_bin'));
		$objData->setFieldValue("medicaid_number",$objPage->getRequest('Tatxt_medicaid_number'));
		$objData->setFieldValue("medicare_number",$objPage->getRequest('Tatxt_medicare_number'));
		$objData->setFieldValue("mutually_defined",$objPage->getRequest('Tatxt_mutually_defined'));
		$objData->setFieldValue("naic_code",$objPage->getRequest('Tatxt_naic_code'));
		$objData->setFieldValue("payer_id",$objPage->getRequest('Tatxt_payer_id'));
		$objData->setFieldValue("ppo_number",$objPage->getRequest('Tatxt_ppo_number'));
		$objData->setFieldValue("prior_authorization",$objPage->getRequest('Tatxt_prior_authorization'));
		$objData->setFieldValue("promotion_number",$objPage->getRequest('Tatxt_promotion_number'));
		$objData->setFieldValue("secondary_coverage",$objPage->getRequest('Tatxt_secondary_coverage'));
		$objData->setFieldValue("social_security",$objPage->getRequest('Tatxt_social_security'));
		$objData->setFieldValue("state_license",$objPage->getRequest('Tatxt_state_license'));
		$objData->setFieldValue("first_name",$objPage->getRequest('Tatxt_first_name'));
		$objData->setFieldValue("middle_name",$objPage->getRequest('Tatxt_middle_name'));
		$objData->setFieldValue("last_name",$objPage->getRequest('Tatxt_last_name'));
		$objData->setFieldValue("suffix",$objPage->getRequest('Tatxt_suffix'));
		$objData->setFieldValue("prefix",$objPage->getRequest('Tatxt_prefix'));
		$objData->setFieldValue("is_from_directory_download","N");
		
		
		$objData->setFieldValue("specialty_type1",$objPage->getRequest('Rrad_specialty_type1'));
		$objData->setFieldValue("specialty_type2",$objPage->getRequest('specialty_type2'));
		$objData->setFieldValue("specialty_type3",$objPage->getRequest('specialty_type3'));
		$objData->setFieldValue("specialty_type4",$objPage->getRequest('specialty_type4'));
		$objData->setFieldValue("specialty_id",$objPage->getRequest('specialty_id'));
		
		
		
		if($strPharmacyMasterAction == 'E')
		{
			$objData->setFieldValue("mt_tran_id",$intPharmacyRecordId);
			$objData->update();
		}
		else
			$objData->insert();
	}
	
	
	header("Location:index.php?file=med_pharmacy");
	exit;	
?>