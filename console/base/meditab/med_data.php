<?php



require_once("med_sql.php");
require_once("med_datalist.php");
class MedData extends  MedSQL {

	
	private $strTableName;
	
	
	private $arrPkField;
	
	
	private $arrField;
	
	
	public $arrNotUniqueFields;
	
	
	private $strWhereClause;

	
	private $strAutoIncField;

	
	private $intAutoId;
	
	
	public	$arrRPAFields;
	
	
	public	$blnActionMessage	=	true;
	
	
	
	function __construct($strTableName=null, $arrPk=null, $arrField=null, $strAutoIncFld=null)
	{
		$this->setProperty($strTableName,$arrPk,$arrField,$strAutoIncFld);
	}
	
	
	
	
	function setProperty($strTableName=null, $arrPk=null, $arrField=null, $strAutoIncFld=null)
	{
		if (null != $strTableName) $this->strTableName = $strTableName;

		
		if ($arrPk != null)	$this->arrPkField = (is_array($arrPk)?$arrPk:array($arrPk));

		
		if ($arrField != null) $this->arrField =(is_array($arrField)?$arrField:array($arrField));
		else $this->arrField = array();
			
		
		if ($strAutoIncFld != null)	$this->strAutoIncField = $strAutoIncFld;
	}

	

	
	
	function getTableName()
	{
		return $this->strTableName;
	}
	
		
	
	function getPkFieldsArray()
	{
		return $this->arrPkField;
	}
	
	
		
		
	function getFieldsArray()
	{
		return $this->arrField;
	}	


		
			
	function getFieldValue($strFieldName)
	{
		if (isset($this->{$strFieldName})) return $this->{$strFieldName};
		else return null;	
	}


	
		
	function getPrimaryKey()
	{
		return $this->arrPkField[0];
	}


	
		
	function getAutoId()
	{
		return $this->intAutoId;
	}

	
		
	function setAutoId($intAutoId)
	{
		$this->intAutoId=$intAutoId;
	}

	

	
		
	function getListObject()
	{
		$objDataList = new MedDataList();	
		$strFieldList = "";
		
		for($intField=0;$intField<count($this->arrField);$intField++)
		{
			$strFieldList .= $this->arrField[$intField].",";
		}		
		$strFieldList = trim($strFieldList,",");
		$objDataList->arrPrimaryKeyFields = $this->arrPkField;
		$objDataList->strSQLSelectFields  =	$strFieldList;
		$objDataList->strSQLFromTables 	  = $this->strTableName;
		$objDataList->strSQLWhereCriteria = null;
		$objDataList->strSQLOrderBy 	  = $this->arrPkField[0];
		return $objDataList; 
	}
	
	
			
	function getRecord($intPkId = null)
	{
		if ($intPkId != null )
		{
			$strPkKey = $this->getPrimaryKey();
			$this->{$strPkKey} =$intPkId; 
		}
		
		$this->loadByPK($this);
	}	
	
			
	
	function getKeyColumn()
	{
		if($this->arrField!=NULL)
		{
			$arrTemp = array();
			for($intKeyIndex=0;$intKeyIndex<count($this->arrField);$intKeyIndex++)	
			{
				$arrKey=$this->arrField[$intKeyIndex];
				if (isset($this->{$arrKey}))		
					$arrTemp [$arrKey] = $this->{$arrKey};
				else	
					$arrTemp [$arrKey] = null;	
			}
			return $arrTemp;
		}
		else MedError::raiseError("FIELD_ARR_NOT_SET","MedData->getKeyColumn");
	}
	
			
		
	function getWhereClause()
	{
		return $this->strWhereClause;
	}	

	
		
		
	function setPrimaryKey()
	{
		if($this->arrPkField[0]!=NULL)
		{
			$strPkField = $this->arrPkField[0];
			$strKeyValueField = 'hid_'.$strPkField;
			$this->{$strPkField} = $_REQUEST[$strKeyValueField];
			$this->strWhereClause = $strPkField . "='".$_REQUEST[$strKeyValueField]."'";
		}
		else MedError::raiseError("PK_FIELD_NOT_SET","MedData->setPrimaryKey");
	}
	
			
	
	function setFieldValue($strField,$strValue)
	{
		if($strField!=NULL && $strField!="")
		{
			
			if (!in_array($strField,$this->arrField)) $this->arrField[] = $strField;	
			$this->{$strField} = $strValue;	
		}
		else MedError::raiseError("FIELD_NOT_SET","MedData->setFieldValue");
	}
	
			
	
	function setPKValue($strValue)
	{
		if($this->arrPkField[0]!=NULL)
		{
			$strPkField = $this->arrPkField[0];	
			$this->{$strPkField} = $strValue; 
		}
		else MedError::raiseError("PK_FIELD_NOT_SET","MedData->setPKValue");
	}

			
	
	function setWhere($strWhere)
	{
		$this->strWhereClause = $strWhere;
	}

			
	
	function setFields($arrFieldValues)	
	{
		if (is_array($arrFieldValues))	
		{
			foreach($arrFieldValues as $arrKey => $arrValue)	
			{
				if (!in_array($arrKey,$this->arrField)) $this->arrField[] =	$arrKey;
				$this->{$arrKey} = $arrValue;	
			}
		}
		else MedError::raiseError("FIELD_VALUE_ARR_NOT_SET","MedData->setFields");
	}
	
		
		
	function update()
	{
		$this->updateByPK($this);
	}
	
		
		
	function updateRows($arrFieldValue, $strWhere)
	{
		return $this->updateByCond($this,$arrFieldValue, $strWhere);
	}

		
			
	function delete()
	{
		$this->deleteRecord($this);
	}

		
		
	function deleteRows($strWhere)
	{
		return $this->deleteByCond($this,$strWhere);
	}
	
		
			
	function insert()
	{
		return $this->insertRecord($this);
	}
	
		
			
	function getAll($strFieldList=null, $strWhere=null, $strOrderBy=null, $blnOrder=false)
	{
		return $this->getAllRecords($this,$strFieldList,$strWhere,$strOrderBy,$blnOrder);
	}
	
		
		
	function checkAddData($arrInsert)
	{
		foreach ($arrInsert as $index => $val)
		{
		  
		  if(!eregi("(^Ap.*|^Dt.*|^In.*|^Dc.*|^Zp.*|^Em.*|^Fx.*|^Ta.*|^Rslt.*|^Rchk.*|^Rrad.*|^slt.*|^chk.*|^rad.*|^Ur.*|^hidin.*|^Ft.*)",$index)) 
		  {
		  		ereg("^Ph", $index);
				unset($arrInsert[$index]);
		  }	
		}
		return $arrInsert;
	} 

			
			
	function validateTableProperty($strMode="A")
	{
		$strErrorDesc="";
		if ($this->strTableName == NULL ) MedError::raiseError("TABLE_NAME_NOT_SET","MedData->validateTableProperty");
		
		if (trim(strtoupper($strMode)) != "A")
		{
			$strPkField = $this->arrPkField[0];
			if (empty($strPkField)) MedError::raiseError("PK_FIELD_NOT_SET","MedData->validateTableProperty");
		}
	}
	
		
		
	function extractForm()
	{				
		$arrInsert = $_REQUEST;
		$arrRet = $this->checkAddData($arrInsert);	
		foreach($arrRet as $arrKey => $arrValue)	
		{
			$arrTemp=explode("_",$arrKey);
			if(ereg("(Dt.*)",$arrKey)) 
			{
				if($arrValue!="")
				{
					$arrValue=split("-",$arrValue);
					$arrValue=@date("Y-m-d",@mktime(0,0,0,$arrValue[0],$arrValue[1],$arrValue[2])); 
				}
				else
				{
					$arrValue= null;
				}	
			}
			elseif(ereg("(pas)",$arrTemp[0]))
			{
				$arrValue=md5($arrValue);
			}
			$chSeparat = "_";
			$arrKeys = substr($arrKey,strpos($arrKey,$chSeparat)+1);	
		
			if((ereg("(Dt.*)",$arrKey)) && $arrValue=='null')$strValues.="".$arrValue."";	
			else $strValues =$arrValue;					

			if((ereg("(Ph.*)",$arrKey)))
			{	
				$intTempValue[0] = substr($arrValue,0,3);
				$intTempValue[1] = substr($arrValue,3,3);				
				$intTempValue[2] = substr($arrValue,6,4);
				$intTempValue[3] = (substr($arrValue,10)=="")?"NULL":substr($arrValue,10);				
				for($intPhId=0;$intPhId<4;$intPhId++)
				{
					$this->arrField[] = $arrKeys.($intPhId+1);						
					$this->{$arrKeys.($intPhId+1)} = $intTempValue[$intPhId];		
				}
			}
			elseif((ereg("(Fx.*)",$arrKey)))
			{	
				$intTempValue[0] = substr($arrValue,0,3);
				$intTempValue[1] = substr($arrValue,3,3);				
				$intTempValue[2] = substr($arrValue,6,4);
				for($intFxId=0;$intFxId<3;$intFxId++)
				{
					$this->arrField[] = $arrKeys.($intFxId+1);						
					$this->{$arrKeys.($intFxId+1)} = $intTempValue[$intFxId];		
				}
			}
			elseif(!(ereg("(hidin.*)",$arrKey) && $strValues==NULL))
			{
				
				if (!in_array($arrKeys,$this->arrField)) $this->arrField[] = $arrKeys;	
				$this->{$arrKeys} = $strValues;											
			}			
		}
	}

		
	function assign($strTableName,$strPkFieldName,$strAssignKey,$arrValue)
	{
			$pkField = $strPkFieldName;
			$strPkValue = MedPage::getRequest("hid_".$strPkFieldName);
			if (empty($strPkValue))
				$strPkValue =$this->intAutoId;
		
			$strFileds = $pkField.",".$strAssignKey;
			$objTemp = new MedData($strTableName,null,null,null); 	
			$strWhere = $pkField." = ".$strPkValue;
			
			$objTemp->deleteRows($strWhere);
			$objTemp->setFieldValue($pkField,$strPkValue);
				
			for($intValue=0;$intValue<count($arrValue);$intValue++)
			{
					$objTemp->setFieldValue($strAssignKey,$arrValue[$intValue])	;
					$objTemp->insert();
			}
	}

		
		
	
	function checkWhetherUnique($arrFieldName,$strAction,$arrFieldValue=null)
	{
		if($arrFieldName==NULL) MedError::raiseError("FIELD_ARR_NULL","MedDate->checkWhetherUnique"); 
		if($arrFieldValue!=NULL)
		{
			
			if(count($arrFieldName)!=count($arrFieldValue)) MedError::raiseError("ARR_CNT_NOT_SAME","MedData->checkWhetherUnique");
			$arrRet = $arrFieldValue;
		}
		else $arrRet = $this->checkAddData($_REQUEST);	

		for($intFieldName=0; $intFieldName<count($arrFieldName); $intFieldName++) 
		{
			if($arrFieldValue!=NULL)
			{
				$strFieldName = $arrFieldName[$intFieldName];
				$strWhere="".$arrFieldName[$intFieldName]." IN ('".$arrRet[$intFieldName]."')";
				
			}
			else
			{
				foreach($arrRet as $arrKey => $arrValue)
				{
					$chSeparat = "_";
					$arrKeys = substr($arrKey,strpos($arrKey,$chSeparat)+1);					
					if($arrKeys==$arrFieldName[$intFieldName])
					{
						$strFieldName = $arrKeys;
						$strWhere="".$arrKeys." IN ('".$arrValue."')";
					}
				}
			}
			
			if(trim(strtoupper($strAction))=="E") 
			{
				$strPkField = $this->getPrimaryKey();
				if(empty($strPkField)) MedError::raiseError("PK_FIELD_NOT_SET","MedData->checkWhetherUnique");
				$intPkFieldValue = $_REQUEST["hid_".$this->getPrimaryKey()];
				if(empty($intPkFieldValue)) MedError::raiseError("PK_FIELD_VALUE_NOT_SET","MedData->checkWhetherUnique");
				$strWhere.=" and ".$strPkField."!='".$intPkFieldValue."'";
			}
			if($strWhere == NULL) MedError::raiseError("WHERE_CONDITION_NOT_SET","MedData->checkWhetherUnique");	
			
			$rsUniqueCheck=$this->getAll("*", $strWhere, null, false);	
			
			if(count($rsUniqueCheck)!=0)	
			{
				$this->arrNotUniqueFields[] = $strFieldName;	
			}	
		}
		
		
		if($this->arrNotUniqueFields==NULL) return true;
		else return false;
	}
	
		
			
	function updateCheckBoxField($strFieldName,$arrUpdatedField,$strTrueValue,$strFalseValue)
	{
		if ($strFieldName)
		{
			if (!is_array($arrUpdatedField))	
			$arrUpdatedField = array($arrUpdatedField);
			
			$objPage = MedPage::getPageObject();	
				
			$arrCheckBoxControl = $objPage->generateHtmlControlName("CHECKBOX","","",$strFieldName);
			if (!array_key_exists($arrCheckBoxControl,$_REQUEST))
			{
				$objPage->setRequest($arrCheckBoxControl,array());	
			}
			$arrTemp = $objPage->getRequest($arrCheckBoxControl);			
			$blnHiddenFound=false;	
			for($intIdx=0;$intIdx<count($arrUpdatedField);$intIdx++)
			{
				$strHidControl = $objPage->generateHtmlControlName("hidden","","",$arrUpdatedField[$intIdx]);
				if(array_key_exists($strHidControl,$_REQUEST))	
				{
					$arrPkId = MedPage::getRequest($strHidControl);
					$blnHiddenFound=true; 
					break;		
				}	
			}
			if(!$blnHiddenFound)	
				MedError::raiseError("HIDDEN_BASED_NOT_SET");
			$arrCheckValue= array();	
			for($intPk=0;$intPk<count($arrPkId);$intPk++)
			{
				$strKey = $arrPkId[$intPk];
				if (in_array($strKey,$arrTemp))
					$arrCheckValue[] =$strTrueValue;
				else
					$arrCheckValue[] =$strFalseValue;					
			}	
			$arrCheckBoxControl = $objPage->generateHtmlControlName("TEXT","","",$strFieldName);
			$objPage->setRequest($arrCheckBoxControl,$arrCheckValue);
		}
	}	
	
		
		
	function updateGrid($strBaseKey,$strBaseColumn,$arrUpdatedField)
	{
		if (!is_array($arrUpdatedField))	
			$arrUpdatedField = array($arrUpdatedField);

		$objPage = MedPage::getPageObject();	
		for($intIdx=0;$intIdx<count($arrUpdatedField);$intIdx++)
		{	
			if (array_key_exists($objPage->generateHtmlControlName("text","","",$arrUpdatedField[$intIdx]),$_REQUEST))
				$arrTextControl[$intIdx] = $objPage->generateHtmlControlName("text","","",$arrUpdatedField[$intIdx]);
			else if(array_key_exists($objPage->generateHtmlControlName("select","","",$arrUpdatedField[$intIdx]),$_REQUEST))
				$arrTextControl[$intIdx] = $objPage->generateHtmlControlName("select","","",$arrUpdatedField[$intIdx]);
			else if(array_key_exists($objPage->generateHtmlControlName("checkbox","","",$arrUpdatedField[$intIdx]),$_REQUEST))
				$arrTextControl[$intIdx] = $objPage->generateHtmlControlName("checkbox","","",$arrUpdatedField[$intIdx]);
			else if(array_key_exists($objPage->generateHtmlControlName("radio","","",$arrUpdatedField[$intIdx]),$_REQUEST))
				$arrTextControl[$intIdx] = $objPage->generateHtmlControlName("radio","","",$arrUpdatedField[$intIdx]);
			else
				MedError::raiseError("UPDATE_FIELD_NOT_SET","MedSQL->updateGrid");
			
			$arrSeqNo[$intIdx] = $objPage->getRequest($arrTextControl[$intIdx]);
			
			if (empty($arrSeqNo[$intIdx]) || !is_array($arrSeqNo[$intIdx]))	
				MedError::raiseError("UPDATE_FIELD_NOT_SET");
		}
		$blnHiddenFound=false;	
		for($intIdx=0;$intIdx<count($arrUpdatedField);$intIdx++)
		{
			$strHidControl = $objPage->generateHtmlControlName("hidden","","",$arrUpdatedField[$intIdx]);
			if(array_key_exists($strHidControl,$_REQUEST))	
			{
				$arrPkId = MedPage::getRequest($strHidControl);
				$blnHiddenFound=true; 
				break;		
			}	
		}

		if(!$blnHiddenFound)	
		  	MedError::raiseError("HIDDEN_BASED_NOT_SET");

		if (empty($strBaseColumn))	
			$blnCheckBox = false;	
		else	
			$blnCheckBox = true;	


		if($blnCheckBox)	
		{
			$strCheckBoxControl = $objPage->generateHtmlControlName("checkbox","","",$strBaseColumn);
			$arrChkId = MedPage::getRequest($strCheckBoxControl);			
		}	
		else
			$arrChkId = $arrPkId;

		if ($arrChkId == null )	
			return ;

		if (($arrChkId != null ) && (!is_array($arrChkId))) 
			MedError::raiseError("BASED_CHECKBOX_FIELD_NOT_SET");

		if (count($arrChkId) == 0) 	
			return;
	


	
		for($intCount=0;$intCount<count($arrChkId);$intCount++)
		{
			if (isset($arrFieldValues))	 	
				unset($arrFieldValues);		
			$arrFieldValues = array();
			if($blnCheckBox)	
			{
				for($intIdx=0;$intIdx<count($arrUpdatedField);$intIdx++)
					$objPage->addArray($arrFieldValues,$arrUpdatedField[$intIdx],$arrSeqNo[$intIdx][$arrChkId[$intCount]]);
				$strWhereCond = $strBaseKey." = ".$arrPkId[$arrChkId[$intCount]]; 
			}
			else	
			{
				for($intIdx=0;$intIdx<count($arrUpdatedField);$intIdx++)
					$objPage->addArray($arrFieldValues,$arrUpdatedField[$intIdx],$arrSeqNo[$intIdx][$intCount]);
				$strWhereCond = $strBaseKey." = ".$arrPkId[$intCount]; 
			}	
			$this->updateRows($arrFieldValues,$strWhereCond); 
		}
	}
	
	
		
	function setArrRPAFields($strFieldsUnset)
	{
		$this->arrRPAFields	=	explode(",",$strFieldsUnset);
	}
	
	
		
	function unsetPAFields($arrFields)
	{
		for($intFields  = 0; $intFields < count($arrFields); $intFields++)
		{
			$blnFlag = false; 
			for($intField=0;$intField<count($this->{arrField});$intField++)
			{
				if(trim($arrFields[$intFields]) == trim($this->{arrField}[$intField]))
				{
					
					$blnFlag = true; 
					if(array_key_exists($arrFields[$intFields],$this))	unset($this->{$arrFields[$intFields]});
				}
				if($blnFlag)
				{
					
					$this->{arrField}[$intField] = $this->{arrField}[$intField+1];
					if($intField == count($this->{arrField})-1)	unset($this->{arrField}[$intField]);
				}
			}
		}
	}
	
		
			
	
	function performAction($strAction,$strWhere,$blnFormAction=true,$arrUpdatedField=null,$strBaseColumn=null,$strFilePara=null)
	{
		$this->validateTableProperty($strAction); 
		if(!empty($strAction))
		{
			
			$objPage = MedPage::getPageObject();
			switch(trim(strtoupper($strAction)))
			{
				case "A" : 
						
						if($blnFormAction == true) $this->extractForm(); 
						
						if($this->arrRPAFields != NULL)	$this->unsetPAFields($this->arrRPAFields);

						if($this->arrField==NULL) MedError::raiseError("FIELD_ARR_NOT_SET","MedData->performAction"); 
						$strExtraValidate=$objPage->getRequest('hid_EXTRA_FIELD_VALIDATION');
						$strMessage="";
						if(!empty($strExtraValidate)) $strMessage=$this->checkExtraValidation($strAction,$strExtraValidate);							
						
						
						if(empty($strMessage)) $this->insert(); 
						break;
	
				case "E" : 
						if ($strWhere == null) $this->setPrimaryKey();	
						
						
						if($blnFormAction == true) $this->extractForm();

						if($this->arrRPAFields != NULL)	$this->unsetPAFields($this->arrRPAFields);

						$arrFieldValues = $this->getKeyColumn(); 
						
						if($strWhere != "") $this->setWhere($strWhere);
						
						$strWhereCond = $this->getWhereClause(); 
						
						if ($strWhereCond == NULL) MedError::raiseError("WHERE_CONDITION_NOT_SET","MedData->performAction");	
						if ($arrFieldValues == NULL) MedError::raiseError("FIELD_VALUE_ARR_NOT_SET","MedData->performAction");	

						$strExtraValidate=$objPage->getRequest('hid_EXTRA_FIELD_VALIDATION');
						$strMessage="";
						if(!empty($strExtraValidate)) $strMessage=$this->checkExtraValidation($strAction,$strExtraValidate);							
						
						
					
						if(empty($strMessage)) $this->updateRows($arrFieldValues,$strWhereCond);	
						break;
							
				case "D" : case  "U" :
						
						if($strWhere != NULL) $strWhereCond = $strWhere;
						else if($this->getWhereClause() != NULL) $strWhereCond = $this->getWhereClause();
						else
						{
							
							
							$strListName = $objPage->getRequest("hid_listname");
							$arrSelId = MedQuickList::GetSelectedPKValues($strListName);
							
							if(is_array($arrSelId))
								$strKeyList = implode(",",$arrSelId);
							else
								$strKeyList = $arrSelId;
								
							
							if(MedPage::getRequest("hid_button_id"))
							{
								$rsButtons=$objPage->getButtons('id',MedPage::getRequest("hid_button_id"));
								$strCheckRef=$rsButtons[0]['check_ref'];
								$strCascadeAction=$rsButtons[0]['cascade_action'];
							}
							else
							{
								$strCheckRef=MedPage::getRequest("check_ref");
								$strCascadeAction=MedPage::getRequest("cascade_action");
							}
							
							
							if(!empty($strCheckRef))
							{
								
								$strMessage="";
								$strKeyList="";
								
								
								for($intSelId=0;$intSelId<count($arrSelId);$intSelId++)
								{
									$strMsg=$this->checkReference($strAction,$strCheckRef,$arrSelId[$intSelId],$intSelId);
									if($strMsg=='') $strKeyList.=$arrSelId[$intSelId].",";
									else $strMessage.=$strMsg."<br>";
								}
							
								if(!empty($strKeyList)) $strKeyList=substr($strKeyList,0,strlen($strKeyList)-1);
								if(!empty($strMessage)) $strMessage=substr($strMessage,0,strlen($strMessage)-4);
							}
							
							if(!empty($strKeyList))
							{			
								if(!empty($strCascadeAction))
								{
									$objDb = MedDB::getDBObject();
									$blnTransaction=true;
									$objDb->setAutoCommit(false);
									$this->performCascadeAction($strAction,$strCascadeAction,$strKeyList);
								}
								
								$strPk = $this->arrPkField[0];
								if($strPk != NULL) $strWhereCond = "".$this->arrPkField[0]." IN (".$strKeyList.")";
								else MedError::raiseError("PK_FIELD_NOT_SET","MedData->performAction");
							}
						}
						
						if(!empty($strKeyList) || empty($strMessage))
						{
							if($strWhereCond == NULL) MedError::raiseError("WHERE_CONDITION_NOT_SET","MedData->performAction");	
							if(trim(strtoupper($strAction)=="D")) 
							{
								if($strFilePara!="")
									$this->autoRemoveFile($strKeyList,$strWhereCond,$strFilePara);
									
								$this->deleteRows($strWhereCond);
							}	
							elseif(trim(strtoupper($strAction=="U")))	
							{
								$rsButtons=$objPage->getButtons('id',MedPage::getRequest("hid_button_id"));
								$arrKeyCol=explode(":",$rsButtons[0]['field_name_u']);
								
								$arrFieldValues=array($arrKeyCol[0]=>$arrKeyCol[1]);
								$this->updateRows($arrFieldValues,$strWhereCond);
							}
						}
						
						if($blnTransaction)
						{
							$objDb->commitTrans();
							$objDb->setAutoCommit(true);
						}
						
						break;
				case "MU" :
						if ($arrUpdatedField == null )
						{
							$rsButtons=$objPage->getButtons('id',MedPage::getRequest("hid_button_id"));
							$strTmpFieldList = $rsButtons[0]["field_name_u"];
							$arrTemp=explode(":",$strTmpFieldList);
							$arrUpdatedField =explode(",",$arrTemp[0]);
							if (count($arrTemp) >1) $strBaseColumn =$arrTemp[1];
						}	
						$this->updateGrid($this->getPrimaryKey(),$strBaseColumn,$arrUpdatedField);
						break;	
			}
			
			if(($strAction=="A" or $strAction=="E") && count($_FILES)>0 && empty($strMessage))
			{
				$this->uploadFile($strAction);
			}
			
			if (($strAction=="A" or $strAction=="E") && empty($strMessage))	
			{
				$extraAssign=$objPage->getRequest("hid_EXTRA_ASSIGN_PARAM");
				
				if (!empty($extraAssign))
				{
						$arrExtra = explode("@",$extraAssign);
						
						for($intExtra=0;$intExtra< count($arrExtra);$intExtra++)
						{
							$arrTemp = explode(":",$arrExtra[$intExtra]);
							$strTblName = $arrTemp[0];
							$strFieldName = $arrTemp[1];
							$strPkField = $arrTemp[2];
							$arrValueName="Mlslt_".$strFieldName;
							$arrValue = $objPage->getRequest($arrValueName);
							if (!is_array($arrValue))
								$arrValue=array();
							
							if ((!empty($strTblName)) && (!empty($strPkField)) && (!empty($strFieldName)) )
										$this->assign($strTblName,$strPkField,$strFieldName,$arrValue);
							else
										MedError::raiseError("NOT_ENOUGTH_DATA_FOR_ASSIGN_ACTION","MedData->performAction");	
					}
				}
			}
			
			
			if($this->blnActionMessage)
			{
				if($strMessage=='') $objPage->objGeneral->setDisplayMessage($strAction);
				else $objPage->objGeneral->setMessage($strMessage);
			}
		}
		else MedError::raiseError("ACT_NOT_SET","MedData->performAction");
	
	}
	
	
	function uploadFile($strAction)
	{
		
		if($strAction=="E")
		{
			$strPkField = $this->arrPkField[0];	
			$intFileId=$this->{$strPkField};
		}
		elseif($strAction="A")
		{
			
			$intFileId=$this->getAutoId();
		}

		
		$arrInsert=$_FILES;
		
		
		$objPage = MedPage::getPageObject();
		
		
		foreach ($arrInsert as $index => $val)
		{
			
			
			$strHidName="hid".substr($index,strpos($index,"_",0),strlen($index));
			$strUploadParam=$objPage->getRequest($strHidName);
			$arrUploadParam=explode(":",$strUploadParam);
			$strPkField=$arrUploadParam[0];
			
			$strUploadPath=$objPage->objGeneral->getSettings($arrUploadParam[2]);
			if(trim(strtoupper($arrUploadParam[1]))=="IMG") $strFinalUploadPath=$strUploadPath."large/";
			else $strFinalUploadPath=$strUploadPath;
			
			$strImageName=$objPage->objGeneral->uploadImage($index,$strFinalUploadPath,$intFileId);

			if($strImageName!="")
			{
				if($arrUploadParam[5] != "")
					$arrFieldValue=array(substr($index,strpos($index,"_",0)+1,strlen($index))=>$strImageName,$arrUploadParam[5]=>$_FILES[$index]['name']);
				else
					$arrFieldValue=array(substr($index,strpos($index,"_",0)+1,strlen($index))=>$strImageName);
					
				$strWhere="$strPkField=".$intFileId;
				MedData::updateRows($arrFieldValue, $strWhere);
			}
			
			if(trim(strtoupper($arrUploadParam[1]))=="IMG" && $strImageName!="")
			{
				$strSourceImage=$strUploadPath."large/".$strImageName;
				if($arrUploadParam[3]!="")
				{
					$strDestPath=$strUploadPath."middle/";
					$strImgParam=$objPage->objGeneral->getSettings($arrUploadParam[3]);
					$arrImgParam=explode(":",$strImgParam);
					$intNewWidth=$arrImgParam[0];
					$intNewHeight=$arrImgParam[1];
					$objPage->objGeneral->resizeImage($strSourceImage,$strDestPath,$strImageName,$intNewWidth,$intNewHeight);
				}
				if($arrUploadParam[4]!="")
				{
					$strDestPath=$strUploadPath."thumb/";
					$strImgParam=$objPage->objGeneral->getSettings($arrUploadParam[4]);
					$arrImgParam=explode(":",$strImgParam);
					$intNewWidth=$arrImgParam[0];
					$intNewHeight=$arrImgParam[1];
					$objPage->objGeneral->resizeImage($strSourceImage,$strDestPath,$strImageName,$intNewWidth,$intNewHeight);
				}
			}
			
		}
	}
	
		
	function autoRemoveFile($intTableId,$strWhereCond,$strUploadParam)
	{
		
		$objPage = MedPage::getPageObject();			
		$arrUploadParam=explode(":",$strUploadParam);				
		if(count($arrUploadParam)>=3)
		{
			$arrRecords=MedPage::getRecords($this->getTableName(),$this->arrPkField[0].",".$arrUploadParam[2], $strWhereCond, "", "", "", "");
			for($intRecord=0;$intRecord<count($arrRecords);$intRecord++)
			{
				$objPage->objGeneral->removeImage($arrUploadParam[0],$objPage->objGeneral->getSettings($arrUploadParam[1]),$arrRecords[$intRecord][$arrUploadParam[2]]);			
			}
		}	
	}
	
	
	function checkReference($strAction,$strReferenceParam,$arrFieldValue,$intRecordId)
	{
		$arrReference=explode("@",$strReferenceParam);
		
		
		if($intRecordId==0 || $intRecordId=='')
		{
			
			for($intReference=0;$intReference<count($arrReference);$intReference++)
			{
				$arrRefParam=explode(":",$arrReference[$intReference]);
				if(count($arrRefParam)!=4) MedError::raiseError("REF_PARAM_NOT_PROPER","MedData->checkReference");
			}
		}
		
		
		for($intReference=0;$intReference<count($arrReference);$intReference++)
		{
			$arrRefParam=explode(":",$arrReference[$intReference]);
			$strTblName=$arrRefParam[1];
			$strFieldNames="count(0) as cnt";
			
			
			if($arrFieldValue!="")
			{ 
				$strWhere= " find_in_set('".$arrFieldValue."',".$arrRefParam[2].")";
				
			}
			else MedError::raiseError("REF_PARAM_NOT_PROPER","MedData->checkReference");
			
			$arrRecords=MedPage::getRecords($strTblName, $strFieldNames, $strWhere, "", "", "", "");
			
			
			if($arrRecords[0]['cnt']>0)
			{
				
				$objPage = MedPage::getPageObject();
				$strMessage=$objPage->objGeneral->getSiteMessage($arrRefParam[3]);
				$strMessage=$this->parseMessage($strMessage,$arrFieldValue);
				return $strMessage;
			}
		}
		return "";
	}
	
	
	function performCascadeAction($strAction,$strCascadeAction,$strKeyList)
	{
		
		$arrCascadeAction=explode("@",$strCascadeAction);
		
		
		for($intCascadeAction=0;$intCascadeAction<count($arrCascadeAction);$intCascadeAction++)
		{
			$arrCascadeParam=explode(":",$arrCascadeAction[$intCascadeAction]);
			
			if(trim(strtoupper($strAction))=="D") $intParamCount=2;
			else $intParamCount=4;
			
			
			if(count($arrCascadeParam)!=$intParamCount) MedError::raiseError("CASCADE_PARAM_NOT_PROPER","MedData->performCascadeAction");
		}
		
		$strTableName=$this->getTableName();
		
		
		for($intCascadeAction=0;$intCascadeAction<count($arrCascadeAction);$intCascadeAction++)
		{
			$arrCascadeParam=explode(":",$arrCascadeAction[$intCascadeAction]);
			
			$this->strTableName=$arrCascadeParam[0];
			
			$strWhereCond=$arrCascadeParam[1]." in (".$strKeyList.")";
			
			
			switch(trim(strtoupper($strAction)))
			{
				case "D":
						$this->deleteRows($strWhereCond);
						break;
				case "U":
						$arrFieldValue=array($arrCascadeParam[2]=>$arrCascadeParam[3]);
						$this->updateRows($arrFieldValue, $strWhereCond);
						break;					
			}
		}
		
		
		$this->strTableName=$strTableName;
	}
	
	
	function parseMessage($strMessage,$intPkValue)
	{
		
		$arrMsg=preg_split("/\{/i",$strMessage);

		
		if(count($arrMsg)>1)
		{		
			
			for($intMsg=1;$intMsg<count($arrMsg);$intMsg++)
			{
				$arrFieldNames=preg_split("/\}/i",$arrMsg[$intMsg]);
				
				$strTblName=$this->getTableName();
				$strFieldName=$arrFieldNames[0];
				$arrPkFields=$this->arrPkField;
				$strWhere=$arrPkFields[0]."='".$intPkValue."'";
				
				$arrRecords=MedPage::getRecords($strTblName, $strFieldName, $strWhere, "", "", "", "");
				$arrValues[$intMsg]=$arrRecords[0][$strFieldName];
				$arrFields[$intMsg]="/{".$strFieldName."}/i";
			}
			
			$strMessage=preg_replace($arrFields,$arrValues,$strMessage);
			return $strMessage;
		}
		else return $strMessage;
	}
	
	
	function checkExtraValidation($strAction,$strExtraValidation)
	{
		$this->arrNotUniqueFields=array();
		
		$arrExtraValidation=explode("@",$strExtraValidation);
		
		
		for($intExtraValidation=0;$intExtraValidation<count($arrExtraValidation);$intExtraValidation++)
		{
			
			$arrValidationParam=explode(":",$arrExtraValidation[$intExtraValidation]);
			
			if(count($arrValidationParam)!=4) MedError::raiseError("EXTRA_VALIDATION_PARAM_NOT_PROPER","MedData->checkExtraValidation");
		}
		
		
		$strMessage="";
		
		
		for($intExtraValidation=0;$intExtraValidation<count($arrExtraValidation);$intExtraValidation++)
		{
			
			$arrValidationParam=explode(":",$arrExtraValidation[$intExtraValidation]);
			
			
			if(trim(strtoupper($strAction))==trim(strtoupper($arrValidationParam[1])) || trim(strtoupper($arrValidationParam[1]))=="B")
			{
				$strMessage.=$this->checkUniqueOrBlankValidation($arrValidationParam[2],$strAction,$arrValidationParam[0],$arrValidationParam[3]);
			}
		}

		if(!empty($strMessage)) $strMessage=substr($strMessage,0,strlen($strMessage)-4);
		return $strMessage;
	}
	
	
			
		
	
	function checkUniqueOrBlankValidation($strFieldName,$strAction,$strValidationType,$strFieldTitle)
	{

	
		if($strFieldName==NULL) MedError::raiseError("FIELD_ARR_NULL","MedDate->checkUniqueOrBlankValidation");
	
	
		$arrRet = $this->checkAddData($_REQUEST);
		
		
		$strMessage="";
		$strValidationType=trim(strtoupper($strValidationType));
		
		if($strValidationType=="BLANK" || $strValidationType=="UNIQUE")
		{
			foreach($arrRet as $arrKey => $arrValue)
			{
				$chSeparat = "_";
				$arrKeys = substr($arrKey,strpos($arrKey,$chSeparat)+1);					
				if($arrKeys==$strFieldName && trim($arrValue)=="")
				{
					$objPage = MedPage::getPageObject();
					$strMessage=$strFieldTitle." ".$objPage->objGeneral->getSiteMessage("REC_MAN_MSG")."<br>";		
					$this->arrNotUniqueFields[]=$strFieldName;
				}
			}
		}

		if(empty($strMessage) && $strValidationType=="UNIQUE")
		{
			foreach($arrRet as $arrKey => $arrValue)
			{
				$chSeparat = "_";
				$arrKeys = substr($arrKey,strpos($arrKey,$chSeparat)+1);					
				if($arrKeys==$strFieldName)
				{
					$strFieldName = $arrKeys;
					$strWhere="".$arrKeys." IN ('".$arrValue."')";
				}
			}
			
			if(trim(strtoupper($strAction))=="E") 
			{
				$strPkField = $this->getPrimaryKey();
				if(empty($strPkField)) MedError::raiseError("PK_FIELD_NOT_SET","MedData->checkUniqueOrBlankValidation");
				$intPkFieldValue = $_REQUEST["hid_".$this->getPrimaryKey()];
				if(empty($intPkFieldValue)) MedError::raiseError("PK_FIELD_VALUE_NOT_SET","MedData->checkUniqueOrBlankValidation");
				$strWhere.=" and ".$strPkField."!='".$intPkFieldValue."'";
			}
			if($strWhere == NULL) MedError::raiseError("WHERE_CONDITION_NOT_SET","MedData->checkUniqueOrBlankValidation");	
			
			$rsUniqueCheck=$this->getAll("*", $strWhere, null, false);	
			
			if(count($rsUniqueCheck)!=0)	
			{
				$objPage = MedPage::getPageObject();
				$strMessage=$strFieldTitle." ".$objPage->objGeneral->getSiteMessage("REC_EXIST_MSG")."<br>";	
				$this->arrNotUniqueFields[] = $strFieldName;	
			}
		}
	
	if(trim(strtoupper($strAction))=="E" && $strMessage!='')
	{
		$intPkvalue=$this->{$this->getPrimaryKey()};
		$objPage->setRequest($this->getPrimaryKey(),$intPkvalue);
	}
	return $strMessage;
	}
		

	
} 
?>