<?php /* Smarty version 2.6.9, created on 2019-11-15 20:27:32
         compiled from ./middle/med_page_buttons_add.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', './middle/med_page_buttons_add.htm', 41, false),)), $this); ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php">
<input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
<input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
<input type="hidden" name="hid_module_id" id="hid_module_id"  value="<?php echo $this->_tpl_vars['intModuleId']; ?>
">
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $this->_tpl_vars['intParentId']; ?>
" />
<?php echo $this->_tpl_vars['strHidUpdateID'];  echo $this->_tpl_vars['strHidFile']; ?>

<input type="hidden" name="hidin_table_id" id="hidin_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
<table border="0" cellpadding="0" cellspacing="0"  width="100%" class="tab-bor" align="center">
<tr>
    <td width="45%" align="left" height="24"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /><span class="bold-text"><span onclick="gotoPageSetting();">Page Settings:</span>&nbsp;<?php echo $this->_tpl_vars['strModuleName']; ?>
<span></td>
	<td width="47%" align="right" height="24"><a href="index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0"> All Tables</a>&nbsp;|
	<a href="index.php?file=med_page_add&hidin_module_id=0&table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hid_page_type=E">View Table</a>&nbsp;|
	  <a href="index.php?file=med_page_multi_list&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Multi</a>&nbsp;|
      <a href="index.php?file=med_page_fields_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Fields</a>&nbsp;|
      <a href="index.php?file=med_page_buttons_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Buttons</a>&nbsp;|
	  <a href="index.php?file=med_page_search_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Search</a>	  </td>
      <td width="8%" align="right"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="javascript:history.back();">Back</a>&nbsp;</td>
  </tr>
   <tr>
	   <td height="1" class="blue-bg" colspan="7"></td>
	</tr>
	<?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
	<tr>
		<td colspan="7" height="25"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
	</tr>
	<?php endif; ?>
   <tr height="30">
    <td colspan="7">
		<table width='100%' cellpadding="0" cellspacing="0" border="0">
    	<tr><td height="10" colspan="2" align="right" ><?php if ($this->_tpl_vars['strPageType'] != 'A'): ?>
            <table border="0" cellspacing="0" cellpadding="0">
              <tr>
                <td align="center">Buttons:</td>
                <td align="center"><?php echo $this->_tpl_vars['strFieldCombo']; ?>
</td>
              </tr>
            </table>
            <?php endif; ?></td>
    	</tr>
		<tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_page_type']; ?>
</td>
				<?php $this->assign('name', ((is_array($_tmp=$this->_tpl_vars['LBL_page_type'])) ? $this->_run_mod_handler('replace', true, $_tmp, "<font color='#FF0000'>*</font>", "") : smarty_modifier_replace($_tmp, "<font color='#FF0000'>*</font>", ""))); ?>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strpage_type']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=BUTTON_PAGE_TYPE&ControlFlag=0',
				'<?php echo $this->_tpl_vars['name']; ?>
','TaRtxt_page_type',500,500);" class="help" alt="Zoom">
				</td>
			</tr>
			<tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_key_col']; ?>
</td>
				<?php $this->assign('col_name', ((is_array($_tmp=$this->_tpl_vars['LBL_key_col'])) ? $this->_run_mod_handler('replace', true, $_tmp, "<font color='#FF0000'>*</font>", "") : smarty_modifier_replace($_tmp, "<font color='#FF0000'>*</font>", ""))); ?>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strkey_col']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=BUTTON_KEY_COLUMN&ControlFlag=0',
				'<?php echo $this->_tpl_vars['col_name']; ?>
','TaRtxt_key_col',500,500);" class="help" alt="Zoom">
				</td>
			</tr>
			<tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_field_name_u']; ?>
</td>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strfield_name_u']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=BUTTON_UPDATE_FIELD&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_field_name_u']; ?>
','Tatxt_field_name_u',500,500);" class="help" alt="Zoom">
				</td>
			</tr>
			<tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_confirm']; ?>
</td>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strconfirm']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=BUTTON_CONFIRM_MSG&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_confirm']; ?>
','Tatxt_confirm',500,500);" class="help" alt="Zoom">
				</td>
			</tr>
			<tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_action']; ?>
</td>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['straction']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=BUTTON_ACTION&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_action']; ?>
','Tatxt_action',500,500);" class="help" alt="Zoom">
				</td>
			</tr>
			<tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_valign']; ?>
</td>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strvalign']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=1',
				'<?php echo $this->_tpl_vars['LBL_valign']; ?>
','Taslt_valign',500,500);" class="help" alt="Zoom">
				</td>
			</tr>
			<tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_halign']; ?>
</td>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strhalign']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=1',
				'<?php echo $this->_tpl_vars['LBL_halign']; ?>
','Taslt_halign',500,500);" class="help" alt="Zoom">
				</td>
			</tr>
			<tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_seq_no']; ?>
</td>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strseq_no']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_seq_no']; ?>
','Tatxt_seq_no',500,500);" class="help" alt="Zoom">
				</td>
		   </tr>
		   <tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_check_ref']; ?>
</td>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strcheck_ref']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=BUTTON_CHECK_REFERENCE&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_check_ref']; ?>
','Tatxt_check_ref',500,500);" class="help" alt="Zoom">
				</td>
		   </tr>
		   <tr>
				<td align='right' width='20%' valign='middle' height="24"><?php echo $this->_tpl_vars['LBL_cascade_action']; ?>
</td>
				<td align='left' height="24">&nbsp;<?php echo $this->_tpl_vars['strcascade_action']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=BUTTON_CHECK_CASCADE&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_cascade_action']; ?>
','Tatxt_cascade_action',500,500);" class="help" alt="Zoom">
				</td>
		   </tr>
		   
		   
			<tr><td colspan="2" height="4" ></td></tr>
			<tr>
				<td></td>
				<td><input type='submit' name='smt_submit' value='Submit' onclick='return submit_form(this.form);' class="btn" ></td>
			</tr>
			<tr><td colspan="2" height="10" ></td></tr>
		</table>
	</td>
  </tr> 
</table>
 </form>
 		