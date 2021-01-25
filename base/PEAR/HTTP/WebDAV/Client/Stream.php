<?php
require_once WEB_ROOT . "base/PEAR/HTTP/Request.php";

require WEB_ROOT . "base/PEAR/HTTP/WebDAV/Tools/_parse_propfind_response.php";
require WEB_ROOT . "base/PEAR/HTTP/WebDAV/Tools/_parse_lock_response.php";


define('HTTP_REQUEST_METHOD_COPY',      'COPY',      true);
define('HTTP_REQUEST_METHOD_MOVE',      'MOVE',      true);
define('HTTP_REQUEST_METHOD_MKCOL',     'MKCOL',     true);
define('HTTP_REQUEST_METHOD_PROPFIND',  'PROPFIND',  true);
define('HTTP_REQUEST_METHOD_PROPPATCH', 'PROPPATCH', true);
define('HTTP_REQUEST_METHOD_LOCK',      'LOCK',      true);
define('HTTP_REQUEST_METHOD_UNLOCK',    'UNLOCK',    true);


class HTTP_WebDAV_Client_Stream 
{
     
    var $userAgent = "PEAR::HTTP_WebDAV_Client";

    
    var $contentType = "application/octet-stream";

    
    var $url = false;

    
    var $path = false;

    
    var $position = 0;

    
    var $stat = array();

    
    var $user = false;

    
    var $pass = false;

    
    var $dav_level = array();

    
    var $dav_allow = array();

    
    var $dirfiles = false;

    
    var $dirpos = 0;

    
    var $eof = false;

    
    var $locktoken = false;

    
    function stream_open($path, $mode, $options, &$opened_path) 
    {
        
        if (!$this->_parse_url($path)) return false;

        
        if (!$this->_check_options())  return false;

        
        
        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_PROPFIND);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }
        $req->addHeader("Depth", "0");
        $req->addHeader("Content-Type", "text/xml");
        $req->addRawPostData('<?xml version="1.0" encoding="utf-8"?>
<propfind xmlns="DAV:">
 <prop>
  <resourcetype/>
  <getcontentlength/>
  <getlastmodified />
  <creationdate/>
 </prop>
</propfind>
');
        $req->sendRequest();

        
        switch ($req->getResponseCode()) {
        case 207: 
            
            $propinfo = &new HTTP_WebDAV_Client_parse_propfind_response($req->getResponseBody());
            $this->stat = $propinfo->stat();
            unset($propinfo);
            break;

        case 404: 
            if (preg_match('|[aw\+]|', $mode)) {
                break; 
            } 
            $this->eof = true;
            
        default: 
            error_log("file not found: ".$req->getResponseCode());
            return false;
        }
        
        
        if (strpos($mode, "w") !== false) {
            $req = &$this->_startRequest(HTTP_REQUEST_METHOD_PUT);

            $req->addHeader('Content-length', 0);

            if (is_string($this->user)) {
                $req->setBasicAuth($this->user, @$this->pass);          
            }

            $req->sendRequest();
        }

        
        if (strpos($mode, "a") !== false) {
            $this->position = $this->stat['size'];            
            $this->eof = true;
        }

        
        return true;
    }


    
    function stream_close() 
    {
        
        if ($this->locktoken) {
            $this->stream_lock(LOCK_UN);
        }

        
        $this->url = false;
    }

    
    function stream_stat() 
    {
        
        
        return $this->stat;
    }

    
    function stream_read($count) 
    {
        
        $start = $this->position;
        $end   = $start + $count - 1;

        
        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_GET);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }
        $req->addHeader("Range", "bytes=$start-$end");

        
        $req->sendRequest();
        $data = $req->getResponseBody();
        $len  = strlen($data);

        
        switch ($req->getResponseCode()) {
        case 200: 
            
            
            $data = substr($data, $start, $count);
            break;

        case 206:
            
            break;

        case 416:
            
            $data = "";
            $len  = 0;
            break;

        default: 
            return false;
        }

        
        if (!$len) {
            $this->eof = true;
        }

        
        $this->position += $len;

        
        return $data;
    }

    
    function stream_write($buffer) 
    {
        
        $start = $this->position;
        $end   = $this->position + strlen($buffer);

        
        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_PUT);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }
        $req->addHeader("Content-Range", "bytes $start-$end/*");
        if ($this->locktoken) {
            $req->addHeader("If", "(<{$this->locktoken}>)");
        }
        $req->addRawPostData($buffer);

        
        $req->sendRequest();

        
        switch ($req->getResponseCode()) {
        case 200:
        case 201:
        case 204:
            $this->position += strlen($buffer);
            return $end - $start;
            
        default: 
            return false;
        }

        /* 
         We do not cope with servers that do not support partial PUTs!
         And we do assume that a server does conform to the following 
         rule from RFC 2616 Section 9.6:

         "The recipient of the entity MUST NOT ignore any Content-* 
         (e.g. Content-Range) headers that it does not understand or 
         implement and MUST return a 501 (Not Implemented) response 
         in such cases."
           
         So the worst case scenario with a compliant server not 
         implementing partial PUTs should be a failed request. A 
         server simply ignoring "Content-Range" would replace 
         file contents with the request body instead of putting
         the data at the requested place but we can blame it 
         for not being compliant in this case ;)

         (TODO: maybe we should do a HTTP version check first?)
 
         we *could* emulate partial PUT support by adding local
         cacheing but for now we don't want to as it adds a lot
         of complexity and storage overhead to the client ...
        */
    }

    
    function stream_eof() 
    {
        
        return $this->eof;
    }

    
    function stream_tell() 
    {
        
        return $this->position;
    }

    
    function stream_seek($pos, $whence) 
    {
        switch ($whence) {
        case SEEK_SET:
            
            $this->position = $pos;
            break;
        case SEEK_CUR:
            
            $this->position += $pos;
            break;
        case SEEK_END:
            
            $this->position = $this->stat['size'] + $pos;
            break;
        default: 
            return false;
        }

        
        $this->eof = false;

        return true;
    }


    
    function url_stat($url) 
    {
        
        
        if (!$this->stream_open($url, "r", array(), $dummy)) {
            return false;
        }
        $stat =  $this->stream_stat();
        $this->stream_close();

        return $stat;
    }





    
    function dir_opendir($path, $options) 
    {
		
        
        if (!$this->_parse_url($path)) return false;

        
        if (!$this->_check_options())  return false;

        if (!isset($this->dav_allow[HTTP_REQUEST_METHOD_PROPFIND])) {
            return false;
        }

        
        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_PROPFIND);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }
        $req->addHeader("Depth", "1");
        $req->addHeader("Content-Type", "text/xml");
        $req->addRawPostData('<?xml version="1.0" encoding="utf-8"?>
<propfind xmlns="DAV:">
 <prop>
  <resourcetype/>
  <getcontentlength/>
  <creationdate/>
  <getlastmodified/>
 </prop>
</propfind>
');
        $req->sendRequest();

        switch ($req->getResponseCode()) {
        case 207: 
            $this->dirfiles = array();
            $this->dirpos = 0;

            
            foreach (explode("\n", $req->getResponseBody()) as $line) {
                
                if (preg_match("/href>([^<]*)/", $line, $matches)) {
                    
                    if ($matches[1] == $this->path) {
                        continue;
                    }

                    
                    $this->dirfiles[] = basename($matches[1]);
                }
            }
            return true;

        default: 
            
            error_log("file not found");
            return false;
        }
    }


    
    function dir_readdir() 
    {
        
        if (!is_array($this->dirfiles)) {
            return false;
        }
        
        
        if ($this->dirpos >= count($this->dirfiles)) {
            return false;
        }

        
        return $this->dirfiles[$this->dirpos++];
    }

    
    function dir_rewinddir() 
    {
        
        
        if (!is_array($this->dirfiles)) {
            return false;
        }

        
        $this->dirpos = 0;
    }

    
    function dir_closedir() 
    {
        
        if (is_array($this->dirfiles)) {
            $this->dirfiles = false;
            $this->dirpos = 0;
        }
    }


    
    function mkdir($path) 
    {
        
        if (!$this->_parse_url($path)) return false;

        
        if (!$this->_check_options())  return false;

        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_MKCOL);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }
        if ($this->locktoken) {
            $req->addHeader("If", "(<{$this->locktoken}>)");
        }
        $req->sendRequest();

        
        $stat = $req->getResponseCode();
        switch ($stat) {
        case 201:
            return true;
        default:
            error_log("mkdir failed - ". $stat);
            return false;
        }
    }


    
    function rmdir($path) 
    {
        

        
        if (!$this->_parse_url($path)) return false;

        
        if (!$this->_check_options())  return false;

        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_DELETE);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }
        if ($this->locktoken) {
            $req->addHeader("If", "(<{$this->locktoken}>)");
        }
        $req->sendRequest();

        
        $stat = $req->getResponseCode();
        switch ($stat) {
        case 204:
            return true;
        default:
            error_log("rmdir failed - ". $stat);
            return false;
        }
    }
     

    
    function rename($path, $new_path) 
    {
        
        if (!$this->_parse_url($path)) return false;

        
        if (!$this->_check_options())  return false;

        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_MOVE);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }
        if ($this->locktoken) {
            $req->addHeader("If", "(<{$this->locktoken}>)");
        }
        if (!$this->_parse_url($new_path)) return false;
        $req->addHeader("Destination", $this->url);
        $req->sendRequest();

        
        $stat = $req->getResponseCode();
        switch ($stat) {
        case 201:
        case 204:
            return true;
        default:
            error_log("rename failed - ". $stat);
            return false;
        }
    }
     

    
    function unlink($path) 
    {
        
        if (!$this->_parse_url($path)) return false;

        
        if (!$this->_check_options())  return false;

        
        if (!isset($this->dav_allow[HTTP_REQUEST_METHOD_DELETE])) {
            return false;
        }       

        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_DELETE);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }
        if ($this->locktoken) {
            $req->addHeader("If", "(<{$this->locktoken}>)");
        }
        $req->sendRequest();

        switch ($req->getResponseCode()) {
        case 204: 
            return true;
        default: 
            return false;
        }
    }
        

    
    function register() 
    {
        
        if (!function_exists("stream_register_wrapper")) {
            return false;
        }

        
        if (!stream_register_wrapper("webdav", "HTTP_WebDAV_Client_Stream")) {
            return false;
        }

        
        
        
        stream_register_wrapper("webdavs", "HTTP_WebDAV_Client_Stream");

        return true;
    }


    
    function _parse_url($path) 
    {
        
        $url = parse_url($path);

        
        $scheme = $url['scheme'];
        switch ($scheme) {
        case "webdav":
            $url['scheme'] = "http";
            break;
        case "webdavs":
            $url['scheme'] = "https";
            break;
        default:
            error_log("only 'webdav:' and 'webdavs:' are supported, not '$url[scheme]:'");
            return false;
        }

        if (isset($this->context)) {
            
            $context = stream_context_get_options($this->context);

            
            if (isset($context[$scheme]['user_agent'])) {
                $this->userAgent = $context[$scheme]['user_agent'];
            }

            
            if (isset($context[$scheme]['content_type'])) {
                $this->contentType = $context[$scheme]['content_type'];
            }
            
            
            
        }


        
        if (isset($url['port'])) {
            $url['host'] .= ":$url[port]";
        }

        
        $this->path = $url["path"];

        
        $this->url = "$url[scheme]://$url[host]$url[path]";

        
        if (isset($url['user'])) {
            $this->user = urldecode($url['user']);
        }
        if (isset($url['pass'])) {
            $this->pass = urldecode($url['pass']);
        }

        return true;
    }

    
    function _check_options() 
    {
        
        $req = &$this->_startRequest(HTTP_REQUEST_METHOD_OPTIONS);
        if (is_string($this->user)) {
            $req->setBasicAuth($this->user, @$this->pass);          
        }

        $req->sendRequest();
		
        if ($req->getResponseCode() != 200) {
            return false;
        }

        
        $dav = $req->getResponseHeader("DAV");
		
        $this->dav_level = array();
        foreach (explode(",", $dav) as $level) {
            $this->dav_level[trim($level)] = true;
        }
        if (!isset($this->dav_level["1"])) {
            
            return false;
        }
        
        
        
        $allow = $req->getResponseHeader("Allow");
        $this->dav_allow = array();
        foreach (explode(",", $allow) as $method) {
            $this->dav_allow[trim($method)] = true;
        }

        

        return true;
    }


    
    function stream_lock($mode) 
    {
        
        
        $ret = false;

        
        if (!isset($this->dav_level["2"])) {
            return false;
        }

        switch ($mode & ~LOCK_NB) {
        case LOCK_UN:
            if ($this->locktoken) {
                $req = &$this->_startRequest(HTTP_REQUEST_METHOD_UNLOCK);
                if (is_string($this->user)) {
                    $req->setBasicAuth($this->user, @$this->pass);          
                }
                $req->addHeader("Lock-Token", "<{$this->locktoken}>");
                $req->sendRequest();

                $ret = $req->getResponseCode() == 204;
            }
            break;

        case LOCK_SH:
        case LOCK_EX:
            $body = sprintf('<?xml version="1.0" encoding="utf-8" ?> 
<D:lockinfo xmlns:D="DAV:"> 
 <D:lockscope><D:%s/></D:lockscope> 
 <D:locktype><D:write/></D:locktype> 
 <D:owner>%s</D:owner> 
</D:lockinfo>',
                            ($mode & LOCK_SH) ? "shared" : "exclusive",
                            get_class($this)); 
            $req = &$this->_startRequest(HTTP_REQUEST_METHOD_LOCK);
            if (is_string($this->user)) {
                $req->setBasicAuth($this->user, @$this->pass);          
            }
            if ($this->locktoken) { 
                $req->addHeader("Lock-Token", "<{$this->locktoken}>");
            }
            $req->addHeader("Timeout", "Infinite, Second-4100000000");
            $req->addHeader("Content-Type", 'text/xml; charset="utf-8"');
            $req->addRawPostData($body);
            $req->sendRequest();

            $ret = $req->getResponseCode() == 200;          

            if ($ret) {
                $propinfo = &new HTTP_WebDAV_Client_parse_lock_response($req->getResponseBody());               
                $this->locktoken = $propinfo->locktoken;
                
            }
            break;
            
        default:
            break;
        }

        return $ret;
    }

    function &_startRequest($method)
    {
        $req = &new HTTP_Request($this->url);

        $req->addHeader('User-agent',   $this->userAgent);
        $req->addHeader('Content-type', $this->contentType);

        $req->setMethod($method);

        return $req;        
    }
}


