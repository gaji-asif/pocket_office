<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();

//get all nav items to use as topics
$topics = UIModel::getNavList();

//form submitted
if(isset($_POST['fullname']))
{
	//validation
	$error_messages = array();
	if(empty($_POST['fullname']) || empty($_POST['username']) || empty($_POST['account']) || empty($_POST['email']))
	{
		$error_messages[] = 'Cannot authenticate user';
	}
	if(empty($_POST['details']))
	{
		$error_messages[] = 'Details cannot be blank';
	}

	//if no bugs found, process
	if(empty($error_messages))
	{
		//get mail body from template
		$view_data = array(
			'from' => $_POST['fullname'],
			'username' => $_POST['username'],
			'account' => $_POST['account'],
			'email' => $_POST['email'],
			'details' => $_POST['details'],
		);
		$body = ViewUtil::loadView('mail/feature-request', $view_data);

		//get new mail object
		$mail = new PHPMailer();

		//add from, to, subject, body
		$mail->SetFrom($_POST['email'], $_POST['fullname']);
		$mail->AddAddress("cbm3384@gmail.com", "Chris Mitchell");
//		$mail->AddAddress("darinrjohnson@gmail.com", "Darin Johnson");
		$mail->Subject = APP_NAME . " Feature Request";
		$mail->MsgHTML($body);

		//send
		if(!$mail->Send())
		{
			$error_messages[] = 'Your feature request failed to send. Please try again.';
		}
		else
		{
			$success_message = 'Your feature request has been sent.';
			unset($_POST);
		}
	}
}

?>
<html>
	<head>
		<title>Request a Feature - <?=APP_NAME?></title>
		<meta name="viewport" content="width=device-width, initial-scale=1.0">
		<link rel="icon" type="image/png" href="images/favicon.png">
		<link href="<?=ROOT_DIR?>/bootstrap/css/bootstrap.css" rel="stylesheet" media="screen">
		<style>
			#request-feature
			{
				margin: 100px auto 0px auto;
				width: 600px;
			}
			input[type="text"]
			{
				height: 30px;
			}
			.controls
			{
				line-height: 30px;
			}
			textarea
			{
				height: 200px;
				width: 350px;
			}
		</style>
	</head>
	<body>
		<div id="request-feature" class="well">
			<h3>Request a Feature</h3>
<?php
if(!empty($error_messages))
{
?>
			<div class="alert alert-error">
				<div><strong>Errors Found!</strong></div>
<?php
	foreach($error_messages as $error)
	{
?>
				<div><?=$error?></div>
<?php
	}
?>
			</div>
<?php
}
else if(!empty($success_message))
{
?>
			<div class="alert alert-success">
				<div><strong>Thank you!</strong> <?=$success_message?></div>
			</div>
<?php
}
?>
			<form action="?" class="form-horizontal" method="post" name="request_feature">
				<input name="fullname" type="hidden" value="<?=$_SESSION['ao_fname'] . ' ' . $_SESSION['ao_lname']?>">
				<input name="account" type="hidden" value="<?=$_SESSION['ao_accountname']?>">
				<input name="username" type="hidden" value="<?=$_SESSION['ao_username']?>">
				<input name="email" type="hidden" value="<?=UserModel::getProperty($_SESSION['ao_userid'], 'email')?>">
				<div class="control-group">
					<label class="control-label" for="details">Details:</label>
					<div class="controls">
						<textarea id="details" name="details"><?=@$_POST['details']?></textarea>
					</div>
				</div>
				<div class="control-group">
					<div class="controls">
						<button type="reset" class="btn">Reset</button>
						<button type="submit" class="btn">Submit</button>
					</div>
				</div>
			</form>
		</div>
	</body>
</html>