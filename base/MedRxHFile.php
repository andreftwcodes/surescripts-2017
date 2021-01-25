<?php



include WEB_ROOT.'base/MedSimpleFile.php';

class MedRxHFile extends MedSimpleFile
{
	
	private $FileType;
	
	
	private $Meta;
	
	
	public function __construct($FileName, $FileType)
	{
		$this->FileType = $FileType;
		
		parent::__construct($FileName);
	}
	
	
	private function parseFileMeta()
	{
		
		global $fbHeader, $fbFooter, $fbFieldSep, $rxFile;
		
		$HeaderData = $this->readHeader(2);
		
		$FooterData = $this->readFooter(2);
		
		$Record = array();
		
		
		foreach($HeaderData as $Index => $Line)
		{
			if(trim($Line) != "")
			{
				if(strpos($Line, "HDR") !== false)
				{
					$PartRecord = $this->parseLine($Line, MedRxHFile::simplifyCofig($fbHeader['COMMON']), $fbFieldSep);
				}
				else if(strpos($Line, $rxFile[$this->FileType]['HeaderID']) !== false)
				{
					$PartRecord = $this->parseLine($Line, MedRxHFile::simplifyCofig($fbHeader[$this->FileType]), $fbFieldSep);
				}
				$Record = array_merge($PartRecord, $Record);
			}
		}
		
		
		foreach($FooterData as $Index => $Line)
		{
			if(trim($Line) != "")
			{
				if(strpos($Line, "TRL") !== false)
				{
					
				}
				else if(strpos($Line, $rxFile[$this->FileType]['FooterID']) !== false)
				{
					$PartRecord = $this->parseLine($Line, MedRxHFile::simplifyCofig($fbFooter[$this->FileType]), $fbFieldSep);
				}
				$Record = array_merge($PartRecord, $Record);
			}
		}
		return $Record;
	}
	
	
	private static function simplifyCofig($Config)
	{
		$SimpleConfig = array();
		foreach($Config as $Item)
		{
			$SimpleConfig[$Item['POS']] = $Item;
		}
		return $SimpleConfig;
	}
	
	
	private function parseLine($RawLine, $Config, $ColumnSep = "|")
	{
		$Line = explode($ColumnSep, $RawLine);
		$Record = array();
		foreach($Line as $ColumnIndex => $ColumnValue)
		{
			if(isset($Config[$ColumnIndex]) === true)
			{
				$ColumnValue = trim($ColumnValue);
			
				if(isset($Config[$ColumnIndex]['CAST']) === true)
				{
					switch($Config[$ColumnIndex]['CAST'])
					{
						CASE "DATE":
							$ColumnValue = strftime("%Y-%m-%d",strtotime($ColumnValue));
							break;
				
						CASE "TIME":
							$ColumnValue = strftime("%H:%M:%S" ,strtotime(substr($ColumnValue,0,6)));
							break;
					}
				}
			
				$Record[$Config[$ColumnIndex]['FIELD']] = trim($ColumnValue);
			}
		}
		return $Record;
	}
	
	
	public function getFileMeta()
	{
		return $this->parseFileMeta();
	}
	
	
	public function writeRecordsInFile($FilePath)
	{
	
	}
}