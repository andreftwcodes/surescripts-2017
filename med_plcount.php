<?php

	
	include_once('med_config.php');
	
	include_once(WEB_ROOT.'base/DB.php');

	$strSQL = "SELECT COUNT(0) AS cnt, message_status as status FROM out_message_transaction WHERE message_status IN ('Pending','Locked') GROUP BY message_status";

	$arrRecords = $medDB->GetAll($strSQL);

	echo json_encode($arrRecords);
	