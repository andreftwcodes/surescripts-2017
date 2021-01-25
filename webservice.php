<?php

include_once 'med_db_config.php';
include_once 'base/PDO.php';



$sTemp = file_get_contents("php://input");


$objPDOFOR = connectDB($ConfigDB['FORMULARY']['Host'], $ConfigDB['FORMULARY']['Database'], $ConfigDB['FORMULARY']['User'], $ConfigDB['FORMULARY']['Password']);

$aTemp	=	@explode("&", $sTemp);

$aRequest = array();
foreach($aTemp as $iKey=>$aValue)
{
	$aTmp	=	@explode("=", $aValue);
	$aRequest[$aTmp[0]] = $aTmp[1];
}

if(!isset($aRequest['client_id']) || strlen(trim($aRequest['client_id'])) != 7)
{
	exit;
}


$sCommand		=	$aRequest['COMMAND'];

if(isset($aRequest['separator']) && $aRequest['separator'] != '')
{
	$sSeparator	=	$aRequest['separator'];
}
else
{
	$sSeparator	=	'#';
}

switch($sCommand)
{
	case 'GET_FORMULARY_INFO': 
				
				header('Content-type: text/xml');
				$iSenderId			=	$aRequest['sender_id'];
				$iProductId			=	$aRequest['product_id'];
				$iCoverageId		=	$aRequest['coverage_id'];
				$iAlternativeId		=	$aRequest['alternative_id'];
				$iCopayId			=	$aRequest['copay_id'];
				$iFormularyId		=	$aRequest['formulary_id'];

				$aTemp = getFormularyInfo($iSenderId, $iProductId, $iCoverageId, $iAlternativeId, $iCopayId, $iFormularyId);

				$sReturn = '<FInfo>';
				foreach($aTemp as $iKey=>$aValue)
				{		
					foreach($aValue as $sKey=>$sValue)
					{
						$sReturn	.=	$sValue;
						$sReturn	.=	$sSeparator;
					}
					$sReturn	=	substr($sReturn, 0, strlen($sReturn)-1);
					
						$sReturn	.=	'\n';
					
				}
				$sReturn .= '</FInfo>';

			break;
	case 'GET_UPDATED_SENDER_ID':
				$sExtractDate	=	date("Y-m-d", strtotime($aRequest['last_update_date']));
				$aTemp = getUpdatedSenderId($sExtractDate);
				$sReturn = '';
				foreach($aTemp as $iKey=>$aValue)
				{		
					foreach($aValue as $sKey=>$sValue)
					{
						$sReturn	.=	$sValue;
						$sReturn	.=	$sSeparator;
					}
					$sReturn	=	substr($sReturn, 0, strlen($sReturn)-1);
					if(isset($aTemp[$iKey+1]))
					{
						$sReturn	.=	'\n';
					}
				}
			break;
	case 'GET_UPDATED_PRODUCT_INFO':
				
			break;
	default:
			break;
}


echo $sReturn;
exit;

function getFormularyInfo($iSenderId, $iProductId, $iCoverageId, $iAlternativeId, $iCopayId, $iFormularyId)
{
	global $objPDOFOR;
	$aReturn = array();
	$sQuery		=	"CALL pr_surescript_formulary_info('".$iSenderId."', '".$iProductId."', '".$iCoverageId."', '".$iAlternativeId."', '".$iCopayId."', '".$iFormularyId."')";

	
    
    $objPDOFOR->run($sQuery);
    $aReturn = $objPDOFOR->select("temp_records");
    
	
    
	return $aReturn;
}
function getUpdatedSenderId($sExtractDate)
{
	global $aReturn, $objPDOFOR;

	$sQuery		=	"SELECT sender_id
						FROM formulary_alternate_header 
						WHERE extract_date >= '".date('Y-m-d', strtotime($sExtractDate))."'
							UNION
						SELECT sender_id
						FROM formulary_copay_header 
						WHERE extract_date >= '".date('Y-m-d', strtotime($sExtractDate))."'
							UNION
						SELECT sender_id
						FROM formulary_coverage_header 
						WHERE extract_date >= '".date('Y-m-d', strtotime($sExtractDate))."'
							UNION
						SELECT sender_id
						FROM formulary_status_header 
						WHERE extract_date >= '".date('Y-m-d', strtotime($sExtractDate))."'";

	return $objPDOFOR->run($sQuery);
}
function getUpdatedProductInfo()
{
	global $aReturn, $objPDOFOR;
	
}
?>
