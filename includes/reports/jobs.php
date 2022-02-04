<?php

include '../common_lib.php';
pageSecure('reports.php');

$salesman = RequestUtil::get('salesman');
$stage = RequestUtil::get('stage');
$order = RequestUtil::get('order');

$extraSql = '';
if($salesman) {
    $extraSql .= "AND j.salesman = '$salesman'";
}
if($stage) {
    $extraSql .= "AND j.stage_num = '$stage'";
}

$sql = "SELECT j.job_id, j.job_number, c.fname, c.lname, u.fname AS salesman_fname, u.lname AS salesman_lname
        FROM customers c, jobs j 
        LEFT JOIN users u ON (u.user_id = j.salesman)
        WHERE j.customer_id = c.customer_id
            AND j.account_id='{$_SESSION['ao_accountid']}'
           $extraSql
        $order";
$results = DBUtil::queryToArray($sql);

$headers = array('Job Number', 'Customer', 'Salesman', 'Stage');
if(RequestUtil::get('csv')) {
    $rows[] = $headers;
    foreach($results as $row) {
        $rows[] = array(
            strval(trim(str_replace('-', '', $row['job_number']))),
            trim("{$row['lname']}, {$row['fname']}"),
            trim(!empty($row['salesman_lname']) ? "{$row['salesman_lname']}, {$row['salesman_fname']}" : ''),
            trim(JobUtil::getCSVStages($row['job_id']))
        );
    }
    
    CSVUtil::generate($rows, 'Basic Jobs Report');
}
?>

<?=ViewUtil::loadView('doc-head')?>
<?=ViewUtil::loadView('report-head', array('title' => 'Jobs - Basic', 'allowCsv' => TRUE))?>

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
            <td><?=$job['lname']?>, <?=$job['fnane']?></td>
            <td><?=!empty($job['salesman_lname']) ? "{$job['salesman_lname']}, {$job['salesman_fname']}" : ''?></td>
            <td><?=JobUtil::getCSVStages($job['job_id'])?></td>
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