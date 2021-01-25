<?php


require_once("med_quicklist.php");
require_once("med_groupcolumn.php");

class MedGroupList extends MedQuickList
{
	public $objGroupColumn;				

	protected $arrGroupExprGroupTotal;	
	protected $arrGroupExprListTotal;		
	protected $strFirstGroupField;	
	protected $objFirstGroupColumn;	
 	public $strGroupHeaderFields;

 	var $strGroupFiledTitle;
 	var $strGroupHederText;
 	var $strGroupFooterText;
	var $intListGroupCount = 0;				

	const GROUP_RENDER_STYLE_SEPERATE_HEADER = 1;
	const GROUP_RENDER_STYLE_COLUMNAR = 2;
	const GROUP_RENDER_STYLE_NONE = 3;

	

	function _construct($intListId)
	{
		$this->intListId = $intListId;
		$this->arrGroupExprColumns = array();
		$this->arrObjGroupColumn = array();
		$this->strFirstGroupField = null;

	}

	

	public function getExprListTotal($strKeyName)
	{
		
	}

	

	public function setGroupOn($objColumn, $strGroupField, $intRenderStyle=1, $strSummaryText=null, $strListSummaryText=null)
	{
		$objGrpColumn = new MedGroupColumn($objColumn, $strGroupField, $intRenderStyle);
		$objGrpColumn->strGroupExprSummary = $strSummaryText;
		$objGrpColumn->strListExprSummary = $strListSummaryText;

		$this->objGroupColumn = $objGrpColumn;
		$this->strFirstGroupField[] = $strGroupField;
		$this->objFirstGroupColumn[] =  $objGrpColumn;
		return $objGrpColumn;
	}

	

	public function show($objListData)
	{
		if($this->intId=='')
			$this->intId=$this->strId;
		return parent::show($objListData);
	}

	

	function renderAllListRows($intTotalRecords)
	{
		if (!$this->objGroupColumn)
			parent::renderAllListRows($intTotalRecords);

		$strCurrentGroupColumn = $this->strFirstGroupField[count($this->strFirstGroupField)-1];

		$intRowIndex = 0;

		
		$this->calculateExpressions($intTotalRecords);

		$blnSummaryOnTop = $this->objGroupColumn->blnShowExprSummaryOnTop;
		$blnRenderGroupSummary = $this->objGroupColumn->blnShowGroupExprSummary;

		if ($blnSummaryOnTop)
			$this->renderListSummary();

		$intGroupIdentifier = 0;
		while ($intRowIndex < $intTotalRecords)
		{
			$arrCurrentRow = $this->arrContentData[$intRowIndex];

			$strCurrentGroupValue = $arrCurrentRow[$strCurrentGroupColumn];

				if(count($arrCondition)>0)
				{
					$objTemp = $this->objGroupColumn;
					$arrPrevCondition	=	$arrCondition;

					foreach($arrCondition as $intKey  => $strValue)
					{

						if($strValue['Value']!=$this->arrContentData[$intRowIndex][$strValue['Title']])
						{
							$arrCondition[$intKey]['Value']	=	$this->arrContentData[$intRowIndex][$strValue['Title']];
							$strFirstGroupValue = $arrCurrentRow[$this->strFirstGroupField[$arrCondition[$intKey]['id']]];
							$this->objGroupColumn = $this->objFirstGroupColumn[$arrCondition[$intKey]['id']];
							$this->beginGroupRender($arrCurrentRow,$blnFirst=true);
						}

					}
					$this->objGroupColumn = $objTemp;
				}
				else
				{
					$arrPrevCondition	=	$arrCondition;
					$objTemp = $this->objGroupColumn;

					for($intGroup=0;$intGroup<count($this->strFirstGroupField);$intGroup++)
					{

						$strFirstGroupValue = $arrCurrentRow[$this->strFirstGroupField[$intGroup]];
						$this->objGroupColumn = $this->objFirstGroupColumn[$intGroup];
						$this->beginGroupRender($arrCurrentRow,$blnFirst=true);
						$arrCondition[]	=	array("Value"=>$strFirstGroupValue,"Title"=>$this->strFirstGroupField[$intGroup],"id"=>$intGroup);

					}

					$this->objGroupColumn = $objTemp;
				}

			if ($blnRenderGroupSummary && $blnSummaryOnTop)
				$this->renderGroupSummary($arrCurrentRow, $intGroupIdentifier);
			$blnIsFirstRow = true;


			while (($intRowIndex < $intTotalRecords) && ($strCurrentGroupValue == $this->arrContentData[$intRowIndex][$strCurrentGroupColumn]))
			{
				$blnCond	=	false;
				foreach($arrCondition as $strValue)
				{
					if($strValue['Value']==$this->arrContentData[$intRowIndex][$strValue['Title']])
					{
						$blnCond	= true;
					}
					else
					{
						$blnCond	= false;
						break;
					}
				}
				if($blnCond)
				{
					$this->renderListRow($this->arrContentData[$intRowIndex], $intRowIndex, $blnIsFirstRow,$arrPrevCondition);
					$blnIsFirstRow = false;
					$arrPrevCondition	=	$arrCondition;
				}
				$intRowIndex++;

			}
			if ($blnRenderGroupSummary && !$blnSummaryOnTop)
				$this->renderGroupSummary($arrCurrentRow, $intGroupIdentifier);
			$intGroupIdentifier++;

		}


		if (!$blnSummaryOnTop)
			$this->renderListSummary();

	}
	

	function beginGroupRender($arrCurrentRow,$blnFirst=false)
	{
		if (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle)
		{
			$intTotCols = count($this->arrColumnArray) - 1;
			if ($this->blnShowSelector)
				$intTotCols++;
			if($this->blnExport==false)
			{
				if ($blnFirst)
					echo "<tr class=grp1 >";
				else
					echo "<tr class=grp2 >";
				$this->objGroupColumn->objListColumn->parseColumn($arrCurrentRow, "colspan=".$intTotCols);
				echo "</tr>";
			}
			else
			{
				$this->objGroupColumn->objListColumn->blnExport= $this->blnExport;
				$this->objGroupColumn->objListColumn->parseColumn($arrCurrentRow);
				echo "\n";
			}
		}
	}

	

	protected function renderListRow($arrCurrentRow, $intRowIndex, $blnIsNewGroup,$arrCondition)
	{
		if($this->blnExport==false)
		{
			if(array_key_exists($intRowIndex,$this->arrRowClass))
				echo "\n<tr class=".$this->arrRowClass[$intRowIndex]['strClassO'];
			else
				echo "\n<tr class=data".(0 == ($intRowIndex % 2) ? "1" : "2");

			if ($this->strVerticalAlignListContent)
				echo " valign = ".$this->strVerticalAlignListContent;
			echo " onmouseover='QL_MOver(this)' onmouseout='QL_MOut(this)'>";

			$arrCurrentRow = $this->arrContentData[$intRowIndex];
			$this->renderSelectorForRow($arrCurrentRow, $intRowIndex);
			for($colIndex=0; $colIndex < count($this->arrColumnArray); $colIndex++)
			{

				$blnGroupExist	=	false;
				for($intGroup=0;$intGroup<count($this->objFirstGroupColumn);$intGroup++)
				{
					if (($colIndex == $this->objFirstGroupColumn[$intGroup]->objListColumn->intCollectionIndex) && (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_COLUMNAR == $this->objGroupColumn->intRenderStyle) )
					{
						$blnGroupExist	=	true;
						$intCurrentColIndex	=	$intGroup;
						break;
					}
				}

				if (($colIndex != $this->objGroupColumn->objListColumn->intCollectionIndex) && $blnGroupExist==false)
					$this->arrColumnArray[$colIndex]->parseColumn($arrCurrentRow,$intRowIndex);
				else
				{

					if (MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle)
						continue;
					else if (MedGroupList::GROUP_RENDER_STYLE_COLUMNAR == $this->objGroupColumn->intRenderStyle)
					{
						if ($blnIsNewGroup)
						{
							if($blnGroupExist)
							{
								if($arrCondition[$intCurrentColIndex]['Value']!= $arrCurrentRow[$arrCondition[$intCurrentColIndex]['Title']] || $arrCondition[$intCurrentColIndex]['Title']=="")
									$this->objFirstGroupColumn[$intCurrentColIndex]->objListColumn->parseColumn($arrCurrentRow,$intRowIndex);
								else
									echo "<td>&nbsp;</td>";
							}
							else
								$this->objGroupColumn->objListColumn->parseColumn($arrCurrentRow,$intRowIndex);
						}
						else
						{
							echo "<td>&nbsp;</td>";
						}
					}
				}
			}

			echo "</tr>";
		}
		else
		{
			if ($this->blnShowSelector)
			{
				echo ""."\t";
			}
			$arrCurrentRow = $this->arrContentData[$intRowIndex];
			for($colIndex=0; $colIndex < count($this->arrColumnArray); $colIndex++)
			{

				$blnGroupExist	=	false;
				for($intGroup=0;$intGroup<count($this->objFirstGroupColumn);$intGroup++)
				{
					if (($colIndex == $this->objFirstGroupColumn[$intGroup]->objListColumn->intCollectionIndex) && (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_COLUMNAR == $this->objGroupColumn->intRenderStyle) )
					{
						$blnGroupExist	=	true;
						$intCurrentColIndex	=	$intGroup;
						break;
					}
				}

				$this->arrColumnArray[$colIndex]->blnExport = $this->blnExport;
				if (($colIndex != $this->objGroupColumn->objListColumn->intCollectionIndex)  && $blnGroupExist==false)
					$this->arrColumnArray[$colIndex]->parseColumn($arrCurrentRow,$intRowIndex);
				else
				{
					if (MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle)
						continue;
					else if (MedGroupList::GROUP_RENDER_STYLE_COLUMNAR == $this->objGroupColumn->intRenderStyle)
					{
						if ($blnIsNewGroup)
						{
							if($blnGroupExist)
							{
								if($arrCondition[$intCurrentColIndex]['Value']!= $arrCurrentRow[$arrCondition[$intCurrentColIndex]['Title']] || $arrCondition[$intCurrentColIndex]['Title']=="")
									$this->objFirstGroupColumn[$intCurrentColIndex]->objListColumn->parseColumn($arrCurrentRow,$intRowIndex);
								else
									echo ""."\t";
							}
							else
								$this->objGroupColumn->objListColumn->parseColumn($arrCurrentRow,$intRowIndex);
						}
						else
						{
							echo ""."\t";
						}
					}
				}
			}
			echo "\n";
		}
	}

	

	function renderGroupSummary($arrCurrentRow, $intGroupIdentifier)
	{
		if (!$this->objGroupColumn->arrExpressions) return;
		$strDisplay = $this->parseGroupText($this->objGroupColumn->strGroupExprSummary, $arrCurrentRow);
		$this->renderFooter($strDisplay, $this->arrGroupExprGroupTotal[$intGroupIdentifier], "gftr");
	}

	

	function renderListSummary()
	{
		if (!$this->objGroupColumn->arrExpressions) return;
		$this->renderFooter($this->objGroupColumn->strListExprSummary, $this->arrGroupExprListTotal, "lftr");
	}

	

	function renderFooter($strDisplay, $arrGroupExprTotal, $strCssClass)
	{
		if($this->blnExport==false)
		{
			$intColspan = 0;
			if ($this->blnShowSelector)
				$intColspan += 1;

			echo "<tr class=".$strCssClass.">";
			for ($colIndex = 0; $colIndex < count($this->arrColumnArray); $colIndex++)
			{
				if (($colIndex == $this->objGroupColumn->objListColumn->intCollectionIndex) &&
					(MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle))
					continue;

				$strCurrentColumn = $this->arrColumnArray[$colIndex];
				$strKeyName = "" + $strCurrentColumn->intCollectionIndex;

				$blnGroupExist	=	false;
				for($intGroup=0;$intGroup<count($this->objFirstGroupColumn);$intGroup++)
					{
						if (($colIndex == $this->objFirstGroupColumn[$intGroup]->objListColumn->intCollectionIndex) && (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle) )
						{
							$blnGroupExist	=	true;
							$intCurrentColIndex	=	$intGroup;
							break;
						}
					}
				if($blnGroupExist)
					$intColspan	=	$intColspan	- 1;


				if (is_array($arrGroupExprTotal) && (array_key_exists($strKeyName, $arrGroupExprTotal)))
				{
					if ($intColspan > 0)
					{

						echo "<td align='left' colspan=".$intColspan.">";
						echo $strDisplay;
						echo "</td>";
					}

					echo "<td".($strCurrentColumn->strHorizontalAlign ? " align=".$strCurrentColumn->strHorizontalAlign : "").
						">".number_format($arrGroupExprTotal[$strKeyName], 2)."</td>";

					$intColspan = 0;
					$strDisplay = "&nbsp;";
				}
				else
				{
					$intColspan++;
				}
			}
			if ($intColspan > 0)
				echo "<td colspan=".$intColspan.">&nbsp;</td>";
			echo "</tr>";
		}
		else
		{
			$intColspan = 0;
			if ($this->blnShowSelector)
				$intColspan += 1;
			for ($colIndex = 0; $colIndex < count($this->arrColumnArray); $colIndex++)
			{
				if (($colIndex == $this->objGroupColumn->objListColumn->intCollectionIndex) &&
					(MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle))
					continue;

				$strCurrentColumn = $this->arrColumnArray[$colIndex];
				$strKeyName = "" + $strCurrentColumn->intCollectionIndex;

				$blnGroupExist	=	false;
				for($intGroup=0;$intGroup<count($this->objFirstGroupColumn);$intGroup++)
					{
						if (($colIndex == $this->objFirstGroupColumn[$intGroup]->objListColumn->intCollectionIndex) && (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle) )
						{
							$blnGroupExist	=	true;
							$intCurrentColIndex	=	$intGroup;
							break;
						}
					}
				if($blnGroupExist)
					$intColspan	=	$intColspan	- 1;
				if (is_array($arrGroupExprTotal) && (array_key_exists($strKeyName, $arrGroupExprTotal)))
				{
					if ($intColspan > 0)
					{
						echo $strDisplay."\t";
						for($i=$intColspan-1;$i>0;$i--)
						{
							echo ""."\t";
						}
					}
					echo number_format($arrGroupExprTotal[$strKeyName], 2)."\t";
					$intColspan = 0;
					$strDisplay = "";
				}
				else
				{
					$intColspan++;
				}
			}
			if ($intColspan > 0)
			{
				for($i=$intColspan;$i>0;$i--)
				{
					echo ""."\t";
				}
			}
			echo "\n";
		}
	}

	

	function getTotalDisplayedColumns()
	{
		$intTotCols = count($this->arrColumnArray);
		if (MedGroupList
::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle)
		{
			$intTotCols -= 1;
		}

		if ($this->blnShowSelector)
			$intTotCols++;
	}

	

	function parseGroupText($strText, $arrCurrentRow)
	{
		if ($strText)
		{
			$strText = str_replace("{0}", $arrCurrentRow[$this->objGroupColumn->strGroupField], $strText);
		}
		return $strText;
	}

	

	protected function renderListingHeader()
	{
		if($this->blnExport==false)
		{
			global $IMAGE_PATH;
			echo "<tr class=hdr".($this->strVerticalAlignListHeader ? " valign=".$this->strVerticalAlignListHeader : "").">";
			if ($this->blnShowSelector)
			{
				echo "<td>";
				if (!$this->intSelectionLimit || 0 >= $this->intSelectionLimit)
				{
					echo "<input type=checkbox id=".$this->intId."_cSlcHd onClick='QL_HeaderCBClick(\"".$this->intId."\",this);'>";
				}
				else echo "&nbsp;";
				echo "</td>";
			}

			for($colIndex=0; $colIndex < count($this->arrColumnArray); $colIndex++)
			{
				if (($colIndex == $this->objGroupColumn->objListColumn->intCollectionIndex) && (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle))
				{
					continue;
				}
				$blnGroupExist	=	false;
				for($intGroup=0;$intGroup<count($this->objFirstGroupColumn);$intGroup++)
				{
					if (($colIndex == $this->objFirstGroupColumn[$intGroup]->objListColumn->intCollectionIndex) && (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle) )
					{
						$blnGroupExist	=	true;
						break;
					}
				}
				if($blnGroupExist)
					continue;
				$objLColumn = $this->arrColumnArray[$colIndex];
				echo "<td".($objLColumn->intWidth ? " width=".$objLColumn->intWidth : "").
					($objLColumn->strHorizontalAlign ? " align=".$objLColumn->strHorizontalAlign : "").
					($objLColumn->strCssClass ? " class=".$objLColumn->strCssClass : "").">";

				if ($objLColumn->strHeaderText)
				{
					$sortCol = $objLColumn->getSortColumn();
					if ($sortCol)
					{
						$symbol = "";
						$order = "asc";
						$sortGroupCol	=	"";
						for($intGroup=0;$intGroup<count($this->strFirstGroupField);$intGroup++)
						{
							$sortGroupCol	.=	$this->strFirstGroupField[$intGroup]." ".$order." ,";
						}

						$arrFirst	=	explode(",",$this->strActiveOrderField);
						$arrSec		=	explode(",",$sortGroupCol.$sortCol);
						$arrDiff	=	array_diff($arrFirst,$arrSec);

						if ($this->strActiveOrderField && count($arrDiff)<1)
						{

							if ($this->strActiveOrderDirection  == "asc" )
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
						$sortGroupCol	=	"";
						for($intGroup=0;$intGroup<count($this->strFirstGroupField);$intGroup++)
						{
							$sortGroupCol	.=	$this->strFirstGroupField[$intGroup]." ".$order." ,";
						}
						$sortCol	=	$sortGroupCol.$sortCol;
						echo "<a href=\"javascript:".parent::getSubmitJs(MedQuickList::SUBMIT_SRC_SORT_LINK, $sortCol, $order).
								"\">".$objLColumn->strHeaderText.$symbol."</a>";
					}
					else echo $objLColumn->strHeaderText;
				}
				else echo "&nbsp;";
				echo "</td>";
			}
			echo "</tr>";
		}
		else
		{
			if ($this->blnShowSelector)
			{
				echo ""."\t";
			}
			for($colIndex=0; $colIndex < count($this->arrColumnArray); $colIndex++)
			{
				if (($colIndex == $this->objGroupColumn->objListColumn->intCollectionIndex) && (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle))
				{
					continue;
				}
				$blnGroupExist	=	false;
				for($intGroup=0;$intGroup<count($this->objFirstGroupColumn);$intGroup++)
				{
					if (($colIndex == $this->objFirstGroupColumn[$intGroup]->objListColumn->intCollectionIndex) && (MedGroupList::GROUP_RENDER_STYLE_SEPERATE_HEADER == $this->objGroupColumn->intRenderStyle || MedGroupList::GROUP_RENDER_STYLE_NONE == $this->objGroupColumn->intRenderStyle) )
					{
						$blnGroupExist	=	true;
						break;
					}
				}
				if($blnGroupExist)
					continue;
				$objLColumn = $this->arrColumnArray[$colIndex];
				if ($objLColumn->strHeaderText)
				{
					echo $objLColumn->strHeaderText."\t";
				}
			}
			echo "\n";
		}
	}

	

	protected function calculateExpressions($intTotalRecords)
	{
		if (!$this->objGroupColumn->arrExpressions) return;

		
		for ($colIndex=0; $colIndex<count($this->objGroupColumn->arrExpressions); $colIndex++)
		{
			$strKeyName = "" + $this->objGroupColumn->arrExpressions[$colIndex][0]->intCollectionIndex;
			$this->arrGroupExprGroupTotal = array();
			$this->arrGroupExprListTotal[$strKeyName] = 0;
		}

		$strCurrentGroupColumn = $this->objGroupColumn->strGroupField;
		$intGroupIdentifier = 0;
		$intRowIndex = 0;

		while ($intRowIndex < $intTotalRecords)
		{
			$arrCurrentRow = $this->arrContentData[$intRowIndex];
			$strCurrentGroupValue = $arrCurrentRow[$strCurrentGroupColumn];

			for ($colIndex=0; $colIndex<count($this->objGroupColumn->arrExpressions); $colIndex++)
			{
				$strKeyName = "" + $this->objGroupColumn->arrExpressions[$colIndex][0]->intCollectionIndex;
				$this->arrGroupExprGroupTotal[$intGroupIdentifier][$strKeyName] = 0;
			}
			while (($intRowIndex < $intTotalRecords) && ($strCurrentGroupValue == $this->arrContentData[$intRowIndex][$strCurrentGroupColumn]))
			{
				for ($colIndex=0; $colIndex<count($this->objGroupColumn->arrExpressions); $colIndex++)
				{
					$arrCurrExpression = $this->objGroupColumn->arrExpressions[$colIndex];
					$strFieldName = $arrCurrExpression[0]->arrFieldArray[0];

					$strKeyName = "" + $arrCurrExpression[0]->intCollectionIndex;
					if (MedGroupColumn::GROUP_EXPR_TYPE_SUM == $arrCurrExpression[1])
						$this->arrGroupExprGroupTotal[$intGroupIdentifier][$strKeyName] += $this->arrContentData[$intRowIndex][$strFieldName];
					else if (MedGroupColumn::GROUP_EXPR_TYPE_COUNT == $arrCurrExpression[1])
						$this->arrGroupExprGroupTotal[$intGroupIdentifier][$strKeyName] += 1;
				}
				$intRowIndex++;
			}

			
			for ($colIndex=0; $colIndex<count($this->objGroupColumn->arrExpressions); $colIndex++)
			{
				$strKeyName = "" + $this->objGroupColumn->arrExpressions[$colIndex][0]->intCollectionIndex;
				$this->arrGroupExprListTotal[$strKeyName] += $this->arrGroupExprGroupTotal[$intGroupIdentifier][$strKeyName];
			}

			$intGroupIdentifier++;
		}
	}
}
?>