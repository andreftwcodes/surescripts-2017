<?php
	
	
	class TestService
	{
		public $blnShowRequestResponse;
		public $objService;
		public static function Debug($message,$doWhat='P')
		{
			switch($doWhat)
			{
				case 'P':
					echo '<pre>';
					print_r($message);
					echo '</pre>';
					break;
				case 'E':
					echo '<pre>';
					print_r($message);
					echo '</pre>';
					break;
				case 'U':
					echo '<pre>';
					print_r(unserialize($message));
					echo '</pre>';
					break;
			}
		}
		public function showRequestXML()
		{
			echo '<h4 style="color:blue">REQUEST XML</h4>';
			TestService::debug(wordwrap(htmlentities($this->objService->__getLastRequest()),200),'E');
		}
		public function showResponseXML()
		{
			echo '<h4 style="color:blue">RESPONSE XML</h4>';
			TestService::debug(wordwrap(htmlentities($this->objService->__getLastResponse()),200),'E');
		}
		
		public static function printMethodTitle($strMethod)
		{
			echo '<h3 style="color:green">Testing Method: '.$strMethod.'</h3>'; 
		}
		
		public function debugMethod($blnShowRequestXML=true,$blnShowResponseXML=true)
		{
			
			
			
			if($blnShowRequestXML)
				$this->showRequestXML();
			if($blnShowResponseXML)
				$this->showResponseXML();
		}
	}
	
?>