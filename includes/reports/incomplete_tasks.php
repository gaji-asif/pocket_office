<?php

include '../common_lib.php';
pageSecure('reports.php');
RequestUtil::makePostSafe();

$taskTypeId = RequestUtil::get('task_type');
$orderBy = RequestUtil::get('order');
$extraSql = '';
if($taskTypeId) {
    $extraSql = "AND tt.task_type_id = '$taskTypeId'";
}
        
$sql = "SELECT j.job_id, j.job_number, tt.task, c.fname, c.lname, u.fname as salesman_fname, u.lname as salesman_lname, t.timestamp, js.location
        FROM task_type tt, tasks t, customers c, jobs j
        LEFT JOIN users u ON (j.salesman = u.user_id)
        LEFT JOIN jurisdiction js ON (js.jurisdiction_id = j.jurisdiction)
        WHERE t.account_id = '{$_SESSION['ao_accountid']}'
            AND t.job_id = j.job_id
            AND t.task_type = tt.task_type_id
            AND j.customer_id = c.customer_id
            AND t.completed IS NULL
            $extraSql
        {$orderBy}";
$results = DBUtil::queryToArray($sql);

$headers = array('Job Number', 'Task Type', 'Customer', 'Salesman', 'Jurisdiction', 'Stage', 'Created');
if(RequestUtil::get('csv')) {
    $rows[] = $headers;
    
    foreach($results as $task) {
        $rows[] = array(
            strval(trim(str_replace('-', '', $task['job_number']))),
            trim($task['task']),
            trim("{$task['lname']}, {$task['fname']}"),
            trim("{$task['salesman_lname']}, {$task['salesman_fname']}"),
            trim($task['location']),
            trim(JobUtil::getCSVStages($task['job_id'])),
            trim(DateUtil::formatDate($task['timestamp']))
        );
    }
    
    CSVUtil::generate($rows, 'Incomplete Tasks');
}
?>

<?=ViewUtil::loadView('doc-head')?>
<?=ViewUtil::loadView('report-head', array('title' => 'Incomplete Tasks', 'allowCsv' => TRUE))?>

<table class="table table-bordered table-condensed report">
    <thead>
        <tr>
            <th><?=implode('</th><th>', $headers)?></th>
        </tr>
    </thead>
    <tbody>
<?php
foreach($results as $task) {
?>
        <tr>
            <td><a href="<?=ROOT_DIR?>/jobs.php?id=<?=$task['job_id']?>"><?=$task['job_number']?></a></td>
            <td><?=$task['task']?></td>
            <td><?=$task['lname']?>, <?=$task['fname']?></td>
            <td><?=$task['salesman_lname'] ? "{$task['salesman_lname']}, {$task['salesman_fname']}" : '' ?></td>
            <td><?=$task['location']?></td>
            <td><?=JobUtil::getCSVStages($task['job_id'])?></td>
            <td><?=DateUtil::formatDate($task['timestamp'])?></td>
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