<?php /* Smarty version 2.6.9, created on 2019-11-12 23:21:19
         compiled from ./middle/med_view_prescriber_master.htm */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Employee Module of Meditab Online Support</title>
<link href="./images/med_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="./images/med_modal_message.css" type="text/css">
</head>
<?php echo '
<script type="text/javascript" src="./../base/meditab/med_quicklist.js"></script>
<script type="text/javascript" src="./../base/meditab/med_common.js"></script>
<script type="text/javascript" src="./base/med_common.js"></script>
<script type="text/javascript" src="./../base/jsoverlib/overlibmws.js"></script>
<script type="text/javascript" src="./../base/jsoverlib/overlibmws_iframe.js"></script>
'; ?>

<body style="margin:5px;">
<form name="frm_list_record" id="frm_list_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file"  value="med_out_message_tran">
  <input type="hidden" name="hid_button_id" id="hid_button_id"  value="<?php echo $this->_tpl_vars['strButtonId']; ?>
">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="L">
  <input type="hidden" name="hid_mt_tran_id" id="hid_mt_tran_id"  value="<?php echo $this->_tpl_vars['intTranId']; ?>
">
  <input type="hidden" name="hid_max_row_limit" id="hid_max_row_limit" value="<?php echo $this->_tpl_vars['intShowMaxRows']; ?>
" />
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tab-bor">
    <tr>
      <td height="24" align="left" valign="middle"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="6" /> <font class="bold-text"><?php echo $this->_tpl_vars['strModuleName']; ?>
</font></td>
      <td width="10%" align="left"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="#" onclick="self.parent.tb_remove();">Close</a></td>
    </tr>
    <tr>
      <td height="1" class="blue-bg" colspan="2"></td>
    </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
    <tr>
      <td height="25" colspan="2"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
    </tr>
    <?php endif; ?>
    <tr>
      <td height="2" colspan="2"></td>
    </tr>
    <tr>
      <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
          	<td height="2" colspan="4"><table cellpadding="1" cellspacing="1" width="100%" border="0">
          <tr>
            <td align="center" valign="top">
				<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
				<legend><strong>Prescriber Detail</strong></legend>
					<table width="100%" border="1" cellpadding="2" cellspacing="2" style="border-collapse:collapse; border:1px solid #CCCCCC">
					<tr>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_spi']; ?>
</td>
						<td align="left" valign="top" ><?php echo $this->_tpl_vars['strspi']; ?>
</td>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_prefix_name']; ?>
</td>
						<td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['strprefix_name']; ?>
</td>
					</tr>
					<tr>
						<td align="right" valign="top" width="15%" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_first_name']; ?>
</td>
						<td align="left" valign="top" width="18%"><?php echo $this->_tpl_vars['strfirst_name']; ?>
</td>
						<td align="right" valign="top" width="15%" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_middle_name']; ?>
</td>
						<td align="left" valign="top" width="18%"><?php echo $this->_tpl_vars['strmiddle_name']; ?>
</td>
						<td align="right" valign="top" width="15%" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_last_name']; ?>
</td>
						<td align="left" valign="top" width="19%"><?php echo $this->_tpl_vars['strlast_name']; ?>
</td>
					</tr>
					
					<tr>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_suffix_name']; ?>
</td>
						<td align="left" valign="top"><?php echo $this->_tpl_vars['strsuffix_name']; ?>
</td>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_specialty_code_primary']; ?>
</td>
						<td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['strspecialty_code_primary']; ?>
</td>
					</tr>
					
					</table>
					</fieldset>
			</td>
          </tr>
		  
		  
		  <tr>
            <td align="center" valign="top">
				<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
				<legend><strong>Prescriber Location</strong></legend>
					<table width="100%" border="1" cellpadding="2" cellspacing="2" style="border-collapse:collapse; border:1px solid #CCCCCC">
					
					<tr>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_dea']; ?>
</td>
						<td align="left" valign="top"><?php echo $this->_tpl_vars['strdea']; ?>
</td>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_state_license_number']; ?>
</td>
						<td align="left" valign="top" ><?php echo $this->_tpl_vars['strstate_license_number']; ?>
</td>
					</tr>
					
					<tr>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_clinic_name']; ?>
</td>
						<td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['strclinic_name']; ?>
</td>
					</tr>
					
					<tr>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_address_line1']; ?>
</td>
						<td align="left" valign="top"><?php echo $this->_tpl_vars['straddress_line1']; ?>
</td>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_address_line2']; ?>
</td>
						<td align="left" valign="top" ><?php echo $this->_tpl_vars['straddress_line2']; ?>
</td>
					</tr>
					
					<tr>
						<td align="right" valign="top" width="15%" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_city']; ?>
</td>
						<td align="left" valign="top" width="35%"><?php echo $this->_tpl_vars['strcity']; ?>
</td>
						<td align="right" valign="top" width="15%" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_state']; ?>
</td>
						<td align="left" valign="top" width="35%"><?php echo $this->_tpl_vars['strstate']; ?>
</td>
					</tr>
					
					<tr>
						<td align="right" valign="top"  bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_zip']; ?>
</td>
						<td align="left" valign="top" ><?php echo $this->_tpl_vars['strzip']; ?>
</td>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_phone_primary']; ?>
</td>
						<td align="left" valign="top"><?php echo $this->_tpl_vars['strphone_primary']; ?>
</td>
					</tr>
					
					<tr>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_phone_alt1_qualifier']; ?>
</td>
						<td align="left" valign="top"><?php if ($this->_tpl_vars['strphone_alt1'] != ''): ?>(<?php echo $this->_tpl_vars['strphone_alt1_qualifier']; ?>
)&nbsp;<?php endif;  echo $this->_tpl_vars['strphone_alt1']; ?>
</td>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_phone_alt2_qualifier']; ?>
</td>
						<td align="left" valign="top"><?php if ($this->_tpl_vars['strphone_alt2'] != ''): ?>(<?php echo $this->_tpl_vars['strphone_alt2_qualifier']; ?>
)&nbsp;<?php endif;  echo $this->_tpl_vars['strphone_alt2']; ?>
</td>
					</tr>
					
					<tr>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_phone_alt3_qualifier']; ?>
</td>
						<td align="left" valign="top"><?php if ($this->_tpl_vars['strphone_alt3'] != ''): ?>(<?php echo $this->_tpl_vars['strphone_alt3_qualifier']; ?>
)&nbsp;<?php endif;  echo $this->_tpl_vars['strphone_alt3']; ?>
</td>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_phone_alt4_qualifier']; ?>
</td>
						<td align="left" valign="top"><?php if ($this->_tpl_vars['strphone_alt4'] != ''): ?>(<?php echo $this->_tpl_vars['strphone_alt4_qualifier']; ?>
)&nbsp;<?php endif;  echo $this->_tpl_vars['strphone_alt4']; ?>
</td>
					</tr>
					
					<tr>
						
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_phone_alt5_qualifier']; ?>
</td>
						<td align="left" valign="top"><?php if ($this->_tpl_vars['strphone_alt5'] != ''): ?>(<?php echo $this->_tpl_vars['strphone_alt5_qualifier']; ?>
)&nbsp;<?php endif;  echo $this->_tpl_vars['strphone_alt5']; ?>
</td>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_phone_alt6_qualifier']; ?>
</td>
						<td align="left" valign="top"><?php if ($this->_tpl_vars['strphone_alt6'] != ''): ?>(<?php echo $this->_tpl_vars['strphone_alt6_qualifier']; ?>
)&nbsp;<?php endif;  echo $this->_tpl_vars['strphone_alt6']; ?>
</td>
					</tr>
					
					<tr>
						<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_email']; ?>
</td>
						<td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['stremail']; ?>
</td>
					</tr>
					</table>
					</fieldset>
			</td>
          </tr>
		  
		<tr>
            <td align="center" valign="top">
				<fieldset style="border:1px solid #BED9F0; border-collapse:collapse;">
				<legend><strong>Directory Information</strong></legend>
				<table width="100%" border="1" cellpadding="2" cellspacing="2" style="border-collapse:collapse; border:1px solid #CCCCCC">
				<tr>
					<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_partner_account']; ?>
</td>
					<td align="left" valign="top" ><?php echo $this->_tpl_vars['strpartner_account']; ?>
</td>
					<td align="right" valign="top" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_service_level_bits']; ?>
</td>
					<td align="left" valign="top" ><?php echo $this->_tpl_vars['strservice_level_bits']; ?>
</td>
				</tr>

				<tr>
					<td align="right" valign="top" width="15%" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_active_start_time']; ?>
</td>
					<td align="left" valign="top" width="35%"><?php echo $this->_tpl_vars['stractive_start_time']; ?>
</td>
					<td align="right" valign="top" width="15%" bgcolor="#EFEFEF"><?php echo $this->_tpl_vars['LBL_active_end_time']; ?>
</td>
					<td align="left" valign="top" width="35%"><?php echo $this->_tpl_vars['stractive_end_time']; ?>
</td>
				</tr>
				</table>
				</fieldset>
			</td>
		</tr>
		  
  		</table></td>
          </tr>  
        </table></td>
    </tr>
  </table>
</form>
</body>
</html>