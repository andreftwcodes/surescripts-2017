<?php



	
	$objPage->objGeneral->checkAuth(13);
	
	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	$objData = new MedData();
	
	
	$strMiddle		= "./middle/med_list_record.htm";		 
	
	
	$intModuleId	= $objPage->getRequest("hidin_module_id");
	$strAction		= $objPage->getRequest("hid_page_type");
	$arrValue		= $objPage->getRequest("txt_msg_value");
	$arrMsgCode		= $objPage->getRequest("hid_msg_value");
	$intTableId		= $objPage->getRequest("hid_table_id");
	$strButtonId	= $objPage->getRequest('hid_button_id');	
	$strMsgCode		= strtoupper(trim($objPage->getRequest('TaRtxt_msg_code')));	

	$rsKeycol 		= $objPage->getTableKeyCol($intTableId);
	$objPage->setTableProperty(&$objData);
	if(!empty($strAction))
	{
		if($strAction	==	"A")
		{
			$objPage->setRequest("msg_code",$strMsgCode);
			$objData->performAction("A",NULL);		
		}
		elseif($strAction	==	"U")
		{	
			
			for($intValue=0; $intValue<count($arrValue); $intValue++)
			{
				$whereCondition 	= "msg_code = '".$arrMsgCode[$intValue]."'";
				$fieldValueArray 	= array("msg_value" => $arrValue[$intValue]);
				$resultObj 			= $objData->updateRows($fieldValueArray, $whereCondition);
			}

			
			$objPage->objGeneral->setMessage($objPage->objGeneral->getSiteMessage('REC_MOD_MSG'));
		}	
		elseif($strAction	==	"D")
		{
			$strDelMsgCode	= @implode("','",$objPage->getRequest('list13_cSlcPK'));	
			$strWhere	= " msg_code in ('".$strDelMsgCode."')";
			MedPage::setRequest("strWhere",$strWhere);
			$objData->performAction("D",$strWhere);		
		}
	
		header("location:index.php?file=med_list_record&hid_table_id=".$intTableId."&hid_page_type=L&hidin_module_id=0");
		exit;
	}
	else
	{	
		$objPage->objGeneral->raiseError("WARNING","No action define","Action Script","Do not call action script without action parameters"); 
	}

?>

