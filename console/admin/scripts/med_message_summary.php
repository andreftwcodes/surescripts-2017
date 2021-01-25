<?PHP


	
	include_once('./base/med_html_table.php');

	
	$strMiddle	= 	"./middle/med_message_summary.htm";
	
	$strSQL		=	"
					SELECT COUNT(0) AS tot_count,'TOTAL_IN_MESSAGE' as label FROM in_message_transaction
					UNION 
					SELECT COUNT(0) AS tot_count,'SENT_IN_MESSAGE' as label FROM in_message_transaction 
					WHERE message_status ='Sent' 
					UNION 
					SELECT COUNT(0) AS tot_count,'PENDING_IN_MESSAGE' as label FROM in_message_transaction 
					WHERE message_status ='Pending' 
					UNION
					SELECT COUNT(0) AS tot_count,'TOTAL_OUT_MESSAGE' as label FROM out_message_transaction
					UNION 
					SELECT COUNT(0) AS tot_count,'SENT_OUT_MESSAGE' as label FROM out_message_transaction 
					WHERE message_status ='Sent' 
					UNION 
					SELECT COUNT(0) AS tot_count,'PENDING_OUT_MESSAGE' as label FROM out_message_transaction 
					WHERE message_status ='Pending' 
					UNION 
					SELECT COUNT(0) AS tot_count,'ERROR_OUT_MESSAGE' as label FROM out_message_transaction 
					WHERE message_status ='Error'
					";

	$rsMessageSummary	=	$objPage->executeSelect($strSQL);
	
	
	$objMessageTable	=	new HtmlTable(7,'width="99%"  border="0" cellpadding="0" cellspacing="0" class="rpt_table"',"",'valign="top" ');
	
	
	$objMessageTable->addData('Inbox',false,false,'colspan="3" align="center"','class="header"');
	$objMessageTable->addData('Outbox',false,false,'colspan="4" align="center"','class="header"');

	
	$objMessageTable->addData('Total',true,false,'align="right"','class="lblue"');
	$objMessageTable->addData('Sent',false,false,'align="right"','class="lblue"');
	$objMessageTable->addData('Pending',false,false,'align="right"','class="lblue"');
	$objMessageTable->addData('Total',false,false,'align="right"','class="lblue"');
	$objMessageTable->addData('Sent',false,false,'align="right"','class="lblue"');
	$objMessageTable->addData('Pending',false,false,'align="right"','class="lblue"');
	$objMessageTable->addData('Error',false,false,'align="right"','class="lblue"');

	for($intIndex = 0, $intTotal = count($rsMessageSummary); $intIndex < $intTotal; $intIndex++)
	{
		$blnForceNewRow = false;
		if($intIndex == 0)
		{
			$blnForceNewRow = true;
		}

		
		$objMessageTable->addData($rsMessageSummary[$intIndex]['tot_count'], $blnForceNewRow,false,'align="right"');
	}
	
	
	$strMessageTable		=	$objMessageTable->getHtml();

	
	$localValues 		= 	array(
									"strMessageTable"	=>	$strMessageTable,
								);
?>