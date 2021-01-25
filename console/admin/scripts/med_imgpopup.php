<?php

	
	$strImageType = trim(strtoupper($objPage->getRequest('imgtype')));
	$arrParam=explode(":",$strImageType);
	
	if(count($arrParam) == 9) 
	{
		$strFileName 	= $arrParam[6];
		$strOFileName 	= $arrParam[8];	
	}
	else
	{ 
		$strFileName 	= $arrParam[5];
		$strOFileName	= $arrParam[7];
	}
			
	$strPath = "../employee/".$objGeneral->getSettings($arrParam[2]);	
	if($arrParam[4] != "")	$strPath .= "large/";
	$strFilePath = $strPath.$strFileName;
	$strExt=explode(".",$strOFileName);
	
	header("Location: ./scripts/med_force_download.php?ext=".$strExt."&hid_file_name=".$strOFileName."&hid_file_path=./../".$strFilePath);
	exit;
?>	
