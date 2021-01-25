<?php /* Smarty version 2.6.9, created on 2019-11-12 21:26:18
         compiled from ./middle/med_pharmacy_mapping.htm */ ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title><?php echo $this->_tpl_vars['strSiteTitle']; ?>
</title>
<link href="./images/med_style.css" rel="stylesheet" type="text/css" />
<link href="./../base/jscalendar/skins/aqua/theme.css" rel="stylesheet" />
<script type="text/javascript" src="./../base/meditab/med_quicklist.js"></script>
<script type="text/javascript" src="./../base/meditab/med_common.js"></script>
<script type="text/javascript" src="./base/med_common.js"></script>
<script type="text/javascript" src="./base/med_shortcut.js"></script>
<script type="text/javascript" src="./base/med_common_ajax.js"></script>
<script type="text/javascript" src="./base/med_suggest.js"></script>
</head>
<body style="margin: 10px;">
<form name="frm_list_record" id="frm_list_record" method="post" action="index.php" enctype="multipart/form-data">
  <input type="hidden" name="strPageType" 	id="strPageType"  	value="<?php echo $this->_tpl_vars['strPageType']; ?>
" />
  <input type="hidden" name="file" 			id="file"  			value="<?php echo $this->_tpl_vars['strFile']; ?>
" />
  <input type="hidden" name="ncpdpid" 		id="ncpdpid"  		value="<?php echo $this->_tpl_vars['intNCPDPid']; ?>
" />  
  
  
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tab-bor">
     <tr>
      <td width="1%" align="left" valign="top">&nbsp;</td>
      <td width="98%" align="left" class="mos-middle-innerbg1" height="21"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <tr>
            <td width="93%" align="left" valign="middle"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /> <font class="bold-text">Meditab Id</font></td>
            <td width="7%" align="right" valign="middle"><a href="#" onclick="javascript:self.parent.tb_remove();" >Close</a></td>
          </tr>
        </table></td>
      <td width="1%" align="right" valign="top">&nbsp;</td>
    </tr>
    <tr>
	  <td height="1" class="blue-bg" colspan="3"></td>
	</tr>
	
    <tr>
      <td colspan="3" class="mos-middleborder"><table width="100%" border="0" cellspacing="0" cellpadding="0">
          <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
          <tr>
            <td height="24"  align="center" valign="middle" class="mos-middlebgsearch"><font class="error-normal"><?php echo $this->_tpl_vars['strMessage']; ?>
</font></td>
          </tr>
          <?php endif; ?>
          <tr>
            <td height="2"></td>
          </tr>
          <tr>
            <td align="left" valign="middle"><table width="100%" border="0" cellpadding="1" cellspacing="1">
                 <tr>
		            <td height="10"></td>
		         </tr>
				 
				<tr height="15">
                  <td align="right" valign="middle">Pharmacy Name:</td>
				   <td align="left" valign="middle"><?php echo $this->_tpl_vars['strPharmacyStoreName']; ?>
</td>
                </tr>
				<tr height="15">
                  <td align="right" valign="middle">NCPDP:</td>
				   <td align="left" valign="middle"><?php echo $this->_tpl_vars['intNCPDPid']; ?>
</td>
                </tr>
				<tr>
                  <td align="right" valign="middle">Meditab Id:</td>
				   <td align="left" valign="middle"><?php echo $this->_tpl_vars['strMeditabId']; ?>
</td>
                </tr>
				<tr>
                  <td align="right" valign="middle"></td>
				   <td align="left" valign="middle"><input type="submit" name="btn_save" id="btn_save" class="btn" value="Submit" onclick="return getSubmit();" /></td>
                </tr>
                <tr>
		            <td height="30">&nbsp;</td>
	            </tr>
              </table></td>
          </tr>
        </table></td>
    </tr>
    <tr>
      <td colspan="3" align="center" valign="top"  height="2"  class="mos-datatablebg-bot"></td>
    </tr>
  </table>
</form>
</body>
<?php echo '
<script>
function getSubmit()
{
	var intMeditabId	=	document.getElementById("TARtxt_meditab_id").value;
	if(intMeditabId == null || intMeditabId == \'\')
	{
		alert("Please enter Meditab Id");
		return false;		
	}
}
</script>

'; ?>