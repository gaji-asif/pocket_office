<?php
if(!isset($filterMapKey)) { return; }
$filters = ReportUtil::buildFiltersMap($filterMapKey);
$columns = ReportUtil::buildColumnsMap($filterMapKey);
$count = 0;
$numFilters = count($filters);
?>
<div class="filters <?=$filterMapKey?>">
    <form class="report-filter">
        <input type="hidden" name="table" value="<?=$filterMapKey?>" />
        <input type="hidden" name="saved_report_id" />
        <div class="row gutters">
            <div class="col span-6">
                <label>Report Name</label>
                <input type="text" name="report_name" />
            </div>
            <div class="col span-6">
                <label>Columns</label>
                <?=ViewUtil::loadView('filter-columns', array('columns' => $columns))?>
            </div>
        </div>
        <div class="row">
<?php
foreach($filters as $label => $filter) {
    if($count && !($count % 2)) {
?>
        </div>
        <div class="row">
<?php
    }
?>
            <div class="col span-6">
                <label><?=StrUtil::humanizeCamelCase($label)?></label>
                <?=$filter?>
            </div>
<?php
    $count++;
}
?>
        </div>
        <div class="clearfix">
            <div class="btn-group pull-right">
                <div class="btn btn-default btn-small" rel="report-clear-filters" title="Clear Filters" tooltip><i class="icon-remove"></i></div>
                <div class="btn btn-default btn-small" rel="report-save" title="Save" tooltip><i class="icon-save"></i></div>
                <div class="btn btn-default btn-small hidden" rel="report-copy" data-copy="true" title="Save Copy" tooltip><i class="icon-copy"></i></div>
                <div class="btn btn-default btn-small" rel="report-csv" title="Export as CSV" tooltip><i class="icon-download-alt"></i></div>
                <div class="btn btn-default btn-small" rel="report-view" title="View" tooltip><i class="icon-bolt"></i></div>
            </div>
        </div>
    </form>
</div>
<script>
$(function() {
    $('.filters.<?=$filterMapKey?>').show();
});
</script>