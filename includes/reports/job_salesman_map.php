<?php

include '../common_lib.php';
pageSecure('reports.php');

$salesman = RequestUtil::get('salesman');
$extraSql = '';
if($salesman) {
    $extraSql .= "AND j.salesman = '$salesman'";
}

$sql = "SELECT j.job_id, j.job_number, u.fname, u.lname
        FROM jobs j, users u
        WHERE u.user_id = j.salesman
            AND j.account_id='{$_SESSION['ao_accountid']}'
           $extraSql
        ORDER BY u.lname ASC, j.job_number ASC";
$results = DBUtil::queryToArray($sql);

$headers = array('Job Number', 'Salesman');
if(RequestUtil::get('csv')) {
    $rows[] = $headers;
    
    foreach($results as $row) {
        $rows[] = array(
            strval(trim(str_replace('-', '', $row['job_number']))),
            trim("{$row['lname']}, {$row['fname']}")
        );
    }
    
    CSVUtil::generate($rows, 'Job/Salesman Map');
}
?>

<?=ViewUtil::loadView('doc-head')?>
<?=ViewUtil::loadView('report-head', array('title' => 'Job/Salesman Map', 'allowCsv' => TRUE))?>

<table class="table table-bordered table-condensed  report">
    <thead>
        <tr>
            <th><?=implode('</th><th>', $headers)?></th>
        </tr>
    </thead>
    <tbody>
<?php

foreach($results as $job) {
?>
        <tr>
            <td><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$job['job_id']?>"><?=$job['job_number']?></a></td>
            <td><?=$job['lname']?>, <?=$job['fname']?></td>
        </tr>
<?php
}
if(!count($results)) {
?>
        <tr>
            <td colspan="10">
                <center>No Results</center>
            </td>
        </tr>
<?php
}
?>
    </tbody>
</table>
</html>