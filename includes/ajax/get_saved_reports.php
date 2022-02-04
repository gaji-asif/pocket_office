<?php
include '../common_lib.php';

//check access
UserModel::checkNavAccess('reports.php', TRUE);

//get saved reports
$savedReports = ReportUtil::getSavedReports();

if(!$savedReports) {
?>
    <h4>No Saved Reports</h4>
<?php
} else {
?>
<div class="row">
    <div class="col span-6" id="search-saved-reports">
        <input type="text" />
    </div>
</div>    
<ul class="job-items-list">
<?php
foreach($savedReports as $savedReport) {
    $query = json_decode(MapUtil::get($savedReport, 'query'), $assoc = TRUE);
    $query['saved_report_id'] = MapUtil::get($savedReport, 'saved_report_id');
?>
    <li>
        <i class="icon-trash action red"
           rel="report-delete"
           data-id="<?=MapUtil::get($savedReport, 'saved_report_id')?>"
           title="Delete" tooltip></i>&nbsp;
        <i class="icon-bell-alt action light-gray"
           rel="open-modal"
           data-script="add_edit_scheduled_report.php?id=<?=MapUtil::get($savedReport, 'saved_report_id')?>"
           title="Edit notifications" tooltip></i>&nbsp;
        <a href="" 
           rel="load-saved-report" 
           data-query='<?=json_encode($query)?>'
           title="View/edit (created by <?=UserUtil::getDisplayName(MapUtil::get($savedReport, 'user_id'), FALSE)?>)" tooltip>
            <?=ucwords(MapUtil::get($query, 'table'))?> - <?=MapUtil::get($savedReport, 'name')?>
        </a>
    </li>
<?php
}
?>
</ul>
<script>
$(function() {
    $('#search-saved-reports input').fastLiveFilter('.job-items-list');
})</script>
<?php
}
?>