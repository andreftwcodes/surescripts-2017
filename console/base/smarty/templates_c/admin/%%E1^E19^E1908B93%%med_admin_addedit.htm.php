<?php /* Smarty version 2.6.9, created on 2019-11-13 20:19:33
         compiled from ./middle/med_admin_addedit.htm */ ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
  <?php echo $this->_tpl_vars['strHidFile']; ?>

  <?php echo $this->_tpl_vars['strEXTRA_FIELD_VALIDATION']; ?>

  <?php echo $this->_tpl_vars['strHidUpdateID']; ?>

  <script src="./base/med_admin_master.js"></script>
  <table border="0" cellpadding="0" cellspacing="0"  width="100%" class="tab-bor" align="center">
    <tr>
      <td height="24" align="left"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /> <font class="bold-text"> <?php echo $this->_tpl_vars['strModuleName']; ?>
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
      <td colspan=2 height="5"  align="right">&nbsp;</td>
    </tr>
    <tr>
      <td colspan="2" width="100%" align="left"><table cellpadding="0" cellspacing="0" width="100%" border="0">
          <tr>
            <td width="25%" align="right" height="30"><?php echo $this->_tpl_vars['LBL_username']; ?>
</td>
            <td  align="left" height="30"><?php echo $this->_tpl_vars['strusername']; ?>
</td>
          </tr>
          <?php if ($this->_tpl_vars['strPageType'] == 'A'): ?>
          <tr>
            <td  align="right" height="30"><?php echo $this->_tpl_vars['LBL_password']; ?>
</td>
            <td  align="left" height="30"><?php echo $this->_tpl_vars['strpassword']; ?>
</td>
          </tr>
          <tr>
            <td  align="right" height="30"><?php echo $this->_tpl_vars['LBL_cpass']; ?>
</td>
            <td  align="left" height="30"><?php echo $this->_tpl_vars['strcpass']; ?>
</td>
          </tr>
          <?php endif; ?>
          <tr>
            <td  align="right" height="30"><?php echo $this->_tpl_vars['LBL_firstname']; ?>
</td>
            <td  align="left" height="30"><?php echo $this->_tpl_vars['strfirstname']; ?>
</td>
          </tr>
          <tr>
            <td  align="right" height="30"><?php echo $this->_tpl_vars['LBL_lastname']; ?>
</td>
            <td  align="left" height="30"><?php echo $this->_tpl_vars['strlastname']; ?>
</td>
          </tr>
          <tr>
            <td  align="right" height="30"><?php echo $this->_tpl_vars['LBL_email']; ?>
</td>
            <td  align="left" height="30"><?php echo $this->_tpl_vars['stremail']; ?>
</td>
          </tr>
          <tr>
            <td  align="right" height="30"><?php echo $this->_tpl_vars['LBL_is_meditab_admin']; ?>
</td>
            <td  align="left" height="30"><?php echo $this->_tpl_vars['stris_meditab_admin']; ?>
</td>
          </tr>
          <?php if ($this->_tpl_vars['strPageType'] == 'A'): ?>
          <tr>
            <td  align="right" height="30"><?php echo $this->_tpl_vars['LBL_status']; ?>
</td>
            <td  align="left" height="30"><?php echo $this->_tpl_vars['strstatus']; ?>
</td>
          </tr>
          <?php endif; ?>
          <tr>
            <td>&nbsp;</td>
            <td align="left"> <?php echo $this->_tpl_vars['strSumbitButton']; ?>
 </td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td colspan="2" height="10"></td>
    </tr>
  </table>
</form>
<?php echo '
<script language="javascript">
function showTimeZone()
{
	if(document.getElementById("slt_office_id").value != 0)
		document.getElementById("divTimeZone").style.display = "none";
	else
		document.getElementById("divTimeZone").style.display = "inline";
}

function extraValid()
{
	if(document.frm_add_record.TaRpas_password.value!=document.frm_add_record.Rpas_cpass.value)	
	{
			alert("Confirm password did not match");
			document.frm_add_record.TaRpas_password.focus();
			return false; //return false
	}
	else
	{
		return true;
	}
	
}
</script>
'; ?>
 