<?php


	
	$objPage->objGeneral->checkSession(1);
	
	
	include_once("./../base/meditab/med_quicklist.php");
	
	
	
	$objListData	=	new MedDataList(); 
	
	$strMiddle		=	"./middle/med_page_listings.htm";		 
	
	$intTableId		=	"-1542";
	$strFile		=	$objPage->getRequest('file');												
	$strButtonId	=	$objPage->getRequest('hid_button_id');									
	$intTableId		=	$objPage->getRequest('hid_table_id');									
	$strPageType	=	$objPage->getRequest('hid_page_type');
	$intModuleId	=	$objPage->getRequest('hidin_module_id');	
	$intShowRows 	= 	$objPage->getRequest('Sr_Intxt_show_rows');
	$strSearch 		= 	$objPage->getRequest('Tatxt_search');
	
	
	$intShowRows	=	$objPage->setAutoRequest('Sr_Intxt_show_rows',$intTableId,$intShowRows);
	$strSearch		=	$objPage->setAutoRequest('Tatxt_search',$intTableId,$strSearch);
	
	
	$strFields		=	"page_title,table_id,module_id,field_referal";
	$strWhere		=	"module_id =".$intModuleId."";  
	
	if($strSearch != "")
	{
		
		$arrSearchField		=	array('page_title','field_referal');
		$strWhere			=	"(".$objPage->getKeywordSearchQuery($arrSearchField,$strSearch).") or table_id = '".$strSearch."'";	
	}		
	$objListData->setProperty("tbl_table","table_id",$strFields,$strWhere,"","");

	
	$objList		=	new MedQuickList("search");
	
	
	$objList->setShowSelector(true);
		
	
	$objList->setShowPaging(true);
	
	
	
	$objList->setSelectionLimit(0);

	$intRecordsPerPage = $objPage->objGeneral->getSettings("GEN_REC_PER_PAGE");
	$intPagesPerGroup = $objPage->objGeneral->getSettings("GEN_PAGE_PER_GROUP");
	
	if($intShowRows	==	"")		$intShowRows = $intRecordsPerPage;
	
	
	$objList->setRecordsPerPage($intShowRows);
	
	
	$objList->setPagesPerGroup($intShowRows);
	
	
	$objList->setCssClass("qlist");

	$objList->addButton("Add Table",":A::med_page_add",false,NULL,NULL,"left","top");
	$objList->addButton("Delete",":D::med_page_add_action",true,"Are you sure you want to delete?",NULL,"left","top");
	$objList->addButton("Copy",":C::med_page_add_action",true,"Are you sure you want to copy?",NULL,"left","top");
	$objList->addButton("Copy Table Data",":CD::med_page_copy_data",false,NULL,NULL,"left","top");
	
	
	$objList->addLinkItem("Table Referal", array("field_referal","table_id","module_id"), "{0}", "index.php?file=med_page_add&hid_table_id={1}&table_id={1}&parent_id=29&hid_page_type=E&hidin_module_id={2}",null,null,"left",true);
	$objList->addLinkItem("Page Title", array("page_title","table_id","module_id"), "{0}", "index.php?file=med_page_add&hid_table_id={1}&table_id={1}&parent_id=29&hid_page_type=E&hidin_module_id={2}",null,null,"left",true);
	$objList->addEvaluatedExprItem("View Multi", array("table_id","module_id"), "<a href='index.php?file=med_page_multi_list&hid_page_type=L&hid_table_id={0}&parent_id=29&hidin_module_id={1}'>View Multi</a>");
	$objList->addEvaluatedExprItem("Field Action", "table_id", "<a href='index.php?file=med_page_fields_listings&hid_page_type=L&hid_table_id={0}&parent_id=29&hidin_module_id=0'>View Fields</a>&nbsp;|&nbsp;<a href='index.php?file=med_page_fields_add&hid_page_type=A&hid_table_id={0}&hidin_module_id=0'>Add Field</a>");
	$objList->addEvaluatedExprItem("Button Action", "table_id", "<a href='index.php?file=med_page_buttons_listings&hid_page_type=L&hid_table_id={0}&parent_id=29&hidin_module_id=0'>View Button</a>&nbsp;|&nbsp;<a href='index.php?file=med_page_buttons_add&hid_page_type=A&hid_table_id={0}&hidin_module_id=0'>Add Button</a>");
	$objList->addEvaluatedExprItem("Search Action", array("table_id","module_id"), "<a href='index.php?file=med_page_search_listings&hid_page_type=L&hid_table_id={0}&parent_id=29&hidin_module_id=0'>View Search</a>&nbsp;|&nbsp;<a href='index.php?file=med_page_search_add&hid_page_type=A&hid_table_id={0}&hidin_module_id=0'>Add Search</a>");
	
	$objList->setAlphaSearchField("page_title");
	

	
	$strSearch 		= 	$objPage->getTextBox("Ta","search","","class='comn-input'",$strSearch,0,"",false);
	$strShowRows 	= 	$objPage->getTextBox("Sr_In","show_rows","","class='comn-input' size='3' maxlength='3'",$intShowRows,0,"",false);
	
	$strPage = $objList->show($objListData);

	
	$strModuleName = "Page Settings";														

	
	$strMessage = $objPage->objGeneral->getMessage();
	
	
	$localValues = array("strMessage"=>$strMessage,"strPage"=>$strPage,"intModuleId"=>$intModuleId,"strTblAdd"=>$strTblAdd,"strPageType"=>$strPageType,"strFile"=>$strFile,"strModuleName"=>$strModuleName,
						 "strSearch"=>$strSearch,"strShowRows"=>$strShowRows,"intShowRows"=>$intShowRows);

?>
