<?php



require_once("med_listcolumn.php");
class MedGroupColumn
{
	public $objListColumn;			
	public $strGroupField;			
	public $intRenderStyle;			
	public $strGroupExprSummary;		
	public $strListExprSummary;		
	public $blnShowExprSummaryOnTop = false;	
	public $blnShowGroupExprSummary = true;		

	public $arrExpressions;					

	const GROUP_EXPR_TYPE_SUM = 1;
	const GROUP_EXPR_TYPE_COUNT = 2;

	
	
	function __construct(MedListColumn $objColumn, $strGrpFieldName, $intStyle)
	{
		$this->objListColumn = $objColumn;
		$this->strGroupField = $strGrpFieldName;
		$this->intRenderStyle = $intStyle;
	}

	

	function addGroupExpression($objListColumn, $strExpressionType)
	{
		$this->arrExpressions[] = array($objListColumn, $strExpressionType);
	}
}
?>