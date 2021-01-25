<?php






require_once 'DB/common.php';


class DB_mysqli extends DB_common
{
    

    
    var $phptype = 'mysqli';

    
    var $dbsyntax = 'mysqli';

    
    var $features = array(
        'limit'         => 'alter',
        'new_link'      => false,
        'numrows'       => true,
        'pconnect'      => false,
        'prepare'       => false,
        'ssl'           => true,
        'transactions'  => true,
    );

    
    var $errorcode_map = array(
        1004 => DB_ERROR_CANNOT_CREATE,
        1005 => DB_ERROR_CANNOT_CREATE,
        1006 => DB_ERROR_CANNOT_CREATE,
        1007 => DB_ERROR_ALREADY_EXISTS,
        1008 => DB_ERROR_CANNOT_DROP,
        1022 => DB_ERROR_ALREADY_EXISTS,
        1044 => DB_ERROR_ACCESS_VIOLATION,
        1046 => DB_ERROR_NODBSELECTED,
        1048 => DB_ERROR_CONSTRAINT,
        1049 => DB_ERROR_NOSUCHDB,
        1050 => DB_ERROR_ALREADY_EXISTS,
        1051 => DB_ERROR_NOSUCHTABLE,
        1054 => DB_ERROR_NOSUCHFIELD,
        1061 => DB_ERROR_ALREADY_EXISTS,
        1062 => DB_ERROR_ALREADY_EXISTS,
        1064 => DB_ERROR_SYNTAX,
        1091 => DB_ERROR_NOT_FOUND,
        1100 => DB_ERROR_NOT_LOCKED,
        1136 => DB_ERROR_VALUE_COUNT_ON_ROW,
        1142 => DB_ERROR_ACCESS_VIOLATION,
        1146 => DB_ERROR_NOSUCHTABLE,
        1216 => DB_ERROR_CONSTRAINT,
        1217 => DB_ERROR_CONSTRAINT,
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $autocommit = true;

    
    var $transaction_opcount = 0;

    
    var $_db = '';

    
    var $mysqli_flags = array(
        MYSQLI_NOT_NULL_FLAG        => 'not_null',
        MYSQLI_PRI_KEY_FLAG         => 'primary_key',
        MYSQLI_UNIQUE_KEY_FLAG      => 'unique_key',
        MYSQLI_MULTIPLE_KEY_FLAG    => 'multiple_key',
        MYSQLI_BLOB_FLAG            => 'blob',
        MYSQLI_UNSIGNED_FLAG        => 'unsigned',
        MYSQLI_ZEROFILL_FLAG        => 'zerofill',
        MYSQLI_AUTO_INCREMENT_FLAG  => 'auto_increment',
        MYSQLI_TIMESTAMP_FLAG       => 'timestamp',
        MYSQLI_SET_FLAG             => 'set',
        
        
        MYSQLI_GROUP_FLAG           => 'group_by'
    );

    
    var $mysqli_types = array(
        MYSQLI_TYPE_DECIMAL     => 'decimal',
        MYSQLI_TYPE_TINY        => 'tinyint',
        MYSQLI_TYPE_SHORT       => 'int',
        MYSQLI_TYPE_LONG        => 'int',
        MYSQLI_TYPE_FLOAT       => 'float',
        MYSQLI_TYPE_DOUBLE      => 'double',
        
        MYSQLI_TYPE_TIMESTAMP   => 'timestamp',
        MYSQLI_TYPE_LONGLONG    => 'bigint',
        MYSQLI_TYPE_INT24       => 'mediumint',
        MYSQLI_TYPE_DATE        => 'date',
        MYSQLI_TYPE_TIME        => 'time',
        MYSQLI_TYPE_DATETIME    => 'datetime',
        MYSQLI_TYPE_YEAR        => 'year',
        MYSQLI_TYPE_NEWDATE     => 'date',
        MYSQLI_TYPE_ENUM        => 'enum',
        MYSQLI_TYPE_SET         => 'set',
        MYSQLI_TYPE_TINY_BLOB   => 'tinyblob',
        MYSQLI_TYPE_MEDIUM_BLOB => 'mediumblob',
        MYSQLI_TYPE_LONG_BLOB   => 'longblob',
        MYSQLI_TYPE_BLOB        => 'blob',
        MYSQLI_TYPE_VAR_STRING  => 'varchar',
        MYSQLI_TYPE_STRING      => 'char',
        MYSQLI_TYPE_GEOMETRY    => 'geometry',
    );


    
    

    
    function DB_mysqli()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('mysqli')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        $ini = ini_get('track_errors');
        ini_set('track_errors', 1);
        $php_errormsg = '';

        if ($this->getOption('ssl') === true) {
            $init = mysqli_init();
            mysqli_ssl_set(
                $init,
                empty($dsn['key'])    ? null : $dsn['key'],
                empty($dsn['cert'])   ? null : $dsn['cert'],
                empty($dsn['ca'])     ? null : $dsn['ca'],
                empty($dsn['capath']) ? null : $dsn['capath'],
                empty($dsn['cipher']) ? null : $dsn['cipher']
            );
            if ($this->connection = @mysqli_real_connect(
                    $init,
                    $dsn['hostspec'],
                    $dsn['username'],
                    $dsn['password'],
                    $dsn['database'],
                    $dsn['port'],
                    $dsn['socket']))
            {
                $this->connection = $init;
            }
        } else {
            $this->connection = @mysqli_connect(
                $dsn['hostspec'],
                $dsn['username'],
                $dsn['password'],
                $dsn['database'],
                $dsn['port'],
                $dsn['socket']
            );
        }

        ini_set('track_errors', $ini);

        if (!$this->connection) {
            if (($err = @mysqli_connect_error()) != '') {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                         null, null, null,
                                         $err);
            } else {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                         null, null, null,
                                         $php_errormsg);
            }
        }

        if ($dsn['database']) {
            $this->_db = $dsn['database'];
        }

        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @mysqli_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        $query = $this->modifyQuery($query);
        if ($this->_db) {
            if (!@mysqli_select_db($this->connection, $this->_db)) {
                return $this->mysqliRaiseError(DB_ERROR_NODBSELECTED);
            }
        }
        if (!$this->autocommit && $ismanip) {
            if ($this->transaction_opcount == 0) {
                $result = @mysqli_query($this->connection, 'SET AUTOCOMMIT=0');
                $result = @mysqli_query($this->connection, 'BEGIN');
                if (!$result) {
                    return $this->mysqliRaiseError();
                }
            }
            $this->transaction_opcount++;
        }
        $result = @mysqli_query($this->connection, $query);
        if (!$result) {
            return $this->mysqliRaiseError();
        }
        if (is_object($result)) {
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
            if (!@mysqli_data_seek($result, $rownum)) {
                return null;
            }
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $arr = @mysqli_fetch_array($result, MYSQLI_ASSOC);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @mysqli_fetch_row($result);
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
        return @mysqli_free_result($result);
    }

    
    

    
    function numCols($result)
    {
        $cols = @mysqli_num_fields($result);
        if (!$cols) {
            return $this->mysqliRaiseError();
        }
        return $cols;
    }

    
    

    
    function numRows($result)
    {
        $rows = @mysqli_num_rows($result);
        if ($rows === null) {
            return $this->mysqliRaiseError();
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
            if ($this->_db) {
                if (!@mysqli_select_db($this->connection, $this->_db)) {
                    return $this->mysqliRaiseError(DB_ERROR_NODBSELECTED);
                }
            }
            $result = @mysqli_query($this->connection, 'COMMIT');
            $result = @mysqli_query($this->connection, 'SET AUTOCOMMIT=1');
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->mysqliRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function rollback()
    {
        if ($this->transaction_opcount > 0) {
            if ($this->_db) {
                if (!@mysqli_select_db($this->connection, $this->_db)) {
                    return $this->mysqliRaiseError(DB_ERROR_NODBSELECTED);
                }
            }
            $result = @mysqli_query($this->connection, 'ROLLBACK');
            $result = @mysqli_query($this->connection, 'SET AUTOCOMMIT=1');
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->mysqliRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function affectedRows()
    {
        if (DB::isManip($this->last_query)) {
            return @mysqli_affected_rows($this->connection);
        } else {
            return 0;
        }
     }

    
    

    
    function nextId($seq_name, $ondemand = true)
    {
        $seqname = $this->getSequenceName($seq_name);
        do {
            $repeat = 0;
            $this->pushErrorHandling(PEAR_ERROR_RETURN);
            $result = $this->query('UPDATE ' . $seqname
                                   . ' SET id = LAST_INSERT_ID(id + 1)');
            $this->popErrorHandling();
            if ($result === DB_OK) {
                
                $id = @mysqli_insert_id($this->connection);
                if ($id != 0) {
                    return $id;
                }

                
                
                
                
                $result = $this->getOne('SELECT GET_LOCK('
                                        . "'${seqname}_lock', 10)");
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
                if ($result == 0) {
                    return $this->mysqliRaiseError(DB_ERROR_NOT_LOCKED);
                }

                
                $result = $this->query('REPLACE INTO ' . $seqname
                                       . ' (id) VALUES (0)');
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }

                
                $result = $this->getOne('SELECT RELEASE_LOCK('
                                        . "'${seqname}_lock')");
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
                
                return 1;

            } elseif ($ondemand && DB::isError($result) &&
                $result->getCode() == DB_ERROR_NOSUCHTABLE)
            {
                
                $result = $this->createSequence($seq_name);

                
                
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                } else {
                    
                    return 1;
                }

            } elseif (DB::isError($result) &&
                      $result->getCode() == DB_ERROR_ALREADY_EXISTS)
            {
                
                
                $result = $this->_BCsequence($seqname);
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
                $repeat = 1;
            }
        } while ($repeat);

        return $this->raiseError($result);
    }

    
    function createSequence($seq_name)
    {
        $seqname = $this->getSequenceName($seq_name);
        $res = $this->query('CREATE TABLE ' . $seqname
                            . ' (id INTEGER UNSIGNED AUTO_INCREMENT NOT NULL,'
                            . ' PRIMARY KEY(id))');
        if (DB::isError($res)) {
            return $res;
        }
        
        return $this->query("INSERT INTO ${seqname} (id) VALUES (0)");
    }

    
    

    
    function dropSequence($seq_name)
    {
        return $this->query('DROP TABLE ' . $this->getSequenceName($seq_name));
    }

    
    

    
    function _BCsequence($seqname)
    {
        
        
        
        $result = $this->getOne("SELECT GET_LOCK('${seqname}_lock',10)");
        if (DB::isError($result)) {
            return $result;
        }
        if ($result == 0) {
            
            
            return $this->mysqliRaiseError(DB_ERROR_NOT_LOCKED);
        }

        $highest_id = $this->getOne("SELECT MAX(id) FROM ${seqname}");
        if (DB::isError($highest_id)) {
            return $highest_id;
        }

        
        
        
        $result = $this->query('DELETE FROM ' . $seqname
                               . " WHERE id <> $highest_id");
        if (DB::isError($result)) {
            return $result;
        }

        
        
        
        $result = $this->getOne("SELECT RELEASE_LOCK('${seqname}_lock')");
        if (DB::isError($result)) {
            return $result;
        }
        return true;
    }

    
    

    
    function quoteIdentifier($str)
    {
        return '`' . $str . '`';
    }

    
    

    
    function escapeSimple($str)
    {
        return @mysqli_real_escape_string($this->connection, $str);
    }

    
    

    
    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        if (DB::isManip($query)) {
            return $query . " LIMIT $count";
        } else {
            return $query . " LIMIT $from, $count";
        }
    }

    
    

    
    function mysqliRaiseError($errno = null)
    {
        if ($errno === null) {
            if ($this->options['portability'] & DB_PORTABILITY_ERRORS) {
                $this->errorcode_map[1022] = DB_ERROR_CONSTRAINT;
                $this->errorcode_map[1048] = DB_ERROR_CONSTRAINT_NOT_NULL;
                $this->errorcode_map[1062] = DB_ERROR_CONSTRAINT;
            } else {
                
                $this->errorcode_map[1022] = DB_ERROR_ALREADY_EXISTS;
                $this->errorcode_map[1048] = DB_ERROR_CONSTRAINT;
                $this->errorcode_map[1062] = DB_ERROR_ALREADY_EXISTS;
            }
            $errno = $this->errorCode(mysqli_errno($this->connection));
        }
        return $this->raiseError($errno, null, null, null,
                                 @mysqli_errno($this->connection) . ' ** ' .
                                 @mysqli_error($this->connection));
    }

    
    

    
    function errorNative()
    {
        return @mysqli_errno($this->connection);
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        if (is_string($result)) {
            
            $id = @mysqli_query($this->connection,
                                "SELECT * FROM $result LIMIT 0");
            $got_string = true;
        } elseif (isset($result->result)) {
            
            $id = $result->result;
            $got_string = false;
        } else {
            
            $id = $result;
            $got_string = false;
        }

        if (!is_a($id, 'mysqli_result')) {
            return $this->mysqliRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @mysqli_num_fields($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $tmp = @mysqli_fetch_field($id);

            $flags = '';
            foreach ($this->mysqli_flags as $const => $means) {
                if ($tmp->flags & $const) {
                    $flags .= $means . ' ';
                }
            }
            if ($tmp->def) {
                $flags .= 'default_' . rawurlencode($tmp->def);
            }
            $flags = trim($flags);

            $res[$i] = array(
                'table' => $case_func($tmp->table),
                'name'  => $case_func($tmp->name),
                'type'  => isset($this->mysqli_types[$tmp->type])
                                    ? $this->mysqli_types[$tmp->type]
                                    : 'unknown',
                'len'   => $tmp->max_length,
                'flags' => $flags,
            );

            if ($mode & DB_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & DB_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
        }

        
        if ($got_string) {
            @mysqli_free_result($id);
        }
        return $res;
    }

    
    

    
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'tables':
                return 'SHOW TABLES';
            case 'users':
                return 'SELECT DISTINCT User FROM mysql.user';
            case 'databases':
                return 'SHOW DATABASES';
            default:
                return null;
        }
    }

    

}



?>
