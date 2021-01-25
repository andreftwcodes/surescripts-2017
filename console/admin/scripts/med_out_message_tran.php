<?php

	
	$objPage->objGeneral->checkAuth("69");
	
	
	include_once('./../base/meditab/med_quicklist.php');
	include_once("./base/med_module.php"); 
	
	
	$objModule = new MedModule();
	
	
	$intTableId 		= 	"10";
	$strPageType		=	"L";
	$intButtonId 		= 	$objPage->getRequest('hid_button_id');
	$strFile 			=	 $objPage->getRequest('file');
	$intTranId 			=	 $objPage->getRequest('tran_id');
	
	$strModuleName		= 	$objPage->getPageTitleByDb($intTableId);
	
	
	$strMessage 		= $objPage->objGeneral->getMessage();
	
	
	$strMiddle		= "./middle/med_out_message_tran.htm";
	$strIndex		=	$strMiddle	;	
		
	
	$strModuleName	= $objPage->getPageTitleByDb($intTableId);
	
	$objPage->setRequest('strEWhere',"om_tran_id = ".$intTranId);	
	
	$strPage 		= $objPage->getHtmlAll($intTableId,$strPageType);
    
	
	$localValues = array(
										"intButtonId"		=>	$intButtonId,
										"strFile"			=>	$strFile,
										"strPage"			=>	$strPage,
										"intTableId"		=>	$intTableId,
										"strPageType"		=>	$strPageType,
										"strMessage"		=>	$strMessage,
										"strModuleName"		=>	$strModuleName,
										"intTranId"			=>	$intTranId,
								);
	
	
	$localValues =array_merge($localValues ,$strPage);
	
	
	function list10_DataLoaded($rsData)
	{
		global $objPage,$objModule,$objList,$IMAGE_PATH;
		$intTotalRecords	=	count($rsData[0]);
		$strDateTimeFormat	=	$objPage->objGeneral->getSettings('DATE_TIME_FORMAT');
		$strMaxLengthToShowMoreIcon		=	40;
		
		for($intData=0;$intData<$intTotalRecords;$intData++)
		{	
			
			$rsData[0][$intData]["timestamp"] 	=	date($strDateTimeFormat,strtotime($rsData[0][$intData]["timestamp"]));

			
			if($rsData[0][$intData]['immediate_response'] != "")
			{
				
				$strImmeditateResponse	=	$rsData[0][$intData]['immediate_response'];
				$strImmeditateResponse	= 	htmlentities($strImmeditateResponse);
				
				
				if(strlen($strImmeditateResponse) > $strMaxLengthToShowMoreIcon )
				{
					
					$arrPopup			=	array();
					$arrPopup[]			= 	array("strRowTitle"=>"Immediate Response"	, "arrColumns"=>array($strImmeditateResponse));
					$arrWidth[]			=	array("strRowTitle"=>'10%'  , "arrColumns"=>array('90%'));
					
					
					$strShowNote	=	$objPage->generatePopupDataTable($arrPopup,$arrWidth,true);
					
					
					unset($arrPopup);
					unset($arrWidth);
					
					
					$strTempNote		=	substr($strImmeditateResponse,0,$strMaxLengthToShowMoreIcon);
					
					
					$rsData[0][$intData]['immediate_response']	=	$strTempNote."".$objPage->openMouseImagePopup("images/read-more.gif",$strShowNote);					
				}
			}	
			
			
			if($rsData[0][$intData]['message_params'] != "")
			{
				
				$strMessageParams	=	$rsData[0][$intData]['message_params'];

				
				if(strlen($strMessageParams) > $strMaxLengthToShowMoreIcon )
				{
					
					$arrPopup			=	array();
					$arrPopup[]			= 	array("strRowTitle"=>"Message Parameter(s)"	, "arrColumns"=>array($strMessageParams));
					$arrWidth[]			=	array("strRowTitle"=>'10%'  , "arrColumns"=>array('90%'));
					
					
					$strShowNote	=	$objPage->generatePopupDataTable($arrPopup,$arrWidth,true);
					
					
					unset($arrPopup);
					unset($arrWidth);
					
					$strMessageParams	=	$rsData[0][$intData]['message_params'];
					$strMessageParams 	= 	var_export(unserialize($strMessageParams),true);
					
					
					$strTempNote		=	substr($strMessageParams,0,$strMaxLengthToShowMoreIcon);
					
					
					$rsData[0][$intData]['message_params']	=	$strTempNote."".$objPage->openMouseImagePopup("images/read-more.gif",$strMessageParams);					
				}
			}	
			
			
			if($rsData[0][$intData]['message'] != "")
			{
				
				$strMessage	=	$rsData[0][$intData]['message'];
				$strMessage = 	htmlentities($strMessage);
				
				if(strlen($strMessage) > $strMaxLengthToShowMoreIcon )
				{
					
					$arrPopup			=	array();
					$arrPopup[]			= 	array("strRowTitle"=>"Message "	, "arrColumns"=>array($strMessage));
					$arrWidth[]			=	array("strRowTitle"=>'10%'  , "arrColumns"=>array('90%'));
					
					
					$strShowNote	=	$objPage->generatePopupDataTable($arrPopup,$arrWidth,true);
					
					
					unset($arrPopup);
					unset($arrWidth);
					
					
					$strTempNote		=	substr($strMessage,0,$strMaxLengthToShowMoreIcon);
					
					
					$rsData[0][$intData]['message']	=	$strTempNote."".$objPage->openMouseImagePopup("images/read-more.gif",$strShowNote);					
				}
			}

			$rsData[0][$intData]['timestamp'] = date("d M y H:i:s", strtotime($rsData[0][$intData]['timestamp']));
			
		}
		
	}		
	
?>
