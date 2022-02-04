<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
if(!ModuleUtil::checkIsFounder()) {
    UIUtil::showListError('You do not have permission to access this.');
}

$id = RequestUtil::get('id');
$action = RequestUtil::get('action');

$errors = array();
$info = array();
if ($action === 'del' && $id) {
    $user = DBUtil::getRecord('users', $id, 'office_id');
    if(count($user)) {
        $errors[] = 'Users currently associated - cannot remove';
    }
    else {
        $sql = "DELETE FROM offices
                WHERE office_id = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        $info[] = "Office successfully removed";
    }
}

?>
<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Offices</h1>
<div class="btn-group pull-right page-menu">
    <div rel="open-modal" data-script="add_office.php" class="btn btn-success" title="Add office" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>
<div class="list-table-container">
    <div class="container">
<?php
if(count($errors)) {
?>
        <div class="row">
            <?=AlertUtil::generate($errors, 'error', FALSE)?>
        </div>
<?php
}
if(count($info)) {
?>
        <div class="row">
            <?=AlertUtil::generate($info, 'info', FALSE)?>
        </div>
<?php
}
?>
    </div>
    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>Name</th>
                <th width="10%" class="text-center">Actions</th>
            </tr>
        </thead>
        <tbody>
<?php
$offices = AccountModel::getAllOffices();
foreach($offices as $office) {
?>
            <tr>
                <td><?=$office['title']?></td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/modoffices.php?id=<?=$office['office_id']?>&action=del" data-confirm="Are you sure you want to remove office '<?=$office['title']?>'?" title="Delete '<?=$office['title']?>'" tooltip>
                            <i class="icon-trash"></i>
                        </a>
                        <a href="" class="btn btn-small" rel="open-modal" data-script="edit_office.php?id=<?=$office['office_id']?>" title="Edit '<?=$office['title']?>'" tooltip>
                            <i class="icon-pencil"></i>
                        </a>
                    </div>
                </td>
            </tr>
<?php
}
?>
        </tbody>
    </table>
</div>
</body>
</html>