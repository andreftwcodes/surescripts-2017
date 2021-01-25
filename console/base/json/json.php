<?php
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    define('JSON_SLICE',   1);
    define('JSON_IN_STR',  2);
    define('JSON_IN_ARR',  4);
    define('JSON_IN_OBJ',  8);
    define('JSON_IN_CMT', 16);
    define('JSON_LOOSE_TYPE', 10);
    define('JSON_STRICT_TYPE', 11);
    
   
    class JSON
    {
       
        function JSON($use=JSON_STRICT_TYPE)
        {
            $this->use = $use;
            
            $this->mbstring_enabled=0;
            $this->mbstring_language=ini_get("mbstring.language");
            if (isset($this->mbstring_language) && !empty($this->mbstring_language))
            	$this->mbstring_enabled=1;            
        }

       
        function encode($var)
        {
       
            switch(gettype($var)) {
                case 'boolean':
                    return $var ? 'true' : 'false';
                
                case 'NULL':
                    return 'null';
                
                case 'integer':
                    return sprintf('%d', $var);
                    
                case 'double':
                case 'float':
                    return sprintf('%f', $var);
                    
                case 'string': 
                    $ascii = '';

    
                    for($c = 0; $c < strlen($var); $c++) {
                
                        if(ord($var{$c}) == 0x08) {
                            $ascii .= '\b';
                        
                        } elseif(ord($var{$c}) == 0x09) {
                            $ascii .= '\t';
                        
                        } elseif(ord($var{$c}) == 0x0A) {
                            $ascii .= '\n';
                        
                        } elseif(ord($var{$c}) == 0x0C) {
                            $ascii .= '\f';
                        
                        } elseif(ord($var{$c}) == 0x0D) {
                            $ascii .= '\r';
                        
                        } elseif((ord($var{$c}) == 0x22) || (ord($var{$c}) == 0x2F) || (ord($var{$c}) == 0x5C)) {
                            $ascii .= '\\'.$var{$c}; 
                        
                        } elseif((ord($var{$c}) >= 0x20) && (ord($var{$c}) <= 0x7F)) {
		                    
		                    $ascii .= $var{$c}; 
		                } else { 
		                	$this->multibyte=false;
		                	if ($this->mbstring_enabled && !empty($var) && (mb_strlen($var) != strlen($var))) {
		                		$this->multibyte=true;		                		
		                	}
		                	
                        	if ($this->multibyte == false) {
		                    	$ascii .= $var{$c};		                                        	
                        	} else {
 								if((ord($var{$c}) & 0xE0) == 0xC0) {
		                            
		                            $char = pack('C*', ord($var{$c}), ord($var{$c+1})); $c+=1;
		                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
		    
		                        } elseif((ord($var{$c}) & 0xF0) == 0xE0) {
		                            
		                            $char = pack('C*', ord($var{$c}), ord($var{$c+1}), ord($var{$c+2})); $c+=2;
		                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
		    
		                        } elseif((ord($var{$c}) & 0xF8) == 0xF0) {
		                            
		                            $char = pack('C*', ord($var{$c}), ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3})); $c+=3;
		                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
		    
		                        } elseif((ord($var{$c}) & 0xFC) == 0xF8) {
		                            
		                            $char = pack('C*', ord($var{$c}), ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3}), ord($var{$c+4})); $c+=4;
		                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
		    
		                        } elseif((ord($var{$c}) & 0xFE) == 0xFC) {
		                            
		                            $char = pack('C*', ord($var{$c}), ord($var{$c+1}), ord($var{$c+2}), ord($var{$c+3}), ord($var{$c+4}), ord($var{$c+5})); $c+=5;
		                            $ascii .= sprintf('\u%04s', bin2hex(mb_convert_encoding($char, 'UTF-16', 'UTF-8')));
		    
		                        }
	                        }
                        }
                    }
                    
                    return sprintf('"%s"', $ascii);
                    
                case 'array':
                    
            		
            		
            		
            		
                    
                    
                    
                    
                    
                    if(is_array($var) && (array_keys($var) !== range(0, sizeof($var) - 1)))
                        return sprintf('{%s}', join(',', array_map(array($this, 'name_value'), array_keys($var), array_values($var))));

                    
                    return sprintf('[%s]', join(',', array_map(array($this, 'encode'), $var)));
                    
                case 'object':
                    $vars = get_object_vars($var);
                    return sprintf('{%s}', join(',', array_map(array($this, 'name_value'), array_keys($vars), array_values($vars))));                    

                default:
                    return '';
            }
        }
        
       
        function enc($var)
        {
            return $this->encode($var);
        }
        
       
        function name_value($name, $value)
        {
            return (sprintf("%s:%s", $this->encode(strval($name)), $this->encode($value)));
        }        
       
        function decode($str)
        {
            $str = preg_replace('#^\s*//(.+)$#m', '', $str); // eliminate single line comments in '// ...' form
            $str = preg_replace('#^\s*/\*(.+)\*/#Us', '', $str); 
            $str = preg_replace('#/\*(.+)\*/\s*$#Us', '', $str); 
            $str = trim($str); 
        
            switch(strtolower($str)) {
                case 'true':
                    return true;
    
                case 'false':
                    return false;
                
                case 'null':
                    return null;
                
                default:
                    if(is_numeric($str)) { 
                        
                        return ((float)$str == (integer)$str)
                            ? (integer)$str
                            : (float)$str;
                        
                    } elseif(preg_match('/^".+"$/s', $str) || preg_match('/^\'.+\'$/s', $str)) { 
                        $delim = substr($str, 0, 1);
                        $chrs = substr($str, 1, -1);
                        $utf8 = '';
                        
                        for($c = 0; $c < strlen($chrs); $c++) {
                        
                            if(substr($chrs, $c, 2) == '\b') {
                                $utf8 .= chr(0x08); $c+=1;
    
                            } elseif(substr($chrs, $c, 2) == '\t') {
                                $utf8 .= chr(0x09); $c+=1;
    
                            } elseif(substr($chrs, $c, 2) == '\n') {
                                $utf8 .= chr(0x0A); $c+=1;
    
                            } elseif(substr($chrs, $c, 2) == '\f') {
                                $utf8 .= chr(0x0C); $c+=1;
    
                            } elseif(substr($chrs, $c, 2) == '\r') {
                                $utf8 .= chr(0x0D); $c+=1;
    
                            } elseif(($delim == '"') && ((substr($chrs, $c, 2) == '\\"') || (substr($chrs, $c, 2) == '\\\\') || (substr($chrs, $c, 2) == '\\/'))) {
                                $utf8 .= $chrs{++$c};
    
                            } elseif(($delim == "'") && ((substr($chrs, $c, 2) == '\\\'') || (substr($chrs, $c, 2) == '\\\\') || (substr($chrs, $c, 2) == '\\/'))) {
                                $utf8 .= $chrs{++$c};
    
                            } elseif(preg_match('/\\\u[0-9A-F]{4}/i', substr($chrs, $c, 6))) { 
                                $utf16 = chr(hexdec(substr($chrs, ($c+2), 2))) . chr(hexdec(substr($chrs, ($c+4), 2)));
                                $utf8 .= mb_convert_encoding($utf16, 'UTF-8', 'UTF-16');
                                $c+=5;
    
                            } elseif((ord($chrs{$c}) >= 0x20) && (ord($chrs{$c}) <= 0x7F)) {
                                $utf8 .= $chrs{$c};
    
                            }
                        
                        }
                        
                        return $utf8;
                    
                    } elseif(preg_match('/^\[.+\]$/s', $str) || preg_match('/^{.+}$/s', $str)) { 
    
                        if($str{0} == '[') {
                            $stk = array(JSON_IN_ARR);
                            $arr = array();
                        } else {
                            if($this->use == JSON_LOOSE_TYPE) {
                                $stk = array(JSON_IN_OBJ);
                                $obj = array();
                            } else {
                                $stk = array(JSON_IN_OBJ);
                                $obj = new ObjectFromJSON();
                            }
                        }
    
                        array_push($stk, array('what' => JSON_SLICE, 'where' => 0, 'delim' => false));
                        $chrs = substr($str, 1, -1);
                        
                        
                        
                        for($c = 0; $c <= strlen($chrs); $c++) {
                        
                            $top = end($stk);
                        
                            if(($c == strlen($chrs)) || (($chrs{$c} == ',') && ($top['what'] == JSON_SLICE))) { 
                                $slice = substr($chrs, $top['where'], ($c - $top['where']));
                                array_push($stk, array('what' => JSON_SLICE, 'where' => ($c + 1), 'delim' => false));
                                
    
                                if(reset($stk) == JSON_IN_ARR) { 
                                    array_push($arr, $this->decode($slice));
    
                                } elseif(reset($stk) == JSON_IN_OBJ) { 
                                    if(preg_match('/^\s*(["\'].*[^\\\]["\'])\s*:\s*(\S.*),?$/Uis', $slice, $parts)) { 
                                        $key = $this->decode($parts[1]);
                                        $val = $this->decode($parts[2]);

                                        if($this->use == JSON_LOOSE_TYPE) {
                                            $obj[$key] = $val;
                                        } else {
                                            $obj->$key = $val;
                                        }
                                    }
    
                                }
    
                            } elseif((($chrs{$c} == '"') || ($chrs{$c} == "'")) && ($top['what'] != JSON_IN_STR)) { 
                                array_push($stk, array('what' => JSON_IN_STR, 'where' => $c, 'delim' => $chrs{$c}));
                                
    
                            } elseif(($chrs{$c} == $top['delim']) && ($top['what'] == JSON_IN_STR) && ($chrs{$c - 1} != "\\")) { 
                                array_pop($stk);
                                
    
                            } elseif(($chrs{$c} == '[') && in_array($top['what'], array(JSON_SLICE, JSON_IN_ARR, JSON_IN_OBJ))) { 
                                array_push($stk, array('what' => JSON_IN_ARR, 'where' => $c, 'delim' => false));
                                
    
                            } elseif(($chrs{$c} == ']') && ($top['what'] == JSON_IN_ARR)) { 
                                array_pop($stk);
                                
    
                            } elseif(($chrs{$c} == '{') && in_array($top['what'], array(JSON_SLICE, JSON_IN_ARR, JSON_IN_OBJ))) { 
                                array_push($stk, array('what' => JSON_IN_OBJ, 'where' => $c, 'delim' => false));
                                
    
                            } elseif(($chrs{$c} == '}') && ($top['what'] == JSON_IN_OBJ)) { 
                                array_pop($stk);
                                
    
                            } elseif((substr($chrs, $c, 2) == '/*') && in_array($top['what'], array(JSON_SLICE, JSON_IN_ARR, JSON_IN_OBJ))) { 
                                array_push($stk, array('what' => JSON_IN_CMT, 'where' => $c, 'delim' => false));
                                $c++;
                                
    
                            } elseif((substr($chrs, $c, 2) == '*/') && ($top['what'] == JSON_IN_CMT)) { 
                                array_pop($stk);
                                $c++;
                                
                                for($i = $top['where']; $i <= $c; $i++)
                                    $chrs = substr_replace($chrs, ' ', $i, 1);
                                
                                
    
                            }
                        
                        }
                        
                        if(reset($stk) == JSON_IN_ARR) {
                            return $arr;
    
                        } elseif(reset($stk) == JSON_IN_OBJ) {
                            return $obj;
    
                        }
                    
                    }
            }
        }
        
       
        function dec($var)
        {
            return $this->decode($var);
        }
        
    }

   
    class ObjectFromJSON { function ObjectFromJSON() {} }
    
?>