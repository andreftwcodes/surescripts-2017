<?php

	
	include_once('med_config.php');
	
	include_once(WEB_ROOT.'base/DB.php');
	
	include_once(WEB_ROOT.'base/MedCommon.php');
	
	$strMethod = strtoupper($_REQUEST['m']);
	switch($strMethod)
	{
		case "PRESCRIBER_LIST":
			$arrResult = getPrescriberList($_POST['mid'], $_POST['spi']);
			break;
		case "GET_PRESCRIBER_MESSAGES":
			$strGetAll	=	$_POST['getAll'];
			$arrResult = getPrescriberMessages($_POST['mid'], $_POST['spi'], $_POST['getAll']);
			break;
		case "PHARMACY_LIST":
			$arrResult = getPharmacyList($_POST['mid'], $_POST['ncpdpid']);
			break;
		case "GET_PHARMACY_MESSAGES":
			$strGetAll	=	$_POST['getAll'];
			$arrResult = getPharmacyMessages($_POST['mid'], $_POST['ncpdp'], $_POST['getAll']);
			break;
	}
	
	if($strMethod != "")
	{
		
		echo serialize($arrResult);
	}
	
	
	
	function getPrescriberList($strMeditabID = "", $strSPI = "")
	{
		global $medDB;
		
		
		if(strpos($strMeditabID,",") !== FALSE)
		{
			$strMeditabID = "'" . implode("','",explode(",", $strMeditabID)) . "'";
		}
		if(strpos($strSPI,",") !== FALSE)
		{
			$strSPI = "'" . implode("','",explode(",", $strSPI)) . "'";
		}
		
		$arrWhere = array();
		
		if($strMeditabID != "")
		{
			$arrWhere[] = " meditab_id IN (" . $strMeditabID . ") ";
		}
		if($strSPI != "")
		{
			$arrWhere[] = " spi IN (" . $strSPI . ") ";
		}
		if(is_array($arrWhere) == TRUE && count($arrWhere)> 0 )
		{
			$strWhere = "WHERE " . implode(" ", $arrWhere);
		}
		
		$strSQL = "SELECT * FROM prescriber_master INNER JOIN prescriber_mos ON 
					   prescriber_master.spi = prescriber_mos.spi 
					   " . $strWhere;
		$rsResult = $medDB->getAll($strSQL);
		return $rsResult;
	}
	
	
	
	function getPrescriberMessages($strMeditabID = "", $strSPI = "", $strGetAll = "")
	{
		global $medDB;
		$strSQL = "SELECT COUNT(0) AS MSG_COUNT, FROM_ID as SPI, MESSAGE_STATUS , 'OUTBOX' AS TYPE, MEDITAB_ID, CLINIC_NAME, CONCAT(PRESCRIBER_MASTER.first_name,' ', PRESCRIBER_MASTER.last_name) as PRE_NAME
					FROM OUT_MESSAGE_TRANSACTION LEFT JOIN PRESCRIBER_MASTER ON OUT_MESSAGE_TRANSACTION.FROM_ID=PRESCRIBER_MASTER.SPI WHERE 1 ";		

		
		if($strMeditabID !=	"")
		{
			$strSearchCondition .=	" AND MEDITAB_ID = '".$strMeditabID."' ";
		}
		if($strSPI != "")
		{
			$strSearchCondition .=	"AND SPI	IN	(".$strSPI.")";
		}

		
		$strSQL   .= $strSearchCondition;
		if($strGetAll	==	"Yes")
		{
			$strSQL   .= " GROUP BY PRESCRIBER_MASTER.SPI, MESSAGE_STATUS ";
		}
		else
		{
			
			$strSQL   .= " GROUP BY TYPE, MESSAGE_STATUS ";
		}			
		$strSQL   .= " UNION";
					
		$strSQL   .= " SELECT COUNT(0) AS MSG_COUNT, TO_ID as SPI, MESSAGE_STATUS , 'INBOX' AS TYPE, MEDITAB_ID, CLINIC_NAME, CONCAT(PRESCRIBER_MASTER.first_name,' ', PRESCRIBER_MASTER.last_name) as PRE_NAME
					FROM IN_MESSAGE_TRANSACTION LEFT JOIN PRESCRIBER_MOS ON IN_MESSAGE_TRANSACTION.to_id = PRESCRIBER_MOS.SPI
					LEFT JOIN PRESCRIBER_MASTER ON IN_MESSAGE_TRANSACTION.TO_ID=PRESCRIBER_MASTER.SPI WHERE 1	";
		if($strMeditabID !=	"")
		{
			$strSQL .=	" AND MEDITAB_ID = '".$strMeditabID."' ";
		}					
		if($strSPI != "")
		{
				$strSQL .=	" AND PRESCRIBER_MOS.SPI	IN	(".$strSPI.")";
		}				
		if($strGetAll	==	"Yes")
		{
			$strSQL   .= " GROUP BY PRESCRIBER_MASTER.SPI, MESSAGE_STATUS ";
		}
		else
		{
			$strSQL 	.= " GROUP BY TYPE, MESSAGE_STATUS";
		}
		$strSQL;
		$rsResult = $medDB->getAll($strSQL);
		return $rsResult;
	}
	
	function getPharmacyList($strMeditabID = "", $strNCPDPId = "")
	{
		global $medDB;
		
		
		if(strpos($strMeditabID,",") !== FALSE)
		{
			$strMeditabID = "'" . implode("','",explode(",", $strMeditabID)) . "'";
		}
		if(strpos($strNCPDPId,",") !== FALSE)
		{
			$strNCPDPId = "'" . implode("','",explode(",", $strNCPDPId)) . "'";
		}
		
		$arrWhere = array();
		
		if($strMeditabID != "")
		{
			$arrWhere[] = " meditab_id IN (" . $strMeditabID . ") ";
		}
		if($strNCPDPId != "")
		{
			$arrWhere[] = " ncpdpid IN (" . $strNCPDPId . ") ";
		}
		if(is_array($arrWhere) == TRUE && count($arrWhere)> 0 )
		{
			$strWhere = "WHERE " . implode(" ", $arrWhere);
		}
		
		$strSQL = "SELECT * FROM pharmacy_master INNER JOIN pharmacy_mos ON 
					   pharmacy_master.ncpdpid = pharmacy_mos.ncpdpid 
					   " . $strWhere;
		$rsResult = $medDB->getAll($strSQL);
		return $rsResult;
	}
	
	
	
	function getPharmacyMessages($strMeditabID = "", $strNCPDPId = "", $strGetAll = "")
	{
		global $medDB;
		$strSQL = "SELECT COUNT(0) AS MSG_COUNT, FROM_ID as NCPDPID, MESSAGE_STATUS , 'OUTBOX' AS TYPE, MEDITAB_ID, PHARMACY_MASTER.STORE_NAME as STORE_NAME
					FROM OUT_MESSAGE_TRANSACTION LEFT JOIN PHARMACY_MASTER ON OUT_MESSAGE_TRANSACTION.FROM_ID=PHARMACY_MASTER.NCPDPID WHERE 1 ";		

		
		if($strMeditabID !=	"")
		{
			$strSearchCondition .=	" AND MEDITAB_ID = '".$strMeditabID."' ";
		}
		if($strNCPDPId != "")
		{
			$strSearchCondition .=	"AND NCPDPID	IN	(".$strNCPDPId.")";
		}

		
		$strSQL   .= $strSearchCondition;
		if($strGetAll	==	"Yes")
		{
			$strSQL   .= " GROUP BY PHARMACY_MASTER.NCPDPID, MESSAGE_STATUS ";
		}
		else
		{
			
			$strSQL   .= " GROUP BY TYPE, MESSAGE_STATUS ";
		}			
		$strSQL   .= " UNION";
					
		$strSQL   .= " SELECT COUNT(0) AS MSG_COUNT, TO_ID as NCPDPID, MESSAGE_STATUS , 'INBOX' AS TYPE, MEDITAB_ID, PHARMACY_MASTER.STORE_NAME as STORE_NAME
					FROM IN_MESSAGE_TRANSACTION LEFT JOIN PHARMACY_MOS ON IN_MESSAGE_TRANSACTION.to_id = PHARMACY_MOS.NCPDPID
					LEFT JOIN PHARMACY_MASTER ON IN_MESSAGE_TRANSACTION.TO_ID=PHARMACY_MASTER.NCPDPID WHERE 1	";
		if($strMeditabID !=	"")
		{
			$strSQL .=	" AND MEDITAB_ID = '".$strMeditabID."' ";
		}					
		if($strNCPDPId != "")
		{
				$strSQL .=	" AND PHARMACY_MOS.NCPDPID	IN	(".$strNCPDPId.")";
		}				
		if($strGetAll	==	"Yes")
		{
			$strSQL   .= " GROUP BY PHARMACY_MASTER.NCPDPID, MESSAGE_STATUS ";
		}
		else
		{
			$strSQL 	.= " GROUP BY TYPE, MESSAGE_STATUS";
		}		
		$rsResult = $medDB->getAll($strSQL);
		return $rsResult;
	}
	
?>