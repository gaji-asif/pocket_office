<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('view_job_history', $myJob, TRUE);
$history = JobModel::getActionHistory($myJob->getMyId());
?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Job History</td>
                    <td align="right">
                        <i class="icon-remove grey btn-close-modal"></i>
                    </td>
                </tr>
            </table>
            <div class="list-table-container">
                <table class="table table-bordered table-condensed table-striped">
                    <thead>
                        <tr>
                            <th>Action</th>
                            <th>User</th>
                            <th width="25%">Timestamp</td>
                        </tr>
                    </thead>
                    <tbody>
<?php
foreach($history as $row) {
?>
                        <tr>
                            <td><?=MapUtil::get($row, 'action')?></td>
                            <td>
                                <a href="#" onclick="parent.location = '<?=ROOT_DIR?>/users.php?id=<?=MapUtil::get($row, 'user_id')?>';"><?=MapUtil::get($row, 'display_name')?></a>
                            </td>
                            <td><?=DateUtil::formatDateTime(MapUtil::get($row, 'timestamp'))?></td>
                        </tr>
<?php
}
if(!count($history)) {
?>
                        <tr>
                            <td colspan="3"><b>No History Found</b></td>
                        </tr>
<?php
}
?>
                    </tbody>
                </table>
            </div>
        </td>
    </tr>
</table>
</body>
</html>