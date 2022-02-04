<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}

$id = RequestUtil::get('id');
$action = RequestUtil::get('action');
$status = RequestUtil::get('status');
$color = RequestUtil::get('color');

if(RequestUtil::get('submit-new')) {
    if(!empty($status) && !empty($color)) {
        $sql = "INSERT INTO status
                VALUES (NULL, '{$_SESSION['ao_accountid']}', '$status', '$color')";
        DBUtil::query($sql);
        UIUtil::showAlert('New status hold added');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(RequestUtil::get('submit-edit')) {
    if(!empty($status) && !empty($color) && !empty($id)) {
        FormUtil::update('status');
        UIUtil::showAlert('Status hold modified');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(!empty($id) && $action == 'del') {
    $statusHold = DBUtil::getRecord('status_holds', $id, 'status_id');
    if(count($statusHold)) {
        UIUtil::showAlert('Jobs currently associated - cannot remove');
    } else {
        $sql = "DELETE FROM status
                WHERE status_id = '$id' and account_id='{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        UIUtil::showAlert('Status hold has been removed.');
    }
}
?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Status Holds</h1>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
                <tr>
                    <td>Add Status Hold</td>
                </tr>
            </table>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="infocontainer">
                <tr>
                    <td width="15%">
                        <form method="post" action="?" name="add-status-hold">
                        <b>Status Hold Name:</b>
                    </td>
                    <td>
                        <input type="text" name="status" size="30">
                    </td>
                </tr>
                <tr>
                    <td width="100">
                        <b>Color:</b>
                    </td>
                    <td>
                        <input size="10" class="color {hash:true}" rel="star-color-picker" data-preview-id="preview-circle" value="CCC" name="color">
                        <i id="preview-circle" class="icon-circle" style="color: #CCC;"></i>
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <input name="submit-new" type="submit" value="Add">
                        </form>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<br />
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
    <tr>
        <td>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
                <tr>
                    <td width="250">
                      Status Holds
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table align="center" border="0" cellpadding="2" cellspacing="0" width="100%" class="infocontainernopadding">
<?php
$statusHolds = JobModel::getAllStatuses();
foreach($statusHolds as $key => $statusHold) {
    $class = $key % 2 == 0 ? 'even' : 'odd';
?>
                <form method="post" action="?">
                <tr class="<?=$class?>">
                    <td width="20">
                        <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/statusholds.php?id=<?=$statusHold['status_id']?>&action=del" data-confirm="Are you sure you want to remove status hold '<?=$statusHold['status']?>'?" title="Delete '<?=$statusHold['status']?>'" tooltip>
                            <i class="icon-trash"></i>
                        </a>
                    </td>
                    <td>
                        <input type="text" name="status" value="<?=$statusHold['status']?>" id="status-hold-name-<?=$statusHold['status_id']?>" style="color: <?=$statusHold['color']?>" />
                    </td>
                    <td align="right">
                        <b>Edit Color:</b>
                        <input size="10" class="color {hash:true}" id="color-input-<?=$statusHold['status_id']?>" rel="star-color-picker" data-preview-id="status-hold-name-<?=$statusHold['status_id']?>" value="<?=$statusHold['color']?>" name="color">
                        <input type="hidden" name="id" value="<?=$statusHold['status_id']?>">
                        <input name="submit-edit" type="submit" value="Go">
                        <input type="button" value="Reset" onclick="$('#color-input-<?=$statusHold['status_id']?>')[0].color.fromString('<?=$statusHold['color']?>'); $('#color-input-<?=$statusHold['status_id']?>').trigger('change');">
                    </td>
                </tr>
                </form>
<?php
  $i++;
}
if(!count($statusHolds)) {
?>
                <tr>
                    <td align="center"><b>No Status Holds Found</b></td>
                </tr>
<?php
}
?>
            </table>
        </td>
    </tr>
</table>
</body>
</html>
