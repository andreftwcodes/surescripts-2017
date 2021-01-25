<?php






require_once 'DB/common.php';


class DB_dbase extends DB_common
{
    

    
    var $phptype = 'dbase';

    
    var $dbsyntax = 'dbase';

    
    var $features = array(
        'limit'         => false,
        'new_link'      => false,
        'numrows'       => true,
        'pconnect'      => false,
        'prepare'       => false,
        'ssl'           => false,
        'transactions'  => false,
    );

    
    var $errorcode_map = array(
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $res_row = array();

    
    var $result = 0;

    
    var $types = array(
        'C' => 'character',
        'D' => 'date',
        'L' => 'boolean',
        'M' => 'memo',
        'N' => 'number',
    );


    
    

    
    function DB_dbase()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('dbase')) {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        
        ini_set('track_errors', 1);
        $php_errormsg = '';

        if (!file_exists($dsn['database'])) {
            $this->dsn['mode'] = 2;
            if (empty($dsn['fields']) || !is_array($dsn['fields'])) {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                         null, null, null,
                                         'the dbase file does not exist and '
                                         . 'it could not be created because '
                                         . 'the "fields" element of the DSN '
                                         . 'is not properly set');
            }
            $this->connection = @dbase_create($dsn['database'],
                                              $dsn['fields']);
            if (!$this->connection) {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                         null, null, null,
                                         'the dbase file does not exist and '
                                         . 'the attempt to create it failed: '
                                         . $php_errormsg);
            }
        } else {
            if (!isset($this->dsn['mode'])) {
                $this->dsn['mode'] = 0;
            }
            $this->connection = @dbase_open($dsn['database'],
                                            $this->dsn['mode']);
            if (!$this->connection) {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                         null, null, null,
                                         $php_errormsg);
            }
        }
        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @dbase_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    function &query($query = null)
    {
        
        $this->res_row[(int)$this->result] = 0;
        $tmp =& new DB_result($this, $this->result++);
        return $tmp;
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        if ($rownum === null) {
            $rownum = $this->res_row[(int)$result]++;
        }
        if ($fetchmode & DB_FETCHMODE_ASSOC) {
            $arr = @dbase_get_record_with_names($this->connection, $rownum);
            if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE && $arr) {
                $arr = array_change_key_case($arr, CASE_LOWER);
            }
        } else {
            $arr = @dbase_get_record($this->connection, $rownum);
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

    
    

    
    function numCols($foo)
    {
        return @dbase_numfields($this->connection);
    }

    
    

    
    function numRows($foo)
    {
        return @dbase_numrecords($this->connection);
    }

    
    

    
    function quoteSmart($in)
    {
        if (is_int($in) || is_double($in)) {
            return $in;
        } elseif (is_bool($in)) {
            return $in ? 'T' : 'F';
        } elseif (is_null($in)) {
            return 'NULL';
        } else {
            return "'" . $this->escapeSimple($in) . "'";
        }
    }

    
    

    
    function tableInfo($result = null, $mode = null)
    {
        if (function_exists('dbase_get_header_info')) {
            $id = @dbase_get_header_info($this->connection);
            if (!$id && $php_errormsg) {
                return $this->raiseError(DB_ERROR,
                                         null, null, null,
                                         $php_errormsg);
            }
        } else {
            
            $db = @fopen($this->dsn['database'], 'r');
            if (!$db) {
                return $this->raiseError(DB_ERROR_CONNECT_FAILED,
                                         null, null, null,
                                         $php_errormsg);
            }

            $id = array();
            $i  = 0;

            $line = fread($db, 32);
            while (!feof($db)) {
                $line = fread($db, 32);
                if (substr($line, 0, 1) == chr(13)) {
                    break;
                } else {
                    $pos = strpos(substr($line, 0, 10), chr(0));
                    $pos = ($pos == 0 ? 10 : $pos);
                    $id[$i] = array(
                        'name'   => substr($line, 0, $pos),
                        'type'   => $this->types[substr($line, 11, 1)],
                        'length' => ord(substr($line, 16, 1)),
                        'precision' => ord(substr($line, 17, 1)),
                    );
                }
                $i++;
            }

            fclose($db);
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $res   = array();
        $count = count($id);

        if ($mode) {
            $res['num_fields'] = $count;
        }

        for ($i = 0; $i < $count; $i++) {
            $res[$i] = array(
                'table' => $this->dsn['database'],
                'name'  => $case_func($id[$i]['name']),
                'type'  => $id[$i]['type'],
                'len'   => $id[$i]['length'],
                'flags' => ''
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

    
}



?>
