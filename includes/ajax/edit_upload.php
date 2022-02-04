<?php
include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');

$uploadId = RequestUtil::get('upload');
$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('edit_uploads', $myJob, TRUE);
$upload = DBUtil::getRecord('uploads', $uploadId);

if(!count($upload)) {
    UIUtil::showModalError('Upload not found!');
}

if(RequestUtil::get('submit')) {
    $title = RequestUtil::get('title');
    if(!$title) {
        $errors[] = 'Title cannot be blank';
    }
    
    if(!count($errors)) {
        $sql = "UPDATE uploads
                SET title='$title'
                WHERE job_id='{$myJob->job_id}'
                    AND upload_id = '$uploadId'
                LIMIT 1";
        DBUtil::query($sql);
        JobModel::saveEvent($myJob->job_id, 'Job File Modified');
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>&tab=uploads', 'jobscontainer', true, true, true);
</script>
<?php
        die();
    }
}

?>
<form method="post" name="edit-upload" action="?upload=<?= $uploadId ?>&id=<?= RequestUtil::get('id') ?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>Modify File</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <td width="25%" class="listitem">
            <b>Title:</b>
        </td>
        <td class="listrow">
            <input size=50 type="text" name="title" value="<?= MapUtil::get($upload, 'title') ?>">
        </td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
            </form>
        </td>
    </tr>
</table>
</body>
</html>