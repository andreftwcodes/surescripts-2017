<?PHP
set_time_limit(0);
	include("med_config.php");
	include(WEB_ROOT.'base/structures/OutMessage.php');
	include(WEB_ROOT.'base/structures/InMessage.php');
	include(WEB_ROOT.'/test/ProductIFaceTest.php');
	$ini = ini_set("soap.wsdl_cache_enabled", 0);
	
	$strURL				=	"http:
	$objProductIFace	=	new TestProductIFace($strURL);
	
	
	
	
	
	
	
	
	try{
	$outMessages =  $objProductIFace->objService->queryInBoxMessages(array(
											'MeditabID'=>null,
											'MeditabTranID'=>null,
											'FromID'=>null,
											'ToID'=>'6581823195002',
											'MessageStatus'=>null,
											'MeditabResponseStatus'=>null,
											'SentTimeFrom'=>null,
											'SentTimeTo'=>null,
											'MTxRecordLimit'=>null,
											'MeditabTranIDFrom'=>null,
											'MeditabTranIDTo'=>null,
										'MTxTranIDFrom'=>null,
										'MTxTranIDTo'=>null,
										'SmsVersion'=>null,
										'AppName'=>null,
			'AppVersion'=>null,
			'VendorName'=>null,
			'MessageID'=>null,
			'RelatedMessageID'=>null
	
											));
	}
	catch(Exception $e)
	{
		$objProductIFace->debugMethod();
	}
	
	echo 'XXXXXXXXXXXXXXX-<br><pre>';
	print_r($outMessages);
	
	exit;
					
	
	
	for($i= 0; $i<1; $i++)
	{
		$objProductIFace->sendToOutBox(1);
	}
	
	
	
	
	
	
	

?>