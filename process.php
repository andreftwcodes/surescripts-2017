<?php

class Process {
    private $strPIDFile;
 
    function __construct($pProcessIdFile) {
        $this->strPIDFile = $pProcessIdFile;
        if(file_exists($this->strPIDFile)) {
            if(!is_writable($this->strPIDFile)) {
                throw new Exception('File Not Writable', 101);
            }
 
            $pid = trim(file_get_contents($this->strPIDFile));
            if(win_kill($pid, 0)) {
                if($this->is_alive($pid)) {
                    
                    throw new Exception('Process Already Running', 100);
                } else {
                    
                    unlink($this->strPIDFile);
                }
            }
        } else {
            if(!is_writable(dirname($this->strPIDFile))) {
                throw new Exception('Directory Is Not Writeable', 102);
            }
        }
 
        $id = getmypid();
        file_put_contents($this->strPIDFile, $id);
 
    }
 
    public function __destruct() {
        if(file_exists($this->strPIDFile)) {
            unlink($this->strPIDFile);
        }
    }
 
    private function is_alive($pId){
		
        
		
		exec('tasklist /fi "status eq Running" /fi "PID eq '.$pId.'"');
        return(count($ProcessState) >= 2);
    }
}
function win_kill($pid){
    $wmi=new COM("winmgmts:{impersonationLevel=impersonate}!\\\\.\\root\\cimv2");
    $procs=$wmi->ExecQuery("SELECT * FROM Win32_Process WHERE ProcessId='".$pid."'");
    foreach($procs as $proc)
      $proc->Terminate();
} 