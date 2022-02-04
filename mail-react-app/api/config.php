<?php
if ($_SERVER['HTTP_HOST'] === 'localhost'){
  ini_set('display_errors', 'on');
  $mysqli = new mysqli("localhost", "root", "root", "xactbid_web");
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
  }
  $GLOBALS['SERVER_URI'] = 'http://localhost/xactbid/';
} else {
  $mysqli = new mysqli("pocketofficepro.com", "pocketoffice_xactbid", "Qs(o6X!;m8#L", "pocketoffice_xactbid");
  if ($mysqli->connect_errno) {
    echo "Failed to connect to MySQL: " . $mysqli->connect_error;
    exit();
  }
  $GLOBALS['SERVER_URI'] = 'https://xactbid.pocketofficepro.com';
}