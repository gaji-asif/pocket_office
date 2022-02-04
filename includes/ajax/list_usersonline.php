<?php 

include '../common_lib.php'; 
if(!UserModel::loggedIn()) {
    die();
}

$usersOnline = AssureChat::getUsersOnline();

$userNames = array();
foreach($usersOnline as $user) {
    $userNames[] = '<a href="" class="minibluelink" rel="change-frame-location" data-url="users.php?id=' . $user['user_id'] . '" data-type="user" data-id="' . $user['user_id'] . '">' . $user['fname'][0] . ' ' . $user['lname'] . '</a>';
}

echo implode(', ', $userNames);