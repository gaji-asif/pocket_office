<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_customers'))
  die("Insufficient Rights");

$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);

$_SESSION['ao_full_customerlist_query_string'] = http_build_query($_GET);

$limit = (int)$_GET['limit'];
if(empty($limit))
{
	$limit = 0;
}

$sort = $_GET['sort'];
$searchStr = $_GET['search'];

//get customers
$customers_array = getCustomerList($sort, $searchStr);
$num_rows = sizeof($customers_array);

?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr>
		<td colspan="10">
			<b>Searching '<?=$searchStr?>' - <?php echo $num_rows ?> result(s) found</b>
		</td>
	</tr>
</table>
<?php
if($num_rows > 0)
{
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
	foreach($customers_array as $key => $customer_row)
    {
        $class = 'odd';
        if($key%2 == 0)
        {
            $class = 'even';
        }
        $myCustomer = new Customer($customer_row['customer_id']);

		$view_data = array(
			'myCustomer' => $myCustomer,
			'class' => $class,
			'true_customer_link' => true
		);
        echo ViewUtil::loadView('customer-list-row', $view_data);
    }
?>
</table>
<?php
}