<?php

include_once(WEB_ROOT . 'base/adodb5/adodb.inc.php');


global $medDB,$medDev;
$medDB			=	NewADOConnection('mysqli');	
$medDB->port	=	3306;
$medDB->debug	=	false;

$medDev			=	NewADOConnection('mysqli');	
$medDev->port	=	3306;
$medDev->debug	=	false;

try
{
	
	
	$medDB->Connect('ipssurescript.ctpjxvfogk4x.us-east-1.rds.amazonaws.com','meditab','m3dih2so4','meditab_server_106_archive');
	$medDB->fetchMode	=	ADODB_FETCH_ASSOC;
        
        
	$medDev->Connect('172.16.4.84','root','meditab','meditab_server_106_archive');
	$medDev->fetchMode	=	ADODB_FETCH_ASSOC;
}
catch (Exception $e)
{
    echo 'Error';
    print_r($e);
}

?>