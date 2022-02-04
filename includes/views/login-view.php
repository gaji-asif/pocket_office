<?php
$login_account = '';
if(defined('LOGIN_ACCOUNT_VALUE'))
{
	$login_account = LOGIN_ACCOUNT_VALUE;
}
?>

<div id="login-container">
	<h1>Please Login</h1>
<?php
if(!empty($error_str))
{
?>
	<div class="login-error"><i class="icon-warning-sign"></i>&nbsp;<?=$error_str?></div>
<?php
}
?>
	<form method="post" action="<?php echo http_build_query($_GET);?>" id="login-form">
		<table class="table-form">
			<tr>
				<td>Username</td>
				<td><input type="text" name="username" id="input-username" value="<?=@$_COOKIE['ao_username']?>" /></td>
			</tr>
			<tr>
				<td>Password</td>
				<td><input type="password" name="password" id="input-password" /></td>
			</tr>
		</table>

		<div class="controls">
			<input type="submit" value="Login" />
			<input type="reset" value="Reset" />
			<a href="" rel="show-forgot-password">Forgot Password</a>
		</div>
	</form>
</div>


<div id="forgot-password-container">
	<h1>Forgot Password</h1>
	<div id="forgot-password-result"></div>
	<table class="table-form">
		<tr>
			<td>Email</td>
			<td><input type="text" id="input-email-forgot" /></td>
		</tr>
	</table>
	<div class="controls">
		<input type="button" value="Submit" rel="submit-forgot-password"/>
		<input type="button" value="Back" rel="hide-forgot-password"/>
		<a href="mailto:cbm3384@gmail.com?subject=<?=APP_NAME?> Help" target="_blank">Help</a>
	</div>


</div>