<?php /* Smarty version 2.6.9, created on 2019-11-12 18:51:18
         compiled from ./middle/med_out_message_history.htm */ ?>
<form name="frm_out_message_history" id="frm_out_message_history" method="post" action="index.php">
  <input type="hidden" name="file" id="file"  value="med_out_message_history">
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
                              <td align="right" valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_message_id']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_strmessage_id']; ?>
</td>
							  <td align="right" valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_date_from']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_strdate_from']; ?>
</td>
							   <td align="right" valign="top" ><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_date_to']; ?>
</font></td>
                              <td align="left" 	valign="top"><?php echo $this->_tpl_vars['Sr_strdate_to']; ?>
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
                            <td align="right" valign="middle"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_message_type']; ?>
</font></td>
                              <td align="left"  valign="middle"><?php echo $this->_tpl_vars['Sr_strmessage_type']; ?>
</td>
                                                         
                             <td><input name="btn_submit" value="Search" class="btn" onclick="return submitSearchForm(this.form);" type="submit"></td>
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