<?PHP


    
    set_time_limit(0);

    
    include('med_config.php');

    
    include_once(WEB_ROOT.'base/MedCommon.php');

    
    include_once(WEB_ROOT.'base/DB.php');

    
    include_once(WEB_ROOT.'base/MedRequest.php');

    
    include_once(WEB_ROOT.'base/MedOutMessage.php');

    
    include_once(WEB_ROOT.'base/MedXmlParser.php');

    
    include_once(WEB_ROOT.'base/MedServiceLevel.php');

    
    if( isset($_GET['TYPE']) == false && isset($_GET['DOWNLOAD']) == false )
    {
	
	$strDirectoryType = $argv[1];

	
	$strDownloadType = $argv[2];
    }
    else
    {
	
	$strDirectoryType = $_GET['TYPE'];

	
	$strDownloadType = $_GET['DOWNLOAD'];
    }

    //die('here');
    $strURL = DIR_URL;

    if($strDownloadType != 'NIGHTLY' && $strDownloadType != 'FULL')
    {
	echo '<h3 style="color:red;font-family:tahoma">Inavalid / no Download Type is not specified. [NIGHTLY/FULL]</h3>';
	exit;
    }

 
    if($strDirectoryType == 'PHARMACY')
    {
	
	$arrConfig = $cfgPharmacy;

	
	$strTable = PHARMACY_MASTER_TABLE;

	
	$strUniqueKey = 'ncpdpid';

	
	$strTaxonomyCode = '183500000X';
    }

    
    else if($strDirectoryType == 'PRESCRIBER')
    {
	
	$arrConfig = $cfgPrescriber;

	
	$strTable = PRESCRIBER_MASTER_TABLE;

	
	$strUniqueKey = 'spi';

	
	$strTaxonomyCode = '193200000X';
    }
    else
    {
	echo '<h3 style="color:red;font-family:tahoma">Invalid / no Directory Type is not specified. [PHARMACY|PRESCRIBER]</h3>';
	exit;
    }
    

    

    
    $objOutMessage = new MedOutMessage('DIRECTORY_DOWNLOAD_'.$strDownloadType);

    $objOutMessage->To = DIR_REQUEST_TO;
    $objOutMessage->From = DATA_PROVIDER_ID;
    $objOutMessage->SentTime = getUTCTime();
    $objOutMessage->Created = $objOutMessage->SentTime;
    /*$objOutMessage->Username = DIR_LOGIN_ID;
    $objOutMessage->Password = DIR_PASSWORD;*/
    $objOutMessage->AccountID = PARTNER_ACCOUNT_ID;
    $objOutMessage->VersionID = DIR_VERSION;
    //$objOutMessage->TaxonomyCode = $strTaxonomyCode;
    // $objOutMessage->DirectoryDate = gmdate('Ymd');
    $objOutMessage->DirectoryDate = date('c');
	$objOutMessage->VendorName = VENDOR_NAME;
	$objOutMessage->ProductName = APP_NAME;
	$objOutMessage->SoftwareVersion = APP_VERSION;

	if($strDirectoryType == 'PRESCRIBER')
	{
		$objOutMessage->DownloadType = 'ProviderLocation';
	}
	else
	{
		$objOutMessage->DownloadType = 'Organization';
	}
	
    $SentTime = getUTCTime();
    $CreatedTime = $SentTime;
    $strRequest = $objOutMessage->getMessage();
	//echo $strRequest;die;
    $strAuth = DIR_LOGIN_ID.':'.DIR_PASSWORD;

	
    $objMedRequest = new MedRequest($strURL . '?id='.$objOutMessage->MessageID);
    $strRequest = STR_REPLACE("\n","",$strRequest);
    //$objMedRequest->addHeader("Authorization: Basic ".base64_encode($strAuth));
    $objMedRequest->addHeader("Content-Type: application/xml; charset=UTF-16");
    $objMedRequest->addHeader("Content-length: ".(strlen($strRequest)));
    $strResponse = $objMedRequest->Post($strRequest);

    
    $objOutMessage->updateOutMessageResponse($strResponse,'');

    

    

    
    if(MedXmlParser::validateXml($strResponse) == 1)
    {
	
	$objXml = new MedXmlParser($strResponse);

	$strDirDownloadURL = $objXml->URL->Value;

	if($strDirDownloadURL != '')
	{
	    
	    copySecureFile(DIR_DOWNLOAD_URL.$strDirDownloadURL,DIR_TO_DOWNLOAD.$strDirDownloadURL);

	    

	    
	    if(file_exists(DIR_TO_DOWNLOAD.$strDirDownloadURL))
	    {
			$zaDirectory = new ZipArchive();
			if($zaDirectory->open(DIR_TO_DOWNLOAD.$strDirDownloadURL) === TRUE)
			{
				$zaDirectory->extractTo(DIR_TO_EXTRACT);
				$zaDirectory->close();
			}
			else
			{
				echo 'Error reading zip file.';
				exit;
			}
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

    

    
    $strFile = DIR_TO_EXTRACT . str_replace(array('zip'),array('txt'),$strDirDownloadURL);
    

    
    $startTime = date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);

    
    $arrDirectoryDownloadLog = array();
    $arrDirectoryDownloadLog['directory_type'] = $strDirectoryType;
    $arrDirectoryDownloadLog['download_type'] = $strDownloadType;
    $arrDirectoryDownloadLog['file_name'] = $strDirDownloadURL;
    $arrDirectoryDownloadLog['start_time'] = $startTime;
    $medDB->AutoExecute(DIRECTORY_DOWNLOAD_LOG, $arrDirectoryDownloadLog , 'INSERT');
    $intLogID = $medDB->Insert_ID();

    

    
    $strNewFileName = str_replace(array('zip'),array('dat'),$strDirDownloadURL);

    
    $strSurescriptFileHandle = fopen($strFile,"r");

    
    $strNewFileHandle = fopen(DIR_TO_EXTRACT.$strNewFileName,"wb");

    
    $intRecordCount = 0;
    $intLineCount = 0;
    $intUpdateCount = 0;

    

    
    if($strDownloadType == 'FULL')
    {
	
	while(!feof($strSurescriptFileHandle))
	{
	    
	    $strNewLineData = fgets($strSurescriptFileHandle,4096);

	    
	    $intColumn = 0;
	    $arrNewLine = array();

		$field_value_arr = explode('|', $strNewLineData);

	    foreach($arrConfig as $intKey => $arr)
	    {
		
		// $strField = trim(substr($strNewLineData,$intColumn,$arr['LOC']));

		
		$strFieldName = $arr['FIELD'];


		$arrNewLine[$strFieldName] = str_replace(array('\\F\\', '\\R\\', '\\S\\'), array('|', '~', '^'), $field_value_arr[$intKey]);
		
		$intColumn += $arr['LOC'];

		if($arr['FIELD'] == 'phone_alt_numbers') {

			$arrNewLine['phone_alt1_qualifier'] = $arrNewLine['phone_alt1'] = $arrNewLine['phone_alt2_qualifier'] = $arrNewLine['phone_alt2'] = $arrNewLine['phone_alt3_qualifier'] = $arrNewLine['phone_alt3'] = $arrNewLine['phone_alt4_qualifier'] = $arrNewLine['phone_alt4'] = $arrNewLine['phone_alt5_qualifier'] = $arrNewLine['phone_alt5'] = $arrNewLine['phone_alt6_qualifier'] = $arrNewLine['phone_alt6'] = '';

			$alt_numbers = explode('~', $field_value_arr[$intKey]);

			if(!empty($alt_numbers[0])) { $i = 1;

				foreach ($alt_numbers as $alt_number) {

					$numbers = explode('^', $alt_number);
					$arrNewLine['phone_alt'.$i.'_qualifier'] = !empty($numbers[0]) ? $numbers[0] : '';
					$arrNewLine['phone_alt'.$i] = !empty($numbers[1]) ? $numbers[1] : '';
					$i++;
				}
			}
		    unset($arrNewLine['phone_alt_numbers']);
		}

		if($arr['FIELD'] == 'service_level')
		{
		     
		    $objServiceLevel = new ServiceLevel();
		    // $arrNewLine['service_level_bits'] = $objServiceLevel->getServiceLevelBits();
		    // unset($objServiceLevel);
			
			$service_levels_arr = $objServiceLevel->ServiceLevels();
			$service_levels = explode('~', $field_value_arr[$intKey]);

			$service_level = 0; $bits = '';
			if(!empty($service_levels[0])) {

				foreach ($service_levels as $level) {

					if(array_key_exists($level, $service_levels_arr)) {

						$bits .= $service_levels_arr[$level].',';
						$service_level += (pow(2, $service_levels_arr[$level]));
					}
				}

				$arrNewLine['service_level_bits'] = rtrim($bits, ',');
				$arrNewLine['service_level'] = $service_level;
			} else {

				$arrNewLine['service_level_bits'] = '';
				$arrNewLine['service_level'] = '';
			}

		    $arrNewLine['text_service_level'] = $field_value_arr[$intKey];
		}
	    }


	    if(array_filter($arrNewLine))
	    {
		
		$arrNewLine['last_updated_from_surescript'] = date("Y-m-d H:i:s");
		$arrNewLine['mt_log_id'] = $intLogID;

		
		$arrFieldNames = array_keys($arrNewLine);

		
		$strNewLine = @implode("||~||",$arrNewLine);

		if($strNewLine != "")
		{
		    
		    fwrite($strNewFileHandle,$strNewLine."\n");

		    
		    $intRecordCount += 1;
		}
	    }

	    
	    $intLineCount += 1;
	}
    }
    

    
    else if($strDownloadType == 'NIGHTLY')
    {
	
	while(!feof($strSurescriptFileHandle))
	{
	    
	    $strNewLineData = fgets($strSurescriptFileHandle,4096);

	    
	    $intColumn = 0;
	    $arrNewLine = array();

		$field_value_arr = explode('|', $strNewLineData);

	    foreach($arrConfig as $intKey => $arr)
	    {
		
		// $strField = trim(substr($strNewLineData,$intColumn,$arr['LOC']));

		
		$strFieldName = $arr['FIELD'];


		$arrNewLine[$strFieldName] = str_replace(array('\\F\\', '\\R\\', '\\S\\'), array('|', '~', '^'), $field_value_arr[$intKey]);

		 
		$intColumn += $arr['LOC'];

		if($arr['FIELD'] == 'phone_alt_numbers') {

			$arrNewLine['phone_alt1_qualifier'] = $arrNewLine['phone_alt1'] = $arrNewLine['phone_alt2_qualifier'] = $arrNewLine['phone_alt2'] = $arrNewLine['phone_alt3_qualifier'] = $arrNewLine['phone_alt3'] = $arrNewLine['phone_alt4_qualifier'] = $arrNewLine['phone_alt4'] = $arrNewLine['phone_alt5_qualifier'] = $arrNewLine['phone_alt5'] = $arrNewLine['phone_alt6_qualifier'] = $arrNewLine['phone_alt6'] = '';

			$alt_numbers = explode('~', $field_value_arr[$intKey]);

			if(!empty($alt_numbers[0])) { $i = 1;

				foreach ($alt_numbers as $alt_number) {

					$numbers = explode('^', $alt_number);
					$arrNewLine['phone_alt'.$i.'_qualifier'] = !empty($numbers[0]) ? $numbers[0] : '';
					$arrNewLine['phone_alt'.$i] = !empty($numbers[1]) ? $numbers[1] : '';
					$i++;
				}
			}
		    unset($arrNewLine['phone_alt_numbers']);
		}

		if($arr['FIELD'] == 'service_level')
		{
		     
		    $objServiceLevel = new ServiceLevel();
		    // $arrNewLine['service_level_bits'] = $objServiceLevel->getServiceLevelBits();
		    // unset($objServiceLevel);
			
			$service_levels_arr = $objServiceLevel->ServiceLevels();
			$service_levels = explode('~', $field_value_arr[$intKey]);

			$service_level = 0; $bits = '';
			if(!empty($service_levels[0])) {

				foreach ($service_levels as $level) {

					if(array_key_exists($level, $service_levels_arr)) {

						$bits .= $service_levels_arr[$level].',';
						$service_level += (pow(2, $service_levels_arr[$level]));
					}
				}

				$arrNewLine['service_level_bits'] = rtrim($bits, ',');
				$arrNewLine['service_level'] = $service_level;
			} else {

				$arrNewLine['service_level_bits'] = '';
				$arrNewLine['service_level'] = '';
			}

		    $arrNewLine['text_service_level'] = $field_value_arr[$intKey];
		}
		
		if($arr['FIELD'] == $strUniqueKey)
		{
		    $strUniqueKeyValue = $field_value_arr[$intKey];
		}

	    }

	    
	    if(array_filter($arrNewLine))
	    {
		
		$arrNewLine['last_updated_from_surescript'] = date("Y-m-d H:i:s");
		$arrNewLine['mt_log_id'] = $intLogID;

		
		$arrFieldNames = array_keys($arrNewLine);

		
		$strNewLine = @implode("||~||",$arrNewLine);

		if($strNewLine != "")
		{
		    
		    $strSQL = "SELECT " . $strUniqueKey . " FROM " . $strTable . " WHERE ".$strUniqueKey." = '".$strUniqueKeyValue."' ";
		    $rsCheckDuplicate = $medDB->GetRow($strSQL);

		    
		    if(count($rsCheckDuplicate) > 0)
		    {
			$medDB->Execute("DELETE FROM " . $strTable . " WHERE ".$strUniqueKey." = '".$strUniqueKeyValue."' ");
			$intUpdateCount += 1;
		    }

		    
		    fwrite($strNewFileHandle,$strNewLine."\n");

		     
		    $intRecordCount += 1;
		}
	    }

	    
	    $intLineCount += 1;

	}
    }
    

    
    fclose($strSurescriptFileHandle);
    fclose($strNewFileHandle);

    

    
    if(file_exists(DIR_TO_EXTRACT.$strNewFileName) && count($arrFieldNames) > 0)
    {

	if($strDownloadType == 'FULL')
	{
	    
	    $medDB->Execute('TRUNCATE TABLE '. $strTable);
	}
	$medDB->Execute("LOAD DATA LOCAL INFILE '".DIR_TO_EXTRACT.$strNewFileName."' 
		    INTO TABLE ".$strTable." FIELDS TERMINATED BY '||~||' ENCLOSED BY '' ESCAPED BY '\b' (".@implode(",",$arrFieldNames).") ;");
    }
    

    
    $arrDirectoryDownloadLog = array();
    $arrDirectoryDownloadLog['end_time'] = date('Y-m-d H:i:s');
    $arrDirectoryDownloadLog['total_lines'] = $intLineCount;
    $arrDirectoryDownloadLog['total_records'] = $intRecordCount;
    $arrDirectoryDownloadLog['total_updates'] = $intUpdateCount;

    
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

    
    unlink($strFile);
    unlink(DIR_TO_EXTRACT.$strNewFileName);
    

    

?>