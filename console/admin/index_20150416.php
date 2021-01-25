<?php

exit(1);
ob_start();
session_start();
$prModuleId	= 0;
$strWebServiceURL = "http://localhost".str_replace("console/admin/index.php", "", $_SERVER['PHP_SELF']);


define('SURESCRIPTS_SERVICES_URL',$strWebServiceURL.'directory_service.php');

include_once('./base/med_smarty.php'); 				

include_once('./../base/meditab/med_page.php');		

include_once("./../base/meditab/med_general.php");	

include_once('./../base/meditab/med_config.php');	

$objPage=new MedPage();
global $objSmarty; 					
global $globalValues;

$intLeftModuleId	=	$objPage->getRequest('hidin_module_id');


include_once("./scripts/left/med_left.php");    	

$IMAGE_PATH			=	$objPage->objGeneral->getSettings("ADMIN_IMAGE_PATH");
$strMedLogo			=	$objPage->objGeneral->getSettings("IMAGE_PATH").$objPage->objGeneral->getSettings("SITE_LOGO");
$strUserName		=	$objPage->objGeneral->getSession('strUserName');
$intLeftModuleId	=	$objPage->getRequest('hidin_module_id');
$intEmpOffice_id	=	$objPage->objGeneral->getSession('intOffice_id');
$strTimeZone		=	$objPage->objGeneral->getSession('strTimeZone');
$blnAdminRights		=	$objPage->objGeneral->getSession('blnAdminRights');
$isMeditabAdmin		=	$objPage->objGeneral->getSession('isMeditabAdmin');
$strServerType		=	$objPage->objGeneral->getSettings("SERVER_TYPE");

$strScript			=	"./scripts/".$_REQUEST['file'].".php";  
$intShowMaxRows		=	$objPage->objGeneral->getSettings("SHOW_MAX_ROW_LIMIT");

$blnTimeZone		=	date_default_timezone_set($strTimeZone);
$strCurrentDate		=	date("d M y h:i:s A");






$intLeftModuleId	=	0;
$intParentId		=	$objPage->getRequest('parent_id');

	

$globalValues		=	array(
								"strTop"			=>	$strTop,
								"strBottom"			=>	$strBottom,
								"strLeft"			=>	$strLeft,
								"strMedLogo"		=>	$strMedLogo, 
								"intLeftModuleId"	=>	$intLeftModuleId,
								"strUserName"		=>	$strUserName,
								"strLeftAdmin"		=>	$strLeftAdmin,
								"strCurrentDate"	=>	$strCurrentDate,
								"IMAGE_PATH"		=>	$IMAGE_PATH,
								"intEmpOffice_id"	=>	$intEmpOffice_id,
								"blnAdminRights"	=>	$blnAdminRights,
								"rsLinks"			=>	$rsLinks,
								"intParentId"		=>	$intParentId,
								"intShowMaxRows"	=>	$intShowMaxRows,
								"isMeditabAdmin"	=>	$isMeditabAdmin,
								"strServerType"		=>	$strServerType
							);
	
	
if(file_exists($strScript))  
{
	$arrayAllowFile = array("med_check_session",							
							"med_common_ajax");
						
	
	if(in_array($_REQUEST['file'],$arrayAllowFile))
	{
		include_once($strScript);
	}
	
	elseif(!$objGeneral->checkSession(1) && $strScript!="./scripts/med_forget_pass.php") 
	{
		include_once("./scripts/med_login.php");    
	}
	else
		include_once($strScript);	 
}
else
	include_once("./scripts/med_home.php");    
	
	
include_once("./scripts/bottom/med_bottom.php");    
unset($objSmarty); 					
?>