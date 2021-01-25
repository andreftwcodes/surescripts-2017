<?php

include_once "process.php";
try {
    $Process = new Process('./myphpscript.pid');
	sleep(25);
} catch(Exception $ex) {
    switch($ex->getCode()) {
        case 100:
            echo 'Script already running...';
            return;
        case 101:
            echo 'File Not Writable';
            return;
        case 102:
            echo 'Folder Not Writable';
            return;
    }
}