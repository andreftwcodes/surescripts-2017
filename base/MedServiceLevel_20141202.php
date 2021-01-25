<?php

class ServiceLevel
{
	public $Eligibility;		
	public $MedicationHistory;	
	
	
	public $CancelRx;			
	
	public $RxFill;				
	
	
	public $RxChange;			
	
	
	public $RefillRequest;		
	
	public $NewRx;				
	
	private $ServiceLevelBits;
	
	public function __construct($ServiceLevelDecimalCode = null)
	{
		if($ServiceLevelDecimalCode == null)
		{
			$this->Eligibility		=	false;
			$this->MedicationHistory=	false;
			$this->CancelRx			=	false;
			$this->RxFill			=	false;
			$this->RxChange			=	false;
			$this->RefillRequest	=	false;
			$this->NewRx			=	false;
		}
		else
		{
			$this->encodeServiceLevelCode($ServiceLevelDecimalCode);
		}
	}
	public function getServiceLevelCode()
	{
		$strServiceLevel	=	'';
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->Eligibility);
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->MedicationHistory);
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->CancelRx);
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->RxFill);
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->RxChange);
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->RefillRequest);
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->NewRx);
		
		return bindec($strServiceLevel);
	}
	private function encodeServiceLevelCode($ServiceLevelDecimalCode)
	{
		$ServiceLevelBinaryCode	=	strrev(str_pad(decbin($ServiceLevelDecimalCode),16,'0',STR_PAD_LEFT));
		
		
		
		$this->NewRx			=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[0]);
		if($this->NewRx === true)
		{
			$this->ServiceLevelBits[0] = '0';
		}
		
		
		$this->RefillRequest	=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[1]);
		if($this->RefillRequest === true)
		{
			$this->ServiceLevelBits[1] = '1';
		}
		
		
		$this->RxChange			=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[2]);
		if($this->RxChange === true)
		{
			$this->ServiceLevelBits[2] = '2';
		}
		
		
		$this->RxFill			=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[3]);
		if($this->RxFill === true)
		{
			$this->ServiceLevelBits[3] = '3';
		}
		
		
		$this->CancelRx			=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[4]);
		if($this->CancelRx === true)
		{
			$this->ServiceLevelBits[4] = '4';
		}
		
		
		$this->MedicationHistory=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[5]);
		if($this->MedicationHistory === true)
		{
			$this->ServiceLevelBits[5] = '5';
		}
		
		
		$this->Eligibility		=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[6]);
		if($this->Eligibility === true)
		{
			$this->ServiceLevelBits[6] = '6';
		}
		
		if(count($this->ServiceLevelBits) == 0)
		{
			
			$this->ServiceLevelBits = '-1';
		}
		else
		{
			$this->ServiceLevelBits = implode(',',$this->ServiceLevelBits);	
		}
	}
	
	
	public function getServiceLevelBits()
	{
		return $this->ServiceLevelBits;
	}
	
	private static function getServiceFlagBasedOnBit($BitValue)
	{
		if($BitValue == '1')
			return true;
		else
			return false;
	}
	private static function getBitBasedOnServiceFlag($ServiceFlag)
	{
		if($ServiceFlag === true)
			return '1';
		else
			return '0';
	}

}

?>