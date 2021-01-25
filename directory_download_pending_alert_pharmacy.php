<?php 

	set_time_limit(0);

	include_once('med_config.php');

	include_once(WEB_ROOT.'base/DB.php');

	include_once(WEB_ROOT.'base/MedCommon.php');

	include_once(WEB_ROOT.'base/MedSimpleMailer.php');

	$strSQL = "SELECT DISTINCT(email), c.* FROM client_master c INNER JOIN client_directory_log cl on c.ncpdpid != cl.ncpdpid AND c.email_notification = 1";

	$rsResult = $medDB->GetAll($strSQL);

	if( !empty($rsResult) ) {

		$Mailer = new MedSimpleMailer();

		$strSubject = "Directory Download Error";
		$i = 1;
		$pharmacy_list = '<table border="1" cellpadding="10" style="border-collapse:collapse;">';
		$pharmacy_list .= '<tr><th>#</th><th>Pharmacy</th><th>NCPDPID</th><th>Email</th><th>Address</th><th>Phone</th></tr>';
		foreach ($rsResult as $res) {

			$pharmacy_list .= '<tr><td>'.$i.'</td><td>'.$res['pharmacy_name'].'</td><td>'.$res['ncpdpid'].'</td><td>'.$res['email'].'</td><td>'.$res['address'].'</td><td>'.$res['phone'].'</td></tr>';

			$strBody = "Hello ".$res['pharmacy_name']."," . "<br />" . 
			 "Your directory download is pending." . "<br />" .
			 "Please connect to Server Console and fix the issue.";

			// Email to pharmacy
			$Mailer->sendEmail($res['email'], null, $strSubject, $strBody, 'GMAIL');
			$i++;
		}
		$pharmacy_list .= '</table>';

		// Subject for support email
		$supportSub = ' Pending to download directory';

		// Body for support email
		$supportbody = "Hello," . "<br />" . "<br />" . 
			 "The following Pharmacies have pending to download the directory." . "<br />" . "<br />" .
			 $pharmacy_list;

		// Email to support
		$Mailer->sendEmail('kaushik.parmar@metizsoft.com, dsrtruth051@gmail.com', null, $supportSub, $supportbody, 'GMAIL');
	}
?>