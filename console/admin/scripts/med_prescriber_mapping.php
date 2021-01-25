<?PHP
	
	
	
	include_once('./../base/meditab/med_grouplist.php');

	
	include_once('./base/med_html_table.php');
	
	
	include_once("./base/med_module.php"); 

	
	$objModule = new MedModule();
	
	
	if($objPage->getRequest("btn_save")	==	"Submit")
	{
		$intSPIId				=	$objPage->getRequest("spi");
		$intMeditabId			=	$objPage->getRequest("TARtxt_meditab_id");
		$strPAgeType			=	$objPage->getRequest("strPageType");
		$strMultipleMeditabId	=	$objPage->getRequest("chk_multiple_meditab_id");

		
		$objData 	=	new MedData(); 
		if($strMultipleMeditabId	!=	"Yes")
		{
			if($intSPIId != "" && $intMeditabId != "")
			{
				if($strPAgeType	==	"A")
				{				
					$objData->setProperty("prescriber_mos","",NULL,NULL);
					$objData->setFieldValue("spi",$intSPIId);
					$objData->setFieldValue("meditab_id",$intMeditabId);
					$objData->insert();
				}
				else
				{
					$objData->setProperty("prescriber_mos","spi",NULL,NULL);
					$objData->setFieldValue("spi",$intSPIId);
					$objData->setFieldValue("meditab_id",$intMeditabId);
					$objData->update();			
				}
				?>
				<script type="text/javascript">
					self.parent.document.getElementById("mt_spi_<?php print $intSPIId; ?>").innerHTML = '<?php print $intMeditabId; ?>';				
				</script>
				<?PHP
			} 
		}
		else
		{
			$strTableName			=	"prescriber_master";
			$strFieldName			=	"spi";
			$strWhere				=	"LEFT(spi, 10) = '".substr($intSPIId,0,10)."'";
			$rsMultipleSPI			=	$objPage->getRecords($strTableName, $strFieldName, $strWhere,"", "","", "");		
			
			if($strPAgeType	==	"A")
			{
				foreach($rsMultipleSPI as $key => $value)
				{
					$intSPIId	=	$value['spi'];
					$objData->setProperty("prescriber_mos","",NULL,NULL);
					$objData->setFieldValue("spi",$intSPIId);
					$objData->setFieldValue("meditab_id",$intMeditabId);
					$objData->insert();
					?>
					<script type="text/javascript">
					self.parent.document.getElementById("mt_spi_<?php print $intSPIId; ?>").innerHTML = '<?php print $intMeditabId; ?>';					
					</script>
					<?PHP					
				}
			}
			else
			{
				foreach($rsMultipleSPI as $key => $value)
				{
					$intSPIId	=	$value['spi'];
					$objData->setProperty("prescriber_mos","spi",NULL,NULL);
					$objData->setFieldValue("spi",$intSPIId);
					$objData->setFieldValue("meditab_id",$intMeditabId);
					$objData->update();
					?>
					<script type="text/javascript">
					self.parent.document.getElementById("mt_spi_<?php print $intSPIId; ?>").innerHTML = '<?php print $intMeditabId; ?>';					
					</script>
					<?PHP	
				}
			}
		} ?>		
		<script type="text/javascript">			
			self.parent.tb_remove();
		</script>
		
<?php } ?>	
<?php
		
	
	
	$intSPI		=	$objPage->getRequest("spi");

	
	$strMiddle				= 	"./middle/med_prescriber_mapping.htm";
	$strTableName			=	"prescriber_mos";
	$strFieldName			=	" count(*) as spi_total";
	$strWhere				=	" spi = '".$intSPI."'";
	$rsPrescriberCount		=	$objPage->getRecords($strTableName, $strFieldName, $strWhere,"", "","", "");
	
	$intSPICount			=	$rsPrescriberCount[0]['spi_total'];
	
	if($intSPICount	> 0)
	{
		$strPageType		=	"E";
	}
	else
	{
		$strPageType		=	"A";
	}
	
	$strTableName			=	"";
	$strTableName			=	"prescriber_master";
	$strFieldName			=	" prescriber_master.spi, CONCAT(first_name, ' ',last_name) as name";
	if($strPageType		==	"E")
	{
		$strTableName			.=	" INNER JOIN prescriber_mos on prescriber_master.spi=prescriber_mos.spi";
		$strFieldName			.=	" , meditab_id ";		
	}	
	$strWhere				=	" prescriber_master.spi = '".$intSPI."'";
	
	$rsPrescriberRecords	=	$objPage->getRecords($strTableName, $strFieldName, $strWhere,"", "","", "");	

	$strPrescriberName		=	$rsPrescriberRecords[0]['name'];
	$intMeditabId			=	$rsPrescriberRecords[0]['meditab_id'];	
		
	
	$strMeditabId			=	$objPage->getTextBox("TA","meditab_id","","class='comn-input'",$intMeditabId,1,"",true);

	
	$strMultipleMeditabId	=	$objPage->getCheckBox("multiple_meditab_id","","","",0,"");

	
	$strFile				=	"med_prescriber_mapping";
		
	
	$strIndex				=	$strMiddle;	
	
	
	$localValues 			= 	array(
									"intSPI"			=>	$intSPI,
									"strPrescriberName"	=>	$strPrescriberName,
									"strMeditabId"		=>	$strMeditabId,
									"strMultipleMeditabId"	=>  $strMultipleMeditabId,
									"strPageType"		=>	$strPageType,
									"strFile"			=>	$strFile
									);

?>