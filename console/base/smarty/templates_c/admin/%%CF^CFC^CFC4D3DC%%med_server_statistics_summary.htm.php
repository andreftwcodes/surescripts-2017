<?php /* Smarty version 2.6.9, created on 2019-11-12 18:50:07
         compiled from ./middle/med_server_statistics_summary.htm */ ?>
<form name="frm_list_record" id="frm_list_record" method="post" action="index.php">
  <input type="hidden" name="file" id="file"  value="med_server_statistics_summary">
  <input type="hidden" name="hid_button_id" id="hid_button_id"  value="<?php echo $this->_tpl_vars['strButtonId']; ?>
">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="L">
  <input type="hidden" name="hid_max_row_limit" id="hid_max_row_limit" value="<?php echo $this->_tpl_vars['intShowMaxRows']; ?>
" />
  <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tab-bor">
    <tr>
      <td colspan="3" height="24" align="left" valign="middle"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="6" /> <font class="bold-text">Message Summary</font></td>
      <td width="5%" align="right"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="javascript:history.back();">Back</a></td>
    </tr>
    <tr>
      <td  height="1" class="blue-bg" colspan="4"></td>
    </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
    <tr>
      <td height="25" colspan="4"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
    </tr>
    <?php endif; ?>
    <tr>
      <td height="2" colspan="4"></td>
    </tr>
	<tr>
		<td height="2" colspan="4">
		<?php echo $this->_tpl_vars['strFilterTable']; ?>

		
		<?php echo $this->_tpl_vars['strServerStaticsTable']; ?>

			
		</td>
    </tr>
  </table>
</form>
<script type="text/javascript">
<?php echo '
function callDetailsSummary(intMeditabId, strMeditabDiv)
{
	var img_src	=	$("#img_"+intMeditabId).attr("src");
	
	if(img_src	==	"./images/nolines_plus.gif")
	{		
		if($("#"+strMeditabDiv).html()==\'\')
		{
			$("#img_"+intMeditabId).attr("src", "./images/loading.gif");
			$.ajax({ 
					url: "index.php?file=med_common_ajax&action=GET_SERVER_STATS_BY_MEDITAB_ID&meditab_id="+intMeditabId, 
					context: document.body, 
					success: function(data){
						$("#"+strMeditabDiv).html(data);
						$("#"+strMeditabDiv).show();
						$("#first_col_"+intMeditabId).show();
						$("#img_"+intMeditabId).attr("src", "./images/nolines_minus.gif");
				  }});
		}
		else
		{
			$("#"+strMeditabDiv).show();
			$("#first_col_"+intMeditabId).show();
			$("#img_"+intMeditabId).attr("src", "./images/nolines_minus.gif");
		}		
	}
	else
	{
		$("#"+strMeditabDiv).hide();
		$("#first_col_"+intMeditabId).hide();
		$("#img_"+intMeditabId).attr("src", "./images/nolines_plus.gif");
	}
}
'; ?>

</script>