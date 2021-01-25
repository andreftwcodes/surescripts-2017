<?php




class MedDataList
{
	public $strSearchableFieldNames;		
	public $strSQLSelectFields;			
	public $strSQLFromTables;			
	public $strSQLWhereCriteria;		
	public $strSQLOrderBy;				
	public $strSQLGroupBy;				
	public $arrPrimaryKeyFields;		
	public $blnCustomMode;		
	public $strSetCustomQuery;		


	const SEARCH_TYPE_EXACT = 0;		
	const SEARCH_TYPE_LIKE = 1;			
	const SEARCH_TYPE_STARTS_WITH = 2;	
	
	
	function __construct()
	{
		$this->blnCustomMode = false;
	}
	
	function setSQL($strQuery)
	{
		$this->blnCustomMode = true;
		$this->strSetCustomQuery = $strQuery;
	}
	
	function setProperty($strTblName,$arrPk,$strSetListFields=null,$strWhereCond=null,$strOrderBy=null,$strGroupBy=null)
	{
		if(!is_array($arrPk)) $arrPk = array($arrPk); 
		
		$this->arrPrimaryKeyFields = $arrPk;
		$this->strSQLFromTables = $strTblName;
		$this->strSQLSelectFields = $strSetListFields;
		$this->strSQLWhereCriteria = $strWhereCond;
		$this->strSQLOrderBy = $strOrderBy;
		$this->strSQLGroupBy = $strGroupBy;
		$this->blnCustomMode = false;
	}
	
	public function setListFields($strSetListFields)
	{
		$this->strSQLSelectFields = $strSetListFields;
	}
	
	
	
	public function setListTbl($strTblName)
	{
		$this->strSQLFromTables = $strTblName;
	}
	
	
	public function setListWhere($strWhereCond)
	{
		$this->strSQLWhereCriteria = $strWhereCond;
	}
	
	
	public function setListOrderBy($strOrderBy)
	{
		$this->strSQLOrderBy = $strOrderBy;
	}
	
	
	public function setListGroupBy($strGroupBy)
	{
		$this->strSQLGroupBy = $strGroupBy;
	}

	
	private function generateWhere($arrFilter)
	{
		$strWhere = "";
		if ($this->strSQLWhereCriteria)	
			$strWhere .= " where (".$this->strSQLWhereCriteria.")";

		if (is_array($arrFilter))	
		{
			for ($idx=0; $idx < count($arrFilter); $idx++)
			{
				if (is_array($arrFilter[$idx][1]))
				{
					$strCondition = "";
					for($i=0; $i < count($arrFilter[$idx][1]); $i++)
					{
						$strSrch = $this->getSrchPattern($arrFilter[$idx][2], $arrFilter[$idx][1][$i]);
						if ($strSrch)
							$strCondition .= $arrFilter[$idx][0].$strSrch." OR ";
					}
					$strCondition = substr($strCondition, 0, (strlen($strCondition) - 4));
				}
				else
				{
					$strSrch = $this->getSrchPattern($arrFilter[$idx][2], $arrFilter[$idx][1]);
					if ($strSrch)
						$strCondition = $arrFilter[$idx][0].$strSrch;
				}

				if ($strWhere)
					$strWhere .= " and ";
				else $strWhere = " where ";
				$strWhere .= "(".$strCondition.")";
			}
		}
		return $strWhere;
	}

	
	
	private function getSrchPattern($strPattern, $strValue)
	{
		$objMedDb = MedDB::getDBObject();
		$strSrch = "";
		switch ($strPattern)
		{
			case MedDataList::SEARCH_TYPE_EXACT:
				$strSrch = " LIKE '".$objMedDb->escapeString($strValue)."'";
				break;
			case MedDataList::SEARCH_TYPE_LIKE:
				$strSrch = " LIKE '%".$objMedDb->escapeString($strValue)."%'";
				break;
			case MedDataList::SEARCH_TYPE_STARTS_WITH:
				$strSrch = " LIKE '".$objMedDb->escapeString($strValue)."%'";
				break;
			case MedDataList::SEARCH_TYPE_ENDS_WITH:
				$strSrch = " LIKE '%".$objMedDb->escapeString($strValue)."'";
				break;
			default:
				break;
		}
		return $strSrch;
	}

	
	private function generateOrderBy($arrOrderBy)
	{
		$strOrderBy = "";
		if (is_array($arrOrderBy))
		{
			foreach ($arrOrderBy as $strFieldName => $strOrder)
			{
				if ($strFieldName)
					$strOrderBy .= $strFieldName." ".$strOrder.",";
			}
		}
		if ($strOrderBy)
		{
			$strOrderBy = substr($strOrderBy,0,(strlen($strOrderBy)-1));
			$strOrderBy = " order by ".$strOrderBy;
		}
		else
		{
			if ($this->strSQLOrderBy)
				$strOrderBy = " order by ".$this->strSQLOrderBy;
		}
		return $strOrderBy;
	}

	
	private function constructQuery($arrFilters, $arrOrderBy, $intStart, $intNoOfRec)
	{
		if ($this->blnCustomMode)
			return $this->strSetCustomQuery;
	
		$strQuery= "select ";
		
		
		$strQuery.= " ".$this->strSQLSelectFields." from ".$this->strSQLFromTables.
			$this->generateWhere($arrFilters);

		if ($this->strSQLGroupBy)
			$strQuery .= " group by ".$this->strSQLGroupBy;

		$strQuery .= $this->generateOrderBy($arrOrderBy);
		
		
		if ($intNoOfRec != "0" && $intNoOfRec != "")
		{
			
			$strQuery.= " limit ".$intStart.",".$intNoOfRec;
		}
		
		return $strQuery;
	}
	
	public function countRecords($arrFilters="")
	{
		$strWhereCond = $this->generateWhere($arrFilters);

		$strQuery = "";
		if ($this->strSQLGroupBy)
			$strQuery = "Select count(distinct ".$this->strSQLGroupBy.") as tot ";
		else
			$strQuery = "Select count(*) as tot ";

		$strQuery .= "FROM ".$this->strSQLFromTables."\n".$strWhereCond;

		$objMedDb = MedDB::getDBObject();
		$arrResult = $objMedDb->executeSelect($strQuery);
		
		return $arrResult[0]["tot"];
	}

	
	
	public function load($arrFilters=null, $arrOrderBy=null, $intStart=null, $intNoOfRec=null)
	{
		$objMedDb = MedDB::getDBObject();
		
		 $strQuery = $this->constructQuery($arrFilters, $arrOrderBy, $intStart, $intNoOfRec); 
		$rsData = $objMedDb->executeSelect($strQuery);
		return $rsData;
	}
}
?>