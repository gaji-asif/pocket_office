<?php
include '../common_lib.php';
ModuleUtil::checkAccess('view_jobs', TRUE, TRUE);

$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight, TRUE);
$limit = RequestUtil::get('limit') ?: 0;

$permits = PermitUtil::getPermitList();
$limited_permits = array_slice($permits, $limit, $_RES_PER_PAGE);
foreach($limited_permits as $permit) {
    $permitDays = MapUtil::get($permit, 'permit_days');
    
    $expires = strtotime(MapUtil::get($permit, 'permit_expires', DateUtil::formatDate()));
    $now = time();
    $rowClass = '';
    if($expires < $now) {
//        $diff = abs($expires - $now);
//        $rowClass = $diff >= 604800 ? 'error' : 'warning'; 
    }
?>
<tr class="<?=$rowClass?>" rel="change-window-location" data-url="/jobs.php?id=<?=MapUtil::get($permit, 'job_id')?>">
    <td><?=MapUtil::get($permit, 'number')?></td>
    <td>
        <a href="/jobs.php?id=<?=MapUtil::get($permit, 'job_id')?>" tooltip>
            <?=MapUtil::get($permit, 'job_number')?>
        </a>
    </td>
    <td>
        <a href="/customers.php?id=<?=MapUtil::get($permit, 'customer_id')?>" tooltip>
            <?=MapUtil::get($permit, 'lname')?>, <?=MapUtil::get($permit, 'fname')?>
        </a>
    </td>
    <td><?=MapUtil::get($permit, 'address')?></td>
    <td><?=MapUtil::get($permit, 'location')?></td>
    <td><?=DateUtil::formatDate(MapUtil::get($permit, 'permit_expires'))?></td>
</tr>
<?php
}

$pages = ceil(count($permits) / $_RES_PER_PAGE);
?>
<script>
    function previousPage() {
<?php
$_GET['limit'] = $limit - 1;
?>
        fetchPermitList('<?=http_build_query($_GET)?>');
    }
    function nextPage() {
<?php
$_GET['limit'] = $limit + 1;
?>
        fetchPermitList('<?=http_build_query($_GET)?>');
    }
    function firstPage() {
<?php
$_GET['limit'] = 0;
?>
        fetchPermitList('<?=http_build_query($_GET)?>');
    }
    function lastPage() {
<?php
$_GET['limit'] = $pages - 1;
?>
        fetchPermitList('<?=http_build_query($_GET)?>');
    }
    
    $(function() {
        UI.generatePagination({
            afterTarget: $('#permits-container').closest('table.table'),
            totalRows: <?=count($permits)?>,
            numPerPage: <?=$_RES_PER_PAGE?>,
            page: <?=$limit?>,
            pages: <?=$pages?>
        });
    });
</script>