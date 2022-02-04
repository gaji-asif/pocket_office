<?php
include 'includes/common_lib.php';
echo ViewUtil::loadView('doc-head');
////echo '<pre>';
$sql = ReportUtil::buildSQL('jobs');
echo "<div>$sql</div>";
$results = DBUtil::query($sql);
$rows = DBUtil::convertResultsToArray($results);
$filters = ReportUtil::buildFiltersMap('jobs');
$columns = ReportUtil::buildColumnsMap('jobs');
?>
<!--<h2><?=$rows ? count($rows) : 0?> Rows</h2>-->
<div style="margin: 20px;">
    <form action="?">
<?php
foreach($filters as $label => $filter) {
?>
        <div class="row">
            <div><label><?=$label?></label></div>
            <div><?=$filter?></div>
        </div>
<?php
}
?>
        <div class="row">
            <div><label>Columns</label></div>
            <div><?=ViewUtil::loadView('filter-columns', array('columns' => $columns))?></div>
        </div>
        <input type="submit" />
    </form>
</div>