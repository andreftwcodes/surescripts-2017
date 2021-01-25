<?php /* Smarty version 2.6.9, created on 2019-11-12 18:50:44
         compiled from ./middle/med_out_message_transaction.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', './middle/med_out_message_transaction.htm', 36, false),)), $this); ?>
<form name="frm_list_record" id="frm_list_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file"  value="med_out_message_transaction">
  <input type="hidden" name="hid_button_id" id="hid_button_id"  value="<?php echo $this->_tpl_vars['strButtonId']; ?>
">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="L">
  <input type="hidden" name="hid_max_row_limit" id="hid_max_row_limit" value="<?php echo $this->_tpl_vars['intShowMaxRows']; ?>
" />
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tab-bor">
    <tr>
      <td height="24" align="left" valign="middle"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="6" /> <font class="bold-text"><?php echo $this->_tpl_vars['strTitle']; ?>
</font></td>
      <td width="5%" align="left"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="javascript:history.back();">Back</a></td>
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
      <td  align="right" valign="middle" colspan="2" ><table  border="0" cellpadding="0" cellspacing="0" width="100%">
          
		  <tr>
            <td class="srch"><table border="0" cellpadding="0" cellspacing="0" width="100%">
                
				<tr>
                  <td class="vsrch"><table border="0" cellpadding="0" cellspacing="0" width="100%">
					  <tr>
                        <td align="right"><table border="0" cellpadding="1" cellspacing="1">
                            <tr>
                              <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_sent_time_from']; ?>
</font></td>
                              <td align="left" valign="middle"><?php echo ((is_array($_tmp=$this->_tpl_vars['Sr_strsent_time_from'])) ? $this->_run_mod_handler('replace', true, $_tmp, '(mm-dd-yyyy)', '') : smarty_modifier_replace($_tmp, '(mm-dd-yyyy)', '')); ?>
</td>
							  <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_sent_time_to']; ?>
</font></td>
                              <td align="left" valign="middle"><?php echo ((is_array($_tmp=$this->_tpl_vars['Sr_strsent_time_to'])) ? $this->_run_mod_handler('replace', true, $_tmp, '(mm-dd-yyyy)', '') : smarty_modifier_replace($_tmp, '(mm-dd-yyyy)', '')); ?>
</td>
							   <td align="right" valign="middle" ><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_message_status']; ?>
</font></td>
                              <td align="left" 	valign="middle"><?php echo $this->_tpl_vars['Sr_strmessage_status']; ?>
</td>
								<td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_meditab_response_status']; ?>
</font></td>
								<td align="left"  valign="middle"><?php echo $this->_tpl_vars['Sr_strmeditab_response_status']; ?>
</td>								
							   <td align="right" valign="middle" ><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_message_from']; ?>
</font></td>
                              <td align="left" 	valign="middle"><?php echo $this->_tpl_vars['Sr_strmessage_from']; ?>
</td>
							  
							  </tr>
                          </table></td>
                      
					  </tr>
					  <tr>
                        <td align="right"><table border="0" cellpadding="1" cellspacing="1">
                         <tr>
							 <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_message_id']; ?>
</font></td>
							 <td align="left"  valign="middle"><?php echo $this->_tpl_vars['Sr_strmessage_id']; ?>
&nbsp;</td>   
							 <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_related_message_id']; ?>
</font></td>
							 <td align="left"  valign="middle"><?php echo $this->_tpl_vars['Sr_strrelated_message_id']; ?>
&nbsp;</td>  
							 <td align="right"  valign="middle"><font class="blue-text-bold-01">From (<?php echo $this->_tpl_vars['strFromMsgLabel']; ?>
):</font></td>
							 <td align="left" valign="middle"><?php echo $this->_tpl_vars['Sr_strFrom']; ?>
</font></td>
							 <td align="right" valign="middle"><font class="blue-text-bold-01">To (<?php echo $this->_tpl_vars['strToMsgLabel']; ?>
):</font></td>
							 <td align="left" valign="middle"><?php echo $this->_tpl_vars['Sr_strTo']; ?>
</td>
                         </tr>						 
                          </table></td>                      
					  </tr>		
                      <tr>
                        <td align="right"><table border="0" cellpadding="1" cellspacing="1">
                            <tr>
                             <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_show_rows']; ?>
</font></td>
                              <td align="left"  valign="middle"><?php echo $this->_tpl_vars['Sr_strshow_rows']; ?>
 row(s)&nbsp;</td>
                             <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_error_note']; ?>
</font></td>
                              <td align="left"  valign="middle"><?php echo $this->_tpl_vars['Sr_strerror_note']; ?>
</td>
							 <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_meditab_id']; ?>
</font></td>
                              <td align="left"  valign="middle"><?php echo $this->_tpl_vars['Sr_strmeditab_id']; ?>
 &nbsp;</td> 
							 <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_meditab_tran_id']; ?>
</font></td>
                              <td align="left"  valign="middle"><?php echo $this->_tpl_vars['Sr_strmeditab_tran_id']; ?>
 &nbsp;</td> 
                             
                              <td><input name="btn_submit" value="Search" class="btn" onclick="return submitSearchForm(this.form);" type="submit"></td>
							  <td align="center" width="4%"><input src="images/export.gif" alt="Export to Excel" title="Export to Excel" name="btn_export" value="Export"  type="image" onclick="return exportExcelFile(this.form);" /></td>
                            </tr>
                          </table></td>
                      </tr>
                    </table></td>
                
				</tr>
              </table></td>
          
		  </tr>
        </table></td>
    </tr> 
    <tr>
      <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" colspan="2"><?php echo $this->_tpl_vars['strPage']; ?>
</td>
          </tr>
          <tr>
            <td align="right"  valign="top" colspan="2"><img src="images/schedule-update.gif"  alt="Update In Message Transaction"  title="Update In Message Transaction" align="top"/>&nbsp;Update Out Message Transaction&nbsp;</td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>
<?php echo '
<script>
function extraSearchValid()
{
	if(checkRowLimit(document.getElementById("Sr_Intxt_show_rows"),document.getElementById("hid_max_row_limit").value) == false)
		return false;
}
</script>
'; ?>