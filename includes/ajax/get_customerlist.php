<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_customers'))
  die("Insufficient Rights");

$windowHeight = RequestUtil::get('window_height', DEFAULT_WINDOW_HEIGHT);
$_RES_PER_PAGE = calcResultsPerPage($windowHeight);

$limit = (int)RequestUtil::get('limit', 0);
$limit = $limit >= 0 ? $limit : 0;
$searchStr = RequestUtil::get('search');

//get customers
$customers = CustomerModel::getList($limit, $_RES_PER_PAGE);
$totalCustomers = DBUtil::getLastRowsFound();

if(!empty($searchStr)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr>
		<td colspan="10">
			<b>Searching '<?=$searchStr?>' - <?php echo $num_rows ?> result(s) found</b>
		</td>
	</tr>
	<tr>
		<td colspan="10">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td>
						<a href="javascript:Request.make('includes/ajax/get_customerlist.php', 'customerscontainer', true, true);" class='basiclink'>
							<i class="icon-double-angle-left"></i>&nbsp;Back
						</a>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>
<?php
}
if(count($customers)) {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
    //limit results
	foreach($customers as $customer) {
        $myCustomer = new Customer($customer['customer_id']);
        echo ViewUtil::loadView('customer-list-row', array('myCustomer' => $myCustomer));
    }
?>
</table>
<?php
    $viewData = array(
        'limit' => $limit,
        'query_string_params' => $_GET,
        'results_per_page' => $_RES_PER_PAGE,
        'total_results' => $totalCustomers,
		'script' => 'get_customerlist',
		'destination' => 'customerscontainer'
    );
    echo ViewUtil::loadView('list-pagination', $viewData);
} else {
?>
<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="5">
	<tr valign="middle">
		<td align="center" colspan="10">
			<b>No Customers Found</b>
		</td>
	</tr>
</table>
<?php
}