<html xmlns="http://www.w3.org/1999/xhtml">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
		<title><?=APP_NAME?> - New Account</title>
	</head>
	<body>
		<table align="center" border="0" cellpadding="5" width="600" style="background: #f5f5f5; border: 1px solid #e3e3e3; font-family: helvetica, arial, sans-serif;">
			<tr>
				<td colspan="2">
					<h2>Welcome to <?=APP_NAME?>!</h2>
					<p>Your new <?=APP_NAME?> account has been created. Please login using the information below.</p>
				</td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">Account Name:</td>
				<td><?=@$name?></td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">Primary Username:</td>
				<td><?=@$username?></td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">Password:</td>
				<td><?=@$password?></td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">Email:</td>
				<td><?=@$email?></td>
			</tr>
			<tr valign="top">
				<td align="right" width="150">URL:</td>
				<td><a href="http://<?=$_SERVER['SERVER_NAME']?>">http://<?=$_SERVER['SERVER_NAME']?></a></td>
			</tr>
			<tr><td colspan="2">&nbsp;</td></tr>
			<tr>
				<td colspan="2">
					<h3>Next Steps</h3>
					<ul>
						<li>Add company logo</li>
						<li>Configure module & navigation access</li>
						<li>Setup offices</li>
						<li>Setup stage advancement access</li>
						<li>Setup status holds</li>
						<li>Setup materials & suppliers</li>
						<li>Setup task types</li>
						<li>Add users and setup user groups</li>
						<li>Setup warranty types</li>
						<li>Configure email and SMS templates</li>
					</ul>
				</td>
			</tr>
		</table>
	</body>
</html>