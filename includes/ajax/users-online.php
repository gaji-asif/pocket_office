<?php
include '../common_lib.php';

$json = new AssureJson();

//authenticate
if (!UserModel::loggedIn()) {
    $json->addError('Not authenticated');
} else {
    $json->addResult('users', AssureChat::getUsersOnline());
}

$json->out();