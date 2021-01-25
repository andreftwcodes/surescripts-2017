<?php



if (!defined('ADODB_DIR')) die();

include_once(ADODB_DIR."/drivers/adodb-postgres64.inc.php");

class ADODB_postgres7 extends ADODB_postgres64 {
	var $databaseType = 'postgres7';	
	var $hasLimit = true;	
	var $ansiOuter = true;
	var $charSet = true; 
	
	function ADODB_postgres7() 
	{
		$this->ADODB_postgres64();
		if (ADODB_ASSOC_CASE !== 2) {
			$this->rsPrefix .= 'assoc_';
		}
		$this->_bindInputArray = PHP_VERSION >= 5.1;
	}

	
	
	
	 function SelectLimit($sql,$nrows=-1,$offset=-1,$inputarr=false,$secs2cache=0) 
	 {
		 $offsetStr = ($offset >= 0) ? " OFFSET ".((integer)$offset) : '';
		 $limitStr  = ($nrows >= 0)  ? " LIMIT ".((integer)$nrows) : '';
		 if ($secs2cache)
		  	$rs = $this->CacheExecute($secs2cache,$sql."$limitStr$offsetStr",$inputarr);
		 else
		  	$rs = $this->Execute($sql."$limitStr$offsetStr",$inputarr);
		
		return $rs;
	 }
 	


	
	function MetaForeignKeys($table, $owner=false, $upper=false)
	{
		$sql = 'SELECT t.tgargs as args
		FROM
		pg_trigger t,pg_class c,pg_proc p
		WHERE
		t.tgenabled AND
		t.tgrelid = c.oid AND
		t.tgfoid = p.oid AND
		p.proname = \'RI_FKey_check_ins\' AND
		c.relname = \''.strtolower($table).'\'
		ORDER BY
			t.tgrelid';
		
		$rs = $this->Execute($sql);
		
		if (!$rs || $rs->EOF) return false;
		
		$arr = $rs->GetArray();
		$a = array();
		foreach($arr as $v) {
			$data = explode(chr(0), $v['args']);
			$size = count($data)-1; 
			for($i = 4; $i < $size; $i++) {
				if ($upper) 
					$a[strtoupper($data[2])][] = strtoupper($data[$i].'='.$data[++$i]);
				else 
					$a[$data[2]][] = $data[$i].'='.$data[++$i];
			}
		}
		return $a;
	}

	function _query($sql,$inputarr)
	{
		if (! $this->_bindInputArray) {
			
			return ADODB_postgres64::_query($sql, $inputarr);
		}
		$this->_errorMsg = false;
		
		if ($inputarr) {
			$sqlarr = explode('?',trim($sql));
			$sql = '';
			$i = 1;
			$last = sizeof($sqlarr)-1;
			foreach($sqlarr as $v) {
				if ($last < $i) $sql .= $v;
				else $sql .= $v.' $'.$i;
				$i++;
			}
			
			$rez = pg_query_params($this->_connectionID,$sql, $inputarr);
		} else {
			$rez = pg_query($this->_connectionID,$sql);
		}
		
		if ($rez && pg_numfields($rez) <= 0) {
			if (is_resource($this->_resultid) && get_resource_type($this->_resultid) === 'pgsql result') {
				pg_freeresult($this->_resultid);
			}
			$this->_resultid = $rez;
			return true;
		}		
		return $rez;
	}
	
 	 
	
	
	
	
	
	
	function GetCharSet()
	{
		
		$this->charSet = @pg_client_encoding($this->_connectionID);
		if (!$this->charSet) {
			return false;
		} else {
			return $this->charSet;
		}
	}
	
	
	function SetCharSet($charset_name)
	{
		$this->GetCharSet();
		if ($this->charSet !== $charset_name) {
			$if = pg_set_client_encoding($this->_connectionID, $charset_name);
			if ($if == "0" & $this->GetCharSet() == $charset_name) {
				return true;
			} else return false;
		} else return true;
	}

}
	


class ADORecordSet_postgres7 extends ADORecordSet_postgres64{

	var $databaseType = "postgres7";
	
	
	function ADORecordSet_postgres7($queryID,$mode=false) 
	{
		$this->ADORecordSet_postgres64($queryID,$mode);
	}
	
	 	
	function MoveNext() 
	{
		if (!$this->EOF) {
			$this->_currentRow++;
			if ($this->_numOfRows < 0 || $this->_numOfRows > $this->_currentRow) {
				$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);
			
				if (is_array($this->fields)) {
					if ($this->fields && isset($this->_blobArr)) $this->_fixblobs();
					return true;
				}
			}
			$this->fields = false;
			$this->EOF = true;
		}
		return false;
	}		

}

class ADORecordSet_assoc_postgres7 extends ADORecordSet_postgres64{

	var $databaseType = "postgres7";
	
	
	function ADORecordSet_assoc_postgres7($queryID,$mode=false) 
	{
		$this->ADORecordSet_postgres64($queryID,$mode);
	}
	
	function _fetch()
	{
		if ($this->_currentRow >= $this->_numOfRows && $this->_numOfRows >= 0)
        	return false;

		$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);
		
		if ($this->fields) {
			if (isset($this->_blobArr)) $this->_fixblobs();
			$this->_updatefields();
		}
			
		return (is_array($this->fields));
	}
	
		
	function _updatefields()
	{
		if (ADODB_ASSOC_CASE == 2) return; 
	
		$arr = array();
		$lowercase = (ADODB_ASSOC_CASE == 0);
		
		foreach($this->fields as $k => $v) {
			if (is_integer($k)) $arr[$k] = $v;
			else {
				if ($lowercase)
					$arr[strtolower($k)] = $v;
				else
					$arr[strtoupper($k)] = $v;
			}
		}
		$this->fields = $arr;
	}
	
	function MoveNext() 
	{
		if (!$this->EOF) {
			$this->_currentRow++;
			if ($this->_numOfRows < 0 || $this->_numOfRows > $this->_currentRow) {
				$this->fields = @pg_fetch_array($this->_queryID,$this->_currentRow,$this->fetchMode);
			
				if (is_array($this->fields)) {
					if ($this->fields) {
						if (isset($this->_blobArr)) $this->_fixblobs();
					
						$this->_updatefields();
					}
					return true;
				}
			}
			
			
			$this->fields = false;
			$this->EOF = true;
		}
		return false;
	}
}
?>