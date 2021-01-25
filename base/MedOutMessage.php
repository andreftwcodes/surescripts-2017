<?php

class MedOutMessage
{
	
	public $MessageID;

	
	public $TranID;

	
	public $MessageType;

	
	public $Params;

	
	public $XmlMessage;

	
	public $MessageData;
	
	public function __construct($MessageType)
	{
		$this->Params = array();
		$this->MessageType	=	$MessageType;
		$this->getLogTranID();
	}

	
	public function __set($Key,$Value)
	{
		$Key				=	strtoupper(trim($Key));
		if($Key != '')
		{
			$this->Params[$Key] = trim($Value);
		}
	}

	
	public function __get($Key)
	{
		$Key				=	strtoupper(trim($Key));
		
		if(isset($this->Params[$Key]))
		{
			if(is_array($this->Params[$Key]))
			{
				return $this->Params[$Key]; 
			}
			else
			{
				return trim($this->Params[$Key]);				
			}
		}
	}

	
	public function getTranId()
	{
		return $this->TranID;
	}
	public function getMessage()
	{
	    
	    
	    
	    
		if($this->XmlMessage == '')
		{
			
			$this->generateMessageID();
				
			$XmlMessage		=	 '<?xml version="1.0" encoding="utf-8"?>' . "\n";
			switch($this->MessageType)
			{
				case 'DIRECTORY_DOWNLOAD_NIGHTLY':
					$XmlMessage	.= '<DirectoryMessage xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" DatatypesVersion="20170715" TransportVersion="20170715" TransactionDomain="DIRECTORY" TransactionVersion="20170715" StructuresVersion="20170715" ECLVersion="20170715" Version="006" Release="001">' . "\n" ;
					$XmlMessage	.= $this->getDirectoryDownloadHeader();
					$XmlMessage	.=	'<Body>' . "\n" . 
							'<DirectoryDownload>' . "\n" .
							'<AccountID>'. $this->AccountID .'</AccountID>' . "\n" .
							'<DownloadType>'. $this->DownloadType .'</DownloadType>' . "\n" .
							'<DirectoryDate>'. $this->DirectoryDate .'</DirectoryDate>' . "\n" .
							'<VersionID>'. $this->VersionID .'</VersionID>' . "\n" .
							'</DirectoryDownload>'.
							'</Body>'.
							'</DirectoryMessage>';
					break;
					
				case 'DIRECTORY_DOWNLOAD_FULL':
					$XmlMessage	.= '<DirectoryMessage xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" DatatypesVersion="20170715"'.
									' TransportVersion="20170715" TransactionDomain="DIRECTORY" TransactionVersion="20170715"'.
									' StructuresVersion="20170715" ECLVersion="20170715" Version="006" Release="001">'."\n";
					$XmlMessage	.= $this->getDirectoryDownloadHeader();
					$XmlMessage	.=	'<Body>' . "\n" .
							'<DirectoryDownload>' . "\n" .
							'<AccountID>'. $this->AccountID.'</AccountID>' . "\n" .
							'<DownloadType>'. $this->DownloadType .'</DownloadType>' . "\n" .
							'<VersionID>'. $this->VersionID .'</VersionID>' . "\n" .
							'</DirectoryDownload>' . "\n".
							'</Body>' . "\n".
							'</DirectoryMessage>';
					break;

				case 'MESSAGE_FROM_PRESCRIBER':
				    
					
					
					if($this->MessageVersion != MESSAGE_VERSION)
					{
					    
					    $From		= $this->From .'.spi@surescripts.com';
					    $To			= $this->To .'.ncpdp@surescripts.com';
					    $XmlMessage .= '<Message xmlns="http://www.surescripts.com/messaging">' . "\n";
					    $XmlMessage .= MedOutMessage::getOutMessageHeader($From,$To);		
					    $XmlMessage .= '<Body>' . "\n" .
							    '<EDIFACTMessage>'. $this->EDIMessage .'</EDIFACTMessage>' . "\n";
					    
					}
					else
					{
					    
					    
					    $strEDIMessage = base64_decode($this->EDIMessage);
					    $strEDIMessage = str_replace("<<MESSAGE_ID_PLACE_HOLDER>>",$this->MessageID , $strEDIMessage);
					    $strEDIMessage = str_replace("<<SOFTWARE_DEVELOPER_PLACE_HOLDER>>",$this->VendorName , $strEDIMessage);
					    $strEDIMessage = str_replace("<<SOFTWARE_PRODUCT_PLACE_HOLDER>>",$this->AppName , $strEDIMessage);
					    $strEDIMessage = str_replace("<<SOFTWARE_VERSION_PLACE_HOLDER>>",$this->AppVersion , $strEDIMessage);
					    $strEDIMessage = str_replace("<<SENT_TIME_PLACE_HOLDER>>",getUTCTime() , $strEDIMessage);
					    $XmlMessage = $strEDIMessage;
					     
					}
					
					break;
					
				case 'MESSAGE_FROM_PHARMACY':
				    
					/*if($this->MessageVersion != MESSAGE_VERSION)
					{
					    $From		= $this->From .'.ncpdp@surescripts.com';
					    $To			= $this->To .'.spi@surescripts.com';
					    $XmlMessage .= '<Message xmlns="http://www.surescripts.com/messaging">' . "\n";
					    $XmlMessage .= MedOutMessage::getOutMessageHeader($From,$To);		
					    $XmlMessage .= '<Body>' . "\n" .
							    '<EDIFACTMessage>'. $this->EDIMessage .'</EDIFACTMessage>' . "\n";
					}
					else
					{
					   $strEDIMessage = base64_decode($this->EDIMessage);
					   $strEDIMessage = str_replace("<<MESSAGE_ID_PLACE_HOLDER>>",$this->MessageID , $strEDIMessage);
					   $strEDIMessage = str_replace("<<SOFTWARE_DEVELOPER_PLACE_HOLDER>>",$this->VendorName , $strEDIMessage);
					   $strEDIMessage = str_replace("<<SOFTWARE_PRODUCT_PLACE_HOLDER>>",$this->AppName , $strEDIMessage);
					   $strEDIMessage = str_replace("<<SOFTWARE_VERSION_PLACE_HOLDER>>",$this->AppVersion , $strEDIMessage);
					   $strEDIMessage = str_replace("<<SENT_TIME_PLACE_HOLDER>>",getUTCTime() , $strEDIMessage);
					   $XmlMessage = $strEDIMessage;
					}*/
					
					$strEDIMessage = base64_decode($this->EDIMessage);
				    $strEDIMessage = str_replace("<<MESSAGE_ID_PLACE_HOLDER>>",$this->MessageID , $strEDIMessage);
				    $strEDIMessage = str_replace("<<SOFTWARE_DEVELOPER_PLACE_HOLDER>>",$this->VendorName , $strEDIMessage);
				    $strEDIMessage = str_replace("<<SOFTWARE_PRODUCT_PLACE_HOLDER>>",$this->AppName , $strEDIMessage);
				    $strEDIMessage = str_replace("<<SOFTWARE_VERSION_PLACE_HOLDER>>",$this->AppVersion , $strEDIMessage);
				    $strEDIMessage = str_replace("<<SENT_TIME_PLACE_HOLDER>>",getUTCTime() , $strEDIMessage);
				    $XmlMessage = $strEDIMessage;
					
					break;
				
				case 'ADD_PRESCRIBER':
					$XmlMessage	.= '<Message xmlns="http://www.surescripts.com/messaging" version="004" release="004">' . "\n" ;
					$XmlMessage	.= $this->getDirectoryDownloadHeader();
					$XmlMessage	.=	'<Body>' . "\n";
					$XmlMessage	.=	$this->getMessageBody($this->MessageType);
					break;
				case 'UPDATE_PRESCRIBER':
					$XmlMessage	.= '<Message xmlns="http://www.surescripts.com/messaging" version="004" release="004">' . "\n" ;
					$XmlMessage	.= $this->getDirectoryDownloadHeader();
					$XmlMessage	.=	'<Body>' . "\n";
					$XmlMessage	.=	$this->getMessageBody($this->MessageType);
					break;
				case 'ADD_PRESCRIBER_LOCATION':
					$XmlMessage	.= '<Message xmlns="http://www.surescripts.com/messaging" version="004" release="004">' . "\n" ;
					$XmlMessage	.= $this->getDirectoryDownloadHeader();
					$XmlMessage	.=	'<Body>' . "\n";
					$XmlMessage	.=	$this->getMessageBody($this->MessageType);
					break;
				case 'UPDATE_PRESCRIBER_LOCATION':
					$XmlMessage	.= '<Message xmlns="http://www.surescripts.com/messaging" version="004" release="004">' . "\n" ;
					$XmlMessage	.= $this->getDirectoryDownloadHeader();
					$XmlMessage	.=	'<Body>' . "\n";
					$XmlMessage	.=	$this->getMessageBody($this->MessageType);
					break;
				case 'ADD_PHARMACY':
					$XmlMessage .= '<Message xmlns="http://www.surescripts.com/messaging" version="004" release="004">' . "\n" ;
					$XmlMessage	.= $this->getDirectoryDownloadHeader();
					$XmlMessage	.=	'<Body>' . "\n";
					$XmlMessage	.=	$this->getMessageBody($this->MessageType);
					break;
				case 'UPDATE_PHARMACY':
					$XmlMessage .= '<Message xmlns="http://www.surescripts.com/messaging" version="004" release="004">' . "\n" ;
					$XmlMessage	.= $this->getDirectoryDownloadHeader();
					$XmlMessage	.=	'<Body>' . "\n";
					$XmlMessage	.=	$this->getMessageBody($this->MessageType);
					break;
				default:
					
					break;
			}
			
			/*if($this->MessageVersion != MESSAGE_VERSION)
			{
			    $XmlMessage .= '</Body>' . "\n" .
					     '</Message>';
			}*/
			
			
			$this->updateOutMessageLog($XmlMessage);
			$this->XmlMessage	=	$XmlMessage;
		}
		
		return $this->XmlMessage;
	}
	
	
	private function getMessageBody($MessageType)
	{
		switch($MessageType)
		{
			case 'ADD_PRESCRIBER':
				$Xml  =  '<AddPrescriber>';
				$Xml .=	$this->getMessageBody('PRESCRIBER');
				$Xml .= '</AddPrescriber>';
				break;
				
				
			case 'UPDATE_PRESCRIBER':
				$Xml  =	'<UpdatePrescriber>';
				$Xml .=	$this->getMessageBody('PRESCRIBER');
				$Xml .=	'</UpdatePrescriber>';
				break;
				
				
			case 'ADD_PRESCRIBER_LOCATION':
				$Xml  =	'<AddPrescriberLocation>';
				$Xml .=	$this->getMessageBody('PRESCRIBER');
				$Xml .=	'</AddPrescriberLocation>';
				break;
				
				
			case 'UPDATE_PRESCRIBER_LOCATION':
				$Xml  =	'<UpdatePrescriberLocation>';
				$Xml .=	$this->getMessageBody('PRESCRIBER');
				$Xml .=	'</UpdatePrescriberLocation>';
				break;
				
			
			case 'ADD_PHARMACY':
				$Xml  =	'<AddPharmacy>';
				$Xml .=	$this->getMessageBody('PHARMACY');
				$Xml .=	'</AddPharmacy>';
				break;
			
				
			case 'UPDATE_PHARMACY':
				$Xml  =	'<UpdatePharmacy>';
				$Xml .=	$this->getMessageBody('PHARMACY');
				$Xml .=	'</UpdatePharmacy>';
				break;

				
			case 'PRESCRIBER':
				$Xml .=	'<Prescriber>';
				$Xml .=	'<DirectoryInformation>';
				$Xml .=	$this->getXmlElement('PortalID');
				$Xml .=	$this->getXmlElement('AccountID');
				$Xml .=	$this->getXmlElement('BackupPortalID');
				$Xml .=	$this->getXmlElement('ServiceLevel');
				
				$Xml .=	$this->getXmlElement('ActiveStartTime');
				$Xml .=	$this->getXmlElement('ActiveEndTime');
				$Xml .=	$this->getXmlElement('SpecialtyID');
				$Xml .=	'</DirectoryInformation>';
				
				$Xml .=	'<Identification>';
				
				$MaxIDAllowed = 50;   
				$IDElementCount = 0; 
				
				
				
				
				$Xml .=	$this->getXmlElement('SPI');
				
				
				$Xml .=	$this->getXmlElement('DEANumber');
				$IDElementCount += 1;
				
				if($this->FileID != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('FileID');
				}
				if($this->StateLicenseNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('StateLicenseNumber');
				}
				if($this->MedicareNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('MedicareNumber');
				}
				if($this->MedicaidNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('MedicaidNumber');
				}
				if($this->DentistLicenseNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('DentistLicenseNumber');
				}
				if($this->UPIN != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('UPIN');
				}
				if($this->PPONumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('PPONumber');
				}
				if($this->SocialSecurity != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('SocialSecurity');
				}
				if($this->NPI != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('NPI');
				}
				if($this->PriorAuthorization != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('PriorAuthorization');
				}
				if($this->MutuallyDefined != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('MutuallyDefined');
				}
				$Xml .= '</Identification>';

				$Xml .=	$this->getXmlElement('ClinicName');
				
				$Xml .=	'<Name>';
				$Xml .=	$this->getXmlElement('LastName');
				$Xml .=	$this->getXmlElement('FirstName');
				$Xml .=	$this->getXmlElement('MiddleName');
				$Xml .=	$this->getXmlElement('Suffix');
				$Xml .=	$this->getXmlElement('Prefix');
				$Xml .=	'</Name>';
				
				if($this->Qualifier != '' || $this->SpecialtyCode != '')
				{
					$Xml .= '<Specialty>';
					$Xml .= $this->getXmlElement('Qualifier');
					$Xml .= $this->getXmlElement('SpecialtyCode');
					$Xml .= '</Specialty>';
				}
				
				
				
				$PrescriberAgent .=	$this->getXmlElement('PrescriberAgent.LastName');
				$PrescriberAgent .=	$this->getXmlElement('PrescriberAgent.FirstName');
				$PrescriberAgent .=	$this->getXmlElement('PrescriberAgent.MiddleName');
				$PrescriberAgent .=	$this->getXmlElement('PrescriberAgent.Suffix');
				$PrescriberAgent .=	$this->getXmlElement('PrescriberAgent.Prefix');		
				$Xml .= $this->getXmlElement('PrescriberAgent', true, $PrescriberAgent);
				
				$Xml .=	'<Address>';
				$Xml .=	$this->getXmlElement('AddressLine1');
				$Xml .=	$this->getXmlElement('AddressLine2');
				$Xml .=	$this->getXmlElement('City');
				$Xml .=	$this->getXmlElement('State');
				$Xml .=	$this->getXmlElement('ZipCode');
				$Xml .=	'</Address>';
				
				$Xml .=	$this->getXmlElement('Email');
				
				$Xml .=	'<PhoneNumbers>';
				$Xml .=	$this->getPhoneNumbers();				
				$Xml .=	'</PhoneNumbers>';
				
				$Xml .=	$this->getXmlElement('DEAAuthorizingName');
				
				$Xml .=	'</Prescriber>';
				break;
				
				
			case 'PHARMACY':
				$Xml  =	'<Pharmacy>';
				$Xml .= '<DirectoryInformation>';
				$Xml .=	$this->getXmlElement('PortalID');
				$Xml .=	$this->getXmlElement('AccountID');
				$Xml .=	$this->getXmlElement('BackupPortalID');
				$Xml .=	$this->getXmlElement('ServiceLevel');
				
				$Xml .=	$this->getXmlElement('ActiveStartTime');
				$Xml .=	$this->getXmlElement('ActiveEndTime');
				$Xml .=	$this->getXmlElement('SpecialtyID');
				$Xml .= '</DirectoryInformation>';
				
				$Xml .= '<Identification>';
				
				$Xml .=	$this->getXmlElement('NCPDPID',false);
				$MaxIDAllowed = 50;   
				$IDElementCount = 0; 
				if($this->FileID != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('FileID');
				}
				if($this->StateLicenseNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('StateLicenseNumber');
				}
				if($this->MedicareNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('MedicareNumber');
				}
				if($this->MedicaidNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('MedicaidNumber');
				}
				if($this->MutuallyDefined != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('MutuallyDefined');
				}
				if($this->PPONumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('PPONumber');
				}
				if($this->PayerID != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('PayerID');
				}
				if($this->BINLocationNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('BINLocationNumber');
				}
				if($this->DEANumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('DEANumber');
				}
				if($this->HIN != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('HIN');
				}
				if($this->SecondaryCoverage != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('SecondaryCoverage');
				}
				if($this->NAICCode != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('NAICCode');
				}
				if($this->PromotionNumber != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('PromotionNumber');
				}
				if($this->SocialSecurity != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('SocialSecurity');
				}
				if($this->NPI != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('NPI');
				}
				if($this->PriorAuthorization != '' && $IDElementCount < $MaxIDAllowed)
				{
					$IDElementCount += 1;
					$Xml .=	$this->getXmlElement('PriorAuthorization');
				}
				$Xml .= '</Identification>';
				
				$Xml .=	$this->getXmlElement('StoreName');
				$Xml .=	$this->getXmlElement('StoreNumber');
				
				
				$Pharmacist	 = $this->getXmlElement('LastName');
				$Pharmacist	.= $this->getXmlElement('FirstName');
				$Pharmacist	.= $this->getXmlElement('MiddleName');
				$Pharmacist	.= $this->getXmlElement('Suffix');
				$Pharmacist	.= $this->getXmlElement('Prefix');
				$Xml .= $this->getXmlElement('Pharmacist', true, $Pharmacist);
				
				$PharmacistAgent  = $this->getXmlElement('PharmacistAgent.LastName');
				$PharmacistAgent .= $this->getXmlElement('PharmacistAgent.FirstName');
				$PharmacistAgent .= $this->getXmlElement('PharmacistAgent.MiddleName');
				$PharmacistAgent .= $this->getXmlElement('PharmacistAgent.Suffix');
				$PharmacistAgent .= $this->getXmlElement('PharmacistAgent.Prefix');
				$Xml .= $this->getXmlElement('PharmacistAgent', true, $PharmacistAgent);
				
				$Xml .=	'<Address>';
				$Xml .=	$this->getXmlElement('AddressLine1');
				$Xml .=	$this->getXmlElement('AddressLine2');
				$Xml .=	$this->getXmlElement('City');
				$Xml .=	$this->getXmlElement('State');
				$Xml .=	$this->getXmlElement('ZipCode');
				$Xml .=	'</Address>';
				
				$Xml .=	$this->getXmlElement('TwentyFourHourFlag');	
				$Xml .=	$this->getXmlElement('CrossStreet');
				$Xml .=	$this->getXmlElement('Email');
				
				$Xml .=	'<PhoneNumbers>';
				$Xml .=	$this->getPhoneNumbers();				
				$Xml .=	'</PhoneNumbers>';
				$Xml .=	'</Pharmacy>';
				break;
		}
		return $Xml;
	}
	
	
	private function getPhoneNumbers()
	{
		$Xml				=	'';
		if($this->Phone!='')
		{
			$Xml	.=	'<Phone>';
			$Xml	.=	'<Number>' . $this->Phone . '</Number>';
			$Xml	.=	'<Qualifier>TE</Qualifier>';
			$Xml	.=	'</Phone>';
		}
		
		if($this->Fax!='')
		{
			$Xml	.=	'<Phone>';
			$Xml	.=	'<Number>' . $this->Fax . '</Number>';
			$Xml	.=	'<Qualifier>FX</Qualifier>';
			$Xml	.=	'</Phone>';
		}
		
		if($this->phone_alt1!='')
		{
			$Xml	.=	'<Phone>';
			$Xml	.=	'<Number>' . $this->phone_alt1 . '</Number>';
			$Xml	.=	'<Qualifier>' . $this->phone_alt1_qualifier . '</Qualifier>';
			$Xml	.=	'</Phone>';
		}
		
		if($this->phone_alt2!='')
		{
			$Xml	.=	'<Phone>';
			$Xml	.=	'<Number>' . $this->phone_alt2 . '</Number>';
			$Xml	.=	'<Qualifier>' . $this->phone_alt2_qualifier . '</Qualifier>';
			$Xml	.=	'</Phone>';
		}
		
		if($this->phone_alt3!='')
		{
			$Xml	.=	'<Phone>';
			$Xml	.=	'<Number>' . $this->phone_alt3 . '</Number>';
			$Xml	.=	'<Qualifier>' . $this->phone_alt3_qualifier . '</Qualifier>';
			$Xml	.=	'</Phone>';
		}
		
		if($this->phone_alt4!='')
		{
			$Xml	.=	'<Phone>';
			$Xml	.=	'<Number>' . $this->phone_alt4 . '</Number>';
			$Xml	.=	'<Qualifier>' . $this->phone_alt4_qualifier . '</Qualifier>';
			$Xml	.=	'</Phone>';
		}
		
		if($this->phone_alt5!='')
		{
			$Xml	.=	'<Phone>';
			$Xml	.=	'<Number>' . $this->phone_alt5 . '</Number>';
			$Xml	.=	'<Qualifier>' . $this->phone_alt5_qualifier . '</Qualifier>';
			$Xml	.=	'</Phone>';
		}
		
		if($this->phone_alt6!='')
		{
			$Xml	.=	'<Phone>';
			$Xml	.=	'<Number>' . $this->phone_alt6 . '</Number>';
			$Xml	.=	'<Qualifier>' . $this->phone_alt6_qualifier . '</Qualifier>';
			$Xml	.=	'</Phone>';
		}
		return $Xml;
	}
	
	
	private function getXmlElement($Element, $DontReturnIfNoValueAvailable = true, $DataValue = NULL)
	{
		$Value				=	'';
		
		
		if($DataValue === NULL)
		{
			$Value			=	$this->__get($Element);
		}
		else
		{
			$Value 			=	$DataValue;	
		}
		
		
		
		$ElementName = $Element;
		if(strpos($Element,'.') !== FALSE)
		{
			
			$ElementParts = explode('.',$Element);
			
			$ElementName = $ElementParts[1];
		}	
		$XmlElement			=	'';
		if($DontReturnIfNoValueAvailable === true)
		{
			if($Value != '')
			{
				$XmlElement		=	'<' . $ElementName . '>' . $Value . '</' . $ElementName . '>';
			}
		}
		else
			$XmlElement		=	'<' . $ElementName . '>' . $Value . '</' . $ElementName . '>';
		return $XmlElement;
	}
	
	
	private function getDirectoryDownloadHeader()
	{
		
		
		/*$XmlMessage = 		'<Header>' . "\n" . 
							'<To>mailto:'. $this->To .'.dp@surescripts.com</To>' . "\n" . 
							'<From>mailto:'. $this->From .'.dp@surescripts.com</From>' . "\n" . 
							'<MessageID>'. $this->MessageID .'</MessageID>' . "\n" .
							'<SentTime>'. $this->SentTime .'</SentTime>' . "\n" .
							'<Security>' . "\n" .
							'<UsernameToken>' . "\n" .
							'<Username>'. $this->Username .'</Username>' . "\n" .
							'<Password>'.
							
							base64_encode(sha1(mb_convert_encoding(strtoupper($this->Password), 'utf-16le'), TRUE)) .
							'</Password>' . "\n" .
							'<Nonce>'. $this->TranID .'</Nonce>' . "\n" .
							'<Created>'. $this->Created .'</Created>' . "\n" .
							'</UsernameToken>' . "\n" .
							'</Security>' . "\n" .
							'</Header>' . "\n";*/
							
		$msg = 		'<Header>' . "\n" . 
							'<To Qualifier="ZZZ">'. $this->To .'</To>' . "\n" . 
							'<From Qualifier="ZZZ">'. $this->From .'</From>' . "\n" . 
							'<MessageID>'. $this->MessageID .'</MessageID>' . "\n" .
							'<SentTime>'. $this->SentTime .'</SentTime>' . "\n" .
							'<SenderSoftware>'. "\n".
							'<SenderSoftwareDeveloper>'. $this->VendorName .'</SenderSoftwareDeveloper>' . "\n" .
							'<SenderSoftwareProduct>'. $this->ProductName .'</SenderSoftwareProduct>' . "\n" .
							'<SenderSoftwareVersionRelease>'. $this->SoftwareVersion .'</SenderSoftwareVersionRelease>' . "\n" .
							'</SenderSoftware>'. "\n".
							'</Header>' . "\n";
		return $msg;
	}
	
	
	private function getOutMessageHeader($From,$To)
	{
		
		$strAppVersion = '';
		if($this->VendorName != "" && $this->AppName != "" && $this->AppVersion != "")
		{
			$strAppVersion = '<AppVersion>' . "\n" .
							'<VendorName>'. $this->VendorName .'</VendorName>' . "\n" .
							'<ApplicationName>'. $this->AppName .'</ApplicationName>' . "\n" .
							'<ApplicationVersion>'. $this->AppVersion .'</ApplicationVersion>' . "\n" .
							'</AppVersion>' . "\n";
		}
		
		$strElmRelatedToMessageID = '';
		if($this->RelatedToMessageID != "")
		{
			$strElmRelatedToMessageID	=	'<RelatesToMessageID>'. $this->RelatedToMessageID .'</RelatesToMessageID>' . "\n";
		}
		
		$XmlMessage	=		'<Header>' . "\n" .
							'<To>mailto:'. $To .'</To>' . "\n" .
							'<From>mailto:'.$From.'</From>' . "\n" .
							'<MessageID>'. $this->MessageID .'</MessageID>' . "\n" .
							$strElmRelatedToMessageID .
							'<SentTime>'. $this->SentTime .'</SentTime>' . "\n" .
							'<SMSVersion>'.$this->SmsVersion .'</SMSVersion>' . "\n" .
							$strAppVersion . 
							'</Header>' . "\n";
		return $XmlMessage; 
	}
	
	
	private function generateMessageID()
	{
		switch($this->MessageType)
		{
			case 'DIRECTORY_DOWNLOAD_FULL':
			case 'DIRECTORY_DOWNLOAD_NIGHTLY':
				
				
				$this->MessageID = 'MTI' . $this->TranID;
				break;
			case 'OUTBOUND_MESSAGE':
				break;
			case 'ADD_PRESCRIBER':
				$this->MessageID = 'AD' . $this->TranID;
				break;
			case 'UPDATE_PRESCRIBER':
				$this->MessageID = 'UD' . $this->TranID;
				break;
			case 'ADD_PRESCRIBER_LOCATION':
				$this->MessageID = 'ADL' . $this->TranID;
				break;
			case 'UPDATE_PRESCRIBER_LOCATION':
				$this->MessageID = 'UDL' . $this->TranID;
				break;
			case 'ADD_PHARMACY':
				$this->MessageID = 'AP' . $this->TranID;
				break;
			case 'UPDATE_PHARMACY':
				$this->MessageID = 'UP' . $this->TranID;
				break;
		}
	}

	
	private function getLogTranID()
	{
		global $medDB;

		$arrRecord['message_type']	=	$this->MessageType;
		$medDB->AutoExecute(OUTBOUND_MESSAGE_HISTORY,$arrRecord , 'INSERT');
		$this->TranID				=	$medDB->Insert_ID();
	}

	
	private function updateOutMessageLog($XmlMessage)
	{
		global $medDB;
		$arrRecord['message_id']	=	$this->MessageID;
		$arrRecord['message']		=	$XmlMessage;
		$arrRecord['message_params']=	serialize($this->Params);
		$arrRecord['om_tran_id']	=	$this->OMTranID;
		$arrRecord['req_tran_id']	=	$this->ReqTranID;
		$medDB->AutoExecute(OUTBOUND_MESSAGE_HISTORY,$arrRecord , 'UPDATE',"tran_id=".$this->TranID);
	}

	
	public function updateOutMessageResponse($XmlResponse, $EDIFactPartOfResponse)
	{
		global $medDB;
		$arrRecord['immediate_response']			=	$XmlResponse;
		$arrRecord['immediate_response_edifact']	=	$EDIFactPartOfResponse;
		$medDB->AutoExecute(OUTBOUND_MESSAGE_HISTORY,$arrRecord , 'UPDATE',"tran_id=".$this->TranID);
	}
}
?>