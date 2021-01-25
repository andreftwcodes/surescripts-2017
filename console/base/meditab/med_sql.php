<?php
 
require_once("med_error.php");
class MedSQL {
	
  	
	function insertRecord($objMedData)
	{
		$strFieldString = "";
		$strValueString = "";
		$arrField=$objMedData->getFieldsArray();
		$intTotFields = count($arrField);
		$objMedDb = MedDB::getDBObject();	

		for($intIdx=0; $intIdx < $intTotFields; $intIdx++)	
		{
			if ($intIdx > 0)
			{
				$strFieldString .= "`,`";
				$strValueString .= ",";
			}

			$strFieldString	=	str_replace("``","`",$strFieldString);
			$strFieldString .= $arrField[$intIdx];
			$strValue = $objMedData->getFieldValue($arrField[$intIdx]);
			$strValueString .= (null === $strValue ? "NULL" : "'".$objMedDb->escapeString($strValue)."'");
		}
		
		$strTableName = $objMedData->getTableName();
		if (empty($strTableName))	
			MedError::raiseError("TABLE_NAME_NOT_SET","MedSQL->insertRecord");

		if (empty($strFieldString))		
			MedError::raiseError("FIELD_NAME_NOT_SET","MedSQL->insertRecord");
			
		$strSql		= "insert into ".$objMedData->getTableName()." (`".$strFieldString."`) values(".$strValueString.")";
		$blnResult	= $objMedDb->executeQuery($strSql);
		$strSql		= "SELECT LAST_INSERT_ID()as autoid";
		$rsAutoId	= $objMedDb->executeSelect($strSql);
		$objMedData->setAutoId($rsAutoId[0]["autoid"]);
		
		
		
		
		
		return $blnResult;
	}

	
		
	function deleteRecord($objMedData)
	{
		global $objPage;
		$arrPkField=$objMedData->getPkFieldsArray();
		if ($arrPkField == null)
				MedError::raiseError("PK_FIELD_NOT_SET","MySQL->deleteRecord");
		$strTableName = $objMedData->getTableName();
		if (empty($strTableName))	
				MedError::raiseError("TABLE_NAME_NOT_SET","MySQL->deleteRecord");

		
		$strQuery = "DELETE  FROM ".$objMedData->getTableName()." WHERE ";
		
		$intTotalField = count($arrPkField);
		for ($intPkField=0; $intPkField < $intTotalField; $intPkField++)
		{
			$strField = $arrPkField[$intPkField];
			$strValue = $objMedData->getFieldValue($strField);
			if (empty($strField) || empty($strValue))
				MedError::raiseError("WHERE_CONDITION_NOT_SET","MySQL->deleteRecord");

			if (0 < $intPkField) $strQuery .= " and ";
			$strQuery .= $arrPkField[$intPkField]." = '".$objMedData->getFieldValue($arrPkField[$intPkField])."'";
		}

		
		$objMedDb = MedDB::getDBObject();
		
		
		$strTableName	=	$objMedData->getTableName();
		$strFld			=	"*";
		$rsRecords		=	$objPage->getRecords($strTableName, $strFld, $strWhere, "", "","", "");
		$rsRecords		=	serialize($rsRecords);
		$objPage->addArray($arrTransaction,"rsRecords",$rsRecords);
		
		
		$objResult = $objMedDb->executeQuery($strQuery);
		
		
		
		
		return $blnResult;
	}

	
	function updateByPK($objMedData)
	{
		if (null == $objMedData->getPkFieldsArray())
			 MedError::raiseError("PK_FIELD_NOT_SET","MySQL->updateByPK");
		$strTableName =$objMedData->getTableName();
		if (empty($strTableName))	
			MedError::raiseError("TABLE_NAME_NOT_SET","MySQL->updateByPK");

		
		$objMedDb = MedDB::getDBObject();

		
		$strQuery = "UPDATE ".$objMedData->getTableName()." SET ";
		$arrField = $objMedData->getFieldsArray();
		$arrPkField = $objMedData->getPkFieldsArray();
		
		for ($intField=0, $intJ=0; $intField<count($arrField); $intField++) 
		{
			if (in_array($arrField[$intField], $arrPkField))	
				continue;
			$strValue = $objMedData->getFieldValue($arrField[$intField]); 
			
			
			$strQuery .= ($intJ++ > 0 ? "," : "")." ".$arrField[$intField]." =".(null === $strValue ? "NULL" : "'".$objMedDb->escapeString($strValue)."'");

		}
		$strQuery .= " WHERE ";
		
		$arrPkField = $objMedData->getPkFieldsArray();
		$keyCount = count($arrPkField);	
		for ($intField=0; $intField < $keyCount; $intField++)
		{
			if (empty($arrPkField[$intField]))
				MedError::raiseError("WHERE_CONDITION_NOT_SET","MySQL->updateByPK");		

			if (0 < $intField) $strQuery .= " AND ";
			$strQuery .= $arrPkField[$intField]." = '".$objMedDb->escapeString($objMedData->getFieldValue($arrPkField[$intField]))."'";
		}
	
		
		$blnResult = $objMedDb->executeQuery($strQuery);
		
		
		
		
		return $blnResult;
	}

	
	function updateByCond($objMedData, $arrFieldValue, $strWhere= null)
	{
		
		$objMedDb = MedDB::getDBObject();		
		$strTableName = $objMedData->getTableName();
		if (empty($strTableName))	
			MedError::raiseError("TABLE_NAME_NOT_SET","MySQL->updateByCond");		

		$strQuery = "UPDATE ".$objMedData->getTableName()." SET ";
			
		$intFieldValue=0;

		foreach($arrFieldValue as $strKey=>$strValue)	
		{
		
			if (empty($strKey))
				MedError::raiseError("FIELD_NAME_NOT_SET","MySQL->updateByCond");		
			$strQuery .= ($intFieldValue++ > 0 ? "," : "")." `".$strKey."`=".(null === $strValue ? "NULL" : "'".$objMedDb->escapeString($strValue)."'");
		}
		if (empty($strWhere))	
				MedError::raiseError("WHERE_CONDITION_NOT_SET","MySQL->updateByCond");	
		$strQuery	=	str_replace("``","`",$strQuery);
		
		$strQuery .= " WHERE ".$strWhere;
		
		
		$blnResult = $objMedDb->executeQuery($strQuery);
		
		
		
		
		
		return $blnResult;
	}


	
	function deleteByCond($objMedData,$strWhere= null)
	{
		global $objPage;
		
		$objMedDb = MedDB::getDBObject();		
		$strTableName = $objMedData->getTableName();
		if (empty($strTableName))	
				MedError::raiseError("TABLE_NAME_NOT_SET","MySQL->deleteByCond");		
		
		if (empty($strWhere))	
				MedError::raiseError("WHERE_CONDITION_NOT_SET","MySQL->deleteByCond");	
						
		
		$strQuery = "DELETE FROM  ".$objMedData->getTableName();
		$strQuery .= " WHERE ".$strWhere;
		
		
		$strTableName	=	$objMedData->getTableName();
		$strFld			=	"*";
		$rsRecords		=	$objPage->getRecords($strTableName, $strFld, $strWhere, "", "","", "");
		$rsRecords		=	serialize($rsRecords);
		$objPage->addArray($arrTransaction,"rsRecords",$rsRecords);
		
		
		$blnResult = $objMedDb->executeQuery($strQuery);
		
		
		
		
		return $blnResult;
	}


	
	function loadByPK(&$objMedData)
	{
		$objMedDb = MedDB::getDBObject();
		$arrPkField = $objMedData->getPkFieldsArray();		
		if (null == $arrPkField )
				return MedError::raiseError("PK_FIELD_NOT_SET","MySQL->loadByPK");
		$strTableName = $objMedData->getTableName();
		if (empty($strTableName))	
				MedError::raiseError("TABLE_NAME_NOT_SET","MySQL->loadByPK");		

		$strQuery = "SELECT * FROM ".$objMedData->getTableName()." WHERE ";
		
		
		
		for ($intField=0; $intField < count($arrPkField); $intField++)
		{
			if (empty($arrPkField[$intField]))
					MedError::raiseError("WHERE_CONDITION_NOT_SET","MySQL->loadByPK");	
			if (0 < $intField) $strQuery .= " AND ";
			$strQuery .= $arrPkField[$intField]." = '".$objMedDb->escapeString($objMedData->getFieldValue($arrPkField[$intField]))."'";
		}
		
		
		$rsResult = $objMedDb->executeSelect($strQuery);
		$arrResultData = null;
		$objDataObjectStruct = $objMedData;	
		if ($rsResult)
		{
			for($intField=0;$intField<count($rsResult);$intField++)	
			{
				$arrRow = $rsResult[$intField];
				if (null == $objDataObjectStruct)
					$objDataObjectStruct = new MedData();
				else
					$objDataObjectStruct = ($objMedData);

			    foreach ($arrRow as $strKey => $strValue)
					$objDataObjectStruct->{$strKey} = $strValue;
				$arrResultData[] = $objDataObjectStruct;
			}
		}
		else
			MedError::raiseError("RECORD_NOT_EXISTS","MySQL->loadByPK");
		return $arrResultData;
	}
	
	function getAllRecords($objMedData, $strFields=null, $strWhere=null, $strOrder=null, $blnOrder=false)
	{
		$strTableName = $objMedData->getTableName();
		if (empty($strTableName))	
				MedError::raiseError("TABLE_NAME_NOT_SET","MySQL->getAllRecords");		

		$arrPkField = $objMedData->getPkFieldsArray();

		
		$strQuery = "select ";

		if ($strFields)
				$strQuery .= "$strFields ";
		else $strQuery .= "* ";

		$strQuery .= "from ".$objMedData->getTableName();
		if ($strWhere)
			$strQuery .= " where ".$strWhere;

		if ($strOrder)
			$strQuery .= " order by ".$strOrder;
		else if ($arrPkField)		
			$strQuery .= " order by ".implode(", ", $arrPkField);

		if ($blnOrder)
			$strQuery .= " DESC ";
		
		$objMedDb = MedDB::getDBObject();

		$rsResult = $objMedDb->executeSelect($strQuery);
		return $rsResult;
	}
	
	
	 
	 
	 
	 
}
?>