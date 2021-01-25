<?php /* Smarty version 2.6.9, created on 2019-11-12 18:51:03
         compiled from ./middle/med_in_message_transaction_addedit.htm */ ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file" value="med_in_message_transaction_action"/>
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
  <input type="hidden" id="hid_tran_id" name="hid_tran_id" value="<?php echo $this->_tpl_vars['intTranId']; ?>
">
  <table border="0" cellpadding="0" cellspacing="0"  width="100%" class="tab-bor" align="center">
    <tr>
      <td height="24" align="left"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /> <font class="bold-text"> <?php echo $this->_tpl_vars['strTitle']; ?>
</font></td>
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
          <tr>
            <td width="15%" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_from_id']; ?>
</td>
            <td width="20%" align="left" valign="top"><?php echo $this->_tpl_vars['strfrom_id']; ?>
</td>
            <td width="15%" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_to_id']; ?>
</td>
            <td align="left" valign="top"><?php echo $this->_tpl_vars['strto_id']; ?>
</td>
		  </tr>
          <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_message_id']; ?>
</td>
            <td align="left" valign="top"><?php echo $this->_tpl_vars['strmessage_id']; ?>
</td>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_related_message_id']; ?>
</td>
            <td align="left" valign="top"><?php echo $this->_tpl_vars['strrelated_message_id']; ?>
</td>
		  </tr>
		  <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_sent_time_in_message_date']; ?>
</td>
            <td align="left" valign="top" colspan="3">
			<table border="0" cellpadding="0" cellspacing="0" align="left">
			<tr>
				<td align="left"><?php echo $this->_tpl_vars['strsent_time_in_message_date']; ?>
</td>
				<td align="left"><?php echo $this->_tpl_vars['strsent_time_in_message_time']; ?>
</td>
				<td align="left"><?php echo $this->_tpl_vars['strsent_time_in_message_ampm']; ?>
</td>
			</tr>
			</table>
			</td>
		  </tr>
		  <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_sms_version']; ?>
</td>
            <td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['strsms_version']; ?>
</td>
		  </tr>
		  <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_app_name']; ?>
</td>
            <td align="left" valign="top"><?php echo $this->_tpl_vars['strapp_name']; ?>
</td>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_app_version']; ?>
</td>
            <td align="left" valign="top"><?php echo $this->_tpl_vars['strapp_version']; ?>
</td>
		  </tr>
		  <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_vendor_name']; ?>
</td>
            <td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['strvendor_name']; ?>
</td>
		  </tr>
		  <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_received_time_date']; ?>
</td>
            <td align="left" valign="top" colspan="3">
			<table border="0" cellpadding="0" cellspacing="0" align="left">
			<tr>
				<td align="left"><?php echo $this->_tpl_vars['strreceived_time_date']; ?>
</td>
				<td align="left"><?php echo $this->_tpl_vars['strreceived_time_time']; ?>
</td>
				<td align="left"><?php echo $this->_tpl_vars['strreceived_time_ampm']; ?>
</td>
			</tr>
			</table>
			</td>
		  </tr>
		  <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_edi_message']; ?>
</td>
            <td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['stredi_message']; ?>
</td>
		  </tr>
		  <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_message_status']; ?>
</td>
            <td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['strmessage_status']; ?>
</td>
		  </tr>
		  <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_meditab_response_status']; ?>
</td>
            <td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['strmeditab_response_status']; ?>
</td>
		  </tr>
          <tr>
            <td align="right" valign="top"><?php echo $this->_tpl_vars['LBL_error_note']; ?>
</td>
            <td align="left" valign="top" colspan="3"><?php echo $this->_tpl_vars['strerror_note']; ?>
</td>
		  </tr>
		  
		  <tr>
		  	<td align="right"></td>
            <td align="left" colspan="" ><input type='submit' name='smt_submit' value='Save' onclick='return submit_form(this.form);' class='btn' >
              &nbsp;
              <input type="reset" value="Cancel" name="btn_reset"  class="btn" >
            </td>
          </tr>
  </table>
  </td>
  </tr>
  </table>
</form>

<?php echo '
<script>
/* ER_10663 [START] */
$( document ).ready(function() {
    var strPageType		=		$("#hid_page_type").val();
	if(strPageType = \'E\')
	{
		//Hide Datepicker Icon
		$("#clt_Dttxt_sent_time_in_message_date").hide();
		$("#clt_Dttxt_received_time_date").hide();
	}
});
/* ER_10663 [END] */
</script>
'; ?>