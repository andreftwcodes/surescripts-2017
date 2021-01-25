<?php


 
error_reporting(E_ALL ^ E_NOTICE);

require_once("med_webservice.php");
require_once("med_data.php");
 	

$objDb=new MedDB(); 


define("DATABASE_TYPE","mysqli");


define("DB_USERNAME","mem");


define("DB_PASSWORD","mem");


define("DB_HOST","localhost");


define("DSN_MEM","mem");


$dsn_mem = array(
    'phptype'  => DATABASE_TYPE,
    'username' => DB_USERNAME,
    'password' => DB_PASSWORD,
    'hostspec' => DB_HOST,
    'database' => DSN_MEM,
);
$options = array(
    'debug'       => 2,
    'portability' => DB_PORTABILITY_ALL,
	'persistent'  => false,
);

$objDb->connect($dsn_mem,$options);

$objGeneral=new MedGeneral();
?>
