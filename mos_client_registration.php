<?php

	
	
	set_time_limit(0);

	
	include_once('med_config.php');
	
	include_once(WEB_ROOT.'base/DB.php');
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	include_once(WEB_ROOT.'base/MedXmlParser.php');
	
	
	$strEntityType = strtoupper(trim($_POST['etype']));
	
	
	$strAction = strtoupper(trim($_POST['action']));
	
	
	$arrRecord = unserialize($_POST['record']);
	
	
	switch($strEntityType)
	{
		case "PHARMACY":
			$strRequestTable = PHARMACY_REQUEST_TABLE;
			$strMasterTable = PHARMACY_MASTER_TABLE;
			$strMOSMappingTable = PHARMACY_MOS_TABLE;
			break;
			
		case "PRESCRIBER":
			$strRequestTable = PRESCRIBER_REQUEST_TABLE;
			$strMasterTable = PRESCRIBER_MASTER_TABLE;
			$strMOSMappingTable = PRESCRIBER_MOS_TABLE;
			break;
		
		default:
			echo 'INVALID OR IMPROPER ENTITY TYPE SPECIFIED.';
			exit;
			break;
	}
	
	
	$arrRecord['service_action'] = $strAction;
	
	
	$arrRecord['partner_account'] = PARTNER_ACCOUNT_ID;
	
	
	$strMeditabId = $arrRecord['meditab_id'];
	unset($arrRecord['meditab_id']);
	
	
	$arrCleanRecord = array();
	foreach($arrRecord as $strKey => $strValue)
	{
		$arrCleanRecord[trim($strKey)] = trim($strValue);
	}
	$arrRecord = $arrCleanRecord;
	unset($arrCleanRecord);
	
	
	$medDB->AutoExecute($strRequestTable, $arrRecord, "INSERT");
	
	
	$intMtTranId	=	$medDB->Insert_ID();
		
	
	$strResponse = file_get_contents(DIRECTORY_SERVICE_URL.'?ACTION='.$strAction.'&ID='.$intMtTranId);
	
	
	$arrResponse = array();
	
	
	if(MedXmlParser::validateXml($strResponse)===true)
	{
		$objXml				=	new MedXmlParser($strResponse);
		$strErrorCode		=	$objXml->Code->Value;
		$strErrorDescription=	$objXml->Description->Value;
		$strNote			=	$objXml->Note->Value;
		
		if($strErrorCode == '000' || $strErrorCode == '')
		{
			$arrResponse['SUCCESS'] = 'Y';
			
			$arrResponse['MSG'] = 'Transaction Successfull.';
			
			if($strNote != '')
			{
				$arrResponse['MSG'] = $strNote;
			}

			
			$intSPIValue	=	$objXml->SPI->Value;
			
			if($intSPIValue != '')
			{
				
				$medDB->AutoExecute($strRequestTable, array('spi'=> $intSPIValue), "UPDATE", "mt_tran_id='" . $intMtTranId . "'");
			
				
				if($strAction == 'ADD_PRESCRIBER' || $strAction == 'ADD_PRESCRIBER_LOCATION')
				{
				    unset($arrRecord['import_id']);
				    
					
					$arrRecord['spi'] = $intSPIValue;
					
					$medDB->AutoExecute($strMasterTable, $arrRecord, "INSERT");
					
					$arrMappingRecord = array();
					$arrMappingRecord['meditab_id'] = $strMeditabId;
					$arrMappingRecord['spi'] = $intSPIValue;
					
					$medDB->AutoExecute($strMOSMappingTable, $arrMappingRecord, "INSERT");
				}
				else
				{
					
					$intSPIValue = $arrRecord['spi'];
					
					$medDB->AutoExecute($strMasterTable, $arrRecord, "UPDATE", "spi='" . $intSPIValue . "'");
				}
				
			}
			
		}
		else if($strErrorCode != '')
		{
			$arrResponse['SUCCESS'] = 'N';
			$arrResponse['MSG'] = 'Error Code: ' . $strErrorCode . '&nbsp;&nbsp;Description: ' . $strErrorDescription;
		}
		else if($strNote != '')
		{
			$arrResponse['SUCCESS'] = 'N';		
			$arrResponse['MSG'] = $strNote;
		}
	}
	else
	{
		
		$arrResponse['SUCCESS'] = 'N';
		$arrResponse['MSG'] = $strResponse; 
	}
	
	$arrResponse['SPI'] = $intSPIValue;
	
	$strResponse = serialize($arrResponse);
	echo $strResponse;
	exit;