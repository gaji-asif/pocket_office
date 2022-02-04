<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
if(!ModuleUtil::checkIsFounder()) {
    UIUtil::showListError('You do not have permission to access this.');
}

$id = RequestUtil::get('id');
$name = RequestUtil::get('name');
$action = RequestUtil::get('action');

$errors = array();
$info = array();
if(RequestUtil::get('new')) {
    if(empty($name)) {
        $errors[] = 'Job type cannot be empty';
    }
    
    if(!count($errors)) {
        $sql = "INSERT INTO job_type (account_id, job_type)
                VALUES ('{$_SESSION['ao_accountid']}', '$name')";
        DBUtil::query($sql);
        $info[] = "'$name' successfully added";
    }
} else if($action === 'del' && $id) {
    $job = DBUtil::getRecord('jobs', $id, 'job_type');
    if(count($job)) {
        $errors[] = 'Job currently associated - cannot remove';
    }
    
    if(!count($errors)) {
        $sql = "DELETE FROM job_type
                WHERE job_type_id = '$id'
                    AND account_id = '{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        $info[] = "Job type successfully removed";
    }
}

?>
<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Job Types</h1>
<div class="list-table-container">
    <div class="container">
        <div class="row">
            <form method="post" action="?" name="type">
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
$jobTypes = JobUtil::getAllJobTypes();
foreach($jobTypes as $jobType) {
?>
            <tr>
                <td><?=$jobType['job_type']?></td>
                <td class="text-center">
                    <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/job_types.php?id=<?=$jobType['job_type_id']?>&action=del" data-confirm="Are you sure you want to remove job type '<?=$jobType['job_type']?>'?" title="Delete '<?=$jobType['job_type']?>'" tooltip>
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
