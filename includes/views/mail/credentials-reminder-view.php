<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title><?=APP_NAME?>: Credentials Reminder</title>
	</head>
	<body style="background: #EDEDED;">
		<table style="background: #ffffff; border: 1px solid #0085CB; font-family: arial; font-size: 12px; width: 600px;" align="center" cellspacing="0" cellpadding="0">
			<tr>
				<td style="background: #0085CB; color: white; font-weight: bold; font-size: 16px; padding: 5px;">
					<?=APP_NAME?>: Credentials Reminder
				</td>
			</tr>
			<tr>
				<td style="padding: 10px;">
					<?=$first_name?>,
					<br><br>
					As you may have noticed, our login process has slightly changed. You are now asked for your account as well as username and password. Below are the credentials you should be using to login to <?=APP_NAME?>.
					<br><br>
					<b>Account:</b> <?=$account?>
					<br>
					<b>Username:</b> <?=$username?>
					<br>
					<b>Password:</b> <?=$password?>
					<br><br>
					<a href='<?=$account_url?>'><?=$account_url?></a>
				</td>
			</tr>
		</table>
	</body>
</html>