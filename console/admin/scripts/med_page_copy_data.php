<?php

	
	include_once("./../base/meditab/med_quicklist.php");

	
	$strMiddle="./middle/med_page_copy_data.htm";		 
	
	include_once("./base/med_module.php"); 
	$objModule=new MedModule();		

	$strSearch = $objPage->getRequest('searchText');
	$strSubmit = $objPage->getRequest('smt_submit');
	if($strSearch)
	{
		$strSearchQuery = " where (";
		$arrSearch = explode(",",$strSearch);
		if(count($arrSearch) > 0)
		{	
			for($intSearch=0;$intSearch<count($arrSearch);$intSearch++)
			$strSearchQuery .= "(table_id = '".strtolower($arrSearch[$intSearch])."' or page_title like '%".strtolower($arrSearch[$intSearch])."%') or ";	
		}
		$strSearchQuery = substr($strSearchQuery,0,strlen($strSearchQuery)-3);
		$strSearchQuery .= ")";
		$objPage->setRequest('searchText',$strSearchQuery);
	}
	
	if($strSubmit)
	{
		$rsTableId = $objPage->getRequest('AddTbl');
		$rsPageId = $objPage->getRequest('PageTbl');
		$strQuery = getQueryData($rsTableId,$rsPageId);
	}	
	
	$strSelectedValue = @implode(",",$objPage->getRequest('AddTbl'));
	$strAddTbl = $objPage->generateCombobox("SYSTABLE_NAME_ID","AddTbl",$strSelectedValue,'class=comn-input style=width:200px',true);
	
	
	$strSelectedPageValue = @implode(",",$objPage->getRequest('PageTbl'));
	$strPageTbl = $objPage->generateCombobox("PAGE_SETTING_TABLE","PageTbl",$strSelectedPageValue,'class=comn-input style=width:200px',true);
	
	
	$strModuleName .= "Copy Page Settings Data";

	
	$strMessage = $objPage->objGeneral->getMessage();
	
	
	$localValues = array("strAddTbl"=>$strAddTbl,"strModuleName"=>$strModuleName,"strSearch"=>$strSearch,"strQuery"=>$strQuery,"strPageTbl"=>$strPageTbl);
	
	function getQueryData($rsTableId,$rsPageId)
	{
		global $objPage,$objModule;
		for($intPageId=0;$intPageId<count($rsPageId);$intPageId++)
		{
			if($rsPageId[$intPageId]	== 'table')  $setBlnTable  = 1; 
			if($rsPageId[$intPageId] 	== 'field')  $setBlnField  = 2;
			if($rsPageId[$intPageId]  	== 'multi')  $setBlnMulti  = 3;
			if($rsPageId[$intPageId]  	== 'button') $setBlnButton = 4;
			if($rsPageId[$intPageId]  	== 'search') $setBlnSearch = 5;
		}		
		
		for($intTableId=0;$intTableId<count($rsTableId);$intTableId++)
		{
			
			$strQuery .= "-- ".$objPage->getPageTitleByDb($rsTableId[$intTableId]);
			$strQuery .= "\r\n";
			$strQuery .= "\r\n";
			
			if(!empty($setBlnTable))
			{
				
				$rsTable  = $objModule->getPageTableQuery($rsTableId[$intTableId]);
				
				for($intTable=0;$intTable<count($rsTable);$intTable++)
				{
					$strQuery .= "DELETE FROM `tbl_table` where table_Id ='".$rsTable[$intTable]['table_id']."';";
					$strQuery .= "\r\n";
				}
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
				$strQuery .= "INSERT INTO `tbl_table` (`table_id`, `field_referal`, `list_table_name`, `addedit_table_name`, `module_id`, `page_title`, `where_clause`, `order_clause`, `group_clause`, `having_clause`, `table_type`, `delete_key_col`, `add_key_col`, `edit_key_col`, `addedit_action_link`, `fixed_title`, `issearch`, `table_desc`, `table_query`, `list_message`, `iscolumnheading`, `listcolumn`) 
									  VALUES ";

				for($intTable=0;$intTable<count($rsTable);$intTable++)
				{
					
					$rsTable[$intTable]['field_referal']		=	str_replace("'","''",$rsTable[$intTable]['field_referal']);
					$rsTable[$intTable]['list_table_name']		=	str_replace("'","''",$rsTable[$intTable]['list_table_name']);
					$rsTable[$intTable]['addedit_table_name']	=	str_replace("'","''",$rsTable[$intTable]['addedit_table_name']);
					$rsTable[$intTable]['page_title']			=	str_replace("'","''",$rsTable[$intTable]['page_title']);
					$rsTable[$intTable]['where_clause']			=	str_replace("'","''",$rsTable[$intTable]['where_clause']);
					$rsTable[$intTable]['order_clause']			=	str_replace("'","''",$rsTable[$intTable]['order_clause']);					
					$rsTable[$intTable]['group_clause']			=	str_replace("'","''",$rsTable[$intTable]['group_clause']);																									
					$rsTable[$intTable]['having_clause']		=	str_replace("'","''",$rsTable[$intTable]['having_clause']);
					$rsTable[$intTable]['where_clause']			=	str_replace("'","''",$rsTable[$intTable]['where_clause']);					
					$rsTable[$intTable]['table_desc']			=	str_replace("'","''",$rsTable[$intTable]['table_desc']);
					$rsTable[$intTable]['table_query']			=	str_replace("'","''",$rsTable[$intTable]['table_query']);
					$rsTable[$intTable]['list_message']			=	str_replace("'","''",$rsTable[$intTable]['list_message']);
					$rsTable[$intTable]['delete_key_col']		=	str_replace("'","''",$rsTable[$intTable]['delete_key_col']);
					$rsTable[$intTable]['edit_key_col']			=	str_replace("'","''",$rsTable[$intTable]['edit_key_col']);
					$rsTable[$intTable]['addedit_action_link']	=	str_replace("'","''",$rsTable[$intTable]['addedit_action_link']);										
					$rsTable[$intTable]['fixed_title']			=	str_replace("'","''",$rsTable[$intTable]['fixed_title']);					
					$rsTable[$intTable]['database_object']		=	str_replace("'","''",$rsTable[$intTable]['database_object']);										

					
					if($intTable)
						$strQuery .= ",";

					$strQuery .= "('".$rsTable[$intTable]['table_id']."','".$rsTable[$intTable]['field_referal']."','".$rsTable[$intTable]['list_table_name']."', '".$rsTable[$intTable]['addedit_table_name']."','".$rsTable[$intTable]['module_id']."', '".$rsTable[$intTable]['page_title']."', '".$rsTable[$intTable]['where_clause']."', '".$rsTable[$intTable]['order_clause']."', '".$rsTable[$intTable]['group_clause']."', '".$rsTable[$intTable]['having_clause']."', '".$rsTable[$intTable]['table_type']."', '".$rsTable[$intTable]['delete_key_col']."', '".$rsTable[$intTable]['add_key_col']."', '".$rsTable[$intTable]['edit_key_col']."', '".$rsTable[$intTable]['addedit_action_link']."', '".$rsTable[$intTable]['fixed_title']."', '".$rsTable[$intTable]['issearch']."', '".$rsTable[$intTable]['table_desc']."', '".$rsTable[$intTable]['table_query']."', '".$rsTable[$intTable]['list_message']."', '".$rsTable[$intTable]['iscolumnheading']."','".$rsTable[$intTable]['listcolumn']."')";
					$strQuery .= "\r\n";
				}

				$strQuery	.=	";";
				$strQuery .= "\r\n-- COMPLITED TBL_TABLES QUERY";
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
			}
			$strQuery .= "\r\n";

			if(!empty($setBlnField))
			{			
				
				$rsFields = $objModule->getPageFieldQuery($rsTableId[$intTableId]);

				
				for($intField=0;$intField<count($rsFields);$intField++)
				{
					$strQuery .= "DELETE FROM `tbl_fields` where id ='".$rsFields[$intField]['id']."';";
					$strQuery .= "\r\n";
				}
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
				if(count($rsFields))
					$strQuery .= "INSERT INTO `tbl_fields` (`id`, `table_id`, `field_name`, `field_type`, `field_length`, `add_field_length_show`, `field_title`,`show_in`, `header_width`, `header_align`, `seq_no`, `body_align`, `issort`, `add_field_type`, `ishidden`,`html_link`, `list_field_html_type`, `addedit_field_html_type`, `list_html_text`, `add_html_text`, `isrequired`,`list_event`, `addedit_event`, `list_extra_property`, `add_extra_property`, `sql_field`, `field_desc`, `field_referal`)
									VALUES ";
				for($intField=0;$intField<count($rsFields);$intField++)
				{
					

					if($intField)
						$strQuery .= ",";
						
					$rsFields[$intField]['field_referal']			=	str_replace("'","''",$rsFields[$intField]['field_referal']);
					$rsFields[$intField]['field_name']				=	str_replace("'","''",$rsFields[$intField]['field_title']);
					$rsFields[$intField]['field_title']				=	str_replace("'","''",$rsFields[$intField]['field_title']);
					$rsFields[$intField]['field_desc']				=	str_replace("'","''",$rsFields[$intField]['field_desc']);
					$rsFields[$intField]['html_link']				=	str_replace("'","''",$rsFields[$intField]['html_link']);					
					$rsFields[$intField]['list_html_text']			=	str_replace("'","''",$rsFields[$intField]['list_html_text']);					
					$rsFields[$intField]['add_html_text']			=	str_replace("'","''",$rsFields[$intField]['add_html_text']);
					$rsFields[$intField]['list_event']				=	str_replace("'","''",$rsFields[$intField]['list_event']);
					$rsFields[$intField]['addedit_event']			=	str_replace("'","''",$rsFields[$intField]['addedit_event']);
					$rsFields[$intField]['list_extra_property']		=	str_replace("'","''",$rsFields[$intField]['list_extra_property']);
					$rsFields[$intField]['add_extra_property']		=	str_replace("'","''",$rsFields[$intField]['add_extra_property']);
					$rsFields[$intField]['list_field_html_type']	=	str_replace("'","''",$rsFields[$intField]['list_field_html_type']);
					$rsFields[$intField]['add_field_type']			=	str_replace("'","''",$rsFields[$intField]['add_field_type']);					
					$rsFields[$intField]['body_align']				=	str_replace("'","''",$rsFields[$intField]['body_align']);					
					$rsFields[$intField]['header_align']			=	str_replace("'","''",$rsFields[$intField]['header_align']);										
					$rsFields[$intField]['field_type']				=	str_replace("'","''",$rsFields[$intField]['field_type']);										
					$rsFields[$intField]['addedit_field_html_type']	=	str_replace("'","''",$rsFields[$intField]['addedit_field_html_type']);										
					$rsFields[$intField]['sql_field']				=	str_replace("'","''",$rsFields[$intField]['sql_field']);										
					
					
	
					$strQuery .= "('".$rsFields[$intField]['id']."','".$rsFields[$intField]['table_id']."','".$rsFields[$intField]['field_name']."', '".$rsFields[$intField]['field_type']."','".$rsFields[$intField]['field_length']."', '".$rsFields[$intField]['add_field_length_show']."','".$rsFields[$intField]['field_title']."', '".$rsFields[$intField]['show_in']."','".$rsFields[$intField]['header_width']."', '".$rsFields[$intField]['header_align']."','".$rsFields[$intField]['seq_no']."', '".$rsFields[$intField]['body_align']."',								    '".$rsFields[$intField]['issort']."', '".$rsFields[$intField]['add_field_type']."','".$rsFields[$intField]['ishidden']."', '".$rsFields[$intField]['html_link']."','".$rsFields[$intField]['list_field_html_type']."', '".$rsFields[$intField]['addedit_field_html_type']."','".$rsFields[$intField]['list_html_text']."', '".$rsFields[$intField]['add_html_text']."','".$rsFields[$intField]['isrequired']."','".$rsFields[$intField]['list_event']."','".$rsFields[$intField]['addedit_event']."','".$rsFields[$intField]['list_extra_property']."','".$rsFields[$intField]['add_extra_property']."','".$rsFields[$intField]['sql_field']."','".$rsFields[$intField]['field_desc']."','".$rsFields[$intField]['field_referal']."')";
					$strQuery .= "\r\n";
				}
				if(count($rsFields))
					$strQuery	.=	";";
				$strQuery .= "\r\n-- COMPLITED TBL_FIELDS QUERY";
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
			}	
			$strQuery .= "\r\n";
			if(!empty($setBlnMulti))
			{		
				
				$rsMulti  = $objModule->getPageMultiQuery($rsTableId[$intTableId]);
				
				
				for($intMulti=0;$intMulti<count($rsMulti);$intMulti++)
				{
					$strQuery .= "DELETE FROM `tbl_table_multi` where table_multi_id ='".$rsMulti[$intMulti]['table_multi_id']."'; \n";
					$strQuery .= "\r\n";
				}
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
				if(count($rsMulti))
					$strQuery .= "INSERT INTO `tbl_table_multi` (`table_multi_id`, `table_id`, `page_title`, `issearch`, `isalpha`, `ispaging`, `isselector`, `table_desc`, `field_id`, `button_id`, `issort`, `iscolumnheading`, `listcolumn`) 
								  VALUES ";

				for($intMulti=0;$intMulti<count($rsMulti);$intMulti++)
				{
					
					if($intMulti)
						$strQuery .= ",";

					$rsMulti[$intMulti]['page_title']			=	str_replace("'","''",$rsMulti[$intMulti]['page_title']);						
					$rsMulti[$intMulti]['field_id']				=	str_replace("'","''",$rsMulti[$intMulti]['field_id']);						
					$rsMulti[$intMulti]['button_id']			=	str_replace("'","''",$rsMulti[$intMulti]['button_id']);						
					$rsMulti[$intMulti]['iscolumnheading']		=	str_replace("'","''",$rsMulti[$intMulti]['iscolumnheading']);						
																				
					$strQuery .= "('".$rsMulti[$intMulti]['table_multi_id']."','".$rsMulti[$intMulti]['table_id']."','".$rsMulti[$intMulti]['page_title']."', '".$rsMulti[$intMulti]['issearch']."','".$rsMulti[$intMulti]['isalpha']."', '".$rsMulti[$intMulti]['ispaging']."', '".$rsMulti[$intMulti]['isselector']."', '".$rsMulti[$intMulti]['table_desc']."', '".$rsMulti[$intMulti]['field_id']."', '".$rsMulti[$intMulti]['button_id']."', '".$rsMulti[$intMulti]['issort']."', '".$rsMulti[$intMulti]['iscolumnheading']."', '".$rsMulti[$intMulti]['listcolumn']."')";
							
					$strQuery .= "\r\n";
				}
				if(count($rsMulti))
					$strQuery	.=	";";
				$strQuery .= "\r\n-- COMPLITED TBL_FIELDS_MULTI QUERY";
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
			
			}
			$strQuery .= "\r\n";
			if(!empty($setBlnButton))
			{			
				
				$rsButtons = $objModule->getPageButtonQuery($rsTableId[$intTableId]);
				
				
				for($intButton=0;$intButton<count($rsButtons);$intButton++)
				{
					$strQuery .= "DELETE FROM `tbl_buttons` where id ='".$rsButtons[$intButton]['id']."';";
					$strQuery .= "\r\n";
				}
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
				if(count($rsButtons))
					$strQuery .= "INSERT INTO `tbl_buttons` (`id`, `table_id`, `page_type`, `key_col`, `field_name_u`, `confirm`, `action`, `seq_no`, `valign`, `halign`, `check_ref`, `cascade_action`) 
									  VALUES";
				for($intButton=0;$intButton<count($rsButtons);$intButton++)
				{
				if($intButton)
						$strQuery .= ",";
					$rsButtons[$intButton]['confirm']			=	str_replace("'","''",$rsButtons[$intButton]['confirm']);
					$rsButtons[$intButton]['key_col']			=	str_replace("'","''",$rsButtons[$intButton]['key_col']);
					$rsButtons[$intButton]['field_name_u']		=	str_replace("'","''",$rsButtons[$intButton]['field_name_u']);					
					$rsButtons[$intButton]['action']			=	str_replace("'","''",$rsButtons[$intButton]['action']);						
					$rsButtons[$intButton]['valign']			=	str_replace("'","''",$rsButtons[$intButton]['valign']);						
					$rsButtons[$intButton]['halign']			=	str_replace("'","''",$rsButtons[$intButton]['halign']);											
					$rsButtons[$intButton]['check_ref']			=	str_replace("'","''",$rsButtons[$intButton]['check_ref']);						
					$rsButtons[$intButton]['cascade_action']	=	str_replace("'","''",$rsButtons[$intButton]['cascade_action']);						
					$rsButtons[$intButton]['page_type']			=	str_replace("'","''",$rsButtons[$intButton]['page_type']);											

					
					$strQuery .= "('".$rsButtons[$intButton]['id']."','".$rsButtons[$intButton]['table_id']."','".$rsButtons[$intButton]['page_type']."', '".$rsButtons[$intButton]['key_col']."','".$rsButtons[$intButton]['field_name_u']."', '".$rsButtons[$intButton]['confirm']."', '".$rsButtons[$intButton]['action']."', '".$rsButtons[$intButton]['seq_no']."', '".$rsButtons[$intButton]['valign']."', '".$rsButtons[$intButton]['halign']."', '".$rsButtons[$intButton]['check_ref']."', '".$rsButtons[$intButton]['cascade_action']."')";
							
					$strQuery .= "\r\n";
				}
				if(count($rsButtons))
					$strQuery	.=	";";
				$strQuery .= "\r\n-- COMPLITED TBL_BUTTONS QUERY";
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
			}
			$strQuery .= "\r\n";
			if(!empty($setBlnSearch))
			{			
				
				$rsSearch = $objModule->getPageSearchQuery($rsTableId[$intTableId]);
				
				for($intSearch=0;$intSearch<count($rsSearch);$intSearch++)
				{
					$strQuery .= "DELETE FROM `tbl_search` where id ='".$rsSearch[$intSearch]['id']."';";
					$strQuery .= "\r\n";
				}
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
				if(count($rsSearch))
					$strQuery .= "INSERT INTO `tbl_search` (`id`, `table_id`, `field_name`, `field_referal`, `field_type`, `field_length`, `add_field_length_show`, `isrequired`, `add_html_text`, `add_extra_property`, `field_desc`, `seq_no`, `add_field_type`, `addedit_field_html_type`, `condition`, `db_field_name`) 
									  VALUES ";
				for($intSearch=0;$intSearch<count($rsSearch);$intSearch++)
				{
					if($intSearch)
						$strQuery .= ",";
	
					$rsSearch[$intSearch]['field_name']						=	str_replace("'","''",$rsSearch[$intSearch]['field_name']);
					$rsSearch[$intSearch]['field_referal']					=	str_replace("'","''",$rsSearch[$intSearch]['field_referal']);
					$rsSearch[$intSearch]['add_html_text']					=	str_replace("'","''",$rsSearch[$intSearch]['add_html_text']);
					$rsSearch[$intSearch]['add_extra_property']				=	str_replace("'","''",$rsSearch[$intSearch]['add_extra_property']);										
					$rsSearch[$intSearch]['field_desc']						=	str_replace("'","''",$rsSearch[$intSearch]['field_desc']);					
					$rsSearch[$intSearch]['db_field_name']					=	str_replace("'","''",$rsSearch[$intSearch]['db_field_name']);										
					$rsSearch[$intSearch]['database_object']				=	str_replace("'","''",$rsSearch[$intSearch]['database_object']);										
					$rsSearch[$intSearch]['condition']						=	str_replace("'","''",$rsSearch[$intSearch]['condition']);																				
					$rsSearch[$intSearch]['addedit_field_html_type']		=	str_replace("'","''",$rsSearch[$intSearch]['addedit_field_html_type']);										
					$rsSearch[$intSearch]['add_field_type']					=	str_replace("'","''",$rsSearch[$intSearch]['add_field_type']);										
					$rsSearch[$intSearch]['field_type']						=	str_replace("'","''",$rsSearch[$intSearch]['field_type']);										
					
					
					$strQuery .= "('".$rsSearch[$intSearch]['id']."','".$rsSearch[$intSearch]['table_id']."','".$rsSearch[$intSearch]['field_name']."', '".$rsSearch[$intSearch]['field_referal']."','".$rsSearch[$intSearch]['field_type']."', '".$rsSearch[$intSearch]['field_length']."', '".$rsSearch[$intSearch]['add_field_length_show']."', '".$rsSearch[$intSearch]['isrequired']."', '".$rsSearch[$intSearch]['add_html_text']."', '".$rsSearch[$intSearch]['add_extra_property']."', '".$rsSearch[$intSearch]['field_desc']."', '".$rsSearch[$intSearch]['seq_no']."','".$rsSearch[$intSearch]['add_field_type']."','".$rsSearch[$intSearch]['addedit_field_html_type']."','".$rsSearch[$intSearch]['condition']."','".$rsSearch[$intSearch]['db_field_name']."')";
							
					$strQuery .= "\r\n";
					$strQuery .= "\r\n";
				}
	
				if(count($rsSearch))
					$strQuery	.=	";";
				$strQuery .= "\r\n-- COMPLITED TBL_SEARCH QUERY";
				$strQuery .= "\r\n";
				$strQuery .= "\r\n";
			}		
		}
		return $strQuery;
	}
?>
