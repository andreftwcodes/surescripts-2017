<?php



class MedSimpleFile
{
	
	protected $File;
	
	 
	protected $Handle;
	
	
	public  $Error;
	
	
	public  $EOF;
	
	
	public function __construct($FileName)
	{
		$this->File = $FileName;
		
		
		$this->Error		=	false;	
		if(file_exists($FileName)) 
		{
			$this->Handle	=	@fopen($FileName, "r");
		}
		else
		{
			$this->Error	=	true;
		}
		$this->EOF			=	false;
	}
	
	
	protected function readFooter($LinesToRead = 2)
	{
		if($this->Handle)
		{
			$Chunk = 4096;
			$Data = '';
			$FileSize = sprintf("%u", filesize($this->File));
			$MaxLength = filesize($this->File);
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

				fseek($this->Handle, ($Length + $SeekSize) * -1, SEEK_END);
				
				$Data = fread($this->Handle, $SeekSize) . $Data;

				if (substr_count($Data, "\n") >= $LinesToRead + 1) 
				{
					preg_match("!(.*?\n){".($LinesToRead)."}$!", $Data, $Match);
					return @explode("\n",$Match[0]);
				}
			}
			return @explode("\n", $Data);
		}
	}
	
	
	protected function readHeader($LinesToRead = 2)
	{
		if($this->Handle)
		{
			
			$intCounter	=	1;
			
			
			while($intCounter <= $LinesToRead && !feof($this->Handle))
			{
				
				
				$Data[]	=	fgets($this->Handle, 4096);
				$intCounter++;
			}
			
			if(feof($this->Handle))
			{
				$this->EOF	=	true;
			}
			return $Data;
		}
	}
	
	
	function __destruct()
	{
		fclose($this->Handle);
	}
}
