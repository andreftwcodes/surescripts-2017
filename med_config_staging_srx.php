<?php
	
	
	$BASE_PATH		=	realpath(__FILE__);
	$THIS_FILE		=	basename($BASE_PATH);
	$SITE_DIR		=	str_replace(array($THIS_FILE,'\\'),array('','/'),$BASE_PATH);

	
	define('WEB_ROOT',$SITE_DIR);
	
	
	include WEB_ROOT . 'med_flat_file_config.php';
	
	
	date_default_timezone_set('GMT');
	
	
	define('SERVER_TYPE', "PHARMACY_PARTNER");
	
	define('PARTNER_ACCOUNT_ID','512');
	define('PORTAL_ID','1901');
	define('DATA_PROVIDER_ID','MEDTAB');
	define('DIR_LOGIN_ID','suiterx');
	define('DIR_PASSWORD','L0ckd0wn@123');
	define('DIR_VERSION','4.4');
	define('DIR_URL','https://staging.surescripts.net/SuiteRxTest106/DirectoryXmlServer.aspx');
	define('ASP_NET_PASSWORD_DIGEST_UTILITY_URL','http://localhost:81/getBase64EncodedFormat.aspx?strToEncode=');
	define('DIR_TO_DOWNLOAD','C:/directory_downloads/zip/');
	define('DIR_TO_EXTRACT','C:/directory_downloads/');
	define('DIR_DOWNLOAD_URL','https://staging.surescripts.net/Downloads/');
	
	
	define('MSG_LOGIN_ID','SuiteRxU$er');
	define('MSG_PASSWORD','*r3t1u$');	
	
	define('MSG_URL','https://staging.surescripts.net/SuiteRxTest/AuthenticatingXmlServer.aspx');
	define('MSG_106_URL','https://staging.surescripts.net/SuiteRxTest106/AuthenticatingXmlServer.aspx');
	
	
	define('SRX_LOGIN_ID','surescripts');
	define('SRX_PASSWORD','SS2009Suiterx');
	
	
	
	define('OVERRIDE_APP_INFO_FOR_OUT_MSG', false);	
	
	define('VENDOR_NAME', 'SuiteMed LLC');
	define('APP_NAME', 'IMS');
	define('APP_VERSION', '12.0.5');
	
	
	define('MESSAGE_VERSION', '10.6');
	
	
	define('FORCE_QUERY_INBOX_METHOD_TO_RETURN_N_RECORDS', 2); 	
	define('FORCE_QUERY_OUTBOX_STATUS_METHOD_TO_RETURN_N_RECORDS', 50); 
	
	
	
	
	
	
	
	define('OUTBOX_MESSAGE_TABLE','out_message_transaction');
	define('INBOX_MESSAGE_TABLE','in_message_transaction');
	define('PHARMACY_MASTER_TABLE','pharmacy_master');
	define('PRESCRIBER_MASTER_TABLE','prescriber_master');
	define('DIRECTORY_DOWNLOAD_LOG','directory_download_log');
	define('OUTBOUND_MESSAGE_HISTORY','outbound_message_history');
	define('OUT_MESSAGE_POST_JOB','out_message_post_job');
	define('PHARMACY_REQUEST_TABLE','pharmacy_requests');
	define('PRESCRIBER_REQUEST_TABLE','prescriber_requests');
	define('OUT_MESSAGE_RETRY_SCHEDULE_TABLE','out_message_transaction_retry_schedule');
	define('PRESCRIBER_MOS_TABLE','prescriber_mos');
	define('PHARMACY_MOS_TABLE','pharmacy_mos');
	
	
	define('NO_DATA_FOUND'		, -1);
	define('NO_DATA_FOUND_MSG'	, 'No data found.');
	define('ERROR_IN_INPUT'		, -2);
	define('ERROR_IN_INPUT_MSG'	, 'Invalid input parameter(s) specified.');
	define('UNKNOWNE_ERROR'		, -99);
	define('UNKNOWNE_ERROR_MSG'	, 'Unknown error occured.');
	
	define('INBOX_BACKUP_DIR'	, 'C:/MSX_INBOX/');
	define('LOG_DIR','C:/MSX_LOGS/');
	
	
	
	define('USE_DEFINED_DELIMITERS',FALSE);
	
	define('UNA_COM_DATA_ELM_SEP',chr(28));
	define('UNA_DATA_ELM_SEP',chr(29));
	define('UNA_DECIMAL_NOTATION',chr(46));
	define('UNA_RELEASE_INDICATOR',chr(32));
	define('UNA_REPETATION_SEP',chr(31));
	define('UNA_SEGMENT_TERMINATOR',chr(30));
	
	
	
	define('LISTEN_MESSAGES_FOR_RECEIVERS_WHICH_ARE_NOT_ON_FILE', TRUE);

	
	define('FIRST_ATTEMPT_OFFSET', 0);		
	define('SECOND_ATTEMPT_OFFSET', 30);	
	define('THIRD_ATTEMPT_OFFSET', 180);	
	define('FORTH_ATTEMPT_OFFSET', 480);	
	
	
	
	define('SPEAKER_ERROR_EMAIL_SUBJECT', 'SuiteRx Surescripts Server Alert');
	define('SPEAKER_ERROR_EMAIL_FROM', 'SRxSSProduction.Meditab.com');
	define('SPEAKER_ERROR_EMAIL_TO', 'dharmavirj@meditab.com');
	define('PENDING_MESSAGE_ALERT_EMAIL_TO', 'dharmavirj@meditab.com, supals@meditab.com, nareshm@meditab.com, vishakhaj@meditab.com');
	
	
	define('DIRECTORY_SERVICE_URL', "http://localhost/meditabserver/directory_service.php");
	
	
	
	define('ZIP_UTILITY_EXE', 'C:/MT_UTILITY/7ZIP/7z.exe');
	define('WEBDAV_UTILITY_EXE', 'C:/MT_UTILITY/BitKinex/bitkinex.exe');
	define('WINSCP_UTILITY_EXE', 'C:/MT_UTILITY/winscp/winscp.com');
	define('UTILITY_BASE_DIR', 'C:/MT_UTILITY/');
	
	
	
	define('RXHUB_APP_SERVER','https://switch-cert01.rxhub.net/rxhub');
	
	define('RXHUB_WEBDAV_SERVER','https://files-cert.rxhub.net/webdav/');
	
	define('RXHUB_ERX_ACTIVITY_REPORT','https://transport-cert.rxhub.net/');
	
	define('RXHUB_PARTICIPANT_NAME','MEDITAB');
	define('RXHUB_PARTICIPANT_ID','T00000000020967');
	define('RXHUB_PARTICIPANT_PASSWORD','QW936Y3L1V');
	define('RXHUB_INCOMING_PASSWORD','2SMO2M9ZCD');
	
	define('RXHUB_WEBDAV_ROOT_DIR', 'C:/meditab/WebDAV/Formulary/'); 
	
	define('RXHUB_WEBDAV_BAKCUP_DIR', 'C:/meditab/WD_BKP/'); 
	define('RXHUB_WEBDAV_IMS_FILE_DIR', 'C:/meditab/WD_IMS/'); 
	
	define('RXHUB_EAR_IMS', 'C:/meditab/EAR_IMS/'); 
	define('RXHUB_EAR_SENT', 'C:/meditab/EAR_SENT/'); 
	define('RXHUB_EAR_RESPONSE', 'C:/meditab/EAR_RESPONSE/'); 
	
	
	define('SPEAKER_SERVICE_WAIT_TIMEOUT', 30); 
	define('OUT_MESSAGE_ERRORS_TABLE', 'out_message_errors');
	
	
	define('RESTRICT_MESSAGE_SUBMISSION_IF_GENERATED_BEFORE_LONG_TIME', TRUE);
	
	define('CONSIDER_MESSAGE_GENERATED_BEFORE_LONG_TIME_IF_GREATER_THAN_TIME', 345600);
		
	
	define('SPEAKER_LIMIT_LOCK_MESSAGE_PER_JOB', 30);
	
	define('TEST_SPI', '9999999999001,9999999999002,9999999999003');
?>