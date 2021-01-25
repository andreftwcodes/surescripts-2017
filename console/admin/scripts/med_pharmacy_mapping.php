<?PHP
	
	
	
	include_once('./../base/meditab/med_grouplist.php');

	
	include_once('./base/med_html_table.php');
	
	
	include_once("./base/med_module.php"); 

	
	$objModule = new MedModule();
	
	
	if($objPage->getRequest("btn_save")	==	"Submit")
	{
		$intNCPDPId		=	$objPage->getRequest("ncpdpid");
		$intMeditabId	=	$objPage->getRequest("TARtxt_meditab_id");
		$strPAgeType	=	$objPage->getRequest("strPageType");
		
		$objData 	=	new MedData(); 
	
		if($intNCPDPId != "" && $intMeditabId != "")
		{
			if($strPAgeType	==	"A")
			{				
				$objData->setProperty("pharmacy_mos","",NULL,NULL);
				$objData->setFieldValue("ncpdpid",$intNCPDPId);
				$objData->setFieldValue("meditab_id",$intMeditabId);
				$objData->insert();
			}
			else
			{
				$objData->setProperty("pharmacy_mos","ncpdpid",NULL,NULL);
				$objData->setFieldValue("ncpdpid",$intNCPDPId);
				$objData->setFieldValue("meditab_id",$intMeditabId);
				$objData->update();			
			}
		}?>
		<script type="text/javascript">
			self.parent.document.getElementById("mt_ncpdp_<?php print $intNCPDPId; ?>").innerHTML = '<?php print $intMeditabId; ?>';
			self.parent.tb_remove();
		</script>
<?php	}  ?>
<?php
		
	
	
	$intNCPDPid		=	$objPage->getRequest("ncpdpid");

	
	$strMiddle				= 	"./middle/med_pharmacy_mapping.htm";
	
	$strTableName			=	"pharmacy_mos";
	$strFieldName			=	" count(*) as ncpdp_total";
	$strWhere				=	" ncpdpid = '".$intNCPDPid."'";
	$rsPrescriberCount		=	$objPage->getRecords($strTableName, $strFieldName, $strWhere,"", "","", "");
	
	$intNCPDPCount			=	$rsPrescriberCount[0]['ncpdp_total'];
	
	if($intNCPDPCount	> 0)
	{
		$strPageType		=	"E";
	}
	else
	{
		$strPageType		=	"A";
	}
	
	$strTableName			=	"";
	$strTableName			=	"pharmacy_master";
	$strFieldName			=	" pharmacy_master.ncpdpid, store_name";
	if($strPageType		==	"E")
	{
		$strTableName			.=	" INNER JOIN pharmacy_mos on pharmacy_master.ncpdpid=pharmacy_mos.ncpdpid";
		$strFieldName			.=	" , meditab_id ";
	}	
	$strWhere				=	" pharmacy_master.ncpdpid = '".$intNCPDPid."'";
	
	$rsPrescriberRecords	=	$objPage->getRecords($strTableName, $strFieldName, $strWhere,"", "","", "");	

	$strPharmacyStoreName	=	$rsPrescriberRecords[0]['store_name'];
	$intMeditabId			=	$rsPrescriberRecords[0]['meditab_id'];	
		
	
	$strMeditabId			=	$objPage->getTextBox("TA","meditab_id","","class='comn-input'",$intMeditabId,1,"",true);

	
	$strMultipleMeditabId	=	$objPage->getCheckBox("multiple_meditab_id","","","",0,"");

	
	$strIndex				=	$strMiddle;	
	
	$strFile				=	"med_pharmacy_mapping";
	
	
	$localValues 			= 	array(
									"intNCPDPid"			=>	$intNCPDPid,
									"strPharmacyStoreName"	=>	$strPharmacyStoreName,
									"strMeditabId"			=>	$strMeditabId,
									"strMultipleMeditabId"	=>	$strMultipleMeditabId,
									"strPageType"			=>	$strPageType,
									"strFile"				=>	$strFile
									);
?>