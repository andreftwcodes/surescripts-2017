<?php
	include_once(WEB_ROOT . '/test/SoapTest.php');
	
	class queryOutBoxMessages
	{
		public $in;
	}
	
	class addPharmacy
	{
		public $addPharmacy;
	}
	
	class executeQuery
	{
		public $SQL;
		public $LineSeparator;
		public $ColumnSeparator;
		
	}
	
	class request
	{
		
	}
	
	
	class TestProductIFace extends TestService 
	{
		
		public function __construct($strURL,$blnShowFunctionList=true)
		{
			$arrOptions			=	array('trace'=>1, 'user'=>'meditab', 'password'=>'mypwd');
			$this->objService	=	new SoapClient($strURL,$arrOptions);
			if($blnShowFunctionList)
			{
				echo '<pre>';
				print_r($this->objService->__getFunctions());
				echo '</pre>';				
			}
			
		}
		
		public function executeQuery($SQL, $ColumnSeparator = "\t", $LineSeparator = "\r\n")
		{
			$executeQuery = new executeQuery();
			$executeQuery->SQL = $SQL;
			$executeQuery->ColumnSeparator = $ColumnSeparator;
			$executeQuery->LineSeparator = $LineSeparator;
			$objRequest = new executeQuery();
			$objRequest->SQL = $SQL;
			$objRequest->ColumnSeparator = $ColumnSeparator;
			$objRequest->LineSeparator = $LineSeparator;
			
			
			$arrResponse	=	$this->objService->executeQuery($executeQuery);
		
			TestService::debug($arrResponse,'P');
			$this->debugMethod();
			return $arrResponse;
		}
		
		public function testMethod($strMethod)
		{
			
		}
	
		public function queryOutBoxMessages($queryOutBoxMessages)
		{
			$this->printMethodTitle(__METHOD__);
			$x	=	new queryOutBoxMessages();
			$x->in	=	"DTesting";
			try{
				$arrResponse	=	$this->objService->queryOutBoxMessages($queryOutBoxMessages);				
			}catch(Exception $e)
			{
				TestService::debug($arrResponse,'P');				
			}
		
			
			return $arrResponse;
		}
		
		public function sendToOutBox($intNoOfMessageToBeInserted)
		{
			$this->printMethodTitle(__METHOD__);
			
			$arrOutMessages					=	array();
			for($i = 0; $i<$intNoOfMessageToBeInserted; $i++)
			{
				$objOutMessage				=	new OutMessage();
				$objOutMessage->MeditabID	=	'0250029';
				$objOutMessage->MeditabTranID=	rand(time(),1000000);
				$objOutMessage->FromID		=	$objOutMessage->MeditabID;
				$objOutMessage->MessageFrom = 'PRESCRIBER';
				$objOutMessage->ToID		=	rand(time(),500000);
				$objOutMessage->EDIMessage	=	base64_encode('UNA. UIBUNOA00<<MESSAGE_ID_PLACE_HOLDER>>9998887P6099678735001D20091001114652,0UIHSCRIPT008001STATUSSTS010UIT3UIZ1');
				$arrOutMessages[]				=	$objOutMessage;
				
			}
			try
			{
				TestService::debug($this->objService->sendToOutBox($arrOutMessages),'P');
			}
			catch(Exception $e)
			{
				if($blnShowRequestXML)
					$this->showRequestXML();
				if($blnShowResponseXML)
					$this->showResponseXML();
			}
			$this->debugMethod();
		}
		
		public function queryInBoxMessages($ToID,$FromID='',$MessageID='',$RelatedMessageID='',
					$MTxTranIDFrom='', $MTxTranIDTo='', $MTxRecordLimit='', $MessageStatus='', $MeditabResponseStatus='',
					$SmsVersion='',$AppName='',$AppVersion='',$VendorName='',$SentTimeFrom='',$SentTimeTo='',
					$ReceivedTimeFrom='',$ReceivedTimeTo='')
		{
			$this->printMethodTitle(__METHOD__);
			$arrResponse	=	$this->objService->queryInBoxMessages(array(
					'ToID'=>$ToID,
					'FromID'=>$FromID,
			'MessageID'=>$MessageID,
			'RelatedMessageID'=>$RelatedMessageID,
			'MTxTranIDFrom'=>$MTxTranIDFrom, 
			'MTxTranIDTo'=>$MTxTranIDTo, 
			'MTxRecordLimit'=>$MTxRecordLimit, 
			'MessageStatus'=>$MessageStatus, 
			'MeditabResponseStatus'=>$MeditabResponseStatus,
			'SmsVersion'=>$SmsVersion,
			'AppName'=>$AppName,
			'AppVersion'=>$AppVersion,
			'VendorName'=>$VendorName,
			'SentTimeFrom'=>$SentTimeFrom,
			'SentTimeTo'=>$SentTimeTo,
			'ReceivedTimeFrom'=>$ReceivedTimeFrom,
			'ReceivedTimeTo'=>$ReceivedTimeTo));
			TestService::debug($arrResponse);
			$this->debugMethod();
		}
		
		public function sendToInBox($intNoOfMessageToBeInserted)
		{
			$this->printMethodTitle(__METHOD__);
			
			$arrInMessages					=	array();
			for($i = 0; $i<$intNoOfMessageToBeInserted; $i++)
			{
				$objInMessage					=	new InMessage();
				$objInMessage->FromID			=	'0050010';
				$objInMessage->ToID				=	'10020030';
				$objInMessage->MessageID		=	rand(time(),500000);
				$objInMessage->ReletedMessageID	=	$objInMessage->MessageID - 10;
				
				$objInMessage->SentTime			=	'2008-1-1';
				$objInMessage->SmsVersion		=	'1.2';
				$objInMessage->AppName			=	'App Navigator';
				$objInMessage->AppVersion		=	'2.3';
				$objInMessage->VendorName		=	'HP';
				
				$objInMessage->EDIMessage		=	'Really very Long Message from ' . 
													$objInMessage->FromID . ' To ' .
													$objInMessage->ToID . 
													' END OF MESSAGE';
				
				$objInMessage->MessageStatus	=	'Pending';
				$objInMessage->ErrorNote		=	'';	
				

				$arrInMessages[]				=	$objInMessage;
				
			}
			
			TestService::debug($this->objService->sendToInBox($arrInMessages),'E');
			$this->debugMethod();
		}
		
		public function getPharmacy($NCPDPID)
		{
			$this->printMethodTitle(__METHOD__);
			
			TestService::debug($this->objService->getPharmacy(array('NCPDPID'=>$NCPDPID)),'E');
			$this->debugMethod();
		}
		
		public function SearchPharmacy($arrSearchPharmacy)
		{
			$this->printMethodTitle(__METHOD__);
			
			TestService::debug($this->objService->SearchPharmacy($arrSearchPharmacy),'E');
			$this->debugMethod();
		}
		public function SearchPrescriber($arrSearchPrescriber)
		{
			$this->printMethodTitle(__METHOD__);
			
			TestService::debug($this->objService->SearchPrescriber($arrSearchPrescriber),'E');
			$this->debugMethod();
		}
		public function addPharmacy($arrAddPharmacy)
		{
			$this->printMethodTitle(__METHOD__);
			$objAddPharmacy		=	new addPharmacy();
			$objAddPharmacy->addPharmacy	=	$arrAddPharmacy;
			TestService::debug($this->objService->addPharmacy($objAddPharmacy),'E');
			$this->debugMethod();
		}
	}
?>