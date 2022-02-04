<?php

include '../common_lib.php';
pageSecure('reports.php');

$start_date = RequestUtil::get('full_startdate');
$end_date = RequestUtil::get('full_enddate');
$order = RequestUtil::get('order');

$sql = "select jobs.job_id, jobs.job_number, customers.fname, customers.lname, customers.address, customers.city, customers.state, customers.zip, users.fname, users.lname, jurisdiction.location, permits.number, jurisdiction.midroof_timing, jurisdiction.ladder, jobs.pif_date
        from customers, jobs
        left join jurisdiction on (jobs.jurisdiction=jurisdiction.jurisdiction_id)
        left join permits on (permits.job_id=jobs.job_id)
        left join users on (jobs.salesman=users.user_id)
        where jobs.account_id='{$_SESSION['ao_accountid']}' and jobs.customer_id=customers.customer_id and jobs.salesman=users.user_id
            and jobs.timestamp>='$start_date' and jobs.timestamp <= '$end_date'
       $order";

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);

echo ViewUtil::loadView('doc-head');
?>

<?=ViewUtil::loadView('report-head', array('title' => 'Jobs - Full'))?>

<table class="table table-bordered table-condensed report">
    <thead>
        <tr>
            <th>Job number</th>
            <th>Customer</th>
            <th>Address</th>
            <th>Jurisdiction</th>
            <th>Mid</th>
            <th>Salesman</th>
            <th>Stage</th>
            <th>Task(s)</th>
            <th>Contractor(s)</th>
        </tr>
        <tr>
            <th>Paid</th>
            </th>
            <th></th>
            <th>CSZ</th>
            <th>Permit</th>
            <th>Ladder</th>
            <th></th>
            <th></th>
            <th></th>
            <th></th>
        </tr>
    </thead>
    <tbody>
<?php
while(list($job_id, $job_number, $cust_fname, $cust_lname, $address, $city, $state, $zip, $sales_fname, $sales_lname, $jurisdiction, $permit, $midroof, $ladder, $paid)=mysqli_fetch_row($res))
{
    $stages = getReportStages($job_id);
    $tasks = JobUtil::getReportTasks($job_id);
    $contractors = JobUtil::getReportContractors($job_id);

    if($ladder!='')
        $ladder = 'Y';
    else $ladder = 'N';

    if($paid!='')
        $paid = DateUtil::formatDate($paid);
    else $paid = 'Not Paid';
?>
        <tr>
            <td>
              <a href='<?=ROOT_DIR?>/jobs.php?id=<?php echo $job_id; ?>'<input type='reset' value='Reset' /><?php echo $job_number; ?></a><br />
              <?php echo $paid; ?>
            </td>
            <td>
              <?php echo $cust_lname.", ".$cust_fname[0]; ?>
            </td>
            <td>
              <?php echo $address; ?><br />
              <?php echo $city.", ".$state." ".$zip; ?>
            </td>
            <td>
              <?php echo $jurisdiction; ?><br />
              <?php echo $permit; ?>
            </td>
            <td>
              <?php echo $midroof; ?><br />
              <?php echo $ladder; ?>
            </td>
            <td>
              <?php echo $sales_lname.", ".$sales_fname[0]; ?>
            </td>
            <td>
              <?php echo $stages; ?>
            </td>
            <td>
              <?php echo $tasks; ?>
            </td>
            <td>
              <?php echo $contractors; ?>
            </td>
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
</html>