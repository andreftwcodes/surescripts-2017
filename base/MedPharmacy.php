<?php





class MedPharmacy
{
	
	
	public function __construct()
	{
		
	}
	
	
	public static function GetPharmacyByNCPDPID($NCPDPID)
	{
		global $medDB;
		$NCPDPID			=	trim($NCPDPID);
		$SQL				=	'SELECT * FROM '.PHARMACY_MASTER_TABLE.' WHERE ncpdpid = \''.$NCPDPID .'\'';
		$Result				=		$medDB->GetRow($SQL);
		if(count($Result) == 0)
		 		$Result		=	NO_DATA_FOUND;
		return $Result;
	}
	
	public static function GetPharmacy($NCPDPID = '', $PharmacyNumber = '', $PharmacyName = '',
			$Phone = '', $Address = '' , $City = '', $State = '', $Zip = '', 
			$ServiceLevel = '' , $LastModifiedSinceDate = '', $FieldsToSelect = '*')
	{
		global $medDB;
		$SQL				=	'SELECT '.$FieldsToSelect.' FROM ' .PHARMACY_MASTER_TABLE.' ';
		
		
		if($NCPDPID != '')
		{
			$Where[]		=	'ncpdpid LIKE \'' . $NCPDPID . '\'';
		}
		if($PharmacyNumber != '')
		{
			$Where[]		=	'pharmacy_number LIKE \'' . $PharmacyNumber . '\'';
		}
		if($PharmacyName != '')
		{
			$Where[]		=	'pharmacy_name LIKE \'' . $PharmacyName . '\'';
		}
		if($Phone != '')
		{
			$Where[]		=	'phone_primary LIKE \'' . $Phone . '\'';
		}
		
		
		
		if($Address != '')
		{
			$Where[]		=	' (address_line1 LIKE \'' . $Address . '%\' OR address_line2 LIKE \'' . $Address . '%\') ';
		}
		if($City != '')
		{
		 	$Where[]		=	'city LIKE \'' . $City . '\'';
		}
		if($State != '')
		{
		 	$Where[]		=	'state LIKE \'' . $State . '\'';
		}
		if($Zip != '')
		{
		 	$Where[]		=	'zip LIKE \'' . $Zip . '\'';
		}
		 	
		
		if(isset($Where[0]))
		{
			$SQL			.=	' WHERE ' . implode(' AND ',$Where);
			file_put_contents('c:/sql-pharmacy.txt',$SQL . '---'.$Address.'---');
		 	$Result			=	$medDB->GetAll($SQL);
		 	
		 	if(count($Result) == 0)
		 	{
		 		$Result		=	NO_DATA_FOUND;
		 	}
		}
		else
		{
		 	$Result			=	ERROR_IN_INPUT;
		}
		return	$Result;
	}
}


?>