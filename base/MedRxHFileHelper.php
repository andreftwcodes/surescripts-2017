<?php


class MedRxHFileHelper
{
	
    
    public static function logProcessedFile($File, $FileType)
    {
		global $medDB, $pdoDB;
		$Result = $Record = array();
		
		$Record['file_type'] = $FileType;
		$Record['actual_file_name'] = basename($File); 
		$Record['file_md5'] = md5_file($File); 
		
		$Result['detail_data_filename'] = $Record['detail_file_path'] = MedRxHFileHelper::saveDetailFile($File, $FileType);

		
		
		$pdoDB->insert("rxh_file_list", $Record);
		return $Result;
    }

    
    public static function isFileProcessed($File)
    {
		global $pdoDB;
		
		$FileName = basename($File);
		
		$FileMD5 = md5_file($File);

		$Sql = "SELECT COUNT(0) as CNT FROM rxh_file_list WHERE actual_file_name = '" . $FileName . "' AND file_md5 = '" . $FileMD5 . "'";

		
		
		
		$Result = $pdoDB->select("rxh_file_list", "actual_file_name = '" . $FileName . "' AND file_md5 = '" . $FileMD5 . "'", '', "COUNT(0) as CNT");
		
		return (boolean)$Result[0]['CNT'];
    }

    
    private static function backupFile($File, $FileType)
    {
		$BackupFilePath = RXHUB_WEBDAV_BAKCUP_DIR . date('Y') . '/' . date('m'). '/' . date('d') . '/' . $FileType . '/' . basename($File) . '.7z';

		
		
		return $BackupFilePath;
    }

    
    private static function saveDetailFile($File, $FileType)
    {
	    
		$Path = RXHUB_WEBDAV_IMS_FILE_DIR . date('Y') . '/' . date('m'). '/' . date('d') . '/' . $FileType . '/';
		
		if(is_dir($Path) === false)
		{
			mkdir($Path, 0777, true);
		}
		$DetailFilePath = $Path . basename($File);
		copy($File, $DetailFilePath); 
		MedRxHFileHelper::removeLastLineFromFile($DetailFilePath, 2); 
		
		
		
		
		
		return $DetailFilePath;
    }

    private static function removeFirstLineFromFile($File, $LinesToRemove)
    {
		
		

		
    }

    
    private static function removeLastLineFromFile($File, $LinesToRemove)
    {
		$Handle = @fopen($File, "a+");

		$LinesFound = array();
		if($Handle)
		{
			$Chunk = 1000;
			$Data = '';
			$FileSize = sprintf("%u", filesize($File));
			$MaxLength = filesize($File);
			if(intval($FileSize) == PHP_INT_MAX)
			{
				$FileSize = PHP_INT_MAX;
			}

			for($Length = 0; $Length < $MaxLength; $Length += $Chunk) 
			{
				if( ($MaxLength - $Length) > $Chunk)
				{
					$SeekSize = $Chunk;
				}
				else
				{
					$SeekSize = $MaxLength - $Length;
				}

				fseek($Handle, ($Length + $SeekSize) * -1, SEEK_END);
				$Data = fread($Handle, $SeekSize) . $Data;
				
				for($i = $Chunk; $i > 0; $i--)
				{
					if(@$Data[$i-1] == "\n")
					{
						$LinesFound[] = $i;
					}
					if(count($LinesFound) == $LinesToRemove+1)
					{
						$GoTo = $Chunk - $i;
						ftruncate($Handle, ($MaxLength) - ($SeekSize - $LinesFound[$LinesToRemove]));
						return true;
					}
				}
				fclose($Handle);
			}
			return false;
		}
    }


    
    private static function removeLastLineFromFileX($File)
    {
		

		$filename = $File;
		
		$file_handle = fopen($filename, 'r');

		
		$linebreak  = false;
		$file_start = false;

		
		$bite = 50;

		
		$filesize = filesize($filename);

		
		fseek($file_handle, 0, SEEK_END);

		while ($linebreak === false && $file_start === false) {
			
			$pos = ftell($file_handle);

			if ($pos < $bite) {
			
			   rewind($file_handle);
			} else {
			   
			   fseek($file_handle, -$bite, SEEK_CUR);
			}

			
			$string = fread($file_handle, $bite) or die ("Can't read from file " . $filename . ".");

			
			if ($pos + $bite >= $filesize) {
			   $string = substr_replace($string, '', -1);
			}

			
			if ($pos < $bite) {
			
			   rewind($file_handle);
			} else {
			   
			   fseek($file_handle, -$bite, SEEK_CUR);
			}

			
			if (is_integer($lb = strrpos($string, "\n"))) {
			   
			   $linebreak = true;
			   
			   $line_end = ftell($file_handle) + $lb + 1;
			}

			if  (ftell($file_handle) == 0) {
			  
			  $file_start = true;
			}
		}

		if ($linebreak === true) {
			
			rewind($file_handle);
			$file_minus_lastline = fread($file_handle, $line_end);

			
			fclose($file_handle);

			
			$file_handle = fopen($filename, 'w+');
			fputs($file_handle, $file_minus_lastline);
			fclose($file_handle);
		} else {
			
			fclose($file_handle);
		}

    }
}
?>
