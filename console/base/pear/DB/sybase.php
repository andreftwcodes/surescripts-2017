<?php






require_once 'DB/common.php';


class DB_sybase extends DB_common
{
    

    
    var $phptype = 'sybase';

    
    var $dbsyntax = 'sybase';

    
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
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $autocommit = true;

    
    var $transaction_opcount = 0;

    
    var $_db = '';


    
    

    
    function DB_sybase()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('sybase') &&
            !PEAR::loadExtension('sybase_ct'))
        {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        $dsn['hostspec'] = $dsn['hostspec'] ? $dsn['hostspec'] : 'localhost';
        $dsn['password'] = !empty($dsn['password']) ? $dsn['password'] : false;
        $dsn['charset'] = isset($dsn['charset']) ? $dsn['charset'] : false;
        $dsn['appname'] = isset($dsn['appname']) ? $dsn['appname'] : false;

        $connect_function = $persistent ? 'sybase_pconnect' : 'sybase_connect';

        if ($dsn['username']) {
            $this->connection = @$connect_function($dsn['hostspec'],
                                                   $dsn['username'],
                                                   $dsn['password'],
                                                   $dsn['charset'],
                                                   $dsn['appname']);
        } else {
            return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                     null, null, null,
                                     'The DSN did not contain a username.');
        }

        if (!$this->connection) {
            return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                     null, null, null,
                                     @sybase_get_last_message());
        }

        if ($dsn['database']) {
            if (!@sybase_select_db($dsn['database'], $this->connection)) {
                return $this->raiseError(DB_ERROR_NODBSELECTED,
                                         null, null, null,
                                         @sybase_get_last_message());
            }
            $this->_db = $dsn['database'];
        }

        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @sybase_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        if (!@sybase_select_db($this->_db, $this->connection)) {
            return $this->sybaseRaiseError(DB_ERROR_NODBSELECTED);
        }
        $query = $this->modifyQuery($query);
        if (!$this->autocommit && $ismanip) {
            if ($this->transaction_opcount == 0) {
                $result = @sybase_query('BEGIN TRANSACTION', $this->connection);
                if (!$result) {
                    return $this->sybaseRaiseError();
                }
            }
            $this->transaction_opcount++;
        }
        $result = @sybase_query($query, $this->connection);
        if (!$result) {
            return $this->sybaseRaiseError();
        }
        if (is_resource($result)) {
            return $result;
        }
        
        
        return $ismanip ? DB_OK : $result;
    }

    
    

    
    function nextResult($result)
    {
        return false;
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        if ($rownum !== null) {
            if (!@sybase_data_seek($result, $rownum)) {
                return null;
            }
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            if (function_exists('sybase_fetch_assoc')) {
                $arr = @sybase_fetch_assoc($result);
            } else {
                if ($arr = @sybase_fetch_array($result)) {
                    foreach ($arr as $key => $value) {
                        if (is_int($key)) {
                            unset($arr[$key]);
                        }
                    }
                }
            }
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @sybase_fetch_row($result);
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
        return @sybase_free_result($result);
    }

    
    

    
    function numCols($result)
    {
        $cols = @sybase_num_fields($result);
        if (!$cols) {
            return $this->sybaseRaiseError();
        }
        return $cols;
    }

    
    

    
    function numRows($result)
    {
        $rows = @sybase_num_rows($result);
        if ($rows === false) {
            return $this->sybaseRaiseError();
        }
        return $rows;
    }

    
    

    
    function affectedRows()
    {
        if (DB::isManip($this->last_query)) {
            $result = @sybase_affected_rows($this->connection);
        } else {
            $result = 0;
        }
        return $result;
     }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);
        if (!@sybase_select_db($this->_db, $this->connection)) {
            return $this->sybaseRaiseError(DB_ERROR_NODBSELECTED);
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
                            . ' (id numeric(10, 0) IDENTITY NOT NULL,'
                            . ' vapor int NULL)');
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DROP TABLE ' . $this->getSequenceName($seq_name));
    }

    
    

    
    function autoCommit($onoff = false)
    {
        
        
        $this->autocommit = $onoff ? true : false;
        return DB_OK;
    }

    
    

    
    function commit()
    {
        if ($this->transaction_opcount > 0) {
            if (!@sybase_select_db($this->_db, $this->connection)) {
                return $this->sybaseRaiseError(DB_ERROR_NODBSELECTED);
            }
            $result = @sybase_query('COMMIT', $this->connection);
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->sybaseRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function rollback()
    {
        if ($this->transaction_opcount > 0) {
            if (!@sybase_select_db($this->_db, $this->connection)) {
                return $this->sybaseRaiseError(DB_ERROR_NODBSELECTED);
            }
            $result = @sybase_query('ROLLBACK', $this->connection);
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->sybaseRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function sybaseRaiseError($errno = null)
    {
        $native = $this->errorNative();
        if ($errno === null) {
            $errno = $this->errorCode($native);
        }
        return $this->raiseError($errno, null, null, null, $native);
    }

    
    

    
    function errorNative()
    {
        return @sybase_get_last_message();
    }

    
    

    
    function errorCode($errormsg)
    {
        static $error_regexps;
        if (!isset($error_regexps)) {
            $error_regexps = array(
                '/Incorrect syntax near/'
                    => DB_ERROR_SYNTAX,
                '/^Unclosed quote before the character string [\"\'].*[\"\']\./'
                    => DB_ERROR_SYNTAX,
                '/Implicit conversion (from datatype|of NUMERIC value)/i'
                    => DB_ERROR_INVALID_NUMBER,
                '/Cannot drop the table [\"\'].+[\"\'], because it doesn\'t exist in the system catalogs\./'
                    => DB_ERROR_NOSUCHTABLE,
                '/Only the owner of object [\"\'].+[\"\'] or a user with System Administrator \(SA\) role can run this command\./'
                    => DB_ERROR_ACCESS_VIOLATION,
                '/^.+ permission denied on object .+, database .+, owner .+/'
                    => DB_ERROR_ACCESS_VIOLATION,
                '/^.* permission denied, database .+, owner .+/'
                    => DB_ERROR_ACCESS_VIOLATION,
                '/[^.*] not found\./'
                    => DB_ERROR_NOSUCHTABLE,
                '/There is already an object named/'
                    => DB_ERROR_ALREADY_EXISTS,
                '/Invalid column name/'
                    => DB_ERROR_NOSUCHFIELD,
                '/does not allow null values/'
                    => DB_ERROR_CONSTRAINT_NOT_NULL,
                '/Command has been aborted/'
                    => DB_ERROR_CONSTRAINT,
                '/^Cannot drop the index .* because it doesn\'t exist/i'
                    => DB_ERROR_NOT_FOUND,
                '/^There is already an index/i'
                    => DB_ERROR_ALREADY_EXISTS,
                '/^There are fewer columns in the INSERT statement than values specified/i'
                    => DB_ERROR_VALUE_COUNT_ON_ROW,
            );
        }

        foreach ($error_regexps as $regexp => $code) {
            if (preg_match($regexp, $errormsg)) {
                return $code;
            }
        }
        return DB_ERROR;
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        if (is_string($result)) {
            
            if (!@sybase_select_db($this->_db, $this->connection)) {
                return $this->sybaseRaiseError(DB_ERROR_NODBSELECTED);
            }
            $id = @sybase_query("SELECT * FROM $result WHERE 1=0",
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
            return $this->sybaseRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @sybase_num_fields($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $f = @sybase_fetch_field($id, $i);
            
            $res[$i] = array(
                'table' => $got_string
                           ? $case_func($result)
                           : $case_func($f->column_source),
                'name'  => $case_func($f->name),
                'type'  => $f->type,
                'len'   => $f->max_length,
                'flags' => '',
            );
            if ($res[$i]['table']) {
                $res[$i]['flags'] = $this->_sybase_field_flags(
                        $res[$i]['table'], $res[$i]['name']);
            }
            if ($mode & DB_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & DB_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
        }

        
        if ($got_string) {
            @sybase_free_result($id);
        }
        return $res;
    }

    
    

    
    function _sybase_field_flags($table, $column)
    {
        static $tableName = null;
        static $flags = array();

        if ($table != $tableName) {
            $flags = array();
            $tableName = $table;

            
            $res = $this->getAll("sp_helpindex $table", DB_FETCHMODE_ASSOC);

            if (!isset($res[0]['index_description'])) {
                return '';
            }

            foreach ($res as $val) {
                $keys = explode(', ', trim($val['index_keys']));

                if (sizeof($keys) > 1) {
                    foreach ($keys as $key) {
                        $this->_add_flag($flags[$key], 'multiple_key');
                    }
                }

                if (strpos($val['index_description'], 'unique')) {
                    foreach ($keys as $key) {
                        $this->_add_flag($flags[$key], 'unique_key');
                    }
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
