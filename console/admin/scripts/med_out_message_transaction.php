<?php

	
	$objPage->objGeneral->checkAuth(7, "L");
	
	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	include_once("./base/med_module.php");
	
	
	$objModule 				= 	new MedModule();
	
	
	$intTableId 			= 	"7";
	$strPageType 			= 	"L";
	$intButtonId 			= 	$objPage->getRequest('hid_button_id');
	$intShowRows			= 	$objPage->getSRequest('Sr_Intxt_show_rows', $intTableId);
	
	$objModule->checkMaxRowLimit($intShowRows);	

	$strFromDate			= 	$objPage->getSRequest('Sr_Dttxt_sent_time_from', $intTableId);
	$strToDate				= 	$objPage->getSRequest('Sr_Dttxt_sent_time_to', $intTableId);
	
	
	$strMtTranId			= 	$objPage->getSRequest('Sr_Tatxt_mt_tran_id', $intTableId);
	
	
	$strMiddle				= 	"./middle/med_out_message_transaction.htm";	

	
	$arrWhere				=	array();
	if($strFromDate!='')
	{
		$arrWhere[]			=	" sent_time >= '" . $objModule->formatDateDB($strFromDate) . " 00:00:00'";
	}
	if($strToDate!='')
	{
		$arrWhere[]			=	" sent_time <= '" . $objModule->formatDateDB($strToDate) . " 23:59:59'";
	}
	$blnExtraWhere			=	false;
	if(count($arrWhere)> 0)
	{
		$strWhere			=	implode(' AND ', $arrWhere);
		$blnExtraWhere		=	true;
	}
	
	
	$strServerType = $objPage->objGeneral->getSettings('SERVER_TYPE');
	if($strServerType == 'PHARMACY_PARTNER')
	{
		$strFromMsgLabel	=	"NCPDP";
		$strToMsgLabel		=	"SPI";
	}
	else if($strServerType == 'PRESCRIBER_PARTNER')
	{
		$strFromMsgLabel	=	"SPI";
		$strToMsgLabel		=	"NCPDP";		
	}	

	$arrHTMLControl			=	$objPage->getHtmlAll($intTableId, $strPageType, true, false, NULL, true, true, $blnExtraWhere, $strWhere);

	$arrHTMLControl['strPage']	=	str_replace("From", "From (".$strFromMsgLabel.")", $arrHTMLControl['strPage']);
	$arrHTMLControl['strPage']	=	str_replace("To", "To (".$strToMsgLabel.")", $arrHTMLControl['strPage']);	

	
	$strMessage 			= 	$objPage->objGeneral->getMessage();
	
	
	$localValues 			= 	array(
									"intButtonId"	=>	$intButtonId,
									"strFile"		=>	$strFile,
									"strPage"		=>	$strPage,
									"intTableId"	=>	$intTableId,
									"strPageType"	=>	$strPageType,
									"strMessage"	=>	$strMessage,
									"blnExport"		=>	$blnExport,
									"strFromMsgLabel"	=>	$strFromMsgLabel,
									"strToMsgLabel"	=>	$strToMsgLabel
								);
	
	$localValues			= 	array_merge($localValues, $arrHTMLControl);
	
	
	function list7_DataLoaded($rsData)
	{
		global $objPage, $objModule, $objList, $IMAGE_PATH;
		$intTotalRecords				=	count($rsData[0]);
		$strMaxLengthToShowMoreIcon		=	35;
		
		for($intData = 0; $intData < $intTotalRecords; $intData++)
		{
			$intTranId	=	$rsData[0][$intData]["tran_id"];
			
			
			$arrPopup	=	array();
			
			$arrPopup[]	=	array("strRowTitle"=>"Message Id", "arrColumns"=>array($rsData[0][$intData]['message_id']));
			$arrPopup[]	=	array("strRowTitle"=>"Related Message Id", "arrColumns"=>array($rsData[0][$intData]['related_message_id']));
			$arrPopup[]	=	array("strRowTitle"=>"SMS Version", "arrColumns"=>array($rsData[0][$intData]['sms_version']));
			$arrPopup[]	=	array("strRowTitle"=>"App Name", "arrColumns"=>array($rsData[0][$intData]['app_name']));
			$arrPopup[]	=	array("strRowTitle"=>"App Version", "arrColumns"=>array($rsData[0][$intData]['app_version']));
			$arrPopup[]	=	array("strRowTitle"=>"Vendor Name", "arrColumns"=>array($rsData[0][$intData]['vendor_name']));
			$arrPopup[]	=	array("strRowTitle"=>"IMS IPS Response Status", "arrColumns"=>array($rsData[0][$intData]['meditab_response_status']));
			$arrPopup[]	=	array("strRowTitle"=>"Error Note", "arrColumns"=>array($rsData[0][$intData]['error_note']));
			$arrPopup[]	=	array("strRowTitle"=>"Post Attempt", "arrColumns"=>array($rsData[0][$intData]['post_attempt']));
			$arrWidth[]	=	array("strRowTitle"=>'10%',	"arrColumns"=>array('90%'));
			
			$strOutMessageTransData	=	$objPage->generatePopupDataTable($arrPopup, $arrWidth, true);
			
			unset($arrPopup);
			unset($arrWidth);
			
			$strEdit			 	=	$objPage->openMouseImagePopup($IMAGE_PATH."schedule-update.gif", $strOutMessageTransData, "", "href='index.php?file=med_out_message_transaction_addedit&hid_page_type=E&tran_id=".$intTranId."'");
			
			$strImageHistory		=	$objPage->getImage("img_hisotry", $IMAGE_PATH."gSearch.gif", " border=0 ");	
			$strOutMessageHisotry	=	$objPage->getHrefLink("index.php?file=med_out_message_tran&tran_id=".$intTranId."&popup=1&TB_iframe=true&height=420&width=800&modal=true",  $strImageHistory, "class='thickbox'");			
			
			$rsData[0][$intData]["edit"]		=	$strEdit.$strOutMessageHisotry;			
			$strEdiMessage						=	$rsData[0][$intData]['edi_message'];
			
			if($strEdiMessage != '')
			{
				$strDecodedEdiMessage				=	base64_decode($strEdiMessage);
				$strEdiMessagePartial				=	substr($strEdiMessage, 0, $strMaxLengthToShowMoreIcon);	
				$strEdiMessage						=	wordwrap($strEdiMessage,100,"\n",true);
				$strDecodedEdiMessage				=	wordwrap($strDecodedEdiMessage,100,"\n",true);
				$rsData[0][$intData]['edi_message']	=	$strEdiMessagePartial."".$objPage->openMouseImagePopup("images/read-more.gif", "<b>Decoded Message:-</b><br>".$strDecodedEdiMessage."<br><b>Encoded Message:-</b><br>".$strEdiMessage."");	
			}
			$rsData[0][$intData]['sent_time'] 	= 	date("d M y H:i:s", strtotime($rsData[0][$intData]['sent_time']));			
		}
	}
?>
