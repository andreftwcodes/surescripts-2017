<?php





















require_once 'Auth/Container.php';
require_once 'adodb.inc.php';
require_once 'adodb-pear.inc.php';
require_once 'adodb-errorpear.inc.php';


class Auth_Container_ADOdb extends Auth_Container
{

    
    var $options = array();

    
    var $db = null;
    var $dsn = '';
	
    
    var $activeUser = '';

    

    
    function Auth_Container_ADOdb($dsn)
    {
        $this->_setDefaults();
		
        if (is_array($dsn)) {
            $this->_parseOptions($dsn);

            if (empty($this->options['dsn'])) {
                PEAR::raiseError('No connection parameters specified!');
            }
        } else {
        	
            $this->options['dsn'] = $dsn;
        }
    }

    
    

    
     function _connect($dsn)
    {
        if (is_string($dsn) || is_array($dsn)) {
        	if(!$this->db) {
	        	$this->db = ADONewConnection($dsn);
	    		if( $err = ADODB_Pear_error() ) {
	   	    		return PEAR::raiseError($err);
	    		}
        	}
        	
        } else {
            return PEAR::raiseError('The given dsn was not valid in file ' . __FILE__ . ' at line ' . __LINE__,
                                    41,
                                    PEAR_ERROR_RETURN,
                                    null,
                                    null
                                    );
        }
        
        if(!$this->db) {
        	return PEAR::raiseError(ADODB_Pear_error());
        } else {
        	return true;
        }
    }

    
    

    
    function _prepare()
    {
    	if(!$this->db) {
    		$res = $this->_connect($this->options['dsn']);  		
    	}
        return true;
    }

    
    

    
    function query($query)
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return $err;
        }
        return $this->db->query($query);
    }

    
    

    
    function _setDefaults()
    {
    	$this->options['db_type']	= 'mysql';
        $this->options['table']       = 'auth';
        $this->options['usernamecol'] = 'username';
        $this->options['passwordcol'] = 'password';
        $this->options['dsn']         = '';
        $this->options['db_fields']   = '';
        $this->options['cryptType']   = 'md5';
    }

    
    

    
    function _parseOptions($array)
    {
        foreach ($array as $key => $value) {
            if (isset($this->options[$key])) {
                $this->options[$key] = $value;
            }
        }

        
        if(!empty($this->options['db_fields'])){
            if(is_array($this->options['db_fields'])){
                $this->options['db_fields'] = join($this->options['db_fields'], ', ');
            }
            $this->options['db_fields'] = ', '.$this->options['db_fields'];
        }
    }

    
    

    
    function fetchData($username, $password)
    {
        
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        
        if(strstr($this->options['db_fields'], '*')){
            $sql_from = "*";
        }
        else{
            $sql_from = $this->options['usernamecol'] . ", ".$this->options['passwordcol'].$this->options['db_fields'];
        }
        
        $query = "SELECT ".$sql_from.
                " FROM ".$this->options['table'].
                " WHERE ".$this->options['usernamecol']." = " . $this->db->Quote($username);
        
        $ADODB_FETCH_MODE = ADODB_FETCH_ASSOC;
        $rset = $this->db->Execute( $query );
        $res = $rset->fetchRow();

        if (DB::isError($res)) {
            return PEAR::raiseError($res->getMessage(), $res->getCode());
        }
        if (!is_array($res)) {
            $this->activeUser = '';
            return false;
        }
        if ($this->verifyPassword(trim($password, "\r\n"),
                                  trim($res[$this->options['passwordcol']], "\r\n"),
                                  $this->options['cryptType'])) {
            
            foreach ($res as $key => $value) {
                if ($key == $this->options['passwordcol'] ||
                    $key == $this->options['usernamecol']) {
                    continue;
                }
                
                
                if(is_object($this->_auth_obj)){
                    $this->_auth_obj->setAuthData($key, $value);
                } else {
                    Auth::setAuthData($key, $value);
                }
            }

            return true;
        }

        $this->activeUser = $res[$this->options['usernamecol']];
        return false;
    }

    
    

    function listUsers()
    {
        $err = $this->_prepare();
        if ($err !== true) {
            return PEAR::raiseError($err->getMessage(), $err->getCode());
        }

        $retVal = array();

        
        if(strstr($this->options['db_fields'], '*')){
            $sql_from = "*";
        }
        else{
            $sql_from = $this->options['usernamecol'] . ", ".$this->options['passwordcol'].$this->options['db_fields'];
        }

        $query = sprintf("SELECT %s FROM %s",
                         $sql_from,
                         $this->options['table']
                         );
        $res = $this->db->getAll($query, null, DB_FETCHMODE_ASSOC);

        if (DB::isError($res)) {
            return PEAR::raiseError($res->getMessage(), $res->getCode());
        } else {
            foreach ($res as $user) {
                $user['username'] = $user[$this->options['usernamecol']];
                $retVal[] = $user;
            }
        }
        return $retVal;
    }

    
    

    
    function addUser($username, $password, $additional = "")
    {
        if (function_exists($this->options['cryptType'])) {
            $cryptFunction = $this->options['cryptType'];
        } else {
            $cryptFunction = 'md5';
        }

        $additional_key   = '';
        $additional_value = '';

        if (is_array($additional)) {
            foreach ($additional as $key => $value) {
                $additional_key .= ', ' . $key;
                $additional_value .= ", '" . $value . "'";
            }
        }

        $query = sprintf("INSERT INTO %s (%s, %s%s) VALUES ('%s', '%s'%s)",
                         $this->options['table'],
                         $this->options['usernamecol'],
                         $this->options['passwordcol'],
                         $additional_key,
                         $username,
                         $cryptFunction($password),
                         $additional_value
                         );

        $res = $this->query($query);

        if (DB::isError($res)) {
           return PEAR::raiseError($res->getMessage(), $res->getCode());
        } else {
          return true;
        }
    }

    
    

    
    function removeUser($username)
    {
        $query = sprintf("DELETE FROM %s WHERE %s = '%s'",
                         $this->options['table'],
                         $this->options['usernamecol'],
                         $username
                         );

        $res = $this->query($query);

        if (DB::isError($res)) {
           return PEAR::raiseError($res->getMessage(), $res->getCode());
        } else {
          return true;
        }
    }

    
}

function showDbg( $string ) {
	print "
-- $string</P>";
}
function dump( $var, $str, $vardump = false ) {
	print "<H4>$str</H4><pre>";
	( !$vardump ) ? ( print_r( $var )) : ( var_dump( $var ));
	print "</pre>";
}
?>
