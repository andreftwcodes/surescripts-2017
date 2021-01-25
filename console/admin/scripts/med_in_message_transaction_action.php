<?php




	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	$strAction	 	=	$objPage->getRequest("hid_page_type");

	
	$objData	= 	new MedData();
	
		
	
	$objPage->setTableProperty($objData);
	
	
	if($strAction) 
	{
		
		$strSentdDate			=  	$objPage->getRequest("Dttxt_sent_time_in_message_date"); 
		$strSentTime			=  	$objPage->getRequest("Tatxt_sent_time_in_message_time"); 
		$strSentAMPM			=  	$objPage->getRequest("slt_sent_time_in_message_ampm"); 
	
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
		
		
		
		$strReceiveddDate			=  	$objPage->getRequest("Dttxt_received_time_date"); 
		$strReceivedTime			=  	$objPage->getRequest("Tatxt_received_time_time"); 
		$strReceivedAMPM			=  	$objPage->getRequest("slt_received_time_ampm"); 
	
		$arrReceivedDate			=	explode("-",$strReceiveddDate);
		$strReceivedDateTime		=	$arrReceivedDate[2]."-".$arrReceivedDate[0]."-".$arrReceivedDate[1];
		
		if($strReceivedTime	!=	'')
		{
			 
			if($strReceivedAMPM == "PM")
			{
				$arrReceivedTime			=	explode(":",$strReceivedTime);
				$arrReceivedTime[0]	=	$arrReceivedTime[0]+12;
				if($arrReceivedTime[0]==24) $arrReceivedTime[0]="12";
				if(empty($arrReceivedTime[1])) $arrReceivedTime[1]="00";
				if(empty($arrReceivedTime[2])) $arrReceivedTime[2]="00";
				$strRDate	=	$strReceivedDateTime." ".$arrReceivedTime[0].":".$arrReceivedTime[1].":".$arrReceivedTime[2];
			}
			elseif($strReceivedAMPM == "AM")
			{
				$arrReceivedTime			=	explode(":",$strReceivedTime);
				if($arrReceivedTime[0]=="12") $arrReceivedTime[0]="00";
				if(empty($arrReceivedTime[1])) $arrReceivedTime[1]="00";
				if(empty($arrReceivedTime[2])) $arrReceivedTime[2]="00";
				$strRDate	=	$strReceivedDateTime." ".$arrReceivedTime[0].":".$arrReceivedTime[1].":".$arrReceivedTime[2];
			}
		}
		else
		{
			$strRDate	=	$strReceivedDateTime." 00:00:59";
		}	
		
		
		
		$objData->setArrRPAFields('sent_time_in_message_date,sent_time_in_message_time,sent_time_in_message_ampm,received_time_date,received_time_time,received_time_ampm');
		
		$objData->setFieldValue("sent_time_in_message",$strDate);
		$objData->setFieldValue("received_time",$strRDate);
	
		
		
		$objData->setFieldValue("meditab_response_status",$objPage->getRequest("Rslt_meditab_response_status"));
		
		
		
		$objData->performAction($strAction,null);	
		
		
		if($strAction =='E')
		{
			
			$objMedDb	=	MedDB::getDBObject();
			
			
			$intUserId		=	$objPage->objGeneral->getSession('intEmployeeId');
			$strIPAddress	=	$_SERVER['REMOTE_ADDR'];
			
			
			$intTranId		=	$objPage->getRequest('hid_tran_id');
			
			
			date_default_timezone_set('UTC');
			$dtCreated		=	date('Y-m-d H:i:s', time());
			
			
			$strSql		=	"INSERT INTO in_message_status_update_log(tran_id,user_id,ip_address,created_datetime) VALUES('$intTranId','$intUserId','$strIPAddress','$dtCreated')";
			$objMedDb->executeQuery($strSql);
		}
		
	}
	else
	{
		$objPage->objGeneral->raiseError("WARNING","No action define","Action Script","Do not call action script without action parameters"); 
	}
	
	header("Location:index.php?file=med_in_message_transaction");
	exit;	
?>