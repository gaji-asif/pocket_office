<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

$key = RequestUtil::get('key');
$value = RequestUtil::get('value');
$label = ucwords(str_replace('_', ' ', $key));
$assureJson = new AssureJson();

if(!$key) {
    $assureJson->addError('Meta key missing')->out();
}

if(SettingsUtil::set($key, $value)) {
    $assureJson->addSuccess("$label saved")->out();
}
$assureJson->addError("$label could not be saved")->out();