<?php
	
	date_default_timezone_set('UTC');

	
	function getUTCTime($Timestamp='')
	{
		if($Timestamp != '')
		{
			return date('Y-m-d\TH:i:s.3\Z',$Timestamp);
		}
		else
		{
			return gmdate('Y-m-d\TH:i:s.3\Z');		
		}
	}
	
	
	function getUTCTimeFormat($strDate='')
	{
	    if($strDate != '')
	    {
		
		$objDate = date_create($strDate);
		return date_format($objDate, 'Y-m-d\TH:i:s.3\Z');
	    }
	    else
	    {
		return gmdate('Y-m-d\TH:i:s.3\Z');		
	    }
	}
	
	
	function convertToEDIFactTimeFormat($Timestamp)
	{
		return date('Ymd:His',$Timestamp);
	}
	
	function convertToMySqlDateTime($Time, $IsTimestamp = FALSE)
	{
		
		if($IsTimestamp === FALSE)
		{
			
			if( strpos($Time, ".") !== false)
			{
				$Time = explode(".", $Time);
				$Time = $Time[0];
			}
			$DateTime = new DateTime($Time);
			$Output = $DateTime->format('Y-m-d H:i:s');
		}
		else
		{
			$Output = date('Y-m-d H:i:s', $Time);
		}
		return $Output;
	}
	
	
	
	function extractZip( $zipFile = '', $dirFromZip = '' )
	{   
	    $zip = zip_open($zipFile);
	
	    if ($zip)
	    {
	        while ($zip_entry = zip_read($zip))
	        {
	            $completePath = $zipDir . dirname(zip_entry_name($zip_entry));
	            $completeName = $zipDir . zip_entry_name($zip_entry);
	           
	            
	            
	            if(!file_exists($completePath) && preg_match( '#^' . $dirFromZip .'.*#', dirname(zip_entry_name($zip_entry)) ) )
	            {
	                $tmp = '';
	                foreach(explode('/',$completePath) AS $k)
	                {
	                    $tmp .= $k.'/';
	                    if(!file_exists($tmp) )
	                    {
	                        @mkdir($tmp, 0777);
	                    }
	                }
	            }
	           
	            if (zip_entry_open($zip, $zip_entry, "r"))
	            {
	                if( preg_match( '#^' . $dirFromZip .'.*#', dirname(zip_entry_name($zip_entry)) ) )
	                {
	                    if ($fd = @fopen($completeName, 'w+'))
	                    {
	                        fwrite($fd, zip_entry_read($zip_entry, zip_entry_filesize($zip_entry)));
	                        fclose($fd);
	                    }
	                    else
	                    {
	                        
	                        mkdir($completeName, 0777);
	                    }
	                    zip_entry_close($zip_entry);
	                }
	            }
	        }
	        zip_close($zip);
	    }
	    return true;
	}
	
	
	function copySecureFile($FromLocation,$ToLocation)
	{
		$Channel = curl_init($FromLocation);
		$File = fopen ($ToLocation, "w");
		curl_setopt($Channel, CURLOPT_FILE, $File);
		curl_setopt($Channel, CURLOPT_HEADER, 0);
		curl_setopt($Channel, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($Channel, CURLOPT_SSL_VERIFYHOST, 0);
		curl_exec($Channel);
		curl_close($Channel);
		fclose($File);
		return file_exists($ToLocation);
	}
	
	
	function archiveInboxMessageToFileSystem($MessageID, $Message, $To)
	{
		if($Message != '')
		{
			
			$StoragePath = date('Y') . '/' . date('m'). '/' . date('d') . '/';
			
			
			
			
			if($To != '')
			{
				$StoragePath .= $To . '/';
			}

			
			if(!is_dir(INBOX_BACKUP_DIR . $StoragePath))
			{
				mkdir(INBOX_BACKUP_DIR . $StoragePath, 0777, true);
			}
			
			
			file_put_contents(INBOX_BACKUP_DIR . $StoragePath . '/' . $MessageID, $Message);
		}
	}
	
	
	function extractIDFromMailAddress($strMailAddress)
	{
		$arrMailAddress	=	explode(':',$strMailAddress);
		$arrMailAddress	=	explode('.',$arrMailAddress[1]);
		return $arrMailAddress[0]; 
	}
	
	
	function getEntityTypeFromMailAddress($strMailAddress)
	{
		$strMailAddress = strtoupper($strMailAddress);
		$EntityType = 'DP';
		if(strpos($strMailAddress,'NCPDP@') !== FALSE)
		{
			$EntityType = 'PHARMACY';
		}
		else if(strpos($strMailAddress,'SPI@') !== FALSE)
		{
			$EntityType = 'PRESCRIBER';
		}
		else
		{
			$EntityType = 'DP';
		}
		return $EntityType;
	}

	
	function getDelimitersFromEDIFactMessage($EDIFactMessage)
	{
		$intUNAPosition = strpos($EDIFactMessage, 'UNA');
		$intUIBPosition = strpos($EDIFactMessage, 'UIB');
		if($intUNAPosition !== FALSE && $intUIBPosition !== FALSE)
		{
			$EDIFactMessage = str_replace(array('UNA'), array(''), $EDIFactMessage);
			$arrEDIFactMessage = explode('UIB',$EDIFactMessage);
			$UNA = $arrEDIFactMessage[0];
			if(strlen($UNA) != 6)
			{
				return FALSE;
			}
			else
			{
				$UNA = str_split($UNA);
				return $UNA;
			}
		}
		else
		{
			return FALSE;	
		}
	}
	function getClientIP()
    {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) 
        {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } 
        elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) 
        {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } 
        else 
        {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
    function getServerTimestamp()
    {
        return date('Y-m-d H:i:s');
    }