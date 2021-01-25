<?php /* Smarty version 2.6.9, created on 2019-11-14 23:31:51
         compiled from ./middle/med_change_pass.htm */ ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file" value="med_change_pass">
  <input type="hidden" name="hid_page_type" id="hid_page_type" value="A">
  <input type="hidden" name="admin_id" id="admin_id" value="<?php echo $this->_tpl_vars['intAdminId']; ?>
">
  <input type="hidden" name="blnFromProfile" id="blnFromProfile" value="<?php echo $this->_tpl_vars['blnFromProfile']; ?>
" />
  <table border="0" cellpadding="0" cellspacing="0"  width="100%" class="tab-bor" align="center">
    <tr>
      <td height="24" align="left"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /> <font class="bold-text">Change Password</font> </td>
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
    <script src="./base/med_admin_master.js"></script>
    <?php echo $this->_tpl_vars['strJsVar']; ?>

    <tr>
      <td colspan="2"><table cellpadding="0" cellspacing="0" border="0" width="100%">
          <tr height="24">
            <td align="right"><font class="error-normal">*</font>New Password:</td>
            <td align="left">&nbsp;<?php echo $this->_tpl_vars['strField2']; ?>
</td>
          </tr>
          <tr height="24">
            <td align="right"><font class="error-normal">*</font>Confirm Password:</td>
            <td align="left">&nbsp;<?php echo $this->_tpl_vars['strField3']; ?>
</td>
          </tr>
          <tr>
            <td>&nbsp;</td>
            <td align="left">&nbsp;<?php echo $this->_tpl_vars['strField4']; ?>
</td>
          </tr>
          <tr>
            <td colspan="2" height="10"></td>
          </tr>
        </table></td>
    </tr>
  </table>
</form>