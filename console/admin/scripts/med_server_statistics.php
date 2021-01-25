<?PHP


	
	include_once('./base/med_html_table.php');

	
	$strMiddle	= 	"./middle/med_server_statistics.htm";
	
	$strReportType = trim(strtoupper($objPage->getRequest('by')));
	if($strReportType == '')
	{
		$strReportType = 'RECEIVER';
	}
	
	
	$strServerType = $objPage->objGeneral->getSettings('SERVER_TYPE');

	
	switch($strServerType)
	{
		case 'PHARMACY_PARTNER':
			$arrLabel['FROM_ID'] = 'SPI';
			$arrLabel['TO_ID'] = 'NCPDPID';
			$strFromField = 'spi';
			$strToField = 'ncpdpid';
			$strFromNameField = "CONCAT(first_name,'',last_name,IF(clinic_name = NULL OR clinic_name = '',' ',CONCAT(' -',clinic_name)))";
			$strToNameField = 'store_name';
			$arrLabel['FROM_NAME'] = 'Prescriber';
			$arrLabel['TO_NAME'] = 'Pharmacy';
			$strFromEntityTable = 'prescriber_master';
			$strToEntityTable = 'pharmacy_master';
			break;
		case 'PRESCRIBER_PARTNER':
			$arrLabel['FROM_ID'] = 'NCPDPID';
			$arrLabel['TO_ID'] = 'SPI';
			$strFromField = 'ncpdpid';
			$strToField = 'spi';
			$strFromNameField = 'store_name';
			$strToNameField = "CONCAT(first_name,'',last_name,IF(clinic_name = NULL OR clinic_name = '',' ',CONCAT(' -',clinic_name)))";
			$arrLabel['FROM_NAME'] = 'Pharmacy';
			$arrLabel['TO_NAME'] = 'Prescriber';
			$strFromEntityTable = 'pharmacy_master';
			$strToEntityTable = 'prescriber_master';
			break;
		default:
			echo '<h2 style="color:#ff0000">Server is not configured properly; Please contact Web Application Administrator.</h2>';
			exit;
	}
	
	$strSenderField = $strFromNameField;
	$strSenderJoinSQL = 'LEFT JOIN ' . $strFromEntityTable . ' ON TBL.ENTITY = ' . $strFromEntityTable . '.' . $strFromField;
	
	
	$strReceiverField = $strToNameField;
	$strReceiverJoinSQL = 'LEFT JOIN ' . $strToEntityTable . ' ON TBL.ENTITY = ' . $strToEntityTable . '.' . $strToField;
	
	if($strReportType == 'SENDER')
	{
		$strReportLink = '<a href="index.php?file=med_server_statistics&by=RECEIVER">View by Receiver</a>';
		
		$strSQL		=	"
						SELECT TBL.*," . $strSenderField . " AS NAME 
						FROM 
						(
							SELECT from_id AS ENTITY,message_status,COUNT(0) MSG_COUNT,'INBOX' AS MSG_TYPE 
							FROM in_message_transaction	GROUP BY from_id,message_status 
							UNION 
							SELECT from_id AS ENTITY,'Total' as message_status,COUNT(0) MSG_COUNT,'INBOX' AS MSG_TYPE 
							FROM in_message_transaction GROUP BY from_id 
							UNION
							SELECT to_id AS ENTITY,message_status,COUNT(0) MSG_COUNT,'OUTBOX' AS MSG_TYPE 
							FROM out_message_transaction GROUP BY to_id,message_status 
							UNION
							SELECT to_id AS ENTITY,'Total' as message_status,COUNT(0) MSG_COUNT,'OUTBOX' AS MSG_TYPE 
							FROM out_message_transaction GROUP BY to_id 
						)
						AS TBL " . $strSenderJoinSQL . "
						GROUP BY MSG_TYPE,ENTITY,message_status 
						ORDER BY MSG_TYPE
						";
	
		$rsMessageSummary	=	$objPage->executeSelect($strSQL);
		
		
		$strBySenderTable	=	getReportTable($rsMessageSummary, 'VIEW_BY_SENDER', $arrLabel);
		
	}
	else if($strReportType == 'RECEIVER')
	{
		$strReportLink = '<a href="index.php?file=med_server_statistics&by=SENDER">View by Sender</a>';
		
		$strSQL		=	"
						SELECT TBL.*," . $strReceiverField . "  AS NAME  
						FROM 
						(
							SELECT to_id AS ENTITY,message_status,COUNT(0) MSG_COUNT,'INBOX' AS MSG_TYPE 
							FROM in_message_transaction	GROUP BY to_id,message_status 
							UNION 
							SELECT to_id AS ENTITY,'Total' as message_status,COUNT(0) MSG_COUNT,'INBOX' AS MSG_TYPE 
							FROM in_message_transaction GROUP BY to_id 
							UNION
							SELECT from_id AS ENTITY,message_status,COUNT(0) MSG_COUNT,'OUTBOX' AS MSG_TYPE 
							FROM out_message_transaction GROUP BY from_id,message_status 
							UNION
							SELECT from_id AS ENTITY,'Total' as message_status,COUNT(0) MSG_COUNT,'OUTBOX' AS MSG_TYPE 
							FROM out_message_transaction GROUP BY from_id 
						)
						AS TBL  " . $strReceiverJoinSQL . "
						GROUP BY MSG_TYPE,ENTITY,message_status 
						ORDER BY MSG_TYPE
						";
	
		$rsMessageSummary		=	$objPage->executeSelect($strSQL);
		
		
		$strByReceiverTable		=	getReportTable($rsMessageSummary, 'VIEW_BY_RECEIVER', $arrLabel);
		
		
	}

	
	$localValues 			= 	array(
									"strBySenderTable"		=>	$strBySenderTable,
									"strByReceiverTable"	=>	$strByReceiverTable,
									"strReportLink" => $strReportLink,
									"strReportType" => $strReportType
								);

	
	function getReportTable($rsMessageSummary, $strReportType, $arrLabel)
	{
		switch($strReportType)
		{
			case 'VIEW_BY_RECEIVER':
				$strEntityID = $arrLabel['TO_ID'];
				$strEntityName = $arrLabel['TO_NAME']; 
				break;
			case 'VIEW_BY_SENDER':
				$strEntityID = $arrLabel['FROM_ID'];
				$strEntityName = $arrLabel['FROM_NAME'];
				break;
		}
		foreach($rsMessageSummary as $arrMessageSummary)
		{
			$arrReport[$arrMessageSummary['entity']][$arrMessageSummary['msg_type']][$arrMessageSummary['message_status']]	=	$arrMessageSummary['msg_count'];
			$arrEntityName[$arrMessageSummary['entity']]	=	$arrMessageSummary['name'];
		}
		
		
		$objMessageTable	=	new HtmlTable(9,'width="99%"  border="0" cellpadding="0" cellspacing="0" class="rpt_table"',"",'valign="top" ');
		
		
		$objMessageTable->addData('',false,false,' align="center"','class="header"');
		$objMessageTable->addData('',false,false,' align="center"','class="header"');
		$objMessageTable->addData('Inbox',false,false,'colspan="3" align="center"','class="header"');
		$objMessageTable->addData('Outbox',false,false,'colspan="4" align="center"','class="header"');

		
		$objMessageTable->addData($strEntityName,true,false,' align="left"','class="lblue"');
		$objMessageTable->addData($strEntityID,false,false,' align="right"','class="lblue"');
		$objMessageTable->addData('Sent',false,false,'align="right"','class="lblue"');
		$objMessageTable->addData('Pending',false,false,'align="right"','class="lblue"');
		$objMessageTable->addData('Total',false,false,'align="right"','class="lblue"');
		$objMessageTable->addData('Sent',false,false,'align="right"','class="lblue"');
		$objMessageTable->addData('Pending',false,false,'align="right"','class="lblue"');
		$objMessageTable->addData('Error',false,false,'align="right"','class="lblue"');
		$objMessageTable->addData('Total',false,false,'align="right"','class="lblue"');
		
		
		$intRow = 0;

		foreach($arrReport as $strReportKey => $arrReportValue)
		{
			$blnForceNewRow = false;
			if($intIndex == 0)
			{
				$blnForceNewRow = true;
			}
			
			
			$strRowClass = ($intRow%2 == 0) ? 'class="even"' : 'class="odd"';
			
			
			if($arrReportValue['INBOX']['Sent'] == "")		$arrReportValue['INBOX']['Sent'] = 0;
			if($arrReportValue['INBOX']['Pending'] == "")	$arrReportValue['INBOX']['Pending']	=	0;
			if($arrReportValue['INBOX']['Total'] == "")		$arrReportValue['INBOX']['Total']	=	0;
			if($arrReportValue['OUTBOX']['Sent'] == "")		$arrReportValue['OUTBOX']['Sent']	=	0;
			if($arrReportValue['OUTBOX']['Pending'] == "")	$arrReportValue['OUTBOX']['Pending']=	0;
			if($arrReportValue['OUTBOX']['Error'] == "")	$arrReportValue['OUTBOX']['Error']	=	0;
			if($arrReportValue['OUTBOX']['Total'] == "")	$arrReportValue['OUTBOX']['Total']	=	0;

			
			$objMessageTable->addData($arrEntityName[$strReportKey],$blnForceNewRow,false,'align="left" ',$strRowClass);
			$objMessageTable->addData($strReportKey,false,false,'align="right" ',$strRowClass);
			$objMessageTable->addData($arrReportValue['INBOX']['Sent'],false,false,'align="right"',$strRowClass);
			$objMessageTable->addData($arrReportValue['INBOX']['Pending'],false,false,'align="right"',$strRowClass);
			$objMessageTable->addData($arrReportValue['INBOX']['Total'],false,false,'align="right"',$strRowClass);
			
			
			$objMessageTable->addData($arrReportValue['OUTBOX']['Sent'],false,false,'align="right"',$strRowClass);
			$objMessageTable->addData($arrReportValue['OUTBOX']['Pending'],false,false,'align="right"',$strRowClass);
			$objMessageTable->addData($arrReportValue['OUTBOX']['Error'],false,false,'align="right"',$strRowClass);
			$objMessageTable->addData($arrReportValue['OUTBOX']['Total'],false,false,'align="right"',$strRowClass);
			
			$intRow += 1;

			
			$intTotInboxSent		=	$intTotInboxSent + $arrReportValue['INBOX']['Sent'];
			$intTotInboxPending		=	$intTotInboxPending + $arrReportValue['INBOX']['Pending'];
			$intTotInboxTotal		=	$intTotInboxTotal + $arrReportValue['INBOX']['Total'];
			$intTotOutboxSent		=	$intTotOutboxSent + $arrReportValue['OUTBOX']['Sent'];
			$intTotOutboxPending	=	$intTotOutboxPending + $arrReportValue['OUTBOX']['Pending'];
			$intTotOutboxError		=	$intTotOutboxError + $arrReportValue['OUTBOX']['Error'];
			$intTotOutboxTotal		=	$intTotOutboxTotal + $arrReportValue['OUTBOX']['Total'];

		}
		
		$objMessageTable->addData('<B>Total</B>',false,false,'colspan="2" align="right"','style="background-color:#FFE4B5"');
		$objMessageTable->addData($intTotInboxSent,false,false,'align="right" class="bold-text"',$strRowClass);
		$objMessageTable->addData($intTotInboxPending,false,false,'align="right" class="bold-text"',$strRowClass);
		$objMessageTable->addData($intTotInboxTotal,false,false,'align="right" class="bold-text"',$strRowClass);

		$objMessageTable->addData($intTotOutboxSent,false,false,'align="right" class="bold-text"',$strRowClass);
		$objMessageTable->addData($intTotOutboxPending,false,false,'align="right" class="bold-text"',$strRowClass);
		$objMessageTable->addData($intTotOutboxError,false,false,'align="right" class="bold-text"',$strRowClass);
		$objMessageTable->addData($intTotOutboxTotal,false,true,'align="right" class="bold-text"',$strRowClass);

		
		return	$objMessageTable->getHtml();
	}
?>