<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
if(!ModuleUtil::checkIsFounder()) {
    UIUtil::showListError('You do not have permission to access this.');
}

$id = RequestUtil::get('id');
$action = RequestUtil::get('action');
$name = RequestUtil::get('name');

$errors = array();
$info = array();
if (RequestUtil::get('new')) {
    if (!$name) {
        $errors[] = 'Name cannot be blank';
    }
    if (!count($errors)) {
        $sql = "INSERT INTO origins (account_id, origin)
                VALUES ('{$_SESSION['ao_accountid']}', '$name')";
        DBUtil::query($sql);
        $info[] = "'$name' successfully added";
    }
} else if ($action === 'del' && $id) {
    $job = DBUtil::getRecord('jobs', $id, 'origin');
    if(count($job)) {
        $errors[] = 'Jobs currently associated - cannot remove';
    }
    
    if (!count($errors)) {
        $sql = "DELETE FROM origins
                WHERE origin_id = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        $info[] = "Job origin successfully removed";
    }
}

?>
<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Job Origins</h1>
<div class="list-table-container">
    <div class="container">
        <div class="row">
            <form method="post" action="?" name="origin">
            <input type="text" name="name" size="50">
            <input type="submit" name="new" value="Add">
            </form>
        </div>
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
$origins = JobUtil::getAllOrigins();
foreach($origins as $origin) {
?>
            <tr>
                <td><?=$origin['origin']?></td>
                <td class="text-center">
                    <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/joborigins.php?id=<?=$origin['origin_id']?>&action=del" data-confirm="Are you sure you want to remove job origin '<?=$origin['origin']?>'?" title="Delete '<?=$origin['origin']?>'" tooltip>
                        <i class="icon-trash"></i>
                    </a>
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