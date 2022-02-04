<?php
include '../common_lib.php';
if(!UserModel::loggedIn()) {
    die();
}

$json = new AssureJson();
$chat = new AssureChat($_SESSION['ao_userid']);

//$json->addResult('total_chats', count($chatData));
$json->addResult('chats', $chat->getAllChats());


//echo '<pre>';
//echo AssureChat::getSumNewMessages($_SESSION['ao_userid']);
//print_r($chat);
$json->out();