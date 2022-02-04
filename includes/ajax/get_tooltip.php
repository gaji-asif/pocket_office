<?php 
include '../common_lib.php'; 
if(!UserModel::loggedIn()) { return; }

$type = ucfirst(RequestUtil::get('type'));
$id = RequestUtil::get('id');

$object = class_exists($type) ? new $type($id, FALSE) : NULL;
if(!$object || !method_exists($object, 'getTooltip')) {
    return;
}

$object->getToolTip();