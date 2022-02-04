<?php
include '../common_lib.php';
UserModel::isAuthenticated();

if(!ModuleUtil::checkIsFounder(FALSE)) {
	JsonUtil::error('Insufficient rights');
	die();
}

$key = RequestUtil::get('key');
$value = RequestUtil::get('value');
$label = ucwords(str_replace('_', ' ', $key));

if(is_array($value)) {
    $value = implode(',', $value);
}

if(AccountModel::setMetaValue($key, $value)) {
	JsonUtil::success("$label saved");
}
else {
	JsonUtil::error("$label could not be saved");
}