<?php

class MedXmlParser
{
	private $Index;
	private $Property;
	
	public function __construct($Xml)
	{
		$Parser				= xml_parser_create();
		xml_parse_into_struct($Parser, $Xml, $this->Property, $this->Index);
		xml_parser_free($Parser);
	}
	
	public function __get($Key)
	{
		$Key				=	strtoupper($Key);
		
		$Value				=	null;
		if(isset($this->Index[$Key]))
		{
			if(count($this->Index[$Key])>0)
			{
				
				if(count($this->Index[$Key])>1)
				{			
					foreach($this->Index[$Key] as $Index)
					{
						$Value[]		= new Property($this->Property[$Index]);	
					}
				}
				else
				{
					
					foreach($this->Index[$Key] as $Index)
					{
						$Value		= new Property($this->Property[$Index]);
					}
				}
			}
			else
			{
				
				$Value				= new Property(array());
			}
		}
		else
		{
			$Value = new Property(array());
		}
		return $Value;
	}
	public function getBodyFirstElement()
	{
		$body_index = $this->Index['BODY'][0];
		$body_index++;
		return $this->Property[$body_index]['tag'];
	}
	
	
	
	public function getRefillResponseStatus()
	{
		$status_index = $this->Index['RESPONSE'][0];
		$status_index++;
		return $this->Property[$status_index]['tag'];
	}
	
	
	
	public static function validateXml($Xml)
	{
	    libxml_use_internal_errors(true);
	
	    $XmlDoc = new DOMDocument('1.0', 'utf-8');
	    $XmlDoc->loadXML($Xml);
	
	    $Errors = libxml_get_errors();
	    if (empty($Errors))
	    {
	        return true;
	    }
	
	    $Error = $Errors[0];
	    if ($Error->level < 3)
	    {
	        return true;
	    }
	
	    $Lines = explode("\r", $Xml);
	    $Line = $Lines[($Error->line)-1];
	
	    $Message = $Error->message.' at line '.$Error->line.':<br />'.htmlentities($Line);
	
	    return $Message;
	}
}


class Property
{
	private $Data;
	
	public function __construct($Data)
	{
		$this->Data	=	$Data;
	}
	
	public function __get($Key)
	{
		$Key				=	strtolower($Key);
		if(isset($this->Data[$Key]))
		{
			return $this->Data[$Key];
		}
	}
	
}
?>