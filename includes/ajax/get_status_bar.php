<?php

include '../common_lib.php';

if(!UserModel::loggedIn())
{
	die();
}

echo ViewUtil::loadView('status-bar');


