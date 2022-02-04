<?php

include '../common_lib.php';
pageSecure('reports.php');

echo ViewUtil::loadView('doc-head');

//let's get stage id
$stage_id = getStageIdByName('Estimate Approval');

//estimate approval stage doesn't exist for this account
if(!$stage_id) {
?>
<h3>Reference to Estimate Approval not found</h3>
<a href='<?=ROOT_DIR?>/reports.php' class='boldlink'><i class="icon-double-angle-left"></i>&nbsp;Back</a>
<?php
    die();
}

//build and execute query
$sql = "SELECT j.job_id,
            j.job_number,
            j.customer_id,
            j.salesman as salesman_id,
            c.fname as customer_fname,
            c.lname as customer_lname,
            u.fname as salesman_fname,
            u.lname as salesman_lname
        FROM stages s, jobs j
        LEFT JOIN customers c ON c.customer_id = j.customer_id
        LEFT JOIN users u ON u.user_id = j.salesman
        LEFT JOIN job_meta jm ON jm.meta_id = j.job_id AND jm.meta_name = 'estimate_approved_date'
        WHERE j.stage_num = s.stage_num
            AND s.stage_id = '$stage_id'
            AND jm.id IS NULL";
$results = DBUtil::query($sql);
$jobs_array = convertResultsToArray($results)

?>

<?=ViewUtil::loadView('report-head', array('title' => 'Jobs Needing Estimate Approval'))?>

<table class="table table-bordered table-condensed  report">
    <thead>
        <tr>
            <th>Job Number</th>
            <th>Customer</th>
            <th>Salesman</th>
        </tr>
    </thead>
    <tbody>
<?php
foreach($jobs_array as $i => $job) {
?>
        <tr>
            <td><a href="<?=AJAX_DIR?>/jobs.php?id=<?=$job['job_id']?>"><?=$job['job_number']?></a></td>
            <td><?=$job['customer_fname']?> <?=$job['customer_lname']?></td>
            <td><?=$job['salesman_fname']?> <?=$job['salesman_lname']?></td>
        </tr>
<?php
}
if(sizeof($jobs_array) === 0)
{
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