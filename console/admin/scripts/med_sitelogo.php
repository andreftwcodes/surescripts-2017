<?php

	
	$strMiddle="./middle/med_sitelogo.htm";		 
	$strIndex = $strMiddle;
	
	
	$strMedLogoPath=$objPage->objGeneral->getSettings("IMAGE_PATH").$objPage->objGeneral->getSettings("SITE_LOGO");
	
	
	$localValues = array("strMedLogoPath"=>$strMedLogoPath,"intWidth"=>$intWidth,"intHeight"=>$intHeight);

?>

