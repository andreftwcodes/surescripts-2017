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
	
	
	if( isset($_GET['TYPE']) == false && isset($_GET['DOWNLOAD']) == false )
	{
		
		$strDirectoryType							=	$argv[1];
		
		$strDownloadType							=	$argv[2];
	}
	else
	{
		
		$strDirectoryType							=	$_GET['TYPE'];
		
		$strDownloadType							=	$_GET['DOWNLOAD'];
	}
	
	
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
	
	$objOutMessage->To				=	'SSDR44';
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
	
	$strRequest = STR_REPLACE("\n","",$strRequest);
	$objMedRequest->addHeader("Authorization: Basic ".base64_encode($strAuth));
	$objMedRequest->addHeader("Content-Type: application/xml; charset=UTF-16");
	$objMedRequest->addHeader("Content-length: ".(strlen($strRequest)));
	$strResponse					=	$objMedRequest->Post($strRequest);
	$objOutMessage->updateOutMessageResponse($strResponse,'');

	

	if(MedXmlParser::validateXml($strResponse) == 1)
	{
		
		$objXml							=	new MedXmlParser($strResponse);
		$strDirDownloadURL				=	$objXml->URL->Value;

		if($strDirDownloadURL != '')
		{
			
			copySecureFile(DIR_DOWNLOAD_URL.$strDirDownloadURL,DIR_TO_DOWNLOAD.$strDirDownloadURL);

			
			if(file_exists(DIR_TO_DOWNLOAD.$strDirDownloadURL))
			{
				$zaDirectory	=	new ZipArchive();
				
		    	$zaDirectory->open(DIR_TO_DOWNLOAD.$strDirDownloadURL);
		    	$zaDirectory->extractTo(DIR_TO_EXTRACT);
				$zaDirectory->close();
			}
			else
			{
				echo 'File does not exists.';
				exit;
			}
		}
		else
		{
			echo 'No Directory download available.';
			exit;
		}
	}
	
	
	$strFile									=	DIR_TO_EXTRACT . str_replace(array('zip'),array('txt'),$strDirDownloadURL);
	
	
	
	$startTime									=	date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
	
	
	$arrDirectoryDownloadLog					=	array();
	$arrDirectoryDownloadLog['directory_type']	=	$strDirectoryType;
	$arrDirectoryDownloadLog['download_type']	=	$strDownloadType;
	
	$arrDirectoryDownloadLog['file_name']		=	$strDirDownloadURL;
	$arrDirectoryDownloadLog['start_time']		=	$startTime;
	$medDB->AutoExecute(DIRECTORY_DOWNLOAD_LOG, $arrDirectoryDownloadLog , 'INSERT');
	$intLogID									=	$medDB->Insert_ID();
	
	$objFile							=	new FlatFile($strFile,$arrConfig,100);

	
	$intRecordCount								=	0;
	$intLineCount								=	0;
	$intUpdateCount								=	0;
	if($strDownloadType == 'FULL')
	{
		$medDB->Execute('TRUNCATE TABLE '. $strTable);
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
				
				
				$arrRecord['last_updated_from_surescript'] = date("Y-m-d H:i:s");
				
				
				$intRecordCount += 	$medDB->AutoExecute($strTable, $arrRecord, 'INSERT');
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
					
					$intUpdateCount		+=	1;

				}
				
				$arrRecord['mt_log_id']				=	$intLogID;
				$intLineCount						+=	1;
				
				
				$arrRecord['last_updated_from_surescript'] = date("Y-m-d H:i:s");
				
				
				$intRecordCount 					+= 	$medDB->AutoExecute($strTable, $arrRecord, 'INSERT');
			}
		}
	}

	$arrDirectoryDownloadLog					=	array();
	$arrDirectoryDownloadLog['end_time']		=	date('Y-m-d H:i:s');
	$arrDirectoryDownloadLog['total_lines']		=	$intLineCount;
	$arrDirectoryDownloadLog['total_records']	=	$intRecordCount;
	$arrDirectoryDownloadLog['total_updates']	=	$intUpdateCount;
	
	
	$medDB->AutoExecute(DIRECTORY_DOWNLOAD_LOG,$arrDirectoryDownloadLog,'UPDATE',"mt_log_id='".$intLogID."'");	
	
	if($strDirectoryType == 'PHARMACY')
	{
	    
	    
	    $medDB->Execute('UPDATE pharmacy_master SET service_level = 3 WHERE service_level > 3');
	}
	
	
	
	
	
	
	$strZipDirectoryPath = DIR_TO_EXTRACT."zip/";
	
	if ($strHandle = opendir($strZipDirectoryPath))
	{
	    
	    while (false !== ($strFileName = readdir($strHandle))) 
	    {
		if(filemtime($strZipDirectoryPath.$strFileName) <= time()-691200 ) 
		{
		   unlink($strZipDirectoryPath.$strFileName);
		}
	    }
	    closedir($strHandle);
	}
	
	
	$objFile->__destruct();
	
	
	unlink($strFile);
	
	

?>