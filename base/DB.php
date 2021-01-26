<?php

include_once(WEB_ROOT . 'base/adodb5/adodb.inc.php');


global $medDB;
$medDB			=	NewADOConnection('mysqli');	
$medDB->port	=	3306;
$medDB->debug	=	false;

try
{
	$medDB->Connect('aayw1kqk39cqjr.c8ttplenlxn1.ap-southeast-1.rds.amazonaws.com', 'ss2017staging', 'ss2017staging12345', 'meditab_server_106');
	$medDB->fetchMode =	ADODB_FETCH_ASSOC;
}
catch (Exception $e)
{
	print_r($e);
}

?>