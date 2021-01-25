<?php

	
	
	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	include_once("./base/med_module.php"); 
		
	
	$objModule = new MedModule();
	
	
	$intTableId 		=	"6";
	$strPageType 		=	$objPage->getRequest('hid_page_type');
	$intTranId			=	$objPage->getRequest("tran_id");
	$strMiddle			= 	"./middle/med_in_message_transaction_addedit.htm";
	
	
	$strMessage 	=	$objPage->objGeneral->getMessage();
	
	
	$strTitle				= 	$objPage->getPageTitleByDb($intTableId);
	$datetimeSentTime		=	$objPage->getRequest("Dttxt_sent_time_in_message_date");
	$datetimeRecievedTime	=	$objPage->getRequest("Dttxt_received_time_date");
	
	if($datetimeSentTime == '' && $strPageType == 'A')
	{
		$objPage->setRequest('Dttxt_sent_time_in_message_date',date('m-d-Y'));
		$objPage->setRequest('Tatxt_sent_time_in_message_time',date('H:i'));
		$objPage->setRequest('slt_sent_time_in_message_ampm',date('A'));
	}
	
	if($datetimeRecievedTime == '' && $strPageType == 'A')
	{
		$objPage->setRequest('Dttxt_received_time_date',date('m-d-Y'));
		$objPage->setRequest('Tatxt_received_time_time',date('H:i'));
		$objPage->setRequest('slt_received_time_ampm',date('A'));
	}
	
	
	
	$strMessage 	=	$objPage->objGeneral->getMessage();
	
	$strHtmlControl	= 	$objPage->getHtmlAll($intTableId,$strPageType,true,true,NULL,true,true,false,"");
	
	$localValues	=	array(
								"intButtonId"	=>	$intButtonId,
								"strFile"		=>	$strFile,
								"intTableId"	=>	$intTableId,
								"strTitle"		=>	$strTitle,
								"strPageType"	=>	$strPageType,
								"strMessage"	=>	$strMessage,
								"intTranId"		=>	$intTranId							
							);
							
	$localValues	=	array_merge($localValues,$strHtmlControl);
?>