<?php /* Smarty version 2.6.9, created on 2019-11-12 18:46:23
         compiled from ./left/med_left_admin.htm */ ?>

<table width="100%" align="center" cellpadding="0" cellspacing="0" bgcolor="#3077aa" border="0">
  <tr>
  	  <td width="100%" height="26" align="left" valign="middle" bgcolor="#5794bf">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
		  <td align="center" valign="middle" width="24" ><img src="images/left-title-arrow.gif" align="absmiddle"/></td>
		  <td align="left">&nbsp;<font class="white-nav">Control Panel</font></td>
		</tr>
      </table>
	</td>
  </tr>
  <tr>
    <td height="8"></td>
  </tr>
  
  
  <tr>
    <td valign="top"><table width="90%" cellpadding="0" cellspacing="0" align="center">
    	<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_server_statistics_summary">Server Statistics</a></td>
        </tr>
		<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_in_message_transaction">In Message Transaction</a></td>
        </tr>
		<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_out_message_transaction">Out Message Transaction</a></td>
        </tr>
        <tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_out_message_history">Out Message History</a></td>
        </tr>
		
		 <tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_pharmacy_master">Pharmacy Directory</a></td>
        </tr>
		
         <tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_prescriber_master">Prescriber Directory</a></td>
        </tr>
		
		<?php if ($this->_tpl_vars['strServerType'] == 'PHARMACY_PARTNER'): ?>
		<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_pharmacy">Pharmacy Update Log</a></td>
        </tr>
		<?php elseif ($this->_tpl_vars['strServerType'] == 'PRESCRIBER_PARTNER'): ?>
		<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_prescribe">Prescriber Requests</a></td>
        </tr>
		<?php endif; ?>
		
        <tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_directory_download_log">Directory Download Log</a></td>
        </tr>
        
      </table></td>
  </tr>
  
  <tr>
    <td height="8"></td>
  </tr>
  
  <tr>
    <td background="images/mem-left-mid-line.gif" height="2"/><td width="0%"></td>
  </tr>
  
  <?php if ($this->_tpl_vars['isMeditabAdmin'] == 'Y'): ?>
  <tr>
  	  <td width="100%" height="26" align="left" valign="middle" bgcolor="#5794bf">
	  <table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
		<tr>
		  <td align="center" valign="middle" width="24" ><img src="images/left-title-arrow.gif" align="absmiddle"/></td>
		  <td align="left">&nbsp;<font class="white-nav">Admin Settings</font></td>
		</tr>
      </table>
	</td>
  </tr>
  
  <tr>
    <td height="8"></td>
  </tr>
  
  
  <tr>
    <td valign="top"><table width="90%" cellpadding="0" cellspacing="0" align="center">
		<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_list_record&hid_table_id=15&hid_page_type=L">Admin Master</a></td>
        </tr>
		
		<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_general_settings&hidin_module_id=0">General Settings</a></td>
        </tr>
		
		<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_combo_info&hid_page_type=L">Combo Settings</a></td>
        </tr>
		
		<tr height="20">
          <td width="15" align="left"><img src="images/orange-arrow.gif" hspace="2"  width="3" /></td>
          <td align="left" class="left-table-nav"><a href="index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0">Page Settings</a></td>
        </tr>
		
		
		
      </table></td>
  </tr>
  
  <tr>
    <td height="8"></td>
  </tr>
  <?php endif; ?>
</table>