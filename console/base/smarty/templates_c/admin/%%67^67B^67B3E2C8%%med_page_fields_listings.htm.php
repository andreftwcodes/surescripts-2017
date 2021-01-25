<?php /* Smarty version 2.6.9, created on 2019-11-15 20:27:28
         compiled from ./middle/med_page_fields_listings.htm */ ?>
<form name="frm_page_fields_listings" id="frm_page_fields_listings" method="post" action="index.php">
<input type="hidden" name="file" id="file"  value="med_page_fields_listings">
<input type="hidden" name="hid_button_id" id="hid_button_id"  value="">
<input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
<input type="hidden" name="hidin_module_id" id="hidin_module_id" value="<?php echo $this->_tpl_vars['intModuleId']; ?>
" />
<input type="hidden" name="hid_page_type" id="hid_page_type" value="<?php echo $this->_tpl_vars['strPageType']; ?>
" />
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $this->_tpl_vars['intParentId']; ?>
" />
<input type="hidden" name="med_delete_url" id="med_delete_url" value="med_page_fields_listings" />
<table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-bor">
	<tr>
    <td align="left" height="24" width="45%"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /><span class="bold-text"><span onclick="gotoPageSetting();">Page Settings:</span>&nbsp;<?php echo $this->_tpl_vars['strModuleName']; ?>
<span></td>
	<td width="49%" align="right" height="24"><a href="index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0"> All Tables</a>&nbsp;|
	<a href="index.php?file=med_page_add&hidin_module_id=0&table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hid_page_type=E">View Table</a>&nbsp;|
	  <a href="index.php?file=med_page_multi_list&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Multi</a>&nbsp;|
      <span class="gray-text">View Fields</span>&nbsp;|
      <a href="index.php?file=med_page_buttons_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Buttons</a>&nbsp;|
	  <a href="index.php?file=med_page_search_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Search</a>	  </td>
      <td width="6%" align="right"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="javascript:history.back();">Back</a>&nbsp;</td>
  </tr>
	 <tr>
		<td height="1" class="blue-bg" colspan="3"></td>
	  </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
	<tr>
		<td colspan="3" height="25"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
	</tr>
	<?php endif; ?>
	<tr>
	  <td height="2" colspan="3" align="right">&nbsp;</td>
	</tr>
	<tr>
	  <td height="2" colspan="3" align="right">
	  <table cellpadding="1" cellspacing="1" border="0">
		<tr>
		<td valign="top"><?php echo $this->_tpl_vars['strFieldListCombo']; ?>
</td>
		<td><input type='submit' name=btn1 value='Go'  class='btn'  ></td>
		<td class="blue-text-bold-01"><?php echo $this->_tpl_vars['Sr_LBL_table_list']; ?>
</td>
		<td><?php echo $this->_tpl_vars['Sr_strtable_list']; ?>
</td>
		<td><input type='button' name=btn1 value='Copy' onClick="doAction(this,'list2','1011:C:0:med_page_fields_listings',1,'Are you sure want to Copy?')" class='btn'  ></td>
		</tr>
		</table>
	  </td>
	</tr>
	<tr>
	  <td height="2" colspan="3" align="right">&nbsp;</td>
	</tr>
	<tr>
	  <td colspan="3" align="center"><?php echo $this->_tpl_vars['strPage']; ?>
</td>
	</tr>
</table>
</form>