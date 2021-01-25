<?php

	
	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	if($_POST && $objPage->getRequest("user_action") == 'sendRx')
	{
		$arrSelARHData			=	$objPage->getRequest("list16_cSlcPK");
		$objMedDb				=	MedDB::getDBObject();
		for($intData = 0; $intData < count($arrSelARHData); $intData++)
		{
			$intARHId	=	$arrSelARHData[$intData];
			
			
			
			
			$strSql				=	"UPDATE rxh_ar_header
										SET mt_is_posted = 'N', mt_auto_send ='N'
										WHERE mt_rxh_arh_id = '".$intARHId."'";
			$objMedDb->executeQuery($strSql);			
		}
		header("Location: index.php?file=med_rxh_master");
		exit;
	}	
	
	
	$strMiddle					=	"./middle/med_rxh_master.htm";	

	
	$intTableId 				=	"16";
	$intButtonId 				=	$objPage->getRequest('hid_button_id');
	$intShowRows				= 	$objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
	$strSrServiceLevel			= 	$objPage->getSRequest('Sr_slt_service_level',$intTableId);
	$strMeditabIdGiven			=	$objPage->getSRequest('Sr_Taslt_meditab_id_given',$intTableId);
	
	$objModule->checkMaxRowLimit($intShowRows);	
	
	$strAction					=	"L";
	
	$strWhere					=	"1";	
	
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
	
	
	function list16_DataLoaded($rsData)
	{
		global $objPage,$objModule,$objList,$IMAGE_PATH;
		$intTotalRecords			=	count($rsData[0]);
				
		for($intData=0; $intData<$intTotalRecords; $intData++)
		{
			$intARHId		=	$rsData[0][$intData]['mt_rxh_arh_id'];
			$strPostedFile	=	$rsData[0][$intData]['mt_ear_posted_file'];
			$strResponseFile=	$rsData[0][$intData]['mt_response_file'];
			$strFileType	=	$rsData[0][$intData]['hdr_file_type'];
			$strTransAction	=	$rsData[0][$intData]['rhd_transmission_action'];
			
			
			$rsData[0][$intData]['Edit']	=	$objPage->getImage("","images/view.gif",'style="cursor:pointer;" onclick="tb_show(null,\'index.php?file=med_rxh_common&amp;arh_id='.$rsData[0][$intData]['mt_rxh_arh_id'].'&amp;TB_iframe=true&amp;height=380&amp;width=800&amp;modal=true\',null);" ');
			
			$rsData[0][$intData]['mt_ear_posted_file']	=	substr($strPostedFile, 0, 29)."...";
			
			$arrPopup			=	array();
			$arrPopup[]			= 	array("strRowTitle"=>"Posted File"	, "arrColumns"=>array($strPostedFile));
			$arrPopup[]			= 	array("strRowTitle"=>"Response File", "arrColumns"=>array("<a href=\"javascript:;\" onclick=\"tb_show(null,'index.php?file=med_rxh_file&amp;arh_id=".$rsData[0][$intData]['mt_rxh_arh_id']."&amp;file_type=Response&amp;TB_iframe=true&amp;height=300&amp;width=750&amp;modal=true',null);\">".$strResponseFile."</a>"));				
			$arrWidth[]			=	array("strRowTitle"=>'10%'  , "arrColumns"=>array('90%'));
			
			
			$strShowFile	=	$objPage->generatePopupDataTable($arrPopup,$arrWidth,true);
			
			
			unset($arrPopup);
			unset($arrWidth);
			
			$rsData[0][$intData]['Edit']	.=	" ".$objPage->openMouseImagePopup("images/ico-details.gif",$strShowFile,"File Details", "","File Details");				

			
			if($strFileType == 'T')
			{
				$rsData[0][$intData]['hdr_file_type']	=	"Testing";
			}
			elseif($strFileType == 'P')
			{
				$rsData[0][$intData]['hdr_file_type']	=	"Production";
			}
			
			
			if($strTransAction == 'N')
			{
				$rsData[0][$intData]['rhd_transmission_action']	=	"New";
			}
			else if($strTransAction == 'R')
			{
				$rsData[0][$intData]['rhd_transmission_action']	=	"Retransmisstion";
			}
			if($rsData[0][$intData]['mt_is_posted'] == 'N' && $rsData[0][$intData]['mt_auto_send'] == 'N')
			{
				$strSendButton		=	$objPage->getButton("post_".$intARHId,"","Send",0,"class='btn' onclick='sendRx(".$intARHId.");' ");
				$rsData[0][$intData]['mt_is_posted']	.=	"<span id='send_process_".$intARHId."'>";
				$rsData[0][$intData]['mt_is_posted']	.=	"&nbsp;".$strSendButton;
				$rsData[0][$intData]['mt_is_posted']	.=	"</span>";
			}
			
			if($rsData[0][$intData]['error_cnt'] == '0')
			{
				$rsData[0][$intData]['error_cnt']	=	'-';
			}
		}
	}
?>