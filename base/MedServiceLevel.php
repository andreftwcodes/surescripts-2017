<?php

class ServiceLevel
{
	
	
	public $ControlledSubstance; 
	public $CCR; 
	public $Census; 
	public $Resupply; 
	public $NotUsed; 
	
	
	
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
			
			$this->ControlledSubstance			=	false;
			$this->CCR							=	false;
			$this->Census						=	false;
			$this->Resupply						=	false;
			$this->NotUsed						=	false;
			
			
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
		
		
		
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->ControlledSubstance);
		
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->CCR);
		
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->Census);
		
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->Resupply);
		
		
		$strServiceLevel	.=	ServiceLevel::getBitBasedOnServiceFlag($this->NotUsed);
		
		
		
		
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
		
		
		
		
		$this->NotUsed		=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[7]);
		if($this->NotUsed === true)
		{
			$this->ServiceLevelBits[7] = '7';
		}
		
		
		$this->Resupply		=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[8]);
		if($this->Resupply === true)
		{
			$this->ServiceLevelBits[8] = '8';
		}
		
		
		$this->Census		=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[9]);
		if($this->Census === true)
		{
			$this->ServiceLevelBits[9] = '9';
		}
		
		
		$this->CCR		=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[10]);
		if($this->CCR === true)
		{
			$this->ServiceLevelBits[10] = '10';
		}
		
		
		$this->ControlledSubstance			=	ServiceLevel::getServiceFlagBasedOnBit($ServiceLevelBinaryCode[11]);
		if($this->ControlledSubstance === true)
		{
			$this->ServiceLevelBits[11] = '11';
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

	public function ServiceLevels() {
		$service_levels = array('New' => 0, 'Refill' => 1, 'Change' => 2, 'RxFill' => 3, 'Cancel' => 4, 'ReSupp' => 8, 'Census' => 9, 'CCR' => 10, 'ControlledSubstance' => 11);
		// $service_levels = array('New' => 0, 'Refill' => 1, 'Change' => 2, 'RxFill' => 3, 'Cancel' => 4, 'ReSupp' => 5, 'Census' => 6, 'CCR' => 7, 'ControlledSubstance' => 8, 'IDProofSM' => 9, 'CIMessage' => 10, 'CIEvent' => 11, 'ePA' => 12, 'RLE' => 13, 'MedicationAdherence' => 14, 'HighRiskMedication' => 15, 'MissingMedication' => 16, 'PDC' => 17, 'LongTermCare' => 18, 'DispositionRequest' => 19, 'DispositionAcknowledgement' => 20, 'UIServiceSummary' => 21, 'PatMedBenefitCheck' => 22, 'MPILoad' => 23, 'MmpocMpiSearch' => 24, 'Specialty Patient Enrollment' => 25, 'Patient Notifications' => 26, 'RxFillIndicatorChange' => 27, 'DrugAdministration' => 28, 'NewRxRequest' => 29, 'NewRxResponseDenied' => 30, 'Recertification' => 31, 'RxTransfer' => 32, 'Specialty Patient Enrollment - Third Party' => 33, 'SPOAutoEnrollment' => 34, 'Real-Time Formulary' => 35, 'DirectoryMessaging' => 36);

		return $service_levels;
	}
}

?>