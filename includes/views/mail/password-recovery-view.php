<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title><?=APP_NAME?>: Password Recovery</title>
	</head>
	<body style="background: #EDEDED;">
		<table style="background: #ffffff; border: 1px solid #0085CB; font-family: arial; font-size: 12px; width: 600px;" align="center" cellspacing="0" cellpadding="0">
			<tr>
				<td style="background: #0085CB; color: white; font-weight: bold; font-size: 16px; padding: 5px;">
					<?=APP_NAME?>: Password Recovery
				</td>
			</tr>
			<tr>
				<td style="padding: 10px;">
					Per your request, here are your <?=APP_NAME?> login credentials:
					<br><br>
					Account: <?=$account?>
					<br>
					Username: <?=$username?>
					<br>
					Password: <?=$pw?>
					<br><br>
					If you did not initiate this request, please contact your administrator immediately. Please visit <?=APP_NAME?> to login.
					<br>
					<a href='<?=$account_url?>'><?=$account_url?></a>
				</td>
			</tr>
		</table>
	</body>
</html>