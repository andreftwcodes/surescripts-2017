<?php /* Smarty version 2.6.9, created on 2019-11-13 20:18:38
         compiled from ./middle/med_add_combo_info.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'cat', './middle/med_add_combo_info.htm', 86, false),)), $this); ?>
<form name="frm_add_combo_info" id="frm_add_combo_info" method="post" action="index.php">
<input type="hidden" name="file" id="file" value="med_combo_info_action" />
<input type="hidden" name="hid_button_id" id="hid_button_id"  value="<?php echo $this->_tpl_vars['strButtonId']; ?>
">
<input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
<input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
<input type="hidden" name="hid_generateRows" id="hid_generateRows" value="<?php echo $this->_tpl_vars['intGenerateRows']; ?>
" />
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $this->_tpl_vars['intParentId']; ?>
" />
<?php echo $this->_tpl_vars['strcombo_id']; ?>

<?php echo $this->_tpl_vars['strEXTRA_FIELD_VALIDATION']; ?>

<script language="javascript" src="./base/med_combo_info.js"></script>
<table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tab-bor">
      <tr>
        <td height="24" align="left" valign="middle"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="6" /> <font class="bold-text"><?php echo $this->_tpl_vars['strModuleName']; ?>
: <?php if ($this->_tpl_vars['strcase_title'] != ""):  echo $this->_tpl_vars['strcase_title'];  else: ?>Add Combo<?php endif; ?></font></td>
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
	  <td height="2" colspan="2"></td>
	 </tr>
	<tr>
        <td colspan="2"><table width="100%" border="0" cellpadding="1" cellspacing="1" class="qlist">
		<tr>
			  <td width="21%" align="right"><?php echo $this->_tpl_vars['LBL_case_name']; ?>
</td>
			  <td align="left" colspan="3"><?php echo $this->_tpl_vars['strcase_name']; ?>
</td>
		</tr>	 
		<tr>
			  <td width="21%" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_query']; ?>
</td>
			  <td align="left" colspan="3"><?php echo $this->_tpl_vars['strquery']; ?>
<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_query']; ?>
','Taara_query',500,500);" class="help" alt="Zoom"></td>
		</tr>
		<tr>
			 <td width="21%" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_key_column']; ?>
</td>
			  <td align="left" colspan="3"><?php echo $this->_tpl_vars['strkey_column']; ?>
</td>
		</tr>
		<tr>
			  <td width="21%" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_value_column']; ?>
</td>
			  <td align="left" colspan="3"><?php echo $this->_tpl_vars['strvalue_column']; ?>
</td>
		</tr>
		<tr>
			  <td width="21%" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_combo_notes']; ?>
</td>
			  <td align="left" colspan="3"><?php echo $this->_tpl_vars['strcombo_notes']; ?>
</td>
		</tr>
       </table></td>
      </tr>
		 
	  <tr>
	  <td height="2" colspan="2">&nbsp;</td>
	 </tr>

<tr><td colspan="2">
<table width=100% id=list32 cellpadding=0 cellspacing=0 border=0 class=qlist>

<tr><td class=srch></td></tr>
<tr><td class=lst align="center">
	<table border=0 cellpadding=0 cellspacing=0 width="80%" align="center" id="tblGenerateRow">
	<tr>
      <td height="24" align="left" valign="middle">&nbsp;&nbsp;&nbsp;<font class="bold-text">Combo Detail</font></td>
	  <td align="right" colspan="3">
	   <font class="blue-text-bold-01">Number of Rows:</font>
	   <input type="text" name="txt_generate_rows" id="txt_generate_rows" maxlength="2" class="comn-input" size="5" value="<?php echo $this->_tpl_vars['intNewRows']; ?>
">
	   <input name="btn_submit" value="Go" class="btn" type="button" title="Go" alt="Go" onclick='return generateComboDetail();' />
	 </td> 
    </tr>

		<tr class="hdr">
			<td width=40% align="left">Key</td>
			<td width=40% align="left">Key Value</td>
			<td width=10% align="center">Seq. No.</td>
			<td width=10% align="center">Position</td>
		</tr>
		<?php unset($this->_sections['intTotCombo']);
$this->_sections['intTotCombo']['name'] = 'intTotCombo';
$this->_sections['intTotCombo']['loop'] = is_array($_loop=$this->_tpl_vars['intGenerateRows']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['intTotCombo']['show'] = true;
$this->_sections['intTotCombo']['max'] = $this->_sections['intTotCombo']['loop'];
$this->_sections['intTotCombo']['step'] = 1;
$this->_sections['intTotCombo']['start'] = $this->_sections['intTotCombo']['step'] > 0 ? 0 : $this->_sections['intTotCombo']['loop']-1;
if ($this->_sections['intTotCombo']['show']) {
    $this->_sections['intTotCombo']['total'] = $this->_sections['intTotCombo']['loop'];
    if ($this->_sections['intTotCombo']['total'] == 0)
        $this->_sections['intTotCombo']['show'] = false;
} else
    $this->_sections['intTotCombo']['total'] = 0;
if ($this->_sections['intTotCombo']['show']):

            for ($this->_sections['intTotCombo']['index'] = $this->_sections['intTotCombo']['start'], $this->_sections['intTotCombo']['iteration'] = 1;
                 $this->_sections['intTotCombo']['iteration'] <= $this->_sections['intTotCombo']['total'];
                 $this->_sections['intTotCombo']['index'] += $this->_sections['intTotCombo']['step'], $this->_sections['intTotCombo']['iteration']++):
$this->_sections['intTotCombo']['rownum'] = $this->_sections['intTotCombo']['iteration'];
$this->_sections['intTotCombo']['index_prev'] = $this->_sections['intTotCombo']['index'] - $this->_sections['intTotCombo']['step'];
$this->_sections['intTotCombo']['index_next'] = $this->_sections['intTotCombo']['index'] + $this->_sections['intTotCombo']['step'];
$this->_sections['intTotCombo']['first']      = ($this->_sections['intTotCombo']['iteration'] == 1);
$this->_sections['intTotCombo']['last']       = ($this->_sections['intTotCombo']['iteration'] == $this->_sections['intTotCombo']['total']);
?>
		<?php if (!(!(1 & $this->_sections['intTotCombo']['index']))): ?>
			<?php $this->assign('tdclass', 'data2'); ?>
		<?php else: ?>
			<?php $this->assign('tdclass', 'data1'); ?>
		<?php endif; ?>
		
		<tr class='<?php echo $this->_tpl_vars['tdclass']; ?>
' onmouseover='QL_MOver(this)' onmouseout='QL_MOut(this)' >
		<?php $this->assign('request_key', ((is_array($_tmp=((is_array($_tmp='Mltxt_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_sections['intTotCombo']['index']) : smarty_modifier_cat($_tmp, $this->_sections['intTotCombo']['index'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_key') : smarty_modifier_cat($_tmp, '_key'))); ?>
		<?php $this->assign('request_key_value', ((is_array($_tmp=((is_array($_tmp='Mltxt_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_sections['intTotCombo']['index']) : smarty_modifier_cat($_tmp, $this->_sections['intTotCombo']['index'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_key_value') : smarty_modifier_cat($_tmp, '_key_value'))); ?>
		<?php $this->assign('request_seq_no', ((is_array($_tmp=((is_array($_tmp='Mltxt_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_sections['intTotCombo']['index']) : smarty_modifier_cat($_tmp, $this->_sections['intTotCombo']['index'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_seq_no') : smarty_modifier_cat($_tmp, '_seq_no'))); ?>
		<?php $this->assign('request_ position', ((is_array($_tmp=((is_array($_tmp='Mltxt_')) ? $this->_run_mod_handler('cat', true, $_tmp, $this->_sections['intTotCombo']['index']) : smarty_modifier_cat($_tmp, $this->_sections['intTotCombo']['index'])))) ? $this->_run_mod_handler('cat', true, $_tmp, '_ position') : smarty_modifier_cat($_tmp, '_ position'))); ?>
		<?php if ($_REQUEST[$this->_tpl_vars['request_key']] == ''): ?>
		
			<td align="left">
				<input type="text" name="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_key" id="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_key" class="comn-input" size="30"  value="<?php echo $this->_tpl_vars['arrComboDetail'][$this->_sections['intTotCombo']['index']]['key']; ?>
">
			</td>
			<td align="left">
				<input type="text" name="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_key_value" id="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_key_value" class="comn-input" size="30" value="<?php echo $this->_tpl_vars['arrComboDetail'][$this->_sections['intTotCombo']['index']]['key_value']; ?>
" >				
			</td>
			<td align="center">
				<input type="text" name="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_seq_no" id="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_seq_no" maxlength="2" class="comn-input" size="5" value="<?php echo $this->_tpl_vars['arrComboDetail'][$this->_sections['intTotCombo']['index']]['seq_no']; ?>
" >
			</td>
			<td align="center">
				<input type="text" name="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_position" id="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_position" maxlength="1" class="comn-input" size="5" value="<?php echo $this->_tpl_vars['arrComboDetail'][$this->_sections['intTotCombo']['index']]['position']; ?>
" >
			</td>
		<?php else: ?>
			<td align="left">
				<input type="text" name="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_key" id="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_key" class="comn-input" size="30"  value="<?php echo $_REQUEST[$this->_tpl_vars['request_key']]; ?>
">
			</td>
			<td align="left">
				<input type="text" name="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_key_value" id="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_key_value" class="comn-input" size="30" value="<?php echo $_REQUEST[$this->_tpl_vars['request_key_value']]; ?>
" >				
			</td>
			<td align="center">
				<input type="text" name="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_seq_no" id="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_seq_no" maxlength="2" class="comn-input" size="5" value="<?php echo $_REQUEST[$this->_tpl_vars['request_seq_no']]; ?>
" >
			</td>
			<td align="center">
				<input type="text" name="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_position" id="Mltxt_<?php echo $this->_sections['intTotCombo']['index']; ?>
_position" maxlength="1" class="comn-input" size="5" value="<?php echo $_REQUEST[$this->_tpl_vars['request_position']]; ?>
" >
			</td>
			
		<?php endif; ?>	
		
		</tr>
		<?php endfor; endif; ?>		
		<tr>
			<td colspan="4"> 
				<div id="rowgenerate"></div>
			</td>
		</tr> 
</table></td></tr>


   <tr>
	<td colspan="2">
	<table width="100%" border="0" cellpadding="1" cellspacing="1" class="qlist">
		<tr>		   
			<td align="center" style="padding-left:20%">&nbsp;<input type="submit" name="submit" id="submit" value="Submit" class="btn" border="0" onclick='return submit_form(this.form);'/></td>
			<td align="right" width="20%" style="padding-right:10%"> <font class="red-text">* Required Seq.No.</font></td>			 
		</tr>		
		</table></td>
  </tr>	  
</table>
</td></tr>
</table>
</form>