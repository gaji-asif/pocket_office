<?php

include '../common_lib.php';
pageSecure('reports.php');

$sql = "SELECT j.job_id, j.job_number, ft.fail_type, c.fname, c.lname, u.fname AS salesman_fname, u.lname AS salesman_lname
        FROM fail_types ft, repairs r, customers c, jobs j
        LEFT JOIN  users u ON j.salesman = u.user_id
        WHERE r.account_id = '{$_SESSION['ao_accountid']}'
            AND r.job_id = j.job_id
            AND r.fail_type = ft.fail_type_id
            AND j.customer_id = c.customer_id
            AND r.completed IS NULL
        ORDER BY j.job_id ASC";
$results = DBUtil::queryToArray($sql);

$headers = array('Job Number', 'Fail Type', 'Customer', 'Salesman', 'Stage');
if(RequestUtil::get('csv')) {
    $rows[] = $headers;
    
    foreach($results as $repair) {
        $rows[] = array(
            strval(trim(str_replace('-', '', $repair['job_number']))),
            trim($repair['fail_type']),
            trim("{$repair['lname']}, {$repair['fname']}"),
            trim("{$repair['salesman_lname']}, {$repair['salesman_fname']}"),
            trim(JobUtil::getCSVStages($repair['job_id'])),
        );
    }
    
    CSVUtil::generate($rows, 'Incomplete Repairs');
}
?>

<?=ViewUtil::loadView('doc-head')?>
<?=ViewUtil::loadView('report-head', array('title' => 'Incomplete Repairs', 'allowCsv' => TRUE))?>

<table class="table table-bordered table-condensed  report">
    <thead>
        <tr>
            <th><?=implode('</th><th>', $headers)?></th>
        </tr>
    </thead>
    <tbody>
<?php

foreach($results as $repair) {
?>
        <tr>
            <td><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$repair['job_id']?>"><?=$repair['job_number']?></a></td>
            <td><?=$repair['fail_type']?></td>
            <td><?=$repair['lname']?>, <?=$repair['fname']?></td>
            <td><?=$repair['salesman_lname'] ? "{$repair['salesman_lname']}, {$repair['salesman_fname']}" : '' ?></td>
            <td><?=JobUtil::getCSVStages($repair['job_id'])?></td>
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