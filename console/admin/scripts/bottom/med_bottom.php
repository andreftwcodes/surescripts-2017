<?php

	
	

	if(file_exists(str_replace("./","template/",$strMiddle)) or  file_exists(str_replace("middle","template/middle",$strMiddle)))
	{
	 	$objSmarty->assign("strMiddle",$strMiddle);
	}
	else 
	{
		$objGeneral->raiseError("SMARTY_ERROR",$objPage->getRequest("file")." - Template file not found","","Make Change In File Name or File Path");
		exit;	
	}
	if (!isset($localValues))	$localValues 	= 	array();
	$valueArray 	= 	array_merge($globalValues,$localValues);

	
	foreach ($valueArray as $key => $value) {
		$objSmarty->assign($key,$value);		
	}
	
	
	$objSmarty->display($strIndex);
?>
