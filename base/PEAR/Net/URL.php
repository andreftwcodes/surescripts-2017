<?php






































class Net_URL
{
    var $options = array('encode_query_keys' => false);
    
    var $url;

    
    var $protocol;

    
    var $username;

    
    var $password;

    
    var $host;

    
    var $port;

    
    var $path;

    
    var $querystring;

    
    var $anchor;

    
    var $useBrackets;

    
    function Net_URL($url = null, $useBrackets = true)
    {
        $this->__construct($url, $useBrackets);
    }

    
    function __construct($url = null, $useBrackets = true)
    {
        $this->url = $url;
        $this->useBrackets = $useBrackets;

        $this->initialize();
    }

    function initialize()
    {
        $HTTP_SERVER_VARS  = !empty($_SERVER) ? $_SERVER : $GLOBALS['HTTP_SERVER_VARS'];

        $this->user        = '';
        $this->pass        = '';
        $this->host        = '';
        $this->port        = 80;
        $this->path        = '';
        $this->querystring = array();
        $this->anchor      = '';

        
        if (!preg_match('/^[a-z0-9]+:\/\//i', $this->url)) {
            $this->protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on' ? 'https' : 'http');

            
            if (!empty($HTTP_SERVER_VARS['HTTP_HOST']) && 
                preg_match('/^(.*)(:([0-9]+))?$/U', $HTTP_SERVER_VARS['HTTP_HOST'], $matches)) 
            {
                $host = $matches[1];
                if (!empty($matches[3])) {
                    $port = $matches[3];
                } else {
                    $port = $this->getStandardPort($this->protocol);
                }
            }

            $this->user        = '';
            $this->pass        = '';
            $this->host        = !empty($host) ? $host : (isset($HTTP_SERVER_VARS['SERVER_NAME']) ? $HTTP_SERVER_VARS['SERVER_NAME'] : 'localhost');
            $this->port        = !empty($port) ? $port : (isset($HTTP_SERVER_VARS['SERVER_PORT']) ? $HTTP_SERVER_VARS['SERVER_PORT'] : $this->getStandardPort($this->protocol));
            $this->path        = !empty($HTTP_SERVER_VARS['PHP_SELF']) ? $HTTP_SERVER_VARS['PHP_SELF'] : '/';
            $this->querystring = isset($HTTP_SERVER_VARS['QUERY_STRING']) ? $this->_parseRawQuerystring($HTTP_SERVER_VARS['QUERY_STRING']) : null;
            $this->anchor      = '';
        }

        
        if (!empty($this->url)) {
            $urlinfo = parse_url($this->url);

            
            $this->querystring = array();

            foreach ($urlinfo as $key => $value) {
                switch ($key) {
                    case 'scheme':
                        $this->protocol = $value;
                        $this->port     = $this->getStandardPort($value);
                        break;

                    case 'user':
                    case 'pass':
                    case 'host':
                    case 'port':
                        $this->$key = $value;
                        break;

                    case 'path':
                        if ($value{0} == '/') {
                            $this->path = $value;
                        } else {
                            $path = dirname($this->path) == DIRECTORY_SEPARATOR ? '' : dirname($this->path);
                            $this->path = sprintf('%s/%s', $path, $value);
                        }
                        break;

                    case 'query':
                        $this->querystring = $this->_parseRawQueryString($value);
                        break;

                    case 'fragment':
                        $this->anchor = $value;
                        break;
                }
            }
        }
    }
    
    function getURL()
    {
        $querystring = $this->getQueryString();

        $this->url = $this->protocol . '://'
                   . $this->user . (!empty($this->pass) ? ':' : '')
                   . $this->pass . (!empty($this->user) ? '@' : '')
                   . $this->host . ($this->port == $this->getStandardPort($this->protocol) ? '' : ':' . $this->port)
                   . $this->path
                   . (!empty($querystring) ? '?' . $querystring : '')
                   . (!empty($this->anchor) ? '#' . $this->anchor : '');

        return $this->url;
    }

    
    function addQueryString($name, $value, $preencoded = false)
    {
        if ($this->getOption('encode_query_keys')) {
            $name = rawurlencode($name);
        }

        if ($preencoded) {
            $this->querystring[$name] = $value;
        } else {
            $this->querystring[$name] = is_array($value) ? array_map('rawurlencode', $value): rawurlencode($value);
        }
    }

    
    function removeQueryString($name)
    {
        if ($this->getOption('encode_query_keys')) {
            $name = rawurlencode($name);
        }

        if (isset($this->querystring[$name])) {
            unset($this->querystring[$name]);
        }
    }

    
    function addRawQueryString($querystring)
    {
        $this->querystring = $this->_parseRawQueryString($querystring);
    }

    
    function getQueryString()
    {
        if (!empty($this->querystring)) {
            foreach ($this->querystring as $name => $value) {
                
                $name = rawurlencode($name);

                if (is_array($value)) {
                    foreach ($value as $k => $v) {
                        $querystring[] = $this->useBrackets ? sprintf('%s[%s]=%s', $name, $k, $v) : ($name . '=' . $v);
                    }
                } elseif (!is_null($value)) {
                    $querystring[] = $name . '=' . $value;
                } else {
                    $querystring[] = $name;
                }
            }
            $querystring = implode(ini_get('arg_separator.output'), $querystring);
        } else {
            $querystring = '';
        }

        return $querystring;
    }

    
    function _parseRawQuerystring($querystring)
    {
        $parts  = preg_split('/[' . preg_quote(ini_get('arg_separator.input'), '/') . ']/', $querystring, -1, PREG_SPLIT_NO_EMPTY);
        $return = array();

        foreach ($parts as $part) {
            if (strpos($part, '=') !== false) {
                $value = substr($part, strpos($part, '=') + 1);
                $key   = substr($part, 0, strpos($part, '='));
            } else {
                $value = null;
                $key   = $part;
            }

            if (!$this->getOption('encode_query_keys')) {
                $key = rawurldecode($key);
            }

            if (preg_match('#^(.*)\[([0-9a-z_-]*)\]#i', $key, $matches)) {
                $key = $matches[1];
                $idx = $matches[2];

                
                if (empty($return[$key]) || !is_array($return[$key])) {
                    $return[$key] = array();
                }

                
                if ($idx === '') {
                    $return[$key][] = $value;
                } else {
                    $return[$key][$idx] = $value;
                }
            } elseif (!$this->useBrackets AND !empty($return[$key])) {
                $return[$key]   = (array)$return[$key];
                $return[$key][] = $value;
            } else {
                $return[$key] = $value;
            }
        }

        return $return;
    }

    
    function resolvePath($path)
    {
        $path = explode('/', str_replace('//', '/', $path));

        for ($i=0; $i<count($path); $i++) {
            if ($path[$i] == '.') {
                unset($path[$i]);
                $path = array_values($path);
                $i--;

            } elseif ($path[$i] == '..' AND ($i > 1 OR ($i == 1 AND $path[0] != '') ) ) {
                unset($path[$i]);
                unset($path[$i-1]);
                $path = array_values($path);
                $i -= 2;

            } elseif ($path[$i] == '..' AND $i == 1 AND $path[0] == '') {
                unset($path[$i]);
                $path = array_values($path);
                $i--;

            } else {
                continue;
            }
        }

        return implode('/', $path);
    }

    
    function getStandardPort($scheme)
    {
        switch (strtolower($scheme)) {
            case 'http':    return 80;
            case 'https':   return 443;
            case 'ftp':     return 21;
            case 'imap':    return 143;
            case 'imaps':   return 993;
            case 'pop3':    return 110;
            case 'pop3s':   return 995;
            default:        return null;
       }
    }

    
    function setProtocol($protocol, $port = null)
    {
        $this->protocol = $protocol;
        $this->port     = is_null($port) ? $this->getStandardPort($protocol) : $port;
    }

    
    function setOption($optionName, $value)
    {
        if (!array_key_exists($optionName, $this->options)) {
            return false;
        }

        $this->options[$optionName] = $value;
        $this->initialize();
    }

    
    function getOption($optionName)
    {
        if (!isset($this->options[$optionName])) {
            return false;
        }

        return $this->options[$optionName];
    }

}
?>
