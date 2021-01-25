<?php /* Smarty version 2.6.9, created on 2019-11-13 20:17:48
         compiled from ./middle/med_page_listings.htm */ ?>
<form name="frm_page_listings" id="frm_page_listings" method="post" action="index.php">
  <input type="hidden" name="file" id="file"  value="<?php echo $this->_tpl_vars['strFile']; ?>
">
  <input type="hidden" name="hid_button_id" id="hid_button_id"  value="">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hidin_module_id" id="hidin_module_id" value="<?php echo $this->_tpl_vars['intModuleId']; ?>
" />
  <input type="hidden" name="hid_page_type" id="hid_page_type" value="<?php echo $this->_tpl_vars['strPageType']; ?>
" />
  <input type="hidden" name="med_delete_url" id="med_delete_url" value="med_page_add_action" />
  <input type="hidden" name="hid_max_row_limit" id="hid_max_row_limit" value="<?php echo $this->_tpl_vars['intShowMaxRows']; ?>
" />
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-bor">
    <tr>
      <td align="left" height="24" colspan="2"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /> <font class="bold-text"><?php echo $this->_tpl_vars['strModuleName']; ?>
</font></td>
    </tr>
    <tr>
      <td height="1" class="blue-bg" colspan="2"></td>
    </tr>
    <tr>
      <td height="1" colspan="2">&nbsp;</td>
    </tr>
    <tr>
      <td height="1" colspan="2" align="right"><font class="blue-text-bold-01">Show:</font>&nbsp;<?php echo $this->_tpl_vars['strShowRows']; ?>
 row(s) <font class="blue-text-bold-01">Search:</font>&nbsp;<?php echo $this->_tpl_vars['strSearch']; ?>
&nbsp;
        <input name="btn_submit" value="Search" class="btn" type="submit" onclick='return submitSearchForm(this.form);'>
      </td>
    </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
    <tr>
      <td colspan="2" height="25"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
    </tr>
    <?php endif; ?>
    <tr>
      <td height="2" colspan="2"></td>
    </tr>
    <tr>
      <td colspan="2" align="center"><?php echo $this->_tpl_vars['strPage']; ?>
</td>
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