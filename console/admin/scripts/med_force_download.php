<?PHP
header("Content-Type: application/".strtolower($_REQUEST['ext']));

header("Content-Disposition: attachment; filename=\"".$_REQUEST['hid_file_name']."\"" );
header("Content-Transfer-Encoding: binary");	
$strFile=file_get_contents($_REQUEST['hid_file_path']);
echo $strFile;
exit;
?>