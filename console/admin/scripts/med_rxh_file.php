<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=iso-8859-1" />
<title>File Detatils</title>
<link href="./images/med_style.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" href="./images/med_modal_message.css" type="text/css">
<link rel="stylesheet" href="./images/jquery.cluetip.css" type="text/css">
<script type="text/javascript" src="./../base/javascript/jquery.min.js"></script>
<script type="text/javascript" src="./../base/meditab/med_quicklist.js"></script>
<script type="text/javascript" src="./../base/jsoverlib/overlibmws_iframe.js"></script>
<script type="text/javascript" src="./../base/javascript/jquery.cluetip.js"></script>
<script type="text/javascript" src="./../base/javascript/jquery.hoverIntent.js"></script>

<form name="frm_rxh_master" id="frm_rxh_master" method="post" action="index.php">
    <input type="hidden" name="file" id="file"  value="med_rxh_common">
    <input type="hidden" name="hid_button_id" id="hid_button_id"  value="{$strButtonId}">
    <input type="hidden" name="hid_table_id" id="hid_table_id"  value="{$intTableId}">
    <input type="hidden" name="hid_page_type" id="hid_page_type"  value="L">
    <input type="hidden" name="hid_max_row_limit" id="hid_max_row_limit" value="{$intShowMaxRows}" />
    <table width="100%" border="0" align="center" cellpadding="0" cellspacing="0" class="tab-bor">
        <tr>
            <td height="24" align="left" valign="middle"><img src="images/orange-sarrow.gif" width="9" height="9" hspace="6" /> <font class="bold-text">File Data</font></td>
            <td width="10%" align="right"><img src="images/dot-back-arrow.gif" width="5" height="9" hspace="5" /><a href="#" onclick="self.parent.tb_remove();">Close</a>&nbsp;</td>
        </tr>
        <tr>
            <td height="1" class="blue-bg" colspan="2"></td>
        </tr>
		<tr>
            <td colspan="2"><table width="100%" border="0" cellpadding="0" cellspacing="0">
                    <tr>
                        <td align="left" colspan="2">
						<?PHP
							
							include_once("./base/med_module.php"); 
						
							
							$objModule 				=	new MedModule();	
						
							
							$intARHId				=	$objPage->getRequest('arh_id');
							$strFileType			=	$objPage->getRequest("file_type");
							
							
							$strTbl_Name			=	"rxh_ar_header";
							$strField_Names			=	"*";
							$strWhere				=	"mt_rxh_arh_id = '".$intARHId."'";
							$rsARHData				=	$objPage->getRecords($strTbl_Name, $strField_Names, $strWhere, "", "","", "");
							print "<pre>";
							print '<font style="font-size:13px; font-family:Tahoma,Arial,Helvetica,sans-serif;">';
							
							if($strFileType  == 'Response')
							{
								$strFilePath		=	$rsARHData[0]['mt_response_file'];
								if(file_exists($strFilePath))
								{
									$strFileData		=	file_get_contents($strFilePath);
									print $strFileData;exit;
								}
								else
								{
									print "No Data Fould"; exit;
								}
							}
							print "</font>";
						?>
						</td>
                    </tr>
                </table></td>
        </tr>
    </table>
</form>
</body>
</html>
<?PHP
exit;
?>