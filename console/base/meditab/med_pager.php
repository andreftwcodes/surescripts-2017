<?php

class MedPager
{
	private $intRecordsPerPage;		
	private $intPagesPerGroup;		
	private $intActivePage;			
	private $intActivePageGroup;	
	private $intRecordCount;		
	private $intPageCount;			

	
	public $strActivePageMessage;		
	public $strPrefixId;

	const PREV_PAGE_GROUP_TEXT = "&lsaquo;";
	const NEXT_PAGE_GROUP_TEXT = "&rsaquo;";
	const FIRST_PAGE_TEXT = "&laquo;";
	const LAST_PAGE_TEXT = "&raquo;";

	
	function __construct($intRecordsPerPage, $intPagesPerGroup, $strPrefix="")
	{

		$this->intRecordsPerPage = $intRecordsPerPage;
		$this->intPagesPerGroup = $intPagesPerGroup;
		$this->intRecordCount = 0;
		$this->intPageCount = 0;
		$this->intActivePage = 0;
		$this->strPrefixId = $strPrefix;
	}

	

	public function setActivePage($intPage)
	{
		$this->intActivePage = $intPage;
		$this->intActivePageGroup = $this->getActivePageGroup();
	}

	

	public function setRecordCount($intTotalRecords)
	{
		$this->intRecordCount = $intTotalRecords;
		$this->updatePageCount();
	}

	

	public function updatePageCount()
	{
		if (0 < $this->intRecordsPerPage)
			$this->intPageCount = ceil($this->intRecordCount / $this->intRecordsPerPage);
		else
			$this->intPageCount = 0;
	}

	

	public function getPageCount()
	{
		return $this->intPageCount;
	}

	
	
	public function getActivePageGroup()
	{
		if ($this->intActivePage == 0 )
			return $this->intActivePage;
		else 
			return ceil($this->intActivePage / $this->intPagesPerGroup);
	}

	

	public function getActivePageStartingRec()
	{
		if (0 < $this->intActivePage)
		{
			return (($this->intActivePage - 1) * $this->intRecordsPerPage);
		}
		else return 0;
	}

	
	public function getActiveGroupStartingPage()
	{
		if (0 < $this->intActivePageGroup)
		{
			return (($this->intActivePageGroup - 1) * $this->intPagesPerGroup + 1);
		}
		else return 0;
	}

	

	public function getActiveGroupEndingPage()
	{
		if ($this->nextGroupExists())
		{
			return ($this->intActivePageGroup * $this->intPagesPerGroup);
		}
		else return $this->intPageCount;
	}

	

	public function getPageGroupCount()
	{
		if ($this->intPageCount == 0 )
		return 	$this->intPageCount;
		return ceil($this->intPageCount / $this->intPagesPerGroup);
	}

	

	public function previousGroupExists()
	{
		if (1 < $this->intActivePageGroup)
			return true;
		else return false;
	}

	

	public function nextGroupExists()
	{
		$pageGroupCount = $this->getPageGroupCount();
		if (0 < $this->intActivePageGroup && $this->intActivePageGroup < $pageGroupCount)
			return true;
		else return false;
	}

	

	public function getPreviousGroupStartingPage()
	{
		if ($this->previousGroupExists())
		{
			return (($this->intActivePageGroup - 2) * $this->intPagesPerGroup + 1);
		}
		else return 0;
	}

	

	public function getNextGroupStartingPage()
	{
		if ($this->nextGroupExists())
		{
			return (($this->intActivePageGroup) * $this->intPagesPerGroup + 1);
		}
		else return 0;
	}

	
	public function show()
	{
		 if ($this->intPageCount <= 1 )
		 	return; 

		
		$noOfRows = $this->intRecordsPerPage;

		if (!$this->intActivePage)
		{
			$this->intActivePage = 1;
			$this->intActivePageGroup = 1;
		}

			
		if ($this->previousGroupExists())
		{
			echo "<a href=\"javascript:".$this->getSubmitJs($this->getPreviousGroupStartingPage())."\">".MedPager::PREV_PAGE_GROUP_TEXT."</a>&nbsp;&nbsp;";
		}

		$endIdx = $this->getActiveGroupEndingPage();
		

		for ($pageIdx = $this->getActiveGroupStartingPage(); $pageIdx <= $endIdx; $pageIdx++)
		{
			if ($pageIdx == $this->intActivePage)
			{
				echo $pageIdx."&nbsp;";
			}
			else
			{
				echo "<a href=\"javascript:".$this->getSubmitJs($pageIdx)."\">".$pageIdx."</a>&nbsp;";
			}
		}

		$page = $this->getNextGroupStartingPage();
		if ($this->nextGroupExists())
		{
			echo "&nbsp;".
				"<a href=\"javascript:".$this->getSubmitJs($this->getNextGroupStartingPage())."\">".MedPager::NEXT_PAGE_GROUP_TEXT."</a>";
		}
	}
	
	public function getFirstPage()
	{
		if(empty($this->intActivePage) || $this->intActivePage ==	1)
			echo MedPager::FIRST_PAGE_TEXT."&nbsp;";
		else	
			echo "<a href=\"javascript:".$this->getSubmitJs('1')."\">".MedPager::FIRST_PAGE_TEXT."</a>&nbsp;";
	}
		
	public function getLastPage()
	{
		if(empty($this->intActivePage) || $this->intActivePage == $this->getPageCount())
			echo "&nbsp;".MedPager::LAST_PAGE_TEXT."&nbsp;";
		else	
			echo "&nbsp;<a href=\"javascript:".$this->getSubmitJs($this->getPageCount())."\">".MedPager::LAST_PAGE_TEXT."</a>&nbsp;";			
	}
		
	public function getPageLimit()
	{
		if ($this->strActivePageMessage)
		{
			$pageLinkStr = $this->strActivePageMessage;
			
			if($this->intActivePage == "")
				$intActivePage = 1;
			else
				$intActivePage = $this->intActivePage;
				
			$pageLinkStr = str_replace("[[PAGE]]", (($intActivePage-1)*$this->intRecordsPerPage+1), $this->strActivePageMessage);
			
			if(($intActivePage*$this->intRecordsPerPage) > $this->intRecordCount)
				$intEndCount	=	$this->intRecordCount;
			else
				$intEndCount	=	$intActivePage*$this->intRecordsPerPage;
			$pageLinkStr = str_replace("[[END]]",$intEndCount, $pageLinkStr);
			$pageLinkStr = str_replace("[[TOTAL]]", $this->intRecordCount, $pageLinkStr);
			echo "<span class=msg>".$pageLinkStr."</span>&nbsp;";
		}
		
	}
	
	private function getSubmitJs($intPage)
	{
		return "QL_Submit('".$this->strPrefixId."', '".MedQuickList::SUBMIT_SRC_PAGER_LINK."', '".$intPage."')";
	}

}
?>