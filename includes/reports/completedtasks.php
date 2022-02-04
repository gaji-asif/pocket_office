<?php

include '../common_lib.php';
pageSecure('reports.php');

$start_date = RequestUtil::get('completed_startdate');
$end_date = RequestUtil::get('completed_enddate');
$order = RequestUtil::get('order');

$sql = "select jobs.job_id, jobs.job_number, task_type.task, tasks.completed, customers.fname as custfname, customers.lname as custlname, customers.address, customers.city, customers.state, customers.zip, jurisdiction.location, u.fname as salesfname, u.lname as saleslname, users.fname as confname, users.lname as conlname
        from task_type, users, tasks, jobs
        left join customers on (jobs.customer_id=customers.customer_id)
        left join jurisdiction on(jobs.jurisdiction=jurisdiction.jurisdiction_id)
        left join users as u on (jobs.salesman=u.user_id)
        where jobs.account_id='{$_SESSION['ao_accountid']}'
            and tasks.completed <= '$end_date'
            and tasks.completed is not null
            and tasks.task_type = task_type.task_type_id
            and jobs.job_id = tasks.job_id
            and tasks.contractor = users.user_id
        group by jobs.job_id
       $order";

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);

echo ViewUtil::loadView('doc-head');
?>

<?=ViewUtil::loadView('report-head', array('title' => 'Completed Tasks'))?>

<table class="table table-bordered table-condensed  report">
    <thead>
        <tr>
            <th>Job number</th>
            <th>Customer</th>
            <th>Address</th>
            <th>Jurisdiction</th>
            <th>Salesman</th>
            <th>Stage</th>
            <th>Task</th>
            <th>Contractor</th>
            <th>Completed</th>
        </tr>
    </thead>
    <tbody>
<?php

while(list($job_id, $job_number, $task_type, $completed, $cust_fname, $cust_lname, $address, $city, $state, $zip, $jurisdiction, $sales_fname, $sales_lname, $contractor_fname, $contractor_lname)=mysqli_fetch_row($res))
{
  $stages = JobUtil::getReportStages($job_id);
  $completed = DateUtil::formatDate($completed);
?>
        <tr>
            <td>
                <a href="<?= ROOT_DIR ?>/jobs.php?id=<?php echo $job_id; ?>"><?php echo $job_number; ?></a>
            </td>
            <td>
                <?php echo $cust_lname . ", " . $cust_fname[0]; ?>
            </td>
            <td>
                <?php echo $address; ?>
                <br />
                <?php echo $city . ", " . $state . " " . $zip; ?>
            </td>
            <td>
                <?php echo $jurisdiction; ?>
            </td>
            <td>
                <?php echo $sales_lname . ", " . $sales_fname[0]; ?>
            </td>
            <td>
                <?php echo $stages; ?>
            </td>
            <td>
                <?php echo $task_type; ?>
            </td>
            <td><?php echo $contractor_lname . ", " . $contractor_fname[0]; ?></td>
            <td><?php echo $completed; ?></td>
        </tr>
<?php
}
if($num_rows == 0)
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