<?php


global $_ADODB_ACTIVE_DBS;
global $ADODB_ACTIVE_CACHESECS; 
global $ACTIVE_RECORD_SAFETY; 
global $ADODB_ACTIVE_DEFVALS; 


$_ADODB_ACTIVE_DBS = array();
$ACTIVE_RECORD_SAFETY = true;
$ADODB_ACTIVE_DEFVALS = false;

class ADODB_Active_DB {
	var $db; 
	var $tables; 
}

class ADODB_Active_Table {
	var $name; 
	var $flds; 
	var $keys; 
	var $_created; 
}


function ADODB_SetDatabaseAdapter(&$db)
{
	global $_ADODB_ACTIVE_DBS;
	
		foreach($_ADODB_ACTIVE_DBS as $k => $d) {
			if (PHP_VERSION >= 5) {
				if ($d->db === $db) return $k;
			} else {
				if ($d->db->_connectionID === $db->_connectionID && $db->database == $d->db->database) 
					return $k;
			}
		}
		
		$obj = new ADODB_Active_DB();
		$obj->db = $db;
		$obj->tables = array();
		
		$_ADODB_ACTIVE_DBS[] = $obj;
		
		return sizeof($_ADODB_ACTIVE_DBS)-1;
}


class ADODB_Active_Record {
	var $_dbat; 
	var $_table; 
	var $_tableat; 
	var $_where; 
	var $_saved = false; 
	var $_lasterr = false; 
	var $_original = false; 
	
	static function UseDefaultValues($bool=null)
	{
	global $ADODB_ACTIVE_DEFVALS;
		if (isset($bool)) $ADODB_ACTIVE_DEFVALS = $bool;
		return $ADODB_ACTIVE_DEFVALS;
	}

	
	static function SetDatabaseAdapter(&$db) 
	{
		return ADODB_SetDatabaseAdapter($db);
	}
	
	
	public function __set($name, $value)
	{
		$name = str_replace(' ', '_', $name);
		$this->$name = $value;
	}
	
	
	function __construct($table = false, $pkeyarr=false, $db=false)
	{
	global $ADODB_ASSOC_CASE,$_ADODB_ACTIVE_DBS;
	
		if ($db == false && is_object($pkeyarr)) {
			$db = $pkeyarr;
			$pkeyarr = false;
		}
		
		if (!$table) { 
			if (!empty($this->_table)) $table = $this->_table;
			else $table = $this->_pluralize(get_class($this));
		}
		if ($db) {
			$this->_dbat = ADODB_Active_Record::SetDatabaseAdapter($db);
		} else
			$this->_dbat = sizeof($_ADODB_ACTIVE_DBS)-1;
		
		
		if ($this->_dbat < 0) $this->Error("No database connection set; use ADOdb_Active_Record::SetDatabaseAdapter(\$db)",'ADODB_Active_Record::__constructor');
		
		$this->_table = $table;
		$this->_tableat = $table; # reserved for setting the assoc value to a non-table name, eg. the sql string in future
		$this->UpdateActiveTable($pkeyarr);
	}
	
	function __wakeup()
	{
  		$class = get_class($this);
  		new $class;
	}
	
	function _pluralize($table)
	{
		$ut = strtoupper($table);
		$len = strlen($table);
		$lastc = $ut[$len-1];
		$lastc2 = substr($ut,$len-2);
		switch ($lastc) {
		case 'S':
			return $table.'es';	
		case 'Y':
			return substr($table,0,$len-1).'ies';
		case 'X':	
			return $table.'es';
		case 'H': 
			if ($lastc2 == 'CH' || $lastc2 == 'SH')
				return $table.'es';
		default:
			return $table.'s';
		}
	}
	
	
	
	
	function UpdateActiveTable($pkeys=false,$forceUpdate=false)
	{
	global $ADODB_ASSOC_CASE,$_ADODB_ACTIVE_DBS , $ADODB_CACHE_DIR, $ADODB_ACTIVE_CACHESECS;
	global $ADODB_ACTIVE_DEFVALS;

		$activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];

		$table = $this->_table;
		$tables = $activedb->tables;
		$tableat = $this->_tableat;
		if (!$forceUpdate && !empty($tables[$tableat])) {

			$tobj = $tables[$tableat];
			foreach($tobj->flds as $name => $fld) {
			if ($ADODB_ACTIVE_DEFVALS && isset($fld->default_value)) 
				$this->$name = $fld->default_value;
			else
				$this->$name = null;
			}
			return;
		}
		
		$db = $activedb->db;
		$fname = $ADODB_CACHE_DIR . '/adodb_' . $db->databaseType . '_active_'. $table . '.cache';
		if (!$forceUpdate && $ADODB_ACTIVE_CACHESECS && $ADODB_CACHE_DIR && file_exists($fname)) {
			$fp = fopen($fname,'r');
			@flock($fp, LOCK_SH);
			$acttab = unserialize(fread($fp,100000));
			fclose($fp);
			if ($acttab->_created + $ADODB_ACTIVE_CACHESECS - (abs(rand()) % 16) > time()) { 
				
				
				$activedb->tables[$table] = $acttab;
				
				
			  	return;
			} else if ($db->debug) {
				ADOConnection::outp("Refreshing cached active record file: $fname");
			}
		}
		$activetab = new ADODB_Active_Table();
		$activetab->name = $table;
		
		
		$cols = $db->MetaColumns($table);
		if (!$cols) {
			$this->Error("Invalid table name: $table",'UpdateActiveTable'); 
			return false;
		}
		$fld = reset($cols);
		if (!$pkeys) {
			if (isset($fld->primary_key)) {
				$pkeys = array();
				foreach($cols as $name => $fld) {
					if (!empty($fld->primary_key)) $pkeys[] = $name;
				}
			} else	
				$pkeys = $this->GetPrimaryKeys($db, $table);
		}
		if (empty($pkeys)) {
			$this->Error("No primary key found for table $table",'UpdateActiveTable');
			return false;
		}
		
		$attr = array();
		$keys = array();
		
		switch($ADODB_ASSOC_CASE) {
		case 0:
			foreach($cols as $name => $fldobj) {
				$name = strtolower($name);
                if ($ADODB_ACTIVE_DEFVALS && isset($fldobj->default_value))
                    $this->$name = $fldobj->default_value;
                else
					$this->$name = null;
				$attr[$name] = $fldobj;
			}
			foreach($pkeys as $k => $name) {
				$keys[strtolower($name)] = strtolower($name);
			}
			break;
			
		case 1: 
			foreach($cols as $name => $fldobj) {
				$name = strtoupper($name);
               
                if ($ADODB_ACTIVE_DEFVALS && isset($fldobj->default_value))
                    $this->$name = $fldobj->default_value;
                else
					$this->$name = null;
				$attr[$name] = $fldobj;
			}
			
			foreach($pkeys as $k => $name) {
				$keys[strtoupper($name)] = strtoupper($name);
			}
			break;
		default:
			foreach($cols as $name => $fldobj) {
				$name = ($fldobj->name);
                
                if ($ADODB_ACTIVE_DEFVALS && isset($fldobj->default_value))
                    $this->$name = $fldobj->default_value;
                else
					$this->$name = null;
				$attr[$name] = $fldobj;
			}
			foreach($pkeys as $k => $name) {
				$keys[$name] = $cols[$name]->name;
			}
			break;
		}
		
		$activetab->keys = $keys;
		$activetab->flds = $attr;

		if ($ADODB_ACTIVE_CACHESECS && $ADODB_CACHE_DIR) {
			$activetab->_created = time();
			$s = serialize($activetab);
			if (!function_exists('adodb_write_file')) include(ADODB_DIR.'/adodb-csvlib.inc.php');
			adodb_write_file($fname,$s);
		}
		$activedb->tables[$table] = $activetab;
	}
	
	function GetPrimaryKeys(&$db, $table)
	{
		return $db->MetaPrimaryKeys($table);
	}
	
	
	function Error($err,$fn)
	{
	global $_ADODB_ACTIVE_DBS;
	
		$fn = get_class($this).'::'.$fn;
		$this->_lasterr = $fn.': '.$err;
		
		if ($this->_dbat < 0) $db = false;
		else {
			$activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];
			$db = $activedb->db;
		}
		
		if (function_exists('adodb_throw')) {	
			if (!$db) adodb_throw('ADOdb_Active_Record', $fn, -1, $err, 0, 0, false);
			else adodb_throw($db->databaseType, $fn, -1, $err, 0, 0, $db);
		} else
			if (!$db || $db->debug) ADOConnection::outp($this->_lasterr);
		
	}
	
	
	function ErrorMsg()
	{
		if (!function_exists('adodb_throw')) {
			if ($this->_dbat < 0) $db = false;
			else $db = $this->DB();
		
			
			if ($db && $db->ErrorMsg()) return $db->ErrorMsg();
		}
		return $this->_lasterr;
	}
	
	function ErrorNo() 
	{
		if ($this->_dbat < 0) return -9999; 
		$db = $this->DB();
		
		return (int) $db->ErrorNo();
	}


	
	function DB()
	{
	global $_ADODB_ACTIVE_DBS;
	
		if ($this->_dbat < 0) {
			$false = false;
			$this->Error("No database connection set: use ADOdb_Active_Record::SetDatabaseAdaptor(\$db)", "DB");
			return $false;
		}
		$activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];
		$db = $activedb->db;
		return $db;
	}
	
	
	function TableInfo()
	{
	global $_ADODB_ACTIVE_DBS;
	
		$activedb = $_ADODB_ACTIVE_DBS[$this->_dbat];
		$table = $activedb->tables[$this->_tableat];
		return $table;
	}
	
	
	function Set(&$row)
	{
	global $ACTIVE_RECORD_SAFETY;
	
		$db = $this->DB();
		
		if (!$row) {
			$this->_saved = false;		
			return false;
		}
		
		$this->_saved = true;
		
		$table = $this->TableInfo();
		if ($ACTIVE_RECORD_SAFETY && sizeof($table->flds) != sizeof($row)) {
            # <AP>
            $bad_size = TRUE;
            if (sizeof($row) == 2 * sizeof($table->flds)) {
                
                $keys = array_filter(array_keys($row), 'is_string');
                if (sizeof($keys) == sizeof($table->flds))
                    $bad_size = FALSE;
            }
            if ($bad_size) {
			$this->Error("Table structure of $this->_table has changed","Load");
			return false;
		}
            # </AP>
		}
        else
		$keys = array_keys($row);
        # <AP>
        reset($keys);
        $this->_original = array();
		foreach($table->flds as $name=>$fld) {
            $value = $row[current($keys)];
			$this->$name = $value;
            $this->_original[] = $value;
            next($keys);
		}
        # </AP>
		return true;
	}
	
	
	function LastInsertID(&$db,$fieldname)
	{
		if ($db->hasInsertID)
			$val = $db->Insert_ID($this->_table,$fieldname);
		else
			$val = false;
			
		if (is_null($val) || $val === false) {
			
			return $db->GetOne("select max(".$fieldname.") from ".$this->_table);
		}
		return $val;
	}
	
	
	function doquote(&$db, $val,$t)
	{
		switch($t) {
		case 'D':
		case 'T':
			if (empty($val)) return 'null';
			
		case 'C':
		case 'X':
			if (is_null($val)) return 'null';
			
			if (strncmp($val,"'",1) != 0 && substr($val,strlen($val)-1,1) != "'") { 
				return $db->qstr($val);
				break;
			}
		default:
			return $val;
			break;
		}
	}
	
	
	function GenWhere(&$db, &$table)
	{
		$keys = $table->keys;
		$parr = array();
		
		foreach($keys as $k) {
			$f = $table->flds[$k];
			if ($f) {
				$parr[] = $k.' = '.$this->doquote($db,$this->$k,$db->MetaType($f->type));
			}
		}
		return implode(' and ', $parr);
	}
	
	
	
	
	function Load($where,$bindarr=false)
	{
		$db = $this->DB(); if (!$db) return false;
		$this->_where = $where;
		
		$save = $db->SetFetchMode(ADODB_FETCH_NUM);
		$row = $db->GetRow("select * from ".$this->_table.' WHERE '.$where,$bindarr);
		$db->SetFetchMode($save);
		
		return $this->Set($row);
	}
	
	
	function Save()
	{
		if ($this->_saved) $ok = $this->Update();
		else $ok = $this->Insert();
		
		return $ok;
	}
	
	
	function Insert()
	{
		$db = $this->DB(); if (!$db) return false;
		$cnt = 0;
		$table = $this->TableInfo();
		
		$valarr = array();
		$names = array();
		$valstr = array();

		foreach($table->flds as $name=>$fld) {
			$val = $this->$name;
			if(!is_null($val) || !array_key_exists($name, $table->keys)) {
				$valarr[] = $val;
				$names[] = $name;
				$valstr[] = $db->Param($cnt);
				$cnt += 1;
			}
		}
		
		if (empty($names)){
			foreach($table->flds as $name=>$fld) {
				$valarr[] = null;
				$names[] = $name;
				$valstr[] = $db->Param($cnt);
				$cnt += 1;
			}
		}
		$sql = 'INSERT INTO '.$this->_table."(".implode(',',$names).') VALUES ('.implode(',',$valstr).')';
		$ok = $db->Execute($sql,$valarr);
		
		if ($ok) {
			$this->_saved = true;
			$autoinc = false;
			foreach($table->keys as $k) {
				if (is_null($this->$k)) {
					$autoinc = true;
					break;
				}
			}
			if ($autoinc && sizeof($table->keys) == 1) {
				$k = reset($table->keys);
				$this->$k = $this->LastInsertID($db,$k);
			}
		}
		
		$this->_original = $valarr;
		return !empty($ok);
	}
	
	function Delete()
	{
		$db = $this->DB(); if (!$db) return false;
		$table = $this->TableInfo();
		
		$where = $this->GenWhere($db,$table);
		$sql = 'DELETE FROM '.$this->_table.' WHERE '.$where;
		$ok = $db->Execute($sql);
		
		return $ok ? true : false;
	}
	
	
	function Find($whereOrderBy,$bindarr=false,$pkeysArr=false)
	{
		$db = $this->DB(); if (!$db || empty($this->_table)) return false;
		$arr = $db->GetActiveRecordsClass(get_class($this),$this->_table, $whereOrderBy,$bindarr,$pkeysArr);
		return $arr;
	}
	
	
	function Replace()
	{
	global $ADODB_ASSOC_CASE;
		
		$db = $this->DB(); if (!$db) return false;
		$table = $this->TableInfo();
		
		$pkey = $table->keys;
		
		foreach($table->flds as $name=>$fld) {
			$val = $this->$name;
			
			if (is_null($val) && !empty($fld->auto_increment)) {
            	continue;
            }
			$t = $db->MetaType($fld->type);
			$arr[$name] = $this->doquote($db,$val,$t);
			$valarr[] = $val;
		}
		
		if (!is_array($pkey)) $pkey = array($pkey);
		
		
		if ($ADODB_ASSOC_CASE == 0) 
			foreach($pkey as $k => $v)
				$pkey[$k] = strtolower($v);
		elseif ($ADODB_ASSOC_CASE == 1) 
			foreach($pkey as $k => $v)
				$pkey[$k] = strtoupper($v);
				
		$ok = $db->Replace($this->_table,$arr,$pkey);
		if ($ok) {
			$this->_saved = true; 
			if ($ok == 2) {
				$autoinc = false;
				foreach($table->keys as $k) {
					if (is_null($this->$k)) {
						$autoinc = true;
						break;
					}
				}
				if ($autoinc && sizeof($table->keys) == 1) {
					$k = reset($table->keys);
					$this->$k = $this->LastInsertID($db,$k);
				}
			}
			
			$this->_original = $valarr;
		} 
		return $ok;
	}

	
	function Update()
	{
		$db = $this->DB(); if (!$db) return false;
		$table = $this->TableInfo();
		
		$where = $this->GenWhere($db, $table);
		
		if (!$where) {
			$this->error("Where missing for table $table", "Update");
			return false;
		}
		$valarr = array(); 
		$neworig = array();
		$pairs = array();
		$i = -1;
		$cnt = 0;
		foreach($table->flds as $name=>$fld) {
			$i += 1;
			$val = $this->$name;
			$neworig[] = $val;
			
			if (isset($table->keys[$name])) {
				continue;
			}
			
			if (is_null($val)) {
				if (isset($fld->not_null) && $fld->not_null) {
					if (isset($fld->default_value) && strlen($fld->default_value)) continue;
					else {
						$this->Error("Cannot set field $name to NULL","Update");
						return false;
					}
				}
			}
			
			if (isset($this->_original[$i]) && $val == $this->_original[$i]) {
				continue;
			}			
			$valarr[] = $val;
			$pairs[] = $name.'='.$db->Param($cnt);
			$cnt += 1;
		}
		
		
		if (!$cnt) return -1;
		$sql = 'UPDATE '.$this->_table." SET ".implode(",",$pairs)." WHERE ".$where;
		$ok = $db->Execute($sql,$valarr);
		if ($ok) {
			$this->_original = $neworig;
			return 1;
		}
		return 0;
	}
	
	function GetAttributeNames()
	{
		$table = $this->TableInfo();
		if (!$table) return false;
		return array_keys($table->flds);
	}
	
};

?>