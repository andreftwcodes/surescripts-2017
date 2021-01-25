<?php
define('SMARTY_DIR','./../base/smarty/libs/');
define('TEMPLATE_DIR','');
require(SMARTY_DIR.'Smarty.class.php');
class Smarty_Class extends Smarty 
{
   function Smarty_Class($cacheflag=true) 
   {
		$PATH='./../base/smarty/';  
		$this->Smarty();
		$this->template_dir = TEMPLATE_DIR."template/";
		$this->compile_dir = $PATH."templates_c/admin/";
		$this->config_dir = $PATH."configs/";
		$this->cache_dir = $PATH."cache/";
		$this->caching = $cacheflag;
		$this->debug = true;
   }
}


$objSmarty = new Smarty_Class(false);  

$strTop="./top/med_top.htm"; 					
$strBottom="./bottom/med_bottom.htm";			
$strLeft="./left/med_left.htm";					
$strLeftAdmin="./left/med_left_admin.htm";	
$strIndex="index.htm";							
?>