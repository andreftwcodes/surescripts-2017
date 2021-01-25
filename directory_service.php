<?php
	
	
	
	include('med_config.php');
	
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	
	include_once(WEB_ROOT.'base/DB.php');
	
	
	include_once(WEB_ROOT.'base/MedRequest.php');
	
	
	include_once(WEB_ROOT.'base/MedOutMessage.php');
	
	
	include_once(WEB_ROOT.'base/MedXmlParser.php');
	
	$strActionType		=	trim($_GET['ACTION']);
	$strTranID			=	trim($_GET['ID']);
	
	if($strActionType == '' && $strTranID == '')
	{
		echo 'Invalid Request';
		exit;
	}
	
	
	$objMessage		=	new MedOutMessage($strActionType);
	
	
	$objMessage->To						=	'SSDR44';
	$objMessage->From					=	DATA_PROVIDER_ID;
	$objMessage->AccountID				=	PARTNER_ACCOUNT_ID;
	$objMessage->ServiceLevel			=	3;
	$objMessage->SentTime				=	getUTCTime();
	$objMessage->Created				=	$objMessage->SentTime;
	$objMessage->Username				=	DIR_LOGIN_ID;
	$objMessage->Password				=	DIR_PASSWORD;
	$objMessage->AccountID				=	PARTNER_ACCOUNT_ID;
	$objMessage->VersionID				=	DIR_VERSION;
	$objMessage->PortalID				=	PORTAL_ID;
	$objMessage->ReqTranID				=	$strTranID;
	
	switch($strActionType)
	{
		case 'ADD_PRESCRIBER':
		case 'UPDATE_PRESCRIBER':
		case 'ADD_PRESCRIBER_LOCATION':
		case 'UPDATE_PRESCRIBER_LOCATION':
			$SQL	=	'SELECT * FROM ' . PRESCRIBER_REQUEST_TABLE . ' WHERE mt_tran_id = \'' . $strTranID . '\'';
			$rsPrescriber	=	$medDB->GetRow($SQL);
			
			
			$objMessage->SPI = $rsPrescriber['spi'];
			$objMessage->DEANumber = $rsPrescriber['dea'];
			$objMessage->StateLicenseNumber = $rsPrescriber['state_license_number'];
			
			
			$objMessage->DentistLicenseNumber = $rsPrescriber['dentist_license_number'];
			$objMessage->FileID = $rsPrescriber['file_id'];
			$objMessage->MedicaidNumber = $rsPrescriber['medicaid_number'];
			$objMessage->MedicareNumber = $rsPrescriber['medicare_number'];
			$objMessage->PPONumber = $rsPrescriber['ppo_number'];
			$objMessage->PriorAuthorization = $rsPrescriber['prior_authorization'];
			$objMessage->SocialSecurity = $rsPrescriber['social_security'];
			$objMessage->UPIN = $rsPrescriber['upin'];
			$objMessage->MutuallyDefined = $rsPrescriber['mutually_defined'];
			
			$objMessage->SpecialtyCode = $rsPrescriber['specialty_code_primary'];
			$objMessage->Qualifier = $rsPrescriber['specialty_qualifier'];
			$objMessage->Prefix = $rsPrescriber['prefix_name'];
			$objMessage->LastName =	$rsPrescriber['last_name'];
			$objMessage->FirstName = $rsPrescriber['first_name'];
			$objMessage->MiddleName = $rsPrescriber['middle_name'];
			$objMessage->Suffix = $rsPrescriber['suffix_name'];
			$objMessage->ClinicName = $rsPrescriber['clinic_name'];

			$objMessage->AddressLine1 = $rsPrescriber['address_line1'];
			$objMessage->AddressLine2 = $rsPrescriber['address_line2'];
			$objMessage->City = $rsPrescriber['city'];
			$objMessage->State = $rsPrescriber['state'];
			$objMessage->ZipCode = $rsPrescriber['zip'];
			$objMessage->Phone = $rsPrescriber['phone_primary'];
			$objMessage->Fax = $rsPrescriber['fax'];
			$objMessage->Email = $rsPrescriber['email'];
				
			$objMessage->phone_alt1 = $rsPrescriber['phone_alt1'];
			$objMessage->phone_alt1_qualifier = $rsPrescriber['phone_alt1_qualifier'];
			$objMessage->phone_alt2 = $rsPrescriber['phone_alt2'];
			$objMessage->phone_alt2_qualifier = $rsPrescriber['phone_alt2_qualifier'];
			$objMessage->phone_alt3 = $rsPrescriber['phone_alt3'];
			$objMessage->phone_alt3_qualifier = $rsPrescriber['phone_alt3_qualifier'];
			$objMessage->phone_alt4 = $rsPrescriber['phone_alt4'];
			$objMessage->phone_alt4_qualifier = $rsPrescriber['phone_alt4_qualifier'];
			$objMessage->phone_alt5 = $rsPrescriber['phone_alt5'];
			$objMessage->phone_alt5_qualifier = $rsPrescriber['phone_alt5_qualifier'];
			$objMessage->phone_alt6 = $rsPrescriber['phone_alt6'];
			$objMessage->phone_alt6_qualifier = $rsPrescriber['phone_alt6_qualifier'];
			
			$objMessage->ActiveStartTime = getUTCTimeFormat($rsPrescriber['active_start_time']);
			$objMessage->ActiveEndTime = getUTCTimeFormat($rsPrescriber['active_end_time']);
			$objMessage->ServiceLevel = $rsPrescriber['service_level'];
			$objMessage->PartnerAccount = $rsPrescriber['partner_account'];
			$objMessage->LastModifiedDate = $rsPrescriber['last_modified_date'];
			
			
			
			$objMessage->TextServiceLevel = $rsPrescriber['text_service_level'];
			
				
			$objMessage->Version = $rsPrescriber['version'];
			$objMessage->NPI = $rsPrescriber['npi'];
			$objMessage->NPILocation = $rsPrescriber['npi_location'];
			$objMessage->SpecialtyID = $rsPrescriber['specialty_id'];
			
			$strMessage						=	 $objMessage->getMessage();
			break;
		
		case 'ADD_PHARMACY':
		case 'UPDATE_PHARMACY':

			$SQL	=	'SELECT * FROM ' . PHARMACY_REQUEST_TABLE . ' WHERE mt_tran_id = \'' . $strTranID . '\'';
			$rsPharmacy	=	$medDB->GetRow($SQL);

			
			$objMessage->NCPDPID = $rsPharmacy['ncpdpid'];
			$objMessage->StoreNumber = $rsPharmacy['store_number'];
			
			
			$objMessage->StoreName = $rsPharmacy['store_name'];
			$objMessage->AddressLine1 = $rsPharmacy['address_line1'];
			$objMessage->AddressLine2 = $rsPharmacy['address_line2'];
			$objMessage->City = $rsPharmacy['city'];
			$objMessage->State = $rsPharmacy['state'];
			$objMessage->ZipCode = $rsPharmacy['zip'];
			$objMessage->Phone = $rsPharmacy['phone_primary'];
			$objMessage->Fax = $rsPharmacy['fax'];
			$objMessage->Email = $rsPharmacy['email'];
			$objMessage->phone_alt1 = $rsPharmacy['phone_alt1'];
			$objMessage->phone_alt1_qualifier = $rsPharmacy['phone_alt1_qualifier'];
			$objMessage->phone_alt2 = $rsPharmacy['phone_alt2'];
			$objMessage->phone_alt2_qualifier = $rsPharmacy['phone_alt2_qualifier'];
			$objMessage->phone_alt3 = $rsPharmacy['phone_alt3'];
			$objMessage->phone_alt3_qualifier = $rsPharmacy['phone_alt3_qualifier'];
			$objMessage->phone_alt4 = $rsPharmacy['phone_alt4'];
			$objMessage->phone_alt4_qualifier = $rsPharmacy['phone_alt4_qualifier'];
			$objMessage->phone_alt5 = $rsPharmacy['phone_alt5'];
			$objMessage->phone_alt5_qualifier = $rsPharmacy['phone_alt5_qualifier'];
			$objMessage->phone_alt6 = $rsPharmacy['phone_alt6'];
			$objMessage->phone_alt6_qualifier = $rsPharmacy['phone_alt6_qualifier'];
			$objMessage->ActiveStartTime = getUTCTimeFormat($rsPharmacy['active_start_time']);
			$objMessage->ActiveEndTime = getUTCTimeFormat($rsPharmacy['active_end_time']);
			$objMessage->ServiceLevel = $rsPharmacy['service_level'];
			$objMessage->PartnerAccount = $rsPharmacy['partner_account'];
			$objMessage->BackupPortalID = $rsPharmacy['fax_portal'];
			$objMessage->LastModifiedDate = $rsPharmacy['last_modified_date'];
			$objMessage->TwentyFourHourFlag = $rsPharmacy['twenty_four_hour_flag'];
			$objMessage->CrossStreet = $rsPharmacy['cross_street'];
			$objMessage->OldServiceLevel = $rsPharmacy['old_service_level'];
			$objMessage->TextServiceLevel = $rsPharmacy['text_service_level'];
			$objMessage->TextServiceLevelChange = $rsPharmacy['text_service_level_change'];
			$objMessage->Version = $rsPharmacy['version'];
			$objMessage->NPI = $rsPharmacy['npi'];
			$objMessage->FileID = $rsPharmacy['file_id'];
			$objMessage->DEANumber = $rsPharmacy['dea'];
			$objMessage->BINLocationNumber = $rsPharmacy['bin'];
			$objMessage->HIN = $rsPharmacy['hin'];
			$objMessage->MedicaidNumber = $rsPharmacy['medicaid_number'];
			$objMessage->MedicareNumber = $rsPharmacy['medicare_number'];
			$objMessage->MutuallyDefined = $rsPharmacy['mutually_defined'];
			$objMessage->NAICCode = $rsPharmacy['naic_code'];
			$objMessage->PayerID = $rsPharmacy['payer_id'];
			$objMessage->PPONumber = $rsPharmacy['ppo_number'];
			$objMessage->PriorAuthorization = $rsPharmacy['prior_authorization'];
			$objMessage->PromotionNumber = $rsPharmacy['promotion_number'];
			$objMessage->SecondaryCoverage = $rsPharmacy['secondary_coverage'];
			$objMessage->SocialSecurity = $rsPharmacy['social_security'];
			$objMessage->StateLicenseNumber = $rsPharmacy['state_license'];
			$objMessage->SpecialtyID = $rsPharmacy['specialty_id'];
			
			$strMessage = $objMessage->getMessage();
			break;
	}

	$objMedRequest				=	new MedRequest(DIR_URL);
	
	
	$objMedRequest->addHeader("Authorization: Basic ".base64_encode(MSG_LOGIN_ID.':'.MSG_PASSWORD));
	$objMedRequest->addHeader("Content-Type: application/xml; charset=UTF-8");
	$objMedRequest->addHeader("Content-length: ".(strlen($strMessage)));
	
	
	$strResponse				=	$objMedRequest->Post($strMessage);
	
	if( strpos($strResponse, "<Message") !== false)
	{
	    
	    $objResponseXml				=	new MedXmlParser($strResponse);

	    $strXML = extractElementData($strResponse, 'Body');
	    $strResponseEDIMessage = str_replace(array("<Body>","<Message>","</Body>","</Message>"),"",$strXML);


	}
	else
	{
		$strResponseEDIMessage = $strResponse;
	}

	
	$objMessage->updateOutMessageResponse($strResponse, $strResponseEDIMessage);
	
	echo $strResponse;
	
	
	function extractElementData($strXml, $strElement, $blnExcludeElement = true)
	{
		
		$intStartPosMargin = $intEndPosMargin = 0;
		if($blnExcludeElement == true)
		{
			$intStartPosMargin = strlen($strElement) + 2;
			$intEndPosMargin = strlen($strElement) - 3;
		}
		
		$intStartPos = strpos($strXml, '<' . $strElement . '>') + $intStartPosMargin;
		
		$intEndPos = strpos($strXml, '</' . $strElement . '>') + $intEndPosMargin; 
		
		
		$strXmlParts = substr($strXml, $intStartPos, $intEndPos);
		
		
		return $strXmlParts;
	}
?>