<?php

	
	$objPage->objGeneral->checkAuth($objPage->getRequest('hid_table_id'));
	
	
	include_once('./../base/meditab/med_quicklist.php');
		
	
	include_once("./../base/json/json.php");
		
	
	include_once("./base/med_module.php"); 
		
	
	$objModule = new MedModule();
		
	if($objPage->getRequest("hid_page_type")=='L')	
	{
		
		$strMiddle	= "./middle/med_list_record.htm";		 
	}
	else 
	{	
		
		$strMiddle	= "./middle/med_add_record.htm";			 
	}

	
	$intTableId 	= $objPage->getRequest('hid_table_id');
	$strPageType 	= $objPage->getRequest('hid_page_type');
	$intButtonId 	= $objPage->getRequest('hid_button_id');
	$strFile 		= $objPage->getRequest('file');
	$blnExport		= true;

	
	if($intTableId==15 || $intTableId==32 || $intTableId==106)
	{
		if($intTableId!=106)
			$objPage->setRequest("arrRestrictedValues",array("1",$objPage->objGeneral->getSession('intAdminId')));
		
		if($objPage->objGeneral->getSession('intOffice_id')!=0)
			$objPage->setRequest("strWhere","office_id=".$objPage->objGeneral->getSession('intOffice_id'));
	}	
	
	
	if($intTableId == 105)	
	{
		
		$arrEmpId 			= 	$objPage->getRequest('Sr_Mlslt_employee');
		
		
		$arrSearchControl	=	$objPage->generateSearch($intTableId,$strWhere);
		
		
		if($arrEmpId[0] != 'all')
			if(count($arrEmpId)>0)	
			{
				
				$strEmpId		=	implode("|",$arrEmpId);
				$strCustomWhere = 	"  REPLACE(emp_id,',',' ') REGEXP '[[:<:]](".$strEmpId.")[[:>:]]' ";
				
				
				if(!empty($strWhere))
					$strWhere .= " AND ";
			}
			
		
		$strWhere .= $strCustomWhere;	
		
		
		$objPage->setRequest("strWhere",$strWhere);
	}
	else
		$objPage->setRequest("strWhere","");
		
	$strPage 			= 	$objPage->getHtmlPage($intTableId,$strPageType);
	
	$strModuleName		= 	$objPage->getPageTitleByDb($intTableId);
	
	
	$strMessage 	= $objPage->objGeneral->getMessage();
	
	
	$localValues = array(
							"intButtonId"	=>	$intButtonId,
							"strFile"		=>	$strFile,
							"strPage"		=>	$strPage,
							"intTableId"	=>	$intTableId,
							"strPageType"	=>	$strPageType,
							"strMessage"	=>	$strMessage,
							"strModuleName"	=>	$strModuleName,
							"blnExport"		=>	$blnExport
						);
						
	
	if(count($arrSearchControl) != 0)					
		$localValues	= array_merge($localValues,$arrSearchControl);

	
	function list106_DataLoaded($rsData)
	{	
		global $objPage,$objModule,$objList;
		for($intRsData=0;$intRsData<count($rsData[0]);$intRsData++)
		{
			$strDesc	=	$rsData[0][$intRsData]['policy_text'];
			if(strlen($strDesc) >= 100)
			{
				$rsData[0][$intRsData]['policy_text']	=	substr($strDesc,0,100)."...";
			}
			
			$strImage							=	$objPage->getImage("view","images/view.gif","border='0' alt='Preview' title='Preview'");
			$rsData[0][$intRsData]['preview']	=	$objPage->getHrefLink("index.php?file=med_policy_view&policy_id=".$rsData[0][$intRsData]["policy_id"],$strImage,"target='_blank'");
			
			
			if(strtoupper(trim($rsData[0][$intRsData]["status"]))=='INACTIVE')
				$objList->setRowClass($intRsData,"datared");			
		}
	}
	
	
	function list105_DataLoaded($rsData)
	{
		global $objList;
				
		for($intData=0;$intData<count($rsData[0]);$intData++)
		{
			
			if(strtoupper(trim($rsData[0][$intData]["status"]))=='INACTIVE')
				$objList->setRowClass($intData,"datared");
		}
	}	
	
	
	function list42_DataLoaded($rsData)
	{
		global $objList,$objPage;

		for($intData=0;$intData<count($rsData[0]);$intData++)
		{
			
			if(strtoupper(trim($rsData[0][$intData]["status"]))=='INACTIVE')
				$objList->setRowClass($intData,"datared");
				
				
			
			$arrPopup			=	array();
			
			
			$arrPopup[]			=	array("strRowTitle"=>"","arrColumns"=>array($rsData[0][$intData]['long_desc']));
			
			
			$arrWidth[]			=	array("strRowTitle"=>'0',"arrColumns"=>array('100%'));
			
			
			$strAdditionalDesc	=	$objPage->generatePopupDataTable($arrPopup,$arrWidth,true);
			
			
			unset($arrPopup);
			
			
			$strProperty	=	$objPage->openMouseImagePopup("",$strAdditionalDesc,"Description");
			
			$rsData[0][$intData]['project_name']	=	$objPage->getHrefLink("index.php?file=med_list_record&amp;hid_table_id=42&amp;hid_page_type=E&amp;project_id=".$rsData[0][$intData]['project_id'],$rsData[0][$intData]['project_name'],$strProperty);
		}
	}		
	
	
	function list110_DataLoaded($rsData)
	{
		global $objList;
		
		for($intData=0;$intData<count($rsData[0]);$intData++)
		{
			
			if(strtoupper(trim($rsData[0][$intData]["status"]))=='INACTIVE')
				$objList->setRowClass($intData,"datared");
		}
	}		
	
	
	function list89_DataLoaded($rsData)
	{
		global $objList;
		
		for($intData=0;$intData<count($rsData[0]);$intData++)
		{
			
			if(strtoupper(trim($rsData[0][$intData]["status"]))=='INACTIVE')
				$objList->setRowClass($intData,"datared");
		}
	}
	
	
	function list15_DataLoaded($rsData)
	{
		
		global $objList;
		for($intRsData=0;$intRsData<count($rsData[0]);$intRsData++)
		{
			
			if(strtoupper(trim($rsData[0][$intRsData]["status"]))=='INACTIVE')
				$objList->setRowClass($intRsData,"datared");
		}
	}		
?>
