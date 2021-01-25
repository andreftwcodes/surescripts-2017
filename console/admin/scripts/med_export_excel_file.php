<?php


	$strFileName	= 	"";
	$strContent		= 	"";

	if(empty($intMultiTableId) || $intMultiTableId=="")
		$intMultiTableId = "";		

	if(!empty($intTableId) && $intTableId!= "")
	{
		$rsFields		= getHeaderContent($intTableId,$intMultiTableId); 	
			 
		$arrListingData	= getListingRecordSet($rsFields);					
		$strFileName	= $rsFields[0]["page_title"];						
	}

	
	if(!empty($intTableId1) && $intTableId1!= "")
	{
		$rsFields1		= getHeaderContent($intTableId1,$intMultiTableId);
		$objPage->setRequest("strWhere",$strWhere1);
		$arrListingData1= getListingRecordSet($rsFields1);
		$strFileName	= $rsFields1[0]["page_title"];
	}
	if(!empty($intTableId2) && $intTableId2!= "")
	{
		$rsFields2		= getHeaderContent($intTableId2,$intMultiTableId);
		$objPage->setRequest("strWhere",$strWhere2);
		$arrListingData2= getListingRecordSet($rsFields2);
		$strFileName	= $rsFields2[0]["page_title"];
	}
	
	
	$strFileName	= str_replace('-','',$strFileName);
	$strFileName	= str_replace('/','',$strFileName);
	$strFileName	= str_replace(" ","-",$strFileName);
	$strFileName	= strtoupper($objPage->pre).$strFileName.date('Y-m-d').".xls";

	
	
	if($intTableId == 60)
	{
		$strContent		.= "\tHalf Leave Detail\n"; 	
		$strContent		= generateHeaderInFile($strContent,$rsFields);	
		$strContent		= generateContentInFile($strContent,$arrListingData);	
		
		$strLeaveWhere	=	$strWhere." and mem_inout_master.is_leave = 'Y'";
		$objPage->setRequest("strWhere",$strLeaveWhere);
		$arrListingData	= getListingRecordSet($rsFields); 

		$strContent		.= "\n\nFull Leave Detail\n"; 
		$strContent		= generateHeaderInFile($strContent,$rsFields);	
		$strContent		= generateContentInFile($strContent,$arrListingData); 
		
	}	
	elseif(!empty($intTableId) && $intTableId!="" && $intTableId != 60 && empty($intTableId1) && empty($intTableId2))
	{
		$strContent		= generateHeaderInFile($strContent,$rsFields);	
		$strContent		= generateContentInFile($strContent,$arrListingData);
	}
	
	if(!empty($intTableId1) && $intTableId1!= "")
	{
		$strContent		.= "\n\tLate Comers List\n"; 	
		$strContent		= generateHeaderInFile($strContent,$rsFields1);	
		$strContent		= generateContentInFile($strContent,$arrListingData1);	
	}	
	if(!empty($intTableId2) && $intTableId2!= "")
	{
		$strContent		.= "\n\n\tLess Work List\n"; 	
		$strContent		= generateHeaderInFile($strContent,$rsFields2);	
		$strContent		= generateContentInFile($strContent,$arrListingData2);	
	}

	
	
	header("Content-Type: application/vnd.ms-excel");
	header("Content-Disposition: attachment; filename=".$strFileName);
	echo $strContent;
	unset($strContent);
	unset($handle);
	exit;
		
	
	function getHeaderContent($intTableId,$intMultiTableId="")
	{
		global $objPage;
		
		include_once("./base/med_module.php"); 
			
		
		$objModule = new MedModule();
		if(!empty($intMultiTableId) && $intMultiTableId!="")
			$objModule->generateListFields($intMultiTableId); 

		$rsFields		= $objPage->getListHeader($intTableId,L);	 
		return $rsFields;
	}

	
	function getListingRecordSet($rsFields)
	{
		global $objPage;
		$arrListingData	= $objPage->getListingSqlQuery($rsFields);
		return $arrListingData;
	}
	
	
	function generateHeaderInFile($strContent,$rsFields)
	{
		
		for($intHeaderId=0;$intHeaderId<count($rsFields);$intHeaderId++)
		{
			 if($rsFields[$intHeaderId]["ishidden"] != 1 && $rsFields[$intHeaderId]["show_in"]!="" && $rsFields[$intHeaderId]["field_type"]!="")
				$strContent	.= $rsFields[$intHeaderId]["field_title"]."\t";
		}
		$strContent	= substr($strContent,0,-1);
		$strContent	.= "\n"; 
		return 	$strContent;
	}
	
	
	function generateContentInFile($strContent,$arrListingInfo)
	{
		global $objPage;
		

		$arrListingData			=	$arrListingInfo["arrListRecord"];
		$arrListFieldHtmlType	= 	explode(",",$arrListingInfo["strListFieldHtmlType"]);
		$arrListHtmlText		= 	explode(",",$arrListingInfo["strListHtmlText"]);
		for($intDataId=0;$intDataId<count($arrListingData);$intDataId++)
		{		
			$intTempId=0;
			foreach($arrListingData[$intDataId] as $strKey=>$strValue)
			{
				$strVal			=	$arrListFieldHtmlType[$intTempId];
				if(!empty($strVal))
				{
					if(strtoupper(trim($strVal)) == "CONDITION")
					{
						if(trim($arrListHtmlText[$intTempId])	!=	"STATUS_IMAGE")
							$arrListingData[$intDataId][$strKey] = $objPage->generateConditionalItem(trim($arrListHtmlText[$intTempId]),$arrListingData[$intDataId][$strKey]);
					}
					$strContent		.=	str_replace("\r\n","",$arrListingData[$intDataId][$strKey])."\t";	
				}
				$intTempId++;
			}			
			$strContent 	.= "\n";
		}
		$strContent			= substr($strContent,0,-1);
		return $strContent;
	}


?>