s<?php

include '../common_lib.php';
pageSecure('reports.php');

$material_id = RequestUtil::get('material');
$color_id = RequestUtil::get('color');
$city = RequestUtil::get('city');

$sql = "select material from materials where material_id = '".intval($material_id)."' and account_id = '".$_SESSION['ao_accountid']."' limit 1";
$res = DBUtil::query($sql);
list($material_name) = mysqli_fetch_row($res);

$color_str = '';
if(!empty($color_id))
{
	$color_str = "and sheet_items.color_id='".intval($color_id)."'";
}

$city_str = '';
if(!empty($city))
{
	$city_str = "and customers.city='". mysqli_real_escape_string(DBUtil::Dbcont(),$city)."'";
}

$sql = "select jobs.job_id, jobs.job_number, customers.customer_id, customers.lname, customers.address, customers.city, colors.color
        from materials, sheets, jobs, customers, sheet_items
        left join colors on (colors.color_id=sheet_items.color_id)
        where materials.material_id='".intval($material_id)."' and materials.material_id=sheet_items.material_id and sheets.sheet_id=sheet_items.sheet_id and jobs.job_id=sheets.job_id and customers.customer_id = jobs.customer_id and jobs.account_id='".$_SESSION['ao_accountid']."' ".$color_str." ".$city_str."
        group by sheets.sheet_id
        order by colors.color asc, customers.lname";

$res = DBUtil::query($sql);

$num_rows = mysqli_num_rows($res);

echo ViewUtil::loadView('doc-head');
?>

<?=ViewUtil::loadView('report-head', array('title' => 'Job - By Material', 'sub_title' => stripslashes($material_name)))?>

<table class="table table-bordered table-condensed  report">
    <thead>
        <tr>
            <th>Job number</th>
            <th>Address</th>
            <th>City</th>
            <th>Color</th>
        </tr>
    </thead>
    <tbody>
<?php

$i=0;
while(list($job_id, $job_number, $cust_id, $cust_lname, $address, $city, $color)=mysqli_fetch_row($res))
{
?>
        <tr>
            <td><a href="<?=ROOT_DIR?>/jobs.php?id=<?php echo $job_id; ?>"><?php echo $job_number; ?></a></td>
            <td><?php echo $address; ?></td>
            <td><?php echo $city; ?></td>
            <td><?php echo $color; ?></td>
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