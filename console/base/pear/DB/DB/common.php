<?php









class DB_common extends PEAR
{
    

    
    var $fetchmode = DB_FETCHMODE_ORDERED;

    
    var $fetchmode_object_class = 'stdClass';

    
    var $was_connected = null;

    
    var $last_query = '';

    
    var $options = array(
        'result_buffering' => 500,
        'persistent' => false,
        'ssl' => false,
        'debug' => 0,
        'seqname_format' => '%s_seq',
        'autofree' => false,
        'portability' => DB_PORTABILITY_NONE,
        'optimize' => 'performance',  
    );

    
    var $last_parameters = array();

    
    var $prepare_tokens = array();

    
    var $prepare_types = array();

    
    var $prepared_queries = array();


    
    

    
    function DB_common()
    {
        $this->PEAR('DB_Error');
    }

    
    

    
    function __sleep()
    {
        if ($this->connection) {
            
            $this->was_connected = true;
        } else {
            $this->was_connected = false;
        }
        if (isset($this->autocommit)) {
            return array('autocommit',
                         'dbsyntax',
                         'dsn',
                         'features',
                         'fetchmode',
                         'fetchmode_object_class',
                         'options',
                         'was_connected',
                   );
        } else {
            return array('dbsyntax',
                         'dsn',
                         'features',
                         'fetchmode',
                         'fetchmode_object_class',
                         'options',
                         'was_connected',
                   );
        }
    }

    
    

    
    function __wakeup()
    {
        if ($this->was_connected) {
            $this->connect($this->dsn, $this->options);
        }
    }

    
    

    
    function __toString()
    {
        $info = strtolower(get_class($this));
        $info .=  ': (phptype=' . $this->phptype .
                  ', dbsyntax=' . $this->dbsyntax .
                  ')';
        if ($this->connection) {
            $info .= ' [connected]';
        }
        return $info;
    }

    
    

    
    function toString()
    {
        return $this->__toString();
    }

    
    

    
    function quoteString($string)
    {
        $string = $this->quote($string);
        if ($string{0} == "'") {
            return substr($string, 1, -1);
        }
        return $string;
    }

    
    

    
    function quote($string = null)
    {
        return ($string === null) ? 'NULL'
                                  : "'" . str_replace("'", "''", $string) . "'";
    }

    
    

    
    function quoteIdentifier($str)
    {
        return '"' . str_replace('"', '""', $str) . '"';
    }

    
    

    
    function quoteSmart($in)
    {
        if (is_int($in) || is_double($in)) {
            return $in;
        } elseif (is_bool($in)) {
            return $in ? 1 : 0;
        } elseif (is_null($in)) {
            return 'NULL';
        } else {
            return "'" . $this->escapeSimple($in) . "'";
        }
    }

    
    

    
    function escapeSimple($str)
    {
        return str_replace("'", "''", $str);
    }

    
    

    
    function provides($feature)
    {
        return $this->features[$feature];
    }

    
    

    
    function setFetchMode($fetchmode, $object_class = 'stdClass')
    {
        switch ($fetchmode) {
            case DB_FETCHMODE_OBJECT:
                $this->fetchmode_object_class = $object_class;
            case DB_FETCHMODE_ORDERED:
            case DB_FETCHMODE_ASSOC:
                $this->fetchmode = $fetchmode;
                break;
            default:
                return $this->raiseError('invalid fetchmode mode');
        }
    }

    
    

    
    function setOption($option, $value)
    {
        if (isset($this->options[$option])) {
            $this->options[$option] = $value;

            
            if ($option == 'optimize') {
                if ($value == 'portability') {
                    switch ($this->phptype) {
                        case 'oci8':
                            $this->options['portability'] =
                                    DB_PORTABILITY_LOWERCASE |
                                    DB_PORTABILITY_NUMROWS;
                            break;
                        case 'fbsql':
                        case 'mysql':
                        case 'mysqli':
                        case 'sqlite':
                            $this->options['portability'] =
                                    DB_PORTABILITY_DELETE_COUNT;
                            break;
                    }
                } else {
                    $this->options['portability'] = DB_PORTABILITY_NONE;
                }
            }

            return DB_OK;
        }
        return $this->raiseError("unknown option $option");
    }

    
    

    
    function getOption($option)
    {
        if (isset($this->options[$option])) {
            return $this->options[$option];
        }
        return $this->raiseError("unknown option $option");
    }

    
    

    
    function prepare($query)
    {
        $tokens   = preg_split('/((?<!\\\)[&?!])/', $query, -1,
                               PREG_SPLIT_DELIM_CAPTURE);
        $token     = 0;
        $types     = array();
        $newtokens = array();

        foreach ($tokens as $val) {
            switch ($val) {
                case '?':
                    $types[$token++] = DB_PARAM_SCALAR;
                    break;
                case '&':
                    $types[$token++] = DB_PARAM_OPAQUE;
                    break;
                case '!':
                    $types[$token++] = DB_PARAM_MISC;
                    break;
                default:
                    $newtokens[] = preg_replace('/\\\([&?!])/', "\\1", $val);
            }
        }

        $this->prepare_tokens[] = &$newtokens;
        end($this->prepare_tokens);

        $k = key($this->prepare_tokens);
        $this->prepare_types[$k] = $types;
        $this->prepared_queries[$k] = implode(' ', $newtokens);

        return $k;
    }

    
    

    
    function autoPrepare($table, $table_fields, $mode = DB_AUTOQUERY_INSERT,
                         $where = false)
    {
        $query = $this->buildManipSQL($table, $table_fields, $mode, $where);
        if (DB::isError($query)) {
            return $query;
        }
        return $this->prepare($query);
    }

    
    

    
    function autoExecute($table, $fields_values, $mode = DB_AUTOQUERY_INSERT,
                         $where = false)
    {
        $sth = $this->autoPrepare($table, array_keys($fields_values), $mode,
                                  $where);
        if (DB::isError($sth)) {
            return $sth;
        }
        $ret =& $this->execute($sth, array_values($fields_values));
        $this->freePrepared($sth);
        return $ret;

    }

    
    

    
    function buildManipSQL($table, $table_fields, $mode, $where = false)
    {
        if (count($table_fields) == 0) {
            return $this->raiseError(DB_ERROR_NEED_MORE_DATA);
        }
        $first = true;
        switch ($mode) {
            case DB_AUTOQUERY_INSERT:
                $values = '';
                $names = '';
                foreach ($table_fields as $value) {
                    if ($first) {
                        $first = false;
                    } else {
                        $names .= ',';
                        $values .= ',';
                    }
                    $names .= $value;
                    $values .= '?';
                }
                return "INSERT INTO $table ($names) VALUES ($values)";
            case DB_AUTOQUERY_UPDATE:
                $set = '';
                foreach ($table_fields as $value) {
                    if ($first) {
                        $first = false;
                    } else {
                        $set .= ',';
                    }
                    $set .= "$value = ?";
                }
                $sql = "UPDATE $table SET $set";
                if ($where) {
                    $sql .= " WHERE $where";
                }
                return $sql;
            default:
                return $this->raiseError(DB_ERROR_SYNTAX);
        }
    }

    
    

    
    function &execute($stmt, $data = array())
    {
        $realquery = $this->executeEmulateQuery($stmt, $data);
        if (DB::isError($realquery)) {
            return $realquery;
        }
        $result = $this->simpleQuery($realquery);

        if ($result === DB_OK || DB::isError($result)) {
            return $result;
        } else {
            $tmp = new DB_result($this, $result);
            return $tmp;
        }
    }

    
    

    
    function executeEmulateQuery($stmt, $data = array())
    {
        $stmt = (int)$stmt;
        $data = (array)$data;
        $this->last_parameters = $data;

        if (count($this->prepare_types[$stmt]) != count($data)) {
            $this->last_query = $this->prepared_queries[$stmt];
            return $this->raiseError(DB_ERROR_MISMATCH);
        }

        $realquery = $this->prepare_tokens[$stmt][0];

        $i = 0;
        foreach ($data as $value) {
            if ($this->prepare_types[$stmt][$i] == DB_PARAM_SCALAR) {
                $realquery .= $this->quoteSmart($value);
            } elseif ($this->prepare_types[$stmt][$i] == DB_PARAM_OPAQUE) {
                $fp = @fopen($value, 'rb');
                if (!$fp) {
                    return $this->raiseError(DB_ERROR_ACCESS_VIOLATION);
                }
                $realquery .= $this->quoteSmart(fread($fp, filesize($value)));
                fclose($fp);
            } else {
                $realquery .= $value;
            }

            $realquery .= $this->prepare_tokens[$stmt][++$i];
        }

        return $realquery;
    }

    
    

    
    function executeMultiple($stmt, $data)
    {
        foreach ($data as $value) {
            $res =& $this->execute($stmt, $value);
            if (DB::isError($res)) {
                return $res;
            }
        }
        return DB_OK;
    }

    
    

    
    function freePrepared($stmt, $free_resource = true)
    {
        $stmt = (int)$stmt;
        if (isset($this->prepare_tokens[$stmt])) {
            unset($this->prepare_tokens[$stmt]);
            unset($this->prepare_types[$stmt]);
            unset($this->prepared_queries[$stmt]);
            return true;
        }
        return false;
    }

    
    

    
    function modifyQuery($query)
    {
        return $query;
    }

    
    

    
    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        return $query;
    }

    
    

    
    function &query($query, $params = array())
    {
        if (sizeof($params) > 0) {
            $sth = $this->prepare($query);
            if (DB::isError($sth)) {
                return $sth;
            }
            $ret =& $this->execute($sth, $params);
            $this->freePrepared($sth, false);
            return $ret;
        } else {
            $this->last_parameters = array();
            $result = $this->simpleQuery($query);
            if ($result === DB_OK || DB::isError($result)) {
                return $result;
            } else {
                $tmp = new DB_result($this, $result);
                return $tmp;
            }
        }
    }

    
    

    
    function &limitQuery($query, $from, $count, $params = array())
    {
        $query = $this->modifyLimitQuery($query, $from, $count, $params);
        if (DB::isError($query)){
            return $query;
        }
        $result =& $this->query($query, $params);
        if (is_a($result, 'DB_result')) {
            $result->setOption('limit_from', $from);
            $result->setOption('limit_count', $count);
        }
        return $result;
    }

    
    

    
    function &getOne($query, $params = array())
    {
        $params = (array)$params;
        
        if (sizeof($params) > 0) {
            $sth = $this->prepare($query);
            if (DB::isError($sth)) {
                return $sth;
            }
            $res =& $this->execute($sth, $params);
            $this->freePrepared($sth);
        } else {
            $res =& $this->query($query);
        }

        if (DB::isError($res)) {
            return $res;
        }

        $err = $res->fetchInto($row, DB_FETCHMODE_ORDERED);
        $res->free();

        if ($err !== DB_OK) {
            return $err;
        }

        return $row[0];
    }

    
    

    
    function &getRow($query, $params = array(),
                     $fetchmode = DB_FETCHMODE_DEFAULT)
    {
        
        
        if (!is_array($params)) {
            if (is_array($fetchmode)) {
                if ($params === null) {
                    $tmp = DB_FETCHMODE_DEFAULT;
                } else {
                    $tmp = $params;
                }
                $params = $fetchmode;
                $fetchmode = $tmp;
            } elseif ($params !== null) {
                $fetchmode = $params;
                $params = array();
            }
        }
        
        if (sizeof($params) > 0) {
            $sth = $this->prepare($query);
            if (DB::isError($sth)) {
                return $sth;
            }
            $res =& $this->execute($sth, $params);
            $this->freePrepared($sth);
        } else {
            $res =& $this->query($query);
        }

        if (DB::isError($res)) {
            return $res;
        }

        $err = $res->fetchInto($row, $fetchmode);

        $res->free();

        if ($err !== DB_OK) {
            return $err;
        }

        return $row;
    }

    
    

    
    function &getCol($query, $col = 0, $params = array())
    {
        $params = (array)$params;
        if (sizeof($params) > 0) {
            $sth = $this->prepare($query);

            if (DB::isError($sth)) {
                return $sth;
            }

            $res =& $this->execute($sth, $params);
            $this->freePrepared($sth);
        } else {
            $res =& $this->query($query);
        }

        if (DB::isError($res)) {
            return $res;
        }

        $fetchmode = is_int($col) ? DB_FETCHMODE_ORDERED : DB_FETCHMODE_ASSOC;

        if (!is_array($row = $res->fetchRow($fetchmode))) {
            $ret = array();
        } else {
            if (!array_key_exists($col, $row)) {
                $ret =& $this->raiseError(DB_ERROR_NOSUCHFIELD);
            } else {
                $ret = array($row[$col]);
                while (is_array($row = $res->fetchRow($fetchmode))) {
                    $ret[] = $row[$col];
                }
            }
        }

        $res->free();

        if (DB::isError($row)) {
            $ret = $row;
        }

        return $ret;
    }

    
    

    
    function &getAssoc($query, $force_array = false, $params = array(),
                       $fetchmode = DB_FETCHMODE_DEFAULT, $group = false)
    {
        $params = (array)$params;
        if (sizeof($params) > 0) {
            $sth = $this->prepare($query);

            if (DB::isError($sth)) {
                return $sth;
            }

            $res =& $this->execute($sth, $params);
            $this->freePrepared($sth);
        } else {
            $res =& $this->query($query);
        }

        if (DB::isError($res)) {
            return $res;
        }
        if ($fetchmode == DB_FETCHMODE_DEFAULT) {
            $fetchmode = $this->fetchmode;
        }
        $cols = $res->numCols();

        if ($cols < 2) {
            $tmp =& $this->raiseError(DB_ERROR_TRUNCATED);
            return $tmp;
        }

        $results = array();

        if ($cols > 2 || $force_array) {
            
            
            if ($fetchmode == DB_FETCHMODE_ASSOC) {
                while (is_array($row = $res->fetchRow(DB_FETCHMODE_ASSOC))) {
                    reset($row);
                    $key = current($row);
                    unset($row[key($row)]);
                    if ($group) {
                        $results[$key][] = $row;
                    } else {
                        $results[$key] = $row;
                    }
                }
            } elseif ($fetchmode == DB_FETCHMODE_OBJECT) {
                while ($row = $res->fetchRow(DB_FETCHMODE_OBJECT)) {
                    $arr = get_object_vars($row);
                    $key = current($arr);
                    if ($group) {
                        $results[$key][] = $row;
                    } else {
                        $results[$key] = $row;
                    }
                }
            } else {
                while (is_array($row = $res->fetchRow(DB_FETCHMODE_ORDERED))) {
                    
                    
                    $key = array_shift($row);
                    if ($group) {
                        $results[$key][] = $row;
                    } else {
                        $results[$key] = $row;
                    }
                }
            }
            if (DB::isError($row)) {
                $results = $row;
            }
        } else {
            
            while (is_array($row = $res->fetchRow(DB_FETCHMODE_ORDERED))) {
                if ($group) {
                    $results[$row[0]][] = $row[1];
                } else {
                    $results[$row[0]] = $row[1];
                }
            }
            if (DB::isError($row)) {
                $results = $row;
            }
        }

        $res->free();

        return $results;
    }

    
    

    
    function &getAll($query, $params = array(),
                     $fetchmode = DB_FETCHMODE_DEFAULT)
    {
        
        
        if (!is_array($params)) {
            if (is_array($fetchmode)) {
                if ($params === null) {
                    $tmp = DB_FETCHMODE_DEFAULT;
                } else {
                    $tmp = $params;
                }
                $params = $fetchmode;
                $fetchmode = $tmp;
            } elseif ($params !== null) {
                $fetchmode = $params;
                $params = array();
            }
        }

        if (sizeof($params) > 0) {
            $sth = $this->prepare($query);

            if (DB::isError($sth)) {
                return $sth;
            }

            $res =& $this->execute($sth, $params);
            $this->freePrepared($sth);
        } else {
            $res =& $this->query($query);
        }

        if ($res === DB_OK || DB::isError($res)) {
            return $res;
        }

        $results = array();
        while (DB_OK === $res->fetchInto($row, $fetchmode)) {
            if ($fetchmode & DB_FETCHMODE_FLIPPED) {
                foreach ($row as $key => $val) {
                    $results[$key][] = $val;
                }
            } else {
                $results[] = $row;
            }
        }

        $res->free();

        if (DB::isError($row)) {
            $tmp =& $this->raiseError($row);
            return $tmp;
        }
        return $results;
    }

    
    

    
    function autoCommit($onoff = false)
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function commit()
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function rollback()
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function numRows($result)
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function affectedRows()
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function getSequenceName($sqn)
    {
        return sprintf($this->getOption('seqname_format'),
                       preg_replace('/[^a-z0-9_.]/i', '_', $sqn));
    }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function createSequence($seq_name)
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function &raiseError($code = DB_ERROR, $mode = null, $options = null,
                         $userinfo = null, $nativecode = null)
    {
        
        if (is_object($code)) {
            
            
            if ($mode === null && !empty($this->_default_error_mode)) {
                $mode    = $this->_default_error_mode;
                $options = $this->_default_error_options;
            }
            $tmp = PEAR::raiseError($code, null, $mode, $options,
                                    null, null, true);
            return $tmp;
        }

        if ($userinfo === null) {
            $userinfo = $this->last_query;
        }

        if ($nativecode) {
            $userinfo .= ' [nativecode=' . trim($nativecode) . ']';
        } else {
            $userinfo .= ' [DB Error: ' . DB::errorMessage($code) . ']';
        }

        $tmp = PEAR::raiseError(null, $code, $mode, $options, $userinfo,
                                'DB_Error', true);
        return $tmp;
    }

    
    

    
    function errorNative()
    {
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function errorCode($nativecode)
    {
        if (isset($this->errorcode_map[$nativecode])) {
            return $this->errorcode_map[$nativecode];
        }
        
        return DB_ERROR;
    }

    
    

    
    function errorMessage($dbcode)
    {
        return DB::errorMessage($this->errorcode_map[$dbcode]);
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function getTables()
    {
        return $this->getListOf('tables');
    }

    
    

    
    function getListOf($type)
    {
        $sql = $this->getSpecialQuery($type);
        if ($sql === null) {
            $this->last_query = '';
            return $this->raiseError(DB_ERROR_UNSUPPORTED);
        } elseif (is_int($sql) || DB::isError($sql)) {
            
            return $this->raiseError($sql);
        } elseif (is_array($sql)) {
            
            return $sql;
        }
        
        return $this->getCol($sql);
    }

    
    

    
    function getSpecialQuery($type)
    {
        return $this->raiseError(DB_ERROR_UNSUPPORTED);
    }

    
    

    
    function _rtrimArrayValues(&$array)
    {
        foreach ($array as $key => $value) {
            if (is_string($value)) {
                $array[$key] = rtrim($value);
            }
        }
    }

    
    

    
    function _convertNullArrayValuesToEmpty(&$array)
    {
        foreach ($array as $key => $value) {
            if (is_null($value)) {
                $array[$key] = '';
            }
        }
    }

    
}



?>
