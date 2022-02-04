<?php

ini_set('display_errors','On');

include 'includes/common_lib.php';



$loggedIn = UserModel::loggedIn();

$page = RequestUtil::get('p');

$id = RequestUtil::get('id');

$iframeSrc = ($page && $id) ? "$page.php?id=$id" : 'dashboard.php';



//print '<pre>';print_r($_POST);die();

if(RequestUtil::get('action') == 'logout') {

    logout();

    header('Location: login.php');

    die();

}



if(!$loggedIn) {

    header('Location: login.php?' . http_build_query($_GET));

    die();

}



echo ViewUtil::loadView('doc-head', array('body_class' => 'app-frameset'));

echo ViewUtil::loadView('app-iframe', array('iframeSrc' => $iframeSrc, 'id' => 'app-iframe'));

//echo ViewUtil::loadView('app-iframe', array('iframeSrc' => NULL, 'id' => 'app-detail-iframe'));

if($loggedIn) {

    echo ViewUtil::loadView('sidebar');

    echo ViewUtil::loadView('status-bar');

    echo AssureChat::loadChatApp();

    echo ViewUtil::loadView('user-voice');

}