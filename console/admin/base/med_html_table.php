<?PHP

	class HtmlTable
	{
		
		private $intColumns;
		
		
		private $strHtmlOutput;
		
		
		private $intRowCounter;
		
		
		private $strExtraTRProperty;
		
		
		private $strExtraTDProperty;
		
		
		private $intColumnCounter;
		
		
		private $blnNoWrap;

		
		public function __construct($intColumns,$strTableExtraProperty='',$strExtraTRProperty='',$strExtraTDProperty='',$blnNoWrap=true)
		{
			$this->intColumns				=	$intColumns;
			$this->strExtraTRProperty		=	' ' . $strExtraTRProperty;
			$this->strExtraTDProperty		=	' ' . $strExtraTDProperty;
			$this->intRowCounter			=	0;
			$this->intColumnCounter			=	0;
			$this->strHtmlOutput			=	'<table '.$strTableExtraProperty.' >';
			$this->blnNoWrap				=	$blnNoWrap;
		}

		
		public function addData($strCellData='',$blnForceNewRow=false,$blnMergeAllCells=false,$strExtraTDProperty='',$strExtraTRProperty='')
		{
                        if($blnForceNewRow == true)
                                $this->__startRow();
                                
			if($strCellData === '')
				$strCellData 					= '';
			if($blnMergeAllCells === true)
				$this->__startCell($this->intColumns,$strExtraTDProperty,$strExtraTRProperty);
			else
				$this->__startCell(0,$strExtraTDProperty,$strExtraTRProperty);

                        
                                
			$this->strHtmlOutput				.=	$strCellData;
			$this->__endCell();
		}

		
		private function __startRow($strExtraTRProperty='')
		{
			$this->intRowCounter				+=	1;
                        $this->intColumnCounter                 	=	0;
			$this->strHtmlOutput				.=	'<tr'.$this->strExtraTRProperty.' '.$strExtraTRProperty.'>';
		}

		
		private function __endRow()
		{
			$this->strHtmlOutput				.=	'</tr>';
		}

		
		private function __startCell($intMergeCell=0,$strExtraTDProperty="",$strExtraTRProperty="")
		{
			if($this->intColumnCounter >= $this->intColumns && $this->intColumns != -1)
				$this->intColumnCounter			=	0;

			if($this->intColumnCounter === 0)
				$this->__startRow($strExtraTRProperty);

			
			if($intMergeCell!==0)
			{
				$strColspan						=	' colspan="'.$intMergeCell.'" ';
				$this->intColumnCounter			=	$intMergeCell;
			}
			else
				$this->intColumnCounter				+=	1;
			if($this->blnNoWrap !== false)
					$strWrap	=	'nowrap="nowrap"';
			$this->strHtmlOutput				.=	'<td '. $strWrap .' ' . $this->strExtraTDProperty. $strExtraTDProperty .' '.$strColspan.'>';
		}

		
		private function __endCell()
		{
			$this->strHtmlOutput				.=	'</td>';
			if($this->intColumnCounter >= $this->intColumns && $this->intColumns !== -1)
				$this->__endRow();
		}

		
		public function getHtml($blnReturn	=	true)
		{
			$this->__endTable();
			if($blnReturn)
				return $this->strHtmlOutput;
		}
		
		
		
		public function __toString()
		{
			return $this->getHtml();	
		}

		private function __endTable()
		{
			
			$intRemainingCells						=	$this->intColumns - $this->intColumnCounter;
			for($intCount = 0; $intCount < $intRemainingCells; $intCount++)
			{
				$this->addData();
			}
			if(($this->intColumns === $this->intColumnCounter && $intRemainingCells === 0 && $this->intColumnCounter === 0) || $this->intColumns === -1)
				$this->__endRow();
			$this->strHtmlOutput 					.=	 '</table>';
		}
	}
?>