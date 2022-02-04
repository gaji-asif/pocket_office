<?php
include '../common_lib.php';
$reportName = RequestUtil::get('report_name');
$csv = RequestUtil::get('csv');

if(RequestUtil::get('print') && !$csv) {
    echo ViewUtil::loadView('doc-head');
}

//check access
$access = UserModel::checkNavAccess('reports.php', !$csv);

$rows = ReportUtil::getResultsFromRequest();
if(!$rows && !$csv) {
    echo AlertUtil::generate(array('No results.'), 'info');
    return;
}

//csv
if($csv) {
    $rows = ReportUtil::getTableRows($rows, NULL, NULL, TRUE);
    array_unshift($rows, ReportUtil::getTableHeaders(NULL, NULL, TRUE));
    
    CSVUtil::generate($rows, ($reportName ? $reportName :  'Report') . ' ' . DateUtil::formatMySQLDate());
}
?>
<h1><?=$reportName ?: 'Report'?> (<?=count($rows)?>)</h1>
<table class="table table-bordered table-condensed">
    <thead>
        <tr>
            <?=ReportUtil::getTableHeaders()?>
        </tr>
    </thead>
    <tbody>
        <?=ReportUtil::getTableRows($rows)?>
    </tbody>
</table>
<div class="text-center">
    Generated <?=DateUtil::formatDateTime()?> by <?=UserUtil::getDisplayName($_SESSION['ao_userid'], FALSE)?>
</div>
<div class="text-center">
    <strong><?=APP_NAME?></strong>
</div>
<?php
if(RequestUtil::get('print')) {
?>
<script>
$(function() {
    window.print();
    setTimeout(function() {
        window.close();
    }, 500);
});
</script>
<?php
}
?>
