<?php /* Smarty version 2.6.9, created on 2019-11-13 20:16:08
         compiled from ./middle/med_general_settings.htm */ ?>
<?php echo '
<script language="javascript">
function deleteSettings()
{
	var sltFlag = true;

	chkBoxArray = document.frm_settings.elements[\'chk_setting_id[]\'];
	for(var intSettings = 0; intSettings < chkBoxArray.length ; intSettings++)
	{	
		if(chkBoxArray[intSettings].checked == true)
		{
			sltFlag = false;
			break;
		}
	}		

	if(sltFlag)
	{
		alert("No Items were selected");
		return false;
	}	
	
	if(confirm("Are you sure to delete?"))
	{
		document.frm_settings.hid_table_id.value = 11;	
		document.frm_settings.hid_page_type.value = "D";	
		document.frm_settings.file.value=\'med_general_settings_action\';
		document.frm_settings.submit();
		return true;
	}				
}

function checkUncheckAll()
{
 	chkBoxArray = document.frm_settings.elements[\'chk_var_name[]\'];
	// To Check All CheckBox
	if(document.frm_settings.check_uncheck.checked == true)
		for(var intSettings = 0; intSettings < chkBoxArray.length ; intSettings++)
			chkBoxArray[intSettings].checked = true;
		
	// To Uncheck All CheckBox
	if(document.frm_settings.check_uncheck.checked == false)
		for(var intSettings = 0; intSettings < chkBoxArray.length ; intSettings++)
			chkBoxArray[intSettings].checked = false;
}
</script>
'; ?>

<form name="frm_settings" id="frm_settings" method="post" action="index.php" enctype="multipart/form-data">
  <input type="hidden" name="file" id="file" value="med_general_settings_action" />
  <input type="hidden" name="hid_table_id" id="hid_table_id" value="70" />
  <input type="hidden" name="hid_page_type" id="hid_page_type" value="" />
  <input type="hidden" id="hid_module_id" name="hidin_module_id" value="<?php echo $this->_tpl_vars['intModuleId']; ?>
"/>
  <input type="hidden" id="hid_sub_module_id" name="hid_sub_module_id" value="<?php echo $this->_tpl_vars['intSubModuleId']; ?>
"/>
  <input type="hidden" name="parent_id" id="parent_id" value="<?php echo $this->_tpl_vars['intParentId']; ?>
" />
  <input type="hidden" name="med_delete_url" id="med_delete_url" value="med_general_settings_action" />
  <table width="100%" border="0" cellspacing="0" cellpadding="0" class="tab-bor">
    <tr>
      <td align="left" height="24" colspan="4"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /> <font class="bold-text"><?php echo $this->_tpl_vars['strModuleName']; ?>
</font></td>
    </tr>
    <tr>
      <td height="1" class="blue-bg" colspan="4"></td>
    </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
    <tr>
      <td colspan="4" class="error-normal" height="25" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
    </tr>
    <?php endif; ?>
    </tr>
    
    <tr height="25" >
      <td align="left" colspan="4" >&nbsp;<?php echo $this->_tpl_vars['strAddButton']; ?>
&nbsp;<?php echo $this->_tpl_vars['strDeleteButton']; ?>
</td>
    </tr>
    <tr> </tr>
    <tr class="hdr">
      <td align="center" width="5%"><input id="check_uncheck" name="check_uncheck" onclick='checkUncheckAll()' type="checkbox">
      </td>
      <td align="left" width="25%">&nbsp;Name</td>
      <td align="right" width="30%">Description:&nbsp;</td>
      <td align="left" width="40%">&nbsp;Value</td>
    </tr>
    <tr>
      <td colspan="4"><?php echo $this->_tpl_vars['strGeneralSettings']; ?>
</td>
    </tr>
    <?php if ($this->_tpl_vars['intCount'] < 1): ?>
    <tr>
      <td align="center" colspan="4" height="25">No data found</td>
    </tr>
    <?php else: ?>
    <?php unset($this->_sections['intGeneralSettings']);
$this->_sections['intGeneralSettings']['name'] = 'intGeneralSettings';
$this->_sections['intGeneralSettings']['loop'] = is_array($_loop=$this->_tpl_vars['rsGeneralSettings']) ? count($_loop) : max(0, (int)$_loop); unset($_loop);
$this->_sections['intGeneralSettings']['show'] = true;
$this->_sections['intGeneralSettings']['max'] = $this->_sections['intGeneralSettings']['loop'];
$this->_sections['intGeneralSettings']['step'] = 1;
$this->_sections['intGeneralSettings']['start'] = $this->_sections['intGeneralSettings']['step'] > 0 ? 0 : $this->_sections['intGeneralSettings']['loop']-1;
if ($this->_sections['intGeneralSettings']['show']) {
    $this->_sections['intGeneralSettings']['total'] = $this->_sections['intGeneralSettings']['loop'];
    if ($this->_sections['intGeneralSettings']['total'] == 0)
        $this->_sections['intGeneralSettings']['show'] = false;
} else
    $this->_sections['intGeneralSettings']['total'] = 0;
if ($this->_sections['intGeneralSettings']['show']):

            for ($this->_sections['intGeneralSettings']['index'] = $this->_sections['intGeneralSettings']['start'], $this->_sections['intGeneralSettings']['iteration'] = 1;
                 $this->_sections['intGeneralSettings']['iteration'] <= $this->_sections['intGeneralSettings']['total'];
                 $this->_sections['intGeneralSettings']['index'] += $this->_sections['intGeneralSettings']['step'], $this->_sections['intGeneralSettings']['iteration']++):
$this->_sections['intGeneralSettings']['rownum'] = $this->_sections['intGeneralSettings']['iteration'];
$this->_sections['intGeneralSettings']['index_prev'] = $this->_sections['intGeneralSettings']['index'] - $this->_sections['intGeneralSettings']['step'];
$this->_sections['intGeneralSettings']['index_next'] = $this->_sections['intGeneralSettings']['index'] + $this->_sections['intGeneralSettings']['step'];
$this->_sections['intGeneralSettings']['first']      = ($this->_sections['intGeneralSettings']['iteration'] == 1);
$this->_sections['intGeneralSettings']['last']       = ($this->_sections['intGeneralSettings']['iteration'] == $this->_sections['intGeneralSettings']['total']);
?>			
    <?php $this->assign('arrHtmlControl', ""); ?>
    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'type',$this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_type']); ?>

    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'field_type',""); ?>

    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'field_name',$this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_name']); ?>

    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'class',"comn-input"); ?>

    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'isrequired','0'); ?>

    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'size',$this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_size']); ?>

    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'maxlength',$this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_maxlength']); ?>
		
    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'value',$this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_value']); ?>

    <?php echo $this->_tpl_vars['objPage']->addArray($this->_tpl_vars['arrHtmlControl'],'tbl_name',$this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_select']); ?>

    <?php if ($this->_tpl_vars['bgcolor'] == '#ffffff'): ?>
    <?php $this->assign('bgcolor', "#f4f6f7"); ?>
    <?php else: ?>
    <?php $this->assign('bgcolor', "#ffffff"); ?>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index_prev']]['field_group'] != $this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['field_group']): ?>
    <tr>
      <td height="1" class="blue-bg" colspan="4"></td>
    </tr>
    <tr>
      <td colspan="4"  align="left" height="24">&nbsp;&nbsp;<font class="bold-text"><?php echo $this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['field_group']; ?>
</font></td>
    </tr>
    <tr>
      <td height="1" class="blue-bg" colspan="4"></td>
    </tr>
    <?php endif; ?>
    <tr bgcolor="<?php echo $this->_tpl_vars['bgcolor']; ?>
">
      <td align="center" height="25" width="5%"><input type="checkbox" name="chk_setting_id[]" id="chk_setting_id[]" value="<?php echo $this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['setting_id']; ?>
" /></td>
      <td align="left" height="25" width="25%"><a href="index.php?file=med_general_settings_add&hid_table_id=11&hid_page_type=E&setting_id=<?php echo $this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['setting_id']; ?>
"><?php echo $this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_name']; ?>
</a>&nbsp;</td>
      <td align="right" height="25" width="30%"><?php echo $this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_desc']; ?>
:&nbsp;</td>
      <td align="left" height="25" width="40%">&nbsp;<?php echo $this->_tpl_vars['objPage']->generateHtmlControl($this->_tpl_vars['arrHtmlControl']);  if ($this->_tpl_vars['rsGeneralSettings'][$this->_sections['intGeneralSettings']['index']]['var_name'] == 'SITE_LOGO'): ?> <a href="#" onclick="popup('index.php?file=med_sitelogo',300,200);">[View]</a><?php endif; ?></td>
    </tr>
    <?php endfor; endif; ?>
    <?php endif; ?>
    <?php if ($this->_tpl_vars['intCount'] >= 1): ?>
    <tr>
      <td></td>
      <td></td>
      <td></td>
     <!-- <td height="35" align="left" ><input type="submit" name="change" id="change" value="Save" class="btn" border="0"/></td>-->
    </tr>
    <?php endif; ?>
  </table>
</form>
<?php echo '
<script language="javascript">
//Function called for General Settings > Field Group
function getFieldGroupControl()
{
	
}
</script>
'; ?>