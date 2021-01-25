<?php

	
	
	$strMiddle		=	"./middle/med_remove_file.htm";		 
	$strIndex		=	$strMiddle;
	
	
	$strAction		=	$objPage->getRequest('strAction');
	
	
	$strImgType		=	$objPage->getRequest('imgtype');	
	
	if (!empty($strImgType))
	{
			
			
			$arrParam=explode(":",$strImgType);
			
			
			$strFileType = $arrParam[1];
			
			$strPath = $objGeneral->getSettings($arrParam[2]);
			if(count($arrParam) == 6)
			{
				
				$strFileName = $arrParam[5];
				
				$strDivId	=	$arrParam[6];
			}
			else
			{
				
				$strFileName = $arrParam[6];
				
				$strDivId	=	$arrParam[7];
			}	

			if (!empty($strAction))
			{
				if (!empty($strFileType) && !empty($strPath)&& !empty($strFileName))
						$blnResponse=$objGeneral->removeImage($strFileType,$strPath,$strFileName);
				else
						$strMessage = "Sorry !!! Not enought parameter to perform this action";		
				if (!$blnResponse)
					$strAction="";
				
				$strMessage = $objPage->objGeneral->getMessage();
			}	
	}
	else
	{
				$strMessage = "Sorry !!! Not enought parameter to perform this action";
				$strAction ="";
	}

	
	$localValues = array("strAction"=>$strAction,"strMessage"=>$strMessage,"strDivId"=>$strDivId,"strFileName"=>$strFileName,"strImgType"=>$strImgType);
?>
