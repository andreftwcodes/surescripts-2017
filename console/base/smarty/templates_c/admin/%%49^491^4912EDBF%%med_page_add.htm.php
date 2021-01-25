<?php /* Smarty version 2.6.9, created on 2019-11-15 20:27:12
         compiled from ./middle/med_page_add.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', './middle/med_page_add.htm', 51, false),)), $this); ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php">
  <?php echo $this->_tpl_vars['strHidUpdateID'];  echo $this->_tpl_vars['strHidFile']; ?>

  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
  <!--<input type="hidden" name="hid_col" id="hid_col" value='<?php echo $this->_tpl_vars['rsAlpMasterjson']; ?>
' />-->
  <input type="hidden" name="hid_seltable_id" id="hid_seltable_id" value="" />
 <!-- <input type="hidden" name="hid_seledit_key_col" id="hid_seledit_key_col" value="" />
   <input type="hidden" name="hid_seldelete_key_col" id="hid_seldelete_key_col" value="" />-->
  <?php echo $this->_tpl_vars['module']; ?>

   <?php echo $this->_tpl_vars['strseledit_key_col']; ?>

   <?php echo $this->_tpl_vars['strseldelete_key_col']; ?>

  <table border="0" cellpadding="0" cellspacing="0"  width="100%" align="center" class="tab-bor">
   <tr>
      <td width="41%" align="left" height="24"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /><span class="bold-text"><span onclick="gotoPageSetting();">Page Settings:</span>&nbsp;<?php echo $this->_tpl_vars['strModuleName']; ?>
<span></td>
	  <?php if ($this->_tpl_vars['strPageType'] == 'E'): ?>
      <td width="49%" align="right" height="24">
	  <a href="index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0"> All Tables</a>&nbsp;| <span class="gray-text"> View Table</span>&nbsp;|
	  <a href="index.php?file=med_page_multi_list&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Multi</a>&nbsp;|
      <a href="index.php?file=med_page_fields_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Fields</a>&nbsp;|
      <a href="index.php?file=med_page_buttons_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Buttons</a>&nbsp;|
	  <a href="index.php?file=med_page_search_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Search</a>	  </td>
	  <?php endif; ?>
	<td width="10%" align="right"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="javascript:history.back();">Back</a>&nbsp;</td>
  </tr>
   <tr>
		<td height="1" class="blue-bg" colspan="3"></td>
	</tr>
	 <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
	<tr>
		<td colspan="5" height="25"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
	</tr>
	<?php endif; ?>
  <tr>
  <td colspan="5" align="left">
  <table cellpadding="0" cellspacing="0" border="0" width="100%">
    <tr>
      <Td width="55%" valign="top"><table cellpadding="0" cellspacing="0" border="0" width="100%">
          <tr class="hdr">
            <td width="23%" height="25" align="right">Description:&nbsp;&nbsp;</td>
            <Td height="25" align="left">&nbsp;Value</Td>
          </tr>
		  <tr>
            <td width="20%" align="right" height="25" valign="middle"><?php echo $this->_tpl_vars['LBL_field_referal']; ?>
&nbsp;</td>
            <td align="left" height="25"><?php echo $this->_tpl_vars['strfield_referal']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_list_table_name']; ?>
','Taara_list_table_name',500,500);" class="help" alt="Zoom">			</td>
          </tr>

          <tr>
            <td width="20%" align="right" height="25">&nbsp;<?php echo $this->_tpl_vars['LBL_page_title']; ?>
&nbsp;</td>
			<?php $this->assign('name', ((is_array($_tmp=$this->_tpl_vars['LBL_page_title'])) ? $this->_run_mod_handler('replace', true, $_tmp, "<font color='#FF0000'>*</font>", "") : smarty_modifier_replace($_tmp, "<font color='#FF0000'>*</font>", ""))); ?>
            <td align="left" height="25"><?php echo $this->_tpl_vars['strpage_title']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['name']; ?>
','TaRtxt_page_title',500,500);" class="help" alt="Zoom">			</td>
          </tr>
		  	
          <tr>
            <td align="right" height="25">Add Fields of:</td>
            <td align="left" height="25"><?php echo $this->_tpl_vars['strAddFieldsOfTable']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=ADD_FIELD_TABLE&ControlFlag=1',
				'Add Fields of:','slt_add_field_table',500,500);" class="help" alt="Zoom"></td>
          </tr>
          <tr>
            <td width="20%" align="right" height="25" valign="top"><?php echo $this->_tpl_vars['LBL_list_table_name']; ?>
&nbsp;</td>
            <td align="left" height="25"><?php echo $this->_tpl_vars['strlist_table_name']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_list_table_name']; ?>
','Taara_list_table_name',500,500);" class="help" alt="Zoom">			</td>
          </tr>
          <tr>
            <td width="20%" align="right" height="25"  valign="top"><?php echo $this->_tpl_vars['LBL_addedit_table_name']; ?>
&nbsp;</td>
            <td align="left" height="25"><?php echo $this->_tpl_vars['straddedit_table_name']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_addedit_table_name']; ?>
','Taara_addedit_table_name',500,500);" class="help" alt="Zoom">			</td>
          </tr>
         <!-- <tr>
            <td width="20%" align="right" height="25" >&nbsp;Module :&nbsp;</td>
            <td align="left" height="25"><?php echo $this->_tpl_vars['module']; ?>
</td>
          </tr>-->
          <tr>
            <td width="20%" align="right" height="25" >Add Table Column:&nbsp;</td>
            <td align="left" height="25" > <?php echo $this->_tpl_vars['strAddTbl']; ?>
 
			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=1',
				'<?php echo $this->_tpl_vars['LBL_addedit_table_name']; ?>
','AddTbl',500,500);" class="help" alt="Zoom">			</td>
          </tr>
          <tr>
            <td width="20%" align="right" height="25" >Edit Table Column:&nbsp;</td>
            <td align="left" height="25"> <?php echo $this->_tpl_vars['strEditTbl']; ?>
&nbsp;:<span id="div_edit_key_col"><?php echo $this->_tpl_vars['stredit_key_col']; ?>
</span>
           <!-- <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=1',
				'<?php echo $this->_tpl_vars['LBL_addedit_table_name']; ?>
','EditKey',500,500);" class="help" alt="Zoom">-->			</td>
          </tr>
          <tr>
            <td width="20%" align="right" height="25" >Delete Table Column:&nbsp;</td>
            <td align="left" height="25" > <?php echo $this->_tpl_vars['strDeleteTbl']; ?>
&nbsp;:<span id="div_delete_key_col"><?php echo $this->_tpl_vars['strdelete_key_col']; ?>
</span>
          <!-- <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=1',
				'<?php echo $this->_tpl_vars['LBL_addedit_table_name']; ?>
','DeleteKey',500,500);" class="help" alt="Zoom">-->		    </td>
          </tr>
          <tr>
            <td width="20%" align="right" height="25"  ><?php echo $this->_tpl_vars['LBL_addedit_action_link']; ?>
&nbsp;</td>
            <td align="left" height="25" ><?php echo $this->_tpl_vars['straddedit_action_link']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
'<?php echo $this->_tpl_vars['LBL_addedit_action_link']; ?>
','Taara_addedit_action_link',500,500);" class="help" alt="Zoom">			</td>
          </tr>
          <tr>
            <td width="20%" align="right" height="25" >&nbsp;<?php echo $this->_tpl_vars['LBL_fixed_title']; ?>
&nbsp;</td>
            <td align="left" height="25"><?php echo $this->_tpl_vars['strfixed_title']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_fixed_title']; ?>
','Tatxt_fixed_title',500,500);" class="help" alt="Zoom">			</td>
          </tr>
		  <tr>
            <td width="20%" align="right" height="25" >&nbsp;<?php echo $this->_tpl_vars['LBL_table_desc']; ?>
&nbsp;</td>
            <td align="left" height="25"><?php echo $this->_tpl_vars['strtable_desc']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_table_desc']; ?>
','Taara_table_desc',500,500);" class="help" alt="Zoom">			</td>
          </tr>

        </table></Td>
      <td width="35%" valign="top" colspan="2"><table cellpadding="0" cellspacing="0" border="0" width="100%">
          <tr class="hdr">
            <td width="15%" height="25" align="right">Description:&nbsp;&nbsp;</td>
            <Td width="35%" height="25" align="left">&nbsp;Value</Td>
          </tr>
          <tr>
            <td colspan="2" height="2"></td>
          </tr>
          <tr>
            <td width="15%" align="right" height="25"  valign="top"><?php echo $this->_tpl_vars['LBL_where_clause']; ?>
&nbsp;</td>
            <td width="35%" align="left" height="25"><?php echo $this->_tpl_vars['strwhere_clause']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_where_clause']; ?>
','Taara_where_clause',500,500);" class="help" alt="Zoom">			
			</td>
          </tr>
          <tr>
            <td width="15%" align="right" height="25"  valign="top"><?php echo $this->_tpl_vars['LBL_order_clause']; ?>
&nbsp;</td>
            <td width="35%" align="left" height="25"><?php echo $this->_tpl_vars['strorder_clause']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_order_clause']; ?>
','Taara_order_clause',500,500);" class="help" alt="Zoom">
			</td>
          </tr>
          <tr>
            <td width="15%" align="right" height="25"  valign="top"><?php echo $this->_tpl_vars['LBL_group_clause']; ?>
&nbsp;</td>
            <td width="35%" align="left" height="25"><?php echo $this->_tpl_vars['strgroup_clause']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_group_clause']; ?>
','Taara_group_clause',500,500);" class="help" alt="Zoom">
			</td>
          </tr>
          <tr>
            <td width="15%" align="right" height="25"  valign="top"><?php echo $this->_tpl_vars['LBL_having_clause']; ?>
&nbsp;</td>
            <td width="35%" align="left" height="25"><?php echo $this->_tpl_vars['strhaving_clause']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_having_clause']; ?>
','Taara_having_clause',500,500);" class="help" alt="Zoom">
			</td>
          </tr>
		   <tr>
            <td width="15%" align="right" height="25"><?php echo $this->_tpl_vars['LBL_list_message']; ?>
&nbsp;</td>
            <td width="35%" align="left" height="25"><?php echo $this->_tpl_vars['strlist_message']; ?>

			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&ControlFlag=0',
				'<?php echo $this->_tpl_vars['LBL_list_message']; ?>
','Tatxt_list_message',500,500);" class="help" alt="Zoom">
			</td>
          </tr>
		  <tr>
		  	<td colspan="2" height="5"></td>
		 </tr>
		 
		  <?php if ($this->_tpl_vars['strPageType'] == 'E'): ?>
		    <tr>
			   <td align="left" height="25" colspan="2"><table width="95%" border="0" cellspacing="0" align="left" cellpadding="0" class="tab-bor">
			<tr>
            <td colspan="2" height="20">&nbsp;&nbsp;<font class="blue-text-bold-01">Search:</font></td>
          </tr>
		  <tr>
            <td colspan="2" height="1" class="blue-bg"></td>
          </tr>
				  <tr> 
					<td width="31%" align="right" height="25">Combo:&nbsp;</td>
					<td align="left" height="25"><?php echo $this->_tpl_vars['strCombo']; ?>
</td>
				  </tr>
				  <tr>
					<td align="right" height="25">By:&nbsp;</td>
					<td align="left" height="25"><select name="searchaplha"  style='width:120px;'><?php echo $this->_tpl_vars['strAlphaFields']; ?>
</select>
					</td>
				  </tr>
				   <tr>
					<td align="right" height="25">Paging:&nbsp;</td>
					<td align="left" height="25"><?php echo $this->_tpl_vars['strPaging']; ?>
</td>
				  </tr>
				 <?php if ($this->_tpl_vars['intPkVal'] != ""): ?>
				 <tr>
					<td align="right" height="25">Show Selector:&nbsp;</td>
					<td align="left" height="25"><?php echo $this->_tpl_vars['strSelector']; ?>
</td>
				  </tr>
				  <?php endif; ?>
				</table>
		    <tr>
		  <?php endif; ?>
        </table></Td>
    </tr>
	</table>
	</td>
	</tr>
	 <tr>
            <td colspan="2" height="2"></td>
     </tr>
    <?php if ($this->_tpl_vars['strPageType'] == 'E'): ?>
    <tr>
	  <td align="center" height="35" colspan="2">
         <input name="submit" type="submit" value="Submit"  class="btn" onclick='return submit_form(this.form);' />
     </td>
	 </tr>
	 <?php else: ?>
	 <tr>
	 <td align="center" height="35" colspan="2">
	 <input name="next" type="submit" value="Next" class="btn" onclick='return submit_form(this.form);' />
     </td>
	 </tr>
	  <?php endif; ?>
    
  </table>
</form>
<?php echo '
<script language="javascript">

//This Function is NOT USED now after removing json.
function editCol(Tbl,Key)
{
	var tables= eval("document.frm_add_record."+Tbl);
	var fields = eval("document.frm_add_record."+Key);
	var arrCol = Array();
	arrCol = eval(document.getElementById(\'hid_col\').value);
	
	var box1 = tables;
	var number = box1.options[box1.selectedIndex].value;
	//if (!number) return;
	
	for (intCol = fields.options.length; intCol >= 0; intCol--)
	{
			fields.options[intCol] = null;
	}
	for(var intCol=0;intCol<arrCol.length;intCol++)
	{
		
		if(number == arrCol[intCol][\'table_name\'])
		{
			intIndex = fields.options.length;
			fields.options[intIndex] = new Option(arrCol[intCol][\'column_name\']);
			fields.options[intIndex].value = arrCol[intCol][\'column_name\'];
	
			if(arrCol[intCol].edit_key_col != null)
			{	
				var selValue = arrCol[intCol].edit_key_col.split(":");	
				var selValues = selValue[1];
			}			
			if(arrCol[intCol][\'column_name\'] == selValues)
			{
				fields.options[intIndex].selected=true;
			}
		}
	}
	
}
function getChangeEditColumn()
{
	var table = document.frm_add_record;
	
	if(table.EditTbl.value == "")
	{
		document.getElementById("div_edit_key_col").style.visibility="hidden";
	}
	else
	{
		
		arrCol = document.getElementById(\'hid_seledit_key_col\').value;
		var selValue = arrCol.split(":");
		document.getElementById(\'hid_seledit_key_col\').value = selValue[1];
		/*if(selValue[0] == "accounts")
			document.getElementById(\'slt_edit_key_col\').value = selValue[1];
		else if(selValue[0] == "mot_client_master")	
			document.getElementById(\'slt_edit_key_col\').value = selValue[1];*/
		getHTMLControl(\'edit_key_col\',\'F:14\',\'EditTbl=EditTbl\');
		document.getElementById("div_edit_key_col").style.visibility="visible";
	}
}
getChangeEditColumn();
function getChangeDeleteColumn()
{
	var table = document.frm_add_record;
	if(table.DeleteTbl.value == "")
	{
		document.getElementById("div_delete_key_col").style.visibility="hidden";
	}
	else
	{
		arrDeleteCol = document.getElementById(\'hid_seldelete_key_col\').value;
		var selValue = arrDeleteCol.split(":");
		document.getElementById(\'hid_seldelete_key_col\').value = selValue[1];
		/*if(selValue[0] == "accounts")
			document.getElementById(\'slt_delete_key_col\').value = selValue[1];
		else if(selValue[0] == "mot_client_master")	
			document.getElementById(\'slt_delete_key_col\').value = selValue[1];*/
		getHTMLControl(\'delete_key_col\',\'F:15\',\'DeleteTbl=DeleteTbl\');
		document.getElementById("div_delete_key_col").style.visibility="visible";
	}
}
getChangeDeleteColumn();

//This Function is NOT USED now after removing json.
function deleteCol(Tbl,Key)
{
	var tables= eval("document.frm_add_record."+Tbl);
	var fields = eval("document.frm_add_record."+Key);
	var arrCol = Array();
	arrCol = eval(document.getElementById(\'hid_col\').value);
	
	var box1 = tables;
	var number = box1.options[box1.selectedIndex].value;
	//if (!number) return;
	
	for (intCol = fields.options.length; intCol >= 0; intCol--)
	{
			fields.options[intCol] = null;
	}
	for(var intCol=0;intCol<arrCol.length;intCol++)
	{
			
		if(number == arrCol[intCol][\'table_name\'])
		{
			intIndex = fields.options.length;
			fields.options[intIndex] = new Option(arrCol[intCol][\'column_name\']);
			fields.options[intIndex].value = arrCol[intCol][\'column_name\'];		
			
			if(arrCol[intCol].delete_key_col != null)
			{	
				var selValue = arrCol[intCol].delete_key_col.split(":");	
				var selValues = selValue[1];
			}
			if(arrCol[intCol][\'column_name\'] == selValues)
			{
				fields.options[intIndex].selected=true;
			}
		}
	
	}
	
}


//deleteCol(\'DeleteTbl\',\'DeleteKey\');
//editCol(\'EditTbl\',\'EditKey\');

</script>
'; ?>