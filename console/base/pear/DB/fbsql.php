<?php






require_once 'DB/common.php';


class DB_fbsql extends DB_common
{
    

    
    var $phptype = 'fbsql';

    
    var $dbsyntax = 'fbsql';

    
    var $features = array(
        'limit'         => 'alter',
        'new_link'      => false,
        'numrows'       => true,
        'pconnect'      => true,
        'prepare'       => false,
        'ssl'           => false,
        'transactions'  => true,
    );

    
    var $errorcode_map = array(
         22 => DB_ERROR_SYNTAX,
         85 => DB_ERROR_ALREADY_EXISTS,
        108 => DB_ERROR_SYNTAX,
        116 => DB_ERROR_NOSUCHTABLE,
        124 => DB_ERROR_VALUE_COUNT_ON_ROW,
        215 => DB_ERROR_NOSUCHFIELD,
        217 => DB_ERROR_INVALID_NUMBER,
        226 => DB_ERROR_NOSUCHFIELD,
        231 => DB_ERROR_INVALID,
        239 => DB_ERROR_TRUNCATED,
        251 => DB_ERROR_SYNTAX,
        266 => DB_ERROR_NOT_FOUND,
        357 => DB_ERROR_CONSTRAINT_NOT_NULL,
        358 => DB_ERROR_CONSTRAINT,
        360 => DB_ERROR_CONSTRAINT,
        361 => DB_ERROR_CONSTRAINT,
    );

    
    var $connection;

    
    var $dsn = array();


    
    

    
    function DB_fbsql()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('fbsql')) {
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

        $connect_function = $persistent ? 'fbsql_pconnect' : 'fbsql_connect';

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

        if ($dsn['database']) {
            if (!@fbsql_select_db($dsn['database'], $this->connection)) {
                return $this->fbsqlRaiseError();
            }
        }

        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @fbsql_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $this->last_query = $query;
        $query = $this->modifyQuery($query);
        $result = @fbsql_query("$query;", $this->connection);
        if (!$result) {
            return $this->fbsqlRaiseError();
        }
        
        
        if (DB::isManip($query)) {
            return DB_OK;
        }
        return $result;
    }

    
    

    
    function nextResult($result)
    {
        return @fbsql_next_result($result);
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        if ($rownum !== null) {
            if (!@fbsql_data_seek($result, $rownum)) {
                return null;
            }
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $arr = @fbsql_fetch_array($result, FBSQL_ASSOC);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @fbsql_fetch_row($result);
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
        return @fbsql_free_result($result);
    }

    
    

    
    function autoCommit($onoff=false)
    {
        if ($onoff) {
            $this->query("SET COMMIT TRUE");
        } else {
            $this->query("SET COMMIT FALSE");
        }
    }

    
    

    
    function commit()
    {
        @fbsql_commit();
    }

    
    

    
    function rollback()
    {
        @fbsql_rollback();
    }

    
    

    
    function numCols($result)
    {
        $cols = @fbsql_num_fields($result);
        if (!$cols) {
            return $this->fbsqlRaiseError();
        }
        return $cols;
    }

    
    

    
    function numRows($result)
    {
        $rows = @fbsql_num_rows($result);
        if ($rows === null) {
            return $this->fbsqlRaiseError();
        }
        return $rows;
    }

    
    

    
    function affectedRows()
    {
        if (DB::isManip($this->last_query)) {
            $result = @fbsql_affected_rows($this->connection);
        } else {
            $result = 0;
        }
        return $result;
     }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);
        do {
            $repeat = 0;
            $this->pushErrorHandling(PEAR_ERROR_RETURN);
            $result = $this->query('SELECT UNIQUE FROM ' . $seqname);
            $this->popErrorHandling();
            if ($ondemand && DB::isError($result) &&
                $result->getCode() == DB_ERROR_NOSUCHTABLE) {
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
            return $this->fbsqlRaiseError();
        }
        $result->fetchInto($tmp, DB_FETCHMODE_ORDERED);
        return $tmp[0];
    }

    
    function createSequence($seq_name)
    {
        $seqname = $this->getSequenceName($seq_name);
        $res = $this->query('CREATE TABLE ' . $seqname
                            . ' (id INTEGER NOT NULL,'
                            . ' PRIMARY KEY(id))');
        if ($res) {
            $res = $this->query('SET UNIQUE = 0 FOR ' . $seqname);
        }
        return $res;
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DROP TABLE ' . $this->getSequenceName($seq_name)
                            . ' RESTRICT');
    }

    
    

    
    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        if (DB::isManip($query)) {
            return preg_replace('/^([\s(])*SELECT/i',
                                "\\1SELECT TOP($count)", $query);
        } else {
            return preg_replace('/([\s(])*SELECT/i',
                                "\\1SELECT TOP($from, $count)", $query);
        }
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

    
    

    
    function fbsqlRaiseError($errno = null)
    {
        if ($errno === null) {
            $errno = $this->errorCode(fbsql_errno($this->connection));
        }
        return $this->raiseError($errno, null, null, null,
                                 @fbsql_error($this->connection));
    }

    
    

    
    function errorNative()
    {
        return @fbsql_errno($this->connection);
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        if (is_string($result)) {
            
            $id = @fbsql_list_fields($this->dsn['database'],
                                     $result, $this->connection);
            $got_string = true;
        } elseif (isset($result->result)) {
            
            $id = $result->result;
            $got_string = false;
        } else {
            
            $id = $result;
            $got_string = false;
        }

        if (!is_resource($id)) {
            return $this->fbsqlRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @fbsql_num_fields($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $res[$i] = array(
                'table' => $case_func(@fbsql_field_table($id, $i)),
                'name'  => $case_func(@fbsql_field_name($id, $i)),
                'type'  => @fbsql_field_type($id, $i),
                'len'   => @fbsql_field_len($id, $i),
                'flags' => @fbsql_field_flags($id, $i),
            );
            if ($mode & DB_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & DB_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
        }

        
        if ($got_string) {
            @fbsql_free_result($id);
        }
        return $res;
    }

    
    

    
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'tables':
                return 'SELECT "table_name" FROM information_schema.tables'
                       . ' t0, information_schema.schemata t1'
                       . ' WHERE t0.schema_pk=t1.schema_pk AND'
                       . ' "table_type" = \'BASE TABLE\''
                       . ' AND "schema_name" = current_schema';
            case 'views':
                return 'SELECT "table_name" FROM information_schema.tables'
                       . ' t0, information_schema.schemata t1'
                       . ' WHERE t0.schema_pk=t1.schema_pk AND'
                       . ' "table_type" = \'VIEW\''
                       . ' AND "schema_name" = current_schema';
            case 'users':
                return 'SELECT "user_name" from information_schema.users'; 
            case 'functions':
                return 'SELECT "routine_name" FROM'
                       . ' information_schema.psm_routines'
                       . ' t0, information_schema.schemata t1'
                       . ' WHERE t0.schema_pk=t1.schema_pk'
                       . ' AND "routine_kind"=\'FUNCTION\''
                       . ' AND "schema_name" = current_schema';
            case 'procedures':
                return 'SELECT "routine_name" FROM'
                       . ' information_schema.psm_routines'
                       . ' t0, information_schema.schemata t1'
                       . ' WHERE t0.schema_pk=t1.schema_pk'
                       . ' AND "routine_kind"=\'PROCEDURE\''
                       . ' AND "schema_name" = current_schema';
            default:
                return null;
        }
    }

    
}



?>
