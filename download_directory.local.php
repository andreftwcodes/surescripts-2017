<?php
	

	
	set_time_limit(0);
	
	
	include('med_config.php');
	
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	
	include_once(WEB_ROOT.'base/DB.php');
	
	
	include_once(WEB_ROOT.'base/FlatFile.php');
	
	
	include_once(WEB_ROOT.'base/MedRequest.php');
	
	
	include_once(WEB_ROOT.'base/MedOutMessage.php');
	
	
	include_once(WEB_ROOT.'base/MedXmlParser.php');
	
	
	include_once(WEB_ROOT.'base/MedServiceLevel.php');

	
	
	$strDirectoryType							=	$_GET['TYPE'];
	
	$strDownloadType							=	$_GET['DOWNLOAD'];
	
	
	$arrOptions									=	array('trace'=>1);
	
	
	$strURL										=	DIR_URL;
	
	if($strDownloadType != 'NIGHTLY' && $strDownloadType != 'FULL')
	{
		echo '<h3 style="color:red;font-family:tahoma">Inavalid / no Download Type is not specified. [NIGHTLY/FULL]</h3>';
		exit;
	}
	
	
	if($strDirectoryType == 'PHARMACY')
	{
		$arrConfig			=	$cfgPharmacy;			
		$strTable			=	PHARMACY_MASTER_TABLE;	
		$strUniqueKey		=	'ncpdpid';				
		$strTaxonomyCode	=	'183500000X';			
	}
	
	else if($strDirectoryType == 'PRESCRIBER')
	{
		$arrConfig			=	$cfgPrescriber;			
		$strTable			=	PRESCRIBER_MASTER_TABLE;
		$strUniqueKey		=	'spi';					
		$strTaxonomyCode	=	'193200000X';			
	}
	else
	{
		echo '<h3 style="color:red;font-family:tahoma">Invalid / no Directory Type is not specified. [PHARMACY|PRESCRIBER]</h3>';
		exit;
	}
	
	
	$objOutMessage					=	new MedOutMessage('DIRECTORY_DOWNLOAD_'.$strDownloadType);
	
	$objOutMessage->To				=	'SSSDIR';
	$objOutMessage->From			=	DATA_PROVIDER_ID;
	$objOutMessage->SentTime		=	getUTCTime();
	$objOutMessage->Created			=	$objOutMessage->SentTime;
	$objOutMessage->Username		=	DIR_LOGIN_ID;
	$objOutMessage->Password		=	DIR_PASSWORD;
	$objOutMessage->AccountID		=	PARTNER_ACCOUNT_ID;
	$objOutMessage->VersionID		=	DIR_VERSION;
	$objOutMessage->TaxonomyCode	=	$strTaxonomyCode;
	$objOutMessage->DirectoryDate	=	gmdate('Ymd');
	
	$SentTime						=	getUTCTime();
	$CreatedTime					=	$SentTime;
	$strRequest						=	$objOutMessage->getMessage();
	
	$strAuth						=	DIR_LOGIN_ID.':'.DIR_PASSWORD;

	$objMedRequest					=	new MedRequest($strURL);
	
	$objMedRequest->addHeader("Authorization: Basic ".base64_encode($strAuth));
	$objMedRequest->addHeader("Content-Type: application/xml; charset=UTF-8");
	$objMedRequest->addHeader("Content-length: ".(strlen($strRequest)));
	
	

	

	
	{
		
		$objXml							=	new MedXmlParser($strResponse);
		$strDirDownloadURL				=	$objXml->URL->Value;

		if($strDirDownloadURL != '')
		{

			
			

			
			
			{
			
		    
		    
			}
			
			{
			
			
			}
		}
		else
		{
			
			
		}
	}
	
	
	
	$zaDirectory	=	new ZipArchive();
    
    
    $strDirDownloadURL = 'sample.txt';
	$strFile = "C:/DIRECTORY_DOWNLOADS/sample.txt";
	
	
	
	$startTime									=	date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
	
	
	$arrDirectoryDownloadLog					=	array();
	$arrDirectoryDownloadLog['directory_type']	=	$strDirectoryType;
	$arrDirectoryDownloadLog['download_type']	=	$strDownloadType;
	$arrDirectoryDownloadLog['file']			=	$strFile;
	$arrDirectoryDownloadLog['start_time']		=	$startTime;
	$medDB->AutoExecute(DIRECTORY_DOWNLOAD_LOG,$arrDirectoryDownloadLog , 'INSERT');
	$intLogID									=	$medDB->Insert_ID();
	
	$objFile							=	new FlatFile($strFile,$arrConfig,5);

	
	$intRecordCount								=	0;
	$intLineCount								=	0;
	$intUpdateCount								=	0;
	if($strDownloadType == 'FULL')
	{
		
		
		while($objFile->EOF != true && $objFile->Error != true)
		{
			
			$arrRecordSet							=	$objFile->parseToRecordset();

			foreach($arrRecordSet as $arrRecord)
			{
				$intLineCount						+=	1;
				
				
				$arrRecord['mt_log_id']				=	$intLogID;
				
				
				$objServiceLevel 					= 	new ServiceLevel($arrRecord['service_level']);	
				$arrRecord['service_level_bits']	=	$objServiceLevel->getServiceLevelBits();
				unset($objServiceLevel);
				
				
				$medDB->AutoExecute($strTable, $arrRecord, 'INSERT');
				$intAffectedRows					=	$medDB->Affected_Rows();
				if($intAffectedRows > 0)
				{
					$intRecordCount 				+= 	$medDB->Affected_Rows();
				}
				else
				{
					logFailedRecord($strDirDownloadURL, serialize($arrRecord));
				}
			}
		}
	}
	else if($strDownloadType == 'NIGHTLY')
	{
		while($objFile->EOF != true && $objFile->Error != true)
		{
			
			$arrRecordSet							=	$objFile->parseToRecordset();

			foreach($arrRecordSet as $arrRecord)
			{
				$strSQL				=	'SELECT ' . $strUniqueKey . ' FROM ' . $strTable . ' WHERE ' . $strUniqueKey . '=\'' . $arrRecord[$strUniqueKey] . '\'';
				$rsCheckDuplicate	=	$medDB->GetRow($strSQL);
				
				if(count($rsCheckDuplicate) > 0)
				{
					$medDB->Execute('DELETE FROM ' . $strTable . ' WHERE ' . $strUniqueKey . '=\'' . $arrRecord[$strUniqueKey] . '\'');
					$intUpdateCount 	+= 	$medDB->Affected_Rows();
				}
				
				$arrRecord['mt_log_id']				=	$intLogID;
				$intLineCount						+=	1;
				
				$medDB->AutoExecute($strTable, $arrRecord, 'INSERT');
				$intAffectedRows					=	$medDB->Affected_Rows();
				if($intAffectedRows > 0)
				{
					$intRecordCount 				+= 	$medDB->Affected_Rows();
				}
				else
				{
					logFailedRecord($strDirDownloadURL, serialize($arrRecord));
				}
			}
		}
	}

	$arrDirectoryDownloadLog					=	array();
	$arrDirectoryDownloadLog['end_time']		=	date('Y-m-d H:i:s');
	$arrDirectoryDownloadLog['total_lines']		=	$intLineCount;
	$arrDirectoryDownloadLog['total_records']	=	$intRecordCount;
	$arrDirectoryDownloadLog['total_updates']	=	$intUpdateCount;
	
	
	$medDB->AutoExecute(DIRECTORY_DOWNLOAD_LOG,$arrDirectoryDownloadLog,'UPDATE',"mt_log_id='".$intLogID."'");	
	
	
	function logFailedRecord($strFile, $strRawLine)
	{
		$strFile = DIR_TO_EXTRACT . "failed/" . $strFile . ".err.txt";
		$strContent = "";
		if(file_exists($strFile) === true)
		{
			$strContent = file_get_contents($strFile) . "\r\n";			
		}
		$strContent .= $strRawLine . "\r\n";
		file_put_contents($strFile, $strContent);
	}
?>