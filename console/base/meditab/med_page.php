<?php
class MedPage
{
	
	var $intTableId;
	
	var $chrShowIn;
	
	var $strPageTitle;
	
	var $objGeneral;
	
	var $intListCount;
	
	var $blnGetListCount;

	
	var $blnSetListPaging 	= 	true;
	
	var $blnSetListSearch 	= 	true;
	

	
	var $blnSetListColumnHeader = true;

	var $blnSetListAlpha  	=	true;
	
	var $blnsetListSort		=	true;
	
	var $blnSetListSelector	=	true;
	
	var $setListSelactor	=	true;
	
	var $intRecordsPerPage	=	0;
	var $intPagesPerGroup	=	0;

	
	var $strSetListFields;
	
	var $strListPageTitle;
	
	var $strSetListButtons	=	"";
	
	var $intPageListColumnNumber;
	
	var $strGroupFieldName;

	
	var $strOldGroupFieldType;

	
	var $strNewOrderByCond;

	
	var $pre = "";

	
	var $strAddFieldsInList = "";

	
	var $strRemoveFieldsFromList = "";

	
	var $blnExport	= false;

	
	var $arrSumField;

	
	var $blnDeleteKeyCol	= true;

	
	function __construct()
	{
		$this->enableOnlySecureMode(APP_HOST_PRIMARY_DOMAIN,IS_SECURE_APP);
		$this->objGeneral=MedGeneral::getGeneralObject();
		$GLOBALS["objPage"] = $this;
	}

	
	function getPageObject()
	{
		return $GLOBALS["objPage"];
	}
	
	function setRequest($strFieldName,$strFieldVal)
	{
		$_REQUEST[$strFieldName]=$strFieldVal;
	}

	
	function setAutoRequest($strFieldName,$intTableId,$strFieldDVal="")
	{
		$strVal	=	$this->getSRequest($strFieldName,$intTableId);

		if($strVal=="" && $strFieldDVal!="")
			$strVal	=	$strFieldDVal;

		
		if(!isset($_REQUEST[$strFieldName]))
			$this->setRequest($strFieldName,$this->objGeneral->getSession($intTableId.$strFieldName));
		else
			$this->objGeneral->setSession($intTableId.$strFieldName,$this->getRequest($strFieldName));

		return 	$strVal;
	}
	
	function setArgument($strFieldName,$strFieldVal)
	{
		$_REQUEST[$strFieldName]=$strFieldVal;
	}
	
	function getRequest($strFieldName)
	{
		if(array_key_exists($strFieldName,$_REQUEST)) return $_REQUEST[$strFieldName];
		else return NULL;
	}

	
	function getSRequest($strFieldName,$intTableId)
	{
		if(isset($_REQUEST[$strFieldName]))
			return $this->getRequest($strFieldName);
		else
			return	$this->objGeneral->getSession($intTableId.$strFieldName);
	}

	
	function setSRequest($strFieldName,$intTableId,$strDefaultVal)
	{
		if(isset($_REQUEST[$strFieldName]))
			return $this->getRequest($strFieldName);
		elseif(isset($_SESSION[$this->objGeneral->getSessionVarName($intTableId.$strFieldName)]))
			return	$this->objGeneral->getSession($intTableId.$strFieldName);
		else
		{
			$this->setRequest($strFieldName,$strDefaultVal);
			return $strDefaultVal;
		}
	}

	
	function setTableProperty(&$objData)
	{
		if($objData == NULL)
		{
			
			$this->objGeneral->raiseError("ERROR_INOBJECT","There is no data available in object on this id:".$this->getTable(),"Object Property is Null","Pass Proper Data And Check Object.");
		}
		else
		{
			$intTblId		= $this->getRequest("hid_table_id"); 
			$strPageType	= strtoupper($this->getRequest("hid_page_type")); 
			$intButtonId	= $this->getRequest("hid_button_id");
			if($intButtonId!=NULL) $rsData = $this->getButtons("id",$intButtonId);
			else $rsData = $this->getTableKeyCol($intTblId); 
			if ($rsData)
			{
				switch(trim(strtoupper($strPageType)))
				{
					case "A":
							$strAttribute = $rsData[0]['add_key_col'];
							break;

					case "E":
							$strAttribute = $rsData[0]['edit_key_col'];
							break;

					case "D" or "U":
							$strAttribute = $rsData[0]['key_col'];
							break;
					case "L" :
							$strAttribute = $rsData[0]['edit_key_col'];
							break;
				}

				if ($strAttribute != null )
				{
					if(ereg("(:)",$strAttribute))
					{
						$arrAtribute = explode(":",$strAttribute);
						$strTableName = $arrAtribute[0];
						if (count($arrAtribute) > 1 ) $strPkField = $arrAtribute[1];
						else $strPkField = null;
					}
					else $strTableName = $strAttribute;
					$objData->setProperty($strTableName,$strPkField,null,null);
				}
			}
		}
	}

	
	function setAddFieldsInList($strAddFieldsInList)
 	{
		$this->strAddFieldsInList	=	$strAddFieldsInList;
 	}

 	
	function setRemoveFieldsFromList($strRemoveFieldsFromList)
	{
		$this->strRemoveFieldsFromList = $strRemoveFieldsFromList;
	}

	
	function setListSearch($blnSetListSearch)
	{
		$this->blnSetListSearch		=	$blnSetListSearch;
	}

	
	function setListColumnHeader($blnSetListColumnHeader)
	{
		$this->blnSetListColumnHeader 	=	$blnSetListColumnHeader;
	}

	
	function setListAlpha($blnSetListAlpha)
	{
		$this->blnSetListAlpha		=	$blnSetListAlpha;
	}

	
	function setListPaging($blnSetListPaging)
	{
		$this->blnSetListPaging 	= 	$blnSetListPaging;
	}

	
	function setListSort($setListSort)
	{
		 $this->blnsetListSort			=	 $setListSort;
	}
	
	function setListSelactor($blnSetListSelector)
	{
		$this->blnSetListSelector	=	$blnSetListSelector;
	}

	
	function setListFields($strSetListFields)
	{
		$this->strSetListFields		=	$strSetListFields;
	}

	
	function setListButtons($strSetListButtons)
	{
		$this->strSetListButtons 	=	$strSetListButtons;
	}
	
	function setListPageTitle($strPageTitle)
	{
		$this->strListPageTitle			=	$strPageTitle;
	}

	
	function getHtmlMulti($intTableMultiId)
	{
		
		$strTbl_Name	=	$this->pre."tbl_table_multi";
		
		$strField_Names	=	"table_multi_id, table_id, page_title, issearch, isalpha, ispaging, isselector, table_desc, field_id, button_id, issort,iscolumnheading,listcolumn";
		
		$strWhere		=	"table_multi_id = ".$intTableMultiId;
		
		$rsMulti=$this->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");

		$strListName = "list".$rsMulti[0]['table_id'];
		if ($this->isGroupFieldAvailable($rsMulti))
			$objList = new MedGroupList($strListName); 
		else
			$objList = new MedQuickList($strListName); 

		
		if($rsMulti[0]['issearch'] == 0 || $rsMulti[0]['issearch'] == NULL )		$this->setListSearch(false);
		if($rsMulti[0]['isalpha'] == 0 || $rsMulti[0]['ispaging'] == NULL )			$this->setListAlpha(false);
		if($rsMulti[0]['ispaging'] == 0 || $rsMulti[0]['ispaging'] == NULL )		$this->setListPaging(false);
		if($rsMulti[0]['isselector'] == 0 || $rsMulti[0]['isselector'] == NULL )	$this->setListSelactor(false);
		if($rsMulti[0]['issort'] == 0 || $rsMulti[0]['issort'] == NULL )			$this->setListSort(false);
		else $this->setListSort(true);

		if($rsMulti[0]['iscolumnheading'] == 0)										$this->setListColumnHeader(false);
		else $this->setListColumnHeader(true);

		
		if($rsMulti[0]['field_id'] != NULL)		$this->setListFields($rsMulti[0]['field_id']);
		if($rsMulti[0]['button_id'] != NULL)	$this->setListButtons($rsMulti[0]['button_id']);
		else 	$this->setListButtons("false");

		if($rsMulti[0]['page_title'] != NULL)	$this->setListPageTitle($rsMulti[0]['page_title']);

		$this->intPageListColumnNumber = $rsMulti[0]['listcolumn'];
		
		$intTableId		=	$rsMulti[0]['table_id'];
		
		return $this->getHtmlPage($intTableId,"L");
	}



	function getHtmlPage($intTableId,$chrShowIn,$blnCustom=false,$blnButton=true)
	{
		global $objDb;
		$this->setTable($intTableId);
		$this->setPageType(strtoupper($chrShowIn));

		
		$strTbl_Name	=$this->pre."tbl_table,".$this->pre."tbl_fields";
		$strField_Names	=" ".$this->pre."tbl_table.*,".$this->pre."tbl_fields.* ";
		if(trim($this->strSetListFields) != NULL)
		{
		$strSetListFields = "and (".$this->pre."tbl_fields.id in (".$this->strSetListFields.") or ishidden=1)";
		$strWhere		="(".$this->pre."tbl_table.table_id=".$this->pre."tbl_fields.table_id and ".$this->pre."tbl_table.table_id=".$this->getTable()." ".$strSetListFields.") ";
		}
		else
		{

			if($this->strAddFieldsInList != "")
				$strAddFieldsInList	=	" or ". $this->pre."tbl_fields.id in (".$this->strAddFieldsInList.")";
			if($this->strRemoveFieldsFromList != "")
				$strRemoveFieldsFromList	=	" and ". $this->pre."tbl_fields.id not in (".$this->strRemoveFieldsFromList.")";
			$strWhere		=	"(".$this->pre."tbl_table.table_id=".$this->pre."tbl_fields.table_id
								and ".$this->pre."tbl_table.table_id=".$this->getTable().")
						  		and (((show_in like '%".$this->getPageType()."%' ".$strRemoveFieldsFromList.") ".
								$strAddFieldsInList." ) and (ishidden=0 or ishidden=1))";

		}
		$strOrder_By	=" seq_no ";

		$rsFields=$this->getRecords($strTbl_Name,$strField_Names,$strWhere,"","",$strOrder_By,"");
		
		
		if(count($rsFields)>0)
		{
			$this->setPageTitle($rsFields[0]['page_title']);	
			
			if($this->chrShowIn=='L') 	return	$this->generateListing($rsFields);  
			else return $this->generateAddEdit($rsFields,$blnCustom,$blnButton);
		}
		else
		{
			$this->objGeneral->raiseError("ERROR_TBLNOREC","There is no data available in tbl_table or tbl_fields on this id:".$this->getTable(),"No Title on this Id","Add the proper data in tbl_table and tbl_fields");
		}
	}

	
	function getListHeader($intTableId,$chrShowIn)
	{
		$this->setTable($intTableId);
		$this->setPageType(strtoupper($chrShowIn));
		
		$strTbl_Name	= $this->pre."tbl_table,".$this->pre."tbl_fields";
		$strField_Names	=" ".$this->pre."tbl_table.*,".$this->pre."tbl_fields.* ";


		if(trim($this->strSetListFields) != NULL)
		{
			$strSetListFields = "and (".$this->pre."tbl_fields.id in (".$this->strSetListFields.") or ishidden=1)";
			$strWhere		="(".$this->pre."tbl_table.table_id=".$this->pre."tbl_fields.table_id and ".$this->pre."tbl_table.table_id=".$this->getTable()." ".$strSetListFields.") and (show_in like '%L%' or show_in='') ";
		}
		else
		{
			$strWhere		="(".$this->pre."tbl_table.table_id=".$this->pre."tbl_fields.table_id and ".$this->pre."tbl_table.table_id=".$this->getTable().")
						  and ((show_in like '%".$this->getPageType()."%') and (ishidden=0 or ishidden=1))";

		}

		$strOrder_By	=" seq_no ";

		$rsFields=$this->getRecords($strTbl_Name,$strField_Names,$strWhere,"","",$strOrder_By,"");
		return $rsFields;
	}


	
	function getListHeaderMulti($intTableId,$intMultiTableId,$chrShowIn)
	{
		
		$strTbl_Name	=	$this->pre."tbl_table_multi";
		
		$strField_Names	=	"table_multi_id, table_id, page_title, issearch, isalpha, ispaging, isselector, table_desc, field_id, button_id, issort,iscolumnheading,listcolumn";
		
		$strWhere		=	"table_multi_id = ".$intMultiTableId;
		
		$rsMulti=$this->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");

		
		if($rsMulti[0]['field_id'] != NULL)		$this->setListFields($rsMulti[0]['field_id']);

		if($rsMulti[0]['iscolumnheading'] == 0)
			$this->setListColumnHeader(false);
		else
			$this->setListColumnHeader(true);

		$rsFields = $this->getListHeader($intTableId,$chrShowIn);

		return $rsFields;
	}

	
	function getParaForSelect($rsFields,&$strTbl_Name,&$strField_Names,&$strWhere,&$strGroup_By,&$strHaving,&$strOrder_By,&$strLimit)
	{
		
		$strField_Names="";
		$strWhere=$rsFields[0]['where_clause'];
		$strGroup_By=$rsFields[0]['group_clause'];
		$strHaving=$rsFields[0]['having_clause'];

		
		if($this->chrShowIn=='L') $strTbl_Name=$rsFields[0]['list_table_name'];
		else $strTbl_Name=$rsFields[0]['addedit_table_name'];
		$strOrder_By=$rsFields[0]['order_clause'];

		$strField_Names=$this->getFields($rsFields);	 
	}

	
	function getConcateFields($rsFields)
	{
		
		$strField_Names="";
		for($iniFields=0;$iniFields<count($rsFields);$iniFields++)
		{
			if($rsFields[$iniFields]['field_type']!="" && $rsFields[$iniFields]['ishidden']!=1)
			{
				$strField_Names			.=	$rsFields[$iniFields]['field_name'].", ";
				$strListFieldHtmlType	.= 	$rsFields[$iniFields]['list_field_html_type'].", ";
				$strListHtmlText		.= 	$rsFields[$iniFields]['list_html_text'].", ";
			}
		}
		
		$strField_Names			=	substr($strField_Names,0,(strlen($strField_Names)-2));
		
		$strListFieldHtmlType	=	substr($strListFieldHtmlType,0,(strlen($strListFieldHtmlType)-2));
		
		$strListHtmlText		=	substr($strListHtmlText,0,(strlen($strListHtmlText)-2));
		$arrFieldInfo["strField_Names"]			=	$strField_Names;
		$arrFieldInfo["strListFieldHtmlType"]	=	$strListFieldHtmlType;
		$arrFieldInfo["strListHtmlText"]		=	$strListHtmlText;
		return $arrFieldInfo;
	}

	
	function getFields($rsFields)
	{
		
		$strField_Names="";
		for($iniFields=0;$iniFields<count($rsFields);$iniFields++)
		{
			if($rsFields[$iniFields]['field_type']!="") $strField_Names.=$rsFields[$iniFields]['field_name'].", ";
		}
		$strField_Names=substr($strField_Names,0,(strlen($strField_Names)-2)); 
		return $strField_Names;
	}

	
	function getButtons($strFieldName,$strFieldValue)
	{
		$strTblName		= $this->pre."tbl_buttons";


		$strFieldNnames	= "id,table_id,page_type,key_col,field_name_u,confirm,action,seq_no,valign,halign,check_ref,cascade_action";
		if(trim($this->strSetListButtons) != NULL)	$strSetListButtons = "and id in (".$this->strSetListButtons.")";
		$strWhere		= $strFieldName." = ".$strFieldValue." ".$strSetListButtons;
		$strOrderBy		= "seq_no";

		$rsButtons		= MedPage::getRecords($strTblName, $strFieldNnames, $strWhere, "", "",$strOrderBy, "");

		return $rsButtons;
	}
	function isGroupFieldAvailable($rsFields)
	{
		$blnFoundGroupField=false;
		for($intFields=0;$intFields<count($rsFields);$intFields++)
		{
			if (strtoupper($rsFields[$intFields]['list_field_html_type']) == "GROUP")
			{
				$blnFoundGroupField=true;
				break;
			}
		}
		return $blnFoundGroupField;
	}

	
	function getListingSqlQuery($rsFields)
	{
		$arrListInfo	 = array();
		$arrFieldsDetail = $this->getConcateFields($rsFields); 
		$strFields		 = $arrFieldsDetail["strField_Names"];
		@eval("\$strFields = \"$strFields\";");
		$strTableName=$rsFields[0]['list_table_name'];
		@eval("\$strTableName = \"$strTableName\";");
		$strWhere=$rsFields[0]['where_clause'];
		if(MedPage::getRequest('strWhere')!='')	$strWhere.=" ".MedPage::getRequest('strWhere');
		@eval("\$strWhere = \"$strWhere\";");
		$strOrderBy = $rsFields[0]['order_clause'];
		@eval("\$strOrderBy = \"$strOrderBy\";");

		$strGroupBy = $rsFields[0]['group_clause'];
		@eval("\$strGroupBy = \"$strGroupBy\";");

		
		if((!empty($this->strGroupFieldName)) && (!empty($this->strNewOrderByCond))) $strOrderBy=$this->strNewOrderByCond;

		
		$arrListRecord	= $this->getRecords($strTableName, $strFields, $strWhere, $strGroupBy, "",$strOrderBy, "");

		
		$arrListInfo["arrListRecord"]		=	$arrListRecord;
		$arrListInfo["strListFieldHtmlType"]=	$arrFieldsDetail["strListFieldHtmlType"];
		$arrListInfo["strListHtmlText"]		=	$arrFieldsDetail["strListHtmlText"];
		return $arrListInfo;
	}

	
	function generateListing($rsFields)
	{
		
		$intExpCordinate = $this->getRequest("btn_export_x");
		if(!empty($intExpCordinate))
			$this->blnExport=true;

		$objListData = new MedDataList(); 
		if($this->blnDeleteKeyCol)
			$arrKeycol = explode(":",$rsFields[0]['delete_key_col']); 

		$arrSerch = explode(":",$rsFields[0]['issearch']);
		if(count($arrKeycol) > 0) $intPkey= trim($arrKeycol[1]); 
		else $intPkey = NULL;

		$strFields = $this->getFields($rsFields); 
		@eval("\$strFields = \"$strFields\";");
		$strTableName=$rsFields[0]['list_table_name'];
		@eval("\$strTableName = \"$strTableName\";");
		$strWhere=$rsFields[0]['where_clause'];
		if(MedPage::getRequest('strWhere')!='')	$strWhere.=" ".MedPage::getRequest('strWhere');
		@eval("\$strWhere = \"$strWhere\";");
		$strOrderBy = $rsFields[0]['order_clause'];
		@eval("\$strOrderBy = \"$strOrderBy\";");

		$strGroupBy = $rsFields[0]['group_clause'];
		@eval("\$strGroupBy = \"$strGroupBy\";");

		if(!preg_match("/ ".$intPkey.",/i",$strFields))
		{
			if($intPkey != NULL)  $strFields .= ",".$intPkey;
		}

		
		
		if((!empty($this->strGroupFieldName)) && (!empty($this->strNewOrderByCond))) $strOrderBy=$this->strNewOrderByCond;

		 
		$objListData->setProperty($strTableName,$intPkey,$strFields,$strWhere,$strOrderBy,$strGroupBy); 
		$strListName = "list".$rsFields[0]["table_id"];
		if ($this->isGroupFieldAvailable($rsFields))
			$objList = new MedGroupList($strListName); 
		else
			$objList = new MedQuickList($strListName); 

		$GLOBALS['objList']=$objList;

		$objList->setJsDirectory("js/"); 

		
		if($this->intPageListColumnNumber != "")	$objList->intListColumnNumber = $this->intPageListColumnNumber;

		if($rsFields[0]['iscolumnheading'] == 0)	$objList->blnShowHeader = false;
		if($this->blnSetListColumnHeader == false)	$objList->blnShowHeader = false;
		else $objList->blnShowHeader = true;

		if(!empty($rsFields[0]['list_message']))
		{
			$objList->strNoDataMessage = $rsFields[0]['list_message'];
		}

		
		$arrOrderBy=explode(",",$strOrderBy);
		if ((!empty($strOrderBy)) && (count($arrOrderBy)<2))
		{
			$arrOrdParam = explode(" ",$strOrderBy);
			$objList->setDefaultOrderField($arrOrdParam[0]);
			$objList->setDefaultOrder($arrOrdParam[1]);

		}
		
		if($this->blnSetListPaging == true)
		{
			if($arrSerch[2]==1)
			{
				$objList->setShowPaging(true);

				if($this->intRecordsPerPage!=0)
					$intRecordsPerPage = $this->intRecordsPerPage;
				else
					$intRecordsPerPage = $this->objGeneral->getSettings("GEN_REC_PER_PAGE");

				if($intPagesPerGroup!=0)
					$intPagesPerGroup = $this->intPagesPerGroup;
				else
					$intPagesPerGroup = $this->objGeneral->getSettings("GEN_PAGE_PER_GROUP");

				
				if ($intRecordsPerPage <= 0 ) $intRecordsPerPage = 10;
				if ($intPagesPerGroup <= 0 ) $intPagesPerGroup = 10;

				$objList->setRecordsPerPage($intRecordsPerPage);
				$objList->setPagesPerGroup($intPagesPerGroup); 
			}
			else  $objList->setShowPaging(false);
		}
		else $objList->setShowPaging(false);

		$intLstCount = $objListData->countRecords();

		
		if($intLstCount <= 0) $this->setListSort(false);

		
		if($arrSerch[2]==0 or $this->blnGetListCount==true)
		{
			$intListCount = $objListData->countRecords();
			$this->setListCount($intListCount);
		}
		$this->blnGetListCount=false;

		if(($rsFields[0]['delete_key_col'] != NULL) && ($intPkey != NULL)&& ($this->blnSetListSelector == true) && ($this->blnDeleteKeyCol))
		{

			
			$objList->setShowSelector(true);
			if (isset($arrSerch[3]) && ($arrSerch[3] == -1 ))
					$objList->setShowSelector(false);

			$objList->setSelectionLimit($arrSerch[3]); 
		}
		else
		{
			$objList->setShowSelector(false); 
			$objList->setSelectionLimit(0); 
		}

		$objList->setSelectedValues($this->getRequest('arrSelectedValues'));
		$objList->setRestrictedValues($this->getRequest('arrRestrictedValues'));

		$objList->setCssClass("qlist");

		$this->generateListingFields($rsFields,$objList); 

		if($arrSerch[1] != "") $objList->setAlphaSearchField($arrSerch[1]);

		
		if($this->strSetListButtons!="false")
			$rsButtons=$this->getButtons("table_id",$rsFields[0]['table_id']);


		if($rsButtons!=NULL)
		{
			$objList->setButtonAlign("left"); 

			for($intButton=0;$intButton<count($rsButtons);$intButton++) 
			{
				$arrButtonDet	= explode(":",$rsButtons[$intButton]['page_type']);	
				$strPageType	= $arrButtonDet[0];	
				$strButtonName	= $arrButtonDet[1];	
				$strAction		= $rsButtons[$intButton]['action'];	

				$arrMsgDet		= explode(":",$rsButtons[$intButton]['confirm']);	
				$blnShowAllow	= $arrMsgDet[0];	
				$strMessage		= $arrMsgDet[1];	
				$strHAlign		= $rsButtons[$intButton]["halign"];
				$strVAlign		= $rsButtons[$intButton]["valign"];

				if($strPageType	==	NULL) $strPageType="0";	
				if(empty($strAction))	
				{
					if(trim(strtoupper($strPageType))=="A")	$strAction="med_list_record";
					else $strAction="med_action";
				}

				$strBtnKeyCol =  $rsButtons[$intButton]['key_col'];
				@eval("\$strBtnKeyCol = \"$strBtnKeyCol\";");

				if(trim(strtoupper($strPageType))=="A") $strKeyCol=$strBtnKeyCol; 
				else $strKeyCol="0";


				$strLink = $rsButtons[$intButton]['id'].":".$strPageType.":".$strKeyCol.":".$strAction; 

				$objList->addButton($strButtonName,$strLink,$blnShowAllow,$strMessage,null,$strHAlign,$strVAlign); 
			}
		}

		
		if(count($this->arrSumField)>0)
		{
			foreach($this->arrSumField as $strKey => $strValue)
			{
				$objList->objGroupColumn->AddGroupExpression($objList->arrColumnArray[$strValue], MedGroupColumn::GROUP_EXPR_TYPE_SUM);
			}
		}

		if($this->blnExport)
		{
			set_time_limit(0);
			$strFileName	= $rsFields[0]["page_title"];
			$strFileName	= str_replace('-','',$strFileName);
			$strFileName	= str_replace('/','',$strFileName);
			$strFileName	= str_replace(" ","-",$strFileName);
			$strFileName	= strtoupper($this->pre).$strFileName.date('Y-m-d').".xls";
			
			header("Content-Type: application/force-download");
			header("Content-Disposition: attachment; filename=".$strFileName);
 		    header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			$objList->blnExport = $this->blnExport;
			$objList->show($objListData); 
			exit;
		}
		else
			return $objList->show($objListData); 

	}

	

	function generateListingFields(&$rsFields,$objList)
	{
		$arrSerch = explode(":",$rsFields[0]['issearch']);
		for($intFields=0;$intFields<count($rsFields);$intFields++) 
		{
			if($rsFields[$intFields]['sql_field']!='') $strFieldName = $rsFields[$intFields]['sql_field'];
			else  $strFieldName   = $rsFields[$intFields]['field_name'];

			$strFieldTitle  = $rsFields[$intFields]['field_title'];

			if($strFieldName == NULL )	$this->objGeneral->raiseError("ERROR_IN_LIST","FieldName or Field Title Not Found",$this->getPageTitleByDb($rsFields[0]['table_id']),"Make Change In Field Title or Field Name");
			else $this->generateListingFieldsByType($rsFields,$intFields,$objList,$arrControl);

			if($this->blnSetListSearch == true)
			{
		    	if($arrSerch[0] == 1) $objList->setMultipleSearchField($arrControl);
			}

		}

	}

	

	function generateListingFieldsByType(&$rsFields,$intFields,&$objList,&$arrControl)
	{

		if($rsFields[$intFields]['sql_field']!="") $strFieldName=$rsFields[$intFields]['sql_field'];
		else $strFieldName=$rsFields[$intFields]['field_name'];
		$strFieldTitle  	= $rsFields[$intFields]['field_title'];
		$strHtmlLink    	= $rsFields[$intFields]['html_link'];
		@eval("\$strHtmlLink = \"$strHtmlLink\";");


		$intHeaderWidth 	= $rsFields[$intFields]['header_width'];
		$strHeaderAlign 	= $rsFields[$intFields]['header_align'];
		$strFieldType   	= $rsFields[$intFields]['field_type'];
		$intIsHidden    	= $rsFields[$intFields]['ishidden'];
		$intIsSort      	= $rsFields[$intFields]['issort'];
		if($this->blnsetListSort == true)		$intIsSort      	= $rsFields[$intFields]['issort'];
		else		$intIsSort      	= 0;
		$strListHtmlText	= $rsFields[$intFields]['list_html_text'];

		@eval("\$strListHtmlText = \"$strListHtmlText\";");

		$strExtraParam		= $rsFields[$intFields]['list_event']." ".$rsFields[$intFields]['list_extra_property'];
		$strEditKeyColomn	= $rsFields[$intFields]['edit_key_col'];
		$arrEditKeyColomn	= explode(":",$strEditKeyColomn);
		$strPK				= $arrEditKeyColomn[1];
		if($rsFields[$intFields]['ishidden'] <> 1)
		{

			if($strFieldTitle != NULL && $strFieldType != NULL) $this->addArray($arrControl,$strFieldName,$strFieldTitle);	
			$strFieldListType=strtoupper($rsFields[$intFields]['list_field_html_type']);

			
			if(!empty($this->strGroupFieldName))
			{
				
				if($strFieldListType=="GROUP") $strFieldListType=$this->strOldGroupFieldType;

				
				if($this->strGroupFieldName==$strFieldName) $strFieldListType="GROUP";

			}

			switch($strFieldListType)
			{
				case "TEXT" 	: 
								if($strPK <> "") $objList->addTextBoxItem($strFieldTitle,array($strFieldName,$strPK),$intHeaderWidth."%",$strListHtmlText,$strHeaderAlign,$strFieldName,$strFieldType,$intIsSort,$strExtraParam);
								else $objList->addTextBoxItem($strFieldTitle,array($strFieldName),$intHeaderWidth."%",$strListHtmlText,$strHeaderAlign,$strFieldName,$strFieldType,$intIsSort,$strExtraParam);
								break;
				case "CHECKBOX" :
								$objList->addCheckBoxItem($strFieldTitle, array($strFieldName,$strPK), $intHeaderWidth."%",$strHeaderAlign,$strFieldName,$strFieldType,$intIsSort,$strExtraParam);
								break;
				case "SELECT"	:
								
								$objList->addComboItem($strFieldTitle,array($strFieldName,$strPK),null,$strListHtmlText,"top",$strListHtmlText,false,$strExtraParam);
								break;
				case "CONDITION":
								$arrCondition=$this->generateSwitchCombo($strListHtmlText);
								
								$objList->addConditionalItem($strFieldTitle, array($strFieldName,$strPK), $arrCondition, $intHeaderWidth, $strHeaderAlign,$intIsSort);
								break;
				case "IMAGE"	:
								if($this->blnExport==false)
								{
									if($strHtmlLink != "")
									{
										$arrLink=explode(":",$strHtmlLink);

										
										if (trim($arrLink[0]) =='#') $arrLink[0]="javascript:void(0)";
										else if ($arrLink[0]!="") $arrLink = $this->generateHtmlLinks($strHtmlLink,$objList);

										if (empty($arrLink[1])) $arrLink[1] = $strPK;

										$strImageParam =  $rsFields[$intFields]['list_extra_property'];
										$strLinkParam = $rsFields[$intFields]['list_event'];

										$arrParam=array($strFieldName);
										for($intLink=1;	$intLink < count($arrLink); $intLink++)
											$arrParam[]=$arrLink[$intLink];

										$objList->addImageItem($strFieldTitle,$arrParam,$strListHtmlText,$arrLink[0],$strImageParam,$strLinkParam,$intHeaderWidth."%",$strHeaderAlign,$intIsSort);
									}
									else
										$objList->addTextItem($strFieldTitle,$strFieldName,$intHeaderWidth."%",$strHeaderAlign,$intIsSort);
								}
								break;

				case "GROUP"	:
								if($strHtmlLink != "")
								{
									$arrLink=explode(":",$strHtmlLink);
									$arrTemp=$arrLink;
									
									if (trim($arrLink[0]) =='#')	$arrLink[0]="javascript:void(0)";
									else if ($arrLink[0]!="")	$arrLink = $this->generateHtmlLinks($strHtmlLink,$objList);

									$arrParam=array($strFieldName);
									if (empty($arrLink[1]))	$arrLink[1] = $strPK;

									for($intLink=1;	$intLink < count($arrTemp); $intLink++)
										$arrParam[]=$arrTemp[$intLink];

									$objGroupColumn = $objList->addLinkItem($strFieldTitle,$arrParam,null,$arrLink[0],$strExtraParam,$intHeaderWidth."%",$strHeaderAlign,$intIsSort,$strFieldType);
								}
								else
								$objGroupColumn = $objList->addTextItem($strFieldTitle,$strFieldName,$intHeaderWidth."%","Left",$intIsSort,$strFieldType);					$objGroupColumn = $objList->setGroupOn($objGroupColumn,$strFieldName,1);
				break;
				case "FILE"	:

								$objGroupColumn = $objList->addFileItem($strFieldTitle,$strFieldName,$strListHtmlText,$intHeaderWidth."%",$strHeaderAlign,$intIsSort,$strFieldType);
								break;

				default 		: 
								if($strFieldType=='' && $strListHtmlText!='')
								{
									$arrLink=explode(":",$strHtmlLink);
									$arrTemp=$arrLink;
									if($arrLink[0]!="")
									{
										$arrLink = $this->generateHtmlLinks($strHtmlLink,$objList); 
										if($this->blnExport==false)
											$strListHtmlText='<a href="'.$arrLink[0].'">'.$strListHtmlText.'</a>';
										else
											$strListHtmlText=$strListHtmlText;
									}

									$arrParam=array($strFieldName);
									if (empty($arrLink[1])) $arrLink[1] = $strPK;

									for($intLink=1;	$intLink < count($arrTemp); $intLink++)
										$arrParam[]=$arrTemp[$intLink];

									$objList->addEvaluatedExprItem($strFieldTitle,$arrParam,$strListHtmlText,$intHeaderWidth."%",$strHeaderAlign);
								}
								elseif($strHtmlLink != "")
								{
									$arrLink=explode(":",$strHtmlLink);
									$arrTemp=$arrLink;
									
									if (trim($arrLink[0]) =='#') $arrLink[0]="javascript:void(0)";
									else if ($arrLink[0]!="") $arrLink = $this->generateHtmlLinks($strHtmlLink,$objList);

									$arrParam=array($strFieldName);
									if (empty($arrLink[1])) $arrLink[1] = $strPK;

									for($intLink=1;	$intLink < count($arrTemp); $intLink++)
										$arrParam[]=$arrTemp[$intLink];

									$objList->addLinkItem($strFieldTitle,$arrParam,null,$arrLink[0],$strExtraParam,$intHeaderWidth."%",$strHeaderAlign,$intIsSort,$strFieldType);
								}
								else
									$objList->addTextItem($strFieldTitle,$strFieldName,$intHeaderWidth."%",$strHeaderAlign,$intIsSort,$strFieldType);
								break;
			}
		}
	}

	
	function generateHtmlLinks($strHtmlLink,$objList)
	{
		if(ereg("(:)", $strHtmlLink))
		{
				$tempString = explode(":",$strHtmlLink);
				$strHtmlLink = $tempString[0].":".$tempString[1];
				$strLink = str_replace(":","&",$strHtmlLink)."={1}";
				$arrHtmlLink=explode(":",$strHtmlLink);
				$strLinkFieldName=$arrHtmlLink[1];
		}
		else
		{
				$strLink = $strHtmlLink;
				$strLinkFieldName="";
		}
		return array($strLink,$strLinkFieldName);
	} 

	
	function generateSearch($intTableId,&$strWhere=NULL)
	{

		
		$rsFields	=	$this->getSearchDetailByTableId($intTableId);

		$arrCustomHtml = array();		
		
		for($intFields=0;$intFields<count($rsFields);$intFields++)
		{
			
			$strFieldName=$rsFields[$intFields]['field_name'];

			
			if($rsFields[$intFields]['ishidden'] <> 1 && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="HIDDEN" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="SAVEHIDDEN")
			{
				$strTempFieldName=$strRequiredStar.$rsFields[$intFields]['field_referal'].":";
				$arrCustomHtml["Sr_LBL_".$strFieldName] = $strTempFieldName;
			}
				$strHtmltype=$rsFields[$intFields]['addedit_field_html_type'];
				
				$arrHtmlControl="";
				$this->addArray($arrHtmlControl,"type",strtolower($strHtmltype));
				$this->addArray($arrHtmlControl,"field_type",$rsFields[$intFields]['add_field_type']);
				$this->addArray($arrHtmlControl,"field_name",$strFieldName);
				$this->addArray($arrHtmlControl,"isrequired",$rsFields[$intFields]['isrequired']);
				$this->addArray($arrHtmlControl,"size",$rsFields[$intFields]['add_field_length_show']);
				$this->addArray($arrHtmlControl,"maxlength",$rsFields[$intFields]['field_length']);
				$this->addArray($arrHtmlControl,"tbl_name",$rsFields[$intFields]['add_html_text']);
				$this->addArray($arrHtmlControl,"event",$rsFields[$intFields]['add_extra_property']);
				$this->addArray($arrHtmlControl,"field_referal",$rsFields[$intFields]['field_referal']);

				$strName = $this->generateHtmlControlName(strtolower($strHtmltype),$rsFields[$intFields]['isrequired'],$rsFields[$intFields]['add_field_type'],$strFieldName,$arrHtmlControl); 
				$strName = "Sr_".$strName;

				
				if(!isset($_REQUEST[$strName]))
					$this->setRequest($strName,$this->objGeneral->getSession($intTableId.$strName));
				else
					$this->objGeneral->setSession($intTableId.$strName,$this->getRequest($strName));

				$strValue="";
				if(strtoupper($rsFields[$intFields]['field_type'])=="DATE")
				{
					if($this->getRequest($strName) != "") $strValue=$this->getRequest($strName);
					else $strValue="";
				}
				else
				if($this->getRequest($strName) != "") $strValue=$this->getRequest($strName);
				elseif(strtoupper($rsFields[$intFields]['addedit_field_html_type'])=='SAVEHIDDEN' || strtoupper($rsFields[$intFields]['addedit_field_html_type'])=='HIDDEN' || strtoupper($rsFields[$intFields]['addedit_field_html_type'])=='TEXT' || strtoupper($rsFields[$intFields]['addedit_field_html_type'])=='LABEL' )
				{
					$strValue=$rsFields[$intFields]['add_html_text'];
					@eval("\$strValue = $strValue;");
				}

				$this->addArray($arrHtmlControl,"value",str_replace("\"","&quot;",$strValue));

				
				if(strtoupper($strHtmltype)=="TEXT" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="HIDDEN" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="SAVEHIDDEN")
				{
					
					$blnFocus=true;
				}
				else $strFocus="";


				
				$arrCustomHtml["Sr_str".$strFieldName] = $this->generateHtmlControl($arrHtmlControl,true);
				if(strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="HIDDEN" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="SAVEHIDDEN" && strtoupper($strHtmltype)!='LABEL')
				{
					$arrCustomHtml["Sr_str".$strFieldName].="<script> var LBL_".$strName."=\"".$rsFields[$intFields]['field_referal']."\";</script>".$strFocus;
				}
		}

		
		$strWhere	=	$this->generateSearchWhere($rsFields);

		return $arrCustomHtml;
	}
	
	function generateAddEdit($rsFields,$blnCustom=false,$blnButton=true)
	{
		if(!$blnCustom)  
		{
			ob_clean();
			ob_start();

			echo "<table width='100%'>";	
			if($rsFields[0]['addedit_action_link'] != "") echo "<input type='hidden' name='file' id='file' value='".$rsFields[0]['addedit_action_link']."'>";
			else echo "<input type='hidden' name='file' id='file'  value='med_action'>";
		}
		else
		{
			$arrCustomHtml = array();		
			if($rsFields[0]['addedit_action_link'] != "") $arrCustomHtml["strHidFile"]="<input type='hidden' name='file' id='file' value='".$rsFields[0]['addedit_action_link']."'>";
			else $arrCustomHtml["strHidFile"]="<input type='hidden' name='file' id='file'  value='med_action'>";
		}

		
		if(strtoupper($this->chrShowIn) == "E" || strtoupper($this->chrShowIn) == "V")
		{
			$arrKeyCol=explode(":",$rsFields[0]['edit_key_col']);
			$strTbl_Name=$arrKeyCol[0];
			$strField_Name=$arrKeyCol[1];
			$this->getParaForSelect($rsFields,$strTbl_Name,$strField_Names,$strWhere,$strGroup_By,$strHaving,$strOrder_By,$strLimit); 	

			if(!eregi("where",$strTbl_Name)) $strWhere=" ".$strField_Name." = '".$this->getRequest($strField_Name)."'";
			else	$strWhere="";
			eval("\$strTbl_Name = \"$strTbl_Name\";");
			eval("\$strWhere = \"$strWhere\";");

			
			$rsPatientInfo=$this->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");
			
	 		$arrHtmlControl="";
			$this->addArray($arrHtmlControl,"type","hidden");
			$this->addArray($arrHtmlControl,"field_name",$strField_Name);
			$this->addArray($arrHtmlControl,"value",$this->getRequest($strField_Name));

			
			if(!$blnCustom)	echo $this->generateHtmlControl($arrHtmlControl);
			else $arrCustomHtml["strHidUpdateID"]=$this->generateHtmlControl($arrHtmlControl);
		}

		$blnFocus=false;
		
		for($intFields=0;$intFields<count($rsFields);$intFields++)
		{
			if($rsFields[$intFields]['sql_field']!='') $strFieldName=$rsFields[$intFields]['sql_field'];
			else $strFieldName=$rsFields[$intFields]['field_name'];

			
			if($rsFields[$intFields]['ishidden'] <> 1 && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="HIDDEN" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="SAVEHIDDEN")
			{
				if($this->chrShowIn != "V")
				{
					if($rsFields[$intFields]['isrequired'] == '1') $strRequiredStar="<font color='#FF0000'>*</font>";
					else $strRequiredStar="";
				}

				if(!$blnCustom) echo "<tr><td align='right' width='20%' height='24' valign='top'>".$strRequiredStar.$rsFields[$intFields]['field_title'].":</td><td align='left' valign='top'>";
				else
				{
					$strTempFieldName=$strRequiredStar.$rsFields[$intFields]['field_title'].":";
					$arrCustomHtml["LBL_".$strFieldName] = $strTempFieldName;
				}

			}
			$arrType=explode(":",$rsFields[$intFields]['addedit_field_html_type']);
			if(count($arrType)>1)
			{
				if(strtoupper($this->chrShowIn) == "E"  || strtoupper($this->chrShowIn) == "V") $strHtmltype=$arrType[1];
				else $strHtmltype=$arrType[0];
			}
			else
				$strHtmltype=$rsFields[$intFields]['addedit_field_html_type'];

			
			$arrHtmlControl="";
			$this->addArray($arrHtmlControl,"type",strtolower($strHtmltype));
			$this->addArray($arrHtmlControl,"field_type",$rsFields[$intFields]['add_field_type']);
			$this->addArray($arrHtmlControl,"field_name",$strFieldName);
			$this->addArray($arrHtmlControl,"isrequired",$rsFields[$intFields]['isrequired']);
			$this->addArray($arrHtmlControl,"size",$rsFields[$intFields]['add_field_length_show']);
			$this->addArray($arrHtmlControl,"maxlength",$rsFields[$intFields]['field_length']);
			$this->addArray($arrHtmlControl,"tbl_name",$rsFields[$intFields]['add_html_text']);

			
			if(strtoupper($this->chrShowIn) == "V")
			{
				if(strtoupper($strHtmltype) == "SELECT" || strtoupper($strHtmltype) == "CHECKBOX")
					$this->addArray($arrHtmlControl,"event","disabled");
				else
					$this->addArray($arrHtmlControl,"event","readonly");

			}
			else
			{
				$this->addArray($arrHtmlControl,"event",$rsFields[$intFields]['addedit_event']);
			}

			$this->addArray($arrHtmlControl,"property",$rsFields[$intFields]['add_extra_property']);
			$this->addArray($arrHtmlControl,"rs",$rsPatientInfo);

			$strName = $this->generateHtmlControlName(strtolower($strHtmltype),$rsFields[$intFields]['isrequired'],$rsFields[$intFields]['add_field_type'],$strFieldName,$arrHtmlControl); 
			if(strtoupper($rsFields[$intFields]['field_type'])=="DATE")
			{

				
				if($this->getRequest($strName) != "") $strValue=$this->getRequest($strName);
				else if($rsPatientInfo[0][$strFieldName]!="")
				{
					if(strtoupper($rsFields[$intFields]['addedit_field_html_type']) == ":LABEL")
					{
						$strFormat = $this->objGeneral->getSettings("DATE_FORMAT");
						if($rsPatientInfo[0][$strFieldName]!='0000-00-00')
							$strValue=@date($strFormat,@strtotime($rsPatientInfo[0][$strFieldName]));
						else
							$strValue="";
					}
					else
					{
						if($rsPatientInfo[0][$strFieldName]!='0000-00-00')
							$strValue=@date("m-d-Y",@strtotime($rsPatientInfo[0][$strFieldName]));
						else
							$strValue="";
					}
				}
				else
				{
					$strValue="";
				}
			}
			else
				if($this->getRequest($strName) != "") $strValue=$this->getRequest($strName);
				elseif(strtoupper($rsFields[$intFields]['addedit_field_html_type'])=='SAVEHIDDEN' || strtoupper($rsFields[$intFields]['addedit_field_html_type'])=='HIDDEN' || strtoupper($rsFields[$intFields]['addedit_field_html_type'])=='TEXT' )
				{
					if (isset($rsPatientInfo[0][$strFieldName]))
							$strValue = $rsPatientInfo[0][$strFieldName];
					else
					{
							$strValue=$rsFields[$intFields]['add_html_text'];
							@eval("\$strValue = $strValue;");
					}
				}
				else $strValue=$rsPatientInfo[0][$strFieldName];

			$this->addArray($arrHtmlControl,"value",str_replace("\"","&quot;",$strValue));

			
			if(strtoupper($this->chrShowIn) != "V")
			{
				if($rsFields[$intFields]['ishidden'] <> 1 && $blnFocus==false && strtoupper($strHtmltype)=="TEXT" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="HIDDEN" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="SAVEHIDDEN")
				{
					$strFocus="<script>document.getElementById('".$strName."').focus();</script>";
					$blnFocus=true;
				}
				else $strFocus="";
			}

			
			if(!$blnCustom)
			{
				if($rsFields[$intFields]['ishidden'] <> 1)
				{
					if(strtoupper($rsFields[$intFields]['addedit_field_html_type']) == "FILE" && strtoupper($this->chrShowIn) == "V")
						echo $strValue;
					else
						echo  $this->generateHtmlControl($arrHtmlControl);

					if(strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="HIDDEN" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="SAVEHIDDEN" && strtoupper($strHtmltype)!='LABEL')
					{
						echo  "<script> var LBL_".$strName."=\"".$rsFields[$intFields]['field_title']."\";</script>".$strFocus;
						echo "</td></tr>";
					}
				}
			}
			else
			{
				if($rsFields[$intFields]['ishidden'] <> 1)
				{
					if(strtoupper($rsFields[$intFields]['addedit_field_html_type']) == "FILE" && strtoupper($this->chrShowIn) == "V")
						$arrCustomHtml["str".$strFieldName] = $strValue;
					else
						$arrCustomHtml["str".$strFieldName] = $this->generateHtmlControl($arrHtmlControl);

					if(strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="HIDDEN" && strtoupper($rsFields[$intFields]['addedit_field_html_type'])!="SAVEHIDDEN" && strtoupper($strHtmltype)!='LABEL')
					{
						$arrCustomHtml["str".$strFieldName].="<script> var LBL_".$strName."=\"".$rsFields[$intFields]['field_title']."\";</script>".$strFocus;
					}
				}
			}
		}


		if(!$blnCustom)
		{
			if(strtoupper($this->chrShowIn) != "V")
			{
				if($blnButton)
				{
					
					$this->addArray($arrHtmlControl,"type","submit");
					$this->addArray($arrHtmlControl,"field_name","submit");
					if($this->chrShowIn == "E") $this->addArray($arrHtmlControl,"value","Update");
					else $this->addArray($arrHtmlControl,"value","Submit");
					$this->addArray($arrHtmlControl,"event","onclick='return submit_form(this.form);' class='btn'");
					
					$strSubmit = $this->generateHtmlControl($arrHtmlControl);

					echo "<tr><td></td><td align='left'>".$strSubmit."</td></tr>";
				}
			}

			echo "</table>";
			if($rsFields[0]['fixed_title']!='')	echo  "<script src=\"".$this->objGeneral->getSettings('JS_PATH')."med_".$rsFields[0]['fixed_title'].".js\"></script>";
			$strBuffer = ob_get_contents();
			ob_end_clean();
			return $strBuffer;
		}
		else
		{
			return $arrCustomHtml;
		}

	}

	
	function addArray(&$arrName,$intKey,$strValue)
	{
		$arrName[$intKey] = $strValue; 
	}

	
	function generateHtmlControlName($strType,$intIsrequired,$strField_type,$strField_name,$arrHtmlControl=NULL)
	{
		switch(strtoupper($strType))
		{
			
			case "TEXT":
							if($intIsrequired == 1) $strName = $strField_type."Rtxt_".$strField_name;
							else $strName = $strField_type."txt_".$strField_name;
							break;
			case "PASSWORD":
							if($intIsrequired == 1) $strName = $strField_type."Rpas_".$strField_name;
							else $strName = $strField_type."pas_".$strField_name;
							break;
			case "TEXTAREA":
							if($intIsrequired == 1) $strName = $strField_type."Rara_".$strField_name;
							else $strName = $strField_type."ara_".$strField_name;
							break;
		  	case "HIDDEN":
		  					$strName = "hid_".$strField_name;
							break;
			case "SAVEHIDDEN":
		  					$strName = "hidin_".$strField_name;
							break;
		  	case "CHECKBOX":
							if($intIsrequired == 1) $strName = "Rchk_".$strField_name;
							else $strName = $strField_type."chk_".$strField_name;
							break;
		  	case "RADIO":
		  					if($intIsrequired == 1) $strName = "Rrad_".$strField_name;
							else $strName = $strField_type."rad_".$strField_name;
							break;
		  	case "SELECT":
							if($intIsrequired == 1) $strName = "Rslt_".$strField_name;
							else $strName = $strField_type."slt_".$strField_name;
							break;
		  	case "FILE":
							$arrPara=explode(":",$arrHtmlControl['tbl_name']);
							$strFileType=strtoupper($arrPara[1]);

							if($strFileType=='IMG')
								$strFileTypeName="img";
							elseif($strFileType=='DOC')
								$strFileTypeName="doc";
							elseif($strFileType=='VID')
								$strFileTypeName="vid";
							else
								$strFileTypeName="all";

							if($intIsrequired == 1) $strName = $strField_type."Rfil".$strFileTypeName."_".$strField_name;
							else $strName = $strField_type."fil".$strFileTypeName."_".$strField_name;
							break;
			case "BUTTON":
							$strName = "btn_".$strField_name;
		 					break;
			case "SUBMIT":
							$strName = "smt_".$strField_name;
		 					break;
			case "IMAGE":
							$strName = "img_".$strField_name;
		 					break;
		}
		return $strName;
	}

	
	function generateHtmlControl($arrHtmlControl,$blnSearchField=false)
	{
		$strName = $this->generateHtmlControlName($arrHtmlControl['type'],$arrHtmlControl['isrequired'],$arrHtmlControl['field_type'],$arrHtmlControl['field_name'],$arrHtmlControl); 
		if($blnSearchField)
			$strName = "Sr_".$strName;

		if($arrHtmlControl['jMessVar']!='')
			$strHtmlControl .="<script> var LBL_".$strName."= '".$arrHtmlControl['jMessVar']."';  </script>";

		switch(strtoupper($arrHtmlControl['type']))
		{
		 	case "TEXT":
							if(strtoupper($arrHtmlControl['field_type'])=="DT") $strHtmlControl .="<table cellpadding='0' cellspacing='0'><tr><td valign='middle'>";

							 $strHtmlControl .="<input type='".$arrHtmlControl['type']."' name='".$strName."' id='".$strName."' value=\"".$arrHtmlControl['value']."\"";
							 if(!empty($arrHtmlControl['size'])) $strHtmlControl .=" size=".$arrHtmlControl['size']." ";
							 if(!empty($arrHtmlControl['maxlength'])) $strHtmlControl .=" maxlength=".$arrHtmlControl['maxlength']." ";
							 if(!empty($arrHtmlControl['class'])) $strHtmlControl .=" class=".$arrHtmlControl['class']." ";
							 if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
							 if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
							 $strHtmlControl .=">";
							 
							 if(strtoupper($arrHtmlControl['field_type'])=="DT")
							 {
								$strHtmlControl .="</td><td valign='middle'><img src='".$this->objGeneral->getSettings('ROOT_JS_PATH')."jscalendar/calender_small.gif' height='14' width='19' name='clt_".$strName."' align='absmiddle'  vspace='3' style='cursor:pointer;' id='clt_".$strName."'  hspace='3'/>&nbsp;(mm-dd-yyyy)</td></tr></table>";
								$strHtmlControl .="<script language=\"javascript1.2\">
												  Calendar.setup({inputField : \"".$strName."\",
															ifFormat : \"%m-%d-%Y\",
															button : \"clt_".$strName."\" });
												  </script>";
							}
							break;
			case "PASSWORD": 
							 $strHtmlControl .="<input type='".$arrHtmlControl['type']."' name='".$strName."' id='".$strName."' value=\"".$arrHtmlControl['value']."\"";
							 if(!empty($arrHtmlControl['size'])) $strHtmlControl .=" size=".$arrHtmlControl['size']." ";
							 if(!empty($arrHtmlControl['maxlength'])) $strHtmlControl .=" maxlength=".$arrHtmlControl['maxlength']." ";
							 if(!empty($arrHtmlControl['class'])) $strHtmlControl .=" class='".$arrHtmlControl['class']."' ";
							 if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
							 if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
							 $strHtmlControl .=">";
							 break;
			case "TEXTAREA":
							$arrProperties=explode(":",$arrHtmlControl['tbl_name']);
							if(count($arrProperties)>0)
							{
								if($arrProperties[0]=='E')
								{
									
									$strHtmlControl .="<script type=\"text/javascript\">
													window.onload = function()
													{
														var sBasePath = \"".$this->objGeneral->getSettings('ROOT_JS_PATH')."fckeditor/\" ;
														var oFCKeditor = new FCKeditor('".$strName."') ;
														oFCKeditor.BasePath	= sBasePath ;
														oFCKeditor.ToolbarSet = \"editor-toolbar1\" ;
														oFCKeditor.Width	= \"".$arrProperties[2]."%\" ;
														oFCKeditor.Height	= ".$arrProperties[1]." ;
														oFCKeditor.ReplaceTextarea() ;
													}
													</script>";
								}
								$intCols=$arrProperties[1];
								$intRows=$arrProperties[2];
							}
							$strTAValue=$arrHtmlControl['value'];
							$strTAValue=str_replace("&quot;","\"",$strTAValue);
							$strHtmlControl .="<textarea cols=".$intCols." rows=".$intRows." id='".$strName."' name='".$strName."'";
							if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
							if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
							$strHtmlControl .=">".$strTAValue."</textarea>";
							break;
			case "HIDDEN":
							$strHtmlControl .="<input type='".$arrHtmlControl['type']."' name='".$strName."' id='".$strName."' value=\"".$arrHtmlControl['value']."\">";
							break;
			case "SAVEHIDDEN":
							$strHtmlControl .="<input type='hidden' name='".$strName."' id='".$strName."' value=\"".$arrHtmlControl['value']."\">";
							break;
			case "CHECKBOX":
							$strHtmlControl .="<input type='".$arrHtmlControl['type']."' name='".$strName."' id='".$strName."' value='Yes'";
							if($arrHtmlControl['value'] == "Yes") $strHtmlControl .="checked ";
							if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
							 if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
							$strHtmlControl .=">";
							break;
			case "RADIO": 
						    if($arrHtmlControl['tbl_name'] <> "") $strHtmlControl .=$this->generateRadioButton($arrHtmlControl['tbl_name'],$strName,$arrHtmlControl['value'],$arrHtmlControl['event']);
							else
							{
								$strHtmlControl .="<input type='".$arrHtmlControl['type']."' name='".$strName."' id='".$strName."' value='".$arrHtmlControl['value']."'";
								if($arrHtmlControl['value'] == "Yes") $strHtmlControl .="checked ";
								if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
								if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
								$strHtmlControl .=">";
							}
					     	break;
			case "SELECT": 
							$strMulti=false;
							if(strtoupper($arrHtmlControl['field_type'])=="ML")
								$strMulti=true;
							if($arrHtmlControl['event'] == "") $arrHtmlControl['event'] = $arrHtmlControl['property'];
							$strHtmlControl .= $this->generateCombobox($arrHtmlControl['tbl_name'],$strName,$arrHtmlControl['value'],$arrHtmlControl['event'],$strMulti);
							break;
			case "FILE": 
						    $strHtmlControl .="<table border='0' cellpadding='0' cellspacing='0' width='100%' align='left'><tr><td align='left' width='15%'>";
							$strHtmlControl .="<input type='".$arrHtmlControl['type']."' name='".$strName."' id='".$strName."'";
							if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
							if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
							$strHtmlControl .="><input type='hidden' name='hid_".$arrHtmlControl['field_name']."' value='".$arrHtmlControl['tbl_name']."'></td>";
							if($arrHtmlControl['rs'][0][$arrHtmlControl['field_name']]!="")
							{
								$strLarge="";

								$strFile=$arrHtmlControl['rs'][0][$arrHtmlControl['field_name']];
								$strFilePara=explode(":",$arrHtmlControl['tbl_name']);
								if($strFilePara[5]!='' && $arrHtmlControl['rs'][0][$strFilePara[5]]!='')
								{
									$strOFileName=$arrHtmlControl['rs'][0][$strFilePara[5]];
								}
								else
									$strOFileName=$strFile;

								if(strtoupper($strFilePara[1])=='IMG')
									$strLarge="large\\";

								if(strtoupper($strFilePara[1])!='IMG' && MedGeneral::getSettings($strFilePara[2])!='' && file_exists(MedGeneral::getSettings($strFilePara[2]).$strLarge.$strFile))
								{
									$strFilePath=MedGeneral::getSettings($strFilePara[2])."large/".$strFile;
									$strImgType=$arrHtmlControl['tbl_name'].":".$strFile.":".$arrHtmlControl['field_name'].":".$strOFileName;
									$strHtmlControl .="<td align='left'><div id='div_".$arrHtmlControl['field_name']."'>&nbsp;<a href='index.php?file=med_imgpopup&imgtype=".$strImgType."')\">".$strOFileName."</a>";
									$strHtmlControl .="&nbsp;|&nbsp;<a href='javascript:void(0)' onclick=\"popup('index.php?file=med_remove_file&imgtype=".$strImgType."','200','200')\">Remove</a></div></td>";
								}
								elseif(strtoupper($strFilePara[1])=='IMG' && MedGeneral::getSettings($strFilePara[2])!='' && file_exists(MedGeneral::getSettings($strFilePara[2]).$strLarge.$strFile))
								{
									$strFilePath=MedGeneral::getSettings($strFilePara[2])."large/".$strFile;
									$strImgType=$arrHtmlControl['tbl_name'].":".$strFile.":".$arrHtmlControl['field_name'].":".$strOFileName;
									$strHtmlControl .="<td align='left'><div id='div_".$arrHtmlControl['field_name']."'>&nbsp;<a href='javascript:void(0)' onclick=\"popup('index.php?file=med_imgpopup&imgtype=".$strImgType."','800','600')\">".$strOFileName."</a>";
									$strHtmlControl .="&nbsp;|&nbsp;<a href='javascript:void(0)' onclick=\"popup('index.php?file=med_remove_file&imgtype=".$strImgType."','200','200')\">Remove</a></div></td>";
								}
							}
							$strHtmlControl .="</tr><tr><td><font color='red' size='1px;'>Max file size ".ini_get('upload_max_filesize')."B</font></td></tr></table>";
							break;
			case "BUTTON": 
							$strHtmlControl .="<input type='".$arrHtmlControl['type']."' name='".$strName."'";
							if(!empty($arrHtmlControl['value'])) $strHtmlControl .="value='".$arrHtmlControl['value']."'";
							if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
							if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
							$strHtmlControl .= ">";
							break;
			case "SUBMIT":  
							$strHtmlControl .="<input type='".$arrHtmlControl['type']."' name='".$strName."' value='".$arrHtmlControl['value']."'";
							if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
							if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
							$strHtmlControl .= ">";
							break;
	  		case "IMAGE":  
						    $strHtmlControl .="<input type='".$arrHtmlControl['type']."' title='image' alt='image'  name='".$strName."' src='".$arrHtmlControl['tbl_name']."'";
							if(!empty($arrHtmlControl['event'])) $strHtmlControl .= " ".$arrHtmlControl['event']." ";
							if(!empty($arrHtmlControl['property'])) $strHtmlControl .= " ".$arrHtmlControl['property']." ";
							$strHtmlControl .= ">";
							break;
	  		case "LABEL":  
						    
							if($arrHtmlControl['tbl_name'] != "")
							{
								$strHtmlControl .= $this->generateConditionalItem($arrHtmlControl['tbl_name'],$arrHtmlControl['value']);
							}
							else $strHtmlControl .= $arrHtmlControl['value'];
							break;

		}
		return $strHtmlControl; 
	}

	
	function generateSwitchCombo($strTableName)
	{
		global $IMAGE_PATH;
		switch(strtoupper($strTableName))
		{

			case "SEQUENCE_NO_MM":
									for($intSeq=0;$intSeq<=59;$intSeq++)
									{
										$this->addArray($arrOptionData,$intSeq,$intSeq);
									}
									break;
			case "SEQUENCE_NO_HH":
									for($intSeq=0;$intSeq<=23;$intSeq++)
									{
										$this->addArray($arrOptionData,$intSeq,$intSeq);
									}
									break;

			default :
					$arrOptionData = $this->getOptions(strtoupper($strTableName));

		}
		return $arrOptionData;
	}

	
	function getDbTableNames()
	{
		$strQuery="show tables from `meditab_server`";
		$rsTables=$this->executeSelect($strQuery);
	}
	
	function getOptions($strCase)
	{
		global $IMAGE_PATH;

		$objDB = MedDB::getDBObject();

		
		
		$arrCase	=	explode(":",$strCase);
		$strCase	=	$arrCase[0];

		
		$strQuery = "SELECT * FROM ".$this->pre."combo_master WHERE case_name ='".$strCase."'";
		
		$rsCase = $objDB->executeSelect($strQuery);
		
		$intComboId = $rsCase[0]["combo_id"];

		$arrOptionData=array();

		
		if(!(isset($arrCase[1]) && ($arrCase[1]=='N')))
		{
			
			if($arrCase[1]=='SL')
			{

				if($arrCase[2]=='NT')
					$strJoinWhere=" AND `key` not in (".strtolower($arrCase[3]).")";
				elseif($arrCase[2]=='N')
					$strJoinWhere=" AND `key` in (".strtolower($arrCase[3]).")";
			}
			
			$strQuery = "SELECT * FROM ".$this->pre."combo_detail WHERE combo_id ='".$intComboId."' ".$strJoinWhere." order by seq_no ";
			
			$rsDataStatic = $objDB->executeSelect($strQuery);

			
			for($intData=0;$intData<count($rsDataStatic);$intData++)
			{
				$strPosition = $rsDataStatic[$intData]["position"];
				if(empty($strPosition))
				{
					$strKey =$rsDataStatic[$intData]["key"];
					$strValue = $rsDataStatic[$intData]["key_value"];
					if (!empty($strValue))	@eval("\$strValue = \"$strValue\";");
					$this->addArray($arrOptionData,$strKey,$strValue);
				}
			}
		}

		
		if (!empty($rsCase[0]["query"]))
		{
			
			$strKeyCol = $rsCase[0]["key_column"];
			
			$strValueCol = $rsCase[0]["value_column"];

			
			$strQuery = $rsCase[0]["query"];
			
			if (!empty($strQuery))	@eval("\$strQuery = \"$strQuery\";");
			
			
			$rsData = $objDB->executeSelect($strQuery);
			
			
			for($intData=0;$intData<count($rsData);$intData++)
			{
				$this->addArray($arrOptionData,$rsData[$intData][$strKeyCol],$rsData[$intData][$strValueCol]);
			}
		}
		if(!(isset($arrCase[1]) && $arrCase[1]=='N'))
		{
			
			for($intData=0;$intData<count($rsDataStatic);$intData++)
			{
				$strPosition = $rsDataStatic[$intData]["position"];

				if($strPosition == "A")
				{
					$strKey =$rsDataStatic[$intData]["key"];
					$strValue = $rsDataStatic[$intData]["key_value"];
					if (!empty($strValue))	@eval("\$strValue = \"$strValue\";");
					$this->addArray($arrOptionData,$strKey,$strValue);
				}
			}
		}

		
		return $arrOptionData;
	}

	
	function generateCombobox($strTableName,$strName,$strSelectedValue,$strEvent=NULL,$strType=false) 
	{
		
		
		global $objDb;
		$strOptValue = "";
		$arrOptionData=$this->generateSwitchCombo($strTableName);

		
		if($strType == true)
		{
			$strHtmlSelect="<select name='".$strName."[]' id='".$strName."[]' multiple=\"multiple\" size=\"4\"".$strEvent.">";
			
			$strOptions=$this->fillCombobox($arrOptionData,$strSelectedValue,true,$strName);
		}
		else
		{
			if($strEvent != NULL) $strHtmlSelect="<select name='".$strName."' id='".$strName."' ".$strEvent.">";
			else $strHtmlSelect="<select name='".$strName."' id='".$strName."'>";

			
			$strOptions=$this->fillCombobox($arrOptionData,$strSelectedValue,false,$strName);

		}
		$strHtmlSelect.=$strOptions;
		$strHtmlSelect.="</select>";

		
		return $strHtmlSelect;
	}
	
	function generateConditionalItem($strTableName,$strSelectedValue)
	{
		$arrOptionData=$this->generateSwitchCombo($strTableName);
		if(!empty($arrOptionData))
		{
			foreach ($arrOptionData as $keys=>$value)
			{
				if($keys == $strSelectedValue)
				{
					return $value;
				}
			}
		}
		else
		{
			return $strSelectedValue;
		}
	}

	
	function fillCombobox($arrOptionData,$strSelectedValue,$strType=false,$strControlName)
	{
		if(count($arrOptionData) > 0)
		{
			$blnSelMatch = false;
			$intCntRec=0;

			$arrKey=array_keys($arrOptionData);

			if(is_array($strSelectedValue))
				$arrSelectedValue=$strSelectedValue;
			else
				$arrSelectedValue=explode(",",$strSelectedValue);


			$intMatch=count(array_diff($arrSelectedValue, $arrKey));

			if($intMatch>0)
			{
				$strSelectedValue	=	$arrKey[0];
				$this->setRequest($strControlName,$arrKey[0]);

			}

			foreach($arrOptionData as $keys => $value) 
			{
				if($strType == true)
				{
					if(is_array($strSelectedValue)) $arrSelectedValue = $strSelectedValue;
					else $arrSelectedValue = explode(",",$strSelectedValue);
					if(in_array($keys,$arrSelectedValue)) $strSel="selected='selected'";
					else $strSel="";
				}
				else
				{
					
					if($strSelectedValue == $keys) $strSel="selected='selected'";
					else $strSel="";
				}
				$strOptions.="<option ".$strSel." value='".$keys."'>".$value."</option>";
			}
		}
		
		return $strOptions;
	}

	
	function generateRadioButton($strTableName,$strName,$strSelectedValue,$strEvent=NULL) 
	{
		global $objDb;
		$strOptValue = "";
		$arrOptionData=$this->generateSwitchCombo($strTableName);
		foreach($arrOptionData as $keys => $value) 
		{
			
			if($strSelectedValue == $keys) $strchk="checked='checked'";
			else $strchk="";
			$strHtmlSelect.="&nbsp;<input type='radio' name='".$strName."' id='".$strName."' value='".$keys."' ".$strchk." ".$strEvent.">&nbsp;".$value;
		}
		
		return $strHtmlSelect;
	}

	
	function getTableKeyCol($intTableId)
	{
		
		global $objDb;
		$strTbl_Name	=$this->pre."tbl_table";
		$strField_Names	=" add_key_col,edit_key_col,delete_key_col";
		$strWhere		="table_id = ".$intTableId;

		$strsql=$this->buildSelect($strTbl_Name,$strField_Names,$strWhere,"","","","");
		$rsFields = $objDb->executeSelect($strsql);
		return $rsFields;
	}

	
	function getPageTitleByDb($intTableId)
	{
		if(trim($this->strListPageTitle) != NULL) return $this->strListPageTitle;
		else
		{
			
			$strTbl_Name	=$this->pre."tbl_table";
			$strField_Names	=" page_title";
			$strWhere		="table_id = ".$intTableId;

			$rsFields = $this->getRecords($strTbl_Name,$strField_Names,$strWhere,"","","","");

			if(count($rsFields)>0)	return $rsFields[0]['page_title'];
			else return NULL;
		}
	}

	
	function buildSelect($strTblName, $strFieldNnames, $strWhere, $strGroupBby, $strHaving,$strOrderBy, $strLimit)
	{
		$strSql="SELECT ";

		if(empty($strFieldNnames))
			$strSql.=" * ";
		else
			$strSql.=" $strFieldNnames ";

		if(!empty($strTblName)) $strSql.=" FROM  $strTblName ";

		if(empty($strWhere))
			$strSql.="";
		else
			$strSql.=" where $strWhere ";

		if(empty($strGroupBby))
			$strSql.="";
		else
			$strSql.=" GROUP BY $strGroupBby";

		if(empty($strHaving))
			$strSql.="";
		else
			$strSql.=" having $strHaving ";

		if(empty($strOrderBy))
			$strSql.="";
		else
			$strSql.=" ORDER BY $strOrderBy";


		if(empty($strLimit))
			$strSql.="";
		else
			$strSql.=" $strLimit ";

		return $strSql;
	}

	
	function getRecords($strTblName, $strFieldNnames, $strWhere, $strGroupBby, $strHaving,$strOrderBy, $strLimit)
	{
		$objDb= MedDB::getDBObject();
		$strSql = MedPage::buildSelect($strTblName, $strFieldNnames, $strWhere, $strGroupBby, $strHaving,$strOrderBy, $strLimit);
		return $objDb->executeSelect($strSql);
	}

	
	function executeSelect($strQuery)
	{
		$objDb= MedDB::getDBObject();
		return $objDb->executeSelect($strQuery);
	}

	
	function getSelectedPKValues($strListId)
	{
		if (isset($_POST[$strListId."_cSlcPK"]) && $_POST[$strListId."_cSlcPK"])
			{
				return $_POST[$strListId."_cSlcPK"];
			}
			else return null;
	}
	
	function setTable($intTableId)
	{
		$this->intTableId=$intTableId;
	}

	
	function getTable()
	{
		return $this->intTableId;
	}

	
	function setPageType($chrShowIn)
	{
		$this->chrShowIn=$chrShowIn;
	}

	
	function getPageType()
	{
		if($this->chrShowIn == "V")
			return $chrShowIn = "E";
		else
			return $this->chrShowIn;
	}
	
	function setPageTitle($strPageTitle)
	{
		$this->strPageTitle=$strPageTitle;
	}

	
	function getPageTitle()
	{
		return $this->strPageTitle;
	}

	
	function setListCount($intListCount)
	{
		$this->intListCount = $intListCount;
	}
	
	function getListCount()
	{
		return $this->intListCount;
	}
	
	function setListCountVar()
	{
		$this->blnGetListCount=true;
	}

	
	function validateMessage($arrFieldName,$intTableId)
	{
		$strMessage="";

		$rsPageTitle=$this->getFieldTitleByIdnName($arrFieldName,$intTableId);

		for($intFieldTitle=0; $intFieldTitle<count($rsPageTitle); $intFieldTitle++)
		{
			$strMessage .= $rsPageTitle[$intFieldTitle]["field_title"];
			$strMessage .= " ".$this->objGeneral->getSiteMessage("REC_EXIST_MSG")."<br>";
		}

		$strMessage=substr($strMessage,0,(strlen($strMessage)-4)); 
		$this->objGeneral->setMessage($strMessage);
	}

	
	function getFieldTitleByIdnName($arrFieldName,$intTableId)
	{
		$strTblName = $this->pre."tbl_fields";
		$strFieldNames = "field_title";
		$strWhere = "table_id='".$intTableId."' and field_name in (";

		for($intFieldName=0; $intFieldName<count($arrFieldName); $intFieldName++)
		{
			$strWhere .= "'".$arrFieldName[$intFieldName]."',";
		}

		$strWhere=substr($strWhere,0,(strlen($strWhere)-1)); 
		$strWhere .= ")";

		$rsSetting= $this->getRecords($strTblName,$strFieldNames,$strWhere,"","","",""); 
		return $rsSetting;
	}


	
	function getTextBox($strFieldType,$strFieldName,$strJMessVar="",$strProperty="",$strValue="",$blnIsRequired=0,$strEvent="",$blnDtShowLable=true)
	{
		
		$arrTextControl="";

		
		$this->addArray($arrTextControl,"field_type",$strFieldType);
		$this->addArray($arrTextControl,"field_name",$strFieldName);
		$this->addArray($arrTextControl,"jMessVar",$strJMessVar);
		$this->addArray($arrTextControl,"property",$strProperty);
		$this->addArray($arrTextControl,"value",str_replace("\"","&quot;",$strValue));
		$this->addArray($arrTextControl,"isrequired",$blnIsRequired);
		$this->addArray($arrTextControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('text',$arrTextControl['isrequired'],$arrTextControl['field_type'],$arrTextControl['field_name']); 

		if(strtoupper($arrTextControl['field_type'])=="DT") $strTextControl .="<table cellpadding='0' cellspacing='0'><tr><td valign='middle'>";

		if($arrTextControl['jMessVar']!='')
			$strTextControl .="<script> var LBL_".$strName."= '".$arrTextControl['jMessVar']."';  </script>";

		$strTextControl .="<input type='text' name='".$strName."' id='".$strName."' value=\"".$arrTextControl['value']."\"";

		if(!empty($arrTextControl['event'])) $strTextControl .= " ".$arrTextControl['event']." ";
		if(!empty($arrTextControl['property'])) $strTextControl .= " ".$arrTextControl['property']." ";

		$strTextControl .=">";

		
		if(substr($strName,0,2) == "Dt")
		{
			$strTextControl .="</td><td valign='middle'><img src='".$this->objGeneral->getSettings('ROOT_JS_PATH')."jscalendar/calender_small.gif' height='14' width='19' name='clt_".$strName."' align='absmiddle'  vspace='3' style='cursor:pointer;' id='clt_".$strName."'  hspace='3'/>&nbsp;";
			if($blnDtShowLable)	$strTextControl .="(mm-dd-yyyy)</td></tr></table>";
			else $strTextControl .="</td></tr></table>";
			$strTextControl .="<script language=\"javascript1.2\">
								Calendar.setup({inputField : \"".$strName."\",
												ifFormat : \"%m-%d-%Y\",
												button : \"clt_".$strName."\" });
								</script>";
		}
		return $strTextControl;
	}


	
	function getPasswordTextBox($strFieldType,$strFieldName,$strJMessVar="",$strProperty="",$blnIsRequired=0)
	{
		
		$arrPasswordTextControl="";

		
		$this->addArray($arrPasswordTextControl,"field_type",$strFieldType);
		$this->addArray($arrPasswordTextControl,"field_name",$strFieldName);
		$this->addArray($arrPasswordTextControl,"jMessVar",$strJMessVar);
		$this->addArray($arrPasswordTextControl,"property",$strProperty);
		$this->addArray($arrPasswordTextControl,"isrequired",$blnIsRequired);

		$strName = $this->generateHtmlControlName('password',$arrPasswordTextControl['isrequired'],$arrPasswordTextControl['field_type'],$arrPasswordTextControl['field_name']); 

		if($arrPasswordTextControl['jMessVar']!='')
			$strPasswordTextControl .="<script> var LBL_".$strName."= '".$arrPasswordTextControl['jMessVar']."';  </script>";

		$strPasswordTextControl .="<input type='password' name='".$strName."' id='".$strName."'";

		if(!empty($arrPasswordTextControl['property'])) $strPasswordTextControl .= " ".$arrPasswordTextControl['property']." ";

		$strPasswordTextControl .=">";

		return $strPasswordTextControl;
	}


	
	function getCheckBox($strFieldName,$strJMessVar="",$strProperty="",$strValue="",$blnIsRequired=0,$strEvent="")
	{
		
		$arrCheckBoxControl="";

		
		$this->addArray($arrCheckBoxControl,"field_name",$strFieldName);
		$this->addArray($arrCheckBoxControl,"jMessVar",$strJMessVar);
		$this->addArray($arrCheckBoxControl,"property",$strProperty);
		$this->addArray($arrCheckBoxControl,"value",$strValue);
		$this->addArray($arrCheckBoxControl,"isrequired",$blnIsRequired);
		$this->addArray($arrCheckBoxControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('checkbox',$arrCheckBoxControl['isrequired'],"",$arrCheckBoxControl['field_name']); 

		if($arrCheckBoxControl['jMessVar']!='')
			$strCheckBoxControl .="<script> var LBL_".$strName."= '".$arrCheckBoxControl['jMessVar']."';  </script>";

		$strCheckBoxControl .="<input type='checkbox' name='".$strName."' id='".$strName."' value='Yes'";

		if($arrCheckBoxControl['value'] == "Yes") $strCheckBoxControl .="checked ";
		if(!empty($arrCheckBoxControl['event'])) $strCheckBoxControl .= " ".$arrCheckBoxControl['event']." ";
		if(!empty($arrCheckBoxControl['property'])) $strCheckBoxControl .= " ".$arrCheckBoxControl['property']." ";
		$strCheckBoxControl .=">";

		return $strCheckBoxControl;
	}


	
	function getFileControl($strFieldType,$strFieldName,$strJMessVar="",$strProperty="",$blnIsRequired=0,$strEvent="")
	{
		
		$arrFileControl="";

		
		$this->addArray($arrFileControl,"field_type",$strFieldType);
		$this->addArray($arrFileControl,"field_name",$strFieldName);
		$this->addArray($arrFileControl,"jMessVar",$strJMessVar);
		$this->addArray($arrFileControl,"property",$strProperty);
		$this->addArray($arrFileControl,"isrequired",$blnIsRequired);
		$this->addArray($arrFileControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('file',$arrFileControl['isrequired'],$arrFileControl['field_type'],$arrFileControl['field_name']); 

		if($arrFileControl['jMessVar']!='')
			$strFileControl .="<script> var LBL_".$strName."= '".$arrFileControl['jMessVar']."';  </script>";

		$strFileControl .="<input type='file' name='".$strName."' id='".$strName."'";
		if(!empty($arrFileControl['event'])) $strFileControl .= " ".$arrFileControl['event']." ";
		if(!empty($arrFileControl['property'])) $strFileControl .= " ".$arrFileControl['property']." ";
		$strFileControl .=">";

		return $strFileControl;
	}

	
	function getButton($strFieldName,$strProperty="",$strValue="",$blnIsRequired=0,$strEvent="")
	{
		
		$arrButtonControl="";

		
		$this->addArray($arrButtonControl,"field_name",$strFieldName);
		$this->addArray($arrButtonControl,"property",$strProperty);
		$this->addArray($arrButtonControl,"value",$strValue);
		$this->addArray($arrButtonControl,"isrequired",$blnIsRequired);
		$this->addArray($arrButtonControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('button',$arrButtonControl['isrequired'],"",$arrButtonControl['field_name']); 

		$strButtonControl .="<input type='button' name='".$strName."' value='".$arrButtonControl['value']."'";
		if(!empty($arrButtonControl['event'])) $strButtonControl .= " ".$arrButtonControl['event']." ";
		if(!empty($arrButtonControl['property'])) $strButtonControl .= " ".$arrButtonControl['property']." ";
		$strButtonControl .= ">";

		return $strButtonControl;
	}


	
	function getSubmitButton($strFieldName,$strProperty="",$strValue="",$blnIsRequired=0,$strEvent="")
	{
		
		$arrSubmitButtonControl="";

		
		$this->addArray($arrSubmitButtonControl,"field_name",$strFieldName);
		$this->addArray($arrSubmitButtonControl,"jMessVar",$strJMessVar);
		$this->addArray($arrSubmitButtonControl,"property",$strProperty);
		$this->addArray($arrSubmitButtonControl,"value",$strValue);
		$this->addArray($arrSubmitButtonControl,"isrequired",$blnIsRequired);
		$this->addArray($arrSubmitButtonControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('submit',$arrSubmitButtonControl['isrequired'],"",$arrSubmitButtonControl['field_name']); 

		$strSubmitButtonControl .="<input type='submit' name='".$strName."' value='".$arrSubmitButtonControl['value']."'";
		if(!empty($arrSubmitButtonControl['event'])) $strSubmitButtonControl .= " ".$arrSubmitButtonControl['event']." ";
		if(!empty($arrSubmitButtonControl['property'])) $strSubmitButtonControl .= " ".$arrSubmitButtonControl['property']." ";
		$strSubmitButtonControl .= ">";

		return $strSubmitButtonControl;
	}


	
	function getImageButton($strFieldName,$strSrc,$strProperty="",$strValue="",$blnIsRequired=0,$strEvent="")
	{
		
		$arrImageButtonControl="";

		
		$this->addArray($arrImageButtonControl,"field_name",$strFieldName);
		$this->addArray($arrImageButtonControl,"src",$strSrc);
		$this->addArray($arrImageButtonControl,"property",$strProperty);
		$this->addArray($arrImageButtonControl,"value",$strValue);
		$this->addArray($arrImageButtonControl,"isrequired",$blnIsRequired);
		$this->addArray($arrImageButtonControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('image',$arrImageButtonControl['isrequired'],"",$arrImageButtonControl['field_name']); 

	    $strImageButtonControl .="<input type='image' title='image' alt='image'  name='".$strName."' src='".$arrImageButtonControl['src']."'";
		if(!empty($arrImageButtonControl['event'])) $strImageButtonControl .= " ".$arrImageButtonControl['event']." ";
		if(!empty($arrImageButtonControl['property'])) $strImageButtonControl .= " ".$arrImageButtonControl['property']." ";
		$strImageButtonControl .= ">";

		return $strImageButtonControl;
	}

	
	function getImage($strFieldName,$strSrc,$strProperty="")
	{
		if($this->blnExport==false)
		{
			
			$arrImageControl="";

			
			$this->addArray($arrImageControl,"field_name",$strFieldName);
			$this->addArray($arrImageControl,"src",$strSrc);
			$this->addArray($arrImageControl,"property",$strProperty);

			$strName = $this->generateHtmlControlName('image',0,"",$arrImageControl['field_name']); 

			$strImageControl .="<img name='".$strName."' id='".$strName."' src='".$arrImageControl['src']."'";
			if(!empty($arrImageControl['property'])) $strImageControl .= " ".$arrImageControl['property']." ";
			$strImageControl .= ">";

			return $strImageControl;
		}
		else
			return "";
	}

	
	function getHiddenControl($strFieldName,$strValue="")
	{
		
		$arrHiddenControl="";

		
		$this->addArray($arrHiddenControl,"field_name",$strFieldName);
		$this->addArray($arrHiddenControl,"value",$strValue);

		$strName = $this->generateHtmlControlName('hidden',"","",$arrHiddenControl['field_name']); 

		$strHiddenControl .="<input type='hidden' name='".$strName."' id='".$strName."' value=\"".$arrHiddenControl['value']."\">";
		return $strHiddenControl;
	}


	
	function getComboBox($strFieldName,$strJMessVar="",$strCaseCombo="",$strSelectedValue="",$blnIsRequired=0,$strEvent="",$strType=false)
	{
		
		$arrComboBoxControl="";

		
		$this->addArray($arrComboBoxControl,"field_name",$strFieldName);
		$this->addArray($arrComboBoxControl,"jMessVar",$strJMessVar);
		$this->addArray($arrComboBoxControl,"caseCombo",$strCaseCombo);
		$this->addArray($arrComboBoxControl,"value",$strSelectedValue);
		$this->addArray($arrComboBoxControl,"isrequired",$blnIsRequired);
		$this->addArray($arrComboBoxControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('select',$arrComboBoxControl['isrequired'],"",$arrComboBoxControl['field_name']); 

		if($arrComboBoxControl['jMessVar']!='')
			$strComboBoxControl .="<script> var LBL_".$strName."= '".$arrComboBoxControl['jMessVar']."';  </script>";


		$strComboBoxControl .= $this->generateCombobox($arrComboBoxControl['caseCombo'],$strName,$arrComboBoxControl['value'],$arrComboBoxControl['event'],$strType);

		return $strComboBoxControl;
	}

	
	function getRadioButton($strFieldName,$strJMessVar="",$strCaseRadio="",$strProperty="",$strValue="",$blnIsRequired=0,$strEvent="")
	{
		
		$arrRadioControl="";

		
		$this->addArray($arrRadioControl,"field_name",$strFieldName);
		$this->addArray($arrRadioControl,"jMessVar",$strJMessVar);
		$this->addArray($arrRadioControl,"caseRadio",$strCaseRadio);
		$this->addArray($arrRadioControl,"property",$strProperty);
		$this->addArray($arrRadioControl,"value",$strValue);
		$this->addArray($arrRadioControl,"isrequired",$blnIsRequired);
		$this->addArray($arrRadioControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('radio',$arrRadioControl['isrequired'],"",$arrRadioControl['field_name']); 

		if($arrRadioControl['jMessVar']!='')
			$strRadioControl .="<script> var LBL_".$strName."= '".$arrRadioControl['jMessVar']."';  </script>";

		if($arrRadioControl['caseRadio'] <> "") $strRadioControl .=$this->generateRadioButton($arrRadioControl['caseRadio'],$strName,$arrRadioControl['value'],$arrHtmlControl['event']);
		else
		{
			$strRadioControl .="<input type='radio' name='".$strName."' id='".$strName."' value='".$arrRadioControl['value']."'";
			if($arrRadioControl['value'] == "Yes") $strRadioControl .="checked ";
			if(!empty($arrRadioControl['event'])) $strRadioControl .= " ".$arrRadioControl['event']." ";
			if(!empty($arrRadioControl['property'])) $strRadioControl .= " ".$arrRadioControl['property']." ";
			$strRadioControl .=">";
		}
		return $strRadioControl;
	}



	
	function getTextArea($strFieldType,$strFieldName,$strJMessVar="",$strActionRowCols="",$strProperty="",$strValue="",$blnIsRequired=0,$strEvent="")
	{
		
		$arrTextAreaControl="";

		
		$this->addArray($arrTextAreaControl,"field_type",$strFieldType);
		$this->addArray($arrTextAreaControl,"field_name",$strFieldName);
		$this->addArray($arrTextAreaControl,"jMessVar",$strJMessVar);
		$this->addArray($arrTextAreaControl,"strActionRowCols",$strActionRowCols);
		$this->addArray($arrTextAreaControl,"property",$strProperty);
		$this->addArray($arrTextAreaControl,"value",$strValue);
		$this->addArray($arrTextAreaControl,"isrequired",$blnIsRequired);
		$this->addArray($arrTextAreaControl,"event",$strEvent);

		$strName = $this->generateHtmlControlName('textarea',$arrTextAreaControl['isrequired'],$arrTextAreaControl['field_type'],$arrTextAreaControl['field_name']); 

		if($arrTextAreaControl['jMessVar']!='')
			$strTextAreaControl .="<script> var LBL_".$strName."= '".$arrTextAreaControl['jMessVar']."';  </script>";

		$arrProperties=explode(":",$arrTextAreaControl['strActionRowCols']);
		if(count($arrProperties)>0)
		{
			if($arrProperties[0]=='E')
			{
				$strTextAreaControl .="<script type=\"text/javascript\">
									window.onload = function()
									{
										var sBasePath = \"".$this->objGeneral->getSettings('ROOT_JS_PATH')."fckeditor/\" ;
										var oFCKeditor = new FCKeditor('".$strName."') ;
										oFCKeditor.BasePath	= sBasePath ;
										oFCKeditor.ReplaceTextarea() ;
									}
								   </script>";
			}
			$intCols=$arrProperties[1];
			$intRows=$arrProperties[2];
		}
		$strTextAreaControl .="<textarea cols=".$intCols." rows=".$intRows." id='".$strName."' name='".$strName."'";
		if(!empty($arrTextAreaControl['event'])) $strTextAreaControl .= " ".$arrTextAreaControl['event']." ";
		if(!empty($arrTextAreaControl['property'])) $strTextAreaControl .= " ".$arrTextAreaControl['property']." ";
		$strTextAreaControl .=">".$arrTextAreaControl['value']."</textarea>";

		return $strTextAreaControl;
	}

	
	function getTblFieldRs($intFieldId)
	{
		
		$strTblName		=	$this->pre."tbl_fields";
		$strFieldNames	=	"addedit_field_html_type,sql_field,field_name,add_field_type,isrequired,
							add_field_length_show,field_length,
							add_html_text,addedit_event,add_extra_property,table_id";
		$strWhere		=	"id = ".$intFieldId;
		return $rsFields 		= 	$this->getRecords($strTblName, $strFieldNames, $strWhere,"","","","");
	}

	
	function getTblSearchRs($intFieldId)
	{
		
		$strTblName		=	$this->pre."tbl_search";
		$strFieldNames	=	"addedit_field_html_type,field_name,add_field_type,isrequired,add_field_length_show,field_length,
							 add_html_text,add_extra_property as addedit_event,table_id";
		$strWhere		=	"id = ".$intFieldId;
		return $rsFields 		= 	$this->getRecords($strTblName, $strFieldNames, $strWhere,"","","","");
	}

	
	function getHtmlControlandFieldNameByFieldId($strFieldType,$intFieldId)
	{
		
		if(trim(strtoupper($strFieldType))=="F")
			$rsFields=$this->getTblFieldRs($intFieldId);
		else
			$rsFields=$this->getTblSearchRs($intFieldId);

		
		$arrType=explode(":",$rsFields[0]['addedit_field_html_type']);
		if(count($arrType)>1) $strHtmltype=$arrType[0];
		else $strHtmltype=$rsFields[0]['addedit_field_html_type'];

		
		if($rsFields[0]['sql_field']!='') $strFieldName=$rsFields[0]['sql_field'];
		else $strFieldName=$rsFields[0]['field_name'];

		
		if($this->getRequest($strFieldName)=='' || $this->getRequest($strFieldName)==NULL)
			$this->setRequest($strFieldName,$this->objGeneral->getSession($rsFields[0]['table_id'].$strFieldName));

		
		$strValue=$this->getRequest($strFieldName);

		
		$arrHtmlControl="";
		$this->addArray($arrHtmlControl,"type",strtolower($strHtmltype));
		$this->addArray($arrHtmlControl,"field_type",$rsFields[0]['add_field_type']);
		$this->addArray($arrHtmlControl,"field_name",$strFieldName);
		$this->addArray($arrHtmlControl,"isrequired",$rsFields[0]['isrequired']);
		$this->addArray($arrHtmlControl,"size",$rsFields[0]['add_field_length_show']);
		$this->addArray($arrHtmlControl,"maxlength",$rsFields[0]['field_length']);
		$this->addArray($arrHtmlControl,"tbl_name",$rsFields[0]['add_html_text']);
		$this->addArray($arrHtmlControl,"event",$rsFields[0]['addedit_event']);
		$this->addArray($arrHtmlControl,"property",$rsFields[0]['add_extra_property']);
		$this->addArray($arrHtmlControl,"value",$strValue);

		
		if($strFieldType=="F")
			$strHtmlControl	=	$this->generateHtmlControl($arrHtmlControl);
		else
			$strHtmlControl	=	$this->generateHtmlControl($arrHtmlControl,true);

		return array("strHtmlControl"	=>	$strHtmlControl,
					"strFieldName"		=>	$strFieldName);
	}

	

	function getSearchDetailByTableId($intTableId)
	{
		$strTbl_Name	=	$this->pre."tbl_search";
		$strField_Names	=	"id, table_id, field_name, field_referal, field_type, field_length, add_field_length_show, addedit_field_html_type, isrequired, add_field_type, field_name, add_html_text, add_html_text,`condition`,db_field_name, add_extra_property, field_desc,seq_no";
		$strWhere		=	"table_id = ".$intTableId;
		$strOrder_By	=	"id";
		$rsSearch		=	$this->getRecords($strTbl_Name,$strField_Names,$strWhere,"","",$strOrder_By,"");
		return $rsSearch;
	}


	
	function generateSearchWhere($arrSearch)
	{

		for($intSearch = 0; $intSearch < count($arrSearch) ; $intSearch++)
		{
			
			if(strtoupper($arrSearch[$intSearch]['addedit_field_html_type']) != 'HIDDEN' &&  strtoupper($arrSearch[$intSearch]['addedit_field_html_type']) != 'BUTTON')
			{
				
				$strFieldType		=	$arrSearch[$intSearch]['addedit_field_html_type'];

				
				$strSearchFieldName	= 	$this->generateHtmlControlName($strFieldType,$arrSearch[$intSearch]['isrequired'],$arrSearch[$intSearch]['add_field_type'],$arrSearch[$intSearch]['field_name'],"");

				
				$strSearchValue		=	$this->getRequest("Sr_".$strSearchFieldName);

				
				$strCondition		=	$arrSearch[$intSearch]['condition'];

				
				$strFieldName		=	$arrSearch[$intSearch]['db_field_name'];

				if(is_array($strSearchValue))
				{
					if($strSearchValue[0] == '0' || trim(strtoupper($strSearchValue[0])) == 'ALL')	$strCheckValue = 0;
					else	$strCheckValue	=	implode(",",$strSearchValue);
				}
				else	$strCheckValue	=	$strSearchValue;

				
				if(!empty($strSearchValue) && !empty($strCondition) && !empty($strFieldName) && $strCheckValue != '0' && strtoupper($strSearchValue) != 'ALL')
				{
					
					switch(strtoupper($arrSearch[$intSearch]['add_field_type']))
					{
						case "DT":
							$arrSearchValue	=	explode("-",$strSearchValue);
							$strSearchValue	=	$arrSearchValue[2]."-".$arrSearchValue[0]."-".$arrSearchValue[1];
						break;
					}

					$strSearchCond	=	"";

					
					switch(strtolower($strCondition))
					{

						case "like"			:
											$strSearchValue	 =	str_replace(" ","+",trim($strSearchValue));
											$arrSearchValue	 =	explode("+",$strSearchValue);
											for($intSearchValue = 0 ; $intSearchValue < count($arrSearchValue); $intSearchValue++)
											{
												$strSearchCond 	.=	$strFieldName." like '%".$arrSearchValue[$intSearchValue]."%' or ";
											}
											$strSearchCond	 =	substr($strSearchCond,0,strlen($strSearchCond)-3);
											$strSearchWhere	.=	" and (".$strSearchCond.")";
						break;
						case "like_whole_word"			:
											$strSearchWhere	.=	" and (".$strFieldName." like '%".trim($strSearchValue)."%')";
						break;
						case "not like"		:
											$strSearchValue	 =	str_replace(" ","+",trim($strSearchValue));
											$arrSearchValue	 =	explode("+",$strSearchValue);
											for($intSearchValue = 0 ; $intSearchValue < count($arrSearchValue); $intSearchValue++)
											{
												$strSearchCond 	.=	$strFieldName." ".$strCondition.
																	" '%".$arrSearchValue[$intSearchValue]."%' or ";
											}
											$strSearchCond	 =	substr($strSearchCond,0,strlen($strSearchCond)-3);
											$strSearchWhere	.=	" and (".$strSearchCond.")";
						break;
						case "in"			:
											if(!is_array($strSearchValue))	$arrSearchValue	=	explode(",",$strSearchValue);
											else	$arrSearchValue	=	$strSearchValue;
											$strSearchValue	 =	implode("','",$arrSearchValue);
											$strSearchWhere	.=	" and ".$strFieldName." ".$strCondition.
																" ('".$strSearchValue."')";
						break;
						case "not in"		:
											if(!is_array($strSearchValue))	$arrSearchValue	=	explode(",",$strSearchValue);
											else	$arrSearchValue	=	$strSearchValue;
											$strSearchValue	 =	implode("','",$arrSearchValue);
											$strSearchWhere	.=	" and ".$strFieldName." ".$strCondition.
																" ('".$strSearchValue."')";
						break;
						case "find_in_set"	:
											$strSearchWhere	.=	" and ".$strCondition."('".$strSearchValue."',".$strFieldName.")";
						break;
						default				:
											$strSearchWhere	.=	" and ".$strFieldName." ".$strCondition." '".$strSearchValue."'";
						break;
					}
				}
			}
		}
		$strSearchWhere	=	substr($strSearchWhere,4);

		
		return $strSearchWhere;
	}

	
	function getSearchRequest()
	{
		
		$arrRequest = $_REQUEST;
		foreach($arrRequest as $strKey=>$strValue)
		{
			
			if(ereg("Sr_",$strKey))
			{
				if(is_array($strValue))
					$strValue	= @implode(",",$strValue);
				$this->addArray($arrSearchRequest,$strKey,$strValue);
			}
		}

		
		return $arrSearchRequest;
	}

  	
	function restoreSearchResult($arrHiddens,$strAction='')
	{
		echo "
			<!DOCTYPE html PUBLIC '-
			<html xmlns='http:
			<head>
			<meta http-equiv='Content-Type' content='text/html; charset=iso-8859-1' />
			</head>
			<body>";
			if($strAction)
				echo "<form name='frm' action='".$strAction."' method='post'>";
			else
				echo "<form name='frm' action='index.php' method='post'>";
			foreach($arrHiddens as $arrKey => $arrValue)
			{
			  echo "<input type='hidden' name='".$arrKey."' id='".$arrKey."' value='".$arrValue."' />";
			}
		echo "</form>
			</body>
			</html>
			  <script>
				document.frm.submit();
			</script>";
		exit;
	}

	
	function getHtmlAll($intTableId,$strPageType,$blnCustom=false,$blnButton=true,$intMultiId=NULL,$blnGenerateList=true,$blnGenerateSearch=true,$blnSearchExists=true,$strExtSearch=NULL)
	{
		
		$strTitle			=	$this->getPageTitleByDb($intTableId);
		$arrAllHtmlControls	=	array("strTitle"=>$strTitle);

		
		if($blnGenerateSearch)
		{
			$arrSearchControls	=	$this->generateSearch($intTableId,$strSearchWhere);
			$arrAllHtmlControls	=	array_merge($arrAllHtmlControls,$arrSearchControls);
		}

		
		$strWhere	=	$strExtSearch;
		if(!empty($strSearchWhere))
		{
			if($blnSearchExists) $strWhere	.=	" and ";
			$strWhere	.=	$strSearchWhere;
		}

		if(!empty($strWhere)) $this->setRequest("strWhere",$strWhere);

		
		if($blnGenerateList)
		{
			if($intMultiId	!= '') $strListPage		= 	$this->getHtmlMulti($intMultiId);
			else $strListPage		= 	$this->getHtmlPage($intTableId,"L");
			$arrAllHtmlControls	=	array_merge($arrAllHtmlControls,array("strPage"=>$strListPage));
		}

		
		if(strtoupper($strPageType) == 'A' || strtoupper($strPageType) == 'E' ||  strtoupper($strPageType) == 'V')
		{
			$strAEPage			= 	$this->getHtmlPage($intTableId,$strPageType,$blnCustom,$blnButton);
			if(!is_array($strAEPage))
				$strAEPage = array("strAEPage"=>$strAEPage);

			$arrAllHtmlControls	=	array_merge($arrAllHtmlControls,$strAEPage);
		}
		return $arrAllHtmlControls;
	}

		
	function openMouseImagePopup($strImage,$strString,$strTitle="Additional Details",$strHref="",$strToolTipText="")
	{
		if($this->blnExport==false)
		{
			global $IMAGE_PATH;
			if($strTitle=="")
				$strTitle="Additional Details";

			$strString = str_replace(array("&#039", "'"), '\&#039;',$strString); 
			$strString = htmlentities($strString,ENT_QUOTES);
			$strMouseOver="";

			if(trim($strString)!='')
				$strMouseOver=" onmouseover=\"return overlib('".str_replace(array("\rn", "\r", "\n"), array('','','<br />'), $strString)."',CAPTION, '".$strTitle."', DELAY, 200, STICKY, MOUSEOFF, 1000, WIDTH, 400, CLOSETEXT, '<img border=0 src=".$IMAGE_PATH."close-inline.gif>', CLOSETITLE, 'Click to Close', CLOSECLICK, FGCLASS, 'olFgClass', CGCLASS, 'olCgClass', BGCLASS, 'olBgClass', TEXTFONTCLASS, 'olFontClass', CAPTIONFONTCLASS, 'olCapFontClass', CLOSEFONTCLASS, 'olCloseFontClass');\" onmouseout=\"return nd(1000);\" ";


			if($strImage!='')
				$strImagePopup="<img src=\"".$strImage."\" border=\"0\" ".$strMouseOver." hspace=\"2\" alt=\"$strToolTipText\" title=\"$strToolTipText\">";
			else
				$strImagePopup=$strMouseOver;

			if($strHref!="")
				$strImagePopup = "<a ".$strHref.">".$strImagePopup."</a>";

			return $strImagePopup;
		}
		else
			return "";
	}

	
	function getKeywordSearchQuery($arrSearchField,$strKeyWords,&$arrSearchWords=array())
	{
		$strSearchWords		=	str_replace("+"," ",$strKeyWords);
		$arrSearchWords		=	explode(" ",$strSearchWords);

		if(count($arrSearchWords)>1)
			$arrSearchWords[]	=	$strKeyWords;

		for($intSearchWords=0; $intSearchWords<count($arrSearchWords); $intSearchWords++)
		{
			for($intSearchField=0; $intSearchField<count($arrSearchField); $intSearchField++)
			{
				if(is_array($arrSearchField[$intSearchField]))
				{
					$strFieldName	=	$arrSearchField[$intSearchField][0];
					$strCondition	=	$arrSearchField[$intSearchField][1];
				}
				else
				{
					$strFieldName	=	$arrSearchField[$intSearchField];
					$strCondition	=	"LIKE";
				}
				if(trim($arrSearchWords[$intSearchWords])!='')
				{
					if(trim(strtoupper($strCondition)) == 'LIKE')
						$strKeyWordSearch	.=	" ".$strFieldName." ".$strCondition." '%".$arrSearchWords[$intSearchWords]."%' or";
					else
						$strKeyWordSearch	.=	" ".$strFieldName." ".$strCondition." '".$arrSearchWords[$intSearchWords]."' or";
				}
			}
		}

		if(empty($strKeyWordSearch))
			return 1;
		else
		{
			$strKeyWordSearch	=	substr($strKeyWordSearch,0,strlen($strKeyWordSearch)-2);
			return $strKeyWordSearch;
		}
	}

	
	function getHrefLink($strLink,$strText,$strExtraProperty="")
	{
		if($this->blnExport==false)
		{
			$strHref	=	"<a href=\"".$strLink."\" ".$strExtraProperty.">".$strText."</a>";
			return $strHref;
		}
		else
			return $strText;
	}

	
	function generatePopupDataTable($arrPopup,$arrWidth,$blnNoWrap=true)
	{
		$strAdditional	= "";

		
		if($blnNoWrap)
			$strNoWrap	=	'nowrap="nowrap"';

		$blnPopupDisplay = 0;

		
		if(count($arrPopup) > 0)
		{
			$strAdditional	.= '<table border="0" width="100%" cellspacing="0" cellpadding="0"><tr><td>';
			$strAdditional	.= '<table border="1" width="100%" style="border-collapse:collapse;border-color:#efefef">';

			foreach($arrPopup as $arrRowTitleData)
			{
				if(count($arrRowTitleData['arrColumns'])>0)
				{
					
					if($arrRowTitleData['strRowTitle'] != "")
						$strTitle	=	'<td align="right" valign="top"  '.$strNoWrap.' width="'.$arrWidth[0]["strRowTitle"].'"><b>'.$arrRowTitleData['strRowTitle'].':</b></td>';

					$strData	=  	"";
					$blnflag	=	0;

					
					for($intRowTitleData = 0;$intRowTitleData < count($arrRowTitleData['arrColumns']);$intRowTitleData++)
					{
						
						if($arrRowTitleData['arrColumns'][$intRowTitleData] != "")
						{
							$strData	.=	'<td align="left" valign="top" width="'.$arrWidth[$intRowTitleData]["arrColumns"][$intRowTitleData].'">'.$arrRowTitleData['arrColumns'][$intRowTitleData].'</td>';
							$blnflag	 = 	1;
						}
						else
						{
							$strData	.=	'<td align="left" valign="top" width="'.$arrWidth[$intRowTitleData]["arrColumns"][$intRowTitleData].'"></td>';
						}
					}

					
					if($blnflag	== 1)
					{
						
						$blnPopupDisplay	=	1;
						$strAdditional	.=	"<tr>".$strTitle.$strData."</tr>";
					}
				}
			}
				$strAdditional	.=	"</table></td></tr></table>";
		}
		
		if($blnPopupDisplay == 1)
			return $strAdditional;
		else
			return NULL;
	}
	
	
	static function enableOnlySecureMode($strHttpHost,$blnSecure = false)
	{
		if(!isset($_SERVER['HTTPS']) && strpos(strtoupper($_SERVER['HTTP_HOST']),$strHttpHost)!==false && $blnSecure === true)
		{
			
			$URL	=	$_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];
			header('Location: https://' . $URL);
		}
	}
	
	
	
	function ConvertDateByTimeZone($strFromTimeZone,$strToTimeZone,$strDateAndTime)
	{

		$strDateTimeZoneFrom 	= 	new DateTimeZone($strFromTimeZone);
		$strDateTimeFrom 		= 	new DateTime("now", $strDateTimeZoneFrom);
		
		$strDateTimeZoneTo		=	new	DateTimeZone($strToTimeZone);
		$strDateTimeTo 			= 	new DateTime("now", $strDateTimeZoneTo);
			
		$intTimeOffset 			=	$strDateTimeZoneFrom->getOffset($strDateTimeTo)   -	$strDateTimeZoneTo->getOffset($strDateTimeTo);

		$arrDateTime			=	explode(" ",$strDateAndTime);
		
		$arrValue				=	split("-",$arrDateTime[0]);
		
		$strInputDate			=	$arrValue[1]."/".$arrValue[2]."/".$arrValue[0];
		
		$strFormatDate			=	strftime("%d %b %Y ".$arrDateTime[1],strtotime($strInputDate));
		
		$strBaseDateTimeStamp	= 	strtotime($strFormatDate);

		$strBaseDateTimeStamp	=	$strBaseDateTimeStamp	-	$intTimeOffset;
		
		$strBaseDateAndTime		=	date("Y-m-d H:i:s",$strBaseDateTimeStamp);

		return $strBaseDateAndTime;
	}

} 
?>