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
			
			$this->strDirectoryPath	=	EPCS_DOCUMENTS_PATH;
			
		}
		
	}
	
	
	public function archivePrescription()
	{
		if($this->objPrescription !== false)
		{
			$this->strSpi		=	$this->objPrescription->Header->From;	
			
			$this->strMessageId	=	$this->objPrescription->Header->MessageID; 
			
			//echo '<pre>';print_r($this->objPrescription);die;
			$this->strPlainText = $this->createPlainTextString();
			//echo $this->strPlainText;die;
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
				//echo '<pre>';print_r($arrWsResult);die;
				return false;
			}
			
			$arrInput	=	array("SignPrescription"	=>	array("strData"	=>	$this->strPlainText, "strCompanyName" => "srxsign"));
			
			
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
		
		// $arrFields[0]	=	"/p:Message/p:Body/*/p:Prescriber/p:NonVeterinarian/p:Identification/p:DEANumber"; 
		$arrFields[0]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Identification/DEANumber"; 
		$arrFields[1]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Identification/SocialSecurity"; 
		$arrFields[2]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Name/LastName"; 
		$arrFields[3]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Name/FirstName"; 
		$arrFields[4]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Address/AddressLine1"; 
		$arrFields[5]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Address/AddressLine2";	
		$arrFields[6]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Address/City";	
		$arrFields[7]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Address/StateProvince"; 
		$arrFields[8]	=	"/Message/Body/*/Prescriber/NonVeterinarian/Address/PostalCode";	
		
		
		$arrFields[9]	=	"/Message/Body/*/Patient/HumanPatient/Name/LastName";	
		$arrFields[10]	=	"/Message/Body/*/Patient/HumanPatient/Name/FirstName";	
		$arrFields[11]	=	"/Message/Body/*/Patient/HumanPatient/Address/AddressLine1";	
		$arrFields[12]	=	"/Message/Body/*/Patient/HumanPatient/Address/AddressLine2";	
		$arrFields[13]	=	"/Message/Body/*/Patient/HumanPatient/Address/City";	
		$arrFields[14]	=	"/Message/Body/*/Patient/HumanPatient/Address/StateProvince";	
		$arrFields[15]	=	"/Message/Body/*/Patient/HumanPatient/Address/PostalCode";	
		
		
		$arrFields[16]	=	"/Message/Body/*/MedicationPrescribed/DrugDescription";	
		$arrFields[17]	=	"/Message/Body/*/MedicationPrescribed/DrugCoded/Strength";	
		$arrFields[18]	=	"/Message/Body/*/MedicationPrescribed/Quantity/Value";	
		$arrFields[19]	=	"/Message/Body/*/MedicationPrescribed/Directions";	
		
		$arrFields[20]	=	"/Message/Body/*/MedicationPrescribed/WrittenDate/Date";	
		$arrFields[21]	=	"/Message/Body/*/MedicationPrescribed/WrittenDate/Date";	
		$arrFields[22]	=	"/Message/Body/*/MedicationPrescribed/WrittenDate/Date";	
		
		$arrFields[23]	=	"/Message/Body/*/MedicationPrescribed/WrittenDate/DateTime";	
		$arrFields[24]	=	"/Message/Body/*/MedicationPrescribed/WrittenDate/DateTime";	
		$arrFields[25]	=	"/Message/Body/*/MedicationPrescribed/WrittenDate/DateTime";	
		
		$arrFields[26]	=	"/Message/Body/*/MedicationPrescribed/EffectiveDate/Date";	
		$arrFields[27]	=	"/Message/Body/*/MedicationPrescribed/EffectiveDate/Date";	
		$arrFields[28]	=	"/Message/Body/*/MedicationPrescribed/EffectiveDate/Date";	
		
		$arrFields[29]	=	"/Message/Body/*/MedicationPrescribed/EffectiveDate/DateTIme";	
		$arrFields[30]	=	"/Message/Body/*/MedicationPrescribed/EffectiveDate/DateTime";	
		$arrFields[31]	=	"/Message/Body/*/MedicationPrescribed/EffectiveDate/DateTime";	
		$arrFields[32]	=	"/Message/Body/*/MedicationPrescribed/Refills/Qualifier";	
		$arrFields[33]	=	"/Message/Body/*/MedicationPrescribed/Refills/Value";	
		$arrFields[34]	=	"/Message/Body/*/MedicationPrescribed/Note";
		
		$arrValues	= array();
		foreach($arrFields as $strIndex => $strFieldPath)
		{
			//echo $strFieldPath;
			//echo '<pre>';print_r($this->objPrescription);
			$arrFieldNodes	=	$this->objPrescription->xpath($strFieldPath);
			// echo '<pre>';print_r($arrFieldNodes);die;
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
		// echo '<pre>';print_r($arrValues);die;
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