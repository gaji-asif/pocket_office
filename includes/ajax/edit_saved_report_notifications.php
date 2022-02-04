<?php
include '../common_lib.php'; 
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
$savedReport = DBUtil::getRecord('saved_reports');
$id = RequestUtil::get('id');
if(!$savedReport) {
    UIUtil::showModalError('Saved report not found!');
}

$errors = array();
if(RequestUtil::get('submit')) {
    
}
?>
<form method="post" action="?id=<?=$id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Modify Saved Report Notifications
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td class="listitem"><b></b></td>
        <td class="listrow">
        </td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input type="submit" name="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>