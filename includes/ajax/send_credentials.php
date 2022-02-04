<?php
include '../common_lib.php';

//get user
$user = DBUtil::getRecord('users');

if(!count($user)) {
	echo 'User data not found!';
    exit();
}

if(NotifyUtil::emailFromTemplate('new_user', $user['user_id'])) {
    echo "{$user['fname']} {$user['lname']} - credentials sent";
} else {
    echo 'Something went wrong - please try again!';
}
exit();