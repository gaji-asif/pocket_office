<?php
include '../common_lib.php';

AuthModel::setSessionStart();

$json = new AssureJson();

//authenticate
if (!UserModel::loggedIn()) {
    $json->addError('Not authenticated');
    $json->out();
}

//get request data
$action = RequestUtil::get('action');
$id = RequestUtil::get('id');
$text = RequestUtil::get("text");

//retrieve
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (!empty($id)) {
        $userData = UserModel::get($id);
        if($userData) {
            //get data
            $chat = new AssureChat($_SESSION['ao_userid']);
            $json->addResult('user_id', $id);
            $json->addResult('fname', $userData['fname']);
            $json->addResult('lname', $userData['lname']);
            $json->addResult('messages', $chat->getMessages($id));
        }
    }
    //user list
    else if($action == 'users') {
        $chat = new AssureChat($_SESSION['ao_userid']);
        $json->addResult('users', AssureChat::getUsersOnline());
        $json->addResult('usersNewMessages', $chat->getSumNewMessagesGroupedByUser());
        $json->addResult('new', $chat->getSumNewMessages());
        
        //update session
        UserModel::updateSession();
    }
    //new messages
    else if($action == 'new') {
        $chat = new AssureChat($_SESSION['ao_userid']);
        $json->addResult('new', $chat->getSumNewMessages());
    }
    //get all message data
    else {
        $chat = new AssureChat($_SESSION['ao_userid']);
        $json->addResult('chats', $chat->getAllChats());
        $json->addResult('new', $chat->getSumNewMessages());
    }
}
//create
else if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($action == 'update') {    //mark all messages with a certain user as read
        $chat = new AssureChat($_SESSION['ao_userid']);
        if($chat->markChatRead($id)) {
            $json->addSuccess('Message updated');
        } else {
            $json->addError('Message(s) not updated');
        }
    }
    else {
        $chat = new AssureChat($_SESSION['ao_userid']);
        $messages = $chat->postMessage($id, $text);
        if($messages) {
            $json->addResult('messages', $messages);
            $json->addResult('user_id', $id);
        } else {
            $json->addError('Post failed');
        }
    }
} else {
    $json->addError('Unknown request');
}

//output
$json->out();
