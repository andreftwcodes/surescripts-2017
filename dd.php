<?php


    $strSurescriptFileHandle = fopen('C:\directory_downloads\test.txt',"r");

    
	while(!feof($strSurescriptFileHandle))
	{
	    
	    $strNewLineData = fgets($strSurescriptFileHandle,4096);

	    
	    $intColumn = 0;
	    $arrNewLine = array();

		$field_value_arr = explode('|', $strNewLineData);
echo '<pre>';
print_r($arrConfig);
echo '</pre>';die;
	    foreach($arrConfig as $intKey => $arr)
	    {
		
		// $strField = trim(substr($strNewLineData,$intColumn,$arr['LOC']));

		
		$strFieldName = $arr['FIELD'];

		
		$arrNewLine[$strFieldName] = utf8_encode($field_value_arr[$intKey]);
// echo '<pre>';
// print_r($strFieldName);
// print_r($field_value_arr[$intKey]);
// echo '</pre>';die;
		
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
					$bits .= $service_levels_arr[$level].',';
					$service_level += (pow(2, $service_levels_arr[$level]));
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

echo '<pre>';
print_r($arrNewLine);
echo '</pre>';die;
	    if(array_filter($arrNewLine))
	    {
		
		$arrNewLine['last_updated_from_surescript'] = date("Y-m-d H:i:s");
		$arrNewLine['mt_log_id'] = $intLogID;

		
		$arrFieldNames = array_keys($arrNewLine);

		
		$strNewLine = @implode("||~||",$arrNewLine);

		echo '<pre>';
		print_r($arrNewLine);
		echo '</pre>';die;
	    if($arrNewLine['spi'] == '6209382913001') {
			echo '<pre>';
			print_r($arrNewLine);
			print_r($strNewLine);
			echo '</pre>';die;
	    }
	    
		if($strNewLine != "")
		{
		    
		    fwrite($strNewFileHandle,$strNewLine."\n");

		    
		    $intRecordCount += 1;
		}
	    }

	    
	    $intLineCount += 1;
	}
?>