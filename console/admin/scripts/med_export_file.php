<?php

	
	
	$strFileName= "18.txt";
	echo $strFileName;
	exit;
	
	header("Content-Type: application/force-download");
	header("Content-Disposition: attachment; filename=".$strFileName);
	readfile($strFileInfo);exit;
?>