<?php


include_once('./base/med_module.php');


$objModule		=	new MedModule();

$strFieldGroup	=	$objPage->getRequest("field_group");

$intFieldOrder = $objModule->getFieldOrder($strFieldGroup);


$objPage->setRequest("field_order",$intFieldOrder[0]['field_order']);


$arrHtmlControl	=	$objPage->getHtmlControlandFieldNameByFieldId("F",616);
$strHtmlControl	=	$arrHtmlControl['strHtmlControl'];
echo $strHtmlControl;
exit;
?>