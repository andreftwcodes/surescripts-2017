<?php /* Smarty version 2.6.9, created on 2020-06-12 07:06:46
         compiled from ./middle/med_login.htm */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Welcome To Admin Panel of Meditab Employee Management</title>
<link href="images/med_style.css" rel="stylesheet" type="text/css" />
<?php echo '
<script type="text/javascript" src="./../base/meditab/med_common.js"></script>
<script type="text/javascript" src="./base/med_common.js"></script>
'; ?>

</head>
<body class="loginpage-bg">
<table cellpadding="0" cellspacing="0" border="0" align="center">
 <form name="frm_add_record" id="frm_add_record" method="post" action="index.php" onsubmit="return submit_form(this);">
  <input type="hidden" name="file" id="file" value="med_login">
<tr>
<td align="left" valign="bottom"><img src="images/left-bg.gif" width="177" height="241"/></td>
<td align="left" valign="top">
<table width="100%" border="0" cellspacing="0" cellpadding="0">
 <tr>
 <Td width="229"><img src="images/adminlogin-top.gif" width="229" height="33" alt="Administrator Login" title="Administrator Login" /></Td>
 </tr>
  <tr>
    <td width="229" align="left" valign="top" class="loginpage-mid-top-bg" height="47"></td>
  </tr>
  <tr>
    <td width="229" align="left" valign="top" class="loginpage-mid-bg" height="102">
	
	  <table width="100%" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td colspan="2" align="center" valign="top"><font class="error-normal"><?php echo $this->_tpl_vars['objPage']->objGeneral->getMessage(); ?>
</font></td>
        </tr>
		<tr>
          <td colspan="2" height="2"></td>
        </tr>
        <tr>
          <td align="right"><font class="gray-text">Username:</font>&nbsp;</td>
          <td align="left"><?php echo $this->_tpl_vars['strUsernameTextBox']; ?>
</td>
        </tr>
        <tr>
          <td colspan="2" height="5"></td>
        </tr>
        <tr>
          <td align="right"><font class="gray-text">Password:</font>&nbsp;</td>
          <td align="left"><?php echo $this->_tpl_vars['strPasswordTextBox']; ?>
</td>
        </tr>
		<tr>
          <td colspan="2" height="5"></td>
        </tr>
		<?php if ($this->_tpl_vars['strRadioOption'] == 'var'): ?>
			<?php $this->assign('strVARChecked', 'checked'); ?>
		<?php else: ?>
			<?php $this->assign('strEmpChecked', 'checked'); ?>
		<?php endif; ?>
		<tr>
          <td colspan="2" height="10" align="center" class="gray-text"><label style="vertical-align:middle;"><strong>I am a</strong></label><input type="radio" name="rad_user_type" id="rad_emp" class="comn-input" style="vertical-align:top;" <?php echo $this->_tpl_vars['strEmpChecked']; ?>
 value="employee" /> <label for="rad_emp" style="cursor:pointer; vertical-align:middle"><strong>Employee</strong></label><input type="radio" name="rad_user_type" id="rad_var" class="comn-input" style="vertical-align:top;" value="var" <?php echo $this->_tpl_vars['strVARChecked']; ?>
 /> <label for="rad_var" style="cursor:pointer; vertical-align:middle"><strong>VAR</strong></label>
		  </td>
        </tr>
		<tr>
          <td colspan="2" height="5"></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td align="left"><input type="image" src="images/bt-login.gif" width="60" height="20" border="0" alt="Login" title="Login"/></td>
        </tr>
      </table>
	</td>
  </tr>
  <tr>
    <td width="229" align="left" valign="top" class="loginpage-mid-bot-bg" height="75"></td>
  </tr>
</table></td>
<td align="left" valign="top"><img src="images/right-bg.gif" width="23" height="257" /></td>
</tr>
<tr>
  <td align="left" valign="top" colspan="3" height="10"></td>
 </tr>
<tr>
  <td colspan="3" align="center" valign="top">
  <table width="70%" border="0" cellspacing="0" cellpadding="0">
  
  <tr>
    <td align="right"><font class="white-text">Powered By</font></td>
    <td align="center" width="88"><img src="images/suiterx-logo.png" width="90" height="auto" hspace="4" alt="SuiteRx" title="SuiteRx" /></td>
    <td align="left"><font class="white-text">&copy;2006 SuiteRx, LLC</font></td>
  </tr>
</table></td>
  </tr>
<tr>
  <td align="left" valign="top" colspan="3"></td>
 </tr>
  </form>
</table>

</body>
</html>
<?php echo '
<script >
document.frm_add_record.TaRtxt_username.focus();
</script>
'; ?>