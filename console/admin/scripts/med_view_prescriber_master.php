<?php

	
	$objPage->objGeneral->checkAuth("14");
	
	
	include_once('./../base/meditab/med_quicklist.php');
	include_once("./base/med_module.php"); 
	
	
	$objModule = new MedModule();
	
	
	$intTableId 		= 	"14";
	$strPageType		=	"V";
	$intButtonId 		= 	$objPage->getRequest('hid_button_id');
	$strFile 			=	 $objPage->getRequest('file');
	$intTranId 			=	 $objPage->getRequest('mt_tran_id');
		
	$strModuleName		= 	$objPage->getPageTitleByDb($intTableId);
	
	
	$strMessage 		= $objPage->objGeneral->getMessage();
	
	
	$strMiddle		= "./middle/med_view_prescriber_master.htm";
	$strIndex 		= $strMiddle;	
	
	
	$strModuleName	= $objPage->getPageTitleByDb($intTableId);
	
	
	$strPage 		= $objPage->getHtmlPage($intTableId,$strPageType,true);
	
	 
	if($strPage['strservice_level_bits'] != '')
	{
		$arrServiceLevel		=	@explode(",",$strPage['strservice_level_bits']);
		$arrSLValue				=	array();
		foreach($arrServiceLevel as $strSLKey => $strSLValue)
		{
			$arrSLValue[]		=	$objModule->getComboValue('SERVICE_LEVEL',$strSLValue);					
		}
		$strPage['strservice_level_bits']	=	@implode(", ",$arrSLValue);
	}
	
	
	
	$localValues 		=	array(
										"intButtonId"		=>	$intButtonId,
										"strFile"			=>	$strFile,
										"intTableId"		=>	$intTableId,
										"strPageType"		=>	$strPageType,
										"strMessage"		=>	$strMessage,
										"strModuleName"		=>	$strModuleName,
										"intTranId"			=>	$intTranId,
							);	
	
	$localValues		= 	array_merge($localValues,$strPage);
	?>
