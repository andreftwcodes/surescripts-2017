<?php



include_once(WEB_ROOT.'base/DB.php');						
include_once(WEB_ROOT.'base/MedCommon.php');					
include_once(WEB_ROOT.'base/structures/OutMessage.php');
include_once(WEB_ROOT.'base/structures/InMessage.php');
include_once(WEB_ROOT.'base/structures/CommonStructures.php');
include_once(WEB_ROOT.'base/structures/Pharmacy.php');
include_once(WEB_ROOT.'base/structures/Prescriber.php');
include_once(WEB_ROOT.'base/structures/OutMessageStatus.php');
include_once(WEB_ROOT.'base/MedPharmacy.php');
include_once(WEB_ROOT.'base/MedPrescriber.php');


class MeditabServer
{
	
	public function queryOutBoxMessages($queryOutBoxMessages)
	{
		
		$arrWhere							=	array();
		
		
		$arrWhere['meditab_id']				=	$queryOutBoxMessages->MeditabID;

		
		$arrWhere['meditab_tran_id']		=	$queryOutBoxMessages->MeditabTranID;
		

		$arrWhere['from_id']				=	$queryOutBoxMessages->FromID;
		
		
		
		if( SERVER_TYPE == "PRESCRIBER_PARTNER")
		{
			
			
		}
		else
		{
			$arrWhere['to_id']					=	$queryOutBoxMessages->ToID;
		}
		

		
		$arrWhere['sent_time_from']			=	$queryOutBoxMessages->SentTimeFrom;
		$arrWhere['sent_time_to']			=	$queryOutBoxMessages->SentTimeTo;

		
		$arrWhere['message_status']			=	$queryOutBoxMessages->MessageStatus;

		
		$arrWhere['meditab_response_status']=	$queryOutBoxMessages->MeditabResponseStatus;

		
		
		$arrWhere['meditab_tran_id_from']	=	$queryOutBoxMessages->MeditabTranIDFrom;
		$arrWhere['meditab_tran_id_to']		=	$queryOutBoxMessages->MeditabTranIDTo;

		
		
		$arrWhere['mtx_tran_id_from']		=	$queryOutBoxMessages->MTxTranIDFrom;
		$arrWhere['mtx_tran_id_to']			=	$queryOutBoxMessages->MTxTranIDTo;

		
		$arrWhere['mtx_record_limit']		=	$queryOutBoxMessages->MTxRecordLimit;
		$arrWhere['sms_version']			=	$queryOutBoxMessages->SmsVersion;
		$arrWhere['app_name']				=	$queryOutBoxMessages->AppName;
		$arrWhere['app_version']			=	$queryOutBoxMessages->AppVersion;
		$arrWhere['vendor_name']			=	$queryOutBoxMessages->VendorName;
		$arrWhere['message_id']				=	$queryOutBoxMessages->MessageID;
		$arrWhere['related_message_id']		=	$queryOutBoxMessages->RelatedMessageID;

		
		$OutMessages						=	 $this->getOutMessages($arrWhere);
		
		if(count($OutMessages)>0)
		{
			$this->UpdateOutMessagesMeditabStatus($arrWhere);
			
			$objResponse				=	new Response();
			$objResponse->out			=	$OutMessages;
		}
		else
		{
			$OutMessages				=	array();
			$objOutMessage				=	new OutMessage();
			$Error						=	new ErrorType();
			$objOutMessage->Error		=	$Error;
			$Error->Code				=	NO_DATA_FOUND;
			$Error->Message				=	NO_DATA_FOUND_MSG;
			$OutMessages[]				=	$objOutMessage;
			$objResponse				=	new Response();
			$objResponse->out			=	$OutMessages;
		}
		return $objResponse;
	}

	
	public function sendToOutBox($OutMessages)
	{
		global $medDB;
		
		$Result	=	'-1';
		
		if($medDB->_errorMsg == "")
		{
			if(count($OutMessages) > 0)
			{
				
				$Result					=	'0';
				foreach($OutMessages as $arrOutMessage)
				{
					if(is_object($arrOutMessage))
					{
						$SearchOutMessage	=	$medDB->GetRow("SELECT tran_id FROM " . OUTBOX_MESSAGE_TABLE . " WHERE meditab_id = '" .
												$arrOutMessage->MeditabID  . "' AND " . " meditab_tran_id = '" . $arrOutMessage->MeditabTranID . "'");
						if(count($SearchOutMessage) <= 0)
						{
							$arrRecord			=	$this->mapOutMessageToArray($arrOutMessage);
							$Result 			= 	$medDB->AutoExecute(OUTBOX_MESSAGE_TABLE, $arrRecord, 'INSERT');
						}
						else
						{
							$Result				=	'-2';
						}
					}
					else
					{
						foreach($arrOutMessage as $OutMessage)
						{
							$SearchOutMessage	=	$medDB->GetRow("SELECT tran_id FROM " . OUTBOX_MESSAGE_TABLE . " WHERE meditab_id = '" .
												$OutMessage->MeditabID  . "' AND " . " meditab_tran_id = '" . $OutMessage->MeditabTranID . "'");										
							if(count($SearchOutMessage) <= 0)
							{
								$arrRecord			=	$this->mapOutMessageToArray($OutMessage);
								$Result 			+= 	$medDB->AutoExecute(OUTBOX_MESSAGE_TABLE, $arrRecord, 'INSERT');
							}
							else
							{
								$Result				=	'-2';
							}
						}
					}
				}
			}
		}
		
		else
		{
			$Result		=	-1;
		}
		
		
		if($Result <= 0 && $Result != -2)
		{
			$Result		=	-1;
		}

		
		$objResponse				=	new Response();
		$objResponse->out			=	$Result;
		return $objResponse;
	}
	
	public function sendToOutBoxWithVerify($OutMessages)
	{
		global $medDB;
		
		$Result	=	'-1';
		$ExtendedStatus = array();
		$ExtendedStatus['FAILED'] = $ExtendedStatus['SUCCEEDED'] = $ExtendedStatus['DUPLICATE'] = array();
		
		if(count($OutMessages) > 0)
		{
			
			$Result					=	'0';
			foreach($OutMessages as $arrOutMessage)
			{
				if(is_object($arrOutMessage))
				{
					$SearchOutMessage	=	$medDB->GetRow("SELECT tran_id FROM " . OUTBOX_MESSAGE_TABLE . " WHERE meditab_id = '" .
											$arrOutMessage->MeditabID  . "' AND " . " meditab_tran_id = '" . $arrOutMessage->MeditabTranID . "'");
					if(count($SearchOutMessage) <= 0)
					{
						
						$arrRecord			=	$this->mapOutMessageToArray($arrOutMessage);
						
						$medDB->AutoExecute(OUTBOX_MESSAGE_TABLE, $arrRecord, 'INSERT');
						
						$LastInsertID		= $medDB->Insert_ID();
						
						if($LastInsertID > 0)
						{
							
							$Result 			+= 1;
							
							$ExtendedStatus['SUCCEEDED'][] = $arrOutMessage->MeditabTranID;
						}
						else
						{
							
							$ExtendedStatus['FAILED'][] = $arrOutMessage->MeditabTranID;
						}
					}
					else
					{
						
						$ExtendedStatus['DUPLICATE'][] = $arrOutMessage->MeditabTranID;
						
						
						
					}
				}
				else
				{
					foreach($arrOutMessage as $OutMessage)
					{
						$SearchOutMessage	=	$medDB->GetRow("SELECT tran_id FROM " . OUTBOX_MESSAGE_TABLE . " WHERE meditab_id = '" .
											$OutMessage->MeditabID  . "' AND " . " meditab_tran_id = '" . $OutMessage->MeditabTranID . "'");
						if(count($SearchOutMessage) <= 0)
						{
							
							$arrRecord			=	$this->mapOutMessageToArray($OutMessage);
							
							$medDB->AutoExecute(OUTBOX_MESSAGE_TABLE, $arrRecord, 'INSERT');
							
							$LastInsertID		= $medDB->Insert_ID();
							
							if($LastInsertID > 0)
							{
								
								$Result 			+= 1;
								
								$ExtendedStatus['SUCCEEDED'][] = $OutMessage->MeditabTranID;
							}
							else
							{
								
								$ExtendedStatus['FAILED'][] = $OutMessage->MeditabTranID;
							}
						}
						else
						{	
							
							$ExtendedStatus['DUPLICATE'][] = $OutMessage->MeditabTranID;
							
						}
					}
				}
			}
		}

		
		$objResponse				=	new Response();
		$objStatus					=	new ExtendedStatus();
		$objStatus->Status			=	$Result;
		$objStatus->Failed			=	@implode(',', $ExtendedStatus['FAILED']);
		$objStatus->Succeeded		=	@implode(',', $ExtendedStatus['SUCCEEDED']);
		$objStatus->Duplicate		=	@implode(',', $ExtendedStatus['DUPLICATE']);
		$objResponse->out			=	$objStatus;
		return $objResponse;
	}

	
	function sendToInBox($InMessages)
	{
		global $medDB;
		
		$Result	=	-1;
		if(count($InMessages) > 0)
		{
			
			$Result					=	0;
			foreach($InMessages as $arrInMessage)
			{

				if(is_object($arrInMessage))
				{
					$arrRecord			=	$this->mapInMessageToArray($arrInMessage);
					$Result 			= 	$medDB->AutoExecute(INBOX_MESSAGE_TABLE, $arrRecord, 'INSERT');
				}
				else
				{
					foreach($arrInMessage as $InMessage)
					{
						$arrRecord			=	$this->mapInMessageToArray($InMessage);
						$Result 			+= 	$medDB->AutoExecute(INBOX_MESSAGE_TABLE, $arrRecord, 'INSERT');
					}
				}
			}
		}
		
		$objResponse				=	new Response();
		$objResponse->out			=	$Result;
		return $objResponse;
	}

	
	public function queryInBoxMessages($queryInBoxMessages)
	{
		//file_put_contents('logs/'.time(), json_encode($queryInBoxMessages));
		$arrWhere				=	array();
		$arrWhere['from_id']			=	$queryInBoxMessages->FromID;
		$arrWhere['to_id']			=	$queryInBoxMessages->ToID;
		$arrWhere['message_id']			=	$queryInBoxMessages->MessageID;
		$arrWhere['related_message_id']		=	$queryInBoxMessages->RelatedMessageID;

		$arrWhere['message_status']		=	$queryInBoxMessages->MessageStatus;
		$arrWhere['sms_version']		=	$queryInBoxMessages->SmsVersion;
		$arrWhere['app_name']			=	$queryInBoxMessages->AppName;
		$arrWhere['app_version']		=	$queryInBoxMessages->AppVersion;
		$arrWhere['vendor_name']		=	$queryInBoxMessages->VendorName;

		$arrWhere['sent_time_from']		=	$queryInBoxMessages->SentTimeFrom;
		$arrWhere['sent_time_to']		=	$queryInBoxMessages->SentTimeTo;
		$arrWhere['received_time_from']		=	$queryInBoxMessages->ReceivedTimeFrom;
		$arrWhere['received_time_to']		=	$queryInBoxMessages->ReceivedTimeTo;

		$arrWhere['mtx_tran_id_from']		=	$queryInBoxMessages->MTxTranIDFrom;
		$arrWhere['mtx_tran_id_to']		=	$queryInBoxMessages->MTxTranIDTo;
		$arrWhere['mtx_record_limit']		=	$queryInBoxMessages->MTxRecordLimit;
		
		
		
		
		if(FORCE_QUERY_INBOX_METHOD_TO_RETURN_N_RECORDS != -1 && FORCE_QUERY_INBOX_METHOD_TO_RETURN_N_RECORDS > 0)
		{
		    $arrWhere['mtx_record_limit']   = FORCE_QUERY_INBOX_METHOD_TO_RETURN_N_RECORDS;
		}
		
		$arrWhere['meditab_response_status']=	$queryInBoxMessages->MeditabResponseStatus;

		$InMessages							=	 $this->getInMessages($arrWhere);

		
		if(count($InMessages)>0)
		{
			
			$objResponse				=	new Response();
			$objResponse->out			=	$InMessages;
		}
		else
		{
			
			$InMessages					=	array();
			$objInMessage				=	new InMessage();
			
			$objInMessage->FromID		=	'0';
			$objInMessage->ToID			=	'0';
			$objInMessage->MessageID	=	'0';
			$objInMessage->EdiMessage	=	'0';
			
			$Error						=	new ErrorType();
			$Error->Code				=	NO_DATA_FOUND;
			$Error->Message				=	NO_DATA_FOUND_MSG;
			
			$objInMessage->Error		=	$Error;
			
			$InMessages[]				=	$objInMessage;
			$objResponse				=	new Response();
			$objResponse->out			=	$InMessages;
		}

		return $objResponse;
	}

	
	public function executeQuery($executeQuery)
	{
		global $medDB;
		$result = "";
		
		$SQL 				= $executeQuery->SQL;
		$ColumnSeparator 	= ($executeQuery->ColumnSeparator);
		$LineSeparator 		= ($executeQuery->LineSeparator);

		$Records 			= $medDB->getAll($SQL);

		foreach($Records as $Record)
		{
			$result .= $LineSeparator;
			foreach($Record as $Column=>$ColumnData)
			{
				$result .= $ColumnSeparator . $ColumnData;
			}
		}
		
		$objResponse				=	new Response();
		$objResponse->out 			=	$result;
		return $objResponse;

	}

	
	public function getPharmacy($getPharmacy)
	{
		
		$rsPharmacy						=	MedPharmacy::GetPharmacyByNCPDPID($getPharmacy->NCPDPID);

		
		$objResponse					=	new Response();

		
		if($rsPharmacy != NO_DATA_FOUND)
		{
			
			$objPharmacy				=	$this->mapPharmacyArrayToObject($rsPharmacy);

			
			$objResponse->out			=	$objPharmacy;
		}
		else
		{
			$objPharmacy				=	new Pharmacy();
			$Error						=	new ErrorType();
			$objPharmacy->Error			=	$Error;
			$Error->Code				=	NO_DATA_FOUND;
			$Error->Message				=	NO_DATA_FOUND_MSG;
			$objResponse->out			=	$objPharmacy;
		}
		return $objResponse;
	}

	
	public function getPrescriber($getPrescriber)
	{
		
		$rsPrescriber					=	MedPrescriber::GetPrescriberBySPI($getPrescriber->SPI);

		
		$objResponse					=	new Response();

		
		if($rsPrescriber != NO_DATA_FOUND)
		{
			
			$objPrescriber				=	$this->mapPrescriberArrayToObject($rsPrescriber);

			
			$objResponse->out			=	$objPrescriber;
		}
		else
		{
			$objPrescriber				=	new Prescriber();
			$Error						=	new ErrorType();
			$objPrescriber->Error		=	$Error;
			$Error->Code				=	NO_DATA_FOUND;
			$Error->Message				=	NO_DATA_FOUND_MSG;
			$objResponse->out			=	$objPrescriber;
		}
		return $objResponse;
	}

	
	public function searchPharmacy($searchPharmacy)
	{
		
		
		
		
		$strFields							=	'*';

		
		$rsPharmacy						=	MedPharmacy::GetPharmacy($searchPharmacy->NCPDPID, $searchPharmacy->PharmacyNumber,
																$searchPharmacy->PharmacyName, $searchPharmacy->Phone, $searchPharmacy->Address,
																$searchPharmacy->City, $searchPharmacy->State, $searchPharmacy->Zip,
																$searchPharmacy->ServiceLevel, $searchPharmacy->LastModifiedSinceDate, $strFields);

		
		$objResponse					=	new Response();

		
		$arrObjPharmacy					=	array();
		
		if($rsPharmacy != NO_DATA_FOUND && $rsPharmacy != ERROR_IN_INPUT)
		{
			foreach($rsPharmacy as $arrPharmacy)
			{
				
				$arrObjPharmacy[]		=	$this->mapPharmacyArrayToObject($arrPharmacy);
			}

			
			$objResponse->out			=	$arrObjPharmacy;
		}
		else
		{
			$objPharmacy				=	new Pharmacy();
			$Error						=	new ErrorType();
			$objPharmacy->Error			=	$Error;

			
			if($rsPharmacy != NO_DATA_FOUND)
			{
				$Error->Code			=	NO_DATA_FOUND;
				$Error->Message			=	NO_DATA_FOUND_MSG;
			}
			else if($rsPharmacy != ERROR_IN_INPUT)
			{
				$Error->Code			=	ERROR_IN_INPUT;
				$Error->Message			=	ERROR_IN_INPUT_MSG;
			}
			else
			{
				$Error->Code			=	UNKNOWNE_ERROR;
				$Error->Message			=	UNKNOWNE_ERROR_MSG;
			}
			$arrObjPharmacy[]			=	$objPharmacy;
			$objResponse->out			=	$arrObjPharmacy;
		}
		return $objResponse;
	}

	
	public function searchPrescriber($searchPrescriber)
	{
		
		
		
		$strFields					=	'spi,dea,last_name,first_name,address_line1,city,state,zip,phone_primary,fax';

		
		$rsPrescriber				=	MedPrescriber::GetPrescriberByAddress($searchPrescriber->Address1,$searchPrescriber->City,$searchPrescriber->State,$searchPrescriber->Zip,$strFields);
		
		$objResponse				=	new Response();

		
		$arrObjPrescriber			=	array();

		
		if($rsPrescriber != NO_DATA_FOUND && $rsPrescriber != ERROR_IN_INPUT)
		{
			foreach($rsPrescriber as $arrPrescriber)
			{
				
				$arrObjPrescriber[]		=	$this->mapPrescriberArrayToObject($arrPrescriber);
			}
			
			$objResponse->out			=	$arrObjPrescriber;
		}
		else
		{
			$objPrescriber				=	new Prescriber();
			$Error						=	new ErrorType();
			$objPrescriber->Error		=	$Error;

			
			if($rsPrescriber != NO_DATA_FOUND)
			{
				$Error->Code				=	NO_DATA_FOUND;
				$Error->Message				=	NO_DATA_FOUND_MSG;
			}
			else if($rsPrescriber != ERROR_IN_INPUT)
			{
				$Error->Code				=	ERROR_IN_INPUT;
				$Error->Message				=	ERROR_IN_INPUT_MSG;
			}
			else
			{
				$Error->Code				=	UNKNOWNE_ERROR;
				$Error->Message				=	UNKNOWNE_ERROR_MSG;
			}
			$arrObjPrescriber[]			=	$objPrescriber;
			$objResponse->out			=	$arrObjPrescriber;
		}
		return $objResponse;
	}

	
	public function markInBoxMessageAsRead($markInBoxMessageAsRead)
	{
		if(ENABLE_MARK_IN_BOX_MESSAGE_AS_READ_LOG){
			$arrMarkMessagesTranId = explode(',', $markInBoxMessageAsRead->TranID);
		}
		
		global $medDB;
		$TranID					=	$markInBoxMessageAsRead->TranID;
		$strWhere				=	"";
		
		
		if( strpos($TranID, ",") !== false )
		{
			$strWhere 			=	' tran_id IN (' . $TranID . ')';
		}
		else if($TranID > 0)
		{
			$strWhere 			=	' tran_id = ' . $TranID;
		}
		
		if($strWhere != "")
		{
			$arrInBoxMessage	=	array();
			$arrInBoxMessage['meditab_response_status'] = 'Y';
			$arrInBoxMessage['message_status'] = 'Sent';
			$arrInBoxMessage['ips_received_datetime'] = getServerTimestamp();
            $arrInBoxMessage['ips_received_by_ip']    = getClientIP();
			if(isset($_SERVER['REMOTE_ADDR']))
			{
				$arrInBoxMessage['message_received_by_ip'] = $_SERVER['REMOTE_ADDR'];
			}
			$medDB->AutoExecute(INBOX_MESSAGE_TABLE, $arrInBoxMessage, 'UPDATE', $strWhere);
			$result 			=	$medDB->_affectedrows();
		}
		else
		{
			
			$result				=	-1;
		}
		
		$objResponse			=	new Response();
		$objResponse->out 		=	$result;
		
		if(ENABLE_MARK_IN_BOX_MESSAGE_AS_READ_LOG && $result != count($arrMarkMessagesTranId)){
			$arrLog	= array();
			$arrLog['request_params'] = $markInBoxMessageAsRead->TranID;
			$arrLog['server_params'] = json_encode($_SERVER);
			$arrLog['response_params'] = json_encode($objResponse);
			$medDB->AutoExecute('service_log', $arrLog, 'INSERT');
		}
		
		return $objResponse;
	}

	
	public function markOutBoxMessageAsRead($markOutBoxMessageAsRead)
	{
		
		global $medDB;
		
		
		$MeditabID				=	trim($markOutBoxMessageAsRead->MeditabID);
		$MeditabTranID			=	trim($markOutBoxMessageAsRead->MeditabTranID);
		
		
		if($MeditabID > 0 && $MeditabTranID > 0)
		{
			$arrInBoxMessage	=	array();
			$arrInBoxMessage['meditab_response_status'] = 'Y';
			$medDB->AutoExecute(OUTBOX_MESSAGE_TABLE, $arrInBoxMessage, 'UPDATE', " meditab_id = '" . $MeditabID . "' AND meditab_tran_id = '" . $MeditabTranID . "'");
			
			$result				=	$medDB->_affectedrows();
		}
		else
		{
			
			$result				=	-1;
		}
		
		$objResponse			=	new Response();
		$objResponse->out 		=	$result;
		return $objResponse;
	}
	
	
	public function markOutBoxMessageStatusAsRead($markOutBoxMessageStatusAsRead)
	{
		
		global $medDB;
		
		
		$TranIDUpdate = $MissingTranIDUpdate = $result = 1;
		$TranIDUpdateCount = $MissingTranIDUpdateCount = 0;

		
		$TranID				= trim($markOutBoxMessageStatusAsRead->TranID);
		$MissingTranID		= trim($markOutBoxMessageStatusAsRead->MissingTranID);
		
		
		$strWhere = "";
		if( strpos($TranID, ",") !== false )
		{
			
			
			
			$strWhere 			=	" tran_id IN (" . $TranID . ")";
		}
		else if($TranID > 0)
		{
			$strWhere 			=	" tran_id = '" . $TranID . "'";
		}
		
		
		if($strWhere != "")
		{
			$arrOutBoxMessage	=	array();
			$arrOutBoxMessage['meditab_response_status'] = 'Y';
			$medDB->AutoExecute(OUTBOX_MESSAGE_TABLE, $arrOutBoxMessage, 'UPDATE', $strWhere);
			$TranIDUpdateCount 	=	$medDB->_affectedrows();
		}
		else
		{
			
			$TranIDUpdate				=	-1;
		}
		
		
		$strWhere = "";
		if( strpos($MissingTranID, ",") !== false )
		{
			
			
			
			$strWhere 			=	" tran_id IN (" . $MissingTranID . ")";
		}
		else if($MissingTranID > 0)
		{
			$strWhere 			=	" tran_id = '" . $MissingTranID . "'";
		}
		
		
		if($strWhere != "")
		{
			$arrMOutBoxMessage	=	array();
			$arrMOutBoxMessage['meditab_response_status'] = 'M';
			$medDB->AutoExecute(OUTBOX_MESSAGE_TABLE, $arrMOutBoxMessage, 'UPDATE', $strWhere);
			$MissingTranIDUpdateCount	=	$medDB->_affectedrows();
		}
		else
		{
			
			$MissingTranIDUpdate				=	-1;
		}
		
		
		if( $$TranIDUpdate == -1 && $MissingTranIDUpdate == -1)
		{
			$result = -1;
		}
		
		
		$objResponse							=	new Response();
		$objResponse->out						=	$result;
		
		
		
		return $objResponse;
	}

	
	public function queryOutBoxMessageStatus($queryOutBoxMessageStatus)
	{
		global $medDB;

		
		$MeditabID				=	trim($queryOutBoxMessageStatus->MeditabID);
		
		$ListOfSPI	=	trim($queryOutBoxMessageStatus->ListOfFromID);
		
		$ListOfSPI	=	"'" . str_replace(array(","), array("','"), $ListOfSPI) . "'";
		
		
		$strLimit = '';
		if( defined("FORCE_QUERY_OUTBOX_STATUS_METHOD_TO_RETURN_N_RECORDS") == true 
			&& FORCE_QUERY_OUTBOX_STATUS_METHOD_TO_RETURN_N_RECORDS != -1 
			&& FORCE_QUERY_OUTBOX_STATUS_METHOD_TO_RETURN_N_RECORDS > 0)
		{
			$strLimit = ' LIMIT 0,' . FORCE_QUERY_OUTBOX_STATUS_METHOD_TO_RETURN_N_RECORDS;
		}
		
		
		$strExtraWhere		= "";
		if( SERVER_TYPE == "PRESCRIBER_PARTNER")
		{
			
			$strExtraWhere	= " AND (meditab_tran_id LIKE '%NEWRX%' OR meditab_tran_id LIKE '%REFRES%') ";
		}
		else
		{	
			$strExtraWhere	= " AND (meditab_tran_id LIKE '%REFREQ%') ";
		}
		
		
		$strSQL 			= "SELECT tran_id, message_id, meditab_tran_id, message_status, status_code, error_note FROM out_message_transaction 
						WHERE from_id IN (" . $ListOfSPI . ") AND meditab_response_status = 'N' 
						AND message_status IN ('Sent', 'Error') " . $strExtraWhere . $strLimit;
		
		
		$rsOutMessageStatus = $medDB->GetAll($strSQL);

		$arrObjOutMessageStatus = array();
		foreach($rsOutMessageStatus as $arrOutMessageStatus)
		{
			
			$objOutMessageStatus = new OutMessageStatus();
			
			
			$objOutMessageStatus->TranID = $arrOutMessageStatus['tran_id'];
			$objOutMessageStatus->MeditabTranID = $arrOutMessageStatus['meditab_tran_id'];
			$objOutMessageStatus->MessageID = $arrOutMessageStatus['message_id'];
			$objOutMessageStatus->MessageStatus = $arrOutMessageStatus['message_status'];
			$objOutMessageStatus->StatusCode = $arrOutMessageStatus['status_code'];
			$objOutMessageStatus->ErrorNote = base64_encode(MeditabServer::translateToUserFriendlyMessage($arrOutMessageStatus['error_note']));
			
			
            $arrObjOutMessageStatus[] = $objOutMessageStatus;
		}
		
		
		$objOutMessageStatusMetaInfo = new OutMessageStatus();
		$objOutMessageStatusMetaInfo->TranID = "-1";
		$objOutMessageStatusMetaInfo->MeditabTranID = "NA";
		$objOutMessageStatusMetaInfo->MessageID = "NA";
		$objOutMessageStatusMetaInfo->MessageStatus = "NA";
		$objOutMessageStatusMetaInfo->StatusCode = FORCE_QUERY_OUTBOX_STATUS_METHOD_TO_RETURN_N_RECORDS;
		$objOutMessageStatusMetaInfo->ErrorNote = "StatusCode represents limit per call, If -Ve then No limit, If +Ve limit equals to +Ve number";
		
		$arrObjOutMessageStatus[] = $objOutMessageStatusMetaInfo;
		
		
		$objResponse			=	new Response();
		$objResponse->out 		=	$arrObjOutMessageStatus;
        return $objResponse;
	}
	
	
	private static function translateToUserFriendlyMessage($strText)
	{
		global $medDB;
		
		
		$strSql = "SELECT technical_message, user_friendly_message FROM user_friendly_messages WHERE status = 'Active'";
		$arrList = $medDB->GetAll($strSql);

		foreach($arrList as $arrMessage)
		{
			if( strpos($strText, $arrMessage['technical_message']) !== false)
			{
				$strText = $arrMessage['user_friendly_message'];
				break;
			}
		}
		
		return $strText;
		
	}

	
	public function addPharmacy($addPharmacy)
	{
		global $medDB;
		$objPharmacy		=	$addPharmacy->addPharmacy;
		$arrPharmacy		=	array();
		$arrPharmacy['ncpdpid']	=	$objPharmacy->NCPDPID;

		$medDB->AutoExecute('pharmacy_requests',$arrPharmacy,'INSERT');
	}

	
	public function updatePharmacy($updatePharmacy)
	{

	}

	
	public function addPrescriber($addPrescriber)
	{

	}

	
	public function updatePrescriber($updatePrescriber)
	{

	}

	
	public function addPrescriberLocation($addPrescriberLocation)
	{

	}

	
	private function mapPharmacyObjectToArray()
	{

	}

	
	private function mapPrescriberArrayToObject($rsPrescriber)
	{
		
			$objPrescriber					=	new Prescriber();

			
			if($rsPrescriber['spi']!='')
				$objPrescriber->SPI			=	$rsPrescriber['spi'];
			if($rsPrescriber['dea']!='')
				$objPrescriber->DEA				=	$rsPrescriber['dea'];
			if($rsPrescriber['state_license_number']!='')
				$objPrescriber->StateLicenseNumber	=	$rsPrescriber['state_license_number'];
			if($rsPrescriber['reference_number_alt1']!='')
				$objPrescriber->ReferenceNumberAlt1Qualifier=	$rsPrescriber['reference_number_alt1'];
			if($rsPrescriber['reference_number_alt1_qualifier']!='')
				$objPrescriber->ReferenceNumberAlt1Qualifier=	$rsPrescriber['reference_number_alt1_qualifier'];

			if($rsPrescriber['specialty_code_primary']!='')
				$objPrescriber->SpecialtyCodePrimary=	$rsPrescriber['specialty_code_primary'];
			if($rsPrescriber['prefix_name']!='')
				$objPrescriber->PrefixName			=	$rsPrescriber['prefix_name'];
			if($rsPrescriber['last_name']!='')
				$objPrescriber->LastName			=	$rsPrescriber['last_name'];
			if($rsPrescriber['first_name']!='')
				$objPrescriber->FirstName			=	$rsPrescriber['first_name'];
			if($rsPrescriber['middle_name']!='')
				$objPrescriber->MiddleName			=	$rsPrescriber['middle_name'];
			if($rsPrescriber['suffix_name']!='')
				$objPrescriber->SuffixName			=	$rsPrescriber['suffix_name'];
			if($rsPrescriber['clinic_name']!='')
				$objPrescriber->ClinicName			=	$rsPrescriber['clinic_name'];

			if($rsPrescriber['address_line1']!='')
				$objPrescriber->Address1			=	$rsPrescriber['address_line1'];
			if($rsPrescriber['address_line2']!='')
				$objPrescriber->Address2			=	$rsPrescriber['address_line2'];
			if($rsPrescriber['city']!='')
				$objPrescriber->City				=	$rsPrescriber['city'];
			if($rsPrescriber['state']!='')
				$objPrescriber->State				=	$rsPrescriber['state'];
			if($rsPrescriber['zip']!='')
				$objPrescriber->Zip					=	$rsPrescriber['zip'];
			if($rsPrescriber['phone_primary']!='')
				$objPrescriber->Phone				=	$rsPrescriber['phone_primary'];
			if($rsPrescriber['fax']!='')
				$objPrescriber->Fax					=	$rsPrescriber['fax'];
			if($rsPrescriber['email']!='')
				$objPrescriber->Email					=	$rsPrescriber['email'];

			if($rsPrescriber['phone_alt1']!='')
				$objPrescriber->PhoneAlt1			=	$rsPrescriber['phone_alt1'];
			if($rsPrescriber['phone_alt1_qualifier']!='')
				$objPrescriber->PhoneAlt1Qualifier=	$rsPrescriber['phone_alt1_qualifier'];
			if($rsPrescriber['phone_alt2']!='')
				$objPrescriber->PhoneAlt2			=	$rsPrescriber['phone_alt2'];
			if($rsPrescriber['phone_alt2_qualifier']!='')
				$objPrescriber->PhoneAlt2Qualifier=	$rsPrescriber['phone_alt2_qualifier'];
			if($rsPrescriber['phone_alt3']!='')
				$objPrescriber->PhoneAlt3			=	$rsPrescriber['phone_alt3'];
			if($rsPrescriber['phone_alt3_qualifier']!='')
				$objPrescriber->PhoneAlt3Qualifier=	$rsPrescriber['phone_alt3_qualifier'];
			if($rsPrescriber['phone_alt4']!='')
				$objPrescriber->PhoneAlt4			=	$rsPrescriber['phone_alt4'];
			if($rsPrescriber['phone_alt4_qualifier']!='')
				$objPrescriber->PhoneAlt4Qualifier=	$rsPrescriber['phone_alt4_qualifier'];
			if($rsPrescriber['phone_alt5']!='')
				$objPrescriber->PhoneAlt5			=	$rsPrescriber['phone_alt5'];
			if($rsPrescriber['phone_alt5_qualifier']!='')
				$objPrescriber->PhoneAlt5Qualifier=	$rsPrescriber['phone_alt5_qualifier'];

			if($rsPrescriber['active_start_time']!='')
				$objPrescriber->ActiveStartTime	=	$rsPrescriber['active_start_time'];
			if($rsPrescriber['active_end_time']!='')
				$objPrescriber->ActiveEndTime		=	$rsPrescriber['active_end_time'];
			if($rsPrescriber['service_level']!='')
				$objPrescriber->ServiceLevel		=	$rsPrescriber['service_level'];
			if($rsPrescriber['service_level_bits']!='')
				$objPrescriber->ServiceLevelBits		=	$rsPrescriber['service_level_bits'];
			if($rsPrescriber['partner_account']!='')
				$objPrescriber->PartnerAccount	=	$rsPrescriber['partner_account'];
			if($rsPrescriber['last_modified_date']!='')
				$objPrescriber->LastModifiedDate	=	$rsPrescriber['last_modified_date'];


			if($rsPrescriber['record_change']!='')
				$objPrescriber->RecordChange		=	$rsPrescriber['record_change'];
			if($rsPrescriber['old_service_level']!='')
				$objPrescriber->OldServiceLevel	=	$rsPrescriber['old_service_level'];
			if($rsPrescriber['text_service_level']!='')
				$objPrescriber->TextServiceLevel	=	$rsPrescriber['text_service_level'];
			if($rsPrescriber['text_service_level_change']!='')
				$objPrescriber->TextServiceLevelChange=	$rsPrescriber['text_service_level_change'];

			if($rsPrescriber['version']!='')
				$objPrescriber->Version			=	$rsPrescriber['version'];
			if($rsPrescriber['npi']!='')
				$objPrescriber->NPI				=	$rsPrescriber['npi'];
			if($rsPrescriber['npi_location']!='')
				$objPrescriber->NPILocation				=	$rsPrescriber['npi_location'];
			return $objPrescriber;
	}

	
	private function mapPharmacyArrayToObject($rsPharmacy)
	{
			
			$objPharmacy					=	new Pharmacy();

			
			if($rsPharmacy['ncpdpid']!='')
				$objPharmacy->NCPDPID			=	$rsPharmacy['ncpdpid'];
			if($rsPharmacy['store_number']!='')
				$objPharmacy->StoreNumber		=	$rsPharmacy['store_number'];
			if($rsPharmacy['reference_number_alt1']!='')
				$objPharmacy->ReferenceNumberAlt1=	$rsPharmacy['reference_number_alt1'];
			if($rsPharmacy['reference_number_alt1_qualifier']!='')
				$objPharmacy->ReferenceNumberAlt1Qualifier=	$rsPharmacy['reference_number_alt1_qualifier'];

			if($rsPharmacy['store_name']!='')
				$objPharmacy->StoreName			=	$rsPharmacy['store_name'];
			if($rsPharmacy['address_line1']!='')
				$objPharmacy->Address1			=	$rsPharmacy['address_line1'];
			if($rsPharmacy['address_line2']!='')
				$objPharmacy->Address2			=	$rsPharmacy['address_line2'];
			if($rsPharmacy['city']!='')
				$objPharmacy->City				=	$rsPharmacy['city'];
			if($rsPharmacy['state']!='')
				$objPharmacy->State				=	$rsPharmacy['state'];
			if($rsPharmacy['zip']!='')
				$objPharmacy->Zip				=	$rsPharmacy['zip'];
			if($rsPharmacy['phone_primary']!='')
				$objPharmacy->Phone				=	$rsPharmacy['phone_primary'];

			if($rsPharmacy['fax']!='')
				$objPharmacy->Fax				=	$rsPharmacy['fax'];
			if($rsPharmacy['email']!='')
				$objPharmacy->Email				=	$rsPharmacy['email'];
			if($rsPharmacy['phone_alt1']!='')
				$objPharmacy->PhoneAlt1			=	$rsPharmacy['phone_alt1'];
			if($rsPharmacy['phone_alt1_qualifier']!='')
				$objPharmacy->PhoneAlt1Qualifier=	$rsPharmacy['phone_alt1_qualifier'];
			if($rsPharmacy['phone_alt2']!='')
				$objPharmacy->PhoneAlt2			=	$rsPharmacy['phone_alt2'];
			if($rsPharmacy['phone_alt2_qualifier']!='')
				$objPharmacy->PhoneAlt2Qualifier=	$rsPharmacy['phone_alt2_qualifier'];
			if($rsPharmacy['phone_alt3']!='')
				$objPharmacy->PhoneAlt3			=	$rsPharmacy['phone_alt3'];
			if($rsPharmacy['phone_alt3_qualifier']!='')
				$objPharmacy->PhoneAlt3Qualifier=	$rsPharmacy['phone_alt3_qualifier'];
			if($rsPharmacy['phone_alt4']!='')
				$objPharmacy->PhoneAlt4			=	$rsPharmacy['phone_alt4'];
			if($rsPharmacy['phone_alt4_qualifier']!='')
				$objPharmacy->PhoneAlt4Qualifier=	$rsPharmacy['phone_alt4_qualifier'];
			if($rsPharmacy['phone_alt5']!='')
				$objPharmacy->PhoneAlt5			=	$rsPharmacy['phone_alt5'];
			if($rsPharmacy['phone_alt5_qualifier']!='')
				$objPharmacy->PhoneAlt5Qualifier=	$rsPharmacy['phone_alt5_qualifier'];

			if($rsPharmacy['active_start_time']!='')
				$objPharmacy->ActiveStartTime	=	$rsPharmacy['active_start_time'];
			if($rsPharmacy['active_end_time']!='')
				$objPharmacy->ActiveEndTime		=	$rsPharmacy['active_end_time'];
			if($rsPharmacy['service_level']!='')
				$objPharmacy->ServiceLevel		=	$rsPharmacy['service_level'];
			if($rsPharmacy['service_level_bits']!='')
				$objPharmacy->ServiceLevelBits		=	$rsPharmacy['service_level_bits'];
			if($rsPharmacy['partner_account']!='')
				$objPharmacy->PartnerAccount	=	$rsPharmacy['partner_account'];
			if($rsPharmacy['last_modified_date']!='')
				$objPharmacy->LastModifiedDate	=	$rsPharmacy['last_modified_date'];
			if($rsPharmacy['twenty_four_hour_flag']!='')
				$objPharmacy->TwentyFourHourFlag=	$rsPharmacy['twenty_four_hour_flag'];
			if($rsPharmacy['cross_street']!='')
				$objPharmacy->CrossStreet		=	$rsPharmacy['cross_street'];
			if($rsPharmacy['record_change']!='')
				$objPharmacy->RecordChange		=	$rsPharmacy['record_change'];
			if($rsPharmacy['old_service_level']!='')
				$objPharmacy->OldServiceLevel	=	$rsPharmacy['old_service_level'];
			if($rsPharmacy['text_service_level']!='')
				$objPharmacy->TextServiceLevel	=	$rsPharmacy['text_service_level'];
			if($rsPharmacy['text_service_level_change']!='')
				$objPharmacy->TextServiceLevelChange=	$rsPharmacy['text_service_level_change'];

			if($rsPharmacy['version']!='')
				$objPharmacy->Version			=	$rsPharmacy['version'];
			if($rsPharmacy['npi']!='')
				$objPharmacy->NPI				=	$rsPharmacy['npi'];
			return $objPharmacy;
	}

	
	private function getInMessages($arrWhere)
	{
		global $medDB;
		$strSQL								=	$this->buildInMessageReadQuery($arrWhere);
		$InMessageSet						=	$medDB->GetAll($strSQL);

		$arrObjInMessage					=	array();
		$strInMessages						=	'';
		if(count($InMessageSet) > 0)
		{
			foreach ($InMessageSet as $InMessage)
			{
				$objInMessage					=	new InMessage();
				$objInMessage->TranID			=	$InMessage['tran_id'];
				$objInMessage->FromID			=	$InMessage['from_id'];
				$objInMessage->ToID				=	$InMessage['to_id'];
				$objInMessage->MessageID		=	$InMessage['message_id'];
				$objInMessage->RelatedMessageID	=	$InMessage['related_message_id'];

				$objInMessage->SentTime			=	$InMessage['sent_time_in_message'];
				$objInMessage->SmsVersion		=	$InMessage['sms_version'];
				$objInMessage->AppName			=	$InMessage['app_name'];
				$objInMessage->AppVersion		=	$InMessage['app_version'];
				$objInMessage->VendorName		=	$InMessage['vendor_name'];

				$objInMessage->ReceivedTime		=	$InMessage['received_time'];
				$objInMessage->EDIMessage		=	$InMessage['edi_message'];

				$objInMessage->MessageStatus	=	$InMessage['message_status'];
				$objInMessage->ErrorNote		=	base64_encode($InMessage['error_note']);
				

				$arrObjInMessage[]				=	$objInMessage;
				unset($objInMessage);

			}
		}
		return $arrObjInMessage;
	}

	
	private function UpdateOutMessagesMeditabStatus($arrWhere)
	{
		global $medDB;
		$strWhere			=	$this->constructOutMessageWhere($arrWhere);
		$strWhere			.=	" AND message_status != 'Pending'";
		$strSQL				=	"UPDATE " . OUTBOX_MESSAGE_TABLE . " SET meditab_response_status = 'Y' WHERE " . $strWhere;
		$blnResult 			= 	$medDB->Execute($strSQL);
	}

	
	private function mapOutMessageToArray($OutMessage)
	{
	
		$arrRecord['meditab_id']			=	$OutMessage->MeditabID;
		$arrRecord['meditab_tran_id']		=	$OutMessage->MeditabTranID;
		$arrRecord['from_id']				=	$OutMessage->FromID;
		$arrRecord['to_id']					=	$OutMessage->ToID;
		
		if($OutMessage->ToID != '')
		{
			$arrRecord['sent_time']				=	date("Y-m-d H:i:s");
		}
		$arrRecord['edi_message']			=	$OutMessage->EDIMessage;

		
		$arrRecord['message_from']			=	$OutMessage->MessageFrom;
		$arrRecord['message_id']			=	$OutMessage->MessageID;
		$arrRecord['related_message_id']	=	$OutMessage->RelatedMessageID;
		if($OutMessage->RelatedMessageID == '')
		{
			$arrRecord['related_message_id'] = $this->getRelatedMessageID($OutMessage->EDIMessage);
		}
		$arrRecord['sms_version']			=	$OutMessage->SmsVersion;
		
		
		
		$arrRecord['message_version']			=	$OutMessage->MessageVersion;
		
		
		

		
		
		if(OVERRIDE_APP_INFO_FOR_OUT_MSG == true)
		{
			
			$arrRecord['app_name']				=	APP_NAME;
			$arrRecord['app_version']			=	APP_VERSION;
			$arrRecord['vendor_name']			=	VENDOR_NAME;
		}
		else
		{
			
			if($OutMessage->AppName == "")
			{
				$arrRecord['app_name']				=	APP_NAME;
			}
			else
			{
				$arrRecord['app_name']				=	$OutMessage->AppName;
			}
			if($OutMessage->AppVersion == "")
			{
				$arrRecord['app_version']			=	APP_VERSION;
			}
			else
			{
				$arrRecord['app_version']			=	$OutMessage->AppVersion;
			}
			if($OutMessage->VendorName == "")
			{
				$arrRecord['vendor_name']			=	VENDOR_NAME;
			}
			else
			{
				$arrRecord['vendor_name']			=	$OutMessage->VendorName;
			}
		}
		

		
		if($OutMessage->MessageStatus != '')
		{
			$arrRecord['message_status']		=	$OutMessage->MessageStatus;
			if( defined('TEST_SPI') )
			{
				$arrTestSPI = explode(",", TEST_SPI);
				if( in_array( $OutMessage->FromID, $arrTestSPI) )
				{
					$arrRecord['message_status']	=	'Error';					
				}
			}
			
		}

		if($OutMessage->ErrorNote)
		{
			$arrRecord['error_note']			=	$OutMessage->ErrorNote;
		}
		
		
		
		
		
		
		
		
		
		if(RESTRICT_MESSAGE_SUBMISSION_IF_GENERATED_BEFORE_LONG_TIME === TRUE && $OutMessage->RelatedMessageID == "" && $OutMessage->MessageVersion == "")
		{
			
			
			
			
			$strDecodedMessage = base64_decode($OutMessage->EDIMessage);
			
			$arrIsValidRx = MeditabServer::isValidRxByDate( $strDecodedMessage );
			
			$strDecodedMessage = MeditabServer::validateAndCorrectQuantityQualifierForRx($strDecodedMessage);
			
			
			$arrRecord['edi_message'] = base64_encode($strDecodedMessage);
			
			

			if($arrIsValidRx['VALID'] !== true)
			{
				
				
				$arrRecord['message_status']		=	'Error';
				$arrRecord['status_code']			=	'900';
				$arrRecord['message_id']			=	'Not-Applicable';
				$arrRecord['error_note']			=	"Old Rx (Dated: ".$arrIsValidRx['RX_DATE'].") can not be sent on today's date. - Rejected by IMS eRx-Server";
			}
		}

		return $arrRecord;

	}
	
	
	private static function isValidRxByDate($strEditFact)
	{
		$blnValid = false;
		
		$UNA = getDelimitersFromEDIFactMessage($strEditFact);

		
		$intDRUStartPos = strpos($strEditFact, "DRU".$UNA[1]);
		
		$intUITStartPos = strpos($strEditFact, "UIT".$UNA[1]);

		
		$strEditFact = substr($strEditFact, $intDRUStartPos, ($intUITStartPos - $intDRUStartPos));

		
		$strEditFact = array_reverse(explode($UNA[1], $strEditFact));

		
		$strRxDate = substr($strEditFact[7], 3, 8);

		
		$strRxTimestamp = strtotime($strRxDate);

		
		$strTodaysTimestamp	= strtotime( strftime("%Y%m%d", gmmktime()) );

		
		if( ($strTodaysTimestamp - $strRxTimestamp) > CONSIDER_MESSAGE_GENERATED_BEFORE_LONG_TIME_IF_GREATER_THAN_TIME )	
		{
			$blnValid = false;
		}
		else
		{
			$blnValid = true;
		}
		return array('VALID' => $blnValid, 'RX_DATE' => $strRxDate);
	}
	
	
	private static function validateAndCorrectQuantityQualifierForRx($strMessage)
	{
		
		global $medDB;
		
		
		$UNA = getDelimitersFromEDIFactMessage($strMessage);

		
		$strDRU = $UNA['5'] . 'DRU' . $UNA[1];
		$strUIT = $UNA['5'] . 'UIT' . $UNA[1];

		
		$arrMessage = explode($UNA[5], $strMessage);

		foreach($arrMessage as $intLine => $strLine)
		{
			$strSegmentID = 'DRU' . $UNA[1];
			if( strpos($strLine, $strSegmentID) !== false )
			{
				$intDRUIndex = $intLine;
				break;
			}
		}

		
		$DRU = $arrMessage[$intDRUIndex];

		
		$intDRULengthBeforeCorrection = strlen($DRU);
		$DRU = str_replace( $UNA[0] . $UNA[0] . 'ND' . $UNA[0], $UNA[0] . $UNA[0] . $UNA[0], $DRU);
		$intDRULengthAfterCorrection = strlen($DRU);

		
		if( $intDRULengthBeforeCorrection == $intDRULengthAfterCorrection )
		{
			
			
			
			$arrDRU = explode( $UNA[0] . 'ND' . $UNA[0], $DRU);

			
			$arrDRU = explode( $UNA[0], $arrDRU[0] );

			
			$NDC = $arrDRU[(count($arrDRU) - 1)];
			
			if( strlen($NDC) > 9 )
			{
				
				
				$QQ = $medDB->GetOne("SELECT qq FROM ndc_qq_mapping WHERE ndc = '" . $NDC . "'");
				
				
				$DRU = str_replace( $UNA[1] . 'ZZ' . $UNA[0], $UNA[1] . $QQ . $UNA[0], $DRU );
			}
		}
		
		
		$arrMessage[$intDRUIndex] = $DRU;
		
		
		$strMessage = implode($UNA[5], $arrMessage);
		
		
		return $strMessage;
	 }
	
	private function mapInMessageToArray($InMessage)
	{
		$arrRecord							=	array();

		$arrRecord['from_id']				=	$InMessage->FromID;
		$arrRecord['to_id']					=	$InMessage->ToID;
		$arrRecord['message_id']			=	$InMessage->MessageID;
		$arrRecord['related_message_id']	=	$InMessage->ReletedMessageID;

		$arrRecord['sent_time_in_message']	=	$InMessage->SentTime;
		$arrRecord['sms_version']			=	$InMessage->SmsVersion;
		$arrRecord['app_name']				=	$InMessage->AppName;
		$arrRecord['app_version']			=	$InMessage->AppVersion;
		$arrRecord['vendor_name']			=	$InMessage->VendorName;

		
		$arrRecord['received_time']				=	date("Y-m-d H:i:s");
		$arrRecord['edi_message']			=	$InMessage->EDIMessage;

		
		if($InMessage->MessageStatus != '')
			$arrRecord['message_status']		=	$InMessage->MessageStatus;
		if($InMessage->ErrorNote)
			$arrRecord['error_note']			=	$InMessage->ErrorNote;
		return $arrRecord;
	}

	

	private function getOutMessages($arrWhere)
	{
		global $medDB;
		$strSQL								=	$this->buildOutMessageReadQuery($arrWhere);
		$OutMessageSet						=	$medDB->GetAll($strSQL);
		$arrObjOutMessage					=	array();
		$strOutMessage						=	'';
		if(count($OutMessageSet) > 0)
		{
			foreach ($OutMessageSet as $OutMessage)
			{
				$objOutMessage					=	new OutMessage();
				$objOutMessage->TranID			=	$OutMessage['tran_id'];
				$objOutMessage->MeditabID		=	$OutMessage['meditab_id'];
				$objOutMessage->MeditabTranID	=	$OutMessage['meditab_tran_id'];
				$objOutMessage->FromID			=	$OutMessage['from_id'];
				$objOutMessage->ToID			=	$OutMessage['to_id'];
				$objOutMessage->SentTime		=	$OutMessage['sent_time'];
				$objOutMessage->EDIMessage		=	base64_encode($OutMessage['edi_message']);
				$objOutMessage->MessageStatus	=	$OutMessage['message_status'];
				$objOutMessage->ErrorNote		=	base64_encode($OutMessage['error_note']);

				
				$objOutMessage->MessageFrom		=	$OutMessage['message_from'];
				$objOutMessage->MessageID		=	$OutMessage['message_id'];
				$objOutMessage->RelatedMessageID=	$OutMessage['related_message_id'];
				$objOutMessage->SmsVersion		=	$OutMessage['sms_version'];
				$objOutMessage->AppName			=	$OutMessage['app_name'];
				$objOutMessage->AppVersion		=	$OutMessage['app_version'];
				$objOutMessage->VendorName		=	$OutMessage['vendor_name'];
				

				$objOutMessage->MeditabResponseStatus=	$OutMessage['meditab_response_status'];
				$arrObjOutMessage[]				=	$objOutMessage;
				unset($objOutMessage);
			}
		}
		return $arrObjOutMessage;
	}

	private function buildInMessageReadQuery($arrWhere)
	{
		
		$strWhere			=	$this->constructInMessageWhere($arrWhere);

		
		if($strWhere != '')
			$strWhere  		=	'WHERE ' . str_replace('nbsp;','',$strWhere);
		$strSQL		=	"SELECT * FROM " . INBOX_MESSAGE_TABLE . " " . $strWhere;

		if($arrWhere['mtx_record_limit'] != '' && $arrWhere['mtx_record_limit'] > 0)
		{
			$strSQL		.= " LIMIT " . $arrWhere['mtx_record_limit'];
		}
		return $strSQL;
	}

	
	private function constructInMessageWhere($arrWhere)
	{
		foreach($arrWhere as $strFilter=>$strCriteria)
		{
			if($strCriteria != '')
			{
				switch($strFilter)
				{
					case 'sent_time_from':
						$arrCondition[]	=	"sent_time_in_message >= '" . $strCriteria . "'";
						break;

					case 'sent_time_to':
						$arrCondition[]	=	"sent_time_in_message <= '" . $strCriteria . "'";
						break;

					case 'received_time_from':
						$arrCondition[]	=	"received_time >= '" . $strCriteria . "'";
						break;

					case 'received_time_to':
						$arrCondition[]	=	"received_time <= '" . $strCriteria . "'";
						break;

					case 'mtx_tran_id_from':
						$arrCondition[]	=	"tran_id >= " . $strCriteria;
						break;

					case 'mtx_tran_id_to':
						$arrCondition[]	=	"tran_id <= " . $strCriteria;
						break;

					case 'meditab_response_status':
						
						$arrCondition[]	=	"meditab_response_status = '" . strtoupper($strCriteria) . "'";
						break;

					case 'mtx_record_limit':
						break;

					default:
						$arrCondition[]	=	$strFilter . " = '" . $strCriteria . "'";
						break;

				}

			}
		}
		return implode(' AND ',$arrCondition);
	}

	
	private function buildOutMessageReadQuery($arrWhere)
	{
		
		$strWhere			=	$this->constructOutMessageWhere($arrWhere);

		
		if($strWhere != '')
		{
			$strWhere  		=	'WHERE ' . str_replace('nbsp;','',$strWhere);
		}
		
		$strSQL		=	"SELECT tran_id, meditab_id, message_from, meditab_tran_id, from_id, to_id, message_id,
						related_message_id, sent_time, sms_version, '' app_name, '' app_version, '' vendor_name,
						'NA' as edi_message, message_status, status_code, error_note, meditab_response_status FROM " . OUTBOX_MESSAGE_TABLE . " " . $strWhere;

		if($arrWhere['mtx_record_limit'] != '' && $arrWhere['mtx_record_limit'] > 0)
		{
			$strSQL		.= " LIMIT " . $arrWhere['mtx_record_limit'];
		}

		return $strSQL;
	}

	
	private function constructOutMessageWhere($arrWhere)
	{
		foreach($arrWhere as $strFilter=>$strCriteria)
		{
			if($strCriteria != '')
			{
				switch($strFilter)
				{
					case 'sent_time_from':
						$arrCondition[]	=	"sent_time >= '" . $strCriteria . "'";
						break;

					case 'sent_time_to':
						$arrCondition[]	=	"sent_time <= '" . $strCriteria . "'";
						break;

					case 'meditab_tran_id_from':
						$arrCondition[]	=	"meditab_tran_id >= " . $strCriteria;
						break;

					case 'meditab_tran_id_to':
						$arrCondition[]	=	"meditab_tran_id <= " . $strCriteria;
						break;

					case 'mtx_tran_id_from':
						$arrCondition[]	=	"tran_id >= " . $strCriteria;
						break;

					case 'mtx_tran_id_to':
						$arrCondition[]	=	"tran_id <= " . $strCriteria;
						break;

					case 'message_status':
						$arrCondition[]	=	"UPPER(message_status) = '" . strtoupper($strCriteria) . "'";
					case 'mtx_record_limit':
						break;

					case 'meditab_response_status':
						$arrCondition[]	=	"UPPER(meditab_response_status) = '" . strtoupper($strCriteria) . "'";
						break;

					case 'mtx_record_limit':
						break;

					default:
						$arrCondition[]	=	$strFilter . " = '" . $strCriteria . "'";
						break;

				}
			}
		}
		return implode(' AND ',$arrCondition);
	}
	
	public function getRelatedMessageID($strMessage)
	{
		$message = base64_decode($strMessage);
		$message = str_replace('<<MESSAGE_ID_PLACE_HOLDER>>','',$message);
		$message = str_replace('<<SENT_TIME_PLACE_HOLDER>>','',$message);
		$message = str_replace('<<SOFTWARE_DEVELOPER_PLACE_HOLDER>>','',$message);
		$message = str_replace('<<SOFTWARE_PRODUCT_PLACE_HOLDER>>','',$message);
		$message = str_replace('<<SOFTWARE_VERSION_PLACE_HOLDER>>','',$message);

		
		$objXml = new SimpleXMLElement($message);

		return $objXml->Header->RelatesToMessageID;

	}

	// Function to call Client Directory Log
	public function logDirectoryDownload($postdata)
	{
		global $medDB;
		$Result	=	'-1';
		$check_pharmacy	= MedPharmacy::GetPharmacyByNCPDPID($postdata->NCPDPID);

		$data = array();
		if( $check_pharmacy != NO_DATA_FOUND ) {
			$data['ncpdpid'] = $postdata->NCPDPID;
			$data['created_at'] = date('Y-m-d H:i:s');
			$Result = $medDB->AutoExecute('client_directory_log', $data, 'INSERT');
		}

		$objResponse = new Response();
		$objResponse->out =	$Result;

		return $objResponse;
	}

}

?>