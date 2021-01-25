<?php

	
	$objPage->objGeneral->checkAuth(6,"L");
	
	
	include_once('./../base/meditab/med_quicklist.php');
		
	
	include_once("./base/med_module.php"); 
		
	
	$objModule 			= 	new MedModule();
	
	
	$intTableId 		= 	"6";
	$strPageType 		= 	"L";
	$intButtonId 		= 	$objPage->getRequest('hid_button_id');	
	$intShowRows		= 	$objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
	
	$objModule->checkMaxRowLimit($intShowRows);	
			
	$strFromDate	= $objPage->getSRequest('Sr_Dttxt_time_in_message_from',$intTableId);
	$strToDate		= $objPage->getSRequest('Sr_Dttxt_time_in_message_to',$intTableId);
    $strSearchSendRecieve		= $objPage->getSRequest('Sr_Rslt_Send_Recieve',$intTableId);


    $intMeditabId = $objPage->getRequest('Sr_Tatxt_meditab_id',$intTableId);
	
	$strMiddle		= 	"./middle/med_in_message_transaction.htm";
 
	
	$arrWhere		=	array();
	
	if($strFromDate!='')
	{
		$arrWhere[]	=	$strSearchSendRecieve . " >= '" . $objModule->formatDateDB($strFromDate) . " 00:00:00'";
	}
	if($strToDate!='')
	{
		$arrWhere[]	=	$strSearchSendRecieve. " <= '" . $objModule->formatDateDB($strToDate) . " 23:59:59'";
	}
	
	
    if($intMeditabId !='')
    {
         $strServerType = $objPage->objGeneral->getSettings('SERVER_TYPE');
        if($strServerType == 'PHARMACY_PARTNER)')
        {
            $arrWhere[] =  "to_id in ( (SELECT ncpdpid FROM pharmacy_mos WHERE meditab_id = '".$intMeditabId."'))";
        }
        else
        {
            $arrWhere[] = "to_id in ((SELECT spi FROM prescriber_mos WHERE meditab_id ='".$intMeditabId."'))";
        }
    }
	$blnExtraWhere	=	false;
	if(count($arrWhere)> 0)
	{
		$strWhere			=	implode(' AND ',$arrWhere);
		$blnExtraWhere		=	true;
	}
	
	
	$strServerType = $objPage->objGeneral->getSettings('SERVER_TYPE');
	if($strServerType == 'PHARMACY_PARTNER')
	{
		$strFromMsgLabel	=	"SPI";
		$strToMsgLabel		=	"NCPDP";
	}
	else if($strServerType == 'PRESCRIBER_PARTNER')
	{
		$strFromMsgLabel	=	"NCPDP";
		$strToMsgLabel		=	"SPI";		
	}
	
	$arrHTMLControl		=	$objPage->getHtmlAll($intTableId,$strPageType,true,false,NULL,true,true,$blnExtraWhere,$strWhere);
	
	$arrHTMLControl['strPage']	=	str_replace("From", "From (".$strFromMsgLabel.")", $arrHTMLControl['strPage']);
	$arrHTMLControl['strPage']	=	str_replace("To", "To (".$strToMsgLabel.")", $arrHTMLControl['strPage']);	

	
	$strMessage 		= 	$objPage->objGeneral->getMessage();
	
	
	$localValues 		= 	array(
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
		
	$localValues		= 	array_merge($localValues,$arrHTMLControl);
	
	
	function list6_DataLoaded($rsData)
	{
		global $objPage,$objModule,$objList,$IMAGE_PATH;
		$intTotalRecords	=	count($rsData[0]);
		$strMaxLengthToShowMoreIcon		=	35;
		
		for($intData=0;$intData<$intTotalRecords;$intData++)
		{
			$intTranId			 =	$rsData[0][$intData]["tran_id"];			
						
		
			$arrPopup	=	array();
			$arrPopup[]	=	array("strRowTitle"=>"Message Id", "arrColumns"=>array($rsData[0][$intData]['message_id']));
			$arrPopup[]	=	array("strRowTitle"=>"Related Message Id", "arrColumns"=>array($rsData[0][$intData]['related_message_id']));
			$arrPopup[]	=	array("strRowTitle"=>"SMS Version", "arrColumns"=>array($rsData[0][$intData]['sms_version']));
			$arrPopup[]	=	array("strRowTitle"=>"App Name", "arrColumns"=>array($rsData[0][$intData]['app_name']));
			$arrPopup[]	=	array("strRowTitle"=>"App Version", "arrColumns"=>array($rsData[0][$intData]['app_version']));
			$arrPopup[]	=	array("strRowTitle"=>"Vendor Name", "arrColumns"=>array($rsData[0][$intData]['vendor_name']));
			$arrPopup[]	=	array("strRowTitle"=>"Error Note", "arrColumns"=>array($rsData[0][$intData]['error_note']));
			$arrPopup[]	=	array("strRowTitle"=>"Meditab Responce Status", "arrColumns"=>array($rsData[0][$intData]['meditab_response_status']));
			
			$arrWidth[]		=	array("strRowTitle"=>'10%',			"arrColumns"=>array('90%'));
			$strInMessageTransData			=	$objPage->generatePopupDataTable($arrPopup,$arrWidth,true);
			unset($arrPopup);
			unset($arrWidth);
			
			$rsData[0][$intData]['edit'] =	$objPage->openMouseImagePopup($IMAGE_PATH."schedule-update.gif",$strInMessageTransData,"","href='index.php?file=med_in_message_transaction_addedit&hid_page_type=E&tran_id=".$intTranId."'");
			
			$strEdiMessage	=	$rsData[0][$intData]['edi_message'];
			if($strEdiMessage != '')
			{
				$strEdiMessagePartial				=	substr($strEdiMessage,0,$strMaxLengthToShowMoreIcon);	
				$rsData[0][$intData]['edi_message']	=	$strEdiMessagePartial."".$objPage->openMouseImagePopup("images/read-more.gif",$strEdiMessage);	
			}		
				
			$rsData[0][$intData]['sent_time_in_message'] = date("d M y H:i:s", strtotime($rsData[0][$intData]['sent_time_in_message']));
			$rsData[0][$intData]['received_time'] = date("d M y H:i:s", strtotime($rsData[0][$intData]['received_time']));			
			
		}
	}
?>
