<?php


	
	$strId			=	$objPage->getRequest('strFieldId');
	$arrId			=	explode(":",$strId);
	$intFieldId		=	$arrId[1];
	$strFieldType	=	$arrId[0];

	
	$rsComboDetails	=	$objPage->getHtmlControlandFieldNameByFieldId($strFieldType,$intFieldId);
	
	
	$strDivName		=	"div_".$rsComboDetails["strFieldName"];
	
	
	echo "!@#***#@!".$strDivName."!@#***#@!".$rsComboDetails["strHtmlControl"]."!@#***#@!";
	exit;

?>
