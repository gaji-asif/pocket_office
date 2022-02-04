<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

$action = RequestUtil::get('action');
$key = RequestUtil::get('key');
$value = RequestUtil::get('value');

if($action == 'set' && $key) {
    SettingsUtil::set($key, $value);
}
?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0">
    <tr>
        <td width="25%" class="listitem "><b>Daily schedule</b></td>
        <td class="listrow">
            <input type="checkbox" data-key="daily_schedule" data-alert="true"
                   class="onchange-set-meta" data-type="user" <?=SettingsUtil::get('daily_schedule') == '1' ? 'checked' : ''?>>
        </td>
    </tr>
</table>