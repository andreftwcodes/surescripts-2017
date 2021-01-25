<?PHP

	
	include_once('./base/med_html_table.php');

	
	$strAction		=	$objPage->getRequest("action");
	
	include_once("./base/med_module.php");
	
	$objModule		=	new MedModule();
	
	if($strAction == "EMP_DETAIL_FOR_VAR_CONTACT")
	{
		$strFieldNames	=	"direct_voip_phone,desg_id,fax_no,email";		
		$intEmpId	=	$objPage->getRequest('emp_id');		
		$rsEmployee		=	$objModule->getEmployeeDetail($intEmpId,$strFieldNames);
	
		if($rsEmployee[0]['desg_id'] != "" )
			$strDesignation	=	$objModule->getNameDesg($rsEmployee[0]['desg_id']);
		else
			$strDesignation	=	"";
		echo $strDesignation."|".$rsEmployee[0]['email']."|".$rsEmployee[0]['direct_voip_phone']."|".$rsEmployee[0]['fax_no'];
		exit;
	}
	if($strAction == "GET_VAR_NEWSLETTER_TEMPLATE_BODY")
	{
		$strFieldNames		=	"description";	
		$intTemplateId		=	$objPage->getRequest('template_id');						
		$strTemplateDetail	=	$objModule->getVarNewsLetterTemplateDetail($intTemplateId,$strFieldNames);
	
		echo $strTemplateDetail[0]['description'];
		exit;
	}
	if($strAction == "GET_VAR_NEWSLETTER_TEMPLATE_SUBJECT")
	{
		$strFieldNames		=	"subject";	
		$intTemplateId		=	$objPage->getRequest('template_id');						
		$strTemplateDetail	=	$objModule->getVarNewsLetterTemplateDetail($intTemplateId,$strFieldNames);
	
		echo $strTemplateDetail[0]['subject'];
		exit;
	}
	if($strAction == "GET_SERVER_STATS_BY_MEDITAB_ID")
	{
		
		$strServerType = $objGeneral->getSettings('SERVER_TYPE');
		
		$intMeditabId		=	$objPage->getRequest('meditab_id');
		
		if($intMeditabId	==	"NO-MEDITABID")
		{
			$intMeditabId	=	"";
		}
		
		if($strServerType == "PRESCRIBER_PARTNER")
		{
			$strSql	=	"SELECT COUNT(0) AS MSG_COUNT, FROM_ID as ENTITY_ID, MESSAGE_STATUS , 'OUTBOX' AS TYPE, MEDITAB_ID, 
					CONCAT(CLINIC_NAME,' (', CONCAT(PRESCRIBER_MASTER.first_name,' ', PRESCRIBER_MASTER.last_name), ')') as ENTITY_NAME
					FROM OUT_MESSAGE_TRANSACTION LEFT JOIN PRESCRIBER_MASTER ON OUT_MESSAGE_TRANSACTION.FROM_ID=PRESCRIBER_MASTER.SPI
					WHERE 
					MEDITAB_ID = '".$intMeditabId."' 
					GROUP BY FROM_ID, MESSAGE_STATUS 
					UNION
					SELECT COUNT(0) AS MSG_COUNT, TO_ID as ENTITY_ID, MESSAGE_STATUS , 'INBOX' AS TYPE, MEDITAB_ID, 
					CONCAT(CLINIC_NAME,' (', CONCAT(PRESCRIBER_MASTER.first_name,' ', PRESCRIBER_MASTER.last_name), ')') as ENTITY_NAME
					FROM IN_MESSAGE_TRANSACTION LEFT JOIN PRESCRIBER_MOS ON IN_MESSAGE_TRANSACTION.TO_ID = PRESCRIBER_MOS.SPI
					LEFT JOIN PRESCRIBER_MASTER ON IN_MESSAGE_TRANSACTION.TO_ID=PRESCRIBER_MASTER.SPI
					WHERE ";
					if($intMeditabId	==	"")
					{
						$strSql	.=	"TO_ID NOT IN (SELECT SPI FROM PRESCRIBER_MOS)";
					}
					else
					{					
						$strSql	.=	"MEDITAB_ID = '".$intMeditabId."'";
					}
		}
		else
		{
			$strSql	=	"SELECT COUNT(0) AS MSG_COUNT, FROM_ID as ENTITY_ID, MESSAGE_STATUS , 'OUTBOX' AS TYPE, MEDITAB_ID, 
					store_name as ENTITY_NAME
					FROM OUT_MESSAGE_TRANSACTION LEFT JOIN PHARMACY_MASTER ON OUT_MESSAGE_TRANSACTION.FROM_ID=PHARMACY_MASTER.NCPDPID
					WHERE 
					MEDITAB_ID = '".$intMeditabId."' 
					GROUP BY FROM_ID, MESSAGE_STATUS 
					UNION
					SELECT COUNT(0) AS MSG_COUNT, TO_ID as ENTITY_ID, MESSAGE_STATUS , 'INBOX' AS TYPE, MEDITAB_ID, 
					store_name as ENTITY_NAME
					FROM IN_MESSAGE_TRANSACTION LEFT JOIN PHARMACY_MOS ON IN_MESSAGE_TRANSACTION.TO_ID = PHARMACY_MOS.NCPDPID
					LEFT JOIN PHARMACY_MASTER ON IN_MESSAGE_TRANSACTION.TO_ID=PHARMACY_MASTER.NCPDPID
					WHERE ";
					if($intMeditabId	==	"")
					{
						$strSql	.=	"TO_ID NOT IN (SELECT NCPDPID FROM PHARMACY_MOS)";
					}
					else
					{					
						$strSql	.=	"MEDITAB_ID = '".$intMeditabId."'";
					}
		}
		$strSql	.=	" GROUP BY	TO_ID, MESSAGE_STATUS";

		$rsStaticsRecord				=	$objPage->executeSelect($strSql);
		echo $strServerStaticsTable	=	getStaticReportTable($rsStaticsRecord, $strServerType);
		exit;
	}
	if($strAction	==	'SEND_ERX')
	{
		$intARHId	=	$objPage->getRequest("arh_id");
		usleep(4000000);
		$objMedDb	=	MedDB::getDBObject();
		$strSql		=	"UPDATE rxh_ar_header
							SET mt_is_posted = 'Y'
							WHERE mt_rxh_arh_id = '".$intARHId."'";
		$objMedDb->executeQuery($strSql);
		exit;
	}
	
	function getStaticReportTable($rsStaticsRecord, $strServerType)
	{
		foreach($rsStaticsRecord as $arrStaticsRecord)
		{
			$arrReport[$arrStaticsRecord['entity_id']][$arrStaticsRecord['type']][$arrStaticsRecord['message_status']]	=	$arrStaticsRecord['msg_count'];			
			$arrReport[$arrStaticsRecord['entity_id']]["entity_name"]	=	$arrStaticsRecord['entity_name'];
		}
		
		$objMessageTable	=	new HtmlTable(9,'width="100%" cellpadding="0" cellspacing="0" border="0" style="border:1px solid #CCCCCC; border-collapse:collapse;"');
		
		
		if($strServerType == "PRESCRIBER_PARTNER")
		{
			$strEntityIdLabel = "SPI";
			$strEntityNameLabel = "Prescriber (Clinic)";
		}
		else
		{
			$strEntityIdLabel = "NCPDPID";
			$strEntityNameLabel = "Pharmacy";
		}
		$objMessageTable->addData($strEntityIdLabel, false, false,' align="left" width="17%" ','style="font-weight:bold; background-color:#EAF0F7; color:#5794BF;"');
		$objMessageTable->addData($strEntityNameLabel, false, false,'align="left" width="27%"');
		$objMessageTable->addData('Sent',false,false,'align="right" width="7%"','class="lblue"');
		$objMessageTable->addData('Pending',false,false,'align="right" width="7%"','class="lblue"');
		$objMessageTable->addData('Total',false,false,'align="right" width="8%"','class="lblue"');
		$objMessageTable->addData('Sent',false,false,'align="right" width="8%"','class="lblue"');
		$objMessageTable->addData('Pending',false,false,'align="right" width="7%"','class="lblue"');
		$objMessageTable->addData('Error',false,false,'align="right" width="7%"','class="lblue"');
		$objMessageTable->addData('Total',false,false,'align="right" width="9%"','class="lblue"');	
		
		
		$intRow = 0;

		foreach($arrReport as $strReportKey => $arrReportValue)
		{
			$blnForceNewRow = false;
			if($intIndex == 0)
			{
				$blnForceNewRow = true;
			}
			
			
			$strRowClass = ($intRow % 2 == 0) ? 'style="background-color:#FFFFFF;"' : 'style="background-color:#F4F6F7;"';
			
			
			if($arrReportValue['INBOX']['Sent'] == "")		$arrReportValue['INBOX']['Sent'] 	= 	0;
			if($arrReportValue['INBOX']['Pending'] == "")	$arrReportValue['INBOX']['Pending']	=	0;
			if($arrReportValue['INBOX']['Total'] == "")		$arrReportValue['INBOX']['Total']	=	0;
			if($arrReportValue['OUTBOX']['Sent'] == "")		$arrReportValue['OUTBOX']['Sent']	=	0;
			if($arrReportValue['OUTBOX']['Pending'] == "")	$arrReportValue['OUTBOX']['Pending']=	0;
			if($arrReportValue['OUTBOX']['Error'] == "")	$arrReportValue['OUTBOX']['Error']	=	0;
			if($arrReportValue['OUTBOX']['Total'] == "")	$arrReportValue['OUTBOX']['Total']	=	0;

			
			$objMessageTable->addData($strReportKey,$blnForceNewRow,false,'align="left" ',$strRowClass);
			$strSPIDetails	=	trim($arrReportValue['entity_name']);
			if(trim($strSPIDetails)	==	"")
			{
				$strSPIDetails	=	"-";
			}
			$objMessageTable->addData($strSPIDetails,false,false,'align="left"');
			$objMessageTable->addData($arrReportValue['INBOX']['Sent'],false,false,'align="right"');
			$objMessageTable->addData($arrReportValue['INBOX']['Pending'],false,false,'align="right"');
			
			$strInboxTotal		=	$arrReportValue['INBOX']['Sent']+$arrReportValue['INBOX']['Pending'];			
			$objMessageTable->addData($strInboxTotal,false,false,'align="right"');
			
			
			$objMessageTable->addData($arrReportValue['OUTBOX']['Sent'],false,false,'align="right"');
			$objMessageTable->addData($arrReportValue['OUTBOX']['Pending'],false,false,'align="right"');
			$objMessageTable->addData($arrReportValue['OUTBOX']['Error'],false,false,'align="right"');
			
			$strOutboxTotal	=	$arrReportValue['OUTBOX']['Sent'] + $arrReportValue['OUTBOX']['Pending'] + $arrReportValue['OUTBOX']['Error'];			
			$objMessageTable->addData($strOutboxTotal,false,false,'align="right"');
			
			$intRow += 1;

			$objMessageTable->addData("",false,true,' style="display:none;" id="row_'.$strReportKey.'" align="left"');				

		}


		
		return	$objMessageTable->getHtml();
	}


	exit;	
?>