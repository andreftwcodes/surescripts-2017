<?php

	
	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	$strMiddle					=	"./middle/med_rxh_common.htm";	

	
	$intTableId 				=	"17";
	$intButtonId 				=	$objPage->getRequest('hid_button_id');
	$intShowRows				= 	$objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
	$strSrServiceLevel			= 	$objPage->getSRequest('Sr_slt_service_level',$intTableId);
	$strMeditabIdGiven			=	$objPage->getSRequest('Sr_Taslt_meditab_id_given',$intTableId);
	$intARHId					=	$objPage->getRequest('arh_id');
	$objModule->checkMaxRowLimit($intShowRows);	
	
	$strIndex					=	$strMiddle;
	$strAction					=	"L";
	
	$strWhere					=	"1";
	if($intARHId != '')
	{
		$strWhere		.=	" AND rxh_ar_common.mt_rxh_arh_id = '".$intARHId."'";
	}
	
	$strHtmlControls			= 	$objPage->getHtmlAll($intTableId,$strAction,true,false,NULL,true,true,true,$strWhere);

	$strPage 					=	$strHtmlControls['strPage'];
	
	$strModuleName				= 	$objPage->getPageTitleByDb($intTableId);
	
	
	$strMessage 				=	$objPage->objGeneral->getMessage();
	
	
	$localValues 				=	array(
											"intButtonId"		=>	$intButtonId,
											"strPage"			=>	$strPage,
											"intTableId"		=>	$intTableId,
											"strPageType"		=>	$strAction,
											"strMessage"		=>	$strMessage,
											"strModuleName"		=>	$strModuleName
										);
	$localValues				= 	array_merge($localValues,$strHtmlControls);
	
	
	function list17_DataLoaded($rsData)
	{
		global $objPage,$objModule,$objList,$IMAGE_PATH;
		$intTotalRecords			=	count($rsData[0]);
				
		for($intData=0; $intData<$intTotalRecords; $intData++)
		{
			$strPostedFile	=	$rsData[0][$intData]['mt_ims_filename'];
			$strFileType	=	$rsData[0][$intData]['hdr_file_type'];
			$strTransAction	=	$rsData[0][$intData]['rhd_transmission_action'];
			if(strlen($strPostedFile) > 15)
			{
				$rsData[0][$intData]['mt_ims_filename']	=	substr($strPostedFile, 0, 14)."..";
				
				$arrPopup			=	array();
				$arrPopup[]			= 	array("strRowTitle"=>"Posted File"	, "arrColumns"=>array($strPostedFile));
				$arrWidth[]			=	array("strRowTitle"=>'10%'  , "arrColumns"=>array('90%'));
				
				
				$strShowFile		=	$objPage->generatePopupDataTable($arrPopup,$arrWidth,true);
				
				
				unset($arrPopup);
				unset($arrWidth);
				
				$rsData[0][$intData]['mt_ims_filename']	.=	" ".$objPage->openMouseImagePopup("images/read-more.gif",$strShowFile);
				
			}
			
			
			$strSummaryImage	=	$objPage->getImage("","images/view.gif","");
			$strSummaryDetails	=	$objPage->getHrefLink("index.php?file=med_rxh_summary&amp;arc_id=".$rsData[0][$intData]['mt_rxh_arc_id'],$strSummaryImage,'rel="index.php?file=med_rxh_summary&amp;arc_id='.$rsData[0][$intData]['mt_rxh_arc_id'].'" class="report_summary" title="Report Summary"');
			$rsData[0][$intData]['Edit']	=	$strSummaryDetails;
		}
	}
?>

