<?php

	
	$objPage->objGeneral->checkAuth(7,"L");
	
	
	include_once('./../base/meditab/med_quicklist.php');
		
	
	include_once("./base/med_module.php"); 
		
	
	$objModule 			= 	new MedModule();
	
	
	$intTableId 		= 	"10";
	$strPageType 		= 	"L";
	$intButtonId 		= 	$objPage->getRequest('hid_button_id');	
	$intShowRows		= 	$objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
	
	$objModule->checkMaxRowLimit($intShowRows);	
	$strFile 			=	 $objPage->getRequest('file');
	$intTranId 			=	 $objPage->getRequest('tran_id');
	
	$strFromDate	= $objPage->getSRequest('Sr_Dttxt_date_from',$intTableId);
	$strToDate		= $objPage->getSRequest('Sr_Dttxt_date_to',$intTableId);
	$objPage->setRequest('strEWhere',"");
	
	
	$strMiddle				= 	"./middle/med_out_message_history.htm";	

	
	$arrWhere			=	array();
	if($strFromDate!='')
	{
		$arrWhere[]		=	" timestamp >= '" . $objModule->formatDateDB($strFromDate) . " 00:00:00'";
	}
	if($strToDate!='')
	{
		$arrWhere[]		=	" timestamp <= '" . $objModule->formatDateDB($strToDate) . " 23:59:59'";
	}
	$blnExtraWhere		=	false;
	if(count($arrWhere)> 0)
	{
		$strWhere			=	implode(' AND ',$arrWhere);
		$blnExtraWhere		=	true;
	}
	
	$arrHTMLControl		=	$objPage->getHtmlAll($intTableId,$strPageType,true,false,NULL,true,true,$blnExtraWhere,$strWhere);
	
	
	$strMessage 			= 	$objPage->objGeneral->getMessage();
	
	
	$localValues 			= 	array(
									"intButtonId"	=>	$intButtonId,
									"strFile"		=>	$strFile,
									"strPage"		=>	$strPage,
									"intTableId"	=>	$intTableId,
									"strPageType"	=>	$strPageType,
									"strMessage"	=>	$strMessage,
									"blnExport"		=>	$blnExport,
									"intTranId"		=>	$intTranId,
								);
		
	$localValues		= 	array_merge($localValues,$arrHTMLControl);
	
		
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
				$strImmeditateResponse = 	htmlentities($strImmeditateResponse);
				
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
				
				

				$strMessageParams 	= 	var_export(unserialize($strMessageParams),true);
				
				preg_match_all("~'EDIMESSAGE' => '(.*?)',~", $strMessageParams, $matches, PREG_PATTERN_ORDER);
				
				$matches[0][0] 		= 	str_replace("'EDIMESSAGE' => '",'',$matches[0][0]);				
				$matches[0][0] 		= 	str_replace("',",'',$matches[0][0]);
				$strEncodedMessage 	= 	$matches[0][0];
				$strDecodedMessage	=	base64_decode($strEncodedMessage);
				$strEncodedMessage	=	wordwrap($strEncodedMessage,100,"\n",true);
				$strDecodedMessage	=	wordwrap($strDecodedMessage,100,"\n",true);
				$strDecodedMessage	=	"'EDIMESSAGE(Decoded)' => '".$strDecodedMessage."',<br/> 'EDIMESSAGE(Encoded)' => '".$strEncodedMessage."',";
				$strMessageParams 	= 	preg_replace("~'EDIMESSAGE' => '(.*?)',~", $strDecodedMessage, $strMessageParams);
				
				
				if(strlen($strMessageParams) > $strMaxLengthToShowMoreIcon )
				{
					
					$strMessageParamsPartial		=	substr($strMessageParams,0,$strMaxLengthToShowMoreIcon);
					
					
					$rsData[0][$intData]['message_params']	=	$strMessageParamsPartial."".$objPage->openMouseImagePopup("images/read-more.gif",$strMessageParams);					
				}
			}	
			
			
			if($rsData[0][$intData]['message'] != "")
			{
				
				$strMessage	=	$rsData[0][$intData]['message'];
				
				preg_match_all('~<EDIFACTMessage>(.*?)</EDIFACTMessage>~', $strMessage, $matches, PREG_PATTERN_ORDER);
				
				$matches[0][0] 		= 	str_replace("<EDIFACTMessage>",'',$matches[0][0]);				
				$matches[0][0] 		= 	str_replace("</EDIFACTMessage>",'',$matches[0][0]);
				$strEncodedMessage 	= 	$matches[0][0];
				$strDecodedMessage	=	base64_decode($strEncodedMessage);
				$strEncodedMessage	=	wordwrap($strEncodedMessage,100,"\n",true);
				$strDecodedMessage	=	wordwrap($strDecodedMessage,100,"\n",true);
				$strDecodedMessage	=	"<EDIFACTMessage><DecodedMessage>".$strDecodedMessage."</DecodedMessage><EncodedMessage>".$strEncodedMessage."</EncodedMessage></EDIFACTMessage>";
				$strMessage 		= 	preg_replace('~<EDIFACTMessage>(.*?)</EDIFACTMessage>~', $strDecodedMessage, $strMessage);
				
				$strMessage 		= 	htmlentities($strMessage);
				
				
				if(strlen($strMessage) > $strMaxLengthToShowMoreIcon )
				{
					
					$strMessagePartial		=	substr($strMessage,0,$strMaxLengthToShowMoreIcon);					
					
					$rsData[0][$intData]['message']	=	$strMessagePartial."".$objPage->openMouseImagePopup("images/read-more.gif",$strMessage);					
				}
			}

			$rsData[0][$intData]['timestamp'] = date("d M y H:i:s", strtotime($rsData[0][$intData]['timestamp']));
		}
	}
?>