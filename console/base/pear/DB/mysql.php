<?php






require_once 'DB/common.php';


class DB_mysql extends DB_common
{
    

    
    var $phptype = 'mysql';

    
    var $dbsyntax = 'mysql';

    
    var $features = array(
        'limit'         => 'alter',
        'new_link'      => '4.2.0',
        'numrows'       => true,
        'pconnect'      => true,
        'prepare'       => false,
        'ssl'           => false,
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


    
    

    
    function DB_mysql()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('mysql')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        $params = array();
        if ($dsn['protocol'] && $dsn['protocol'] == 'unix') {
            $params[0] = ':' . $dsn['socket'];
        } else {
            $params[0] = $dsn['hostspec'] ? $dsn['hostspec']
                         : 'localhost';
            if ($dsn['port']) {
                $params[0] .= ':' . $dsn['port'];
            }
        }
        $params[] = $dsn['username'] ? $dsn['username'] : null;
        $params[] = $dsn['password'] ? $dsn['password'] : null;

        if (!$persistent) {
            if (isset($dsn['new_link'])
                && ($dsn['new_link'] == 'true' || $dsn['new_link'] === true))
            {
                $params[] = true;
            } else {
                $params[] = false;
            }
        }
        if (version_compare(phpversion(), '4.3.0', '>=')) {
            $params[] = isset($dsn['client_flags'])
                        ? $dsn['client_flags'] : null;
        }

        $connect_function = $persistent ? 'mysql_pconnect' : 'mysql_connect';

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
            if (($err = @mysql_error()) != '') {
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
            if (!@mysql_select_db($dsn['database'], $this->connection)) {
                return $this->mysqlRaiseError();
            }
            $this->_db = $dsn['database'];
        }

        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @mysql_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        $query = $this->modifyQuery($query);
        if ($this->_db) {
            if (!@mysql_select_db($this->_db, $this->connection)) {
                return $this->mysqlRaiseError(DB_ERROR_NODBSELECTED);
            }
        }
        if (!$this->autocommit && $ismanip) {
            if ($this->transaction_opcount == 0) {
                $result = @mysql_query('SET AUTOCOMMIT=0', $this->connection);
                $result = @mysql_query('BEGIN', $this->connection);
                if (!$result) {
                    return $this->mysqlRaiseError();
                }
            }
            $this->transaction_opcount++;
        }
        if (!$this->options['result_buffering']) {
            $result = @mysql_unbuffered_query($query, $this->connection);
        } else {
            $result = @mysql_query($query, $this->connection);
        }
        if (!$result) {
            return $this->mysqlRaiseError();
        }
        if (is_resource($result)) {
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
            if (!@mysql_data_seek($result, $rownum)) {
                return null;
            }
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $arr = @mysql_fetch_array($result, MYSQL_ASSOC);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @mysql_fetch_row($result);
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
        return @mysql_free_result($result);
    }

    
    

    
    function numCols($result)
    {
        $cols = @mysql_num_fields($result);
        if (!$cols) {
            return $this->mysqlRaiseError();
        }
        return $cols;
    }

    
    

    
    function numRows($result)
    {
        $rows = @mysql_num_rows($result);
        if ($rows === null) {
            return $this->mysqlRaiseError();
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
                if (!@mysql_select_db($this->_db, $this->connection)) {
                    return $this->mysqlRaiseError(DB_ERROR_NODBSELECTED);
                }
            }
            $result = @mysql_query('COMMIT', $this->connection);
            $result = @mysql_query('SET AUTOCOMMIT=1', $this->connection);
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->mysqlRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function rollback()
    {
        if ($this->transaction_opcount > 0) {
            if ($this->_db) {
                if (!@mysql_select_db($this->_db, $this->connection)) {
                    return $this->mysqlRaiseError(DB_ERROR_NODBSELECTED);
                }
            }
            $result = @mysql_query('ROLLBACK', $this->connection);
            $result = @mysql_query('SET AUTOCOMMIT=1', $this->connection);
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->mysqlRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function affectedRows()
    {
        if (DB::isManip($this->last_query)) {
            return @mysql_affected_rows($this->connection);
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
            $result = $this->query("UPDATE ${seqname} ".
                                   'SET id=LAST_INSERT_ID(id+1)');
            $this->popErrorHandling();
            if ($result === DB_OK) {
                
                $id = @mysql_insert_id($this->connection);
                if ($id != 0) {
                    return $id;
                }
                
                
                
                $result = $this->getOne("SELECT GET_LOCK('${seqname}_lock',10)");
                if (DB::isError($result)) {
                    return $this->raiseError($result);
                }
                if ($result == 0) {
                    
                    return $this->mysqlRaiseError(DB_ERROR_NOT_LOCKED);
                }

                
                $result = $this->query("REPLACE INTO ${seqname} (id) VALUES (0)");
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
                    $repeat = 1;
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
        
        $res = $this->query("INSERT INTO ${seqname} (id) VALUES (0)");
        if (DB::isError($res)) {
            return $res;
        }
        
        return $this->query("UPDATE ${seqname} SET id = 0");
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
            
            
            return $this->mysqlRaiseError(DB_ERROR_NOT_LOCKED);
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

    
    

    
    function quote($str)
    {
        return $this->quoteSmart($str);
    }

    
    

    
    function escapeSimple($str)
    {
        if (function_exists('mysql_real_escape_string')) {
            return @mysql_real_escape_string($str, $this->connection);
        } else {
            return @mysql_escape_string($str);
        }
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

    
    

    
    function modifyLimitQuery($query, $from, $count, $params = array())
    {
        if (DB::isManip($query)) {
            return $query . " LIMIT $count";
        } else {
            return $query . " LIMIT $from, $count";
        }
    }

    
    

    
    function mysqlRaiseError($errno = null)
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
            $errno = $this->errorCode(mysql_errno($this->connection));
        }
        return $this->raiseError($errno, null, null, null,
                                 @mysql_errno($this->connection) . ' ** ' .
                                 @mysql_error($this->connection));
    }

    
    

    
    function errorNative()
    {
        return @mysql_errno($this->connection);
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        if (is_string($result)) {
            
            $id = @mysql_list_fields($this->dsn['database'],
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
            return $this->mysqlRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $count = @mysql_num_fields($id);
        $res   = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $res[$i] = array(
                'table' => $case_func(@mysql_field_table($id, $i)),
                'name'  => $case_func(@mysql_field_name($id, $i)),
                'type'  => @mysql_field_type($id, $i),
                'len'   => @mysql_field_len($id, $i),
                'flags' => @mysql_field_flags($id, $i),
            );
            if ($mode & DB_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & DB_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
        }

        
        if ($got_string) {
            @mysql_free_result($id);
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
