
<?php


class MedDB
{
  
	var $objarrdb;
	var $blnAutoCommit;
	function __construct()
	{
			$GLOBALS["objDb"] = $this;
			$this->blnAutoCommit = true;
	}
	
	function getDBObject()
	{
		return $GLOBALS["objDb"];
	}
  
	function connect($dsn,$options=array())
	{
		$this->strUserName = $dsn['username'];
		$this->strPassword = $dsn['password'];
		try
		{ 
			$this->objClient = new SoapClient(null, array('location' => "http:
			
		}  
		catch (SoapFault $fault) {
			$fault->faultstring .= "<br>Could not connect to the server (Check Internet Connection or URL)";
			$this->raiseError($fault->faultcode,$fault->faultstring,$fault->faultactor);
		}
		
	}
  
	function disconnect()
	{
	
	}

  
	function raiseError($strCode,$strMessage,$strDesc)
	{
		
			$errorData = explode("^",$strMessage);
			$errorCode =  $errorData[0];
			$errorMessage = $errorData[1];
			$errorDesc = $errorData[2];
			$objGeneral = MedGeneral::getGeneralObject();
			$errorDesc = str_replace("[nativecode","<br>[nativecode",$errorDesc);					
			if ($objGeneral == null )
			{
				$errorContent='<table width="98%" border="0" cellpadding="0" cellspacing="0">
								<tr>
								  <td width="29%" align="right" class="error-td03"><font class="error-bold">Http Host:</font>&nbsp;</td>
								  <td width="71%" align="left" class="error-td04">'.$strSiteName.'</td>
								</tr>
				 				<tr>
				  					<td class="comnbgmid" align="center"  valign="middle" height="300">				
				  								<font color=red><b>'.$errorCode."  ".$errorMessage." ".$errorDesc.'<b></font>
				  					</td>
								</tr>
							<table>';
				echo $errorContent;			
				exit;
			}
			else
			{
	
				$objGeneral->raiseError($errorCode,$errorMessage,$_SERVER["SCRIPT_NAME"],$errorDesc);
			}	
			return true;										
	}

  
	function isConnected()
	{
		if ($this->objClient == null )
			return false;
		else
			return true;	
	}

  
	function executeSelect ($strSql)
	{
		try
		{
			return $this->objClient->executeSelect($strSql); 
		}
		catch (SoapFault $fault) {
			$this->raiseError($fault->faultcode,$fault->faultstring,$fault->faultactor);
		}
	}

  
	function executeQuery($strSql)
	{
		try
		{
			$this->objClient->executeQuery($strSql); 
		}
		catch (SoapFault $fault) {
			$this->raiseError($fault->faultcode,$fault->faultstring,$fault->faultactor);
		}
	}	
  	
	function setAutoCommit($blnAutoCommit=true)
	{
	}

  	
	function commitTrans()
	{
		
	}
  	
	function rollbackTrans()
	{
		
	}
	
  	
  function escapeString($strValue)
    {
    	
		return $strValue;
    }
}
?>