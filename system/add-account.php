<?php
require_once '../includes/common_lib.php';

//check for system user
UserModel::systemUserCheck();

//add new account
if(isset($_POST['account-name']))
{
	//empty errors array
	$post_errors = array();

	//empty post error classes array
	$post_error_classes = array();

	//error checking
	if(empty($_POST['account-name']))
	{
		$post_error_classes['account-name'] = 'error';
		$post_errors[] = 'Account Name cannot be blank';
	}
	if(empty($_POST['primary-contact']))
	{
		$post_error_classes['primary-contact'] = 'error';
		$post_errors[] = 'Primary Contact cannot be blank';
	}
	if(empty($_POST['primary-username']))
	{
		$post_error_classes['primary-username'] = 'error';
		$post_errors[] = 'Primary Username cannot be blank';
	}
	if(UserModel::usernameExists($_POST['primary-username']))
	{
		$post_error_classes['primary-username'] = 'error';
		$post_errors[] = 'Primary Username already in use';
	}
	if(empty($_POST['email']) || !ValidateUtil::validateEmail($_POST['email']))
	{
		$post_error_classes['email'] = 'error';
		$post_errors[] = 'Email cannot be blank and must be proper format';
	}
	if(UserModel::emailExists($_POST['email']))
	{
		$post_error_classes['email'] = 'error';
		$post_errors[] = 'Email already in use';
	}
	if(empty($_POST['phone']) || !ValidateUtil::validateUSPhone($_POST['phone']))
	{
		$post_error_classes['phone'] = 'error';
		$post_errors[] = 'Phone cannot be blank and must be proper format';
	}
	if(UserModel::phoneExists($_POST['phone']))
	{
		$post_error_classes['phone'] = 'error';
		$post_errors[] = 'Phone already in use';
	}
	if(empty($_POST['address']))
	{
		$post_error_classes['address'] = 'error';
		$post_errors[] = 'Address cannot be blank';
	}
	if(empty($_POST['city']))
	{
		$post_error_classes['city'] = 'error';
		$post_errors[] = 'City cannot be blank';
	}
	if(!ValidateUtil::validateStateAbbreviation($_POST['state']))
	{
		$post_error_classes['state'] = 'error';
		$post_errors[] = 'State cannot be blank and must be proper format';
	}
	if(empty($_POST['zip']) || !ValidateUtil::validateUSZipCode($_POST['zip']))
	{
		$post_error_classes['zip'] = 'error';
		$post_errors[] = 'Zip cannot be blank and must be proper format';
	}
	if(empty($_POST['license-limit']) || !is_numeric($_POST['license-limit']))
	{
		$post_error_classes['license-limit'] = 'error';
		$post_errors[] = 'License Limit cannot be blank and must be proper format';
	}
	//no errors, add to database
	if(empty($post_errors))
	{
		if(addNewAccount($_POST))
		{
			$success_message = "'{$_POST['account-name']}' has been been successfully added";
			unset($_POST);
		}
		else
		{
			$post_errors[] = 'Error writing to the database. Please submit again.';
		}
	}
}

?>

<?=ViewUtil::loadView('system-configuration-head', array('title' => 'Add Account'))?>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
<?=ViewUtil::loadView('system-configuration-sidebar')?>
				</div>
				<div class="span9">
					<div class="page-header">
						<h1>Add Account</h1>
					</div>
					<div class="well well-small">
						<h3>Account Information</h3>
<?php
if(!empty($post_errors))
{
?>
						<div class="alert alert-error">
							<ul class="unstyled">
								<li><strong>Errors Found</strong></li>
<?php
	foreach($post_errors as $error)
	{
?>
								<li><?=$error?></li>
<?php
	}
?>
							</ul>
						</div>
<?php
}
if(!empty($success_message))
{
?>
						<div class="alert alert-success">
							<strong><?=$success_message?></strong>
						</div>
<?php
}
?>
						<form class="form-horizontal" action="?" name="add-account" method="post">
							<div class="control-group <?=@$post_error_classes['account-name']?>">
								<label class="control-label" for="account-name">Account Name</label>
								<div class="controls">
									<input type="text" class="span6" id="account-name" name="account-name" value="<?=@$_POST['account-name']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['primary-contact']?>">
								<label class="control-label" for="primary-contact">Primary Contact</label>
								<div class="controls">
									<input type="text" class="span6" id="primary-contact" name="primary-contact" value="<?=@$_POST['primary-contact']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['primary-username']?>">
								<label class="control-label" for="primary-username">Primary Username</label>
								<div class="controls">
									<input type="text" class="span6" id="primary-username" name="primary-username" value="<?=@$_POST['primary-username']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['email']?>">
								<label class="control-label" for="email">Email</label>
								<div class="controls">
									<input type="email" class="span6" id="email" name="email" value="<?=@$_POST['email']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['phone']?>">
								<label class="control-label" for="phone">Phone</label>
								<div class="controls">
									<input type="tel" class="span2" id="phone" name="phone" value="<?=@$_POST['phone']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['address']?>">
								<label class="control-label" for="address">Address</label>
								<div class="controls">
									<input type="text" class="span6" id="address" name="address" value="<?=@$_POST['address']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['city']?>">
								<label class="control-label" for="city">City</label>
								<div class="controls">
									<input type="text" class="span6" id="city" name="city" value="<?=@$_POST['city']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['state']?>">
								<label class="control-label" for="state">State</label>
								<div class="controls">
									<select id="state" name="state">
<?php
$states_array = getStates();

foreach($states_array as $abbr => $state)
{
	$selected = '';
	if($abbr == @$_POST['state'])
	{
		$selected = 'selected';
	}
?>
										<option value="<?=$abbr?>" <?=$selected?>><?=$abbr?></option>
<?php
}
?>
									</select>
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['zip']?>">
								<label class="control-label" for="zip">Zip</label>
								<div class="controls">
									<input type="text" class="span2" id="zip" name="zip" value="<?=@$_POST['zip']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['license-limit']?>">
								<label class="control-label" for="license-limit">License Limit</label>
								<div class="controls">
									<input type="number" class="span2" id="license-limit" name="license-limit" value="<?=@$_POST['license-limit']?>" />
								</div>
							</div>
							<div class="form-actions">
								<button type="reset" class="btn">Reset</button>
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</form>
					</div>
				</div>
			</div>
<?=ViewUtil::loadView('system-configuration-footer')?>