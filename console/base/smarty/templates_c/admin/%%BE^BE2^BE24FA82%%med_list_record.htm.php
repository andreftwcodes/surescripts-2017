<?php /* Smarty version 2.6.9, created on 2019-11-13 20:16:00
         compiled from ./middle/med_list_record.htm */ ?>
<form name="frm_list_record" id="frm_list_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file"  value="<?php echo $this->_tpl_vars['strFile']; ?>
">
  <input type="hidden" name="hid_button_id" id="hid_button_id"  value="<?php echo $this->_tpl_vars['strButtonId']; ?>
">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
  <input type="hidden" id="med_delete_url" name="med_delete_url" value="med_action">
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tab-bor">
    <tr>
      <td height="24" align="left" valign="middle"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="6" /> <font class="bold-text"><?php echo $this->_tpl_vars['strModuleName']; ?>
</font></td>
    </tr>
    <tr>
      <td height="1" class="blue-bg"></td>
    </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
    <tr>
      <td height="25"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
    </tr>
    <?php endif; ?>
    <?php if (( ! empty ( $this->_tpl_vars['strCustomSearch'] ) )): ?>
    <tr>
      <td  align="right" valign="middle"><table class="qlist" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td class="srch"><table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td class="vsrch"><table border="1" cellpadding="0" cellspacing="0">
                      <tr>
                      	<td><?php echo $this->_tpl_vars['strModule']; ?>
</td>
						<td><?php echo $this->_tpl_vars['strCustomSearch']; ?>
</td>
						<td><input name="smt_submit" value=" Search " class="btn" type="submit"></td>
					 </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
        </table></td>
    </tr>
    <?php endif; ?>
	
	<?php if ($this->_tpl_vars['intTableId'] == '105'): ?>
	<tr>
      <td  align="right" valign="middle"><table class="qlist" border="0" cellpadding="0" cellspacing="0" width="100%">
          <tr>
            <td class="srch"><table border="0" cellpadding="0" cellspacing="0" width="100%">
                <tr>
                  <td class="vsrch"><table border="0" cellpadding="0" cellspacing="0">
                      <tr>
                      	<td align="right" valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_team_title']; ?>
</font></td>
						<td align="left"  valign="top"><font class="normal-text"><?php echo $this->_tpl_vars['Sr_strteam_title']; ?>
</font></td>
						
						<td align="right" valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_status']; ?>
</font></td>
						<td align="left"  valign="top"><font class="normal-text"><?php echo $this->_tpl_vars['Sr_strstatus']; ?>
</font></td>

                      	<td align="right" valign="top"><font class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_employee']; ?>
</font></td>
						<td align="left"  valign="top"><font class="normal-text"><?php echo $this->_tpl_vars['Sr_stremployee']; ?>
</font></td>
						<td align="left"  valign="bottom"><input name="btn_submit" value="Search" class="btn" onclick="return submit_form(this.form);" type="submit"></td>									
						<td align="left"  valign="bottom"><input src="images/export.gif" alt="Export to Excel" name="btn_export" value="Export"  type="image" onclick="return exportExcelFile(this.form);" title="Export to Excel"/></td>
					 </tr>
                    </table></td>
                </tr>
              </table></td>
          </tr>
        </table></td>
    </tr>
	
	
	<?php endif; ?>
    <tr>
      <td height="2" colspan="2"></td>
    </tr>
    <tr>
    
    <td colspan="2">
	
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
	<?php if ($this->_tpl_vars['intTableId'] != '105'): ?>
      <tr>
        <td align="right" valign="middle"><font class="blue-text-bold-01">Export to Excel:</font>&nbsp;</td>
        <td align="left" valign="middle" width="3%"><input src="images/export.gif" alt="Export to Excel" name="btn_export" value="Export"  type="image" onclick="return exportExcelFile(this.form);" title="Export to Excel"/></td>
	  </tr>
	<?php endif; ?>
      <tr>
        <td align="center" colspan="2"><?php echo $this->_tpl_vars['strPage']; ?>
</td>
      </tr>
      <?php if ($this->_tpl_vars['intTableId'] == 105 && $this->_tpl_vars['strPageType'] == 'L'): ?>
      <tr>
        <td align="right"  valign="top" colspan="2"><img src="images/emp_group.gif"  alt="Employees of Group"  title="Employees of Group" align="top"/>&nbsp;Employees of Team&nbsp;</td>
      </tr>
      
      <?php endif; ?>
    </table>
    </td>
    
    </tr>
    
  </table>
</form>