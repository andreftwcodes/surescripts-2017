<?php

	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	include_once('../../base/MedServiceLevel.php');
	
	
	include_once('../../base/MedXmlParser.php');
	
	
	$strAction	 			=	$objPage->getRequest("hid_action");
	$strAddLocation	 		=	$objPage->getRequest("hid_add_location");
	$strUpdateAction		=	$objPage->getRequest('Rrad_action');
	$intPrescriberMasterId	=	$objPage->getRequest('hid_pm_id');
	$intSPIValue = $objPage->getRequest('Tatxt_spi');
	
	
	
	$strServiceAction	=	$strAction;

	
	$objData				= 	new MedData();
	
	
	$objServiceLevel		=	new ServiceLevel();

	
	$objPage->setTableProperty($objData);

	
	if($strAction) 
	{
		
		$strActiveStartDate			=  	$objPage->getRequest("DtRtxt_active_start_time");
		$strActiveStartTime			=  	$objPage->getRequest("Tatxt_active_start_time_one");
		$arrActiveStartDate			=	explode("-",$strActiveStartDate);
		$strStoreActiveStartDateInDB=	$arrActiveStartDate[2]."-".$arrActiveStartDate[0]."-".$arrActiveStartDate[1];
		
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
		
		
		
		$arrAdditionalSpecialty = $objPage->getRequest('Tachk_specialty_type');
		
		
		$arrAdditionalSpecialtyOptions = $objModule->getSpecialtyOptions("D",",all");
 
		
		$arrAdditionalSpecialtyOptions = array_flip($arrAdditionalSpecialtyOptions);
		    
		
		$intSpecialtyIdBitValue = 0;
		
		if(count($arrAdditionalSpecialty) > 0)
		{
		    
		    $intSpecialtyIndex = 1;
		    
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
		
		
		
		$objData->setFieldValue("service_action",$strServiceAction);
		
		
		foreach($_REQUEST as $strKey => $strValue)
		{
			if(substr($strKey,0,strlen('chk_sl_')) == 'chk_sl_')
			{
				$strKey			=	str_replace("chk_","",$strKey);
				$strRPAFields	.=	$strKey.",";
			}
		}
		$strRPAFields	=	substr($strRPAFields,0,strlen($strRPAFields)-1);
		$objData->setArrRPAFields('specialty_type,active_start_time_one,active_end_time_one,action,'.$strRPAFields);
		
		
		$objData->performAction("A",null);
		
		
		$intMtTranId	=	$objData->getAutoId();

		
		
		$strResponse	=	file_get_contents(SURESCRIPTS_SERVICES_URL.'?ACTION='.$strServiceAction.'&ID='.$intMtTranId);

		
		$strRedirectTo	=	"Location:index.php?file=med_prescribe_addedit&response_back=YES";

		
		
		$strIsSuccess	=	'NO';

		if(MedXmlParser::validateXml($strResponse)===true)
		{
			$objXml				=	new MedXmlParser($strResponse);
			$strErrorCode		=	$objXml->Code->Value;
			$strErrorDescription=	$objXml->Description->Value;
			
			
			$strNote			=	$objXml->Note->Value;
			
			if($strErrorCode == '000' || $strErrorCode == '')
			{
				$strIsSuccess	=	'YES';
				
				$objPage->objGeneral->setSession('PRESCRIBER_SERVICE_MSG','Transaction Successfull.');
				
				if($strNote != '')
					$objPage->objGeneral->setSession('PRESCRIBER_SERVICE_MSG',$strNote);

				
				if($objXml->SPI->Value != '')
				{
				    $intSPIValue = $objXml->SPI->Value;
				}
				
				
				if($intSPIValue != '')
				{
					$objModule->updatePrescriberSPI($intMtTranId,$intSPIValue);
				
					
					if($strServiceAction == 'ADD_PRESCRIBER' || $strServiceAction == 'ADD_PRESCRIBER_LOCATION')
						$strPrescriberMasterAction	=	'A';
					else
					{
						$strPrescriberMasterAction	=	'E';

						
						
					}
					
					
					$intPrescriberMasterId = updatePrescriberMaster($strPrescriberMasterAction, $intSPIValue, $intPrescriberMasterId);
					
				}
				
			}
			else if($strErrorCode != '')
			{
				$objPage->objGeneral->setSession('PRESCRIBER_SERVICE_MSG','Error Code: ' . $strErrorCode . '&nbsp;&nbsp;Description: ' . $strErrorDescription);
			}
			else if($strNote != '')
			{
				$objPage->objGeneral->setSession('PRESCRIBER_SERVICE_MSG',$strNote);
			}
		}
		else
		{
			
			$objPage->objGeneral->setSession('PRESCRIBER_SERVICE_MSG',$strResponse);
		}
		if($strIsSuccess == 'YES')
		{
			
			header($strRedirectTo . '&is_success='.$strIsSuccess . '&action=' . getRelativeResponseBackAction($strServiceAction, $strIsSuccess) . '&hid_pm_id=' . $intPrescriberMasterId);
		}
		else
		{
			header($strRedirectTo . '&is_success='.$strIsSuccess . '&action=' . getRelativeResponseBackAction($strServiceAction, $strIsSuccess) . '&mt_tran_id=' . $intMtTranId);
		}
		exit;
		
	}
	else
	{
		$objPage->objGeneral->raiseError("WARNING","No action define","Action Script","Do not call action script without action parameters"); 
	}

	
	function getRelativeResponseBackAction($strServiceAction, $isSuccess)
	{
		$strResponseBackAction = $strServiceAction;
		switch($strServiceAction)
		{
			CASE 'ADD_PRESCRIBER':
				if($isSuccess == 'YES')
				{
					$strResponseBackAction = 'UPDATE_PRESCRIBER';
				}
				break;
				
			CASE 'UPDATE_PRESCRIBER':
				break;
				
			CASE 'ADD_PRESCRIBER_LOCATION':
				if($isSuccess == 'YES')
				{
					$strResponseBackAction = 'UPDATE_PRESCRIBER_LOCATION';
				}
				break;
				
			CASE 'UPDATE_PRESCRIBER_LOCATION':
				break;
		}
		return $strResponseBackAction;
	}
	
	
	function updatePrescriberMaster($strPrescriberMasterAction, $intSPIValue, $intPrescriberMasterId = '')
	{
		global $objPage;
		
		
		$objData = new MedData();
		
		
		$objData->setProperty("prescriber_master","mt_tran_id",NULL,NULL);
		
		
		$objData->setFieldValue("prefix_name",$objPage->getRequest('Tatxt_prefix_name'));
		$objData->setFieldValue("first_name",$objPage->getRequest('TaRtxt_first_name'));
		$objData->setFieldValue("middle_name",$objPage->getRequest('Tatxt_middle_name'));
		$objData->setFieldValue("last_name",$objPage->getRequest('TaRtxt_last_name'));
		$objData->setFieldValue("suffix_name",$objPage->getRequest('Tatxt_suffix_name'));
		$objData->setFieldValue("specialty_qualifier",$objPage->getRequest('slt_specialty_qualifier'));
		$objData->setFieldValue("specialty_code_primary",$objPage->getRequest('Taslt_specialty_code_primary'));
		
		
		$objData->setFieldValue("dea",$objPage->getRequest('TaRtxt_dea'));
		$objData->setFieldValue("npi",$objPage->getRequest('TaRtxt_npi'));
		$objData->setFieldValue("state_license_number",$objPage->getRequest('Tatxt_state_license_number'));
		$objData->setFieldValue("file_id",$objPage->getRequest('Tatxt_file_id'));
		$objData->setFieldValue("medicaid_number",$objPage->getRequest('Tatxt_medicaid_number'));
		$objData->setFieldValue("medicare_number",$objPage->getRequest('Tatxt_medicare_number'));
		$objData->setFieldValue("upin",$objPage->getRequest('Tatxt_upin'));
		$objData->setFieldValue("dentist_license_number",$objPage->getRequest('Tatxt_dentist_license_number'));
		$objData->setFieldValue("mutually_defined",$objPage->getRequest('Tatxt_mutually_defined'));
		$objData->setFieldValue("prior_authorization",$objPage->getRequest('Tatxt_prior_authorization'));
		$objData->setFieldValue("social_security",$objPage->getRequest('Tatxt_social_security'));
		$objData->setFieldValue("ppo_number",$objPage->getRequest('Tatxt_ppo_number'));
		
		
		$objData->setFieldValue("clinic_name",$objPage->getRequest('Tatxt_clinic_name'));
		$objData->setFieldValue("address_line1",$objPage->getRequest('TaRtxt_address_line1'));
		$objData->setFieldValue("address_line2",$objPage->getRequest('Tatxt_address_line2'));
		$objData->setFieldValue("city",$objPage->getRequest('TaRtxt_city'));
		$objData->setFieldValue("state",$objPage->getRequest('Rslt_state'));
		$objData->setFieldValue("zip",$objPage->getRequest('InRtxt_zip'));
		
		
		$objData->setFieldValue("phone_primary",$objPage->getRequest('TaRtxt_phone_primary'));
		$objData->setFieldValue("fax",$objPage->getRequest('TaRtxt_fax'));
		$objData->setFieldValue("email",$objPage->getRequest('Tatxt_email'));
		$objData->setFieldValue("phone_alt1_qualifier",$objPage->getRequest('Taslt_phone_alt1_qualifier'));
		$objData->setFieldValue("phone_alt1",$objPage->getRequest('Tatxt_phone_alt1'));
		$objData->setFieldValue("phone_alt2_qualifier",$objPage->getRequest('Taslt_phone_alt2_qualifier'));
		$objData->setFieldValue("phone_alt2",$objPage->getRequest('Tatxt_phone_alt2'));
		$objData->setFieldValue("phone_alt3_qualifier",$objPage->getRequest('Taslt_phone_alt3_qualifier'));
		$objData->setFieldValue("phone_alt3",$objPage->getRequest('Tatxt_phone_alt3'));
		$objData->setFieldValue("phone_alt4_qualifier",$objPage->getRequest('Taslt_phone_alt4_qualifier'));
		$objData->setFieldValue("phone_alt4",$objPage->getRequest('Tatxt_phone_alt4'));
		$objData->setFieldValue("phone_alt5_qualifier",$objPage->getRequest('Taslt_phone_alt5_qualifier'));
		$objData->setFieldValue("phone_alt5",$objPage->getRequest('Tatxt_phone_alt5'));
		$objData->setFieldValue("phone_alt6_qualifier",$objPage->getRequest('Taslt_phone_alt6_qualifier'));
		$objData->setFieldValue("phone_alt6",$objPage->getRequest('Tatxt_phone_alt6'));
		
		
		$objData->setFieldValue("service_level",$objPage->getRequest('service_level'));
		$objData->setFieldValue("service_level_bits",$objPage->getRequest('service_level_bits'));
		$objData->setFieldValue("last_modified_date",date("Y-m-d H:i:s"));
		$objData->setFieldValue('active_start_time',$objPage->getRequest('TaRtxt_active_start_time'));
		$objData->setFieldValue('active_end_time',$objPage->getRequest('TaRtxt_active_end_time'));
		
		
		$objData->setFieldValue("specialty_type1",$objPage->getRequest('specialty_type1'));
		$objData->setFieldValue("specialty_type2",$objPage->getRequest('specialty_type2'));
		$objData->setFieldValue("specialty_type3",$objPage->getRequest('specialty_type3'));
		$objData->setFieldValue("specialty_type4",$objPage->getRequest('specialty_type4'));
		$objData->setFieldValue("specialty_id",$objPage->getRequest('specialty_id'));
		
		
		
		if($strPrescriberMasterAction == 'E')
		{
			$objData->setFieldValue("mt_tran_id",$intPrescriberMasterId);
			$objData->update();
		}
		else
		{
			$objData->setFieldValue("spi",$intSPIValue);
			$objData->insert();
			$intPrescriberMasterId = $objData->getAutoId();
		}
		return $intPrescriberMasterId;
	}
?>