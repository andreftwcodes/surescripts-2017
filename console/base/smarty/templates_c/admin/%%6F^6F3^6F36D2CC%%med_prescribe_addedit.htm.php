<?php /* Smarty version 2.6.9, created on 2019-11-22 21:01:28
         compiled from ./middle/med_prescribe_addedit.htm */ ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file" value="med_prescribe_action" />
<input type="hidden" name="hid_page_type" id="hid_page_type" value="<?php echo $this->_tpl_vars['strPageType']; ?>
" />
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_action" id="hid_action"  value="<?php echo $this->_tpl_vars['strAction']; ?>
">
  <input type="hidden" id="hid_mt_tran_id" name="hid_mt_tran_id" value="<?php echo $this->_tpl_vars['intTranId']; ?>
">
  <input type="hidden" id="hid_pm_id" name="hid_pm_id" value="<?php echo $this->_tpl_vars['intPrescriberMasterId']; ?>
">
  <table border="0" cellpadding="0" cellspacing="0"  width="100%" class="tab-bor" align="center">
    <tr>
      <td height="24" align="left"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /> 
	  <font class="bold-text"> <?php echo $this->_tpl_vars['strTitle']; ?>
</font><?php if ($this->_tpl_vars['strMeditabId'] != ''): ?> &nbsp;&nbsp; <font class="bold-text"> Meditab Id: <?php echo $this->_tpl_vars['strMeditabId']; ?>
</font><?php endif; ?></td>
      <td width="5%" align="left"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="javascript:history.back();">Back</a></td>
    </tr>
    <tr>
      <td height="1" class="blue-bg" colspan="2"></td>
    </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
    <tr>
      <td colspan="2" height="25"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
    </tr>
    <?php endif; ?>
    <tr>
      <td colspan=2 height="5"  align="right">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" width="100%" align="left"><table cellpadding="1" cellspacing="1" width="100%" border="0">

	  <?php if ($this->_tpl_vars['strAction'] == 'UPDATE_PRESCRIBER' || $this->_tpl_vars['strAction'] == 'UPDATE_PRESCRIBER_LOCATION'): ?>
      <tr>
												<td align="right" valign="middle"  width="18%">
												
												<?php echo $this->_tpl_vars['LBL_action']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['straction']; ?>
</td>
												
										</tr>
		<?php endif; ?>

          <tr  >
            <td align="center" valign="top" colspan="2" >
									<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
									<legend><strong>Prescriber Basic Information</strong></legend>
										<table border="0" cellpadding="1" cellspacing="0" id="td_basic_info">
										
										<tr>
												<td align="right" valign="top"  width="18%"><?php echo $this->_tpl_vars['LBL_spi']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['strspi']; ?>
</td>
												
										</tr>										
										<tr>
												<td align="right" valign="top"  width="18%"><?php echo $this->_tpl_vars['LBL_prefix_name']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['strprefix_name']; ?>
</td>
												
										</tr>
										<tr>
												<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_first_name']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['strfirst_name']; ?>
</td>
										</tr>
										<tr>
												<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_middle_name']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['strmiddle_name']; ?>
</td>
										</tr>
										<tr>
												<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_last_name']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['strlast_name']; ?>
</td>
										</tr>
										<tr>
												<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_suffix_name']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['strsuffix_name']; ?>
</td>
										</tr>
										<tr>
												<td align="right" valign="top" nowrap="nowrap"><?php echo $this->_tpl_vars['LBL_specialty_qualifier']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['strspecialty_qualifier']; ?>
</td>
										</tr>
										<tr>
												<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_specialty_code_primary']; ?>
</td>
												<td align="left" valign="top"><?php echo $this->_tpl_vars['strspecialty_code_primary']; ?>
</td>
										</tr>
										
										</table>
										</fieldset>
								</td>
          </tr>
		  
		  
		  <tr >
            <td colspan="2" align="center" valign="top" >
									<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
									<legend><strong>Prescriber Location</strong></legend>
										<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
				<legend><strong>Indentification</strong></legend>
				<table width="100%" border="0" cellspacing="1" cellpadding="1">
				   <tr>
					<td  align="right" valign="top" width="20%"><?php echo $this->_tpl_vars['LBL_dea']; ?>
</td>
					<td  align="left" valign="top" width="15%"><?php echo $this->_tpl_vars['strdea']; ?>
</td>
					<td  align="right" valign="top" width="15%"><?php echo $this->_tpl_vars['LBL_npi']; ?>
</td>
					<td  align="left" valign="top" width="20%"><?php echo $this->_tpl_vars['strnpi']; ?>
</td>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_state_license_number']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strstate_license_number']; ?>
</td>
				  </tr>
				   
				<tr>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_file_id']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strfile_id']; ?>
</td>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_medicaid_number']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strmedicaid_number']; ?>
</td>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_medicare_number']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strmedicare_number']; ?>
</td>
					
				  </tr>
					
					<tr>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_upin']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strupin']; ?>
</td>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_dentist_license_number']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strdentist_license_number']; ?>
</td>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_mutually_defined']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strmutually_defined']; ?>
</td>
				  </tr>
					
					<tr>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_prior_authorization']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strprior_authorization']; ?>
</td>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_social_security']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strsocial_security']; ?>
</td>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_ppo_number']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strppo_number']; ?>
</td>
				  </tr>

				</table>
				</fieldset>
				
				
				<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
				<legend><strong>Location Information</strong></legend>
				<table width="100%" border="0" cellspacing="1" cellpadding="1">
				  <tr>
				  	<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_clinic_name']; ?>
</td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['strclinic_name']; ?>
</td>
					<td  align="right" valign="top" width="20%"><?php echo $this->_tpl_vars['LBL_address_line1']; ?>
</td>
					<td  align="left" valign="top" width="15%"><?php echo $this->_tpl_vars['straddress_line1']; ?>
</td>
					<td  align="right" valign="top" width="15%"><?php echo $this->_tpl_vars['LBL_address_line2']; ?>
</td>
					<td  align="left" valign="top" width="20%"><?php echo $this->_tpl_vars['straddress_line2']; ?>
</td>
					
				  </tr>
				  
				  <tr>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_city']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strcity']; ?>
</td>
					<td  align="right" valign="top"><?php echo $this->_tpl_vars['LBL_state']; ?>
</td>
					<td  align="left" valign="top"><?php echo $this->_tpl_vars['strstate']; ?>
</td>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_zip']; ?>
</td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['strzip']; ?>
</td>
				 </tr>
				</table>
				</fieldset>
				
				<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
				<legend><strong>Contacts/Communications</strong></legend>
				<table width="100%" border="0" cellspacing="1" cellpadding="1">
				<tr>
					<td align="right" valign="top" width="20%"><?php echo $this->_tpl_vars['LBL_phone_primary']; ?>
</td>
					<td align="left" valign="top" width="15%"><?php echo $this->_tpl_vars['strphone_primary']; ?>
</td>
					<td align="right" valign="top" width="15%"><?php echo $this->_tpl_vars['LBL_fax']; ?>
</td>
					<td align="left" valign="top" width="20%"><?php echo $this->_tpl_vars['strfax']; ?>
</td>
					<td align="right" valign="top" width="15%"><?php echo $this->_tpl_vars['LBL_email']; ?>
</td>
					<td align="left" valign="top" width="15%"><?php echo $this->_tpl_vars['stremail']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_phone_alt1_qualifier']; ?>
</td>
					<td align="left" valign="top" colspan="5"><?php echo $this->_tpl_vars['strphone_alt1_qualifier']; ?>
&nbsp;<?php echo $this->_tpl_vars['strphone_alt1']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_phone_alt2_qualifier']; ?>
</td>
					<td align="left" valign="top" colspan="5"><?php echo $this->_tpl_vars['strphone_alt2_qualifier']; ?>
&nbsp;<?php echo $this->_tpl_vars['strphone_alt2']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_phone_alt3_qualifier']; ?>
</td>
					<td align="left" valign="top" colspan="5"><?php echo $this->_tpl_vars['strphone_alt3_qualifier']; ?>
&nbsp;<?php echo $this->_tpl_vars['strphone_alt3']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_phone_alt4_qualifier']; ?>
</td>
					<td align="left" valign="top" colspan="5"><?php echo $this->_tpl_vars['strphone_alt4_qualifier']; ?>
&nbsp;<?php echo $this->_tpl_vars['strphone_alt4']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_phone_alt5_qualifier']; ?>
</td>
					<td align="left" valign="top" colspan="5"><?php echo $this->_tpl_vars['strphone_alt5_qualifier']; ?>
&nbsp;<?php echo $this->_tpl_vars['strphone_alt5']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_phone_alt6_qualifier']; ?>
</td>
					<td align="left" valign="top" colspan="5"><?php echo $this->_tpl_vars['strphone_alt6_qualifier']; ?>
&nbsp;<?php echo $this->_tpl_vars['strphone_alt6']; ?>
</td>
				</tr>
				</table>
				</fieldset>
				
				
				<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
				<legend><strong>Directory Information</strong></legend>
				<table width="100%" border="0" cellspacing="1" cellpadding="1">
				<tr>
					<td align="right" valign="top"><font color="#FF0000">*</font>Service Level: </td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['strServiceLevelTable']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_selspecialty_type1']; ?>
</td>
					<td align="left" valign="top" ><?php echo $this->_tpl_vars['strSpecialtyTable']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_active_start_time']; ?>
</td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['stractive_start_time']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_active_start_time_one']; ?>
</td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['stractive_start_time_one']; ?>
&nbsp;(HH:MM:SS 24-Hour)</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_active_end_time']; ?>
</td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['stractive_end_time']; ?>
</td>
				</tr>
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_active_end_time_one']; ?>
</td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['stractive_end_time_one']; ?>
&nbsp;(HH:MM:SS 24-Hour)</td>
				</tr>
				
					<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_twenty_four_hour_flag']; ?>
</td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['strtwenty_four_hour_flag']; ?>
</td>
					</tr>
					
				<tr>
					<td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_fax_portal']; ?>
</td>
					<td align="left" valign="top"><?php echo $this->_tpl_vars['strfax_portal']; ?>
</td>
				</tr>
				</table>
				</fieldset>

										</fieldset>
								</td>
          </tr>
		  <tr>
		  	<td align="center" colspan="2" ><input type='submit' name='smt_submit' value='Save' onclick='return submit_form(this.form);' class='btn' >
              &nbsp;
            <input type="button" value="Cancel" name="btn_reset"  class="btn" onclick="window.location='index.php?file=med_prescriber_master';"/ >
            </td>
          </tr>
  </table>
  </td>
  </tr>
  </table>
</form>
<?php echo '
<script language="Javascript">
// If Action is Add Location then we need to submit only SPI ROOT and remove the
// Location part which is the last 3 digits of the complete SPI.
/*if(document.getElementById(\'hid_add_location\').value == \'YES\')
{
	var SPI		=	document.getElementById("Tatxt_spi").value;
	SPI			=	SPI.substring(0,10);
	document.getElementById("Tatxt_spi").value	=	SPI;
}*/
// To set Action based on radio selection
$(document).ready(function(){
	$("[name=\'Rrad_action\']").click(function(){
		$("#hid_action").val($(this).val());
	});
});
function extraValid()
{
	var strSpecialtyQualifier		=	document.getElementById("slt_specialty_qualifier").value;
	var strSpecialtyCodePrimary		=	document.getElementById("Taslt_specialty_code_primary").value;
	
	if(strSpecialtyQualifier != \'\' && strSpecialtyCodePrimary == \'\')
	{
		alert("Please provide Specialty Code ");
		return false;
	}
	else if(strSpecialtyQualifier == \'\' && strSpecialtyCodePrimary != \'\')
	{
		alert("Please provide Speciality Qualifier ");
		return false;
	}
	
	strAlertMessage	=	\'\';
	strFocusField	=	\'\';
	
	intNPI			=	getElement("TaRtxt_npi").value;
	if(intNPI != \'\' && intNPI.length != 10)
	{
		if(strAlertMessage == \'\')
			strFocusField	=	\'TaRtxt_npi\';
		
		strAlertMessage	+=	"NPI must be exactly 10 digits.\\n";
		setStyle(getElement("TaRtxt_npi"));
	}
	
	strZip			=	getElement("InRtxt_zip").value;
	if(strZip.length != 5 && strZip.length != 9)
	{
		if(strAlertMessage == \'\')
			strFocusField	=	\'InRtxt_zip\';
		
		strAlertMessage	+=	"Please enter valid zip.\\n";
		setStyle(getElement("InRtxt_zip"));
	}
	
	/* --------------- ADDRESS VALIDATION [START] ----------------*/
	strAddress1	=	Trim(getElement("TaRtxt_address_line1").value);
	arrAddress1	=	strAddress1.split(" ");

	if(arrAddress1.length == 1)
	{
		if(strAlertMessage == \'\')
			strFocusField	=	\'TaRtxt_address_line1\';
			
		strAlertMessage	+=	"Please enter valid Address Line 1.\\n";
		setStyle(getElement("TaRtxt_address_line1"));
	}
	
	strAddress2	=	Trim(getElement("Tatxt_address_line2").value);
	arrAddress2	=	strAddress2.split(" ");
	if(arrAddress2.length == 1 && strAddress2 != \'\')
	{
		if(strAlertMessage == \'\')
			strFocusField	=	\'Tatxt_address_line2\';
			
		strAlertMessage	+=	"Please enter valid Address Line 2.\\n";
		setStyle(getElement("Tatxt_address_line2"));
	}
	/* --------------- ADDRESS VALIDATION [END] ----------------*/
	
	/* --------------- PHONE VALIDATION [START] ----------------*/
	
	//Primary Phone
	objPhone	=	getElement("TaRtxt_phone_primary");
	if(checkPhoneValidation(objPhone))
	{
		if(strAlertMessage == \'\')
			strFocusField	=	\'TaRtxt_phone_primary\';
		
		strAlertMessage	+=	"Please enter valid Primary Phone.\\n";
		setStyle(getElement(\'TaRtxt_phone_primary\'));
	}
	
	/* -------------- FAX VALIDATION [START] ---------------*/
	objFax	=	getElement("TaRtxt_fax");
	if(checkPhoneValidation(objFax))
	{
		if(strAlertMessage == \'\')
			strFocusField	=	\'TaRtxt_fax\';
		
		strAlertMessage	+=	"Please enter valid Fax.\\n";
		setStyle(getElement(\'TaRtxt_fax\'));
	}
	/* --------------- FAX VALIDATION [END] ----------------*/
	
	//Alternate Phone..
	for(intIndex = 1; intIndex <= 6; intIndex++)
	{
		objPhone	=	getElement("Tatxt_phone_alt"+intIndex);
		strPhone	=	objPhone.value;

		if(strPhone != \'\')
		{
			if(checkPhoneValidation(objPhone))
			{
				if(strAlertMessage == \'\')
					strFocusField	=	\'Tatxt_phone_alt\'+intIndex;
				
				strAlertMessage	+=	"Please enter valid Alternate Phone "+intIndex+".\\n";
				setStyle(getElement(\'Tatxt_phone_alt\'+intIndex));
			}
		}
	}
	/* --------------- PHONE VALIDATION [END] ----------------*/
	
	/*----------------- SPECIALTY VALIDATION [START] ------------*/
	intSpecialtySelected = 0;
	$.each($("[id^=\'Tachk_specialty_type_\']"),function()
	{
	    if($(this).attr("checked"))
	    {
		intSpecialtySelected++;
	    }
	});
	
	if(intSpecialtySelected > 4)
	{
	    strAlertMessage	+=	"Directory Specialty (SpecialtyID) only allows up to 4 selections.\\n";
	}
	/*----------------- SPECIALTY VALIDATION [END] --------------*/
	
	/* --------------- TIME VALIDATION [START] --------------*/
	objTime		=	getElement("Tatxt_active_start_time_one");
	strTime		=	objTime.value;
	
	if(strTime != \'\')
	{
		if(checkTimeValidation(objTime))
		{
			if(strAlertMessage == \'\')
				strFocusField	=	\'Tatxt_active_start_time_one\';
			
			strAlertMessage	+=	"Please enter valid Active Start Time (UTC).\\n";
			setStyle(getElement(\'Tatxt_active_start_time_one\'));
		}
	}
	
	objTime		=	getElement("Tatxt_active_end_time_one");
	strTime		=	objTime.value;
	
	if(strTime != \'\')
	{
		if(checkTimeValidation(objTime))
		{
			if(strAlertMessage == \'\')
				strFocusField	=	\'Tatxt_active_end_time_one\';
			
			strAlertMessage	+=	"Please enter valid Active End Time (UTC).\\n";
			setStyle(getElement(\'Tatxt_active_end_time_one\'));
		}
	}
	/* --------------- TIME VALIDATION [END] ----------------*/
	
	if(strAlertMessage != \'\')
	{
		alert(strAlertMessage);
		eval(strStyle);
		if(strFocusField != \'\')
		{
			getElement(strFocusField).focus();
		}
		return false;
	}

	//Enable all form fields before submitting...
	enableDisableAllChildControls(\'td_basic_info\',false);
}

//Function to check Phone validation.. format should be 1234567890x1234567890
function checkPhoneValidation(objPhone)
{
	//By default take blnError as false..
	blnError	=	false;
	
	strPhone	=	objPhone.value;
	arrPhone	=	strPhone.split("x");
	
	/*
	* first part is phone and second part is extension...
	*/
	
	if(arrPhone[0].length != 10)	//If phone part length is less than 10 then error...
		blnError	=	true;
	else
	{
		if(arrPhone[1] == \'\')
		{
			objPhone.value = strPhone.replace("x","");
		}
		
		strPhone	=	strPhone.replace("x","");
		if(isNaN(strPhone))
			blnError	=	true;
	}
	
	return blnError;
}

//Function to check Time validation...
function checkTimeValidation(objTime)
{
	//By default take blnError as false..
	blnError	=	false;
	strTime		=	objTime.value;
	arrTime		=	strTime.split(":");

	if(isNaN(strTime.replace(/:/g,"")))
		blnError	=	true;
	else if(arrTime[0] >= 24 ||  arrTime[1] > 59 || arrTime[2] > 59)
		blnError	=	true;

	return blnError;	
}


hideShowPrescriberDetail();
function hideShowPrescriberDetail()
{
	if(getElement("Rrad_action") != null)
		strServiceActionValue	=	getRadioCheckedValue(\'Rrad_action\');
	else
		strServiceActionValue	=	getElement(\'hid_action\').value;

	if(strServiceActionValue == \'ADD_PRESCRIBER_LOCATION\' || strServiceActionValue == \'UPDATE_PRESCRIBER_LOCATION\')
	{
		enableDisableAllChildControls(\'td_basic_info\',true);
	}
	else
	{
		enableDisableAllChildControls(\'td_basic_info\',false);
	}
	
}

function enableDisableAllChildControls(intDivId,blnDisable)
{
	$.each($("#"+intDivId+" :input"),function()
	{
		$(this).attr(\'disabled\',blnDisable);
	});
}



function getRadioCheckedValue(strFieldName)
{
	var radioObj	=	document.getElementById(strFieldName);
	radioObj		=	document.getElementsByName(radioObj.name);
	var radioLength = 	radioObj.length;
	
	strValue		=	\'\';
	for(var i = 0; i < radioLength; i++) {
		if(radioObj[i].checked) {
			strValue	= radioObj[i].value;
		}
	}
	return strValue;
}
</script>
'; ?>