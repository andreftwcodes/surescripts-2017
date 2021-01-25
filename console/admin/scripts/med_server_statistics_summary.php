<?PHP


	
	include_once('./base/med_html_table.php');
	
	
	$strServerType = $objGeneral->getSettings('SERVER_TYPE');
	
	
	$strMiddle	= 	"./middle/med_server_statistics_summary.htm";
	
	
	$arrExtSearch	=	array();
	
	
	if($_POST)
	{
		$objPage->objGeneral->setSession("server_stat_filter_set", "Y");
	}
	
	
	if($_POST && $objPage->getRequest("txt_date_from") != '')
	{
		$strDateFrom	=	$objPage->getRequest("txt_date_from");
		$objPage->objGeneral->setSession("server_stat_date_from", $strDateFrom);		
	}
	else if($_POST && $objPage->getRequest("txt_date_from") == '')
	{
		$strDateFrom	=	$objPage->getRequest("txt_date_from");
		$objPage->objGeneral->setSession("server_stat_date_from", "");		
	}
	else if($objPage->objGeneral->getSession("server_stat_date_from") != '')
	{
		$strDateFrom	=	$objPage->objGeneral->getSession("server_stat_date_from");		
	}
	else if($objPage->objGeneral->getSession("server_stat_filter_set") != 'Y')
	{
		$strDateFrom	=	date("m-d-Y", strtotime("-7 Days"));
	}
	
	
	if($_POST && $objPage->getRequest("txt_date_to") != '')
	{
		$strDateTo	=	$objPage->getRequest("txt_date_to");
		$objPage->objGeneral->setSession("server_stat_date_to", $strDateTo);
	}
	else if($_POST && $objPage->getRequest("txt_date_to") == '')
	{
		$strDateTo	=	$objPage->getRequest("txt_date_to");
		$objPage->objGeneral->setSession("server_stat_date_to", "");		
	}
	else if($objPage->objGeneral->getSession("server_stat_date_to") != '')
	{
		$strDateTo	=	$objPage->objGeneral->getSession("server_stat_date_to");
	}
	else if($objPage->objGeneral->getSession("server_stat_filter_set") != 'Y')
	{
		$strDateTo	=	date("m-d-Y");
	}
	
	
	$arrStatus		=	$objPage->getRequest("slt_status");
	if($_POST && count($arrStatus) > 0)
	{
		$strStatus		=	implode(",", $arrStatus);
		$objPage->objGeneral->setSession("server_stat_status", $strStatus);
	}
	else if($_POST && count($arrStatus) == 0)
	{
		$strStatus		=	"";
		$objPage->objGeneral->setSession("server_stat_status", "");
	}
	else if($objPage->objGeneral->getSession("server_stat_status") != '')
	{
		$strStatus		=	$objPage->objGeneral->getSession("server_stat_status");
	}
	else if($objPage->objGeneral->getSession("server_stat_filter_set") != 'Y')
	{
		$strStatus		=	"Pending,Error";
	}	
	
	
	if($strDateFrom != '')
	{
		$arrDateFrom	=	explode("-",$strDateFrom);
		$arrExtSearch[]	=	"DATE_FORMAT(MED_TBL.PROCESS_DATE, '%Y-%m-%d') >= '".date($arrDateFrom[2]."-".$arrDateFrom[0]."-".$arrDateFrom[1])."'";
	}

	
	if($strDateTo != '')
	{
		$arrDateTo		=	explode("-",$strDateTo);
		$arrExtSearch[]	=	"DATE_FORMAT(MED_TBL.PROCESS_DATE, '%Y-%m-%d') <= '".date($arrDateTo[2]."-".$arrDateTo[0]."-".$arrDateTo[1])."'";
	}
	
	
	if($strStatus != '')
	{
		$arrExtSearch[]	=	"MED_TBL.MESSAGE_STATUS IN ('".str_replace(",", "','", $strStatus)."')";
	}
	
	if(count($arrExtSearch) > 0)
	{
		$strExtSearch	=	implode(" AND ", $arrExtSearch);
		$strExtSearch	=	" WHERE ".$strExtSearch;
	}


	
	if($strServerType == "PRESCRIBER_PARTNER")
	{
		$strSQL		=	"
						 SELECT * FROM (
						 SELECT if(MEDITAB_ID<>'',MEDITAB_ID,'NO-MEDITABID') as meditab_id, MESSAGE_STATUS, COUNT(0) AS MSG_COUNT, 'OUTBOX' AS TYPE , sent_time as PROCESS_DATE
						 
						 FROM 
						 OUT_MESSAGE_TRANSACTION
						 GROUP BY meditab_id, message_status
						 
						 UNION
						 
						 SELECT if(MEDITAB_ID<>'',MEDITAB_ID,'NO-MEDITABID') as meditab_id, MESSAGE_STATUS, COUNT(0) AS MSG_COUNT, 'INBOX' AS TYPE, received_time as PROCESS_DATE
						 FROM IN_MESSAGE_TRANSACTION LEFT JOIN prescriber_mos ON to_id = spi
						 GROUP BY meditab_id, message_status
						 ) AS MED_TBL ".$strExtSearch." ORDER BY TYPE ASC, MESSAGE_STATUS ASC, MSG_COUNT DESC";
	}
	else if($strServerType == "PHARMACY_PARTNER")
	{
		$strSQL		=	"
						 SELECT * FROM (
						 SELECT if(MEDITAB_ID<>'',MEDITAB_ID,'NO-MEDITABID') as meditab_id, MESSAGE_STATUS, COUNT(0) AS MSG_COUNT, 'OUTBOX' AS TYPE, sent_time as PROCESS_DATE
						 FROM 
						 OUT_MESSAGE_TRANSACTION
						 GROUP BY meditab_id, message_status
						 
						 UNION
						 
						 SELECT if(MEDITAB_ID<>'',MEDITAB_ID,'NO-MEDITABID') as meditab_id, MESSAGE_STATUS, COUNT(0) AS MSG_COUNT, 'INBOX' AS TYPE, received_time as PROCESS_DATE
						 FROM IN_MESSAGE_TRANSACTION LEFT JOIN pharmacy_mos ON to_id = ncpdpid
						 GROUP BY meditab_id, message_status
						 ) AS MED_TBL ".$strExtSearch." ORDER BY TYPE DESC, MESSAGE_STATUS ASC, MSG_COUNT DESC";
	}
	else
	{
		echo "Invalid Server Type configured, please correct it under Settings or contact administrator.";
		exit;
	}

	$rsStaticsRecord		=	$objPage->executeSelect($strSQL);
	
	$strFilterTable			=	createSearchFilter($strDateFrom, $strDateTo, $strStatus);
	
	$strServerStaticsTable	=	getStaticReportTable($rsStaticsRecord);
	
	
	$localValues 			= 	array(
									"strFilterTable"		=>	$strFilterTable,
									"strServerStaticsTable"	=>	$strServerStaticsTable
									
								);
								
	function createSearchFilter($strDateFrom, $strDateTo, $strStatus)
	{
		global $objPage;		
		
		
		$strFilterTable		.=	'<table border="0" width="100%" style="border:1px solid #CCCCCC; border-collapse:collapse;" cellpadding="2" cellspacing="2" align="right" valign="middle" >';
		$strFilterTable		.=	'<tr><td>';
		$strFilterTable		.=	'<table border="0" style="border-collapse:collapse;" cellpadding="0" cellspacing="2" align="right" valign="middle" >';
		$strFilterTable		.=	'<tr>';
		
		$strFilterTable		.=	'<td valign="top" style="padding-top:4px;"><font class="blue-text-bold-01" >Date From:</font></td>';
		$strFilterTable		.=	'<td valign="top" align="left" style="padding:0;">';
			$strFilterTable		.=	'<table style="padding:0;"><tr><td align="right"><input class="comn-input" size="8" type="text" name="txt_date_from" id="txt_date_from" value="'.$strDateFrom.'" /></td><td align="left"><img align="absmiddle" width="19" vspace="-1" hspace="0" height="14" id="clt_DtRtxt_date_from" style="cursor:pointer;" name="clt_DtRtxt_date_from" src="./../base/jscalendar/calender_small.gif">';
			$strFilterTable		.=	'<script language="javascript1.2">
									Calendar.setup({inputField : "txt_date_from",
											ifFormat : "%m-%d-%Y",
											button : "clt_DtRtxt_date_from" });
									</script>
									<script> var LBL_txt_date_from="Date From";</script></td></tr></table>';
									
		$strFilterTable		.=	'</td>';
		
		$strFilterTable		.=	'<td valign="top" style="padding-top:4px;"><font class="blue-text-bold-01">To:</font></td>';		
		$strFilterTable		.=	'<td valign="top" align="left" style="padding:0;">';
			$strFilterTable		.=	'<table style="padding:0;"><tr><td><input class="comn-input" size="8" type="text" name="txt_date_to" id="txt_date_to" value="'.$strDateTo.'" /></td><td><img align="absmiddle" width="19" vspace="0" hspace="0" height="14" id="clt_DtRtxt_date_to" style="cursor:pointer;" name="clt_DtRtxt_date_from" src="./../base/jscalendar/calender_small.gif">';
			$strFilterTable		.=	'<script language="javascript1.2">
									Calendar.setup({inputField : "txt_date_to",
											ifFormat : "%m-%d-%Y",
											button : "clt_DtRtxt_date_to" });
									</script>
									<script> var LBL_txt_date_to="To";</script></td></tr></table>';

		$strFilterTable		.=	'</td>';
		
		$strFilterTable		.=	'<td valign="top" style="padding:3px;"><font class="blue-text-bold-01"> Status: </font></td>';
		$strFilterTable		.=	'<td valign="top">';
			$strStatusCombo		=	$objPage->getComboBox("status","","MESSAGE_STATUS:SL:NT:'All'",$strStatus,0,"class='comn-input'",$strType=true);
			$strFilterTable		.=	$strStatusCombo;
			$strFilterTable		.=	'<img border="0" hspace="3" style="cursor:pointer" onmouseout="return nd(1000);" onmouseover="getSelectedComboValue(\'slt_status[]\',\'zoom_slt_status\');" onclick="zoomCombo(\'slt_status[]\',this,\'2\',\'4\')" id="zoom_slt_status" src="images/s_zoom_up.gif">';
		$strFilterTable		.=	'</td>';
		
		$strFilterTable		.=	'<td valign="bottom">';
		$strFilterTable		.=	'<input type="submit" onclick="return submitSearchForm(this.form);" class="btn" value="Search" name="btn_submit">';
		$strFilterTable		.=	'<td>';
		
		$strFilterTable		.=	'</tr>';
		$strFilterTable		.=	'</table>';
		$strFilterTable		.=	'</td></tr>';
		$strFilterTable		.=	'</table>';
		return $strFilterTable;
	}
	
	function getStaticReportTable($rsStaticsRecord)
	{
		global $objPage;
		foreach($rsStaticsRecord as $arrStaticsRecord)
		{
			$arrReport[$arrStaticsRecord['meditab_id']][$arrStaticsRecord['type']][$arrStaticsRecord['message_status']]	=	$arrStaticsRecord['msg_count'];
		}		
		
		
		$objMessageTable	=	new HtmlTable(9,'width="100%"  border="0"  style="border-collapse:collapse;" cellpadding="0" cellspacing="0" class="rpt_table"',"",'valign="top" ');		
		
		
		$objMessageTable->addData('',false,false,' align="center"','class="header"');
		$objMessageTable->addData('',false,false,' align="center"','class="header"');
		$objMessageTable->addData('Inbox',false,false,'colspan="3" align="center"','class="header"');
		$objMessageTable->addData('Outbox',false,false,'colspan="4" align="center"','class="header"');

		
		$objMessageTable->addData($strEntityName,true,false,' align="left" width="1%"','class="lblue"');
		$objMessageTable->addData("Meditab Id",false,false,' align="left" width="44%"','class="lblue"');
		$objMessageTable->addData('Sent',false,false,'align="right" width="7%"','class="lblue"');
		$objMessageTable->addData('Pending',false,false,'align="right" width="7%"','class="lblue"');
		$objMessageTable->addData('Total',false,false,'align="right" width="8%"','class="lblue"');
		$objMessageTable->addData('Sent',false,false,'align="right" width="8%"','class="lblue"');
		$objMessageTable->addData('Pending',false,false,'align="right" width="7%"','class="lblue"');
		$objMessageTable->addData('Error',false,false,'align="right" width="7%"','class="lblue"');
		$objMessageTable->addData('Total',false,false,'align="right" width="9%"','class="lblue"');		
		
		$intRow = 0;
		
		if(count($arrReport) > 0)
		{
			foreach($arrReport as $strReportKey => $arrReportValue)
			{
				$blnForceNewRow = false;
				if($intIndex == 0)
				{
					$blnForceNewRow = true;
				}
				
				
				$strRowClass = ($intRow%2 == 0) ? 'style="background-color:#FFFFFF;"' : 'style="background-color:#F4F6F7;"';
				
				
				if($arrReportValue['INBOX']['Sent'] == "")		$arrReportValue['INBOX']['Sent'] 	= 	0;
				if($arrReportValue['INBOX']['Pending'] == "")	$arrReportValue['INBOX']['Pending']	=	0;
				if($arrReportValue['INBOX']['Total'] == "")		$arrReportValue['INBOX']['Total']	=	0;
				if($arrReportValue['OUTBOX']['Sent'] == "")		$arrReportValue['OUTBOX']['Sent']	=	0;
				if($arrReportValue['OUTBOX']['Pending'] == "")	$arrReportValue['OUTBOX']['Pending']=	0;
				if($arrReportValue['OUTBOX']['Error'] == "")	$arrReportValue['OUTBOX']['Error']	=	0;
				if($arrReportValue['OUTBOX']['Total'] == "")	$arrReportValue['OUTBOX']['Total']	=	0;
				
				
				if($strReportKey == 'NO-MEDITABID')
				{
					$strMeditabLink		=	$strReportKey;
				}
				else if($strReportKey != '')
				{
					$strMOSSiteURL		=	$objPage->objGeneral->getSettings("MOS_SITE_URL");
					$strMeditabLink		=	$objPage->getHrefLink($strMOSSiteURL."index.php?file=med_client_summary&meditab_id=".$strReportKey,$strReportKey,'target="_blank"');
				}
				else
				{
					$strMeditabLink		=	$strReportKey;
				}				
				
				
				$objMessageTable->addData("<img style='cursor:pointer;' id='img_".$strReportKey."' onclick='callDetailsSummary(\"".$strReportKey."\", \"row_".$strReportKey."\")' src='./images/nolines_plus.gif'>",$blnForceNewRow,false,'align="left" ',$strRowClass);
				$objMessageTable->addData($strMeditabLink,false,false,'align="left"');
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
	
				
				$intTotInboxSent		=	$intTotInboxSent + $arrReportValue['INBOX']['Sent'];
				$intTotInboxPending		=	$intTotInboxPending + $arrReportValue['INBOX']['Pending'];
				$intTotInboxTotal		=	$intTotInboxTotal + $strInboxTotal;
				
				$intTotOutboxSent		=	$intTotOutboxSent + $arrReportValue['OUTBOX']['Sent'];
				$intTotOutboxPending	=	$intTotOutboxPending + $arrReportValue['OUTBOX']['Pending'];
				$intTotOutboxError		=	$intTotOutboxError + $arrReportValue['OUTBOX']['Error'];
				$intTotOutboxTotal		=	$intTotOutboxTotal + $strOutboxTotal;
	
				$objMessageTable->addData("&nbsp;",false,false,'align="left" style="display:none;" id="first_col_'.$strReportKey.'"');
				$objMessageTable->addData("",false,true,'colspan="8" style="display:none;" id="row_'.$strReportKey.'" align="left"');				
	
			}
		}
		else
		{
			$objMessageTable->addData("No records Available",false,true,'align="center"');
		}
		
		$objMessageTable->addData('Total',false,false,'colspan="2" align="left" class="bold-text"','style="background-color:#DCE3EB"  ');
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