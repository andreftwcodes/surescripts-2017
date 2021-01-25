<?php



class FlatFile
{
	
	private $Config;
	
	 
	private $Handle;
	
	
	private $LinesToBeRead;
	
	
	public  $EOF;
	
	
	public  $Error;
	
	
	public function __construct($FileName,$Config,$LinesToBeRead=100)
	{
		
		$this->Error		=	false;	
		if(file_exists($FileName))
			$this->Handle	=	@fopen($FileName, "r");
		else
			$this->Error	=	true;		
		$this->Config		=	$Config;
		$this->LinesToBeRead=	$LinesToBeRead;
		$this->EOF			=	false;
	}
	
	
	public function parseToRecordset()
	{
		$RecordSet			=	array();	
		if($this->Handle)
		{
			
			$intCounter	=	1;
			
			
			while($intCounter <= $this->LinesToBeRead && !feof($this->Handle))
			{
				
				
				$RawLine		=	fgets($this->Handle, 4096);
				if(trim($RawLine) != '')
				{
					$Line			=	new Line($RawLine,$this->Config);
					$RecordSet[]	=	$Line->parseToRecord();
					unset($Line);
					
					$intCounter++;
				}
			}
			
			if(feof($this->Handle))
			{
				$this->EOF	=	true;
			}
		}
		
		return $RecordSet;
	}
	
	
	public function __destruct()
	{
		@fclose($this->Handle);
	}
}


class Line
{
	
	private $FileLine;
	
	
	private $Config;
	
	
	private $Error;

	public function __construct($FileLine,$Config)
	{
		$this->FileLine	=	$FileLine;
		$this->Config	=	$Config;

	}
	
	
	public function parseToRecord()
	{
		$intColumn		=	0;
		$Record			=	array();
		foreach($this->Config as $ColumnConfig)
		{
			$Cast		=	'';
			if(isset($ColumnConfig['CAST']) == true)
			{
				$Cast = $ColumnConfig['CAST'];
			}
			switch($Cast)
			{
				case 'DATETIME':
					$Record[$ColumnConfig['FIELD']]	=	convertToMySqlDateTime(trim(substr($this->FileLine,$intColumn,$ColumnConfig['LOC'])));
					break;
				case 'DATE':
					$Record[$ColumnConfig['FIELD']]	=	convertToMySqlDate(trim(substr($this->FileLine,$intColumn,$ColumnConfig['LOC'])));
					break;
				default:
					$Record[$ColumnConfig['FIELD']]	=	trim(substr($this->FileLine,$intColumn,$ColumnConfig['LOC']));
					break;			
			}
			
			$intColumn						+=	$ColumnConfig['LOC'];
		}
		return $Record;
	}
}

?>