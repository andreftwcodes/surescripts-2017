<?php


	
	$objPage->objGeneral->checkAuth(9,"L");
	
	
	include_once('./../base/meditab/med_grouplist.php');
	
	
	include_once("./base/med_module.php"); 
		
	
	$objModule 			= 	new MedModule();
	
	
	$intTableId 		= 	"9";
	$strPageType 		= 	"L";
	$intButtonId 		= 	$objPage->getRequest('hid_button_id');	
	$intShowRows		= 	$objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
	$strAddress			=	$objPage->getSRequest('Sr_txt_address_line1',$intTableId);
	$strSrServiceLevel	= 	$objPage->getSRequest('Sr_slt_service_level',$intTableId);
	
	$objModule->checkMaxRowLimit($intShowRows);	
			
	
	$strMiddle			= 	"./middle/med_prescribe.htm";
	
	$strWhere			=	1;
	
	if($strSrServiceLevel != '' && $strSrServiceLevel != 'all')
	{
		$strWhere		.=	" and FIND_IN_SET('".$strSrServiceLevel."',service_level_bits) ";
	}
	
	if($strAddress!='')
	{
		$strWhere	.=	" and (address_line1 like '%".$strAddress."%' OR address_line2 like '%".$strAddress."%')";
	}
			 

	$arrHTMLControl			=	$objPage->getHtmlAll($intTableId,$strPageType,true,false,NULL,true,true,true,$strWhere);
	
	
	$arrSLOptions			=	$objPage->getOptions("SERVICE_LEVEL:SL:NT:''");
	
	foreach($arrSLOptions as $strSLKey => $strSLValue)
	{
		($strSrServiceLevel == "".$strSLKey."") ? $strSelected = "selected" : $strSelected = "";
		$arrSLOptions[]			=	'<option value="'.$strSLKey.'" '.$strSelected.'>'.$strSLValue.'</option>';
	}
	
	$strSLSelectBox				=	'<select name="Sr_slt_service_level" id="Sr_slt_service_level" class="comn-input">
									'.@implode("",$arrSLOptions).'
									</select>';
	
	
	
	$strMessage 		= 	$objPage->objGeneral->getMessage();
	
	
	$localValues 		= 	array(
									"intButtonId"	=>	$intButtonId,
									"strFile"		=>	$strFile,
									"strPage"		=>	$strPage,
									"intTableId"	=>	$intTableId,
									"strPageType"	=>	$strPageType,
									"strMessage"	=>	$strMessage,
									"blnExport"		=>	$blnExport,
									"strSLSelectBox"=>	$strSLSelectBox
								);
		
	$localValues		= 	array_merge($localValues,$arrHTMLControl);
	
	
	function list9_DataLoaded($rsData)
	{
		global $objPage,$objModule,$objList,$IMAGE_PATH;
		$intTotalRecords	=	count($rsData[0]);
		
		for($intData=0;$intData<$intTotalRecords;$intData++)
		{
			$intTranId			 =	$rsData[0][$intData]["mt_tran_id"];
			$intSpi				 =	$rsData[0][$intData]["spi"];
			
			$arrPopup[]	=	array("strRowTitle"=>"Email",	"arrColumns"=>array($rsData[0][$intData]["email"]));	
			$arrPopup[]	=	array("strRowTitle"=>"	",	"arrColumns"=>array($rsData[0][$intData]["version"]));	
			$arrPopup[]	=	array("strRowTitle"=>"NPI",	"arrColumns"=>array($rsData[0][$intData]["npi"]));	
			$arrPopup[]	=	array("strRowTitle"=>"Partner Account",	"arrColumns"=>array($rsData[0][$intData]["partner_account"]));			
			$arrWidth[]	=	array("strRowTitle"=>'10%',		"arrColumns"=>array('90%'));
			
			$strEditMouseHover	=	$objPage->generatePopupDataTable($arrPopup,$arrWidth,true);
			
			unset($arrPopup);
			unset($arrWidth);
			
			$strImageHistory			=	$objPage->getImage("img_hisotry",$IMAGE_PATH."gSearch.gif"," border=0 ");	
			$strOutMessageHisotry		=	$objPage->getHrefLink("index.php?file=med_out_message_tran&tran_id=".$intTranId."&popup=1&TB_iframe=true&height=420&width=800&modal=true",$strImageHistory,"class='thickbox'");
			
			$rsData[0][$intData]["edit"] =	$strOutMessageHisotry;
			
			
			if($rsData[0][$intData]["service_level_bits"] != '')
			{
				$arrServiceLevel		=	@explode(",",$rsData[0][$intData]["service_level_bits"]);
				$arrSLValue				=	array();
				foreach($arrServiceLevel as $strSLKey => $strSLValue)
				{
					$arrSLValue[]		=	$objModule->getComboValue('SERVICE_LEVEL',$strSLValue);					
				}
				$rsData[0][$intData]["service_level_bits"]	=	@implode(", ",$arrSLValue);
			}
			
			
		}
	}
?>