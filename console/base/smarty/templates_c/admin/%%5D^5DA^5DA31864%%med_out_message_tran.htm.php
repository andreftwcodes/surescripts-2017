<?php /* Smarty version 2.6.9, created on 2020-05-06 15:11:34
         compiled from ./middle/med_out_message_tran.htm */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>Employee Module of Meditab Online Support</title>
<link href="./images/med_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="./images/med_modal_message.css" type="text/css">
</head>
<?php echo '
<script type="text/javascript" src="./../base/meditab/med_quicklist.js"></script>
<script type="text/javascript" src="./../base/meditab/med_common.js"></script>
<script type="text/javascript" src="./base/med_common.js"></script>
<script type="text/javascript" src="./../base/jsoverlib/overlibmws.js"></script>
<script type="text/javascript" src="./../base/jsoverlib/overlibmws_iframe.js"></script>
'; ?>

<body style="margin:5px;">
<form name="frm_list_record" id="frm_list_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file"  value="med_out_message_tran">
  <input type="hidden" name="hid_button_id" id="hid_button_id"  value="<?php echo $this->_tpl_vars['strButtonId']; ?>
">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="L">
  <input type="hidden" name="tran_id" id="tran_id"  value="<?php echo $this->_tpl_vars['intTranId']; ?>
">
  <input type="hidden" name="hid_max_row_limit" id="hid_max_row_limit" value="<?php echo $this->_tpl_vars['intShowMaxRows']; ?>
" />
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tab-bor">
    <tr>
      <td height="24" align="left" valign="middle"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="6" /> <font class="bold-text"><?php echo $this->_tpl_vars['strTitle']; ?>
</font></td>
      <td width="6%" align="left"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="#" onclick="self.parent.tb_remove();">Close</a></td>
    </tr>
    <tr>
      <td height="1" class="blue-bg" colspan="2"></td>
    </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
    <tr>
      <td height="25" colspan="2"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
    </tr>
    <?php endif; ?>
    <tr>
      <td height="2" colspan="2"></td>
    </tr>
    <tr>
      <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
          <tr>
            <td align="center" colspan="2"><?php echo $this->_tpl_vars['strPage']; ?>
</td>
          </tr>  
        </table></td>
    </tr>
  </table>
</form>
</body>
</html>