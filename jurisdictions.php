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
    $job = DBUtil::getRecord('jobs', $id, 'jurisdiction');
    if(count($job)) {
        $errors[] = 'Jobs currently associated - cannot remove';
    }
    else {
          $sql = "DELETE FROM jurisdiction
                WHERE jurisdiction_id = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
                DBUtil::query($sql);
        $sql = "DELETE FROM jurisdiction_additionals
                WHERE jurisdiction_id = '$id'
                LIMIT 1";
        DBUtil::query($sql);
        $info[] = "Jurisdiction successfully removed";
    }
}

?>
<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Jurisdictions</h1>
<div class="btn-group pull-right page-menu">
    <div rel="open-modal" data-script="add_jurisdiction.php" class="btn btn-success" title="Add jurisdiction" tooltip>
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
$jurisdictions = CustomerModel::getAllJurisdictions();
foreach($jurisdictions as $jurisdiction) {
?>
            <tr>
                <td><?=$jurisdiction['location']?></td>
                <td class="text-center">
                    <div class="btn-group">
                        <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/jurisdictions.php?id=<?=$jurisdiction['jurisdiction_id']?>&action=del" data-confirm="Are you sure you want to remove jurisdiction '<?=$jurisdiction['location']?>'?" title="Delete '<?=$jurisdiction['location']?>'" tooltip>
                            <i class="icon-trash"></i>
                        </a>
                        <a href="" class="btn btn-small" rel="open-modal" data-script="edit_jurisdiction.php?id=<?=$jurisdiction['jurisdiction_id']?>" title="Edit '<?=$jurisdiction['location']?>'" tooltip>
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
