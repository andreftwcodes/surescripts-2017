<?php



require_once WEB_ROOT.'base/MedSimpleFile.php';

class MedEARFile extends MedSimpleFile
{

	
	private $FileType;

    
	private $ARCRecord;

    
    private $ARARecords;

    
    private $HeaderMeta;

    
    private $TransmissionAction;

    
    private $IsHeaderRecordInsertRequired;

    
    private $HeaderRecordID;

	
	public function __construct($FileName, $FileType)
	{
        
        $this->ARARecords = array();
        $this->HeaderMeta = array();
        
		$this->FileType = $FileType;
        
		parent::__construct($FileName);
        
        $this->parseMetaHeader();
	}
	
	
	public function getMetaHeader()
	{
		return $this->HeaderMeta;
	}

    
    private function parseMetaHeader()
    {
		$Header = $this->readHeader(2);
		$Meta = @explode(EAR_COL_SEP, $Header[0]);
		$this->ARCRecord = $Header[1];
		$HeaderMeta = array();
        $HeaderMeta['HDR'] = trim($Meta[0]);
		$HeaderMeta['MEDITAB_ID'] = trim($Meta[1]);
		$HeaderMeta['SPI'] = trim($Meta[2]);
		
		
		
		$Meta[3] = trim($Meta[3]);
		if( strpos($Meta[3], '-') === false)
		{
			$HeaderMeta['FROM_DATE'] = $Meta[3];
			$HeaderMeta['FROM_DATE_STD'] = MedEARFile::formatDateForDB($Meta[3]);
		}
		else
		{
			$HeaderMeta['FROM_DATE_STD'] = $Meta[3];
			$HeaderMeta['FROM_DATE'] = MedEARFile::formatsDateForSurescripts( $Meta[3] );
		}
		
		$Meta[4] = trim($Meta[4]);
		if( strpos($Meta[4], '-') === false)
		{
			$HeaderMeta['TO_DATE'] = $Meta[4];
			$HeaderMeta['TO_DATE_STD'] = MedEARFile::formatDateForDB($Meta[4]);
		}
		else
		{
			$HeaderMeta['TO_DATE_STD'] = $Meta[4];
			$HeaderMeta['TO_DATE'] = MedEARFile::formatsDateForSurescripts( $Meta[4] );
		}

        
        $this->HeaderMeta = $HeaderMeta;
    }
	
		
	public static function formatDateForDB($strDate)
	{
		return  strftime("%Y-%m-%d", strtotime($strDate));
	}
	
	public static function formatsDateForSurescripts($strDate)
	{
		
        $Search = array("-");
        $Replace = array("");
		return str_replace( $Search, $Replace, $strDate );
	}

	
	public function getARCRecord()
	{
        
		$ARA = @explode(EAR_COL_SEP, $this->ARCRecord);
		$ARARecord['ara_rx_dea'] = $ARA[3];
		$ARARecord['ara_rx_npi'] = $ARA[4];
		$ARARecord['ara_rx_state_license_no'] = $ARA[5];
		$ARARecord['ara_rx_state'] = $ARA[6];
		$ARARecord['ara_zipcode'] = $ARA[7];
		$ARARecord['ara_rx_confidential_id'] = trim($ARA[8]);
        
        $ARARecord['mt_meditab_id'] = $this->HeaderMeta['MEDITAB_ID'];
        $ARARecord['mt_spi'] = $this->HeaderMeta['SPI'];
        $ARARecord['rhd_report_start_date'] = $this->HeaderMeta['FROM_DATE_STD'];
        $ARARecord['rhd_report_end_date'] = $this->HeaderMeta['TO_DATE_STD'];
		return $ARARecord;
	}

    
    public function validateFile($blnValidateCountWithFooter = false)
    {
        $Errors = array();

        
        if(isset($this->HeaderMeta) != true)
        {
            $this->parseMetaHeader();
        }

        
        if(strtoupper(strftime('%A',strtotime($this->HeaderMeta['FROM_DATE_STD']))) != "SUNDAY")
        {
            $Errors[] = "EAR must start from Sunday";
        }

        
        $NextSaturday = strtotime($this->HeaderMeta['FROM_DATE_STD'] . "+6day");
		
        if(strtoupper(strftime('%A',$NextSaturday)) != "SATURDAY" || strftime('%Y-%m-%d',$NextSaturday) != $this->HeaderMeta['TO_DATE_STD'])
        {
            $Errors[] = "Invalid end date for EAR report";
        }

        
		rewind($this->Handle);  
		$Counter['ARC'] = $Counter['ARA'] = $Counter['ARD'] = $Counter['TRL'] = 0;
		$Footer = array();
		while(!feof($this->Handle))
		{
			$Line		=	fgets($this->Handle);

			$RecordType = substr($Line,0,3);
			if($RecordType == "ARC")
			{
				
				$Counter['ARC'] += 1;
			}
			else if($RecordType == "ARA")
			{
				
				$Counter['ARA'] += 1;
				$this->ARARecords[] = $Line;
			}
			else if($RecordType == "ARD")
			{
				
				$Counter['ARD'] += 1;
			}
			else if($RecordType == "TRL")
			{
				
				$Counter['TRL'] += 1;
				$TRL = explode(EAR_COL_SEP, $Line);
				$TRL['ARA'] = $TRL[1];
				$TRL['ARD'] = $TRL[2];
			}
		}
		if($blnValidateCountWithFooter === true)
		{
			
			if($Counter['ARC'] != 1)
			{
				$Errors[] = "Invalid ARA line count (" . $Counter['ARA'] . "), it must be 1 per file";
			}
			
			if($Counter['TRL'] != 1)
			{
				$Errors[] = "Invalid TRL line count (" . $Counter['TRL'] . "), it must be 1 per file";
			}
			
			
			if($Counter['ARA'] != $TRL['ARA'])
			{
					$Errors[] = "Invalid ARA line count (" . $Counter['ARA'] . "), does not match with TRL count (" . $TRL['ARA'] . ")";
			}
			
			if(($Counter['ARA'] <= 7 && $Counter['ARA'] >= 1) == false)
			{
					$Errors[] = "Invalid ARA line count (" . $Counter['ARA'] . "), it must be between 1 to 7 per file";
			}
			
			if($Counter['ARD'] != $TRL['ARD'])
			{
					$Errors[] = "Invalid ARD line count (" . $Counter['ARD'] . "), does not match with TRL count (" . $TRL['ARD'] . ")";
			}
		}
        return $Errors;
    }

    
    private function findTransmissionAction()
    {
        global $medDB;

        
        $Sql = "SELECT mt_rxh_arh_id, rhd_transmission_action, mt_is_posted FROM rxh_ar_header WHERE
                rhd_report_start_date ='" . $this->HeaderMeta['FROM_DATE_STD'] . "'
                AND rhd_report_end_date = '" . $this->HeaderMeta['TO_DATE_STD'] . "' ORDER BY hdr_transmission_date ASC";

        $rsHeader = $medDB->getRow($Sql);
        
        if(count($rsHeader) > 0)
        {
			
            if($rsHeader['mt_is_posted'] == "N")
            {
                $TransmissionAction = $rsHeader['rhd_transmission_action'];
                
                $this->HeaderRecordID = $rsHeader['mt_rxh_arh_id'];
				
				$this->IsHeaderRecordInsertRequired = 'N';
            }
			
			else if($rsHeader['mt_is_posted'] == "N" && count($rsHeader) > 1)
			{
				
                $TransmissionAction = "R";
                $this->IsHeaderRecordInsertRequired = 'Y';
				
				
				foreach($rsHeader as $Header)
				{
					if($Header['mt_is_posted'] == "N")
					{
						$TransmissionAction = "R";
						$this->IsHeaderRecordInsertRequired = 'N';
						$this->HeaderRecordID = $Header['mt_rxh_arh_id'];
					}
				}
			}
            else
            {
				
                $TransmissionAction = "R";
                $this->IsHeaderRecordInsertRequired = 'Y';
            }
        }
        else
        {
            
            $TransmissionAction = "N";
            
            $this->IsHeaderRecordInsertRequired = 'Y';
        }
    }

    
    public function getTransmissionAction()
    {
        if(isset($this->TransmissionAction) === false)
        {
            $this->findTransmissionAction();
        }
        return $this->TransmissionAction;
    }

    
    public function doWeNeedToAddHeaderRecord($AutoAddIfRequired = true)
    {
        if($this->IsHeaderRecordInsertRequired === "Y")
        {
            $this->addHeaderRecord();
        }
        return $this->IsHeaderRecordInsertRequired;
    }

    
    public function getHeaderID()
    {
        if($this->HeaderRecordID == NULL || isset($this->HeaderRecordID) === false)
        {
            $this->doWeNeedToAddHeaderRecord();
        }
        return $this->HeaderRecordID;
    }

    
    public function addHeaderRecord($AutoSend = "")
    {
        global $medDB;

        
        $HRecord = array();
        $HRecord['hdr_transmission_date'] = date("Y-m-d");
        $HRecord['hdr_extract_date'] = date("Y-m-d");
        $HRecord['hdr_file_type'] = "T";    
        $HRecord['rhd_report_start_date'] = $this->HeaderMeta['FROM_DATE_STD'];
        $HRecord['rhd_report_end_date'] = $this->HeaderMeta['TO_DATE_STD'];
        $HRecord['mt_is_posted'] = "N";
        
        if($this->TransmissionAction != "R")
        {
            $HRecord['rhd_transmission_action'] = "N";
            $HRecord['mt_auto_send'] = "Y";
        }
        else
        {
            $HRecord['rhd_transmission_action'] = "R";
            $HRecord['mt_auto_send'] = "N";
        }
        
        if($AutoSend != "")
        {
            $HRecord['mt_auto_send'] = $AutoSend;
        }

        
        $medDB->AutoExecute("rxh_ar_header", $HRecord, "INSERT");
        $this->HeaderRecordID = $medDB->Insert_ID();
        return $this->HeaderRecordID;
    }

    
    public function processSummaryRecords($CommonHeaderID)
    {
        global $medDB;
		
        
        if(count($this->ARARecords) > 0)
        {
            foreach($this->ARARecords as $ARALine)
            {
                if(trim($ARALine) != "")
                {
                    $ARARecord = MedEARFile::parseARARecord($ARALine);
                    $ARARecord['mt_rxh_arc_id'] = $CommonHeaderID;
                    $medDB->AutoExecute("rxh_ar_summary", $ARARecord, "INSERT");
                }
            }
        }
    }

    
    private static function parseARARecord($ARALine)
    {
        $ARARecord = array();
        $ARALine = @explode(EAR_COL_SEP, $ARALine);

        $ARARecord['ara_rx_date'] = MedEARFile::formatDateForDB( trim($ARALine[1]) );
        $ARARecord['ara_e_rx_count'] = trim($ARALine[2]);
        $ARARecord['ara_fax_rx_count'] = trim($ARALine[3]);
        $ARARecord['ara_print_rx_count'] = trim($ARALine[4]);
        return $ARARecord;
    }

    
    public static function checkForFileDuplicationAndGenerateUniqueName($File)
    {
        if(file_exists($File) === true)
        {
            $File = MedEARFile::generateUniqueName($File);
        }
        return $File;
    }

    
    private static function generateUniqueName($File, $Counter = 1)
    {

        $DirName = dirname($File) . '/';
        $FileName = MedEARFile::getFileNameAndExtension($File);
        do
        {
            $NewFileName = $FileName['NAME'] . '_' . $Counter . $FileName['EXT'];
            $Counter++;
        }
        while(file_exists($DirName . $NewFileName) === true);
        return $DirName . $NewFileName;
    }

    
    public static function getFileNameAndExtension($File)
    {
        $FileName = array();
        if(strpos($File, ".") !== false)
        {
            $FileName['EXT'] = "." . end(explode(".", $File));
            $FileName['NAME'] = basename($File, $FileName['EXT']);
        }
        else
        {
            $FileName['EXT'] = '';
            $FileName['NAME'] = $File;
        }
        return $FileName;
    }

    
    public function updateFinalFlagNegativeForPastFiles($CommonHeaderID)
    {
        global $medDB;
        
        
        $Where = "mt_spi = '" . $this->HeaderMeta['SPI'] . "' AND mt_rxh_arh_id = " . $this->HeaderRecordID . " AND mt_rxh_arc_id != " . $CommonHeaderID;
        $medDB->AutoExecute("rxh_ar_common", array('mt_is_final'=>'N'), "UPDATE", $Where);
    }
}

?>