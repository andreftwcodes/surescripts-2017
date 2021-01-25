<?php






require_once 'DB/common.php';


class DB_sqlite extends DB_common
{
    

    
    var $phptype = 'sqlite';

    
    var $dbsyntax = 'sqlite';

    
    var $features = array(
        'limit'         => 'alter',
        'new_link'      => false,
        'numrows'       => true,
        'pconnect'      => true,
        'prepare'       => false,
        'ssl'           => false,
        'transactions'  => false,
    );

    
    var $errorcode_map = array(
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $keywords = array (
        'BLOB'      => '',
        'BOOLEAN'   => '',
        'CHARACTER' => '',
        'CLOB'      => '',
        'FLOAT'     => '',
        'INTEGER'   => '',
        'KEY'       => '',
        'NATIONAL'  => '',
        'NUMERIC'   => '',
        'NVARCHAR'  => '',
        'PRIMARY'   => '',
        'TEXT'      => '',
        'TIMESTAMP' => '',
        'UNIQUE'    => '',
        'VARCHAR'   => '',
        'VARYING'   => '',
    );

    
    var $_lasterror = '';


    
    

    
    function DB_sqlite()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('sqlite')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        if ($dsn['database']) {
            if (!file_exists($dsn['database'])) {
                if (!touch($dsn['database'])) {
                    return $this->sqliteRaiseError(DB_ERROR_NOT_FOUND);
                }
                if (!isset($dsn['mode']) ||
                    !is_numeric($dsn['mode']))
                {
                    $mode = 0644;
                } else {
                    $mode = octdec($dsn['mode']);
                }
                if (!chmod($dsn['database'], $mode)) {
                    return $this->sqliteRaiseError(DB_ERROR_NOT_FOUND);
                }
                if (!file_exists($dsn['database'])) {
                    return $this->sqliteRaiseError(DB_ERROR_NOT_FOUND);
                }
            }
            if (!is_file($dsn['database'])) {
                return $this->sqliteRaiseError(DB_ERROR_INVALID);
            }
            if (!is_readable($dsn['database'])) {
                return $this->sqliteRaiseError(DB_ERROR_ACCESS_VIOLATION);
            }
        } else {
            return $this->sqliteRaiseError(DB_ERROR_ACCESS_VIOLATION);
        }

        $connect_function = $persistent ? 'sqlite_popen' : 'sqlite_open';

        
        ini_set('track_errors', 1);
        $php_errormsg = '';

        if (!$this->connection = @$connect_function($dsn['database'])) {
            return $this->raiseError(DB_ERROR_NODBSELECTED,
                                     null, null, null,
                                     $php_errormsg);
        }
        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @sqlite_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        $query = $this->modifyQuery($query);

        $php_errormsg = '';

        $result = @sqlite_query($query, $this->connection);
        $this->_lasterror = $php_errormsg ? $php_errormsg : '';

        $this->result = $result;
        if (!$this->result) {
            return $this->sqliteRaiseError(null);
        }

        
        
        if (!$ismanip) {
            $numRows = $this->numRows($result);
            if (is_object($numRows)) {
                
                return $numRows;
            }
            return $result;
        }
        return DB_OK;
    }

    
    

    
    function nextResult($result)
    {
        return false;
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        if ($rownum !== null) {
            if (!@sqlite_seek($this->result, $rownum)) {
                return null;
            }
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $arr = @sqlite_fetch_array($result, SQLITE_ASSOC);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @sqlite_fetch_array($result, SQLITE_NUM);
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

    
    

    
    function freeResult(&$result)
    {
        
        if (!is_resource($result)) {
            return false;
        }
        $result = null;
        return true;
    }

    
    

    
    function numCols($result)
    {
        $cols = @sqlite_num_fields($result);
        if (!$cols) {
            return $this->sqliteRaiseError();
        }
        return $cols;
    }

    
    

    
    function numRows($result)
    {
        $rows = @sqlite_num_rows($result);
        if ($rows === null) {
            return $this->sqliteRaiseError();
        }
        return $rows;
    }

    
    

    
    function affectedRows()
    {
        return @sqlite_changes($this->connection);
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DROP TABLE ' . $this->getSequenceName($seq_name));
    }

    
    function createSequence($seq_name)
    {
        $seqname = $this->getSequenceName($seq_name);
        $query   = 'CREATE TABLE ' . $seqname .
                   ' (id INTEGER UNSIGNED PRIMARY KEY) ';
        $result  = $this->query($query);
        if (DB::isError($result)) {
            return($result);
        }
        $query   = "CREATE TRIGGER ${seqname}_cleanup AFTER INSERT ON $seqname
                    BEGIN
                        DELETE FROM $seqname WHERE id<LAST_INSERT_ROWID();
                    END ";
        $result  = $this->query($query);
        if (DB::isError($result)) {
            return($result);
        }
    }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);

        do {
            $repeat = 0;
            $this->pushErrorHandling(PEAR_ERROR_RETURN);
            $result = $this->query("INSERT INTO $seqname (id) VALUES (NULL)");
            $this->popErrorHandling();
            if ($result === DB_OK) {
                $id = @sqlite_last_insert_rowid($this->connection);
                if ($id != 0) {
                    return $id;
                }
            } elseif ($ondemand && DB::isError($result) &&
                      $result->getCode() == DB_ERROR_NOSUCHTABLE)
            {
                $result = $this->createSequence($seq_name);
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                } else {
                    $repeat = 1;
                }
            }
        } while ($repeat);

        return $this->raiseError($result);
    }

    
    

    
    function getDbFileStats($arg = '')
    {
        $stats = stat($this->dsn['database']);
        if ($stats == false) {
            return false;
        }
        if (is_array($stats)) {
            if (is_numeric($arg)) {
                if (((int)$arg <= 12) & ((int)$arg >= 0)) {
                    return false;
                }
                return $stats[$arg ];
            }
            if (array_key_exists(trim($arg), $stats)) {
                return $stats[$arg ];
            }
        }
        return $stats;
    }

    
    

    
    function escapeSimple($str)
    {
        return @sqlite_escape_string($str);
    }

    
    

    
    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        return "$query LIMIT $count OFFSET $from";
    }

    
    

    
    function modifyQuery($query)
    {
        if ($this->options['portability'] & DB_PORTABILITY_DELETE_COUNT) {
            if (preg_match('/^\s*DELETE\s+FROM\s+(\S+)\s*$/i', $query)) {
                $query = preg_replace('/^\s*DELETE\s+FROM\s+(\S+)\s*$/',
                                      'DELETE FROM \1 WHERE 1=1', $query);
            }
        }
        return $query;
    }

    
    

    
    function sqliteRaiseError($errno = null)
    {
        $native = $this->errorNative();
        if ($errno === null) {
            $errno = $this->errorCode($native);
        }

        $errorcode = @sqlite_last_error($this->connection);
        $userinfo = "$errorcode ** $this->last_query";

        return $this->raiseError($errno, null, null, $userinfo, $native);
    }

    
    

    
    function errorNative()
    {
        return $this->_lasterror;
    }

    
    

    
    function errorCode($errormsg)
    {
        static $error_regexps;
        if (!isset($error_regexps)) {
            $error_regexps = array(
                '/^no such table:/' => DB_ERROR_NOSUCHTABLE,
                '/^no such index:/' => DB_ERROR_NOT_FOUND,
                '/^(table|index) .* already exists$/' => DB_ERROR_ALREADY_EXISTS,
                '/PRIMARY KEY must be unique/i' => DB_ERROR_CONSTRAINT,
                '/is not unique/' => DB_ERROR_CONSTRAINT,
                '/columns .* are not unique/i' => DB_ERROR_CONSTRAINT,
                '/uniqueness constraint failed/' => DB_ERROR_CONSTRAINT,
                '/may not be NULL/' => DB_ERROR_CONSTRAINT_NOT_NULL,
                '/^no such column:/' => DB_ERROR_NOSUCHFIELD,
                '/column not present in both tables/i' => DB_ERROR_NOSUCHFIELD,
                '/^near ".*": syntax error$/' => DB_ERROR_SYNTAX,
                '/[0-9]+ values for [0-9]+ columns/i' => DB_ERROR_VALUE_COUNT_ON_ROW,
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
            
            $id = @sqlite_array_query($this->connection,
                                      "PRAGMA table_info('$result');",
                                      SQLITE_ASSOC);
            $got_string = true;
        } else {
            $this->last_query = '';
            return $this->raiseError(DB_ERROR_NOT_CAPABLE, null, null, null,
                                     'This DBMS can not obtain tableInfo' .
                                     ' from result sets');
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = count($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            if (strpos($id[$i]['type'], '(') !== false) {
                $bits = explode('(', $id[$i]['type']);
                $type = $bits[0];
                $len  = rtrim($bits[1],')');
            } else {
                $type = $id[$i]['type'];
                $len  = 0;
            }

            $flags = '';
            if ($id[$i]['pk']) {
                $flags .= 'primary_key ';
            }
            if ($id[$i]['notnull']) {
                $flags .= 'not_null ';
            }
            if ($id[$i]['dflt_value'] !== null) {
                $flags .= 'default_' . rawurlencode($id[$i]['dflt_value']);
            }
            $flags = trim($flags);

            $res[$i] = array(
                'table' => $case_func($result),
                'name'  => $case_func($id[$i]['name']),
                'type'  => $type,
                'len'   => $len,
                'flags' => $flags,
            );

            if ($mode & DB_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & DB_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
        }

        return $res;
    }

    
    

    
    function getSpecialQuery($type, $args = array())
    {
        if (!is_array($args)) {
            return $this->raiseError('no key specified', null, null, null,
                                     'Argument has to be an array.');
        }

        switch ($type) {
            case 'master':
                return 'SELECT * FROM sqlite_master;';
            case 'tables':
                return "SELECT name FROM sqlite_master WHERE type='table' "
                       . 'UNION ALL SELECT name FROM sqlite_temp_master '
                       . "WHERE type='table' ORDER BY name;";
            case 'schema':
                return 'SELECT sql FROM (SELECT * FROM sqlite_master '
                       . 'UNION ALL SELECT * FROM sqlite_temp_master) '
                       . "WHERE type!='meta' "
                       . 'ORDER BY tbl_name, type DESC, name;';
            case 'schemax':
            case 'schema_x':
                
                return 'SELECT sql FROM (SELECT * FROM sqlite_master '
                       . 'UNION ALL SELECT * FROM sqlite_temp_master) '
                       . "WHERE tbl_name LIKE '{$args['table']}' "
                       . "AND type!='meta' "
                       . 'ORDER BY type DESC, name;';
            case 'alter':
                
                $rows = strtr($args['rows'], $this->keywords);

                $q = array(
                    'BEGIN TRANSACTION',
                    "CREATE TEMPORARY TABLE {$args['table']}_backup ({$args['rows']})",
                    "INSERT INTO {$args['table']}_backup SELECT {$args['save']} FROM {$args['table']}",
                    "DROP TABLE {$args['table']}",
                    "CREATE TABLE {$args['table']} ({$args['rows']})",
                    "INSERT INTO {$args['table']} SELECT {$rows} FROM {$args['table']}_backup",
                    "DROP TABLE {$args['table']}_backup",
                    'COMMIT',
                );

                
                foreach ($q as $query) {
                    $this->query($query);
                }
                return "SELECT * FROM {$args['table']};";
            default:
                return null;
        }
    }

    
}



?>
