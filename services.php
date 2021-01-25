<?php
	
	if(isset($_GET['WSDL']) || isset($_GET['wsdl']))
	{
		$strWSDLFile = file_get_contents('./MeditabServer.wsdl');
		header('Content-Type: text/xml');
		header("Content-length: ".(strlen($strWSDLFile)));
		echo $strWSDLFile;
		exit;
	}
	
	
	include_once('med_config.php');
	include_once('./base/MeditabServer.php');
	
	
	
	
	
	$medServer = new SoapServer(WEB_ROOT . "MeditabServer.wsdl", array('cache_wsdl' => WSDL_CACHE_NONE)); 
	
	$medServer->setClass("MeditabServer");
	
	$medServer->handle(); 
	
	
?>