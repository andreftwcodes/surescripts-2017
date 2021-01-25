<?php








 
class MedArray2Xml 
{
	var $text;
	var $arrays, $keys, $node_flag, $depth, $xml_parser;
	
	
	function array2xml($array) 
	{
		
		
		$this->text.= $this->array_transform($array);
		return $this->text;
	}

	function array_transform($array)
	{
		

		foreach($array as $key => $value)
		{
			if(!is_array($value))
			{
				$this->text .= "<$key>$value</$key>";
			} 
			else 
			{
				$this->text.="<$key>";
				$this->array_transform($value);
				$this->text.="</$key>";
			}
		}
		return $array_text;
	}
	
	function xml2array($xml)
	{
		$this->depth=-1;
		$this->xml_parser = xml_parser_create();
		xml_set_object($this->xml_parser, $this);
		xml_parser_set_option ($this->xml_parser,XML_OPTION_CASE_FOLDING,0);
		xml_set_element_handler($this->xml_parser, "startElement", "endElement");
		xml_set_character_data_handler($this->xml_parser,"characterData");
		xml_parse($this->xml_parser,$xml,true);
		xml_parser_free($this->xml_parser);
		return $this->arrays[0];
	}
	function startElement($parser, $name, $attrs)
	{
	   $this->keys[]=$name; 
	   $this->node_flag=1;
	   $this->depth++;
	}
	function characterData($parser,$data)
	{
	   $key=end($this->keys);
	   $this->arrays[$this->depth][$key]=$data;
	   $this->node_flag=0; 
	}
	function endElement($parser, $name)
	{
	   $key=array_pop($this->keys);
	   
	   if($this->node_flag==1)
	   {
	     $this->arrays[$this->depth][$key]=$this->arrays[$this->depth+1];
	     unset($this->arrays[$this->depth+1]);
	   }
	   $this->node_flag=1;
	   $this->depth--;
	}

}

?>