<?php


class MedRequest
{
	public $Channel;
	public $Headers;
	
	
	public function __construct($Host)
	{
		//die('here');
		$this->Channel	=	curl_init();
		//echo $Host;die;
		curl_setopt($this->Channel,CURLOPT_URL,$Host);
	}
	
	
	public function addHeader($Header)
	{
		
		$this->Headers[]=	$Header;
	}
	
	
	public function Post($Data, $ReturnResponse=1, $BasicAuth = NULL)
	{
		curl_setopt($this->Channel, CURLOPT_POST, 1);
		if(count($this->Headers)>0)
		{
			curl_setopt($this->Channel, CURLOPT_HTTPHEADER, $this->Headers);	
		}
		
		/*if($BasicAuth !== NULL && $BasicAuth != "")
		{
			curl_setopt($this->Channel, CURLOPT_USERPWD, $BasicAuth);
		}*/
		
		curl_setopt($this->Channel, CURLOPT_POSTFIELDS, $Data);
		curl_setopt($this->Channel, CURLOPT_RETURNTRANSFER, $ReturnResponse);
		curl_setopt($this->Channel, CURLOPT_VERBOSE, 1);
		curl_setopt($this->Channel, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->Channel, CURLOPT_SSL_VERIFYHOST, 0);
		curl_setopt($this->Channel, CURLOPT_SSLCERT, SS_PEM_FILE);
		curl_setopt($this->Channel, CURLOPT_SSLCERTPASSWD, SS_PEM_PASSWORD);
		return curl_exec($this->Channel);
	}
	
	
	public function Get($BasicAuth = NULL)
	{
		if($BasicAuth !== NULL && $BasicAuth != "")
		{
			curl_setopt($this->Channel, CURLOPT_USERPWD, $BasicAuth);
		}
		curl_setopt($this->Channel, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($this->Channel, CURLOPT_VERBOSE, 1);
		curl_setopt($this->Channel, CURLOPT_SSL_VERIFYPEER, 0);
		curl_setopt($this->Channel, CURLOPT_SSL_VERIFYHOST, 0);
		return curl_exec($this->Channel);
	}

}
?>