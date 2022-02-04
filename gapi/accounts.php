<?php
require_once '../includes/common_lib.php';

//check for system user
UserModel::systemUserCheck();

//get all accounts
$accounts_array = AccountModel::getAll();
?>

<?=ViewUtil::loadView('system-configuration-head', array('title' => 'Add Account'))?>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
<?=ViewUtil::loadView('system-configuration-sidebar')?>
				</div>
				<div class="span9">
					<div class="page-header">
						<h1>Accounts</h1>
					</div>
					<div>
						<table class="table table-bordered table-condensed table-hover table-striped">
							<thead>
								<tr>
									<th>Account</th>
									<th>Primary Contact</th>
									<th>City</th>
									<th>State</th>
									<th>DOB</th>
									<th>License Limit</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
<?php
foreach($accounts_array as $account)
{
?>
								<tr>
									<td><?=$account['account_name']?></td>
									<td><?=$account['primary_contact']?></td>
									<td><?=$account['city']?></td>
									<td><?=$account['state']?></td>
									<td><?=$account['reg_date']?></td>
									<td><?=$account['license_limit']?></td>
									<td>
										<!--<a href=""><i class="icon-trash"></i></a>-->
										<form action="edit-account.php" class="form-no-margin" id="edit-account-<?=$account['account_id']?>" method="post" name="edit-account-<?=$account['account_id']?>" />
											<input type="hidden" name="account-id" value="<?=$account['account_id']?>" />
										</form>
										<a rel="edit-account-link" href="" data-form-id="edit-account-<?=$account['account_id']?>"><i class="icon-pencil"></i></a>
										<!--<a href=""><i class="icon-info-sign"></i></a>-->
									</td>
								</tr>
<?php
}
?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
<?=ViewUtil::loadView('system-configuration-footer')?>