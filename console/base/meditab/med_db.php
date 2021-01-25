<?php

class MedDB
{
  
	var $objarrdb;
	var $blnAutoCommit;
	function __construct()
	{
		$this->blnAutoCommit = true;
	}
	
	function getDBObject($objCn="")
	{
		if($objCn=="")
			return $GLOBALS["objDb"];
		else
			return $GLOBALS[$objCn];	
	}
  
	function connect($dsn,$options=array())
	{
		$this->objarrdb =& DB::connect($dsn, $options);	
		if($this->isError($this->objarrdb))				
			exit(0);
		
		$this->objarrdb->setFetchMode(DB_FETCHMODE_ASSOC);			
		return $this;
	}
  
	function disconnect()
	{
		if($this->isconnected())					
		{	
			$this->objarrdb->disconnect();				
			if($this->isError($this->objarrdb))		
				exit(0);		
		}
	}

  
	function isError($objDb)
	{
		if(PEAR::isError($objDb))					
		{
			$result = $objDb;
			if (!$this->blnAutoCommit)
				$this->rollbackTrans();
			$errorCode = $result->getCode();
			$errorMessage = $result->getMessage();
			$errorDesc = $result->getUserInfo();
			$objGeneral = MedGeneral::getGeneralObject();
			$errorDesc = str_replace("[nativecode","<br>[nativecode",$errorDesc);		
			if ($objGeneral == null )
			{
				$strSiteName = $_SERVER["HTTP_HOST"];
				$strRequestURL =	$_SERVER["REQUEST_URI"];
				$errorContent=$strSiteName.' Server has found internal error. Please, try after some time or contact administrator.';
				echo $errorContent;			
				MedGeneral::sendErrorMail($errorContent,true);								
				exit;
			}
			else
			{
	
				$objGeneral->raiseError($errorCode,$errorMessage,$_SERVER["SCRIPT_NAME"],$errorDesc);
			}	
			return true;										
		} 
		else
			return false;										
	}

  
	function isConnected()
	{
		return $this->objarrdb->isConnected();		
	}

  
	function executeSelect ($strSql)
	{
		$objResult=$this->objarrdb->getAll($strSql);	
		if($this->isError($objResult))				
			exit;		
		return $objResult;							
	}

  
	function executeQuery($strSql)
	{
		
		$objResult=$this->objarrdb->query($strSql);	
		if($this->isError($objResult))				
			exit;			
		return $objResult;							
	}	
  	
	function setAutoCommit($blnAutoCommit=true)
	{
		$this->blnAutoCommit=$blnAutoCommit;
		$this->objarrdb->autoCommit($blnAutoCommit);						
	}

  	
	function commitTrans()
	{
		$this->objarrdb->commit();						
	}
  	
	function rollbackTrans()
	{
		$this->objarrdb->rollback();					
	}
	
  	
  function escapeString($strValue)
    {
    	
		return trim($strValue);
    }
}
?>