<?php

	
	
	include_once('./../base/meditab/med_quicklist.php');
	
	
	$strMiddle					=	"./middle/med_pharmacy_master.htm";	

	
	$intTableId 				=	"13";
	$intButtonId 				=	$objPage->getRequest('hid_button_id');
	$intShowRows				= 	$objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
	$strSrServiceLevel			= 	$objPage->getSRequest('Sr_slt_service_level',$intTableId);
	$strMeditabIdGiven			=	$objPage->getSRequest('Sr_Taslt_meditab_id_given',$intTableId);
	$arrSrSpecialtyDirectory		=	$objPage->getSRequest('Sr_Mlslt_directory_specialty',$intTableId);
	
	$objModule->checkMaxRowLimit($intShowRows);	
	
	$strAction					=	"L";
	
	$strWhere					=	" 1 ";
	
	if($strSrServiceLevel != '' && $strSrServiceLevel != 'all')
	{
		$strWhere				.=	" and FIND_IN_SET('".$strSrServiceLevel."',service_level_bits) ";
	}
	if($strMeditabIdGiven	== "Yes")
	{
		$strWhere				.=	" and pharmacy_master.ncpdpid IN (select ncpdpid from pharmacy_mos)";
	}
	else if($strMeditabIdGiven	== "No")
	{
		$strWhere				.=	" and pharmacy_master.ncpdpid NOT IN (select ncpdpid from pharmacy_mos)";
	}
	
	if($arrSrSpecialtyDirectory != '' && count($arrSrSpecialtyDirectory) > 0 && !in_array("all",$arrSrSpecialtyDirectory))
	{
	    foreach($arrSrSpecialtyDirectory as $intKey => $strSpecialtyTypeValue)
	    {
		$arrSpecialtyWhere[] = "specialty_type1 = '".$strSpecialtyTypeValue."' 
					OR specialty_type2 = '".$strSpecialtyTypeValue."' 
					OR specialty_type3 = '".$strSpecialtyTypeValue."' 
					OR specialty_type4 = '".$strSpecialtyTypeValue."' ";
	    }
	    $strWhere .= " AND (".@implode(" OR ",$arrSpecialtyWhere).") ";
	}
	
	$strHtmlControls			= 	$objPage->getHtmlAll($intTableId,$strAction,true,false,NULL,true,true,true,$strWhere);

	$strPage 					=	$strHtmlControls['strPage'];
	
	$strModuleName				= 	$objPage->getPageTitleByDb($intTableId);
	
	
	$arrSLOptions				=	$objPage->getOptions("SERVICE_LEVEL:SL:NT:''");
	
	foreach($arrSLOptions as $strSLKey => $strSLValue)
	{
		($strSrServiceLevel == "".$strSLKey."") ? $strSelected = "selected" : $strSelected = "";
		$arrSLOptions[]			=	'<option value="'.$strSLKey.'" '.$strSelected.'>'.$strSLValue.'</option>';
	}
	
	$strSLSelectBox				=	'<select name="Sr_slt_service_level" id="Sr_slt_service_level" class="comn-input">
									'.@implode("",$arrSLOptions).'
									</select>';
	
	
	
	$strMessage 				=	$objPage->objGeneral->getMessage();
	
	
	$localValues 				=	array(
											"intButtonId"		=>	$intButtonId,
											"strPage"			=>	$strPage,
											"intTableId"		=>	$intTableId,
											"strPageType"		=>	$strAction,
											"strMessage"		=>	$strMessage,
											"strModuleName"		=>	$strModuleName,	
											"strSLSelectBox"	=>	$strSLSelectBox,
											"strServiceLevel"	=>	$strServiceLevel						
										);	
	$localValues				= 	array_merge($localValues,$strHtmlControls);		
	
	
	function list13_DataLoaded($rsData)
	{
	    global $objPage,$objModule,$objList,$IMAGE_PATH;
	    $intTotalRecords = count($rsData[0]);

	    for($intData=0;$intData<$intTotalRecords;$intData++)
	    {
		$intTranId= $rsData[0][$intData]["mt_tran_id"];
		
		$strImageHistory = $objPage->getImage("img_hisotry",$IMAGE_PATH."gSearch.gif"," border=0 ", "title=View");
		
		$strImageMeditabMap = $objPage->getImage("img_hisotry",$IMAGE_PATH."mos.gif"," border=0 ", "title=View");
		
		$strOutMessageHisotry = $objPage->getHrefLink("index.php?file=med_view_pharmacy_master&mt_tran_id=".$intTranId."&popup=1&TB_iframe=true&height=500&width=700&modal=true",$strImageHistory,"class='thickbox'");

		$strEdit = $objPage->openMouseImagePopup($IMAGE_PATH."schedule-update.gif","","","href='index.php?file=med_pharmacy_addedit&hid_page_type=A&hid_pm_id=".$intTranId."'");

		if($rsData[0][$intData]["meditab_id"]=='')
		{	
		    $srtMeditabLink = $objPage->getHrefLink("index.php?file=med_pharmacy_mapping&ncpdpid=".$rsData[0][$intData]["ncpdpid"]."&popup=1&TB_iframe=true&height=180&width=250&modal=true","Unspecified","class='thickbox' id='mt_ncpdp_".$rsData[0][$intData]["ncpdpid"]."' ");				
		}
		else
		{	
		    $srtMeditabLink = $objPage->getHrefLink("index.php?file=med_pharmacy_mapping&ncpdpid=".$rsData[0][$intData]["ncpdpid"]."&popup=1&TB_iframe=true&height=180&width=250&modal=true",$rsData[0][$intData]["meditab_id"],"class='thickbox'  id='mt_ncpdp_".$rsData[0][$intData]["ncpdpid"]."' ");
		}

		$rsData[0][$intData]["meditab_id"] = $srtMeditabLink;
		$rsData[0][$intData]["store_name"] = $strEdit." ".$strOutMessageHisotry."&nbsp;".$rsData[0][$intData]["store_name"];

		
		if($rsData[0][$intData]["service_level_bits"] != '' && $rsData[0][$intData]["service_level_bits"] != '-1')
		{
		    $arrServiceLevel = @explode(",",$rsData[0][$intData]["service_level_bits"]);
		    $arrSLValue = array();
		    foreach($arrServiceLevel as $strSLKey => $strSLValue)
		    {
			    $arrSLValue[] = $objModule->getComboValue('SERVICE_LEVEL',$strSLValue);
		    }
		    $rsData[0][$intData]["service_level_bits"] = @implode(", ",$arrSLValue);
		}
		else if($rsData[0][$intData]["service_level_bits"] == '-1')
		{
			$rsData[0][$intData]["service_level_bits"] = "Disabled";
		}
		

		if($rsData[0][$intData]["is_from_directory_download"] == 'N')
		    $rsData[0][$intData]["ncpdpid"] = $rsData[0][$intData]["ncpdpid"]."*";
		
		$rsData[0][$intData]["address"] .= "<br>".$rsData[0][$intData]["city"].", ".$rsData[0][$intData]["state"]." - ".$rsData[0][$intData]["zip"];

	    }
	}
?>