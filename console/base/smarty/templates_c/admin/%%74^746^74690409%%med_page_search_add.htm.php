<?php /* Smarty version 2.6.9, created on 2019-11-15 20:27:34
         compiled from ./middle/med_page_search_add.htm */ ?>
<form name="frm_add_record" id="frm_add_record" method="post" action="index.php">
  <input type="hidden" name="hid_table_id" id="hid_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="hid_page_type" id="hid_page_type"  value="<?php echo $this->_tpl_vars['strPageType']; ?>
">
  <input type="hidden" name="hidin_table_id" id="hidin_table_id"  value="<?php echo $this->_tpl_vars['intTableId']; ?>
">
  <input type="hidden" name="strLeftId" id="strLeftId" value="<?php echo $this->_tpl_vars['strLeftId']; ?>
" />
  <input type="hidden" name="parent_id" id="parent_id" value="<?php echo $this->_tpl_vars['intParentId']; ?>
" />
  <?php echo $this->_tpl_vars['strHidUpdateID'];  echo $this->_tpl_vars['strHidFile']; ?>

  <table border="0" cellpadding="0" cellspacing="0" width="100%" class="tab-bor" align="center">
    <tr>
      <td width="45%" align="left" height="24"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="4" /><span class="bold-text"><span onclick="gotoPageSetting();">Page Settings:</span>&nbsp;<?php echo $this->_tpl_vars['strModuleName']; ?>
<span></td>
      <td width="48%" align="right" height="24"><a href="index.php?file=med_page_listings&hid_page_type=L&hidin_module_id=0"> All Tables</a>&nbsp;| <a href="index.php?file=med_page_add&hidin_module_id=0&table_id=<?php echo $this->_tpl_vars['intTableId']; ?>
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
      <td width="7%" align="right"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="javascript:history.back();">Back</a>&nbsp;</td>
    </tr>
    <tr>
      <td height="1" class="blue-bg" colspan="7"></td>
    </tr>
    <?php if (( ! empty ( $this->_tpl_vars['strMessage'] ) )): ?>
    <tr>
      <td colspan="7" height="25"  class="error-normal" align="center" valign="middle"><?php echo $this->_tpl_vars['strMessage']; ?>
</td>
    </tr>
    <?php endif; ?>
    <tr>
      <td colspan="7" align="left"><table width="96%" border="0" cellspacing="0" cellpadding="0" align="center">
          <tr>
            <td height="8" colspan="4" align="right">&nbsp;</td>
          </tr>
          <tr>
            <td height="8" align="right"><?php if ($this->_tpl_vars['strPageType'] != 'A'): ?>
              <table border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td align="center">Search Fields:</td>
        <td align="center"><?php echo $this->_tpl_vars['strFieldCombo']; ?>
</td>
      </tr>
    </table><?php endif; ?></td>
          </tr>
          <tr>
            <td colspan="4" align="center"><fieldset>
              <legend><?php echo $this->_tpl_vars['strTitle']; ?>
 Fields:</legend>
              <table width="100%" border="0" cellspacing="1" cellpadding="1">
                <tr>
                  <td align="right" valign="middle" height="24" ><?php echo $this->_tpl_vars['LBL_field_referal']; ?>
</td>
                  <td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strfield_referal']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=REFERAL&amp;ControlFlag=0','<?php echo $this->_tpl_vars['referal']; ?>
','TaRtxt_field_referal',500,500);" class="help" alt="Zoom" /></td>
                  <td width="23%" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_addedit_field_html_type']; ?>
</td>
                  <td width="59%" align="left" valign="top"><?php echo $this->_tpl_vars['straddedit_field_html_type']; ?>
<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&parent_id=20&code=ADDEDIT_FIELD_HTML_TYPE&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_addedit_field_html_type']; ?>
','Taslt_addedit_field_html_type',500,500);" class="help" alt="Zoom"></td>
                </tr>
                <tr>
                  <td align="right" width="15%" valign="middle" height="25"><?php echo $this->_tpl_vars['LBL_field_name']; ?>
</td>
                  <td width="29%" height="25" align="left" valign="middle"><?php echo $this->_tpl_vars['strfield_name']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=FILED_NAME&amp;ControlFlag=0','<?php echo $this->_tpl_vars['name']; ?>
','TaRtxt_field_name',500,500);" class="help" alt="Zoom" /> </td>
                  <td height="24" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_add_field_type']; ?>
</td>
                  <td height="24" align="left" valign="top"><?php echo $this->_tpl_vars['stradd_field_type']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onClick="zoomIn('index.php?file=med_zoom&parent_id=20&code=ADD_FIELD_TYPE&ControlFlag=1','<?php echo $this->_tpl_vars['LBL_add_field_type']; ?>
','Taslt_add_field_type',500,500);" class="help" alt="Zoom"> </td>
                </tr>
                <tr>
                  <td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_db_field_name']; ?>
</td>
                  <td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strdb_field_name']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=FIELD_TYPE&amp;ControlFlag=1','<?php echo $this->_tpl_vars['LBL_field_type']; ?>
','Tatxt_db_field_name',500,500);" class="help" alt="Zoom" /> </td>
                  <td align="right" valign="top" height="24"><?php echo $this->_tpl_vars['LBL_add_html_text']; ?>
</td>
                  <td align="left" valign="top" height="24"><?php echo $this->_tpl_vars['stradd_html_text']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=ADDEDIT_HTML_TEXT&amp;ControlFlag=0','<?php echo $this->_tpl_vars['LBL_add_html_text']; ?>
','Tatxt_add_html_text',500,500);" class="help" alt="Zoom" /> </td>
                </tr>
                <tr>
                  <td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_condition']; ?>
</td>
                  <td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strcondition']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=FIELD_TYPE&amp;ControlFlag=1','<?php echo $this->_tpl_vars['LBL_conditon']; ?>
','Taslt_field_type',500,500);" class="help" alt="Zoom" /> </td>
                  <td align="right"  valign="top" height="24"><?php echo $this->_tpl_vars['LBL_field_length']; ?>
</td>
                  <td align="left" valign="top" height="24"><?php echo $this->_tpl_vars['strfield_length']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=FIELD_LENGTH&amp;ControlFlag=0','<?php echo $this->_tpl_vars['LBL_field_length']; ?>
','Tatxt_field_length',500,500);" class="help" alt="Zoom" /> </td>
                </tr>
                <tr>
                  <td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_field_type']; ?>
</td>
                  <td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strfield_type']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=FIELD_TYPE&amp;ControlFlag=1','<?php echo $this->_tpl_vars['LBL_field_type']; ?>
','Taslt_field_type',500,500);" class="help" alt="Zoom" /> </td>
                  <td height="24" align="right"  valign="top"><?php echo $this->_tpl_vars['LBL_add_field_length_show']; ?>
</td>
                      <td width="59%" height="24" align="left" valign="top"><?php echo $this->_tpl_vars['stradd_field_length_show']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=ADD_FIELD_LENGTH_SHOW&amp;ControlFlag=0','<?php echo $this->_tpl_vars['LBL_add_field_length_show']; ?>
','Tatxt_add_field_length_show',500,500);" class="help" alt="Zoom" /> </td>
                </tr>
				<tr>
				  <td align="right" valign="middle" height="24"><?php echo $this->_tpl_vars['LBL_seq_no']; ?>
</td>
				  <td align="left" valign="middle" height="24"><?php echo $this->_tpl_vars['strseq_no']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=SEQUENCE_NUMBER&amp;ControlFlag=0','<?php echo $this->_tpl_vars['LBL_seq_no']; ?>
','Tatxt_seq_no',500,500);" class="help" alt="Zoom" /></td>
				  <td height="24" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_add_extra_property']; ?>
</td>
                  <td height="24" align="left" valign="top"><?php echo $this->_tpl_vars['stradd_extra_property']; ?>
 <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=ADD_EXTRA_PROPERTY&amp;ControlFlag=0','<?php echo $this->_tpl_vars['LBL_add_extra_property']; ?>
','Tatxt_add_extra_property',500,500);" class="help" alt="Zoom" /> </td>
				</tr>
                  
				<tr>
                  <td height="24" align="right" valign="top"><?php echo $this->_tpl_vars['LBL_field_desc']; ?>
</td>
                  <td align="left" valign="top"><?php echo $this->_tpl_vars['strfield_desc']; ?>
<img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=FIELD_DESC&amp;ControlFlag=0','<?php echo $this->_tpl_vars['LBL_field_desc']; ?>
','Taara_field_desc',500,500);" class="help" alt="Zoom" /></td>
                  <td height="24" align="right"  valign="top"><?php echo $this->_tpl_vars['LBL_isrequired']; ?>
</td>
                      <td width="59%" align="left" valign="top"><?php echo $this->_tpl_vars['strisrequired']; ?>
&nbsp; <img src="<?php echo $this->_tpl_vars['IMAGE_PATH']; ?>
zoom.gif" border="0" onclick="zoomIn('index.php?file=med_zoom&parent_id=20&amp;code=ISREQUIRED&amp;ControlFlag=1','<?php echo $this->_tpl_vars['LBL_isrequired']; ?>
','Taslt_isrequired',500,500);" class="help" alt="Zoom" /> </td>
                </tr>
                
				
                <tr>
                  <td height="30" colspan="4" align="center" valign="middle"><input type='submit' name='smt_submit' value='Submit' onclick='return submit_form(this.form);' class='btn' /></td>
                </tr>
              </table>
              </fieldset></td>
          </tr>
          <tr>
            <td colspan="4" height="8"></td>
          </tr>
          <tr>
            <td colspan="2" height="4"></td>
          </tr>
          <tr>
            <td align="center" colspan="2">&nbsp;</td>
          </tr>
          <tr>
            <td colspan="2" height="4" ></td>
          </tr>
        </table>
		</td>
		</tr></table>
</form>