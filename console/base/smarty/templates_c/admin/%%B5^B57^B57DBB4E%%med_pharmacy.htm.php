<?php /* Smarty version 2.6.9, created on 2019-11-12 18:53:23
         compiled from ./middle/med_pharmacy.htm */ ?>
<form name="frm_list_record" id="frm_list_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file"  value="med_pharmacy">
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
                              <td align="right" valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_ncpdpid']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_strncpdpid']; ?>
</td>
							  <td align="right" valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_store_name']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_strstore_name']; ?>
</td>
							   <td align="right" valign="top" ><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_store_number']; ?>
</font></td>
                              <td align="left" 	valign="top"><?php echo $this->_tpl_vars['Sr_strstore_number']; ?>
</td>
							  <td align="right"  valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_phone_primary']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_strphone_primary']; ?>
</font></td>
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
                              <td align="right"  valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_address_line1']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_straddress_line1']; ?>
</font></td>
							  <td align="right"  valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_city']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_strcity']; ?>
</font></td>
							  <td align="right" valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_state']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_strstate']; ?>
</td>
							  <td align="right"  valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_zip']; ?>
</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['Sr_strzip']; ?>
</font></td>                              
                               </tr>
                          </table></td>                      
					  </tr>			  
					
					 <tr>
                        <td align="right">
							<table border="0" cellpadding="1" cellspacing="1">
							<tr>
								<td align="right" valign="top"><font class="blue-text-bold-01">Service Level:</font></td>
                              <td align="left" valign="top"><?php echo $this->_tpl_vars['strSLSelectBox']; ?>
</td>                                                  
                              <td><input name="btn_submit" value="Search" class="btn" onclick="return submitSearchForm(this.form);" type="submit"></td>  
							</tr>
							</table>
						</td>
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
            <td align="right"  valign="top" colspan="2"><img src="images/schedule-update.gif"  alt="Edit Pharmacy Request"  title="Edit Pharmacy Request" align="top"/>-&nbsp;Edit Pharmacy Request&nbsp;&nbsp;<img src="images/ico-inactive.gif" /> - Request Failed</td>
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