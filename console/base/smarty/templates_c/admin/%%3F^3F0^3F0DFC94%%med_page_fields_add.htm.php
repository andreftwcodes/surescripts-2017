<?php /* Smarty version 2.6.9, created on 2019-11-15 20:27:29
         compiled from ./middle/med_page_fields_add.htm */ ?>
<?php require_once(SMARTY_CORE_DIR . 'core.load_plugins.php');
smarty_core_load_plugins(array('plugins' => array(array('modifier', 'replace', './middle/med_page_fields_add.htm', 55, false),)), $this); ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php">
<input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
<input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
<input type="hidden" name="hidin_table_id" id="hidin_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
<input type="hidden" name="parent_id" id="parent_id" value="<?php echo $this->_tpl_vars['intParentId']; ?>
" />
<input type="hidden" name="hid_show_in" id="hid_show_in" value="<?php echo $this->_tpl_vars['strShowIn']; ?>
" />
<?php echo $this->_tpl_vars['strHidUpdateID'];  echo $this->_tpl_vars['strHidFile']; ?>

<table border="0" cellpadding="0" cellspacing="0"  width="100%" class="tab-bor" align="center">
  <tr>
    <td width="45%" align="left"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /><span class="bold-text"><span onclick="gotoPageSetting();">Page Settings:</span>&nbsp;<?php echo $this->_tpl_vars['strModuleName']; ?>
<span></td>
    <td width="49%" align="right" height="24"><a href="index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0"> All Tables</a>&nbsp;| <a href="index.php?file=med_page_add&hidin_module_id=0&table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hid_page_type=E">View Table</a>&nbsp;|
	  <a href="index.php?file=med_page_multi_list&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Multi</a>&nbsp;|
      <a href="index.php?file=med_page_fields_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Fields</a>&nbsp;|
      <a href="index.php?file=med_page_buttons_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Buttons</a>&nbsp;|
	  <a href="index.php?file=med_page_search_listings&hid_page_type=L&hid_table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
&parent_id=<?php echo $this->_tpl_vars['intParentId']; ?>
&hidin_module_id=0&strLeftId=<?php echo $this->_tpl_vars['strLeftId']; ?>
">View Search</a>	  </td>
      <td width="6%" align="right"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="javascript:history.back();">Back</a>&nbsp;</td>
  </tr>
	 <tr>
		<td height="1" class="blue-bg" colspan="6"></td>
	  </tr>
	 <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
	<tr>
		<td colspan="6" height="25"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
	</tr>
	<?php endif; ?>
	<tr>
	  <td colspan="6" align="left"><table width="96%" border="0" cellspacing="0" cellpadding="0" align="center">

  <tr>
  	<td colspan="4" height="8"></td>
</tr>
<!--tr>
<td align="left" valign="top"><fieldset><legend>Fields List</legend>
<table width="100%" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td><?php echo $this->_tpl_vars['strPageList']; ?>
</td>
  </tr>
</table>
</fieldset>
</td>
</tr-->
 <tr>
    <td align="right"><?php if ($this->_tpl_vars['strPageType'] != 'A'): ?><table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center">Fields:</td>
        <td align="center"><?php echo $this->_tpl_vars['strFieldCombo']; ?>
</td>
      </tr>
    </table><?php endif; ?></td>
 </tr>
  <tr>
  	<td colspan="4" align="center"><fieldset><legend>Common:</legend>
		<table width="100%" cellpadding="0" cellspacing="0" border="0" align="center">
		<tr>
				<td align="right" valign="middle" height="24" ><?php echo $this->_tpl_vars['LBL_field_referal']; ?>
</td>
				<?php $this->assign('referal', ((is_array($_tmp=$this->_tpl_vars['LBL_field_referal'])) ? $this->_run_mod_handler('replace', true, $_tmp, "<font color='#FF0000'>*</font>", "") : smarty_modifier_replace($_tmp, "<font color='#FF0000'>*</font>", ""))); ?>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strfield_referal']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&code=REFERAL&ControlFlag=0','<?php echo $this->_tpl_vars['referal']; ?>
','TaRtxt_field_referal',500,500);" class="help" alt="Zoom"></td>
  </tr>
			<tr>
				<td align="right" width="19%" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_field_name']; ?>
</td>
				<?php $this->assign('name', ((is_array($_tmp=$this->_tpl_vars['LBL_field_name'])) ? $this->_run_mod_handler('replace', true, $_tmp, "<font color='#FF0000'>*</font>", "") : smarty_modifier_replace($_tmp, "<font color='#FF0000'>*</font>", ""))); ?>
				<td width="31%" height="25" align="left" valign="middle">&nbsp;<?php echo $this->_tpl_vars['strfield_name']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=FILED_NAME&ControlFlag=0','<?php echo $this->_tpl_vars['name']; ?>
','TaRtxt_field_name',500,500);" class="help" alt="Zoom">				</td>
				<td align="right" width="20%" valign="middle" height="21"><?php echo $this->_tpl_vars['LBL_field_type']; ?>
</td>
				<td align="left" valign="middle" height="21" width="30%">&nbsp;<?php echo $this->_tpl_vars['strfield_type']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=FIELD_TYPE&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_field_type']; ?>
','Taslt_field_type',500,500);" class="help" alt="Zoom">				</td>
			</tr>
				<tr>
				<td align="right" valign="middle"><?php echo $this->_tpl_vars['LBL_sql_field']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strsql_field']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=ALIAS_NAME&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_sql_field']; ?>
','Tatxt_sql_field',500,500);" class="help" alt="Zoom">				</td>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_field_title']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strfield_title']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=FIELD_TITLE&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_field_title']; ?>
','Tatxt_field_title',500,500);" class="help" alt="Zoom">				</td>
			</tr>
				<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_seq_no']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strseq_no']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=SEQUENCE_NUMBER&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_seq_no']; ?>
','Tatxt_seq_no',500,500);" class="help" alt="Zoom"></td>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_ishidden']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strishidden']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=HIDDEN&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_ishidden']; ?>
','Taslt_ishidden',500,500);" class="help" alt="Zoom">				</td>
			</tr>
					<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_show_in']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['showcombo']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=SHOW_IN&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_show_in']; ?>
','show_in[]',500,500);" class="help" alt="Zoom">				</td>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_field_desc']; ?>
</td>
				<td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strfield_desc']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=FIELD_DESC&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_field_desc']; ?>
','Taara_field_desc',500,500);" class="help" alt="Zoom">				</td>
			</tr>
		</table>
		</fieldset>		</td>
	</tr>
	<tr>
		<td colspan="4" height="8"></td>
	</tr>
		<tr>
			<td align="right" valign="middle" height="24" colspan="4"><table width="100%" border="0" cellspacing="0" cellpadding="0" align="center">
  <tr>
    <td width="50%" align="left" valign="top"><fieldset><legend>List Data:</legend><table width="100%" border="0" cellspacing="0" cellpadding="0">
      	<tr>
				<td align="right" width="40%" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_list_field_html_type']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strlist_field_html_type']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=LIST_FIELD_HTML_TYPE&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_list_field_html_type']; ?>
','Taslt_list_field_html_type',500,500);" class="help" alt="Zoom">				</td>
			</tr>
				<tr>
				<td align="right" width="40%" valign="middle"><?php echo $this->_tpl_vars['LBL_list_html_text']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strlist_html_text']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=LIST_HTML_TEXT&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_list_html_text']; ?>
','Tatxt_list_html_text',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
			<td align="right"  valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_html_link']; ?>
</td>
			<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strhtml_link']; ?>
 
			<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=HTML_LINK&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_html_link']; ?>
','Tatxt_html_link',500,500);" class="help" alt="Zoom">			</td>
			</tr>
			<tr>
				<td align="right"  valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_list_event']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strlist_event']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=LIST_EVENT&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_list_event']; ?>
','Tatxt_list_event',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
				<td align="right"  valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_list_extra_property']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strlist_extra_property']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=LIST_EXTRA_PROPERTY&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_list_extra_property']; ?>
','Tatxt_list_extra_property',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_header_width']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strheader_width']; ?>
&nbsp;% 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=HEADER_WIDTH&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_header_width']; ?>
','Tatxt_header_width',500,500);" class="help" alt="Zoom"></td>
			</tr>	
			<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_header_align']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strheader_align']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=HEADER_ALIGN&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_header_align']; ?>
','Taslt_header_align',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			
	     	<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_body_align']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strbody_align']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=BODY_ALIGN&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_body_align']; ?>
','Taslt_body_align',500,500);" class="help" alt="Zoom">				</td>
			</tr>
				<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_issort']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strissort']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=SORTING&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_issort']; ?>
','Taslt_issort',500,500);" class="help" alt="Zoom">				</td>
			</tr>
				
				<tr>
                  <td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_search_field_type']; ?>
</td>
				  <td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strsearch_field_type']; ?>
</td>
				  </tr>
                <tr>
                  <td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_search_html_text']; ?>
&nbsp;</td>
                  <td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strsearch_html_text']; ?>
</td>
                </tr>
                <tr>
                  <td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_search_is_editable']; ?>
&nbsp;</td>
                  <td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strsearch_is_editable']; ?>
</td>
                </tr>

</table>
</fieldset></td>
<td width="10"></td>
    <td align="left" valign="top"><fieldset>
      <legend>AddEdit Data:</legend>
      <table width="100%" border="0" cellspacing="0" cellpadding="0">
	       	<tr>
				<td align="right" width="49%" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_addedit_field_html_type']; ?>
</td>
				<td width="51%" height="24" align="left" valign="middle">&nbsp;<?php echo $this->_tpl_vars['addedit_field_html_type']; ?>
:<?php echo $this->_tpl_vars['addedit_field_html_type_extra']; ?>

				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=ADDEDIT_FIELD_HTML_TYPE&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_addedit_field_html_type']; ?>
','add_field_type',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
				<td align="right"  valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_add_field_type']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['stradd_field_type']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=ADD_FIELD_TYPE&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_add_field_type']; ?>
','Taslt_add_field_type',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
				<td align="right" width="49%" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_add_html_text']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['stradd_html_text']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=ADDEDIT_HTML_TEXT&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_add_html_text']; ?>
','Tatxt_add_html_text',500,500);" class="help" alt="Zoom">				</td>
			</tr>
				<tr>
				<td align="right"  valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_field_length']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strfield_length']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=FIELD_LENGTH&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_field_length']; ?>
','Tatxt_field_length',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
				<td align="right"  valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_add_field_length_show']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['stradd_field_length_show']; ?>
 			
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=ADD_FIELD_LENGTH_SHOW&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_add_field_length_show']; ?>
','Tatxt_add_field_length_show',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_addedit_event']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['straddedit_event']; ?>
 
<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=ADDEDIT_EVENT&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_addedit_event']; ?>
','Tatxt_addedit_event',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_add_extra_property']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['stradd_extra_property']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=ADD_EXTRA_PROPERTY&ControlFlag=0','<?php echo $this->_tpl_vars['LBL_add_extra_property']; ?>
','Tatxt_add_extra_property',500,500);" class="help" alt="Zoom">				</td>
			</tr>
			<tr>
				<td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_isrequired']; ?>
</td>
				<td align="left" valign="middle" height="24">&nbsp;<?php echo $this->_tpl_vars['strisrequired']; ?>
 
				<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&code=ISREQUIRED&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_isrequired']; ?>
','Taslt_isrequired',500,500);" class="help" alt="Zoom">				</td>
			</tr>
      </table>
    </fieldset></td>
  </tr>
</table></td>
	   </tr>
  
</table></td>
	</tr>
  <tr><td colspan="2" height="4"></td></tr>
  <tr>
	<td align="center" colspan="2"><input type='submit' name='smt_submit' value='Submit' onclick='return submit_form(this.form);' class='btn' ></td>
</tr>
<tr><td colspan="2" height="4" ></td></tr>
</table>
 </form>

 
 		</td>
		</tr>
		</table></td>
      </tr>
      </table></td>
  </tr>

  </tr> 
</table>
<?php echo '
<script language="javascript">
showInSelection();

//If none of show-in is selected then dont show first record : \'List\' as selected.
function showInSelection()
{
	strShowValue = document.getElementById("hid_show_in").value;

	if(strShowValue == "")
		document.getElementById("show_in[]").options[0].selected = false;
	
	
	//By Default Alignment should be Left
	if(document.getElementById("Taslt_header_align").value == "Default")
		document.getElementById("Taslt_header_align").value = "Left";
		
	if(document.getElementById("Taslt_body_align").value == "Default")
		document.getElementById("Taslt_body_align").value = "Left";

}
</script>
'; ?>
