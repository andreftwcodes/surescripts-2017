<?php


class MedListColumn
{
	const TYPE_TEXT = 1;			
	const TYPE_LINK = 2;			
	const TYPE_CONDITIONAL = 3;		
	const TYPE_EVALUATED_EXPR = 4;	
	const TYPE_TEXTBOX = 5;			
	const TYPE_COMBO = 6;			
	const TYPE_CHECKBOX = 7;		
	const TYPE_FILE	= 8;			

	public $intType;				
	public $strHeaderText;			
	public $arrFieldArray;    		
	public $blnIsSortable;			
	public $strSortField;			
	public $strDisplayExpr;			
	public $arrConditionalValuesArray;	
	public $strFieldName;				
	public $intTextBoxWidth;			
	public $strTextFieldType;			
	public $arrComboOptions;		
	public $strHref;				
	public $strOnClick;				
	public $blnAddKeys;				

	public $intWidth;				
	public $strHorizontalAlign;		
	public $strCssClass;			
	public $strVerticalAlign='Top';		

	public $intCollectionIndex;		
	public $strCheckType;			
	public $blnExport;				
	
	
	function __construct($intType, $strHeader, $arrFieldList, $intWidth=null, $strHAlign=null)
	{
		$this->intType = $intType;
		$this->strHeaderText = $strHeader;
		$this->arrFieldArray = $arrFieldList;
		$this->intWidth = $intWidth;
		$this->strHorizontalAlign = $strHAlign;
		$this->blnExport = false;
		
		if (count($arrFieldList)>1)
			$this->blnAddKeys = true;
		else	
			$this->blnAddKeys = false;
		switch ($this->intType)
		{
			case MedListColumn::TYPE_TEXT:
			case MedListColumn::TYPE_LINK:
			case MedListColumn::TYPE_CONDITIONAL:
			case MedListColumn::TYPE_TEXTBOX:		
			case MedListColumn::TYPE_CHECKBOX:					
			case MedListColumn::TYPE_FILE:					
			break;
			default:
				$this->blnIsSortable = false;
		}
	}

	
	
	
	function getSortColumn()
	{
		$strSortCol = null;
		if ($this->blnIsSortable)

		{
			if ($this->strSortField)
				$strSortCol = $this->strSortField;
			else $strSortCol = $this->arrFieldArray[0];
		}
		return $strSortCol;
	}

	

	function parseColumn($arrData, $strExtraAttributes=null)
	{		
		$strGeneratedContent = $this->parseColumnText($arrData,$strExtraAttributes);
		if($this->blnExport)
		{
			$strGeneratedContent = str_replace("\r\n","",$strGeneratedContent);
			echo $strGeneratedContent."\t";	
		}
		else
		{			
			echo "<td";
			if ($strExtraAttributes)
				echo " ".$strExtraAttributes;
			if ($this->strHorizontalAlign)
				echo " align=".$this->strHorizontalAlign;
			if ($this->strVerticalAlign)
				echo " valign=".$this->strVerticalAlign;
			if ($this->strCssClass)
				echo " class=".$this->strCssClass;
			if ($this->intWidth)
				echo " width=".$this->intWidth;
			echo ">".($strGeneratedContent ? $strGeneratedContent : "&nbsp;")."</td>";
		}	
	}

	
	function parseColumnText($arrData,$intRowIdx=0)
	{
		$strGeneratedContent = "";	
		switch($this->intType)
		{
			case MedListColumn::TYPE_TEXT:
					$strGeneratedContent = $arrData[$this->arrFieldArray[0]];
					$strGeneratedContent = $this->formatContent($strGeneratedContent);
					break;

			case MedListColumn::TYPE_FILE:
					if($this->blnExport==false)
					{
						$objPage = MedPage::getPageObject();
						$strName = $objPage->generateHtmlControlName("FILE",null,null,$this->strFieldName); 
						$strHiddenName = $objPage->generateHtmlControlName("HIDDEN",null,null,$this->strFieldName); 					
						$strGeneratedContent ="<table cellpadding='0' border='0' cellspacing='0'width=100% ><tr><td  align=left>";
						$strGeneratedContent.="<input type=file name='".$strName."[]' type='".$strName."[]'>";
						$strGeneratedContent.="<input name=".$strHiddenName."[] value='".$this->strDisplayExpr."' type=hidden ></td>";
						$strLarge="";					
						$strFile=$arrData[$this->arrFieldArray[0]];
						
						$strFilePara=explode(":",$this->strDisplayExpr);
						if(strtoupper($strFilePara[1])=='IMG')
							$strLarge="large\\";
						
						if(MedGeneral::getSettings($strFilePara[2])!='' && file_exists(MedGeneral::getSettings($strFilePara[2]).$strLarge.$strFile) && !empty($strFile))
						{
							$strFilePath=MedGeneral::getSettings($strFilePara[2])."large/".$strFile;
							$strImgType=$this->strDisplayExpr.":".$strFile.":".$this->strFieldName.$intRowIdx;
							$strGeneratedContent .="<td ><div id='div_".$this->strFieldName.$intRowIdx."' ><a href='javascript:void(0)' onclick=\"popup('index.php?file=med_imgpopup&imgtype=".$strImgType."','800','600')\">".$strFile."</a>";
							$strGeneratedContent .="&nbsp;|&nbsp;<a href='javascript:void(0)' onclick=\"popup('index.php?file=med_remove_file&imgtype=".$strImgType."','200','200')\">Remove</a></div></td>";
						}
						$strGeneratedContent.= "</tr></table>";
					}
					else
					{
						$strGeneratedContent=$arrData[$this->arrFieldArray[0]];
					}	
					break;
			case MedListColumn::TYPE_LINK:
					if($this->blnExport==false)
					{	
						if ($this->strDisplayExpr)
							$strGeneratedContent = $this->parseText($this->strDisplayExpr, $this->arrFieldArray, $arrData);
						else
							$strGeneratedContent = $arrData[$this->arrFieldArray[0]];
						$strGeneratedContent = $this->formatContent($strGeneratedContent);
						if ($strGeneratedContent)
						{
							
							$strOnClick = $this->parseText($this->strOnClick, $this->arrFieldArray, $arrData);
							if ($this->strHref != null )
							{
								$strLink = $this->parseText($this->strHref, $this->arrFieldArray, $arrData);
								$strGeneratedContent = "<a href='".$strLink."'".($strOnClick ? $strOnClick : "").">".$strGeneratedContent."</a>";
							}	
							else if ($strOnClick != null )
									$strGeneratedContent = "<a href='JavaScript::void(0);'". ($strOnClick ? $strOnClick : "").">".$strGeneratedContent."</a>";
							
						}
					}
					else
					{
						if ($this->strDisplayExpr)
							$strGeneratedContent = $this->parseText($this->strDisplayExpr, $this->arrFieldArray, $arrData);
						else
							$strGeneratedContent = $arrData[$this->arrFieldArray[0]];
						$strGeneratedContent = $this->formatContent($strGeneratedContent);					
					}	
					break;

			case MedListColumn::TYPE_CONDITIONAL:

					switch($this->strCheckType)
					{
						case "Val"	: 
									$strValueText = "";
									$strValueText = $this->arrConditionalValuesArray[$arrData[$this->arrFieldArray[0]]];
									$strGeneratedContent = $this->parseText($strValueText, $this->arrFieldArray, $arrData);
						break;
						case "Exp"	: 
									foreach( $this->arrConditionalValuesArray as $keys=>$value)
									{
										$keys=str_replace("{0}",$arrData[$this->arrFieldArray[0]],$keys);
										MedPage::addArray($arrTemp,$keys,$value);
									}
									foreach($arrTemp as $keys=>$value)
									{
										$keys = "(".$keys.")";
										@eval("\$keys = $keys;");
										if($keys)
										{
											$strGeneratedContent = $value;
											break;
										}
									}
						break;
					}
					break;

			case MedListColumn::TYPE_EVALUATED_EXPR:
					if ($this->strDisplayExpr)
						$strGeneratedContent = $this->parseText($this->strDisplayExpr, $this->arrFieldArray, $arrData);
					break;

			case MedListColumn::TYPE_TEXTBOX:
					if($this->blnExport==false)
					{
						$strValueText = "";
						$strValueText =  $arrData[$this->arrFieldArray[0]];
						$strGeneratedContent = $this->parseText($strValueText, $this->arrFieldArray, $arrData);
						
						$objPage = MedPage::getPageObject();
						$strName = $objPage->generateHtmlControlName("TEXT",null,null,$this->strFieldName); 

						if ($this->blnAddKeys)
						$hidden_ids="<input type='hidden' name=hid_".$this->strFieldName."[] value='".$arrData[$this->arrFieldArray[1]]."'>";
						
						if($this->intTextBoxWidth != "")
							$intMaxLength = "maxlength=".$this->intTextBoxWidth."";
						if($this->intTextBoxWidth != "")	
							$intSize = "size=".$this->intTextBoxWidth."";
						

						$strGeneratedContent	=	"<input type='text' class='comn-input' ".$intMaxLength." ".$intSize." name='".$strName."[]' value=\"".$strGeneratedContent."\" onKeyUp='checkType(this,\"".$this->strTextFieldType."\");' ".(($this->strOnClick==null || empty($this->strOnClick))?"":$this->strOnClick) ."  >".$hidden_ids;
					}
					else
					{
						$strValueText = "";
						$strValueText =  $arrData[$this->arrFieldArray[0]];
						$strGeneratedContent = $this->parseText($strValueText, $this->arrFieldArray, $arrData);
					}	
					break;
						
			case MedListColumn::TYPE_CHECKBOX:
					if($this->blnExport==false)
					{
						$strValueText = "";
						$strValueText =  $arrData[$this->arrFieldArray[0]];
						$strGeneratedContent = $this->parseText($strValueText, $this->arrFieldArray, $arrData);
						if ($this->isSelectedValue($strGeneratedContent))
							$strText="checked";
						else	
							$strText="";
						$objPage = MedPage::getPageObject();
						$strName = $objPage->generateHtmlControlName("CHECKBOX",null,null,$this->strFieldName); 

						if ($this->blnAddKeys)
							$hidden_ids = "<input type='hidden' name=hid_".$this->strFieldName."[] value='".$arrData[$this->arrFieldArray[1]]."'>";
						if ($this->strTextFieldType == null )
						$strGeneratedContent = "<input type='checkbox' name='".$strName."[]' value='".$intRowIdx."' class='checkbox' ".$strText." ".(($this->strOnClick==null || empty($this->strOnClick))?"":$this->strOnClick) ." >".$hidden_ids;
						else
						$strGeneratedContent = "<input type='checkbox' name='".$strName."[]' value='".$arrData[$this->arrFieldArray[1]]."' class='checkbox' ".$strText." ".(($this->strOnClick==null || empty($this->strOnClick))?"":$this->strOnClick) ." >".$hidden_ids;			
					}
					else
					{
						$strValueText = "";
						$strValueText =  $arrData[$this->arrFieldArray[0]];
						$strGeneratedContent = $this->parseText($strValueText, $this->arrFieldArray, $arrData);
					}	
					break;			
						
			case MedListColumn::TYPE_COMBO :
					if($this->blnExport==false)
					{
						$strValueText = "";
						$strValueText =  $arrData[$this->arrFieldArray[0]];
						if ($this->blnAddKeys)
						$hidden_ids="<input type='hidden' name=hid_".$this->strFieldName."[]  value='".$arrData[$this->arrFieldArray[1]]."'>";

						if (is_array($this->arrComboOptions))
						{	
							$strValueText = $this->generateComboContent($strValueText);
							$strGeneratedContent = "<select name='".$this->strFieldName."[]' ".(($this->strOnClick==null || empty($this->strOnClick))?"":$this->strOnClick) ." >".$strValueText."</select>".$hidden_ids;
						}
						else
						{
							$objPage = MedPage::getPageObject();
							$strControlCase =$this->arrComboOptions;
							$strName = $objPage->generateHtmlControlName("SELECT",null,null,$this->strFieldName)."[]"; 
							$strGeneratedContent = $objPage->generateCombobox($strControlCase,$strName,$strValueText);
							$strGeneratedContent .= $hidden_ids;
						}	
					}
					else
					{
						$strGeneratedContent = $arrData[$this->arrFieldArray[0]];
					}	
					break;
			default:
					$strGeneratedContent = "";
		}
		return $strGeneratedContent;
	}


	
	function isSelectedValue($strGeneratedContent)
	{
		$blnStatus = false;
		switch(trim(strtoupper($strGeneratedContent)))
		{
			case "YES" : case "TRUE" : case "1" : case "Y"  : case "T"  :
					$blnStatus = true;
					break;	
			default :
					$blnStatus = false;
					break;	
		}
		return $blnStatus;	
	}
	
	
	function generateComboContent($strSelectedValue)
	{
		$strOptionString="";
		foreach ($this->arrComboOptions as $strOptionKey => $strOptionValue) 
		{
			if (trim(strtoupper($strOptionKey)) == trim(strtoupper($strSelectedValue)))
				$strOptionString .= "<option value=".$strOptionKey." selected > ".$strOptionValue."</option>\n";
			else
				$strOptionString .= "<option value=".$strOptionKey." > ".$strOptionValue."</option>\n";	
		}
		return $strOptionString;
	}
	
	function formatContent($strGeneratedContent)
	{
		if (trim(strtoupper($this->strTextFieldType)) == "DATE" )	
		{
			$objGeneral = MedGeneral::getGeneralObject();
			$strDateFormat =$objGeneral->getSettings("DATE_FORMAT");

			if ((!empty($strDateFormat)) && (!empty($strGeneratedContent)))
			$strGeneratedContent = date($strDateFormat,strtotime($strGeneratedContent));
		}
		return $strGeneratedContent;
	}

	

	function parseText($strText, $arrFieldNames, $arrData)
	{
		for($intFields = 0;$intFields < count($arrFieldNames);$intFields++)
		{
			$strFind = "{".$intFields."}";
			$strValue = $arrData[$arrFieldNames[$intFields]];
			$strText = str_replace($strFind,$strValue,$strText);

		}
		return $strText;
	}
}
?>