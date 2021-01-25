<?php

	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	$strAction	 	=	$objPage->getRequest("hid_page_type");

	
	$objData	= 	new MedData();

	
	$objPage->setTableProperty($objData);
	
	
	if($strAction) 
	{
		
		$strSentdDate			=  	$objPage->getRequest("Dttxt_sent_time_date"); 
		$strSentTime			=  	$objPage->getRequest("Tatxt_sent_time_time"); 
		$strSentAMPM			=  	$objPage->getRequest("slt_sent_time_ampm"); 
	
		$arrSentDate		=	explode("-",$strSentdDate);
		$strSentDateTime		=	$arrSentDate[2]."-".$arrSentDate[0]."-".$arrSentDate[1];
		
		if($strSentTime	!=	'')
		{
			 
			if($strSentAMPM == "PM")
			{
				$arrSentTime			=	explode(":",$strSentTime);
				$arrSentTime[0]	=	$arrSentTime[0]+12;
				if($arrSentTime[0]==24) $arrSentTime[0]="12";
				if(empty($arrSentTime[1])) $arrSentTime[1]="00";
				if(empty($arrSentTime[2])) $arrSentTime[2]="00";
				$strDate	=	$strSentDateTime." ".$arrSentTime[0].":".$arrSentTime[1].":".$arrSentTime[2];
				
			}
			elseif($strSentAMPM == "AM")
			{
				
				$arrSentTime			=	explode(":",$strSentTime);
				if($arrSentTime[0]=="12") $arrSentTime[0]="00";
				if(empty($arrSentTime[1])) $arrSentTime[1]="00";
				if(empty($arrSentTime[2])) $arrSentTime[2]="00";
				$strDate	=	$strSentDateTime." ".$arrSentTime[0].":".$arrSentTime[1].":".$arrSentTime[2];
				
				
			}
		}
		else
		{
			$strDate	=	$strSentDateTime." 00:00:59";
		}	
		
		
		$objData->setArrRPAFields('sent_time_date,sent_time_time,sent_time_ampm');
		
		$objData->setFieldValue("sent_time",$strDate);	
		
		
		$objData->performAction($strAction);	
		
	}
	else
	{
		$objPage->objGeneral->raiseError("WARNING","No action define","Action Script","Do not call action script without action parameters"); 
	}
	
	header("Location:index.php?file=med_out_message_transaction");
	exit;	
?>