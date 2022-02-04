<?php

include '../common_lib.php';
pageSecure('reports.php');

if(RequestUtil::get('holds') == 'yes')
  $holds_str = "and status_holds.status_hold_id is null";

$order = RequestUtil::get('order');

$sql = "select jobs.job_id, jobs.job_number, customers.fname, customers.lname, users.fname, users.lname, sheets.delivery_date
		from customers, sheets, jobs
		left join users on (jobs.salesman=users.user_id)
		left join status_holds on (status_holds.job_id=jobs.job_id)
		where jobs.account_id = '{$_SESSION['ao_accountid']}' AND jobs.job_id=sheets.job_id and jobs.customer_id=customers.customer_id and sheets.confirmed is null
		$holds_str $order";

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);

ob_start();

echo ViewUtil::loadView('doc-head');

?>

<?=ViewUtil::loadView('report-head', array('title' => 'Orders Not Confirmed'))?>

<table class="table table-bordered table-condensed  report">
    <thead>
        <tr>
            <th>Job number</th>
            <th>Customer</th>
            <th>Salesman</th>
            <th>Delivery Date</th>
        </tr>
    </thead>
    <tbody>
<?php

while(list($job_id, $job_number, $cust_fname, $cust_lname, $sales_fname, $sales_lname, $delivery_date)=mysqli_fetch_row($res))
{
    if(!empty($delivery_date))
      $delivery_date = DateUtil::formatDate($delivery_date);
    else $delivery_date = "Not yet set";

    $stages = JobUtil::getCSVStages($job_id);

    if($sales_fname!='') {
        $salesman_str = $sales_lname.", ".$sales_fname[0];
    }
?>
        <tr>
            <td><a href="<?=ROOT_DIR?>/jobs.php?id=<?php echo $job_id; ?>"><?php echo $job_number; ?></a></td>
            <td><?php echo $cust_lname.", ".$cust_fname[0]; ?></td>
            <td><?php echo $salesman_str; ?></td>
            <td><?php echo $delivery_date; ?></td>
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