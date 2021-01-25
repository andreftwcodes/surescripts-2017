<?php /* Smarty version 2.6.9, created on 2019-11-15 00:12:35
         compiled from ./middle/med_general_settings_add.htm */ ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php" enctype="multipart/form-data">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
  <input type="hidden" name="file" id="file" value="med_general_settings_add" />
  <?php echo $this->_tpl_vars['strEXTRA_FIELD_VALIDATION']; ?>

  <?php echo $this->_tpl_vars['strHidUpdateID']; ?>

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
      <td colspan="2" height="10"></td>
    </tr>
    <tr>
      <td colspan="2" align="center" height="30"><!----------- TABLE START --------------->
        <table cellspacing="2" cellpadding="2" align="left" style="padding-left:70px">
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_var_name']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strvar_name']; ?>
 </td>
          </tr>
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_var_desc']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strvar_desc']; ?>
 </td>
          </tr>
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_var_value']; ?>
 </td>
            <td align='left' valign='top'><?php echo $this->_tpl_vars['strvar_value']; ?>
</td>
          </tr>
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_var_type']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strvar_type']; ?>
 </td>
          </tr>
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_var_select']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strvar_select']; ?>
 </td>
          </tr>
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_var_size']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strvar_size']; ?>
 </td>
          </tr>
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_var_maxlength']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strvar_maxlength']; ?>
 </td>
          </tr>
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_seq_no']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strseq_no']; ?>
 </td>
          </tr>
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_field_group']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strfield_group']; ?>
&nbsp;&nbsp;
              <div id="div_other_field_group" style="display:none;">
                <input name="Tatxt_other_field_group" id="Tatxt_other_field_group" value="" class="comn-input" type="text">
              </div></td>
          </tr>
          <!--<tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_field_order']; ?>
 </td>
            <td align='left' valign='top'><div id="div_field_order" name="div_field_order"> <?php echo $this->_tpl_vars['strfield_order']; ?>
 </div></td>
          </tr>-->
          <tr>
            <td align='right' width='20%' height='24' valign='top'> <?php echo $this->_tpl_vars['LBL_status']; ?>
 </td>
            <td align='left' valign='top'> <?php echo $this->_tpl_vars['strstatus']; ?>
 </td>
          </tr>
          <tr>
            <td></td>
            <td align='left'><input type='button' name='smt_submit' value='Submit' onclick='return submit_form(this.form);' class='btn'>
            </td>
          </tr>
        </table>
        <!----------- TABLE START --------------->
      </td>
    </tr>
    <tr>
      <td colspan="2" height="10"></td>
    </tr>
  </table>
</form>
<?php echo '
<script language="javascript">
getFieldGroupControl();

if(getElement(\'hid_page_type\').value == \'E\')
{
	getElement(\'Tatxt_var_value\').setAttribute(\'maxlength\',getElement(\'Intxt_var_maxlength\').value);
	getElement(\'Tatxt_var_value\').setAttribute(\'size\',getElement(\'Intxt_var_size\').value);
}

function getFieldGroupControl()
{

	if(document.getElementById("Rslt_field_group").value == -1)
		document.getElementById("div_other_field_group").style.display="inline";
	else	
		document.getElementById("div_other_field_group").style.display="none";
	if(document.getElementById("Rslt_field_group").value != -1 && document.getElementById("Rslt_field_group").value != "")
	{
	getPageData(\'index.php?file=med_general_settings_get_field_order&field_group=\'+document.getElementById("Rslt_field_group").value,\'div_field_order\');

	}
}
/*
	This function called for Action 
*/
function extraValid()
{
	strFrm			= document.frm_add_record;
	alertMsg 		= "";

		if(document.getElementById("Rslt_field_group").value == -1)
		{
			if(Trim(strFrm.Tatxt_other_field_group.value) == "")
			{
				alertMsg += "\\nPlease enter value for Field Group.";
				strFrm.Tatxt_other_field_group.value	= "";
				setStyle(strFrm.Tatxt_other_field_group);
			}
			else
			{
				resetStyle(strFrm.Tatxt_other_field_group);
			}	
		}

		if(alertMsg != "")
		{
			alertMsg = "Following errors:\\n" + alertMsg;	
			alert(alertMsg);	
			eval(strStyle);
			eval(strFocus);
			return false
		}
		//Get the action to be performed
		strFrm.file.value = "med_general_settings_action";
		strFrm.submit();
		return true;
}
</script>
'; ?>