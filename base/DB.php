<?php

include_once(WEB_ROOT . 'base/adodb5/adodb.inc.php');


global $medDB;
$medDB			=	NewADOConnection('mysqli');	
$medDB->port	=	3306;
$medDB->debug	=	false;

try
{
	
	
	// $medDB->Connect('ipssurescript.ctpjxvfogk4x.us-east-1.rds.amazonaws.com','meditab','m3dih2so4','meditab_server_106');
	$medDB->Connect('localhost','root','root','meditab_server_106');
	$medDB->fetchMode	=	ADODB_FETCH_ASSOC;
}
catch (Exception $e)
{
	print_r($e);
}

?>