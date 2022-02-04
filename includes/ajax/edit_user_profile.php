<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
$myUser = UserModel::getMe();
if(!$myUser->exists()) {
    UIUtil::showModalError('Could not retrieve user data');
}

$errors = array();
if(RequestUtil::get('submit')) {
    $dba = RequestUtil::get('dba');
    $email = RequestUtil::get('email');
    $phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));
    $sms = RequestUtil::get('sms');

    if(!ValidateUtil::validateEmail($email)) {
        $errors[] = 'Email incorrect format';
    }
    if(UserModel::emailExists($email) && $email != $myUser->get('email')) {
        $errors[] = 'Email in use';
    }
    if(!ValidateUtil::validateUSPhone($phone)) {
        $errors[] = 'Phone incorrect format';
    }
    if(strlen($phone) && UserModel::phoneExists($phone) && $phone != $myUser->get('phone')) {
        $errors[] = 'Phone in use';
    }

    if(!count($errors)) {
        $myUser->updateBasicInfo($dba, $email, $phone, $sms);
?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_basicinformation.php', 'basicinfocontainer', true, true, true);
</script>
<?php
        die();
    }
}

?>
<form method="post" action="?">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>Edit Profile</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td class="listitem"><b>DBA:</b></td>
        <td class="listrow"><input type="text" name="dba" value="<?=$myUser->dba?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Email:</b></td>
        <td class="listrow"><input type="text" name="email" value="<?=$myUser->email?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Phone:</b></td>
        <td class="listrow">
            <input type="text" class="masked-phone" name="phone" value="<?=$myUser->phone?>">
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>SMS Carrier:</b></td>
        <td class="listrow">
            <select name="sms">
                <option value="">No SMS</option>
<?php
$carriers = getAllSmsCarriers();
foreach($carriers as $carrier) {
?>
                <option value="<?=MapUtil::get($carrier, 'sms_id')?>" <?=$myUser->sms_carrier == MapUtil::get($carrier, 'sms_id') ? 'selected' : ''?>>
                    <?=MapUtil::get($carrier, 'carrier')?>
                </option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td colspan=2 class="listrow" align="right">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>