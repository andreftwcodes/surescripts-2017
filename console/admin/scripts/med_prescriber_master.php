<?PHP

	
	
	include_once('./../base/meditab/med_grouplist.php');		
	
	
	$strMiddle				=	"./middle/med_prescriber_master.htm";	
		
	
	$intTableId 			=	"14";
	$intButtonId 			=	$objPage->getRequest('hid_button_id');
	$intShowRows			= 	$objPage->getSRequest('Sr_Intxt_show_rows',$intTableId);
$strSrServiceLevel		= 	$objPage->getSRequest('Sr_slt_service_level',$intTableId);
	$strMeditabIdGiven		=	$objPage->getSRequest('Sr_Taslt_meditab_id_given',$intTableId);	
	
	$objModule->checkMaxRowLimit($intShowRows);	
	$strAction				=	"L";
	
	$strWhere				=	1;
	
	if($strSrServiceLevel != '' && $strSrServiceLevel != 'all')
	{
		$strWhere			.=	" and FIND_IN_SET('".$strSrServiceLevel."',service_level_bits) ";
	}
	if($strMeditabIdGiven	== "Yes")
	{
		$strWhere				.=	" and prescriber_master.spi IN (select spi from prescriber_mos)";
	}
	else if($strMeditabIdGiven	== "No")
	{
		$strWhere				.=	" and prescriber_master.spi NOT IN (select spi from prescriber_mos)";
	}
	
	
	$strHtmlControls		= 	$objPage->getHtmlAll($intTableId,$strAction,true,false,NULL,true,true,true,$strWhere);
	
	$strPage 				=	$strHtmlControls['strPage'];
	
	$strModuleName			= 	$objPage->getPageTitleByDb($intTableId);
	
	
	$arrSLOptions			=	$objPage->getOptions("SERVICE_LEVEL:SL:NT:''");
	
	foreach($arrSLOptions as $strSLKey => $strSLValue)
	{
		($strSrServiceLevel == "".$strSLKey."") ? $strSelected = "selected" : $strSelected = "";
		$arrSLOptions[]		=	'<option value="'.$strSLKey.'" '.$strSelected.'>'.$strSLValue.'</option>';
	}
	
	$strSLSelectBox			=	'<select name="Sr_slt_service_level" id="Sr_slt_service_level" class="comn-input">
									'.@implode("",$arrSLOptions).'
									</select>';
	
	
	
	$strMessage 			=	$objPage->objGeneral->getMessage();
	
	
	$localValues 			=	array(
									"intButtonId"	=>	$intButtonId,
									"strPage"		=>	$strPage,
									"intTableId"	=>	$intTableId,
									"strPageType"	=>	$strAction,
									"strMessage"	=>	$strMessage,
									"strModuleName"	=>	$strModuleName,
									"strSLSelectBox"=>	$strSLSelectBox
								);	

	$localValues			=	array_merge($localValues,$strHtmlControls);		

		
	function list14_DataLoaded($rsData)
	{
		global $objPage,$objModule,$objList,$IMAGE_PATH;
		$intTotalRecords	=	count($rsData[0]);
				
		for($intData=0;$intData<$intTotalRecords;$intData++)
		{
			$intTranId      =  	$rsData[0][$intData]["mt_tran_id"];			
			$strImageHistory		=	$objPage->getImage("img_hisotry",$IMAGE_PATH."gSearch.gif"," border=0 ", "title=View");	
			$strImageMeditabMap		=	$objPage->getImage("img_hisotry",$IMAGE_PATH."mos.gif"," border=0 ", "title=View");	
			$strOutMessageHisotry	=	$objPage->getHrefLink("index.php?file=med_view_prescriber_master&mt_tran_id=".$intTranId."&popup=1&TB_iframe=true&height=480&width=650&modal=true",$strImageHistory,"class='thickbox'");
			
			
			
			
			$strUpdatePrescriberLocationIcon	=	$objPage->getHrefLink("index.php?file=med_prescribe_addedit&action=UPDATE_PRESCRIBER_LOCATION&hid_pm_id=".$intTranId."",$objPage->getImage("img_prescriber_location",$IMAGE_PATH."edit.gif"," border=0 height='13' title='Update Prescriber Location' ", ""),"");
			
			
			$strAddPrescriberLocationIcon		=	$objPage->getHrefLink("index.php?file=med_prescribe_addedit&action=ADD_PRESCRIBER_LOCATION&hid_pm_id=".$intTranId."",$objPage->getImage("img_prescriber_location",$IMAGE_PATH."mos-icon-new.gif"," border=0 title='Add Prescriber Location' ", ""),"");


			$rsData[0][$intData]["spi_location"] =	$strUpdatePrescriberLocationIcon."&nbsp;".$strAddPrescriberLocationIcon."&nbsp;".$strOutMessageHisotry."&nbsp;".$rsData[0][$intData]["spi_location"];
	
			

			if($rsData[0][$intData]["meditab_id"] == '')
			{
				$srtMeditabLink		=	$objPage->getHrefLink("index.php?file=med_prescriber_mapping&spi=".$rsData[0][$intData]["spi"]."&popup=1&TB_iframe=true&height=170&width=320&modal=true","Unspecified","class='thickbox' id='mt_spi_".$rsData[0][$intData]["spi"]."' ");
			}
			else
			{
				$srtMeditabLink		=	$objPage->getHrefLink("index.php?file=med_prescriber_mapping&spi=".$rsData[0][$intData]["spi"]."&popup=1&TB_iframe=true&height=170&width=320&modal=true",$rsData[0][$intData]["meditab_id"],"class='thickbox'  id='mt_spi_".$rsData[0][$intData]["spi"]."' ");
			}

  			$rsData[0][$intData]["meditab_id"]	=	$srtMeditabLink;
			

			

			
			if($rsData[0][$intData]["service_level_bits"] != '')
			{
				$arrServiceLevel		=	@explode(",",$rsData[0][$intData]["service_level_bits"]);
				$arrSLValue				=	array();
				foreach($arrServiceLevel as $strSLKey => $strSLValue)
				{
					$arrSLValue[]		=	$objModule->getComboValue('SERVICE_LEVEL',$strSLValue);					
				}
				$rsData[0][$intData]["service_level_bits"]	=	@implode(", ",$arrSLValue);
			}
			
		}
	}
?>
