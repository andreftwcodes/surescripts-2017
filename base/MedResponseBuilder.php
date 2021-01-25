<?php


class MedResponseBuilder
{
	private $FunctionType;
	private $UseDefinedDelimiters;
	private $Parameters;
	
	public function __construct($FunctionType, $UseDefinedDelimiters = FALSE)
	{
		$this->FunctionType = $FunctionType;
		$this->UseDefinedDelimiters = $UseDefinedDelimiters;	
	}
	
	public function __get($Key)
	{
		$Key = strtoupper($Key);
		if(isset($this->Parameters[$Key]))
		{
			return $this->Parameters[$Key];
		}
	}
	
	public function __set($Key, $Value)
	{
		$Key = strtoupper($Key);
		$this->Parameters[$Key] = $Value;
	}
	
	public function getEdiFactMessage()
	{
		$EDIFactResponse = '';
		switch($this->FunctionType)
		{
			CASE 'STATUS':
			CASE 'VERIFY':
			CASE 'ERROR':
				if($this->UseDefinedDelimiters === FALSE)
				{
				    
				    
				    $EDIFactResponse = '<Code><<STATUS_CODE>></Code>';
				    

				    if($this->FunctionType == 'ERROR')
				    {
					$EDIFactResponse .= '<DescriptionCode><<ERROR_DESCRIPTION_CODE>></DescriptionCode>';
					$EDIFactResponse .= '<Description><<ERROR_DESCRIPTION>></Description>';
				    }
				}
				else
				{
					
				    
				    
				    $EDIFactResponse = '<Code><<STATUS_CODE>></Code>';
				    
				    
				    if($this->FunctionType == 'ERROR')
				    {
					$EDIFactResponse .= '<DescriptionCode><<ERROR_DESCRIPTION_CODE>></DescriptionCode>';
					$EDIFactResponse .= '<Description><<ERROR_DESCRIPTION>></Description>';
				    }
				    
				}
				
				$ReplaceTo = array(
									'<<MESSAGE_ID>>',
									'<<RELATES_TO_MESSAGE_ID>>',
									'<<FROM_ID>>',
									'<<FROM_IDENTIFIER>>',
									'<<TO_ID>>',
									'<<TO_IDENTIFIER>>',
									'<<SENT_TIME>>',
									'<<MESSAGE_FUNCTION>>',
									'<<STATUS_CODE>>',
									'<<ERROR_DESCRIPTION_CODE>>',
									'<<ERROR_DESCRIPTION>>'
								  );
								  
				$ReplaceWith = array(
									$this->MESSAGE_ID,
									$this->RELATES_TO_MESSAGE_ID,
									$this->FROM_ID,
									$this->FROM_IDENTIFIER,
									$this->TO_ID,
									$this->TO_IDENTIFIER,
									convertToEDIFactTimeFormat($this->SENT_TIME),
									$this->FunctionType,
									$this->STATUS_CODE,
									$this->ERROR_DESCRIPTION_CODE,
									$this->ERROR_DESCRIPTION
									);
							
				$EDIFactResponse = str_replace($ReplaceTo, $ReplaceWith, $EDIFactResponse);
				
				break;
		}
		return $EDIFactResponse;
	}
	
	public function getResponse()
	{
	    
	    
	    
	    
	    
		$XmlResponse = '';
		switch($this->FunctionType)
		{
			CASE 'STATUS':
			CASE 'VERIFY':
			CASE 'ERROR':
				$XmlResponse	=	'<?xml version="1.0" encoding="utf-8"?>
						<Message xmlns="http://www.surescripts.com/messaging" version="010" release="006">
						<Header>
						    <To Qualifier="'.$this->TO_IDENTIFIER.'">' . $this->TO_ID . '</To>
						    <From Qualifier="'.$this->FROM_IDENTIFIER.'">' . $this->FROM_ID . '</From>
						    <MessageID>'.$this->MESSAGE_ID.'</MessageID>
						    <RelatesToMessageID>' . $this->RELATES_TO_MESSAGE_ID . '</RelatesToMessageID>
						    <SentTime>' . getUTCTime($this->SENT_TIME) . '</SentTime>
						    <SenderSoftware>
							<SenderSoftwareDeveloper>'.$this->VENDOR_NAME.'</SenderSoftwareDeveloper>
							<SenderSoftwareProduct>'.$this->APP_NAME.'</SenderSoftwareProduct>
							<SenderSoftwareVersionRelease>'.$this->APP_VERSION.'</SenderSoftwareVersionRelease>
						    </SenderSoftware>
						</Header>
						<Body>
						    <'.$this->FunctionType.'>
						    '.trim($this->getEdiFactMessage()).'
						    </'.$this->FunctionType.'>
						</Body>
						</Message>';
				break;
		}
		return $XmlResponse;
	}
}
?>