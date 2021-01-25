<?php

	
	
	include_once('./../base/meditab/med_quicklist.php');		
	
	
	$intTableId 	= "12";
	$intButtonId 	= $objPage->getRequest('hid_button_id');
	$intShowRows		= 	$objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
	$strFromDate	= $objPage->getSRequest('Sr_Dttxt_start_time_from',$intTableId);
	$strToDate		= $objPage->getSRequest('Sr_Dttxt_start_time_to',$intTableId);
	$strAction		=	"L";
	
	$objModule->checkMaxRowLimit($intShowRows);	
	
	$strMiddle	= "./middle/med_directory_download_log.htm";	
	
	
	$arrWhere			=	array();
	if($strFromDate!='')
	{
		$arrWhere[]		=	" start_time >= '" . $objModule->formatDateDB($strFromDate) . " 00:00:00'";
	}
	if($strToDate!='')
	{
		$arrWhere[]		=	" start_time <= '" . $objModule->formatDateDB($strToDate) . " 23:59:59'";
	}
	$blnExtraWhere		=	false;
	if(count($arrWhere)> 0)
	{
		$strWhere			=	implode(' AND ',$arrWhere);
		$blnExtraWhere		=	true;
	}	
	$strHtmlControls	= 	$objPage->getHtmlAll($intTableId,$strAction,true,false,NULL,true,true,$blnExtraWhere,$strWhere);
	
	$strPage 			=	$strHtmlControls['strPage'];
	
	$strModuleName		= 	$objPage->getPageTitleByDb($intTableId);
	
	
	$strMessage 	= $objPage->objGeneral->getMessage();
	
	
	$localValues = array(
							"intButtonId"	=>	$intButtonId,
							"strPage"		=>	$strPage,
							"intTableId"	=>	$intTableId,
							"strPageType"	=>	$strAction,
							"strMessage"	=>	$strMessage,
							"strModuleName"	=>	$strModuleName,									
						);	
	$localValues		= 	array_merge($localValues,$strHtmlControls);		

function list12_DataLoaded($rsData)
	{
		global $objPage,$objModule,$objList,$IMAGE_PATH;
		$strDateTimeFormat	=	$objPage->objGeneral->getSettings('DATE_TIME_FORMAT');
		$intTotalRecords	=	count($rsData[0]);

		for($intData=0;$intData<$intTotalRecords;$intData++)
		{
			if($rsData[0][$intData]["start_time"] == '0000-00-00 00:00:00')
			{
			$rsData[0][$intData]["start_time"] 	=	"";
			}
			else 
			{
			$rsData[0][$intData]["start_time"] 	=	date($strDateTimeFormat,strtotime($rsData[0][$intData]["start_time"]));	
			}
			
			if($rsData[0][$intData]["end_time"] == '0000-00-00 00:00:00')
			{
			$rsData[0][$intData]["end_time"] 	=	"";
			}
			else 
			{
			$rsData[0][$intData]["end_time"] 	=	date($strDateTimeFormat,strtotime($rsData[0][$intData]["end_time"]));	
			}
			
			$arrFile = explode("/", $rsData[0][$intData]["file"]);
			$rsData[0][$intData]["file"] 		=	'<label title="'.$rsData[0][$intData]["file"].'">' . $arrFile[count($arrFile)-1] .'</label>';
			
			$rsData[0][$intData]['start_time'] = date("d M y H:i:s", strtotime($rsData[0][$intData]['start_time']));
			$rsData[0][$intData]['end_time']   = date("d M y H:i:s", strtotime($rsData[0][$intData]['end_time']));
			
		}
	}
?>
