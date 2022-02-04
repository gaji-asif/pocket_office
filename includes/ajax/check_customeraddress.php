<?php
include '../common_lib.php'; 
$address = RequestUtil::get('address');
if(!ModuleUtil::checkAccess('add_job') || !$address) { die(); }

$sql = "SELECT customer_id, fname, lname
        FROM customers
        WHERE account_id = '{$_SESSION['ao_accountid']}'
            AND concat_ws(' ',address,city,state,zip) LIKE '%$address%'";
$results = DBUtil::query($sql);

$numRows = DBUtil::numRows($results);
if($numRows === 1) {
	list($customer_id, $fname, $lname, $old_address) = mysqli_fetch_row($results);
?>
    <span class="red"><br />Address similar to:</span>
    <a href="" rel="change-window-location" data-url="/customers.php?id=<?=$customer_id?>" data-type="customer" data-id="<?=$customer_id?>" tooltip>
      <?=$fname?> <?=$lname?>
    </a>
<?php
} else if($numRows > 1) {
?>
    <span class="red"><br />Address similar to:</span>
    <a href="#" onclick="parent.location='<?=ROOT_DIR?>/search.php?id=<?=$address?>';">
        <?=$numRows?> addresses
    </a>
<?php
}