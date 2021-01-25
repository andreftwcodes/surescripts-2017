<?php

	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	include_once("./base/med_module.php"); 
		
	
	$objModule = new MedModule();
	
	
	$intTableId 		=	"7";
	$strPageType 		=	$objPage->getRequest('hid_page_type');
	$intTranId			=	$objPage->getRequest("tran_id");
	$strFile			=	$objPage->getRequest("file");
	$strMiddle			= 	"./middle/med_out_message_transaction_addedit.htm";
	
	
	$datetimeSentTimeDate	=	$objPage->getRequest("Dttxt_sent_time_date");
	$intRelatedMessageId	=	$objPage->getRequest("Tatxt_related_message_id");
	$intPostAttempt			=   $objPage->getRequest("Intxt_post_attempt");
	
	if($datetimeSentTimeDate == '' && $strPageType == 'A')
	{
		$objPage->setRequest('Dttxt_sent_time_date',date('m-d-Y'));
		$objPage->setRequest('Tatxt_sent_time_time',date('H:i'));
		$objPage->setRequest('slt_sent_time_ampm',date('A'));
	}
	
	if($intRelatedMessageId == '' && $strPageType == 'A')
	{
		$objPage->setRequest('Intxt_related_message_id',"0");
	}
	
	if($intPostAttempt == '' && $strPageType == 'A')
	{
		$objPage->setRequest('Intxt_post_attempt',"0");
	}
	
	
		
	
	$strMessage 	=	$objPage->objGeneral->getMessage();
	
	
	$strTitle		= 	$objPage->getPageTitleByDb($intTableId);

	
	$strMessage 	=	$objPage->objGeneral->getMessage();
	
	$strHtmlControl	= 	$objPage->getHtmlAll($intTableId,$strPageType,true,true,NULL,true,true,false,"");
	
	$localValues	=	array(
								"intButtonId"	=>	$intButtonId,
								"strFile"		=>	$strFile,
								"intTableId"	=>	$intTableId,
								"strTitle"		=>	$strTitle,
								"strPageType"	=>	$strPageType,
								"strMessage"	=>	$strMessage,
								"intTranId"		=>	$intTranId,												
							);
	$localValues	=	array_merge($localValues,$strHtmlControl);
?>