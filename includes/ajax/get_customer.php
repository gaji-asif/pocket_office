<?php

include '../common_lib.php';

ModuleUtil::checkAccess('view_customers', TRUE, TRUE);

$customer = new Customer(RequestUtil::get('id'));

if(!$customer) {

    UIUtil::showListError('Announcement not found!');

}

UserModel::storeBrowsingHistory("{$customer->getDisplayName()}", 'address_16', 'customers.php', $customer->getMyId());

?>

<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">

<?php

$view_data = array(

	'myCustomer' => $customer

);

echo ViewUtil::loadView('customer-list-row', $view_data);

?>

</table>

<table width="100%" border="0" class="data-table" cellpadding="5" cellspacing="0">

<?php

if(ModuleUtil::checkAccess('edit_customer'))

{

?>

    <tr>

        <td colspan="10">

            <div class="btn-group pull-right">

                <div rel="open-modal" data-script="edit_customer.php?id=<?=$customer->getMyId()?>"class="btn btn-small" title="Edit Customer" tooltip>

                    <i class="icon-pencil"></i>

                </div>

            </div>

        </td>

    </tr>

<?php

}

?>

	<tr>

		<td colspan="10">

			<table border="0" width="100%">

				<tr>

					<td class="smalltitle">Customer Profile:</td>

				</tr>

				<tr valign="top">

					<td>

						<table border="0" width="100%" class='listtable' cellpadding="0" cellspacing="0">

							<tr valign="top">

								<td width="150" class="listitemnoborder"><b>Address:</b></td>

								<td class="listrownoborder">

<?php

$fullAddress = $customer->get('address'). ' ' . $customer->get('city'). ' ' . $customer->get('state'). ' ' . $customer->get('zip');

?>

									<a href="/workflow<?=MapUtil::getUrlToInternalMap($fullAddress)?>" class="basiclink">

										<?=$customer->get('address')?><br />

										<?=$customer->get('city')?>, <?=$customer->get('state')?> <?=$customer->get('zip')?>

									</a>

								</td>

							</tr>

							<tr>

								<td class="listitem"><b>Cross Street:</b></td>

								<td class="listrow"><?=$customer->get('cross_street')?></td>

							</tr>

							<tr>

								<td class="listitem"><b>Latitude:</b></td>

								<td class="listrow"><?=$customer->get('lat')?></td>

							</tr>

							<tr>

								<td class="listitem"><b>Longitude:</b></td>

								<td class="listrow"><?=$customer->get('long')?></td>

							</tr>

							<tr>

								<td class="listitem"><b>Phone:</b></td>

								<td class="listrow">

									<?=UIUtil::formatPhone($customer->get('phone'))?>

<?php

if($customer->get('phone2')) {

?>

                                    , <?=UIUtil::formatPhone($customer->get('phone2'))?>

<?php

}

?>



								</td>

							</tr>

							<tr>

								<td class="listitem"><b>Email:</b></td>

								<td class="listrow"><?=$customer->get('email')?></td>

							</tr>

                            <tr valign="top">

								<td class="listitem">

									<b>Jobs:</b>

								</td>

								<td class="listrow">

<?php

$jobs = CustomerModel::getJobs($customer->getMyId());

foreach($jobs as $job) {

?>

                                        <div><a href="/workflow/jobs.php?id=<?=MapUtil::get($job, 'job_id')?>"><?=MapUtil::get($job, 'job_number')?></a></div>

<?php

}

?>

								</td>

							</tr>

						</table>

					</td>

				</tr>

			</table>

		</td>

	</tr>

	<tr><td colspan=10>&nbsp;</td></tr>

	<tr>

		<td colspan=10 class='infofooter'>

			<table border="0" cellpadding="0" cellspacing="0" width="100%">

				<tr>

					<td>

						<a href="javascript:clearElement('notes'); Request.make('<?= AJAX_DIR ?>/get_customerlist.php', 'customerscontainer', true, true);" class='basiclink'>

							<i class="icon-double-angle-left"></i>&nbsp;Back

						</a>

					</td>

				</tr>

			</table>

		</td>

	</tr>

</table>

