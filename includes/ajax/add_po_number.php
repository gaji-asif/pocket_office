<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');

$id = RequestUtil::get('id');
$myJob = new Job($id);
ModuleUtil::checkJobModuleAccess('modify_job_type', $myJob, TRUE);

if(RequestUtil::get('submit')) {
    FormUtil::update('jobs');

    $myJob->storeSnapshot();
    JobModel::saveEvent($myJob->job_id, "PO Number Added");
?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
</script>
<?php
    die();
}
?>
<form method="post" action="?id=<?=$id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <?php

            if(!empty($myJob->po_number))
            {
        ?>
        <td>
            Edit PO Number
        </td>
        <?php

	    } else {

        ?>
        <td>
            Add PO Number
        </td>
        <?php
            }
        ?>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>PO Number:</b>
        </td>
        <td class="listrownoborder">
            <input type="text" name="po_number" value="<?=$myJob->po_number?>">
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