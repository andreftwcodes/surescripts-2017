<?php

require_once WEB_ROOT . "base/PEAR/HTTP/WebDAV/Client/Stream.php";

if (!HTTP_WebDAV_Client_Stream::register()) {
    PEAR::raiseError("couldn't register WebDAV stream wrappers");
}

?>
