<?php






require_once 'DB/common.php';


class DB_pgsql extends DB_common
{
    

    
    var $phptype = 'pgsql';

    
    var $dbsyntax = 'pgsql';

    
    var $features = array(
        'limit'         => 'alter',
        'new_link'      => '4.3.0',
        'numrows'       => true,
        'pconnect'      => true,
        'prepare'       => false,
        'ssl'           => true,
        'transactions'  => true,
    );

    
    var $errorcode_map = array(
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $autocommit = true;

    
    var $transaction_opcount = 0;

    
    var $affected = 0;

    
    var $row = array();

    
    var $_num_rows = array();


    
    

    
    function DB_pgsql()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('pgsql')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        $protocol = $dsn['protocol'] ? $dsn['protocol'] : 'tcp';

        $params = array('');
        if ($protocol == 'tcp') {
            if ($dsn['hostspec']) {
                $params[0] .= 'host=' . $dsn['hostspec'];
            }
            if ($dsn['port']) {
                $params[0] .= ' port=' . $dsn['port'];
            }
        } elseif ($protocol == 'unix') {
            
            if ($dsn['socket']) {
                $params[0] .= 'host=' . $dsn['socket'];
            }
            if ($dsn['port']) {
                $params[0] .= ' port=' . $dsn['port'];
            }
        }
        if ($dsn['database']) {
            $params[0] .= ' dbname=\'' . addslashes($dsn['database']) . '\'';
        }
        if ($dsn['username']) {
            $params[0] .= ' user=\'' . addslashes($dsn['username']) . '\'';
        }
        if ($dsn['password']) {
            $params[0] .= ' password=\'' . addslashes($dsn['password']) . '\'';
        }
        if (!empty($dsn['options'])) {
            $params[0] .= ' options=' . $dsn['options'];
        }
        if (!empty($dsn['tty'])) {
            $params[0] .= ' tty=' . $dsn['tty'];
        }
        if (!empty($dsn['connect_timeout'])) {
            $params[0] .= ' connect_timeout=' . $dsn['connect_timeout'];
        }
        if (!empty($dsn['sslmode'])) {
            $params[0] .= ' sslmode=' . $dsn['sslmode'];
        }
        if (!empty($dsn['service'])) {
            $params[0] .= ' service=' . $dsn['service'];
        }

        if (isset($dsn['new_link'])
            && ($dsn['new_link'] == 'true' || $dsn['new_link'] === true))
        {
            if (version_compare(phpversion(), '4.3.0', '>=')) {
                $params[] = PGSQL_CONNECT_FORCE_NEW;
            }
        }

        $connect_function = $persistent ? 'pg_pconnect' : 'pg_connect';

        $ini = ini_get('track_errors');
        $php_errormsg = '';
        if ($ini) {
            $this->connection = @call_user_func_array($connect_function,
                                                      $params);
        } else {
            ini_set('track_errors', 1);
            $this->connection = @call_user_func_array($connect_function,
                                                      $params);
            ini_set('track_errors', $ini);
        }

        if (!$this->connection) {
            return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                     null, null, null,
                                     $php_errormsg);
        }
        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @pg_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        $query = $this->modifyQuery($query);
        if (!$this->autocommit && $ismanip) {
            if ($this->transaction_opcount == 0) {
                $result = @pg_exec($this->connection, 'begin;');
                if (!$result) {
                    return $this->pgsqlRaiseError();
                }
            }
            $this->transaction_opcount++;
        }
        $result = @pg_exec($this->connection, $query);
        if (!$result) {
            return $this->pgsqlRaiseError();
        }
        
        
        if ($ismanip) {
            $this->affected = @pg_affected_rows($result);
            return DB_OK;
        } elseif (preg_match('/^\s*\(*\s*(SELECT|EXPLAIN|SHOW)\s/si', $query)) {
            
            $this->row[(int)$result] = 0; 
            $numrows = $this->numRows($result);
            if (is_object($numrows)) {
                return $numrows;
            }
            $this->_num_rows[(int)$result] = $numrows;
            $this->affected = 0;
            return $result;
        } else {
            $this->affected = 0;
            return DB_OK;
        }
    }

    
    

    
    function nextResult($result)
    {
        return false;
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        $result_int = (int)$result;
        $rownum = ($rownum !== null) ? $rownum : $this->row[$result_int];
        if ($rownum >= $this->_num_rows[$result_int]) {
            return null;
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $arr = @pg_fetch_array($result, $rownum, PGSQL_ASSOC);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @pg_fetch_row($result, $rownum);
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
        $this->row[$result_int] = ++$rownum;
        return DB_OK;
    }

    
    

    
    function freeResult($result)
    {
        if (is_resource($result)) {
            unset($this->row[(int)$result]);
            unset($this->_num_rows[(int)$result]);
            $this->affected = 0;
            return @pg_freeresult($result);
        }
        return false;
    }

    
    

    
    function quote($str)
    {
        return $this->quoteSmart($str);
    }

    
    

    
    function quoteSmart($in)
    {
        if (is_int($in) || is_double($in)) {
            return $in;
        } elseif (is_bool($in)) {
            return $in ? 'TRUE' : 'FALSE';
        } elseif (is_null($in)) {
            return 'NULL';
        } else {
            return "'" . $this->escapeSimple($in) . "'";
        }
    }

    
    

    
    function escapeSimple($str)
    {
        return str_replace("'", "''", str_replace('\\', '\\\\', $str));
    }

    
    

    
    function numCols($result)
    {
        $cols = @pg_numfields($result);
        if (!$cols) {
            return $this->pgsqlRaiseError();
        }
        return $cols;
    }

    
    

    
    function numRows($result)
    {
        $rows = @pg_numrows($result);
        if ($rows === null) {
            return $this->pgsqlRaiseError();
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
            
            
            $result = @pg_exec($this->connection, 'end;');
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->pgsqlRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function rollback()
    {
        if ($this->transaction_opcount > 0) {
            $result = @pg_exec($this->connection, 'abort;');
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->pgsqlRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function affectedRows()
    {
        return $this->affected;
    }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);
        $repeat = false;
        do {
            $this->pushErrorHandling(PEAR_ERROR_RETURN);
            $result =& $this->query("SELECT NEXTVAL('${seqname}')");
            $this->popErrorHandling();
            if ($ondemand && DB::isError($result) &&
                $result->getCode() == DB_ERROR_NOSUCHTABLE) {
                $repeat = true;
                $this->pushErrorHandling(PEAR_ERROR_RETURN);
                $result = $this->createSequence($seq_name);
                $this->popErrorHandling();
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
            } else {
                $repeat = false;
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
        $seqname = $this->getSequenceName($seq_name);
        $result = $this->query("CREATE SEQUENCE ${seqname}");
        return $result;
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DROP SEQUENCE '
                            . $this->getSequenceName($seq_name));
    }

    
    

    
    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        return "$query LIMIT $count OFFSET $from";
    }

    
    

    
    function pgsqlRaiseError($errno = null)
    {
        $native = $this->errorNative();
        if ($errno === null) {
            $errno = $this->errorCode($native);
        }
        return $this->raiseError($errno, null, null, null, $native);
    }

    
    

    
    function errorNative()
    {
        return @pg_errormessage($this->connection);
    }

    
    

    
    function errorCode($errormsg)
    {
        static $error_regexps;
        if (!isset($error_regexps)) {
            $error_regexps = array(
                '/(relation|sequence|table).*does not exist|class .* not found/i'
                    => DB_ERROR_NOSUCHTABLE,
                '/index .* does not exist/'
                    => DB_ERROR_NOT_FOUND,
                '/column .* does not exist/i'
                    => DB_ERROR_NOSUCHFIELD,
                '/relation .* already exists/i'
                    => DB_ERROR_ALREADY_EXISTS,
                '/(divide|division) by zero$/i'
                    => DB_ERROR_DIVZERO,
                '/pg_atoi: error in .*: can\'t parse /i'
                    => DB_ERROR_INVALID_NUMBER,
                '/invalid input syntax for( type)? (integer|numeric)/i'
                    => DB_ERROR_INVALID_NUMBER,
                '/value .* is out of range for type \w*int/i'
                    => DB_ERROR_INVALID_NUMBER,
                '/integer out of range/i'
                    => DB_ERROR_INVALID_NUMBER,
                '/value too long for type character/i'
                    => DB_ERROR_INVALID,
                '/attribute .* not found|relation .* does not have attribute/i'
                    => DB_ERROR_NOSUCHFIELD,
                '/column .* specified in USING clause does not exist in (left|right) table/i'
                    => DB_ERROR_NOSUCHFIELD,
                '/parser: parse error at or near/i'
                    => DB_ERROR_SYNTAX,
                '/syntax error at/'
                    => DB_ERROR_SYNTAX,
                '/column reference .* is ambiguous/i'
                    => DB_ERROR_SYNTAX,
                '/permission denied/'
                    => DB_ERROR_ACCESS_VIOLATION,
                '/violates not-null constraint/'
                    => DB_ERROR_CONSTRAINT_NOT_NULL,
                '/violates [\w ]+ constraint/'
                    => DB_ERROR_CONSTRAINT,
                '/referential integrity violation/'
                    => DB_ERROR_CONSTRAINT,
                '/more expressions than target columns/i'
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
            
            $id = @pg_exec($this->connection, "SELECT * FROM $result LIMIT 0");
            $got_string = true;
        } elseif (isset($result->result)) {
            
            $id = $result->result;
            $got_string = false;
        } else {
            
            $id = $result;
            $got_string = false;
        }

        if (!is_resource($id)) {
            return $this->pgsqlRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @pg_numfields($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $res[$i] = array(
                'table' => $got_string ? $case_func($result) : '',
                'name'  => $case_func(@pg_fieldname($id, $i)),
                'type'  => @pg_fieldtype($id, $i),
                'len'   => @pg_fieldsize($id, $i),
                'flags' => $got_string
                           ? $this->_pgFieldFlags($id, $i, $result)
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
            @pg_freeresult($id);
        }
        return $res;
    }

    
    

    
    function _pgFieldFlags($resource, $num_field, $table_name)
    {
        $field_name = @pg_fieldname($resource, $num_field);

        $result = @pg_exec($this->connection, "SELECT f.attnotnull, f.atthasdef
                                FROM pg_attribute f, pg_class tab, pg_type typ
                                WHERE tab.relname = typ.typname
                                AND typ.typrelid = f.attrelid
                                AND f.attname = '$field_name'
                                AND tab.relname = '$table_name'");
        if (@pg_numrows($result) > 0) {
            $row = @pg_fetch_row($result, 0);
            $flags  = ($row[0] == 't') ? 'not_null ' : '';

            if ($row[1] == 't') {
                $result = @pg_exec($this->connection, "SELECT a.adsrc
                                    FROM pg_attribute f, pg_class tab, pg_type typ, pg_attrdef a
                                    WHERE tab.relname = typ.typname AND typ.typrelid = f.attrelid
                                    AND f.attrelid = a.adrelid AND f.attname = '$field_name'
                                    AND tab.relname = '$table_name' AND f.attnum = a.adnum");
                $row = @pg_fetch_row($result, 0);
                $num = preg_replace("/'(.*)'::\w+/", "\\1", $row[0]);
                $flags .= 'default_' . rawurlencode($num) . ' ';
            }
        } else {
            $flags = '';
        }
        $result = @pg_exec($this->connection, "SELECT i.indisunique, i.indisprimary, i.indkey
                                FROM pg_attribute f, pg_class tab, pg_type typ, pg_index i
                                WHERE tab.relname = typ.typname
                                AND typ.typrelid = f.attrelid
                                AND f.attrelid = i.indrelid
                                AND f.attname = '$field_name'
                                AND tab.relname = '$table_name'");
        $count = @pg_numrows($result);

        for ($i = 0; $i < $count ; $i++) {
            $row = @pg_fetch_row($result, $i);
            $keys = explode(' ', $row[2]);

            if (in_array($num_field + 1, $keys)) {
                $flags .= ($row[0] == 't' && $row[1] == 'f') ? 'unique_key ' : '';
                $flags .= ($row[1] == 't') ? 'primary_key ' : '';
                if (count($keys) > 1)
                    $flags .= 'multiple_key ';
            }
        }

        return trim($flags);
    }

    
    

    
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'tables':
                return 'SELECT c.relname AS "Name"'
                        . ' FROM pg_class c, pg_user u'
                        . ' WHERE c.relowner = u.usesysid'
                        . " AND c.relkind = 'r'"
                        . ' AND NOT EXISTS'
                        . ' (SELECT 1 FROM pg_views'
                        . '  WHERE viewname = c.relname)'
                        . " AND c.relname !~ '^(pg_|sql_)'"
                        . ' UNION'
                        . ' SELECT c.relname AS "Name"'
                        . ' FROM pg_class c'
                        . " WHERE c.relkind = 'r'"
                        . ' AND NOT EXISTS'
                        . ' (SELECT 1 FROM pg_views'
                        . '  WHERE viewname = c.relname)'
                        . ' AND NOT EXISTS'
                        . ' (SELECT 1 FROM pg_user'
                        . '  WHERE usesysid = c.relowner)'
                        . " AND c.relname !~ '^pg_'";
            case 'schema.tables':
                return "SELECT schemaname || '.' || tablename"
                        . ' AS "Name"'
                        . ' FROM pg_catalog.pg_tables'
                        . ' WHERE schemaname NOT IN'
                        . " ('pg_catalog', 'information_schema', 'pg_toast')";
            case 'views':
                
                return 'SELECT viewname from pg_views WHERE schemaname'
                        . " NOT IN ('information_schema', 'pg_catalog')";
            case 'users':
                
                return 'SELECT usename FROM pg_user';
            case 'databases':
                return 'SELECT datname FROM pg_database';
            case 'functions':
            case 'procedures':
                return 'SELECT proname FROM pg_proc WHERE proowner <> 1';
            default:
                return null;
        }
    }

    

}



?>
