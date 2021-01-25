<?php






require_once 'DB/common.php';


class DB_ibase extends DB_common
{
    

    
    var $phptype = 'ibase';

    
    var $dbsyntax = 'ibase';

    
    var $features = array(
        'limit'         => false,
        'new_link'      => false,
        'numrows'       => 'emulate',
        'pconnect'      => true,
        'prepare'       => true,
        'ssl'           => false,
        'transactions'  => true,
    );

    
    var $errorcode_map = array(
        -104 => DB_ERROR_SYNTAX,
        -150 => DB_ERROR_ACCESS_VIOLATION,
        -151 => DB_ERROR_ACCESS_VIOLATION,
        -155 => DB_ERROR_NOSUCHTABLE,
        -157 => DB_ERROR_NOSUCHFIELD,
        -158 => DB_ERROR_VALUE_COUNT_ON_ROW,
        -170 => DB_ERROR_MISMATCH,
        -171 => DB_ERROR_MISMATCH,
        -172 => DB_ERROR_INVALID,
        
        -205 => DB_ERROR_NOSUCHFIELD,
        -206 => DB_ERROR_NOSUCHFIELD,
        -208 => DB_ERROR_INVALID,
        -219 => DB_ERROR_NOSUCHTABLE,
        -297 => DB_ERROR_CONSTRAINT,
        -303 => DB_ERROR_INVALID,
        -413 => DB_ERROR_INVALID_NUMBER,
        -530 => DB_ERROR_CONSTRAINT,
        -551 => DB_ERROR_ACCESS_VIOLATION,
        -552 => DB_ERROR_ACCESS_VIOLATION,
        
        -625 => DB_ERROR_CONSTRAINT_NOT_NULL,
        -803 => DB_ERROR_CONSTRAINT,
        -804 => DB_ERROR_VALUE_COUNT_ON_ROW,
        -904 => DB_ERROR_CONNECT_FAILED,
        -922 => DB_ERROR_NOSUCHDB,
        -923 => DB_ERROR_CONNECT_FAILED,
        -924 => DB_ERROR_CONNECT_FAILED
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $affected = 0;

    
    var $autocommit = true;

    
    var $last_stmt;

    
    var $manip_query = array();


    
    

    
    function DB_ibase()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('interbase')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }
        if ($this->dbsyntax == 'firebird') {
            $this->features['limit'] = 'alter';
        }

        $params = array(
            $dsn['hostspec']
                    ? ($dsn['hostspec'] . ':' . $dsn['database'])
                    : $dsn['database'],
            $dsn['username'] ? $dsn['username'] : null,
            $dsn['password'] ? $dsn['password'] : null,
            isset($dsn['charset']) ? $dsn['charset'] : null,
            isset($dsn['buffers']) ? $dsn['buffers'] : null,
            isset($dsn['dialect']) ? $dsn['dialect'] : null,
            isset($dsn['role'])    ? $dsn['role'] : null,
        );

        $connect_function = $persistent ? 'ibase_pconnect' : 'ibase_connect';

        $this->connection = @call_user_func_array($connect_function, $params);
        if (!$this->connection) {
            return $this->ibaseRaiseError(DB_ERROR_CONNECT_FAILED);
        }
        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @ibase_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        $query = $this->modifyQuery($query);
        $result = @ibase_query($this->connection, $query);

        if (!$result) {
            return $this->ibaseRaiseError();
        }
        if ($this->autocommit && $ismanip) {
            @ibase_commit($this->connection);
        }
        if ($ismanip) {
            $this->affected = $result;
            return DB_OK;
        } else {
            $this->affected = 0;
            return $result;
        }
    }

    
    

    
    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        if ($this->dsn['dbsyntax'] == 'firebird') {
            $query = preg_replace('/^([\s(])*SELECT/i',
                                  "SELECT FIRST $count SKIP $from", $query);
        }
        return $query;
    }

    
    

    
    function nextResult($result)
    {
        return false;
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        if ($rownum !== null) {
            return $this->ibaseRaiseError(DB_ERROR_NOT_CAPABLE);
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            if (function_exists('ibase_fetch_assoc')) {
                $arr = @ibase_fetch_assoc($result);
            } else {
                $arr = get_object_vars(ibase_fetch_object($result));
            }
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @ibase_fetch_row($result);
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
        return @ibase_free_result($result);
    }

    
    

    function freeQuery($query)
    {
        @ibase_free_query($query);
        return true;
    }

    
    

    
    function affectedRows()
    {
        if (is_integer($this->affected)) {
            return $this->affected;
        }
        return $this->ibaseRaiseError(DB_ERROR_NOT_CAPABLE);
    }

    
    

    
    function numCols($result)
    {
        $cols = @ibase_num_fields($result);
        if (!$cols) {
            return $this->ibaseRaiseError();
        }
        return $cols;
    }

    
    

    
    function prepare($query)
    {
        $tokens   = preg_split('/((?<!\\\)[&?!])/', $query, -1,
                               PREG_SPLIT_DELIM_CAPTURE);
        $token    = 0;
        $types    = array();
        $newquery = '';

        foreach ($tokens as $key => $val) {
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
                    $tokens[$key] = preg_replace('/\\\([&?!])/', "\\1", $val);
                    $newquery .= $tokens[$key] . '?';
            }
        }

        $newquery = substr($newquery, 0, -1);
        $this->last_query = $query;
        $newquery = $this->modifyQuery($newquery);
        $stmt = @ibase_prepare($this->connection, $newquery);
        $this->prepare_types[(int)$stmt] = $types;
        $this->manip_query[(int)$stmt]   = DB::isManip($query);
        return $stmt;
    }

    
    

    
    function &execute($stmt, $data = array())
    {
        $data = (array)$data;
        $this->last_parameters = $data;

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
            $i++;
        }

        array_unshift($data, $stmt);

        $res = call_user_func_array('ibase_execute', $data);
        if (!$res) {
            $tmp =& $this->ibaseRaiseError();
            return $tmp;
        }
        
        $this->last_stmt = $stmt;
        if ($this->manip_query[(int)$stmt]) {
            $tmp = DB_OK;
        } else {
            $tmp =& new DB_result($this, $res);
        }
        return $tmp;
    }

    
    function freePrepared($stmt, $free_resource = true)
    {
        if (!is_resource($stmt)) {
            return false;
        }
        if ($free_resource) {
            @ibase_free_query($stmt);
        }
        unset($this->prepare_tokens[(int)$stmt]);
        unset($this->prepare_types[(int)$stmt]);
        unset($this->manip_query[(int)$stmt]);
        return true;
    }

    
    

    
    function autoCommit($onoff = false)
    {
        $this->autocommit = $onoff ? 1 : 0;
        return DB_OK;
    }

    
    

    
    function commit()
    {
        return @ibase_commit($this->connection);
    }

    
    

    
    function rollback()
    {
        return @ibase_rollback($this->connection);
    }

    
    

    function transactionInit($trans_args = 0)
    {
        return $trans_args
                ? @ibase_trans($trans_args, $this->connection)
                : @ibase_trans();
    }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $sqn = strtoupper($this->getSequenceName($seq_name));
        $repeat = 0;
        do {
            $this->pushErrorHandling(PEAR_ERROR_RETURN);
            $result =& $this->query("SELECT GEN_ID(${sqn}, 1) "
                                   . 'FROM RDB$GENERATORS '
                                   . "WHERE RDB\$GENERATOR_NAME='${sqn}'");
            $this->popErrorHandling();
            if ($ondemand && DB::isError($result)) {
                $repeat = 1;
                $result = $this->createSequence($seq_name);
                if (DB::isError($result)) {
                    return $result;
                }
            } else {
                $repeat = 0;
            }
        } while ($repeat);
        if (DB::isError($result)) {
            return $this->raiseError($result);
        }
        $arr = $result->fetchRow(DB_FETCHMODE_ORDERED);
        $result->free();
        return $arr[0];
    }

    
    

    
    function createSequence($seq_name)
    {
        $sqn = strtoupper($this->getSequenceName($seq_name));
        $this->pushErrorHandling(PEAR_ERROR_RETURN);
        $result = $this->query("CREATE GENERATOR ${sqn}");
        $this->popErrorHandling();

        return $result;
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DELETE FROM RDB$GENERATORS '
                            . "WHERE RDB\$GENERATOR_NAME='"
                            . strtoupper($this->getSequenceName($seq_name))
                            . "'");
    }

    
    

    
    function _ibaseFieldFlags($field_name, $table_name)
    {
        $sql = 'SELECT R.RDB$CONSTRAINT_TYPE CTYPE'
               .' FROM RDB$INDEX_SEGMENTS I'
               .'  JOIN RDB$RELATION_CONSTRAINTS R ON I.RDB$INDEX_NAME=R.RDB$INDEX_NAME'
               .' WHERE I.RDB$FIELD_NAME=\'' . $field_name . '\''
               .'  AND UPPER(R.RDB$RELATION_NAME)=\'' . strtoupper($table_name) . '\'';

        $result = @ibase_query($this->connection, $sql);
        if (!$result) {
            return $this->ibaseRaiseError();
        }

        $flags = '';
        if ($obj = @ibase_fetch_object($result)) {
            @ibase_free_result($result);
            if (isset($obj->CTYPE)  && trim($obj->CTYPE) == 'PRIMARY KEY') {
                $flags .= 'primary_key ';
            }
            if (isset($obj->CTYPE)  && trim($obj->CTYPE) == 'UNIQUE') {
                $flags .= 'unique_key ';
            }
        }

        $sql = 'SELECT R.RDB$NULL_FLAG AS NFLAG,'
               .'  R.RDB$DEFAULT_SOURCE AS DSOURCE,'
               .'  F.RDB$FIELD_TYPE AS FTYPE,'
               .'  F.RDB$COMPUTED_SOURCE AS CSOURCE'
               .' FROM RDB$RELATION_FIELDS R '
               .'  JOIN RDB$FIELDS F ON R.RDB$FIELD_SOURCE=F.RDB$FIELD_NAME'
               .' WHERE UPPER(R.RDB$RELATION_NAME)=\'' . strtoupper($table_name) . '\''
               .'  AND R.RDB$FIELD_NAME=\'' . $field_name . '\'';

        $result = @ibase_query($this->connection, $sql);
        if (!$result) {
            return $this->ibaseRaiseError();
        }
        if ($obj = @ibase_fetch_object($result)) {
            @ibase_free_result($result);
            if (isset($obj->NFLAG)) {
                $flags .= 'not_null ';
            }
            if (isset($obj->DSOURCE)) {
                $flags .= 'default ';
            }
            if (isset($obj->CSOURCE)) {
                $flags .= 'computed ';
            }
            if (isset($obj->FTYPE)  && $obj->FTYPE == 261) {
                $flags .= 'blob ';
            }
        }

        return trim($flags);
    }

    
    

    
    function &ibaseRaiseError($errno = null)
    {
        if ($errno === null) {
            $errno = $this->errorCode($this->errorNative());
        }
        $tmp =& $this->raiseError($errno, null, null, null, @ibase_errmsg());
        return $tmp;
    }

    
    

    
    function errorNative()
    {
        if (function_exists('ibase_errcode')) {
            return @ibase_errcode();
        }
        if (preg_match('/^Dynamic SQL Error SQL error code = ([0-9-]+)/i',
                       @ibase_errmsg(), $m)) {
            return (int)$m[1];
        }
        return null;
    }

    
    

    
    function errorCode($nativecode = null)
    {
        if (isset($this->errorcode_map[$nativecode])) {
            return $this->errorcode_map[$nativecode];
        }

        static $error_regexps;
        if (!isset($error_regexps)) {
            $error_regexps = array(
                '/generator .* is not defined/'
                    => DB_ERROR_SYNTAX,  
                '/table.*(not exist|not found|unknown)/i'
                    => DB_ERROR_NOSUCHTABLE,
                '/table .* already exists/i'
                    => DB_ERROR_ALREADY_EXISTS,
                '/unsuccessful metadata update .* failed attempt to store duplicate value/i'
                    => DB_ERROR_ALREADY_EXISTS,
                '/unsuccessful metadata update .* not found/i'
                    => DB_ERROR_NOT_FOUND,
                '/validation error for column .* value "\*\*\* null/i'
                    => DB_ERROR_CONSTRAINT_NOT_NULL,
                '/violation of [\w ]+ constraint/i'
                    => DB_ERROR_CONSTRAINT,
                '/conversion error from string/i'
                    => DB_ERROR_INVALID_NUMBER,
                '/no permission for/i'
                    => DB_ERROR_ACCESS_VIOLATION,
                '/arithmetic exception, numeric overflow, or string truncation/i'
                    => DB_ERROR_INVALID,
            );
        }

        $errormsg = @ibase_errmsg();
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
            
            $id = @ibase_query($this->connection,
                               "SELECT * FROM $result WHERE 1=0");
            $got_string = true;
        } elseif (isset($result->result)) {
            
            $id = $result->result;
            $got_string = false;
        } else {
            
            $id = $result;
            $got_string = false;
        }

        if (!is_resource($id)) {
            return $this->ibaseRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @ibase_num_fields($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $info = @ibase_field_info($id, $i);
            $res[$i] = array(
                'table' => $got_string ? $case_func($result) : '',
                'name'  => $case_func($info['name']),
                'type'  => $info['type'],
                'len'   => $info['length'],
                'flags' => ($got_string)
                            ? $this->_ibaseFieldFlags($info['name'], $result)
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
            @ibase_free_result($id);
        }
        return $res;
    }

    
    

    
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'tables':
                return 'SELECT DISTINCT R.RDB$RELATION_NAME FROM '
                       . 'RDB$RELATION_FIELDS R WHERE R.RDB$SYSTEM_FLAG=0';
            case 'views':
                return 'SELECT DISTINCT RDB$VIEW_NAME from RDB$VIEW_RELATIONS';
            case 'users':
                return 'SELECT DISTINCT RDB$USER FROM RDB$USER_PRIVILEGES';
            default:
                return null;
        }
    }

    

}



?>
