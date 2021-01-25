<?php
include_once('../med_config.php');
include_once(WEB_ROOT.'base/db.php');



$rsPharmacy		=	$medDB->getAll('SELECT * FROM pharmacy_master');
$strPharmacy	=	'';
foreach($rsPharmacy as $arrPharmacy)
{
	foreach($cfgPharmacy as $cfgColumn)
	{
		$strPharmacy	.= str_pad($arrPharmacy[$cfgColumn['FIELD']],$cfgColumn['LOC'],' ',STR_PAD_RIGHT);
	}
	$strPharmacy		.= "\r\n";
}
file_put_contents(WEB_ROOT.'test/data/pharmacy.txt',$strPharmacy);


$rsPrescriber	=	$medDB->getAll('SELECT * FROM prescriber_master');
$strPrescriber	=	'';
foreach($rsPrescriber as $arrPrescriber)
{
	foreach($cfgPrescriber as $cfgColumn)
	{
		$strPrescriber	.= str_pad($arrPrescriber[$cfgColumn['FIELD']],$cfgColumn['LOC'],' ',STR_PAD_RIGHT);
	}
	$strPrescriber		.= "\r\n";
}
file_put_contents(WEB_ROOT.'test/data/prescriber.txt',$strPrescriber);
?>