<?php



require_once("med_listcolumn.php");


require_once("med_pager.php");


require_once("med_datalist.php");


require_once("med_list_search.php");

class MedQuickList
{
	public $strId;							

	
	public $strCssClass;					
	public $strShowHeader;					
	public $strListTitle;   				
	public $strNoDataMessage;				
	public $strDataLoadErrorMessage;		
	public $strVerticalAlignListContent;	
	public $strVerticalAlignListHeader;		

	
	public $strJsDirectory;					
	public $blnShowSelector;				
	public $intSelectionLimit;				
	public $strSelectionLimitExceededMsg;	
	public $blnShowPaging;					
	public $intRecordsPerPage;				
	public $intPagesPerGroup;				
	public $blnShowMultipleFieldSearch;
	public $blnShowAlphabeticSearch;		
	public $blnAlphabeticSearchField;		
	public $blnSelector;					
	
	public $intListColumnNumber;
	
	
	public $arrColumnArray;					

	public $arrSelectedPKValues;			
	public $arrRestrictedPKValues;			

	public $strActiveOrderField;				
	public $strActiveOrderDirection;			
	public $intActivePage;

	
	public $strAppendListRowHtml;
	
	
	public $blnExport;


	
	public $objSearch;					
	private $objSubmitSrc;				
	protected $arrContentData;
	protected $objPager;				
	protected $objListDataGenerator;	

	private $strSubmitSrcHolder;		
	private $strOrderFieldHolder;		
	private $strOrderDirectionHolder;	
	private $intActivePageHolder;

	private $blnIsAllowDelete; 
	private $strDeleteUrl;	
	private $blnIsAllowAdd;	
	private $strAddUrl;		
	private $intTableId;		
	private $strPageType;		
	private $strTopButtonAlign;	
	private $strBottonButtonAlign;	
	private $arrButtonList;	
	private $strBaseKey;		
	
	public $arrRowClass;    

	const SUBMIT_SRC_NONE = 0;
	const SUBMIT_SRC_TEXT_SEARCH_LINK = 1;
	const SUBMIT_SRC_ALPHA_SEARCH_LINK = 2;
	const SUBMIT_SRC_SORT_LINK = 3;
	const SUBMIT_SRC_PAGER_LINK = 4;
	const SUBMIT_SRC_EXTERNAL = 5;

	
	function __construct($listid)
	{
		$this->strId = $listid;

		$this->strSubmitSrcHolder = $this->strId."_hSubmitSrc";
		$this->strOrderFieldHolder = $this->strId."_hOFld";
		$this->strOrderDirectionHolder = $this->strId."_hODirc";
		$this->intActivePageHolder = $this->strId."_hPg";
		$this->intActivePageGroupHolder = $this->strId."_hPgGrp";

	 	
	 	$this->strNoDataMessage = "No data found";
	 	$this->strDataLoadErrorMessage = "Error loading data";
	 	$this->blnShowPaging = true;
	 	$this->blnShowHeader = true;
		$this->blnShowSelector = true;
		$this->intRecordsPerPage = 10;
		$this->intPagesPerGroup = 10;
		$this->blnShowAlphabeticSearch = false;
		$this->objSearch = new MedListSearch($this->strId);
		$this->strButtonAlign = "right";
		$this->strJsDirectory = "include/js/";
		$this->strCssClass = "qlist";	
		$this->blnSelector = true;	
		$this->intListColumnNumber = 1;
		
		$this->arrButtonList=array();
		$this->arrRowClass=array();
		$this->blnExport = false;		
		$this->loadState();
	}
	
	
	public function setHiddenValues($tableid,$pageType)
	{   
		
		
	}
	
	
	public function setButtonAlign($align="right")
	{
		$this->strButtonAlign = $align;
	}
	
	
 	public function addTextItem($strHeader, $strField, $intWidth=null, $strhAlign=null,$blnSortAllow=false,$strFieldType=null)
 	{
 		if (is_array($strField))	
 			$fieldList = array($strField[0]);
 		else
 			$fieldList = array($strField);

 		$objColumn = new MedListColumn(MedListColumn::TYPE_TEXT, $strHeader, $fieldList, $intWidth, $strhAlign);
		$objColumn->blnIsSortable = $blnSortAllow;
		$objColumn->strTextFieldType = $strFieldType;			
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}

	
 	public function addBaseKey($fieldList)
 	{
		$this->strBaseKey=$fieldList;
		return $objColumn;
 	}


	
 	public function addCheckBoxItem($strHeader, $fieldList, $intWidth=null,$strhAlign=null,$strFieldName="chk",$strFieldType=null,$blnSortAllow=false,$strExtraParam=null)
 	{
 		if (!is_array($fieldList))
 			$fieldList = array($fieldList);

 		$objColumn = new MedListColumn(MedListColumn::TYPE_CHECKBOX, $strHeader, $fieldList, $intWidth,0,$strhAlign);
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
		$objColumn->strOnClick = $strExtraParam;
		$objColumn->strTextFieldType = $strFieldType;		
		$objColumn->strFieldName = $fieldList[0];	
		$objColumn->blnIsSortable = $blnSortAllow;
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}


	
 	public function addEvaluatedExprItem($strHeader, $fieldList, $strDisplayExpr, $intWidth=null, $strhAlign=null,$blnDisplayExpr=false)
 	{
 		if (!is_array($fieldList))
 			$fieldList = array($fieldList);

 		$objColumn = new MedListColumn(MedListColumn::TYPE_EVALUATED_EXPR, $strHeader, $fieldList, $intWidth, $strhAlign);
		$objColumn->strDisplayExpr = $strDisplayExpr;
		$objColumn->blnIsSortable = $blnDisplayExpr;		
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}

	
 	public function addLinkItem($strHeader, $fieldList, $strCaptionDisplayExpr=null, $strHref=null, $strOnClick=null, $intWidth=null, $strhAlign=null,$blnSortAllow=false,$strFieldType=null)
 	{
 		if (!is_array($fieldList))
 			$fieldList = array($fieldList);

 		$objColumn = new MedListColumn(MedListColumn::TYPE_LINK, $strHeader, $fieldList, $intWidth, $strhAlign);
		$objColumn->blnIsSortable = $blnSortAllow;
		$objColumn->strHref = $strHref;
		$objColumn->strOnClick = $strOnClick;
		$objColumn->strTextFieldType = $strFieldType;		
		$objColumn->strDisplayExpr = $strCaptionDisplayExpr;
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}

	
 	public function addImageItem($strHeader,$fieldList,$strScr,$strHref=null,$strImageParam=null, $strOnClick=null,$intWidth=null, $strhAlign=null,$blnSortAllow=false)
 	{
		global $IMAGE_PATH;
 		if (!is_array($fieldList))
 			$fieldList = array($fieldList);
 
 		
 				$strCaptionDisplayExpr = "<img src=\"".$IMAGE_PATH.$strScr."\"".$strImageParam." >";
 		
 			
 		$objColumn = new MedListColumn(MedListColumn::TYPE_LINK, $strHeader, $fieldList, $intWidth, $strhAlign);
		$objColumn->blnIsSortable = $blnSortAllow;
		$objColumn->strHref = $strHref;
		$objColumn->strOnClick = $strOnClick;
		$objColumn->strDisplayExpr = $strCaptionDisplayExpr;
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}


	
	public function addFileItem($strHeader, $fieldList, $strCaptionDisplayExpr=null,$intWidth=null,$strhAlign=null,$blnSortAllow=false,$strFieldType=null)
 	{
 		if (!is_array($fieldList))
 			$fieldList = array($fieldList);

 		$objColumn = new MedListColumn(MedListColumn::TYPE_FILE, $strHeader, $fieldList, $intWidth, $strhAlign);
		$objColumn->blnIsSortable = false;
		$objColumn->strTextFieldType = $strFieldType;		
		$objColumn->strFieldName = $fieldList[0];			
		$objColumn->strDisplayExpr = $strCaptionDisplayExpr;
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}
	
 	public function addConditionalItem($strHeader, $fieldList, $strConditionalParam, $intWidth=null, $strhAlign=null,$blnSortAllow=false)
 	{
 		if (!is_array($fieldList))
 			$fieldList = array($fieldList);

 		$objColumn = new MedListColumn(MedListColumn::TYPE_CONDITIONAL, $strHeader, $fieldList, $intWidth."%", $strhAlign);
 		$objColumn->arrConditionalValuesArray = $strConditionalParam;
		
		
		if($objColumn->strCheckType == "")
		{
			foreach ($strConditionalParam  as $keys=>$value)
			{
				if(preg_match("{0}",$keys) && $keys != '0')	$objColumn->strCheckType = "Exp";
				else	$objColumn->strCheckType = "Val";	
				break;
			}
		}
		
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
		$objColumn->blnIsSortable = $blnSortAllow;
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}

	
 	public function addTextBoxItem($strHeader, $fieldList, $intWidth=null, $intBoxWidth=10,$strhAlign=null,$strFieldName="fld",$intFieldType="1",$blnSortAllow=false,$strExtraParam=null)
 	{
 		if (!is_array($fieldList))
 			$fieldList = array($fieldList);

 		$objColumn = new MedListColumn(MedListColumn::TYPE_TEXTBOX, $strHeader, $fieldList, $intWidth,$intBoxWidth, $strhAlign);
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
		$objColumn->strFieldName = $fieldList[0];
		$objColumn->intTextBoxWidth = $intBoxWidth;
		$objColumn->strTextFieldType = $intFieldType;
		$objColumn->blnIsSortable = $blnSortAllow;
		$objColumn->strHorizontalAlign=$strhAlign;
		$objColumn->strOnClick = $strExtraParam;
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}

	
 	public function addComboItem($strHeader, $fieldList, $intWidth=null,$arrOptionValues,$strhAlign=null,$strFieldName="cmb",$blnSortAllow=false,$strExtraParam=null)
 	{
 		if (!is_array($fieldList))
 			$fieldList = array($fieldList);

 		
 			


 		$objColumn = new MedListColumn(MedListColumn::TYPE_COMBO, $strHeader, $fieldList, $intWidth,$strhAlign);
 		
		$objColumn->blnIsSortable = $blnSortAllow;
 		$objColumn->intCollectionIndex = count($this->arrColumnArray);
		
		$objColumn->strOnClick = $strExtraParam;		
		$objColumn->strFieldName = $fieldList[0];		
		$objColumn->arrComboOptions = $arrOptionValues;
 		$this->arrColumnArray[] = $objColumn;
		return $objColumn;
 	}

	
	public function setAllowDelete($strDeleteUrl = "index.php")
	{
		$this->blnIsAllowDelete = true;
		$this->strDeleteUrl = $strDeleteUrl;		
	}

	

	public function setAllowAdd($strAddUrl = "index.php?file=form.htm")
	{
		$this->blnIsAllowAdd = true;
		$this->strAddUrl = $strAddUrl;		
	}

		
	public function addButton($strCaption,$strUrl,$blnAllowSelect=null,$strMesString=null,$strUserCode=null,$strHAlign="center",$strVAlign="top")
	{
		if (($strHAlign != null )&&(!empty($strHAlign)))
		{
			if (trim(strtoupper($strVAlign)) == "TOP")
				$this->strTopButtonAlign=$strHAlign;
			else
				$this->strBottomButtonAlign=$strHAlign;
		}	
		
		if (($strVAlign == null )||(empty($strVAlign)))
			$strVAlign = "top";
		
		$this->arrButtonList[]=array(
			"caption" => $strCaption,
			"url" => $strUrl,
			"allowSelect" => $blnAllowSelect,	
			"messageString" => $strMesString,
			"userCode" => $strUserCode,		
			"halign" => $strHAlign,		
			"valign" => $strVAlign				
		);		
	}	

	
	public function show($objListData)
	{
		if($this->blnExport==false)
		{
			ob_clean();
			ob_start();
			$arrFilter = array();
			$arrOrderBy = array();
			$this->objListDataGenerator = $objListData;
	
			
			$arrFilter = $this->objSearch->GetSearchCriteria();
	
			
			$startRec = 0;
			if ($this->blnShowPaging)
			{
				$this->objPager = new MedPager($this->intRecordsPerPage, $this->intPagesPerGroup, $this->strId);
				$intCountResult = $this->objListDataGenerator->countRecords($arrFilter);
				if (false === $intCountResult)
				{
					
				}
				else
				{
	
					$this->objPager->setRecordCount($intCountResult);
					$lastPage = $this->objPager->getPageCount();
					if ($lastPage < $this->intActivePage)
					{
						$this->intActivePage = $lastPage;
					}
					$this->objPager->setActivePage($this->intActivePage);
					$startRec = $this->objPager->getActivePageStartingRec();
				}
			}
			
			
			if ($this->strActiveOrderDirection)
			{
				$arrOrderBy[$this->strActiveOrderField] = $this->strActiveOrderDirection;
			}		
			
			$this->arrContentData = $this->objListDataGenerator->load($arrFilter, $arrOrderBy, $startRec, ($this->blnShowPaging ? $this->intRecordsPerPage : null));			
		}	
		else
		{
			$this->objListDataGenerator = $objListData;
			
			if ($this->strActiveOrderDirection)
			{
				$arrOrderBy[$this->strActiveOrderField] = $this->strActiveOrderDirection;
			}	
			
			$this->arrContentData = $this->objListDataGenerator->load($arrFilter, $arrOrderBy, 0, null);
		}	
			
		if (false === $this->arrContentData)
		{
			
			if (function_exists($this->strId."_DataLoadError"))
			{
				call_user_func($this->strId."_DataLoadError", array());
			}
		}
		else
		{
			
			if (function_exists($this->strId."_DataLoaded"))
			{
				call_user_func($this->strId."_DataLoaded", array(&$this->arrContentData));
			}
		}

		$this->beginRendering();
		
		if($this->blnExport==false)
		{
			$this->saveState();
			$strBuffer = ob_get_contents();
			ob_end_clean();
			return $strBuffer;		
		}	
	}

	

	public function loadState()
	{
		
		if (isset($_REQUEST[$this->strSubmitSrcHolder]))
		{
			
			$this->parseRequest();
		}
		else
		{
			
			if (isset($_GET["ql"]) && ("f" == $_GET["ql"]))
			{
				
				$this->initializeState();
			}
			else
			{
				
				$this->loadFromSession();
			}
		}
	}

	

	public function parseRequest()
	{
		

		
		if (isset($_POST[$this->strSubmitSrcHolder]))
			$this->objSubmitSrc = $_POST[$this->strSubmitSrcHolder];
		else
			$this->objSubmitSrc = MedQuickList::SUBMIT_SRC_NONE;

		
		if (isset($_POST[$this->strId."_cSlcPK"]) && $_POST[$this->strId."_cSlcPK"])
		{
			$this->arrSelectedPKValuesArray = $_POST[$this->strId."_cSlcPK"];
			
		}
		
		if (isset($_REQUEST[$this->strOrderFieldHolder]) && ($_REQUEST[$this->strOrderFieldHolder]))
		{
			
			$this->strActiveOrderField = $_REQUEST[$this->strOrderFieldHolder];
			$this->strActiveOrderDirection = $_REQUEST[$this->strOrderDirectionHolder];
		}

		
		$this->intActivePage = 1;
		if (isset($_REQUEST[$this->intActivePageHolder]) && $_REQUEST[$this->intActivePageHolder])
		{
			$this->intActivePage = $_REQUEST[$this->intActivePageHolder];
		}
		
		$this->objSearch->parseRequest();

		
		if (MedQuickList::SUBMIT_SRC_SORT_LINK == $this->objSubmitSrc ||
			MedQuickList::SUBMIT_SRC_TEXT_SEARCH_LINK == $this->objSubmitSrc ||
			MedQuickList::SUBMIT_SRC_ALPHA_SEARCH_LINK == $this->objSubmitSrc)
		{
			$this->intActivePage = 1;
		}
	}

	
	public function initializeState()
	{
		$this->intActivePage = 1;
		
		if (isset($_SESSION["MedQuickList"][MedGeneral::getProjectName().MedGeneral::getSession('module_id').$this->strId]))
			$_SESSION["MedQuickList"][MedGeneral::getProjectName().MedGeneral::getSession('module_id').$this->strId] = null;
	}

	

	public function loadFromSession()
	{
		
		
		
		if (!isset($_SESSION["MedQuickList"][MedGeneral::getProjectName().MedGeneral::getSession('module_id').$this->strId]))
			return;

		$properties = $_SESSION["MedQuickList"][MedGeneral::getProjectName().MedGeneral::getSession('module_id').$this->strId];
		
		
		$this->arrSelectedPKValuesArray = $properties["arrSelectedPKValuesArray"];

		
		$this->strActiveOrderField = $properties["strActiveOrderField"];
		$this->strActiveOrderDirection = $properties["strActiveOrderDirection"];

		
		$this->intActivePage = $properties["intActivePage"];

		
		$this->objSearch->loadState($properties);
	}

	
	public function saveState()
	{
		
		$properties = array();

		
		$properties["arrSelectedPKValuesArray"] = $this->arrSelectedPKValuesArray;

		
		$properties["strActiveOrderField"] = $this->strActiveOrderField;
		$properties["strActiveOrderDirection"] = $this->strActiveOrderDirection;

		
		$properties["intActivePage"] = $this->intActivePage;
		
		
		$this->objSearch->saveState($properties);
		$_SESSION["MedQuickList"][MedGeneral::getProjectName().MedGeneral::getSession('module_id').$this->strId] = $properties;
	}

	
	private function beginRendering()
	{
		if($this->blnExport==false)
		{
			$this->parseHiddenFields();
			echo "<table width=100% id=".$this->strId.($this->getAttributesToRender())." cellpadding=0 cellspacing=0 border=0 class=".$this->strCssClass.">\n";
			if ($this->objSearch)
			{
				echo "\n<tr><td class=srch>";
				$this->objSearch->Show();
				echo "</td></tr>";
			}
	
			
			echo "\n<tr><td class=lst>";
			$this->renderListingTable();
			echo "</td></tr>";
	
			echo "\n</table>";
		}
		else
			$this->renderListingTable();		
	}

	

	protected function getAttributesToRender()
	{
		$attributeString =
			($this->intSelectionLimit && (0 < $this->intSelectionLimit) ? " slctLimit=".$this->intSelectionLimit : "").
			($this->strSelectionLimitExceededMsg ? " slctLimitExceedMsg='".$this->strSelectionLimitExceededMsg."'" : "");

		return $attributeString;
	}
	
	private function renderButtons($strLoc)
	{
		if (count($this->arrButtonList) > 0 )
		{
			for($i=0;$i<count($this->arrButtonList);$i++)
			{
				
				$arrUrl	=	@explode(":",$this->arrButtonList[$i]["url"]);
				
				
				if($arrUrl[1] == "J" || $arrUrl[1] == "JA")
				{
					$objPage = MedPage::getPageObject();
					
					
					if($objPage->objGeneral->getSession('intRights') == 0 && $arrUrl[1] == "JA")
					{
						if (trim(strtoupper($strLoc))== trim(strtoupper($this->arrButtonList[$i]["valign"])))
							echo "&nbsp;<input type='button' name=btn".$i." value='".$this->arrButtonList[$i]["caption"]."' ".$arrUrl[3]." class='btn' ".$this->arrButtonList[$i]["userCode"]." >";
							
					}
					else if($arrUrl[1] == "J") 
					{
						if (trim(strtoupper($strLoc))== trim(strtoupper($this->arrButtonList[$i]["valign"])))
							echo "&nbsp;<input type='button' name=btn".$i." value='".$this->arrButtonList[$i]["caption"]."' ".$arrUrl[3]." class='btn' ".$this->arrButtonList[$i]["userCode"]." >";
					}	
				}
				else
				{
					if (trim(strtoupper($strLoc))== trim(strtoupper($this->arrButtonList[$i]["valign"])))
						echo "&nbsp;<input type='button' name=btn".$i." value='".$this->arrButtonList[$i]["caption"]."' onClick=\"doAction(this,'".$this->strId."','".$this->arrButtonList[$i]["url"]."',".(($this->arrButtonList[$i]["allowSelect"])?"1":"0").",'".$this->arrButtonList[$i]["messageString"]."')\" class='btn' ".$this->arrButtonList[$i]["userCode"]." >";
				}
			}	
			echo "</td>";
		
		}
	}


	private function countTotalButton($strLoc)
	{
		
		$intTotal=0;
		if (count($this->arrButtonList) > 0 )
		{
			for($i=0;$i<count($this->arrButtonList);$i++)
			{
			if (trim(strtoupper($strLoc))== trim(strtoupper($this->arrButtonList[$i]["valign"])))
				$intTotal++;
			}
		}
		return $intTotal;
	}

	
	protected function renderListingTable()
	{
		if($this->blnExport==false)
		{
			if ($this->blnShowSelector)
				$totcols = count($this->arrColumnArray)+1;
			else
				$totcols = count($this->arrColumnArray);
	
			echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>";
			if ($this->countTotalButton("top") > 0 )
			{
				if ((empty($this->strTopButtonAlign)) || $this->strTopButtonAlign == null)
					$this->strTopButtonAlign = "left";
				echo "<tr  align=".$this->strButtonAlign." ><td  class='space' colspan=".$totcols." align=".$this->strTopButtonAlign." valign='middle' >";
				$this->renderButtons("top");
				echo "</tr>";		
			}
			
	
			if ($this->blnIsAllowDelete)
				$this->blnShowSelector = true;
			
			if ($this->strListTitle)
				echo "<tr><td colspan=".$totcols." class=ttl>".$this->strListTitle."</td></tr>\n";
	
			
			if ($this->blnShowHeader)	$this->renderListingHeader();
	
			$this->renderListingContent();
	
	
			echo "</table>";
		}
		else
		{
			$this->renderListingHeader();
			if ($this->arrContentData)
			{
				$totalRecords = count($this->arrContentData);
				if (0 < $totalRecords)
					$this->renderAllListRows($totalRecords);
			}	
		}		
	}

	

	protected function renderListingContent()
	{
		if ($this->blnShowSelector)
			$totcols = count($this->arrColumnArray)+1;
		else
			$totcols = count($this->arrColumnArray);

		if (false === $this->arrContentData)
		{
			echo "<tr><td colspan=".$totcols.">".$this->strDataLoadErrorMessage."</td></tr>";
			return;
		}
		if ($this->arrContentData)
		{
			$totalRecords = count($this->arrContentData);
			echo "<input type=hidden name='".$this->strId."_rows' value='".$totalRecords."' id='".$this->strId."_rows' >\n";		
			if ($this->blnShowPaging && (0 < $this->intRecordsPerPage))
			{
				if ($this->intRecordsPerPage < $totalRecords)
					$totalRecords = $this->intRecordsPerPage;
			}
		}
		else
			$totalRecords = 0;
		
		if (0 < $totalRecords)
		{
			$this->renderAllListRows($totalRecords);
			if ($this->strAppendListRowHtml)
				eval($this->strAppendListRowHtml);

			if ($this->countTotalButton("bottom") > 0 )
			{
				if ((empty($this->strBottomButtonAlign)) || $this->strBottomButtonAlign == null)
					$this->strBottomButtonAlign = "right";
	
				echo "<tr  align=".$this->strButtonAlign." ><td colspan=".$totcols." align=".$this->strBottomButtonAlign." height='30px' valign='middle' >";
				$this->renderButtons("bottom");
				echo "</tr>";		
			}	
			
			if (($this->blnShowPaging) && (	$this->objPager->getPageCount() > 1 ))
			{
				echo "<tr class=pgr><td align='right' colspan=".$totcols."><font class='bold-text'>";
				$this->objPager->strActivePageMessage="[[[PAGE]] to [[END]] of [[TOTAL]]]";
				$this->objPager->getPageLimit();
				echo "</font>&nbsp;<font class='header_text'>".$this->objPager->getPageCount()."&nbsp;Page(s):&nbsp;</font>";
				$this->objPager->getFirstPage()."&nbsp;";
				$this->objPager->show();
				$this->objPager->getLastPage();
				echo "&nbsp;</td></tr>";
			}
			
		}
		else
			echo "<tr class=data1><td align='center' colspan=".$totcols.">".$this->strNoDataMessage."</td></tr>";
	}

	

	protected function renderAllListRows($totalRecords)
	{
		for ($intRowIdx=0; $intRowIdx < $totalRecords; $intRowIdx++)
		{
			$this->renderListRow($intRowIdx);
		}
	}

	

	protected function renderListRow($intRowIdx)
	{
		if($this->blnExport==false)
		{
			if(0 == ($intRowIdx % $this->intListColumnNumber))
			{
				if(array_key_exists($intRowIdx,$this->arrRowClass))
					echo "\n<tr class=".$this->arrRowClass[$intRowIdx]['strClassO'];
				else				
					echo "\n<tr class=data".(0  == (($intRowIdx / $this->intListColumnNumber)%2) ? "1" : "2");
				if ($this->strVerticalAlignListContent)
					echo " valign = ".$this->strVerticalAlignListContent;
				if  (($this->blnSelector) || ($this->blnShowSelector))
					echo " onmouseover='QL_MOver(this)' onmouseout='QL_MOut(this)' ";
				echo ">";
			}
			$this->renderAllColumnsForRow($intRowIdx);
			if($this->intListColumnNumber - 1 == ($intRowIdx % $this->intListColumnNumber))
			{
				echo "</tr>";
			}
		}
		else
		{
			$this->renderAllColumnsForRow($intRowIdx);
			echo "\n";
		}	
	}

	
 
	public function setRowClass($intRow,$strClassO)
	{
		
		
		if($strOverClass=="")
			$strOverClass=$strOriginalClass;
			
		$this->arrRowClass[$intRow]=array(
			"strClassO" => $strClassO,
		);		
	}
	
	
	protected function renderListingHeader()
	{
		if($this->blnExport==false)
		{
			global $IMAGE_PATH;		
			echo "<tr class=hdr".($this->strVerticalAlignListHeader ? " valign=".$this->strVerticalAlignListHeader : "").">";
	
			for($intLC=0;$intLC<$this->intListColumnNumber;$intLC++)
			{
				if ($this->blnShowSelector)
				{
					echo "<td width=2%>";
					if (!$this->intSelectionLimit || 0 >= $this->intSelectionLimit)
					{
						echo "<input type=checkbox id=".$this->strId."_cSlcHd onClick='QL_HeaderCBClick(\"".$this->strId."\",this);  '>";
					}
					else echo "&nbsp;";
					echo "</td>";
				}
				for($colIdx=0; $colIdx < count($this->arrColumnArray); $colIdx++)
				{
				
					$objlColumn = $this->arrColumnArray[$colIdx];
					
					echo "<td ".($objlColumn->intWidth ? " width=".$objlColumn->intWidth : "").
						($objlColumn->strHorizontalAlign ? " align=".$objlColumn->strHorizontalAlign : "").
						($objlColumn->strCssClass ? " class=".$objlColumn->strCssClass : "").">";
		
					if  (($objlColumn->intType == MedListColumn::TYPE_CHECKBOX) && (empty($objlColumn->strHeaderText)))
					{
						$objPage = MedPage::getPageObject();
						$strName = $objPage->generateHtmlControlName("CHECKBOX",null,null,$objlColumn->strFieldName)."[]"; 
						echo "<input type=checkbox id=".$this->strId."_chkID onClick='QL_HeaderChkClick(\"".$this->strId."\",this,\"".$strName."\"); '>";
					}
					else if ($objlColumn->strHeaderText)
					{
						$sortCol = $objlColumn->getSortColumn();
						if ($sortCol)
						{
							$symbol = "";
							$order = "asc";
							if ($this->strActiveOrderField && ($this->strActiveOrderField == $sortCol))
							{
								if ($this->strActiveOrderDirection == "asc" )
								{				
									$strFilePath=$IMAGE_PATH."up-arrow.gif";			
									if(file_exists($strFilePath))  $symbol = "&nbsp;<img src='".$strFilePath."' border='0' align='absmiddle'>";
									else $symbol = "<font class=srtAsc face='Webdings'>5</font>";
									$order = "desc";
								}
								else
								{
									$strFilePath=$IMAGE_PATH."down-arrow.gif";			
									if(file_exists($strFilePath))  $symbol = "&nbsp;<img src='".$strFilePath."' border='0' align='absmiddle'>";
									else $symbol = "<font class=srtDesc face='Webdings'>6</font>";
								}
							}
							echo "<a href=\"javascript:".$this->getSubmitJs(MedQuickList::SUBMIT_SRC_SORT_LINK, $sortCol, $order).
									"\">".$objlColumn->strHeaderText.$symbol."</a>";
						}
						else echo $objlColumn->strHeaderText;
					}
					else echo "&nbsp;";
					echo "</td>";
				}
			}
			echo "</tr>";
		}
		else
		{
			for($intLC=0;$intLC<$this->intListColumnNumber;$intLC++)
			{
				for($colIdx=0; $colIdx < count($this->arrColumnArray); $colIdx++)
				{				
					$objlColumn = $this->arrColumnArray[$colIdx];
					if ($objlColumn->strHeaderText)
					{
						echo $objlColumn->strHeaderText."\t";
					}
				}	
			}
			echo "\n";
		}	
	}

	

	protected function getSubmitJs($submitSrc, $strValue1, $strValue2)
	{
		return "QL_Submit('".$this->strId."', '".$submitSrc."', '".$strValue1."', '".$strValue2."')";
	}

	

	protected function renderAllColumnsForRow($rowIndex)
	{
		$currentRow = $this->arrContentData[$rowIndex];
		if($this->blnExport==false)
			$this->renderSelectorForRow($currentRow, $rowIndex);
			
		for($colIdx=0; $colIdx < count($this->arrColumnArray); $colIdx++)
		{
			$this->arrColumnArray[$colIdx]->blnExport= $this->blnExport;
			$this->arrColumnArray[$colIdx]->parseColumn($currentRow,$rowIndex);
		}
	}
	
	

	protected function renderSelectorForRow($currentRow, $rowIndex)
	{
		if ($this->blnShowSelector)
		{
			$intValue = $currentRow[$this->objListDataGenerator->arrPrimaryKeyFields[0]];
			$strTempValue="";			
			if (is_array($this->arrSelectedPKValues) && (in_array($intValue ,$this->arrSelectedPKValues)))
				$strTempValue="checked";
			
			if (is_array($this->arrRestrictedPKValues) && (in_array($intValue ,$this->arrRestrictedPKValues)))
			{
				echo "<td height=20>&nbsp;</td>";
				return;
			}
			echo "<td valign='top'><input type=checkbox name='".$this->strId."_cSlcPK[]' id=".
					$this->strId."_cSlcPK_".$rowIndex.
					" value='".$currentRow[$this->objListDataGenerator->arrPrimaryKeyFields[0]].
					"' onclick=\"QL_CBClick('".$this->strId."', this, $rowIndex)\" ".$strTempValue." ></td>";
		}
	}

	

	protected function parseHiddenFields()
	{
		
		echo "<input type=hidden name=hid_listname value='".$this->strId."'>\n";		
		echo "<input type=hidden name=".$this->strSubmitSrcHolder." id=".$this->strSubmitSrcHolder." value='".$this->objSubmitSrc."'>\n";
		echo "<input type=hidden name=".$this->strOrderFieldHolder." id=".$this->strOrderFieldHolder." value='".$this->strActiveOrderField."'>\n";
		echo "<input type=hidden name=".$this->strOrderDirectionHolder." id=".$this->strOrderDirectionHolder." value='".$this->strActiveOrderDirection."'>\n";
		
		if ($this->blnShowPaging)
			echo "<input type=hidden name=".$this->intActivePageHolder." id=".$this->intActivePageHolder." value='".$this->intActivePage."'>\n";
		$this->objSearch->showHiddenFields();
	}

	

	public static function getSelectedPKValues($listId)
	{
		if (isset($_POST[$listId."_cSlcPK"]) && $_POST[$listId."_cSlcPK"])
		{
			return $_POST[$listId."_cSlcPK"];
		}
		else return null;
	}

	
	public function setJsDirectory($strJsDirectory) 
	{
		$this->strJsDirectory = $strJsDirectory;
	}
	
	
	public function setShowSelector($blnShowSelector) 
	{
		$this->blnShowSelector = $blnShowSelector;
	}
	
	
	public function setShowPaging($blnShowPaging) 
	{
		$this->blnShowPaging = $blnShowPaging;
	}
	
		
	public function setSelectionLimit($intSelectionLimit) 
	{
		$this->intSelectionLimit = $intSelectionLimit;
	}
	
		
	public function setRecordsPerPage($intRecordsPerPage) 
	{
		$this->intRecordsPerPage = $intRecordsPerPage;
	}
	
		
	public function setPagesPerGroup($intPagesPerGroup) 
	{
		$this->intPagesPerGroup = $intPagesPerGroup;
	}
	
		
	public function setCssClass($strCssClass) 
	{
		$this->strCssClass = $strCssClass;
	}
	
		
	public function setSelectedValues($arrSelectedPk) 
	{
		if (!is_array($arrSelectedPk))
			$arrSelectedPk=array($arrSelectedPk);
		$this->arrSelectedPKValues= $arrSelectedPk;
	}

	
		
	public function setRestrictedValues($arrRestrictedPk) 
	{
		if (!is_array($arrRestrictedPk))
			$arrRestrictedPk=array($arrRestrictedPk);
		$this->arrRestrictedPKValues = $arrRestrictedPk;
	}
	
		
	public function setAlphaSearchField($strField) 
	{
		$this->objSearch->blnShowAlphabeticSearch = true;
		$this->objSearch->strAlphabeticSearchField = $strField;
	}
	
		
	public function setMultipleSearchField($arrSearchItems) 
	{
		$this->objSearch->blnShowMultipleFieldSearch = true; 
		$this->objSearch->arrSearchItems = $arrSearchItems;
	}

		
	public function setSelectorOff($arrSearchItems) 
	{	
		$this->blnSelector=false;
	}

		
	public function setDefaultOrderField($strOrderField) 
	{	
		if (!isset($_REQUEST[$this->strOrderFieldHolder]))
			$this->strActiveOrderField=$strOrderField;
	}
		
	public function setDefaultOrder($strOrderField) 
	{	
		if (!isset($_REQUEST[$this->strOrderFieldHolder]))
		{
				if (trim(strtoupper($strOrderField))=="ASC" or empty($strOrderField))
					$this->strActiveOrderDirection = "asc";
				else	
					$this->strActiveOrderDirection = "desc";	
		}
	}
}
?>