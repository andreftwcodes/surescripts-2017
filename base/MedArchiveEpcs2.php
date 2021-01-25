<?php



 
 
 

class MedArchiveEpcs
{
	var $strOriginalMessage;	
	
	var $objPrescription;		
	var $strPlainText;
	
	var $strSignedText;			
	
	var $strMessageDigest;		
	
	var $strPublicKey;
	
	var $strWebServiceStatus;	
	
	var $strWebServiceUrl;		
	
	var $strDirectoryPath;		
	
	var $strSpi;				
	
	var $strMessageId;			
	
	var $strError;				
	
	
	function __construct($strPrescriptionXml,$strWebServiceUrl)
	{
		
		$this->strOriginalMessage	=	$strPrescriptionXml;
		
		
		$objPrescriptionXml	=	new SimpleXMLElement($strPrescriptionXml);
		
		if($objPrescriptionXml	!== false)
		{

			
			$arrNamespaces = $objPrescriptionXml->getDocNamespaces();
			$objPrescriptionXml->registerXPathNamespace('p', $arrNamespaces['']);
			
			
			$this->objPrescription	= 	$objPrescriptionXml;
			
			$this->strWebServiceUrl =	$strWebServiceUrl;
			
			$this->strDirectoryPath	=	WEB_ROOT.'signed_data/';
			
		}
		
	}
	
	
	public function archivePrescription()
	{
		if($this->objPrescription !== false)
		{
			$this->strSpi		=	$this->objPrescription->Header->From;	
			
			$this->strMessageId	=	$this->objPrescription->Header->MessageID; 
			
			
			$this->strPlainText = $this->createPlainTextString();
			
			
			$intCallWebServiceResult = $this->callWebService();
			
			
			if($intCallWebServiceResult == 1)
			{
				if($this->saveToFile() !== false)
				{
					return 1;
				}
			}
			else
			{
				return 0;
			}
				
		}
		return -1;
	}
	
	
	
	private function callWebService()
	{
		if($this->strPlainText != "")
		{
			
			try 
			{
				$objWs	=	new SoapClient($this->strWebServiceUrl, array('trace' => 0,'exceptions' => true));
			}
			catch(SoapFault $fault)
			{
				$this->strError	=	"Error calling Web Service for signing EPCS document.";
				return false;
			}
			
			$arrInput	=	array("SignPrescription"	=>	array("strData"	=>	$this->strPlainText, "strCompanyName" => "meditab"));
			
			
			try 
			{
				$objWsResult = $objWs->__soapcall('SignPrescription', $arrInput);
			}
			catch(SoapFault $fault)
			{
				$this->strError	=	"Error calling Web Service for signing EPCS document.";
				return false;
			}
				
			
			$arrWsResult	=	$objWsResult->SignPrescriptionResult->string;
		
			$this->strWebServiceStatus				=	$arrWsResult[0];
					
			
			if(strtolower($this->strWebServiceStatus) == "success")
			{
				$this->strPublicKey					=	$arrWsResult[2];
				$this->strMessageDigest				=	$arrWsResult[3];
				$this->strSignedText				=	$arrWsResult[4];
				
				return 1;
			}
			else
			{
				$this->strError		=	$arrWsResult[1];
			}
		}
		return false;
	}
	
	
	public function getSignedText()
	{
		return $this->strSignedText;
	}
	
	
	public function getMessageDigest()
	{
		return $this->strMessageDigest;
	}
	
	
	public function getPlainText()
	{
		return $this->strPlainText;
	}

	
	public function getError()
	{
		return $this->strError;
	}
	
	
	private function createPlainTextString()
	{
		$arrFields = array();
		
		$arrFields[0]	=	"/p:Message/p:Body/*/p:Prescriber/p:Identification/p:DEANumber"; 
		$arrFields[1]	=	"/p:Message/p:Body/*/p:Prescriber/p:Identification/p:SocialSecurity"; 
		$arrFields[2]	=	"/p:Message/p:Body/*/p:Prescriber/p:Name/p:LastName"; 
		$arrFields[3]	=	"/p:Message/p:Body/*/p:Prescriber/p:Name/p:FirstName"; 
		$arrFields[4]	=	"/p:Message/p:Body/*/p:Prescriber/p:Address/p:AddressLine1"; 
		$arrFields[5]	=	"/p:Message/p:Body/*/p:Prescriber/p:Address/p:AddressLine2";	
		$arrFields[6]	=	"/p:Message/p:Body/*/p:Prescriber/p:Address/p:City";	
		$arrFields[7]	=	"/p:Message/p:Body/*/p:Prescriber/p:Address/p:State"; 
		$arrFields[8]	=	"/p:Message/p:Body/*/p:Prescriber/p:Address/p:ZipCode";	
		
		
		$arrFields[9]	=	"/p:Message/p:Body/*/p:Patient/p:Name/p:LastName";	
		$arrFields[10]	=	"/p:Message/p:Body/*/p:Patient/p:Name/p:FirstName";	
		$arrFields[11]	=	"/p:Message/p:Body/*/p:Patient/p:Address/p:AddressLine1";	
		$arrFields[12]	=	"/p:Message/p:Body/*/p:Patient/p:Address/p:AddressLine2";	
		$arrFields[13]	=	"/p:Message/p:Body/*/p:Patient/p:Address/p:City";	
		$arrFields[14]	=	"/p:Message/p:Body/*/p:Patient/p:Address/p:State";	
		$arrFields[15]	=	"/p:Message/p:Body/*/p:Patient/p:Address/p:ZipCode";	
		
		
		$arrFields[16]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:DrugDescription";	
		$arrFields[17]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:DrugCoded/p:Strength";	
		$arrFields[18]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:Quantity/p:Value";	
		$arrFields[19]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:Directions";	
		
		$arrFields[20]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:WrittenDate/p:Date";	
		$arrFields[21]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:WrittenDate/p:Date";	
		$arrFields[22]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:WrittenDate/p:Date";	
		
		$arrFields[23]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:WrittenDate/p:DateTime";	
		$arrFields[24]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:WrittenDate/p:DateTime";	
		$arrFields[25]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:WrittenDate/p:DateTime";	
		
		$arrFields[26]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:EffectiveDate/p:Date";	
		$arrFields[27]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:EffectiveDate/p:Date";	
		$arrFields[28]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:EffectiveDate/p:Date";	
		
		$arrFields[29]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:EffectiveDate/p:DateTIme";	
		$arrFields[30]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:EffectiveDate/p:DateTime";	
		$arrFields[31]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:EffectiveDate/p:DateTime";	
		$arrFields[32]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:Refills/p:Qualifier";	
		$arrFields[33]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:Refills/p:Value";	
		$arrFields[34]	=	"/p:Message/p:Body/*/p:MedicationPrescribed/p:Note";
		
		$arrValues	= array();
		foreach($arrFields as $strIndex => $strFieldPath)
		{
			
			$arrFieldNodes	=	$this->objPrescription->xpath($strFieldPath);
			
			if($arrFieldNodes !== false && count($arrFieldNodes) > 0)
			{
				foreach($arrFieldNodes as $objNode)
				{
					$strValue	=	(string)$objNode;
					
					
					switch($strIndex)
					{
						case '1':
							$strValue	=	str_replace('-','',$strValue);	
						case '20':
						case '23':
						case '26':
						case '29':
							$strValue	=	substr($strValue,0,4);			
							break;
						case '21':
						case '24':
						case '27':
						case '30':
							$strValue	=	substr($strValue,5,2);			
							break;
						case '22':
						case '25':
						case '28':
						case '31':
							$strValue	=	substr($strValue,8,2);			
							break;
							
					} 
					$arrValues[$strIndex]	=	$strValue;
				}
			}
		}
		return implode('',$arrValues);
	}
	
	/**Save the signed text to file
	 * @return boolean
	 */
	private function saveToFile()
	{
		$dtToday	=	date("Y-m-d");
		$arrToday	=	@explode("-",$dtToday);
		
		$strDirectoryPath	= $this->strDirectoryPath.@implode('\\',$arrToday);
		if(file_exists($strDirectoryPath) === false)
		{
			mkdir($strDirectoryPath,0777,true);
		}
		
		$strSignedData	=	"<SignedMessage>".$this->strSignedText."</SignedMessage>\n\n";
		$strSignedData	.=	"<MessageDigest>".$this->strMessageDigest."</MessageDigest>\n\n";
		$strSignedData	.=	"<PublicKey>".$this->strPublicKey."</PublicKey>\n\n";
		$strSignedData	.=	"<PlainText>".$this->strPlainText."</PlainText>\n\n";
		
		$strDate	=	date("YmdHis");
		
		
		
		
		
		$strMessageIdFilename	=	md5($this->strMessageId);
		$strFilename		 	=	"NABP_".$strDate."_".$strMessageIdFilename;
		
		
		
		
		$blnOriginalMessageSaved	=	file_put_contents($strDirectoryPath.'\\'.$strFilename.'.msg',$this->strOriginalMessage, FILE_APPEND);
		
		$blnSignedMessageSaved	=	file_put_contents($strDirectoryPath.'\\'.$strFilename.'.epcs',$strSignedData, FILE_APPEND);
		
		if($blnOriginalMessageSaved !== false && $blnSignedMessageSaved !== false)
		{
			return true;
		}
		else
		{
			$this->strError = "Error saving to file.";
		}
		return false;
	}
}
?>