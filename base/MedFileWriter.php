<?php

abstract class MedFileWriter
{
	public $SQL;
	public $Limit;
	public $Type;
	
	
	public function __construct($SQL, $Limit, $Type)
	{
		$this->SQL = $SQL;
		$this->Limit = $Limit;
		$this->FileType = $Type;
	}
	
	public function getRecords($StartAt = 0)
	{
		global $medDB;
		$medDB->fetchMode = ADODB_FETCH_NUM;
		$Result = $medDB->GetAll($this->buildSelect('GET_RECORDSET', $StartAt));
		$medDB->fetchMode = ADODB_FETCH_ASSOC;
		return $Result;
	}
	
	public function getCount()
	{
		global $medDB;
		$Result = $medDB->GetRow($this->buildSelect('GET_COUNT'));
		return $Result['COUNT'];
	}
	
	public function buildSelect($Type, $StartAt = 0)
	{
		switch($Type)
		{
			CASE 'GET_COUNT':
				$SQL = 'SELECT COUNT(0) AS COUNT FROM ' . $this->SQL['TABLE'] . ' ' . $this->SQL['WHERE']; 
				break;
			CASE 'GET_RECORDSET':
				$SQL = 'SELECT ' . $this->SQL['SELECT'] . ' FROM ' . $this->SQL['TABLE'] . ' ' . $this->SQL['WHERE'] . 
						'LIMIT ' . $StartAt . ',' . $this->Limit; 
				break;
		}
		return $SQL;
	}
	
	public abstract function writeFile($ColumnSep, $LineSep, $ColumnValueEnclosedBy, array $EscapeCharset);

}

