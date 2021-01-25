<?php

require_once "./base/Thread.php";


if( ! Thread::available() ) 
{
	die( 'Threads not supported' );
}


$Threads = array();
$index = 0;

$arrMsg = array("N123", "N234", "N343","R223");

foreach( $arrMsg as $index=>$msg)
{
	$Threads[$index] = new Thread('print_now');
	$Threads[$index]->start( $msg);
}

function print_now($do)
{
	for($i=0; $i < 20; $i++)
	{
		echo $i . '--' . $do . '<br/>';
	}
}


while( !empty( $threads ) ) {
	foreach( $threads as $index => $thread ) {
		if( ! $thread->isAlive() ) {
			unset( $threads[$index] );
		}
	}
	
	sleep( 1 );
}