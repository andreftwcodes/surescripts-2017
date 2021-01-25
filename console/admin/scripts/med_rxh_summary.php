<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Follow up</title>
</head>
<body>
<?PHP


	
	include_once("./base/med_module.php"); 

	
	$objModule 				=	new MedModule();	

	
	$intARCId				=	$objPage->getRequest('arc_id');
	
	
	$strTbl_Name			=	"rxh_ar_summary";
	$strField_Names			=	"*";
	$strWhere				=	"mt_rxh_arc_id = '".$intARCId."'";
	$rsARCData				=	$objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");

	
	$intTotal 				=	count($rsARCData);

	$strTable				=	'<table align="center" width="435px" border="1" style="border-collapse:collapse;border:1px solid #CCCCCC" cellpadding="1" cellspacing="1" >';

	if($blnMultiTaskId)
		$strTaskIdTD		=	'<td align="center" width="8%" height="20"><strong>Task ID</strong></td>';
	else
		$strTaskIdTD		=	'<td align="left" width="3%" height="20"></td>';

	$strTable				.=	'<tr>									
									<td align="center" valign="top" width="20%"><strong>Date</strong></td>
									<td align="right" valign="top" width="20%"><strong>eRx</strong></td>
									<td align="right" valign="top" width="20%"><strong>Fax</strong></td>
									<td align="right" valign="top" width="20%"><strong>Print</strong></td>
								</tr>';
	
	if($intTotal > 0)
	{
		for($intData = 0; $intData < $intTotal; $intData++)
		{
			$strTable		.=	'<tr>
									<td align="center" height="20">'.date("d M y", strtotime($rsARCData[$intData]['ara_rx_date'])).'</td>
									<td align="right" valign="top">'.$rsARCData[$intData]['ara_e_rx_count'].'</td>
									<td align="right" valign="top">'.$rsARCData[$intData]['ara_fax_rx_count'].'</td>
									<td align="right" valign="top">'.$rsARCData[$intData]['ara_print_rx_count'].'</td>
								</tr>';
		}
	}
	else
	{
		$strTable			.=	'<tr>
									<td colspan="7" align="center">No data found..</td>
								</tr>';
	}
	$strTable				.=	'</table>';
	
	
	echo $strTable;
?>
</body>
</html>
<?PHP
exit;
?>