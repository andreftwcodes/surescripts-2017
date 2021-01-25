<?php 
	
	
	set_time_limit(0);
	
	
	include_once('med_config.php');
	
	
	include_once(WEB_ROOT.'base/DB.php');
	
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	
	include_once(WEB_ROOT.'base/MedSimpleMailer.php');
	
	$strCurrentTime = date("Y-m-d");
	$strYesterdayDate	=	date('Y-m-d',strtotime("-1 days"));
	
	
	$strSql	=	"SELECT * FROM archive_log a WHERE date(a.log_starttime) IN ('" .$strCurrentTime ."', '" .$strYesterdayDate ."') ORDER BY a.log_id DESC LIMIT 1";
	
	$rsResult = $medDB->GetRow($strSql);
	
		
	$strBody = '';
	if(empty($rsResult)){
		$strSql	=	"SELECT date(a.log_starttime) as last_log_date FROM archive_log a ORDER BY a.log_id DESC LIMIT 1";
		$arrLastArchiveDetail = $medDB->GetRow($strSql);
		$strBody = "Hello Admin," . "<br /><br />" . 
			"Data is not archived after "	.$arrLastArchiveDetail['last_log_date'] ." date. <br />" .
			"Please connect to Server Console and fix the issue.";
	}
	else{
		$arrFailedArchiveTable = Array();
		if(!($rsResult['records_outbox_moved'] != '' && $rsResult['records_outbox_moved'] >= 0 && $rsResult['records_outbox_moved'] == $rsResult['records_outbox_deleted'])){
			$arrFailedArchiveTable[]	=	'out_message_transaction';
		}
		if(!($rsResult['records_outbox_history_moved'] != '' && $rsResult['records_outbox_history_moved'] >= 0 && $rsResult['records_outbox_history_moved'] == $rsResult['records_outbox_history_deleted'])){
			$arrFailedArchiveTable[]	=	'outbound_message_history';
		}
		if(!($rsResult['records_postjob_moved'] != '' && $rsResult['records_postjob_moved'] >= 0 && $rsResult['records_postjob_moved'] == $rsResult['records_postjob_deleted'])){
			$arrFailedArchiveTable[]	=	'out_message_post_job';
		}
		if(!($rsResult['records_inbox_moved'] != '' && $rsResult['records_inbox_moved'] >= 0 && $rsResult['records_inbox_moved'] == $rsResult['records_inbox_deleted'])){
			$arrFailedArchiveTable[]	=	'in_message_transaction';
		}
		
		if(!empty($arrFailedArchiveTable)){
			$strBody	=	"Hello Admin," . "<br />" . 
				"There is an issue while archiving below table(s). <ul>" ;
			foreach($arrFailedArchiveTable as $strTable){
				$strBody	.=	'<li>' .$strTable	.'</li>';
			}
			$strBody .= '</ul>Please connect to Server Console and fix the issue.';
		}
	}
	if(!empty($strBody)){
		$Mailer = new MedSimpleMailer();
		$Mailer->sendEmail(ARCHIVE_FAILURE_EMAIL_TO, ARCHIVE_FAILURE_EMAIL_FROM, ARCHIVE_FAILURE_EMAIL_SUBJECT, $strBody);		
	}
?>