<?php
	

	
	set_time_limit(0);

	
	include_once('med_config.php');
	
	
	include_once(WEB_ROOT.'base/MedRequest.php');
	
	
	$strFileName = "FAA4.csv";
	
	
	$strFileToBeImported = "./mass_prescriber_upload/" . $strFileName;

	$fHandle = fopen($strFileToBeImported, "r");
	
	
	$intRecordsImported = $intBadRecords = 0;
	
	$intCounter = 0;
	
	$strCurrentUniqueID = $strLastUniqueID = "";
	printHeader();
	while( $arrLine = fgetcsv($fHandle))
	{
		
		$strCurrentUniqueID = $arrLine[0];
		
		if($arrLine[1] != "")
		{
			$strCurrentUniqueID = $arrLine[1];
		}
		if($intCounter >= 1 && $strCurrentUniqueID != "")
		{
			
			if($strLastUniqueID != $strCurrentUniqueID || $strCurrentUniqueID == "")
			{
				$arrData['action'] = "ADD_PRESCRIBER";
			}
			else
			{
				$arrData['action'] = "ADD_PRESCRIBER_LOCATION";
				$arrRecord['spi'] = $strSPIRoot;
			}
			$arrData['etype'] = "PRESCRIBER";
			$arrRecord['specialty_qualifier'] = "AM";
			$arrRecord['specialty_code_primary'] = "IM";
			$arrRecord['service_level'] = "1";
			$arrRecord['service_level_bits'] = "0";
			$arrRecord['partner_account'] = "700";
			$arrRecord['active_start_time'] = "2011-09-28 12:00:00";
			$arrRecord['active_end_time'] = "2018-09-28 12:00:00";

			
			$arrRecord['dea'] = $arrLine[0];
			$arrRecord['npi'] = $arrLine[1];
			$arrRecord['first_name'] = $arrLine[2];
			$arrRecord['last_name'] = $arrLine[3];
			$arrRecord['clinic_name'] = $arrLine[4];
			$arrRecord['address_line1'] = $arrLine[5];
			$arrRecord['address_line2'] = $arrLine[6];
			$arrRecord['city'] = $arrLine[7];
			$arrRecord['state'] = $arrLine[8];
			$arrRecord['zip'] = $arrLine[9];
			$arrRecord['phone_primary'] = $arrLine[10];
			$arrRecord['fax'] = $arrLine[11]; 
			$arrRecord['meditab_id'] = str_pad($arrLine[12], 7, "0", STR_PAD_LEFT);
			$arrData['record'] = serialize($arrRecord);
			
			
			
			$objRequest = new MedRequest("https://smssproduction.meditab.com/surescripts/mos_client_registration.php");

			
			$strResponse = $objRequest->Post($arrData);
			$arrResponse = unserialize($strResponse);
			$strSPIRoot = substr($arrResponse['SPI'], 0, 10);
			
			printBody($arrRecord, $arrResponse['SPI'], $arrResponse, $arrData['action']);
		}
		$strLastUniqueID = $strCurrentUniqueID;
		$intCounter++;
		
		
	}
	printFooter();
	
function printHeader()
{
	echo '<table border="1" style="border-collapse:collapse;font-family:tahoma;font-size:10px">';
	echo '<tr style="font-weight:bold">';
	echo '<td>', "SPI", "</td>";
	echo '<td>', "DEA", "</td>";
	echo '<td>', "NPI", "</td>";
	echo '<td>', "First Name", "</td>";
	echo '<td>', "Last Name", "</td>";
	echo '<td>', "Clinic Name", "</td>";
	echo '<td>', "Address 1", "</td>";
	echo '<td>', "Address 2", "</td>";
	echo '<td>', "City", "</td>";
	echo '<td>', "State", "</td>";
	echo '<td>', "Zip", "</td>";
	echo '<td>', "Phone", "</td>";
	echo '<td>', "Fax", "</td>";
	echo '<td>', "MeditabID", "</td>";
	echo '<td>', "Action?", "</td>";
	echo '<td>', "Success?", "</td>";
	echo '<td>', "Response", "</td>";
	echo '</tr>';
}
function printBody($arrRecord, $SPI, $Response, $Action)
{
	echo '<tr>';
	echo '<td>', $SPI, "</td>";
	echo '<td>', $arrRecord['dea'], "</td>";
	echo '<td>', $arrRecord['npi'], "</td>";
	echo '<td>', $arrRecord['first_name'], "</td>";
	echo '<td>', $arrRecord['last_name'], "</td>";
	echo '<td>', $arrRecord['clinic_name'], "</td>";
	echo '<td>', $arrRecord['address_line1'], "</td>";
	echo '<td>', $arrRecord['address_line2'], "</td>";
	echo '<td>', $arrRecord['city'], "</td>";
	echo '<td>', $arrRecord['state'], "</td>";
	echo '<td>', $arrRecord['zip'], "</td>";
	echo '<td>', $arrRecord['phone_primary'], "</td>";
	echo '<td>', $arrRecord['fax'], "</td>";
	echo '<td>', $arrRecord['meditab_id'], "</td>";
	echo '<td>', $Action, "</td>";
	echo '<td>', $Response['SUCCESS'], "</td>";
	echo '<td>', $Response['MSG'], "</td>";
	echo '</tr>';
	flush();
}
function printFooter()
{
	echo '</table>';
}