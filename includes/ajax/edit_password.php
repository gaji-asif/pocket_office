<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');

$myUser = UserModel::getMe();

$errors = array();
$info = array();
if(RequestUtil::get('submit')) {
	$currentPassword = RequestUtil::get('current_password');
	$newPassword = RequestUtil::get('password');
	$newPassword2 = RequestUtil::get('password_confirm');

	if($currentPassword != $myUser->getPassword()) {
        $errors[] = 'Current password incorrect';
	}
	if($newPassword != $newPassword2) {
		$errors[] = 'Passwords do not match';
	}
	if(strlen($newPassword) < 7) {
		$errors[] = 'New Password too short';
	}

	if(!count($errors)) {
        FormUtil::update('users', $_SESSION['ao_userid']);
		NotifyUtil::emailFromTemplate('password_changed', $myUser->getUserID());
        $info[] = 'Password successfully changed';
	}
}

?>
<form method="post" action="?">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Change Password
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<?=AlertUtil::generate($info, 'info', TRUE)?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td width=150 class="listitem">
            <b>Current Password:</b>
        </td>
        <td class="listrow"><input type="password" name="current_password"></td>
    </tr>
    <tr>
        <td class="listitem">
            <b>New Password:</b>
        </td>
        <td class="listrow">
            <input type="password" name="password"><br />
            <span class="smallnote">New Password must be at least 7 characters.</span>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Confirm New Password:</b>
        </td>
        <td class="listrow"><input type="password" name="password_confirm"></td>
    </tr>
    <tr>
        <td colspan=2 class="listrow" align="right">
            <input type="submit" name="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>