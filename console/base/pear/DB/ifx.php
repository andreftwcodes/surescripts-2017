<?php






require_once 'DB/common.php';


class DB_ifx extends DB_common
{
    

    
    var $phptype = 'ifx';

    
    var $dbsyntax = 'ifx';

    
    var $features = array(
        'limit'         => 'emulate',
        'new_link'      => false,
        'numrows'       => 'emulate',
        'pconnect'      => true,
        'prepare'       => false,
        'ssl'           => false,
        'transactions'  => true,
    );

    
    var $errorcode_map = array(
        '-201'    => DB_ERROR_SYNTAX,
        '-206'    => DB_ERROR_NOSUCHTABLE,
        '-217'    => DB_ERROR_NOSUCHFIELD,
        '-236'    => DB_ERROR_VALUE_COUNT_ON_ROW,
        '-239'    => DB_ERROR_CONSTRAINT,
        '-253'    => DB_ERROR_SYNTAX,
        '-292'    => DB_ERROR_CONSTRAINT_NOT_NULL,
        '-310'    => DB_ERROR_ALREADY_EXISTS,
        '-316'    => DB_ERROR_ALREADY_EXISTS,
        '-319'    => DB_ERROR_NOT_FOUND,
        '-329'    => DB_ERROR_NODBSELECTED,
        '-346'    => DB_ERROR_CONSTRAINT,
        '-386'    => DB_ERROR_CONSTRAINT_NOT_NULL,
        '-391'    => DB_ERROR_CONSTRAINT_NOT_NULL,
        '-554'    => DB_ERROR_SYNTAX,
        '-691'    => DB_ERROR_CONSTRAINT,
        '-692'    => DB_ERROR_CONSTRAINT,
        '-703'    => DB_ERROR_CONSTRAINT_NOT_NULL,
        '-1204'   => DB_ERROR_INVALID_DATE,
        '-1205'   => DB_ERROR_INVALID_DATE,
        '-1206'   => DB_ERROR_INVALID_DATE,
        '-1209'   => DB_ERROR_INVALID_DATE,
        '-1210'   => DB_ERROR_INVALID_DATE,
        '-1212'   => DB_ERROR_INVALID_DATE,
        '-1213'   => DB_ERROR_INVALID_NUMBER,
    );

    
    var $connection;

    
    var $dsn = array();


    
    var $autocommit = true;

    
    var $transaction_opcount = 0;

    
    var $affected = 0;


    
    

    
    function DB_ifx()
    {
        $this->DB_common();
    }

    
    

    
    function connect($dsn, $persistent = false)
    {
        if (!PEAR::loadExtension('informix') &&
            !PEAR::loadExtension('Informix'))
        {
            return $this->raiseError(DB_ERROR_EXTENSION_NOT_FOUND);
        }

        $this->dsn = $dsn;
        if ($dsn['dbsyntax']) {
            $this->dbsyntax = $dsn['dbsyntax'];
        }

        $dbhost = $dsn['hostspec'] ? '@' . $dsn['hostspec'] : '';
        $dbname = $dsn['database'] ? $dsn['database'] . $dbhost : '';
        $user = $dsn['username'] ? $dsn['username'] : '';
        $pw = $dsn['password'] ? $dsn['password'] : '';

        $connect_function = $persistent ? 'ifx_pconnect' : 'ifx_connect';

        $this->connection = @$connect_function($dbname, $user, $pw);
        if (!is_resource($this->connection)) {
            return $this->ifxRaiseError(DB_ERROR_CONNECT_FAILED);
        }
        return DB_OK;
    }

    
    

    
    function disconnect()
    {
        $ret = @ifx_close($this->connection);
        $this->connection = null;
        return $ret;
    }

    
    

    
    function simpleQuery($query)
    {
        $ismanip = DB::isManip($query);
        $this->last_query = $query;
        $this->affected   = null;
        if (preg_match('/(SELECT)/i', $query)) {    
            
            
            $result = @ifx_query($query, $this->connection, IFX_SCROLL);
        } else {
            if (!$this->autocommit && $ismanip) {
                if ($this->transaction_opcount == 0) {
                    $result = @ifx_query('BEGIN WORK', $this->connection);
                    if (!$result) {
                        return $this->ifxRaiseError();
                    }
                }
                $this->transaction_opcount++;
            }
            $result = @ifx_query($query, $this->connection);
        }
        if (!$result) {
            return $this->ifxRaiseError();
        }
        $this->affected = @ifx_affected_rows($result);
        
        
        if (preg_match('/(SELECT)/i', $query)) {
            return $result;
        }
        
        

        
        @ifx_free_result($result);

        return DB_OK;
    }

    
    

    
    function nextResult($result)
    {
        return false;
    }

    
    

    
    function affectedRows()
    {
        if (DB::isManip($this->last_query)) {
            return $this->affected;
        } else {
            return 0;
        }
    }

    
    

    
    function fetchInto($result, &$arr, $fetchmode, $rownum = null)
    {
        if (($rownum !== null) && ($rownum < 0)) {
            return null;
        }
        if ($rownum === null) {
            
            $rownum = 'NEXT';
        } else {
            
            $rownum++;
        }
        if (!$arr = @ifx_fetch_row($result, $rownum)) {
            return null;
        }
        if ($fetchmode !== DB_FETCHMODE_ASSOC) {
            $i=0;
            $order = array();
            foreach ($arr as $val) {
                $order[$i++] = $val;
            }
            $arr = $order;
        } elseif ($fetchmode == DB_FETCHMODE_ASSOC &&
                  $this->options['portability'] & DB_PORTABILITY_LOWERCASE)
        {
            $arr = array_change_key_case($arr, CASE_LOWER);
        }
        if ($this->options['portability'] & DB_PORTABILITY_RTRIM) {
            $this->_rtrimArrayValues($arr);
        }
        if ($this->options['portability'] & DB_PORTABILITY_NULL_TO_EMPTY) {
            $this->_convertNullArrayValuesToEmpty($arr);
        }
        return DB_OK;
    }

    
    

    
    function numCols($result)
    {
        if (!$cols = @ifx_num_fields($result)) {
            return $this->ifxRaiseError();
        }
        return $cols;
    }

    
    

    
    function freeResult($result)
    {
        return @ifx_free_result($result);
    }

    
    

    
    function autoCommit($onoff = true)
    {
        
        
        $this->autocommit = $onoff ? true : false;
        return DB_OK;
    }

    
    

    
    function commit()
    {
        if ($this->transaction_opcount > 0) {
            $result = @ifx_query('COMMIT WORK', $this->connection);
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->ifxRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function rollback()
    {
        if ($this->transaction_opcount > 0) {
            $result = @ifx_query('ROLLBACK WORK', $this->connection);
            $this->transaction_opcount = 0;
            if (!$result) {
                return $this->ifxRaiseError();
            }
        }
        return DB_OK;
    }

    
    

    
    function ifxRaiseError($errno = null)
    {
        if ($errno === null) {
            $errno = $this->errorCode(ifx_error());
        }
        return $this->raiseError($errno, null, null, null,
                                 $this->errorNative());
    }

    
    

    
    function errorNative()
    {
        return @ifx_error() . ' ' . @ifx_errormsg();
    }

    
    

    
    function errorCode($nativecode)
    {
        if (ereg('SQLCODE=(.*)]', $nativecode, $match)) {
            $code = $match[1];
            if (isset($this->errorcode_map[$code])) {
                return $this->errorcode_map[$code];
            }
        }
        return DB_ERROR;
    }

    
    

    
    function tableInfo($result, $mode = null)
    {
        if (is_string($result)) {
            
            $id = @ifx_query("SELECT * FROM $result WHERE 1=0",
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
            return $this->ifxRaiseError(DB_ERROR_NEED_MORE_DATA);
        }

        $flds = @ifx_fieldproperties($id);
        $count = @ifx_num_fields($id);

        if (count($flds) != $count) {
            return $this->raiseError("can't distinguish duplicate field names");
        }

        if ($this->options['portability'] & DB_PORTABILITY_LOWERCASE) {
            $case_func = 'strtolower';
        } else {
            $case_func = 'strval';
        }

        $i   = 0;
        $res = array();

        if ($mode) {
            $res['num_fields'] = $count;
        }

        foreach ($flds as $key => $value) {
            $props = explode(';', $value);
            $res[$i] = array(
                'table' => $got_string ? $case_func($result) : '',
                'name'  => $case_func($key),
                'type'  => $props[0],
                'len'   => $props[1],
                'flags' => $props[4] == 'N' ? 'not_null' : '',
            );
            if ($mode & DB_TABLEINFO_ORDER) {
                $res['order'][$res[$i]['name']] = $i;
            }
            if ($mode & DB_TABLEINFO_ORDERTABLE) {
                $res['ordertable'][$res[$i]['table']][$res[$i]['name']] = $i;
            }
            $i++;
        }

        
        if ($got_string) {
            @ifx_free_result($id);
        }
        return $res;
    }

    
    

    
    function getSpecialQuery($type)
    {
        switch ($type) {
            case 'tables':
                return 'SELECT tabname FROM systables WHERE tabid >= 100';
            default:
                return null;
        }
    }

    

}



?>
