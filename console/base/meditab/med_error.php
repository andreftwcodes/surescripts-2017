<?php

class MedError
{
	 
	var $intErrorCode;
	 
	var $strErrorDescription;
	 
	var $strErrorSoultion;
	function getErrorCode()
	{
		return $this->intErrorCode;
	}

	function getErrorDesc()
	{
		return $this->strErrorDescription;
	}

	function getErrorSolution()
	{
		return $this->strErrorSoultion;
	}
	function raiseError($intErrorCode=null,$strFunctionName=null)
	{
		$objDB = MedDB::getDBObject();
		$objError = new MedError();
		
		$strQuery = "SELECT * FROM db_error_messages WHERE error_code ='".$intErrorCode."'";
		$rs = $objDB->executeSelect($strQuery);
		if (count($rs)>0)
		{
			$strErrorDescription=$rs[0]["error_description"];
			$strSoultion = $rs[0]["error_solution"];
		}
		else
		{
			$strErrorDescription="Description of this error(".$intErrorCode.") is not available in db";
			$strSoultion="null";
		}
		$strModuleName=$_SERVER["SCRIPT_FILENAME"];		
		$strModuleName .= "( ".$strFunctionName.")";
		$objGeneral = MedGeneral::getGeneralObject();
		$objGeneral->raiseError("DB_OBJECT",$strErrorDescription, $strModuleName,$strSoultion);
	}
	
	function setError($intErrorCode, $strErrorDescription=null, $strErrorSoultion=null)
	{
		$this->intErrorCode = $intErrorCode;
		$this->strErrorDescription = $strErrorDescription;
		$this->strErrorSoultion = $strErrorSoultion;
	}
	
	function getErrorDescription()
	{
		 return $this->mErrorMessage;
	}
}
?>
