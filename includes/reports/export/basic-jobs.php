<?php

include '../../common_lib.php';
pageSecure('reports.php');

$salesman = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['salesman']);
$stage = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['stage']);
$order = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['order']);

if(!empty($salesman))
{
	$salesman_str = "and jobs.salesman='$salesman'";
}
if(!empty($stage))
{
	$stage_str = "and jobs.stage_num='$stage'";
}

//build and execute query
$sql = "select jobs.job_id, jobs.job_number, customers.fname, customers.lname, users.fname, users.lname, jobs.timestamp
       from customers, jobs left join users on (users.user_id = jobs.salesman)
	   where jobs.customer_id = customers.customer_id and jobs.account_id = '{$_SESSION['ao_accountid']}'
       $salesman_str $stage_str $order";
$res = DBUtil::query($sql);

//new export object
$exporter = new ExportDataExcel('browser', 'basic_jobs_.xls');
$exporter->initialize();

//add title row
$exporter->addRow(array('Job Number', 'Customer', 'Salesman', 'Created'));

//output
$exporter->finalize();

exit();