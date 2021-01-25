<?php

include_once('med_config.php');
include_once(WEB_ROOT.'lib/data_objects/base/DB.php');

class ProductIFace
{

	private function buildOutMessageReadQuery()
	{
		$strSQL		=	'SELECT * FROM out_message_transaction';
	}
	

	public function GetRxItem($RxNumber)
	{
		global $medDB;
		$strSQL						=	$this->buildRxItemSQL($RxNumber);
		$RxItemSet					=	$medDB->GetRow($strSQL);

		if(count($RxItemSet) <= 0)
		{
			$objRxItem				=	$this->getItemNotFoundRxItemObject($RxNumber);
			$objRxItem->RxNumber	=	$RxNumber;
		}
		else
		{
			
			$objRxItem							=	new RxItem();
			$objRxItem->RxNumber				=	$RxItemSet['rx_number'];
			$objRxItem->RxValid					=	$RxItemSet['rx_valid'];
			$objRxItem->RxCustomerName			=	$RxItemSet['rx_customer_name'];
			$objRxItem->RxAmountDue				=	$RxItemSet['amount_due'];
			$objRxItem->RxTaxable				=	$RxItemSet['taxable'];
			$RxMessage							=	new RxMessage();
			$RxMessage->Message					=	'Item Found.';
			$RxMessage->Print					=	false;
			$objRxItem->RxItemFlags				=	$this->getRxItemFlag($RxItemSet['patient_id']);
		}
		return $objRxItem;
	}
	
	
	public function SaveRxInvoice($RxInvoice)
	{
		global $medDB;

		
		$arrInsertHeaderRecord['inv_number']		=	$RxInvoice->RxInvoiceNumber;
		$arrInsertHeaderRecord['inv_date']			=	$RxInvoice->RxInvoiceDate;
		$arrInsertHeaderRecord['sub_total']			=	$RxInvoice->RxSubTotal;
		$arrInsertHeaderRecord['tax_total']			=	$RxInvoice->RxTaxTotal;
		$arrInsertHeaderRecord['inv_total']			=	$RxInvoice->RxTotal;
		
		
		foreach($RxInvoice->RxPayments as $RxPayment)
		{
			switch ($RxPayment->Type)
			{
				
				case UNKNOWN:
				case CASH:
				case COUPON:
				case PAYMENT_TYPE_IGNORED_6:
				case PAYMENT_TYPE_IGNORED_7:
				case PAYMENT_TYPE_IGNORED_9:
					$arrInsertHeaderRecord['cash_amt']		=	$RxPayment->Amount;
					break;
				
				case CHARGE:
					$arrInsertHeaderRecord['charge_amt']	=	$RxPayment->Amount;
					break;
				
				case CREDIT_CARD:
					$arrInsertHeaderRecord['cc_amt']		=	$RxPayment->Amount;
					break;
				
				case CHEQUE:
					$arrInsertHeaderRecord['check_amt']		=	$RxPayment->Amount;
					break;
				
				case DISCOUNT:
					$arrInsertHeaderRecord['disc_amt']		=	$RxPayment->Amount;
					break;
				
				case CHANGE:
					$arrInsertHeaderRecord['cash_amt']		-=	$RxPayment->Amount;
					break;
			}
		}
		
		
		
		foreach($RxInvoice->RxItemFlagResults as $RxItemFlagResult)
		{
			$blnAccepted	=	'N';
				if($RxItemFlagResult->Accepted == true)
					$blnAccepted	=	'Y';
			
			switch ($RxItemFlagResult->Type)
			{
				
				
				case HIPPA_ACCEPTED:
					$arrInsertHeaderRecord['hippa_accepted']	=	$blnAccepted;
					$arrInsertHeaderRecord['hippa_sig']			=	$RxItemFlagResult->Signature;
					break;
				
				case SAFETY_CAP_ACCEPTED:
					$arrInsertHeaderRecord['ezcap_accepted']	=	$blnAccepted;
					$arrInsertHeadeRecord['ezcap_sig']			=	$RxItemFlagResult->Signature;
					break;
				
				case PHARMACY_CONSULT:
					$arrInsertHeaderRecord['counsel_accepted']	=	$blnAccepted;
					$arrInsertHeaderRecord['counsel_sig']		=	$RxItemFlagResult->Signature;
					break;
			}
		}
		
		
		$TableHeader				=	'paladin_receipts_hdr';
		$blnResult 					= 	$medDB->AutoExecute($TableHeader, $arrInsertHeaderRecord, 'INSERT');
		
		
		if($blnResult)
		{
			
			foreach($RxInvoice->RxItems as $RxItem)
			{
				$TableDetail						=	'paladin_receipts_dtl';
				$arrDetailRecord					=	array();
				$arrDetailRecord['inv_number']		=	$RxInvoice->RxInvoiceNumber;
				$arrDetailRecord['rx_number']		=	$RxItem->RxNumber;
				$arrDetailRecord['amt_collected']	=	$RxItem->RxAmountDue;
				$arrDetailRecord['checkout_flag']	=	true;
				
				$blnDetailResult 					= $medDB->AutoExecute($TableDetail, $arrDetailRecord, 'INSERT');
			}
		}
		$blnInsert	=	false;
		if($blnResult == 1)
			$blnInsert	=	true;
		return $blnInsert;
	}
	
	
	private function getItemNotFoundRxItemObject($RxNumber)
	{
		$objRxItemNotFound					=	new RxItem();
		$objRxItemNotFound->RxNumber		=	$RxNumber;
		$objRxItemNotFound->RxValid			=	false;
		$objRxItemNotFound->RxTaxable		=	false;
		$objRxItemNotFound->RxAmountDue		=	0.00;
		$RxMessage							=	new RxMessage();
		$RxMessage->Message					=	"Item not Available.";
		$RxMessage->Print					=	false;
		$objRxItemNotFound->RxMessages		=	array();
		$objRxItemNotFound->RxMessages[]	=	$RxMessage;
		return $objRxItemNotFound;
	}
	
	
	private function buildRxItemSQL($RxNumber)
	{
		$strSQL	=	'SELECT ' .
						'rx_number 			= p.rx_id,' .
						'rx_valid     		= \'true\',' .
						'rx_customer_name   = pm.lastname + \', \' + pm.firstname,' .
						'amount_due   		= IsNull(wd.copay,0) - (If wd.insurance_id <> -100 AND IsNull(wd.sales_tax_amount,0) > 0 Then IsNull(wd.sales_tax_amount,0) Else 0 EndIf),' .
						'taxable            = (If wd.insurance_id = -100 AND IsNull(wd.sales_tax_amount,0) > 0 Then \'true\' Else \'false\' EndIf),' .
						'patient_id			= pm.id	' .
					' FROM ' .
						'walkin_header wh, walkin_detail wd, prescription p, patient_master pm' . 
					' WHERE ' .
						'(p.patient_id = pm.id ) and ' . 
						'( p.active = \'Y\' ) and ' .
						'( wh.tran_id = wd.tran_id ) and ' .
						'( wd.rx_id = p.tran_id ) and ' . 
						'( wh.queue_status <> \'D\' ) and' .
						'( wd.checkout_status = \'Y\' and IsNull(wd.checked_by,\'\') <> \'\' ) and ' . 
						'( p.rx_id = \''.$RxNumber.'\' ) ' .
					'ORDER BY wh.tran_id';
		return $strSQL;
	}
	
	
	private function getRxItemFlag($PatientId)
	{
		global $medDB;
		$strSQL								=	$this->buildRxItemFlagSQL($PatientId);
		$RxItemFlagSet						=	$medDB->GetRow($strSQL);
		
		$arrRxItemFlagObj					=	array();
		if(count($RxItemFlagSet) > 0)
		{
			foreach ($RxItemFlagSet as $arrRxItemFlag) 
			{
				$objRxItemFlag				=	new RxItemFlag();
				$objRxItemFlag->Type		=	$arrRxItemFlag['flag_type'];
				$objRxItemFlag->Message		=	$arrRxItemFlag['flag_message'];
				$objRxItemFlag->Required	=	$arrRxItemFlag['flag_required'];
				$arrRxItemFlagObj[]			=	$objRxItemFlag;
			}
		}
		return $arrRxItemFlagObj;
	}
	
	
	private function buildRxItemFlagSQL($PatientId)
	{
		$strSQL	=	'SELECT TOP 1 ' . 
						'flag_type = 1,' . 
						'flag_message = \'HIPPA First Time Signature\',' .
						'flag_required = (If IsNull(sm.sign_required,\'N\') = \'N\' Then \'false\' Else \'true\' EndIf),' .
						'patient_signed = ( SELECT count(*) FROM patient_signform ps WHERE ps.patient_id = \'' . $PatientId . '\' .  AND ps.sign_for = \'H\') ' .
					'FROM ' . 
						'signform_master sm' . 
					'WHERE ' . 
						'sm.procedure_id = \'H\' AND ' .
						'isnull(sm.note,\'\')<>\'\' AND ' .
						'patient_signed = 0 ' .
					'UNION ALL ' .
					'SELECT Top 1 flag_type = 2, ' .
						'flag_message = \'Safety cap agreement\',' .
						'flag_required = (If IsNull(sm.sign_required,\'N\') = \'N\' Then \'false\' Else \'true\' EndIf), ' . 
						'patient_signed = ( SELECT count(*) FROM patient_signform ps WHERE ps.patient_id = \'' . $PatientId . '\' . AND ps.sign_for = \'E\') ' . 
					'FROM ' . 
						'signform_master sm ' . 
					'WHERE ' .
						'sm.procedure_id = \'E\' AND ' . 
      					'isnull(sm.note,\'\')<>\'\' AND ' .
       					' patient_signed = 0' .
       				'UNION ALL ' .
       				'SELECT ' . 
       					'flag_type = 3,' . 
       					'flag_message = \'Ask for pharmacy consultation\',' . 
       					'flag_required = \'false\',' .
       					'0 ' . 
       				'FROM ' . 
       					'office_parameters ' . 
       				'WHERE ' . 
       					'parameter_id = \'IPS_0055\' and ' . 
       					'office_id = 1';
		return $strSQL;
	}
}

?>