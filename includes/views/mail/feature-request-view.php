<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title><?=APP_NAME?> Feature Request</title>
	</head>
	<body>
		<table align="center" border="0" cellpadding="5" width="600" style="background: #f5f5f5; border: 1px solid #e3e3e3;">
			<tr>
				<td align="center" colspan="2">
					<strong>An <?=APP_NAME?> Feature Request has been submitted</strong>
				</td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">From:</td>
				<td><?=@$from?></td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">Username:</td>
				<td><?=@$username?></td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">Account:</td>
				<td><?=@$account?></td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">Email:</td>
				<td><?=@$email?></td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">Details:</td>
				<td><?=stripslashes(@$details)?></td>
			</tr>
		</table>
	</body>
</html>