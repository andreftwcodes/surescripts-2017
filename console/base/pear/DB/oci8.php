<?php






require_once 'DB/common.php';


class DB_oci8 extends DB_common
{
    

    
    var $phptype = 'oci8';

    
    var $dbsyntax = 'oci8';

    
    var $features = array(
        'limit'         => 'alter',
        'new_link'      => '5.0.0',
        'numrows'       => 'subquery',
        'pconnect'      => true,
        'prepare'       => true,
        'ssl'           => false,
        'transactions'  => true,
    );

    
    var $errorcode_map = array(
        1    => DB_ERROR_CONSTRAINT,
        900  => DB_ERROR_SYNTAX,
        904  => DB_ERROR_NOSUCHFIELD,
        913  => DB_ERROR_VALUE_COUNT_ON_ROW,
        921  => DB_ERROR_SYNTAX,
        923  => DB_ERROR_SYNTAX,
        942  => DB_ERROR_NOSUCHTABLE,
        955  => DB_ERROR_ALREADY_EXISTS,
        1400 => DB_ERROR_CONSTRAINT_NOT_NULL,
        1401 => DB_ERROR_INVALID,
        1407 => DB_ERROR_CONSTRAINT_NOT_NULL,
        1418 => DB_ERROR_NOT_FOUND,
        1476 => DB_ERROR_DIVZERO,
        1722 => DB_ERROR_INVALID_NUMBER,
        2289 => DB_ERROR_NOSUCHTABLE,
        2291 => DB_ERROR_CONSTRAINT,
        2292 => DB_ERROR_CONSTRAINT,
        2449 => DB_ERROR_CONSTRAINT,
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $autocommit = true;

    
    var $_data = array();

    
    var $last_stmt;

    
    var $manip_query = array();


    
    

    
    function DB_oci8()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('oci8')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        if (function_exists('oci_connect')) {
            if (isset($dsn['new_link'])
                && ($dsn['new_link'] == 'true' || $dsn['new_link'] === true))
            {
                $connect_function = 'oci_new_connect';
            } else {
                $connect_function = $persistent ? 'oci_pconnect'
                                    : 'oci_connect';
            }

            
            if (empty($dsn['database']) && !empty($dsn['hostspec'])) {
                $db = $dsn['hostspec'];
            } else {
                $db = $dsn['database'];
            }

            $char = empty($dsn['charset']) ? null : $dsn['charset'];
            $this->connection = @$connect_function($dsn['username'],
                                                   $dsn['password'],
                                                   $db,
                                                   $char);
            $error = OCIError();
            if (!empty($error) && $error['code'] == 12541) {
                
                $this->connection = @$connect_function($dsn['username'],
                                                       $dsn['password'],
                                                       null,
                                                       $char);
            }
        } else {
            $connect_function = $persistent ? 'OCIPLogon' : 'OCILogon';
            if ($dsn['hostspec']) {
                $this->connection = @$connect_function($dsn['username'],
                                                       $dsn['password'],
                                                       $dsn['hostspec']);
            } elseif ($dsn['username'] || $dsn['password']) {
                $this->connection = @$connect_function($dsn['username'],
                                                       $dsn['password']);
            }
        }

        if (!$this->connection) {
            $error = OCIError();
            $error = (is_array($error)) ? $error['message'] : null;
            return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                     null, null, null,
                                     $error);
        }
        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        if (function_exists('oci_close')) {
            $ret = @oci_close($this->connection);
        } else {
            $ret = @OCILogOff($this->connection);
        }
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $this->_data = array();
        $this->last_parameters = array();
        $this->last_query = $query;
        $query = $this->modifyQuery($query);
        $result = @OCIParse($this->connection, $query);
        if (!$result) {
            return $this->oci8RaiseError();
        }
        if ($this->autocommit) {
            $success = @OCIExecute($result,OCI_COMMIT_ON_SUCCESS);
        } else {
            $success = @OCIExecute($result,OCI_DEFAULT);
        }
        if (!$success) {
            return $this->oci8RaiseError($result);
        }
        $this->last_stmt = $result;
        if (DB::isManip($query)) {
            return DB_OK;
        } else {
            @ocisetprefetch($result, $this->options['result_buffering']);
            return $result;
        }
    }

    
    

    
    function nextResult($result)
    {
        return false;
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        if ($rownum !== null) {
            return $this->raiseError(DB_ERROR_NOT_CAPABLE);
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $moredata = @OCIFetchInto($result,$arr,OCI_ASSOC+OCI_RETURN_NULLS+OCI_RETURN_LOBS);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE &&
                $moredata)
            {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $moredata = OCIFetchInto($result,$arr,OCI_RETURN_NULLS+OCI_RETURN_LOBS);
        }
        if (!$moredata) {
            return null;
        }
        if ($this->options['portability'] & DB_PORTABILITY_RTRIM) {
            $this->_rtrimArrayValues($arr);
        }
        if ($this->options['portability'] & DB_PORTABILITY_NULL_TO_EMPTY) {
            $this->_convertNullArrayValuesToEmpty($arr);
        }
        return DB_OK;
    }

    
    

    
    function freeResult($result)
    {
        return @OCIFreeStatement($result);
    }

    
    function freePrepared($stmt, $free_resource = true)
    {
        if (!is_resource($stmt)) {
            return false;
        }
        if ($free_resource) {
            @ocifreestatement($stmt);
        }
        if (isset($this->prepare_types[(int)$stmt])) {
            unset($this->prepare_types[(int)$stmt]);
            unset($this->manip_query[(int)$stmt]);
        } else {
            return false;
        }
        return true;
    }

    
    

    
    function numRows($result)
    {
        
        if ($this->options['portability'] & DB_PORTABILITY_NUMROWS &&
            $result === $this->last_stmt)
        {
            $countquery = 'SELECT COUNT(*) FROM ('.$this->last_query.')';
            $save_query = $this->last_query;
            $save_stmt = $this->last_stmt;

            if (count($this->_data)) {
                $smt = $this->prepare('SELECT COUNT(*) FROM ('.$this->last_query.')');
                $count = $this->execute($smt, $this->_data);
            } else {
                $count =& $this->query($countquery);
            }

            if (DB::isError($count) ||
                DB::isError($row = $count->fetchRow(DB_FETCHMODE_ORDERED)))
            {
                $this->last_query = $save_query;
                $this->last_stmt = $save_stmt;
                return $this->raiseError(DB_ERROR_NOT_CAPABLE);
            }
            return $row[0];
        }
        return $this->raiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function numCols($result)
    {
        $cols = @OCINumCols($result);
        if (!$cols) {
            return $this->oci8RaiseError($result);
        }
        return $cols;
    }

    
    

    
    function prepare($query)
    {
        $tokens   = preg_split('/((?<!\\\)[&?!])/', $query, -1,
                               PREG_SPLIT_DELIM_CAPTURE);
        $binds    = count($tokens) - 1;
        $token    = 0;
        $types    = array();
        $newquery = '';

        foreach ($tokens as $key => $val) {
            switch ($val) {
                case '?':
                    $types[$token++] = DB_PARAM_SCALAR;
                    unset($tokens[$key]);
                    break;
                case '&':
                    $types[$token++] = DB_PARAM_OPAQUE;
                    unset($tokens[$key]);
                    break;
                case '!':
                    $types[$token++] = DB_PARAM_MISC;
                    unset($tokens[$key]);
                    break;
                default:
                    $tokens[$key] = preg_replace('/\\\([&?!])/', "\\1", $val);
                    if ($key != $binds) {
                        $newquery .= $tokens[$key] . ':bind' . $token;
                    } else {
                        $newquery .= $tokens[$key];
                    }
            }
        }

        $this->last_query = $query;
        $newquery = $this->modifyQuery($newquery);
        if (!$stmt = @OCIParse($this->connection, $newquery)) {
            return $this->oci8RaiseError();
        }
        $this->prepare_types[(int)$stmt] = $types;
        $this->manip_query[(int)$stmt] = DB::isManip($query);
        return $stmt;
    }

    
    

    
    function &execute($stmt, $data = array())
    {
        $data = (array)$data;
        $this->last_parameters = $data;
        $this->_data = $data;

        $types =& $this->prepare_types[(int)$stmt];
        if (count($types) != count($data)) {
            $tmp =& $this->raiseError(DB_ERROR_MISMATCH);
            return $tmp;
        }

        $i = 0;
        foreach ($data as $key => $value) {
            if ($types[$i] == DB_PARAM_MISC) {
                
                $data[$key] = preg_replace("/^'(.*)'$/", "\\1", $data[$key]);
                $data[$key] = str_replace("''", "'", $data[$key]);
            } elseif ($types[$i] == DB_PARAM_OPAQUE) {
                $fp = @fopen($data[$key], 'rb');
                if (!$fp) {
                    $tmp =& $this->raiseError(DB_ERROR_ACCESS_VIOLATION);
                    return $tmp;
                }
                $data[$key] = fread($fp, filesize($data[$key]));
                fclose($fp);
            }
            if (!@OCIBindByName($stmt, ':bind' . $i, $data[$key], -1)) {
                $tmp = $this->oci8RaiseError($stmt);
                return $tmp;
            }
            $i++;
        }
        if ($this->autocommit) {
            $success = @OCIExecute($stmt, OCI_COMMIT_ON_SUCCESS);
        } else {
            $success = @OCIExecute($stmt, OCI_DEFAULT);
        }
        if (!$success) {
            $tmp = $this->oci8RaiseError($stmt);
            return $tmp;
        }
        $this->last_stmt = $stmt;
        if ($this->manip_query[(int)$stmt]) {
            $tmp = DB_OK;
        } else {
            @ocisetprefetch($stmt, $this->options['result_buffering']);
            $tmp =& new DB_result($this, $stmt);
        }
        return $tmp;
    }

    
    

    
    function autoCommit($onoff = false)
    {
        $this->autocommit = (bool)$onoff;;
        return DB_OK;
    }

    
    

    
    function commit()
    {
        $result = @OCICommit($this->connection);
        if (!$result) {
            return $this->oci8RaiseError();
        }
        return DB_OK;
    }

    
    

    
    function rollback()
    {
        $result = @OCIRollback($this->connection);
        if (!$result) {
            return $this->oci8RaiseError();
        }
        return DB_OK;
    }

    
    

    
    function affectedRows()
    {
        if ($this->last_stmt === false) {
            return $this->oci8RaiseError();
        }
        $result = @OCIRowCount($this->last_stmt);
        if ($result === false) {
            return $this->oci8RaiseError($this->last_stmt);
        }
        return $result;
    }

    
    

    
    function modifyQuery($query)
    {
        if (preg_match('/^\s*SELECT/i', $query) &&
            !preg_match('/\sFROM\s/i', $query)) {
            $query .= ' FROM dual';
        }
        return $query;
    }

    
    

    
    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        
        

        if (count($params)) {
            $result = $this->prepare("SELECT * FROM ($query) "
                                     . 'WHERE NULL = NULL');
            $tmp =& $this->execute($result, $params);
        } else {
            $q_fields = "SELECT * FROM ($query) WHERE NULL = NULL";

            if (!$result = @OCIParse($this->connection, $q_fields)) {
                $this->last_query = $q_fields;
                return $this->oci8RaiseError();
            }
            if (!@OCIExecute($result, OCI_DEFAULT)) {
                $this->last_query = $q_fields;
                return $this->oci8RaiseError($result);
            }
        }

        $ncols = OCINumCols($result);
        $cols  = array();
        for ( $i = 1; $i <= $ncols; $i++ ) {
            $cols[] = '"' . OCIColumnName($result, $i) . '"';
        }
        $fields = implode(', ', $cols);
        
        
        
        
        
        

        
        
        
        $query = "SELECT $fields FROM".
                 "  (SELECT rownum as linenum, $fields FROM".
                 "      ($query)".
                 '  WHERE rownum <= '. ($from + $count) .
                 ') WHERE linenum >= ' . ++$from;
        return $query;
    }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);
        $repeat = 0;
        do {
            $this->expectError(DB_ERROR_NOSUCHTABLE);
            $result =& $this->query("SELECT ${seqname}.nextval FROM dual");
            $this->popExpect();
            if ($ondemand && DB::isError($result) &&
                $result->getCode() == DB_ERROR_NOSUCHTABLE) {
                $repeat = 1;
                $result = $this->createSequence($seq_name);
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
            } else {
                $repeat = 0;
            }
        } while ($repeat);
        if (DB::isError($result)) {
            return $this->raiseError($result);
        }
        $arr = $result->fetchRow(DB_FETCHMODE_ORDERED);
        return $arr[0];
    }

    
    function createSequence($seq_name)
    {
        return $this->query('CREATE SEQUENCE '
                            . $this->getSequenceName($seq_name));
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DROP SEQUENCE '
                            . $this->getSequenceName($seq_name));
    }

    
    

    
    function oci8RaiseError($errno = null)
    {
        if ($errno === null) {
            $error = @OCIError($this->connection);
            return $this->raiseError($this->errorCode($error['code']),
                                     null, null, null, $error['message']);
        } elseif (is_resource($errno)) {
            $error = @OCIError($errno);
            return $this->raiseError($this->errorCode($error['code']),
                                     null, null, null, $error['message']);
        }
        return $this->raiseError($this->errorCode($errno));
    }

    
    

    
    function errorNative()
    {
        if (is_resource($this->last_stmt)) {
            $error = @OCIError($this->last_stmt);
        } else {
            $error = @OCIError($this->connection);
        }
        if (is_array($error)) {
            return $error['code'];
        }
        return false;
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $res = array();

        if (is_string($result)) {
            
            $result = strtoupper($result);
            $q_fields = 'SELECT column_name, data_type, data_length, '
                        . 'nullable '
                        . 'FROM user_tab_columns '
                        . "WHERE table_name='$result' ORDER BY column_id";

            $this->last_query = $q_fields;

            if (!$stmt = @OCIParse($this->connection, $q_fields)) {
                return $this->oci8RaiseError(DB_ERROR_NEED_MORE_DATA);
            }
            if (!@OCIExecute($stmt, OCI_DEFAULT)) {
                return $this->oci8RaiseError($stmt);
            }

            $i = 0;
            while (@OCIFetch($stmt)) {
                $res[$i] = array(
                    'table' => $case_func($result),
                    'name'  => $case_func(@OCIResult($stmt, 1)),
                    'type'  => @OCIResult($stmt, 2),
                    'len'   => @OCIResult($stmt, 3),
                    'flags' => (@OCIResult($stmt, 4) == 'N') ? 'not_null' : '',
                );
                if ($mode & DB_TABLEINFO_ORDER) {
                    $res['order'][$res[$i]['name']] = $i;
                }
                if ($mode & DB_TABLEINFO_ORDERTABLE) {
                    $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
                }
                $i++;
            }

            if ($mode) {
                $res['num_fields'] = $i;
            }
            @OCIFreeStatement($stmt);

        } else {
            if (isset($result->result)) {
                
                $result = $result->result;
            }

            $res = array();

            if ($result === $this->last_stmt) {
                $count = @OCINumCols($result);
                if ($mode) {
                    $res['num_fields'] = $count;
                }
                for ($i = 0; $i < $count; $i++) {
                    $res[$i] = array(
                        'table' => '',
                        'name'  => $case_func(@OCIColumnName($result, $i+1)),
                        'type'  => @OCIColumnType($result, $i+1),
                        'len'   => @OCIColumnSize($result, $i+1),
                        'flags' => '',
                    );
                    if ($mode & DB_TABLEINFO_ORDER) {
                        $res['order'][$res[$i]['name']] = $i;
                    }
                    if ($mode & DB_TABLEINFO_ORDERTABLE) {
                        $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
                    }
                }
            } else {
                return $this->raiseError(DB_ERROR_NOT_CAPABLE);
            }
        }
        return $res;
    }

    
    

    
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'tables':
                return 'SELECT table_name FROM user_tables';
            case 'synonyms':
                return 'SELECT synonym_name FROM user_synonyms';
            default:
                return null;
        }
    }

    

}



?>
