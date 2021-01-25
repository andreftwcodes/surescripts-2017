<?php


 
error_reporting(E_ALL ^ E_NOTICE);
require_once("./../base/pear/DB.php");
require_once("med_db.php");
require_once("med_data.php");
 	
$objDb=new MedDB(); 

define("DATABASE_TYPE","mysqli");



// define("DB_USERNAME","meditab");
define("DB_USERNAME","ss2017staging");



// define("DB_PASSWORD","m3dih2so4");
define("DB_PASSWORD","ss2017staging12345");



// define("DB_HOST","ipssurescript.ctpjxvfogk4x.us-east-1.rds.amazonaws.com");
define("DB_HOST","aa70vm6lip8c08.c8ttplenlxn1.ap-southeast-1.rds.amazonaws.com");


define("DSN_MEM","meditab_server_106");



define('APP_HOST_PRIMARY_DOMAIN','MEDITAB.COM');


define('IS_SECURE_APP',false);


$dsn_mem = array(
    'phptype'  => DATABASE_TYPE,
    'username' => DB_USERNAME,
    'password' => DB_PASSWORD,
    'hostspec' => DB_HOST,
    'database' => DSN_MEM,
);
$options = array(
    'debug'       => 3,
    'portability' => DB_PORTABILITY_ALL,
	'persistent'  => false,
);

$objDb=$objDb->connect($dsn_mem,$options);

$GLOBALS["objDb"] = $objDb;
$objGeneral=new MedGeneral();
?>
