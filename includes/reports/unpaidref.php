<?php

include '../common_lib.php';
pageSecure('reports.php');

$referral = RequestUtil::get('referral');
$salesman = RequestUtil::get('referral');
$holds = RequestUtil::get('holds');
$stage = RequestUtil::get('stage');
$order = RequestUtil::get('order');

if($referral!='')
  $referral_str = " and jobs.referral='".intval($referral)."' ";
if($salesman!='')
  $salesman_str = " and jobs.salesman='".intval($salesman)."' ";
if($stage!='')
  $stage_str = " and jobs.stage_num=>'".intval($stage)."' ";
if($holds=='yes')
  $holds_str = " and status_holds.status_hold_id is not null ";

$sql = "select jobs.job_id, jobs.job_number, customers.fname, customers.lname, users.fname, users.lname, users2.fname, users2.lname".
       " from customers, jobs".
       " left join users on (users.user_id=jobs.referral)".
       " left join status_holds on (status_holds.job_id=jobs.job_id)".
       " left join users as users2 on (users2.user_id=jobs.salesman)".
       " where jobs.referral is not null and jobs.referral_paid is null and jobs.customer_id=customers.customer_id and jobs.account_id='".$_SESSION['ao_accountid']."'".
       $referral_str.
       $salesman_str.
       $stage_str.
       $holds_str.
       "group by jobs.job_id ".$order;

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);

ob_start();

echo ViewUtil::loadView('doc-head');
?>

<?=ViewUtil::loadView('report-head', array('title' => 'Unpaid Referrals'))?>

<table class="table table-bordered table-condensed  report">
    <thead>
        <tr>
            <th>Job number</th>
            <th>Customer</th>
            <th>Referral</th>
            <th>Salesman</th>
            <th>Stage</th>
        </tr>
    </thead>
    <tbody>
<?php
while(list($job_id, $job_number, $cust_fname, $cust_lname, $ref_fname, $ref_lname, $salesman_fname, $salesman_lname)=mysqli_fetch_row($res))
{
    $stages = JobUtil::getCSVStages($job_id);

    $ref_str = $ref_lname.", ".$ref_fname[0];

    if($sales_fname!='') {
        $salesman_str = $sales_lname.", ".$sales_fname[0];
    }
?>
        <tr>
            <td><a href="<?=ROOT_DIR?>/jobs.php?id=<?php echo $job_id; ?>"><?php echo $job_number; ?></a></td>
            <td><?php echo $cust_lname.", ".$cust_fname[0]; ?></td>
            <td><?php echo $ref_str; ?></td>
            <td><?php echo $salesman_str; ?></td>
            <td><?php echo $stages; ?></td>
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