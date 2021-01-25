<?php



include_once('med_config.php');


include_once(WEB_ROOT.'base/MedXmlParser.php');


include_once(WEB_ROOT.'base/DB2.php');


include_once(WEB_ROOT.'base/MedCommon.php');


include_once(WEB_ROOT.'base/MedArchiveEpcs2.php');

set_time_limit(0);

global $intCounter;

$intCounter=0;

echo "Script Execution Initiated at: ".date('Y-m-d H:i:s').PHP_EOL;

processPendingScripts();

echo "Script Execution Completed at: ".date('Y-m-d H:i:s').PHP_EOL;

echo "Total Processed :".$intCounter;

exit;


function processPendingScripts()
{
    global $medDev,$medDB,$intCounter;
    
    echo "Fetching Scripts".PHP_EOL;
    
    $strSql     =   "SELECT i.tran_id,i.message_id,i.edi_message
                    FROM in_message_transaction AS i
                    WHERE DATE(i.received_time)>='2016-11-20' AND DATE(i.received_time)<='2017-01-10' AND is_processed='N' AND (i.epcs_signed_text IS NULL OR i.epcs_signed_text='')
                    LIMIT 500";

    $arrData    =   $medDev->GetAll($strSql);
    
    if(count($arrData)>0)
    {
        foreach ($arrData as $rsMessage)
        {
            echo "Processing ".$rsMessage['message_id'].PHP_EOL;
            
            $arrResult  =   signPrescription($rsMessage);
            
            if($arrResult['is_epcs'] == 'Y')
            {
                if($arrResult['result'] == '1')
                {
                    updateScript(array('is_epcs'=>'Y','epcs_signed_text'=>$arrResult['epcs_signed_text'],'epcs_signed_text'=>$arrResult['epcs_message_digest'],'epcs_message_digest'=>$arrResult['epcs_plain_text'],'tran_id'=>$rsMessage['tran_id']));
                    saveSigningLog(array('fk_tran_id'=>$rsMessage['tran_id'],'message_id'=>$rsMessage['message_id'],'is_error'=>'N'));
                }
                else
                {
                    saveSigningLog(array('fk_tran_id'=>$rsMessage['tran_id'],'message_id'=>$rsMessage['message_id'],'is_error'=>'Y'));
                }
                
                markAsProcessed(array('is_processed'=>'Y','tran_id'=>$rsMessage['tran_id']));
                
                
            }
            else
            {
                updateScript(array('is_epcs'=>'N','tran_id'=>$rsMessage['tran_id']));
                markAsProcessed(array('is_processed'=>'Y','tran_id'=>$rsMessage['tran_id']));
            }
            
            $intCounter++;
			
			echo 'Processed : '.$intCounter.PHP_EOL;
        }
        
        sleep(30);
        
        processPendingScripts();
    }
}


function saveSigningLog($arrLog)
{
    global $medDev,$medDB;
    return $medDB->AutoExecute('meditab_server_106.signing_log',$arrLog,'INSERT');
}


function markAsProcessed($arrUpdate)
{
    global $medDev,$medDB;
    return $medDev->AutoExecute('in_message_transaction',$arrUpdate,'UPDATE',"tran_id='".$arrUpdate['tran_id']."'");
}


function updateScript($arrScript)
{
    global $medDev,$medDB;
    return $medDB->AutoExecute('in_message_transaction',$arrScript,'UPDATE',"tran_id='".$arrScript['tran_id']."'");
}


function signPrescription($arrPrescription)
{
    $objXml = new MedXmlParser(base64_decode($arrPrescription['edi_message']));
    
    $strMessageType = $objXml->getBodyFirstElement();
    
    $strEpcsSignedText		=	"";
    $strEpcsMessageDigest	=	"";
    $strEpcsPlainText		=	"";
    
    $blnFillableMessage		=	false;
    
    if($strMessageType	== 'NEWRX')
    {
        $blnFillableMessage     =	true;
    }
    elseif ($strMessageType == 'REFRES')
    {
        $strRefillResponseStatus	=	$objXml->getRefillResponseStatus();

        if($strRefillResponseStatus == 'APPROVED' || $strRefillResponseStatus == 'APPROVEDWITHCHANGES')
        {
                $blnFillableMessage =	true;
        }
    }
    
    
    $blnControlledSubstance		=	false;
    if(is_array($objXml->DEASchedule) || $objXml->DEASchedule->Value	!=	"")
    {
        $blnControlledSubstance     =	true;
    }
    
    if($blnControlledSubstance === true && $blnFillableMessage === true)
    {
        
        $blnSiFlagPresent	= false;
        if(is_array($objXml->DrugCoverageStatusCode))
        {
            foreach($objXml->DrugCoverageStatusCode as $objDrugCoverageStatusCode)
            {
                    if($objDrugCoverageStatusCode->Value == "SI")
                    {
                            $blnSiFlagPresent	= true;
                    }
            }
        }
        else
        {
            if($objXml->DrugCoverageStatusCode->Value == "SI")
            {
                    $blnSiFlagPresent	=	true;
            }
        }

        
        if($blnSiFlagPresent == false)
        {
                $arrResponse    =   array('is_epcs'=>'N');
        }
        else
        {
                
                $objArchiveEpcs		=	new MedArchiveEpcs(base64_decode($arrPrescription['edi_message']),EPCS_WEB_SERVICE_URL);
                $intEpcsArchiveResult	=	$objArchiveEpcs->archivePrescription();

                if($intEpcsArchiveResult == 1)
                {
                        $blnEpcsArchived	=	true;

                        $strEpcsSignedText	=	$objArchiveEpcs->getSignedText();
                        $strEpcsMessageDigest	=	$objArchiveEpcs->getMessageDigest();
                        $strEpcsPlainText	=	$objArchiveEpcs->getPlainText();
                        
                        $arrResponse    =   array('is_epcs'=>'Y','epcs_signed_text'=>$strEpcsSignedText,'epcs_message_digest'=>$strEpcsMessageDigest,'epcs_plain_text'=>$strEpcsPlainText,'result'=>'1');
                }
                else
                {
                       $arrResponse    =   array('is_epcs'=>'Y','result'=>'-1');
                }
        }
    }
    else
    {
        $arrResponse    =   array('is_epcs'=>'N');
    }
    
    return $arrResponse;
}
?>
