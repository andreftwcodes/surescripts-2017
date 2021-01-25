<?php






require_once 'DB/common.php';


class DB_odbc extends DB_common
{
    

    
    var $phptype = 'odbc';

    
    var $dbsyntax = 'sql92';

    
    var $features = array(
        'limit'         => 'emulate',
        'new_link'      => false,
        'numrows'       => true,
        'pconnect'      => true,
        'prepare'       => false,
        'ssl'           => false,
        'transactions'  => false,
    );

    
    var $errorcode_map = array(
        '01004' => DB_ERROR_TRUNCATED,
        '07001' => DB_ERROR_MISMATCH,
        '21S01' => DB_ERROR_VALUE_COUNT_ON_ROW,
        '21S02' => DB_ERROR_MISMATCH,
        '22001' => DB_ERROR_INVALID,
        '22003' => DB_ERROR_INVALID_NUMBER,
        '22005' => DB_ERROR_INVALID_NUMBER,
        '22008' => DB_ERROR_INVALID_DATE,
        '22012' => DB_ERROR_DIVZERO,
        '23000' => DB_ERROR_CONSTRAINT,
        '23502' => DB_ERROR_CONSTRAINT_NOT_NULL,
        '23503' => DB_ERROR_CONSTRAINT,
        '23504' => DB_ERROR_CONSTRAINT,
        '23505' => DB_ERROR_CONSTRAINT,
        '24000' => DB_ERROR_INVALID,
        '34000' => DB_ERROR_INVALID,
        '37000' => DB_ERROR_SYNTAX,
        '42000' => DB_ERROR_SYNTAX,
        '42601' => DB_ERROR_SYNTAX,
        'IM001' => DB_ERROR_UNSUPPORTED,
        'S0000' => DB_ERROR_NOSUCHTABLE,
        'S0001' => DB_ERROR_ALREADY_EXISTS,
        'S0002' => DB_ERROR_NOSUCHTABLE,
        'S0011' => DB_ERROR_ALREADY_EXISTS,
        'S0012' => DB_ERROR_NOT_FOUND,
        'S0021' => DB_ERROR_ALREADY_EXISTS,
        'S0022' => DB_ERROR_NOSUCHFIELD,
        'S1009' => DB_ERROR_INVALID,
        'S1090' => DB_ERROR_INVALID,
        'S1C00' => DB_ERROR_NOT_CAPABLE,
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $affected = 0;


    
    

    
    function DB_odbc()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('odbc')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }
        switch ($this->dbsyntax) {
            case 'access':
            case 'db2':
            case 'solid':
                $this->features['transactions'] = true;
                break;
            case 'navision':
                $this->features['limit'] = false;
        }

        
        if ($dsn['database']) {
            $odbcdsn = $dsn['database'];
        } elseif ($dsn['hostspec']) {
            $odbcdsn = $dsn['hostspec'];
        } else {
            $odbcdsn = 'localhost';
        }

        $connect_function = $persistent ? 'odbc_pconnect' : 'odbc_connect';

        if (empty($dsn['cursor'])) {
            $this->connection = @$connect_function($odbcdsn, $dsn['username'],
                                                   $dsn['password']);
        } else {
            $this->connection = @$connect_function($odbcdsn, $dsn['username'],
                                                   $dsn['password'],
                                                   $dsn['cursor']);
        }

        if (!is_resource($this->connection)) {
            return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                     null, null, null,
                                     $this->errorNative());
        }
        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $err = @odbc_close($this->connection);
        $this->connection = null;
        return $err;
    }

    
    

    
    function simpleQuery($query)
    {
        $this->last_query = $query;
        $query = $this->modifyQuery($query);
        $result = @odbc_exec($this->connection, $query);
        if (!$result) {
            return $this->odbcRaiseError(); 
        }
        
        
        if (DB::isManip($query)) {
            $this->affected = $result; 
            return DB_OK;
        }
        $this->affected = 0;
        return $result;
    }

    
    

    
    function nextResult($result)
    {
        return @odbc_next_result($result);
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        $arr = array();
        if ($rownum !== null) {
            $rownum++; 
            if (version_compare(phpversion(), '4.2.0', 'ge')) {
                $cols = @odbc_fetch_into($result, $arr, $rownum);
            } else {
                $cols = @odbc_fetch_into($result, $rownum, $arr);
            }
        } else {
            $cols = @odbc_fetch_into($result, $arr);
        }
        if (!$cols) {
            return null;
        }
        if ($fetchmode !== DB_FETCHMODE_ORDERED) {
            for ($i = 0; $i < count($arr); $i++) {
                $colName = @odbc_field_name($result, $i+1);
                $a[$colName] = $arr[$i];
            }
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
                $a = array_change_key_case($a, CASE_LOWER);
            }
            $arr = $a;
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
        return @odbc_free_result($result);
    }

    
    

    
    function numCols($result)
    {
        $cols = @odbc_num_fields($result);
        if (!$cols) {
            return $this->odbcRaiseError();
        }
        return $cols;
    }

    
    

    
    function affectedRows()
    {
        if (empty($this->affected)) {  
            return 0;
        }
        $nrows = @odbc_num_rows($this->affected);
        if ($nrows == -1) {
            return $this->odbcRaiseError();
        }
        return $nrows;
    }

    
    

    
    function numRows($result)
    {
        $nrows = @odbc_num_rows($result);
        if ($nrows == -1) {
            return $this->odbcRaiseError(DB_ERROR_UNSUPPORTED);
        }
        if ($nrows === false) {
            return $this->odbcRaiseError();
        }
        return $nrows;
    }

    
    

    
    function quoteIdentifier($str)
    {
        switch ($this->dsn['dbsyntax']) {
            case 'access':
                return '[' . $str . ']';
            case 'mssql':
            case 'sybase':
                return '[' . str_replace(']', ']]', $str) . ']';
            case 'mysql':
            case 'mysqli':
                return '`' . $str . '`';
            default:
                return '"' . str_replace('"', '""', $str) . '"';
        }
    }

    
    

    
    function quote($str)
    {
        return $this->quoteSmart($str);
    }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);
        $repeat = 0;
        do {
            $this->pushErrorHandling(PEAR_ERROR_RETURN);
            $result = $this->query("update ${seqname} set id = id + 1");
            $this->popErrorHandling();
            if ($ondemand && DB::isError($result) &&
                $result->getCode() == DB_ERROR_NOSUCHTABLE) {
                $repeat = 1;
                $this->pushErrorHandling(PEAR_ERROR_RETURN);
                $result = $this->createSequence($seq_name);
                $this->popErrorHandling();
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
                $result = $this->query("insert into ${seqname} (id) values(0)");
            } else {
                $repeat = 0;
            }
        } while ($repeat);

        if (DB::isError($result)) {
            return $this->raiseError($result);
        }

        $result = $this->query("select id from ${seqname}");
        if (DB::isError($result)) {
            return $result;
        }

        $row = $result->fetchRow(DB_FETCHMODE_ORDERED);
        if (DB::isError($row || !$row)) {
            return $row;
        }

        return $row[0];
    }

    
    function createSequence($seq_name)
    {
        return $this->query('CREATE TABLE '
                            . $this->getSequenceName($seq_name)
                            . ' (id integer NOT NULL,'
                            . ' PRIMARY KEY(id))');
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DROP TABLE ' . $this->getSequenceName($seq_name));
    }

    
    

    
    function autoCommit($onoff = false)
    {
        if (!@odbc_autocommit($this->connection, $onoff)) {
            return $this->odbcRaiseError();
        }
        return DB_OK;
    }

    
    

    
    function commit()
    {
        if (!@odbc_commit($this->connection)) {
            return $this->odbcRaiseError();
        }
        return DB_OK;
    }

    
    

    
    function rollback()
    {
        if (!@odbc_rollback($this->connection)) {
            return $this->odbcRaiseError();
        }
        return DB_OK;
    }

    
    

    
    function odbcRaiseError($errno = null)
    {
        if ($errno === null) {
            switch ($this->dbsyntax) {
                case 'access':
                    if ($this->options['portability'] & DB_PORTABILITY_ERRORS) {
                        $this->errorcode_map['07001'] = DB_ERROR_NOSUCHFIELD;
                    } else {
                        
                        $this->errorcode_map['07001'] = DB_ERROR_MISMATCH;
                    }

                    $native_code = odbc_error($this->connection);

                    
                    if ($native_code == 'S1000') {
                        $errormsg = odbc_errormsg($this->connection);
                        static $error_regexps;
                        if (!isset($error_regexps)) {
                            $error_regexps = array(
                                '/includes related records.$/i'  => DB_ERROR_CONSTRAINT,
                                '/cannot contain a Null value/i' => DB_ERROR_CONSTRAINT_NOT_NULL,
                            );
                        }
                        foreach ($error_regexps as $regexp => $code) {
                            if (preg_match($regexp, $errormsg)) {
                                return $this->raiseError($code,
                                        null, null, null,
                                        $native_code . ' ' . $errormsg);
                            }
                        }
                        $errno = DB_ERROR;
                    } else {
                        $errno = $this->errorCode($native_code);
                    }
                    break;
                default:
                    $errno = $this->errorCode(odbc_error($this->connection));
            }
        }
        return $this->raiseError($errno, null, null, null,
                                 $this->errorNative());
    }

    
    

    
    function errorNative()
    {
        if (!is_resource($this->connection)) {
            return @odbc_error() . ' ' . @odbc_errormsg();
        }
        return @odbc_error($this->connection) . ' ' . @odbc_errormsg($this->connection);
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        if (is_string($result)) {
            
            $id = @odbc_exec($this->connection, "SELECT * FROM $result");
            if (!$id) {
                return $this->odbcRaiseError();
            }
            $got_string = true;
        } elseif (isset($result->result)) {
            
            $id = $result->result;
            $got_string = false;
        } else {
            
            $id = $result;
            $got_string = false;
        }

        if (!is_resource($id)) {
            return $this->odbcRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @odbc_num_fields($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $col = $i + 1;
            $res[$i] = array(
                'table' => $got_string ? $case_func($result) : '',
                'name'  => $case_func(@odbc_field_name($id, $col)),
                'type'  => @odbc_field_type($id, $col),
                'len'   => @odbc_field_len($id, $col),
                'flags' => '',
            );
            if ($mode & DB_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & DB_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
        }

        
        if ($got_string) {
            @odbc_free_result($id);
        }
        return $res;
    }

    
    

    
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'databases':
                if (!function_exists('odbc_data_source')) {
                    return null;
                }
                $res = @odbc_data_source($this->connection, SQL_FETCH_FIRST);
                if (is_array($res)) {
                    $out = array($res['server']);
                    while($res = @odbc_data_source($this->connection,
                                                   SQL_FETCH_NEXT))
                    {
                        $out[] = $res['server'];
                    }
                    return $out;
                } else {
                    return $this->odbcRaiseError();
                }
                break;
            case 'tables':
            case 'schema.tables':
                $keep = 'TABLE';
                break;
            case 'views':
                $keep = 'VIEW';
                break;
            default:
                return null;
        }

        
        $res  = @odbc_tables($this->connection);
        if (!$res) {
            return $this->odbcRaiseError();
        }
        $out = array();
        while ($row = odbc_fetch_array($res)) {
            if ($row['TABLE_TYPE'] != $keep) {
                continue;
            }
            if ($type == 'schema.tables') {
                $out[] = $row['TABLE_SCHEM'] . '.' . $row['TABLE_NAME'];
            } else {
                $out[] = $row['TABLE_NAME'];
            }
        }
        return $out;
    }

    

}



?>
