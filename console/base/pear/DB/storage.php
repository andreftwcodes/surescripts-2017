<?php






require_once 'DB.php';


class DB_storage extends PEAR
{
    

    
    var $_table = null;

    
    var $_keycolumn = null;

    
    var $_dbh = null;

    
    var $_properties = array();

    
    var $_changes = array();

    
    var $_readonly = false;

    
    var $_validator = null;

    
    

    
    function DB_storage($table, $keycolumn, &$dbh, $validator = null)
    {
        $this->PEAR('DB_Error');
        $this->_table = $table;
        $this->_keycolumn = $keycolumn;
        $this->_dbh = $dbh;
        $this->_readonly = false;
        $this->_validator = $validator;
    }

    
    

    
    function _makeWhere($keyval = null)
    {
        if (is_array($this->_keycolumn)) {
            if ($keyval === null) {
                for ($i = 0; $i < sizeof($this->_keycolumn); $i++) {
                    $keyval[] = $this->{$this->_keycolumn[$i]};
                }
            }
            $whereclause = '';
            for ($i = 0; $i < sizeof($this->_keycolumn); $i++) {
                if ($i > 0) {
                    $whereclause .= ' AND ';
                }
                $whereclause .= $this->_keycolumn[$i];
                if (is_null($keyval[$i])) {
                    
                    
                    $whereclause .= ' IS NULL';
                } else {
                    $whereclause .= ' = ' . $this->_dbh->quote($keyval[$i]);
                }
            }
        } else {
            if ($keyval === null) {
                $keyval = @$this->{$this->_keycolumn};
            }
            $whereclause = $this->_keycolumn;
            if (is_null($keyval)) {
                
                
                $whereclause .= ' IS NULL';
            } else {
                $whereclause .= ' = ' . $this->_dbh->quote($keyval);
            }
        }
        return $whereclause;
    }

    
    

    
    function setup($keyval)
    {
        $whereclause = $this->_makeWhere($keyval);
        $query = 'SELECT * FROM ' . $this->_table . ' WHERE ' . $whereclause;
        $sth = $this->_dbh->query($query);
        if (DB::isError($sth)) {
            return $sth;
        }
        $row = $sth->fetchRow(DB_FETCHMODE_ASSOC);
        if (DB::isError($row)) {
            return $row;
        }
        if (!$row) {
            return $this->raiseError(null, DB_ERROR_NOT_FOUND, null, null,
                                     $query, null, true);
        }
        foreach ($row as $key => $value) {
            $this->_properties[$key] = true;
            $this->$key = $value;
        }
        return DB_OK;
    }

    
    

    
    function insert($newpk)
    {
        if (is_array($this->_keycolumn)) {
            $primarykey = $this->_keycolumn;
        } else {
            $primarykey = array($this->_keycolumn);
        }
        settype($newpk, "array");
        for ($i = 0; $i < sizeof($primarykey); $i++) {
            $pkvals[] = $this->_dbh->quote($newpk[$i]);
        }

        $sth = $this->_dbh->query("INSERT INTO $this->_table (" .
                                  implode(",", $primarykey) . ") VALUES(" .
                                  implode(",", $pkvals) . ")");
        if (DB::isError($sth)) {
            return $sth;
        }
        if (sizeof($newpk) == 1) {
            $newpk = $newpk[0];
        }
        $this->setup($newpk);
    }

    
    

    
    function toString()
    {
        $info = strtolower(get_class($this));
        $info .= " (table=";
        $info .= $this->_table;
        $info .= ", keycolumn=";
        if (is_array($this->_keycolumn)) {
            $info .= "(" . implode(",", $this->_keycolumn) . ")";
        } else {
            $info .= $this->_keycolumn;
        }
        $info .= ", dbh=";
        if (is_object($this->_dbh)) {
            $info .= $this->_dbh->toString();
        } else {
            $info .= "null";
        }
        $info .= ")";
        if (sizeof($this->_properties)) {
            $info .= " [loaded, key=";
            $keyname = $this->_keycolumn;
            if (is_array($keyname)) {
                $info .= "(";
                for ($i = 0; $i < sizeof($keyname); $i++) {
                    if ($i > 0) {
                        $info .= ",";
                    }
                    $info .= $this->$keyname[$i];
                }
                $info .= ")";
            } else {
                $info .= $this->$keyname;
            }
            $info .= "]";
        }
        if (sizeof($this->_changes)) {
            $info .= " [modified]";
        }
        return $info;
    }

    
    

    
    function dump()
    {
        foreach ($this->_properties as $prop => $foo) {
            print "$prop = ";
            print htmlentities($this->$prop);
            print "<br />\n";
        }
    }

    
    

    
    function &create($table, &$data)
    {
        $classname = strtolower(get_class($this));
        $obj =& new $classname($table);
        foreach ($data as $name => $value) {
            $obj->_properties[$name] = true;
            $obj->$name = &$value;
        }
        return $obj;
    }

    
    

    



    
    

    
    function set($property, $newvalue)
    {
        
        
        if ($this->_readonly) {
            return $this->raiseError(null, DB_WARNING_READ_ONLY, null,
                                     null, null, null, true);
        }
        if (@isset($this->_properties[$property])) {
            if (empty($this->_validator)) {
                $valid = true;
            } else {
                $valid = @call_user_func($this->_validator,
                                         $this->_table,
                                         $property,
                                         $newvalue,
                                         $this->$property,
                                         $this);
            }
            if ($valid) {
                $this->$property = $newvalue;
                if (empty($this->_changes[$property])) {
                    $this->_changes[$property] = 0;
                } else {
                    $this->_changes[$property]++;
                }
            } else {
                return $this->raiseError(null, DB_ERROR_INVALID, null,
                                         null, "invalid field: $property",
                                         null, true);
            }
            return true;
        }
        return $this->raiseError(null, DB_ERROR_NOSUCHFIELD, null,
                                 null, "unknown field: $property",
                                 null, true);
    }

    
    

    
    function &get($property)
    {
        
        if (isset($this->_properties[$property])) {
            return $this->$property;
        }
        $tmp = null;
        return $tmp;
    }

    
    

    
    function _DB_storage()
    {
        if (sizeof($this->_changes)) {
            $this->store();
        }
        $this->_properties = array();
        $this->_changes = array();
        $this->_table = null;
    }

    
    

    
    function store()
    {
        foreach ($this->_changes as $name => $foo) {
            $params[] = &$this->$name;
            $vars[] = $name . ' = ?';
        }
        if ($vars) {
            $query = 'UPDATE ' . $this->_table . ' SET ' .
                implode(', ', $vars) . ' WHERE ' .
                $this->_makeWhere();
            $stmt = $this->_dbh->prepare($query);
            $res = $this->_dbh->execute($stmt, $params);
            if (DB::isError($res)) {
                return $res;
            }
            $this->_changes = array();
        }
        return DB_OK;
    }

    
    

    
    function remove()
    {
        if ($this->_readonly) {
            return $this->raiseError(null, DB_WARNING_READ_ONLY, null,
                                     null, null, null, true);
        }
        $query = 'DELETE FROM ' . $this->_table .' WHERE '.
            $this->_makeWhere();
        $res = $this->_dbh->query($query);
        if (DB::isError($res)) {
            return $res;
        }
        foreach ($this->_properties as $prop => $foo) {
            unset($this->$prop);
        }
        $this->_properties = array();
        $this->_changes = array();
        return DB_OK;
    }

    
}



?>
