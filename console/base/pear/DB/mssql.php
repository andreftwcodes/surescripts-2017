<?php






require_once 'DB/common.php';


class DB_mssql extends DB_common
{
    

    
    var $phptype = 'mssql';

    
    var $dbsyntax = 'mssql';

    
    var $features = array(
        'limit'         => 'emulate',
        'new_link'      => false,
        'numrows'       => true,
        'pconnect'      => true,
        'prepare'       => false,
        'ssl'           => false,
        'transactions'  => true,
    );

    
    
    var $errorcode_map = array(
        110   => DB_ERROR_VALUE_COUNT_ON_ROW,
        155   => DB_ERROR_NOSUCHFIELD,
        170   => DB_ERROR_SYNTAX,
        207   => DB_ERROR_NOSUCHFIELD,
        208   => DB_ERROR_NOSUCHTABLE,
        245   => DB_ERROR_INVALID_NUMBER,
        515   => DB_ERROR_CONSTRAINT_NOT_NULL,
        547   => DB_ERROR_CONSTRAINT,
        1913  => DB_ERROR_ALREADY_EXISTS,
        2627  => DB_ERROR_CONSTRAINT,
        2714  => DB_ERROR_ALREADY_EXISTS,
        3701  => DB_ERROR_NOSUCHTABLE,
        8134  => DB_ERROR_DIVZERO,
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $autocommit = true;

    
    var $transaction_opcount = 0;

    
    var $_db = null;


    
    

    
    function DB_mssql()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('mssql') && !PEAR::loadExtension('sybase')
            && !PEAR::loadExtension('sybase_ct'))
        {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        $params = array(
            $dsn['hostspec'] ? $dsn['hostspec'] : 'localhost',
            $dsn['username'] ? $dsn['username'] : null,
            $dsn['password'] ? $dsn['password'] : null,
        );
        if ($dsn['port']) {
            $params[0] .= ((substr(PHP_OS, 0, 3) == 'WIN') ? ',' : ':')
                        . $dsn['port'];
        }

        $connect_function = $persistent ? 'mssql_pconnect' : 'mssql_connect';

        $this->connection = @call_user_func_array($connect_function, $params);

        if (!$this->connection) {
            return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                     null, null, null,
                                     @mssql_get_last_message());
        }
        if ($dsn['database']) {
            if (!@mssql_select_db($dsn['database'], $this->connection)) {
                return $this->raiseError(DB_ERROR_NODBSELECTED,
                                         null, null, null,
                                         @mssql_get_last_message());
            }
            $this->_db = $dsn['database'];
        }
        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @mssql_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        if (!@mssql_select_db($this->_db, $this->connection)) {
            return $this->mssqlRaiseError(DB_ERROR_NODBSELECTED);
        }
        $query = $this->modifyQuery($query);
        if (!$this->autocommit && $ismanip) {
            if ($this->transaction_opcount == 0) {
                $result = @mssql_query('BEGIN TRAN', $this->connection);
                if (!$result) {
                    return $this->mssqlRaiseError();
                }
            }
            $this->transaction_opcount++;
        }
        $result = @mssql_query($query, $this->connection);
        if (!$result) {
            return $this->mssqlRaiseError();
        }
        
        
        return $ismanip ? DB_OK : $result;
    }

    
    

    
    function nextResult($result)
    {
        return @mssql_next_result($result);
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        if ($rownum !== null) {
            if (!@mssql_data_seek($result, $rownum)) {
                return null;
            }
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $arr = @mssql_fetch_array($result, MSSQL_ASSOC);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @mssql_fetch_row($result);
        }
        if (!$arr) {
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
        return @mssql_free_result($result);
    }

    
    

    
    function numCols($result)
    {
        $cols = @mssql_num_fields($result);
        if (!$cols) {
            return $this->mssqlRaiseError();
        }
        return $cols;
    }

    
    

    
    function numRows($result)
    {
        $rows = @mssql_num_rows($result);
        if ($rows === false) {
            return $this->mssqlRaiseError();
        }
        return $rows;
    }

    
    

    
    function autoCommit($onoff = false)
    {
        
        
        $this->autocommit = $onoff ? true : false;
        return DB_OK;
    }

    
    

    
    function commit()
    {
        if ($this->transaction_opcount > 0) {
            if (!@mssql_select_db($this->_db, $this->connection)) {
                return $this->mssqlRaiseError(DB_ERROR_NODBSELECTED);
            }
            $result = @mssql_query('COMMIT TRAN', $this->connection);
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->mssqlRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function rollback()
    {
        if ($this->transaction_opcount > 0) {
            if (!@mssql_select_db($this->_db, $this->connection)) {
                return $this->mssqlRaiseError(DB_ERROR_NODBSELECTED);
            }
            $result = @mssql_query('ROLLBACK TRAN', $this->connection);
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->mssqlRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function affectedRows()
    {
        if (DB::isManip($this->last_query)) {
            $res = @mssql_query('select @@rowcount', $this->connection);
            if (!$res) {
                return $this->mssqlRaiseError();
            }
            $ar = @mssql_fetch_row($res);
            if (!$ar) {
                $result = 0;
            } else {
                @mssql_free_result($res);
                $result = $ar[0];
            }
        } else {
            $result = 0;
        }
        return $result;
    }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);
        if (!@mssql_select_db($this->_db, $this->connection)) {
            return $this->mssqlRaiseError(DB_ERROR_NODBSELECTED);
        }
        $repeat = 0;
        do {
            $this->pushErrorHandling(PEAR_ERROR_RETURN);
            $result = $this->query("INSERT INTO $seqname (vapor) VALUES (0)");
            $this->popErrorHandling();
            if ($ondemand && DB::isError($result) &&
                ($result->getCode() == DB_ERROR || $result->getCode() == DB_ERROR_NOSUCHTABLE))
            {
                $repeat = 1;
                $result = $this->createSequence($seq_name);
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
            } elseif (!DB::isError($result)) {
                $result =& $this->query("SELECT @@IDENTITY FROM $seqname");
                $repeat = 0;
            } else {
                $repeat = false;
            }
        } while ($repeat);
        if (DB::isError($result)) {
            return $this->raiseError($result);
        }
        $result = $result->fetchRow(DB_FETCHMODE_ORDERED);
        return $result[0];
    }

    
    function createSequence($seq_name)
    {
        return $this->query('CREATE TABLE '
                            . $this->getSequenceName($seq_name)
                            . ' ([id] [int] IDENTITY (1, 1) NOT NULL,'
                            . ' [vapor] [int] NULL)');
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DROP TABLE ' . $this->getSequenceName($seq_name));
    }

    
    

    
    function quoteIdentifier($str)
    {
        return '[' . str_replace(']', ']]', $str) . ']';
    }

    
    

    
    function mssqlRaiseError($code = null)
    {
        $message = @mssql_get_last_message();
        if (!$code) {
            $code = $this->errorNative();
        }
        return $this->raiseError($this->errorCode($code, $message),
                                 null, null, null, "$code - $message");
    }

    
    

    
    function errorNative()
    {
        $res = @mssql_query('select @@ERROR as ErrorCode', $this->connection);
        if (!$res) {
            return DB_ERROR;
        }
        $row = @mssql_fetch_row($res);
        return $row[0];
    }

    
    

    
    function errorCode($nativecode = null, $msg = '')
    {
        if (!$nativecode) {
            $nativecode = $this->errorNative();
        }
        if (isset($this->errorcode_map[$nativecode])) {
            if ($nativecode == 3701
                && preg_match('/Cannot drop the index/i', $msg))
            {
                return DB_ERROR_NOT_FOUND;
            }
            return $this->errorcode_map[$nativecode];
        } else {
            return DB_ERROR;
        }
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        if (is_string($result)) {
            
            if (!@mssql_select_db($this->_db, $this->connection)) {
                return $this->mssqlRaiseError(DB_ERROR_NODBSELECTED);
            }
            $id = @mssql_query("SELECT * FROM $result WHERE 1=0",
                               $this->connection);
            $got_string = true;
        } elseif (isset($result->result)) {
            
            $id = $result->result;
            $got_string = false;
        } else {
            
            $id = $result;
            $got_string = false;
        }

        if (!is_resource($id)) {
            return $this->mssqlRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @mssql_num_fields($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $res[$i] = array(
                'table' => $got_string ? $case_func($result) : '',
                'name'  => $case_func(@mssql_field_name($id, $i)),
                'type'  => @mssql_field_type($id, $i),
                'len'   => @mssql_field_length($id, $i),
                
                'flags' => $got_string
                           ? $this->_mssql_field_flags($result,
                                                       @mssql_field_name($id, $i))
                           : '',
            );
            if ($mode & DB_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & DB_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
        }

        
        if ($got_string) {
            @mssql_free_result($id);
        }
        return $res;
    }

    
    

    
    function _mssql_field_flags($table, $column)
    {
        static $tableName = null;
        static $flags = array();

        if ($table != $tableName) {

            $flags = array();
            $tableName = $table;

            
            $res = $this->getAll("EXEC SP_HELPINDEX[$table]", DB_FETCHMODE_ASSOC);

            foreach ($res as $val) {
                $keys = explode(', ', $val['index_keys']);

                if (sizeof($keys) > 1) {
                    foreach ($keys as $key) {
                        $this->_add_flag($flags[$key], 'multiple_key');
                    }
                }

                if (strpos($val['index_description'], 'primary key')) {
                    foreach ($keys as $key) {
                        $this->_add_flag($flags[$key], 'primary_key');
                    }
                } elseif (strpos($val['index_description'], 'unique')) {
                    foreach ($keys as $key) {
                        $this->_add_flag($flags[$key], 'unique_key');
                    }
                }
            }

            
            $res = $this->getAll("EXEC SP_COLUMNS[$table]", DB_FETCHMODE_ASSOC);

            foreach ($res as $val) {
                $val = array_change_key_case($val, CASE_LOWER);
                if ($val['nullable'] == '0') {
                    $this->_add_flag($flags[$val['column_name']], 'not_null');
                }
                if (strpos($val['type_name'], 'identity')) {
                    $this->_add_flag($flags[$val['column_name']], 'auto_increment');
                }
                if (strpos($val['type_name'], 'timestamp')) {
                    $this->_add_flag($flags[$val['column_name']], 'timestamp');
                }
            }
        }

        if (array_key_exists($column, $flags)) {
            return(implode(' ', $flags[$column]));
        }
        return '';
    }

    
    

    
    function _add_flag(&$array, $value)
    {
        if (!is_array($array)) {
            $array = array($value);
        } elseif (!in_array($value, $array)) {
            array_push($array, $value);
        }
    }

    
    

    
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'tables':
                return "SELECT name FROM sysobjects WHERE type = 'U'"
                       . ' ORDER BY name';
            case 'views':
                return "SELECT name FROM sysobjects WHERE type = 'V'";
            default:
                return null;
        }
    }

    
}



?>
