<?php


class MedListSearch
{
	private $intPrefixId;				

	
	public $strSearchTitle;				
	public $strSearchButtonCaption;		
	private $intSize;
	private $strSeperator;				

	
	public $blnShowMultipleFieldSearch;		
	public $blnShowAlphabeticSearch;		
	public $strAlphabeticSearchField;		
	public $arrValueSearch;		


	public $arrSearchItems;					


	public $strActiveSearchField;
	public $strActiveSearchText;
	public $strActiveAlphaSearch;

	const SEARCH_TEXT_HOLDER = "_tNSrchTxt";
	const SEARCH_FIELD_HOLDER = "_sNSrchFld";
	const ALPHA_SEARCH_TEXT = "_hASrch";
	const FIXED_VALUE_SEARCH_FIELD_HOLDER = "_hFVSrchFld";
	const FIXED_VALUE_SEARCH_TEXT_HOLDER = "_hFVSrchTxt";
	const SEARCH_TEXT_ALL = "All";							


	
	function __construct($intPrefixId, $arrOptions = "")
	{
		$this->strButtonCaption = " Search ";
		$this->arrSearchItems = $arrOptions;
		$this->strSearchTitle = "Search";
		$this->strSeperator = "&nbsp||&nbsp";
		$this->intPrefixId = $intPrefixId;
	}

	
	public function addItem($strSearchField, $strSearchDisplayName)
	{
		$this->arrSearchItems[$strSearchField] = $strSearchDisplayName;
	}

	
	public function show()
	{
		if ($this->blnShowMultipleFieldSearch || $this->blnShowAlphabeticSearch)
		{
			echo "<table border=0 cellpadding=0 cellspacing=0 width=100%>";
			if ($this->blnShowMultipleFieldSearch)
			{
				echo "<tr><td class=vsrch>";
				$this->showUserValueSearch();
				echo "</td></tr>";
			}
			if ($this->blnShowAlphabeticSearch)
			{
				echo "<tr><td class=asrch>";
				$this->showAlphabeticSearch();
				echo "</td></tr>";
			}
			echo "</table>";
		}
	}

	
	private function showAlphabeticSearch()
	{
		
		$this->arrValueSearch=array("Active"=>"Active","Inactive"=>"Inactive");
		$this->strAlphabeticSearchField=true;
		if ($this->blnShowAlphabeticSearch && $this->strAlphabeticSearchField)
		{
			$arrSearch = array(MedListSearch
::SEARCH_TEXT_ALL, "#ab", "cde", "fgh", "ijk", "lmn", "opq", "rst", "uvw", "xyz");
			for ($idx=0; $idx < count($arrSearch); $idx++)
	   		{
   				if ($arrSearch[$idx] == $this->strActiveAlphaSearch)
   				{
					echo "<span class=active>".$arrSearch[$idx]."</span>";
   				}
   				else
	   				echo "<a href='javascript:QL_Submit(\"".$this->intPrefixId."\",\"".MedQuickList::SUBMIT_SRC_ALPHA_SEARCH_LINK."\",\"".$arrSearch[$idx]."\");'>".$arrSearch[$idx]."</a>";
	   			echo "&nbsp;";
	   		}
		}
		else if (count($this->arrValueSearch) > 0 )
		{

			$arrSearch = $this->arrValueSearch;
			
			while (list($strKey, $strValue) = each($arrSearch)) {

				if ($strKey == $this->strActiveAlphaSearch)
   				{
					echo "<span class=active>".$strValue."</span>";
   				}
   				else
	   				echo "<a href='javascript:QL_Submit(\"".$this->intPrefixId."\",\"".MedQuickList::SUBMIT_SRC_ALPHA_SEARCH_LINK."\",\"".$strKey."\");'>".$strValue."</a>";

			}		
			
		}
		
	}


	
	public function parseRequest()
	{
		if (isset($_REQUEST[$this->intPrefixId.MedListSearch
::SEARCH_FIELD_HOLDER]) && !empty($_REQUEST[$this->intPrefixId.MedListSearch
::SEARCH_FIELD_HOLDER]))
		{
			$this->strActiveSearchField = $_REQUEST[$this->intPrefixId.MedListSearch
::SEARCH_FIELD_HOLDER];
			if (isset($_REQUEST[$this->intPrefixId.MedListSearch
::SEARCH_TEXT_HOLDER]) && (0 < strlen($_REQUEST[$this->intPrefixId.MedListSearch
::SEARCH_TEXT_HOLDER])))
			{
				$this->strActiveSearchText = $_REQUEST[$this->intPrefixId.MedListSearch
::SEARCH_TEXT_HOLDER];
			}
   		}

   		if (isset($_REQUEST[$this->intPrefixId.MedListSearch
::ALPHA_SEARCH_TEXT]) && $_REQUEST[$this->intPrefixId.MedListSearch
::ALPHA_SEARCH_TEXT])
   		{
   			$this->strActiveAlphaSearch = $_REQUEST[$this->intPrefixId.MedListSearch
::ALPHA_SEARCH_TEXT];
   		}

	}

	

	public function loadState($arrState)
	{
		$this->strActiveSearchField = $arrState["strActiveSearchField"];
		$this->strActiveSearchText = $arrState["strActiveSearchText"];
		$this->strActiveAlphaSearch = $arrState["strActiveAlphaSearch"];
	}

	

	public function saveState(&$arrState)
	{
		$arrState["strActiveSearchField"] = $this->strActiveSearchField;
		$arrState["strActiveSearchText"] = $this->strActiveSearchText;
		$arrState["strActiveAlphaSearch"] = $this->strActiveAlphaSearch;
	}

	

	public function getSearchCriteria()
	{
		$arrSearchParam = array();
		if ($this->strActiveSearchField)
		{
			$arrSearchParam[] = array($this->strActiveSearchField, $this->strActiveSearchText, MedDataList::SEARCH_TYPE_LIKE);
		}
		if ($this->strActiveAlphaSearch && MedListSearch
::SEARCH_TEXT_ALL !== $this->strActiveAlphaSearch)
		{
			$arrSearchParam[] = array($this->strAlphabeticSearchField, str_split($this->strActiveAlphaSearch, 1), MedDataList::SEARCH_TYPE_STARTS_WITH);
		}
		return $arrSearchParam;
	}

	

	public function showHiddenFields()
	{
		echo "<input type=hidden name=".$this->intPrefixId.MedListSearch
::ALPHA_SEARCH_TEXT." id=".$this->intPrefixId.MedListSearch
::ALPHA_SEARCH_TEXT." value='".$this->strActiveAlphaSearch."'>";
	}

	

	private function showUserValueSearch()
	{
		echo "<table border=0 cellpadding=0 cellspacing=0><tr>";
   		echo "<td>".$this->strSearchTitle."</td>";
		echo "<td><input type=text class='comn-input' ".($this->intSize ? $this->intSize." " : "").
				"name=".$this->intPrefixId.MedListSearch
::SEARCH_TEXT_HOLDER.
				" id=".$this->intPrefixId.MedListSearch
::SEARCH_TEXT_HOLDER.
				" value='".htmlspecialchars($this->strActiveSearchText, ENT_QUOTES)."' class=txtbox></td>";
   		echo "<td><select name=".$this->intPrefixId.MedListSearch
::SEARCH_FIELD_HOLDER." id=".$this->intPrefixId.MedListSearch
::SEARCH_FIELD_HOLDER.">";
   		echo "<option value=''".($this->strActiveSearchField == "" ? " selected" : "")."></option>";
		foreach ($this->arrSearchItems as $strOptionKey => $strOptionValue)
   		{
   			if ($this->strActiveSearchField == $strOptionKey)
   				echo "<option value='".$strOptionKey."' selected >".$strOptionValue."</option>";
   			else
   				echo "<option value='".$strOptionKey."'>".$strOptionValue."</option>";
   		}
   		echo "</select></td>";
		echo "<td><input type=button onclick='QL_Submit(\"".$this->intPrefixId."\",\"".MedQuickList::SUBMIT_SRC_TEXT_SEARCH_LINK."\");' name=btnSrch value='".$this->strButtonCaption."' class=btn></td>";
		echo "</tr></table>";
	}
}
?>