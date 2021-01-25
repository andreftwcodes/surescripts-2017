<?php





class MedPrescriber
{
	
	
	public function __construct()
	{
		
	}
	
	
	public static function GetPrescriberBySPI($SPI)
	{
		global $medDB;
		$SPI				=	trim($SPI);
		$SQL				=	'SELECT * FROM '.PRESCRIBER_MASTER_TABLE.' WHERE spi = \''.$SPI .'\'';
		$Result				=	$medDB->GetRow($SQL);
		if(count($Result) == 0)
		 		$Result		=	NO_DATA_FOUND;
		return $Result;
	}
	
	
	
	public static function GetPrescriberByAddress(
			$SPI = '', $DEA = '', $LastName = '', $FirstName = '', $ClinicName = '',
			$Address = '', $City = '', $State = '', $Zip = '', $Phone = '',
			$ServiceLevel = '' , $LastModifiedSinceDate = '', $FieldsToSelect='*')
	{
		global $medDB;
		$SQL				=	'SELECT ' . $FieldsToSelect . ' FROM ' . PRESCRIBER_MASTER_TABLE . ' ';
		
		
		if($SPI != '')
			$Where[]		=	'spi = \'' . trim($SPI) . '\'';
			
		if($DEA != '')
			$Where[]		=	'dea = \'' . trim($DEA) . '\'';
		
		if($LastName != '')
			$Where[]		=	'last_name LIKE \'' . $LastName . '%\'';
		
		if($FirstName != '')
			$Where[]		=	'first_name LIKE \'' . $FirstName . '%\'';
		
		if($ClinicName != '')
			$Where[]		=	'clinic_name LIKE \'' . $ClinicName . '%\'';
			
		
		
		
		if($Address != '')
			$Where[]		=	' (address_line1 LIKE \'' . $Address . '%\' OR address_line2 LIKE \'' . $Address . '%\') ';
			
		if($City != '')
			$Where[]		=	'city LIKE \'' . $City . '\'';
			
		if($State != '')
		 	$Where[]		=	'state LIKE \'' . $State . '\'';
		 	
		if($Zip != '')
		 	$Where[]		=	'zip LIKE \'' . $Zip . '\'';
		 
		if($Phone != '')
		 	$Where[]		=	'phone_primary LIKE \'' . $Phone . '\'';

		if($ServiceLevel != '')
		 	$Where[]		=	'service_level = \'' . $ServiceLevel . '\'';
		 	
		if($LastModifiedSinceDate != '')
		 	$Where[]		=	'last_modified_date >= \'' . $LastModifiedSinceDate . '\'';
		
		if(isset($Where[0]))
		{
			$SQL			.=	' WHERE ' . implode(' AND ',$Where);
		 	$Result			=	$medDB->GetAll($SQL);
		 	if(count($Result) == 0)
		 		$Result		=	NO_DATA_FOUND;
		}
		else
		{
		 	$Result			=	ERROR_IN_INPUT;
		}
		return	$Result;
	}
}

?>