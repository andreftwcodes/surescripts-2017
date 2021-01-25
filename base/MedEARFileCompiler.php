<?php



include WEB_ROOT.'base/MedSimpleFile.php';

class MedEARFileCompiler
{
    
    
    private $FileName;

    
    private $FilePath;
    
    
    private $Handle;
    
    
    private $LineCount;
    
    
    public function __construct()
    {
        $this->LineCount = 0;
    }

    
    public static function getFileHeader($HeaderRecordID, $FileType = "T")
    {
        $Header = "HDR" . EAR_COL_SEP .                             
                "10" . EAR_COL_SEP .                                
                RXHUB_PARTICIPANT_ID . EAR_COL_SEP .                
                RXHUB_PARTICIPANT_PASSWORD . EAR_COL_SEP .          
                "RXHUB" . EAR_COL_SEP .                             
                "IMS" .  EAR_COL_SEP .                              
                str_pad($HeaderRecordID, 10, "0", STR_PAD_LEFT) . EAR_COL_SEP .                     
                date("Ymd") . EAR_COL_SEP .                         
                substr(date("Hisd"), 0, 9) . EAR_COL_SEP .          
                "EAR" . EAR_COL_SEP .                               
                "" . EAR_COL_SEP .                                  
                date("Ymd") . EAR_COL_SEP .                         
                $FileType;
        return $Header;
    }

    
    public static function getReportHeader($StartDate, $EndDate, $TransmissionAction)
    {
        $Header = "RHD" . EAR_COL_SEP .
                $StartDate . EAR_COL_SEP .
                $EndDate . EAR_COL_SEP .
                $TransmissionAction;
        return $Header;
    }

    
    public static function getARALine($Record)
    {
        $Line = "ARA" . EAR_COL_SEP .								
                RXHUB_PARTICIPANT_ID . EAR_COL_SEP .				
                "RXHUB" . EAR_COL_SEP .								
                $Record['ara_rx_dea'] . EAR_COL_SEP .				
                $Record['ara_rx_npi'] . EAR_COL_SEP .				
                $Record['ara_rx_state_license_no'] . EAR_COL_SEP .  
                $Record['ara_rx_state'] . EAR_COL_SEP .				
                $Record['ara_zipcode'] . EAR_COL_SEP .				
                $Record['ara_rx_confidential_id'] . EAR_COL_SEP .   
                str_replace( array("-"), array(""), $Record['ara_rx_date']) . EAR_COL_SEP .    
                $Record['ara_e_rx_count'] . EAR_COL_SEP .           
                $Record['ara_fax_rx_count'] . EAR_COL_SEP .         
                $Record['ara_print_rx_count'] . EAR_COL_SEP .       
                "0";            
        return $Line;
    }
	
	
	public static function getARDPrefix($Record)
	{
		$LinePrefix = "ARD" . EAR_COL_SEP .							
                RXHUB_PARTICIPANT_ID . EAR_COL_SEP .				
                "RXHUB" . EAR_COL_SEP .								
                $Record['ara_rx_dea'] . EAR_COL_SEP .				
                $Record['ara_rx_npi'] . EAR_COL_SEP .				
                $Record['ara_rx_state_license_no'] . EAR_COL_SEP .  
                $Record['ara_rx_state'] . EAR_COL_SEP .				
                $Record['ara_zipcode'] . EAR_COL_SEP .				
                $Record['ara_rx_confidential_id'] . EAR_COL_SEP;
		return $LinePrefix;
	}

    
    public function getReportTrailer()
    {
        $Line = "RTR" . EAR_COL_SEP . ($this->getLineCount()-2);
        return $Line;
    }

    
    public function getFileTrailer()
    {
        $Line = "TRL" . EAR_COL_SEP . ($this->getLineCount()-1);
        return $Line;
    }

    
    private function generateFileName($Extension = ".rpt")
    {
        if(isset($this->FileName) === false)
        {
            $this->FileName = "EAR_" . RXHUB_PARTICIPANT_ID . '_' . date("Ymd") . '_' . date("His") . $Extension;
        }
        return $this->FileName;
    }

    
    public function getFileName()
    {
        return $this->FileName;
    }

    
    public function getFilePath()
    {
        return $this->FilePath;
    }

    
    public function initReport()
    {
        
        $Path = RXHUB_EAR_SENT . date('Y') . '/' . date('m'). '/' . date('d') . '/';
        if(is_dir($Path) === false)
        {
            mkdir($Path, 0777, true);
        }

        
        $this->FilePath = $Path . $this->generateFileName();
        $this->Handle = fopen($this->FilePath, 'a');
    }

    
    public function write($Line)
    {
	
        $this->LineCount += 1;
        $Line = trim($Line) . "\n";
        return fwrite($this->Handle, $Line);
    }

    
    public function getLineCount()
    {
        return $this->LineCount;
    }
    
    
    public function uploadFile()
    {
	
	$UploadScript = file_get_contents( UTILITY_BASE_DIR . 'winscp/UPLOAD_TPL.bat' );
	$Search = array("<<FILE_TO_UPLOAD>>", "<<UTILITY_BASE_DIR>>", "<<WINSCP_UTILITY_EXE>>");
	$WindowsFilePath = str_replace("/", "\\", $this->FilePath);
	$Replace = array($WindowsFilePath, UTILITY_BASE_DIR, WINSCP_UTILITY_EXE);
	$UploadScript = str_replace($Search, $Replace, $UploadScript);
	
	
	file_put_contents( UTILITY_BASE_DIR . 'winscp/UPLOAD.bat', $UploadScript);

	
	$Command = WINSCP_UTILITY_EXE . ' /script="' . UTILITY_BASE_DIR . 'winscp/UPLOAD.bat" /ini="' . UTILITY_BASE_DIR . 'winscp/WinSCP.ini"';
	$Log = array();
	exec($Command, $Log);
	
	$Log = implode("\r\n", $Log);
	
	return $Log;
    }

    
    public function __destruct()
    {
        fclose($this->Handle);
    }

}

