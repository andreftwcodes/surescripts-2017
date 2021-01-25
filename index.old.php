<?php

$Xml	= file_get_contents("php://input");

file_put_contents("c:/newxml.".mktime().".xml",$Xml);

?>