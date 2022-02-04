<?php
include '../common_lib.php';
UserModel::isAuthenticated();

if($_SESSION['ao_founder']!=1)
  die("Insufficient Rights");

$sql = "select count(user_id) from users where account_id='".$_SESSION['ao_accountid']."' and is_deleted<>1";
$res = DBUtil::query($sql)or dide(mysqli_error());
list($total_num_users)=mysqli_fetch_row($res);

$sql = "select * from accounts where account_id='".$_SESSION['ao_accountid']."' limit 1";
$res = DBUtil::query($sql)or dide(mysqli_error());

list($id, $name, $primary, $email, $phone, $address, $city, $state, $zip, $reg_date, $is_active, $logo, $job_unit, $hash, $license_limit)=mysqli_fetch_row($res);
?>

<table border="0" class="data-table" cellpadding="0" cellspacing="0" width="100%">
	<tr valign='top'>
		<td width=300 align="center" class="listrownoborder" style="border-right: 1px solid #e1e1e1;">
<?php
if(!empty($logo))
{
?>
			<img height=125 width=280 src='logos/<?php echo $logo; ?>'>
<?php
}
?>
		</td>
		<td>
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
				<tr valign='top'>
					<td width="25%" class="listitemnoborder"><b>Company:</b></td>
					<td class="listrownoborder">
						<?php echo $name; ?>
					</td>
				</tr>
				<tr valign='top'>
					<td class="listitem"><b>Address:</b></td>
					<td class="listrow">
						<?php echo $address; ?>
					</td>
				</tr>
				<tr>
					<td class="listitemnoborder"></td>
					<td class="listrownoborder">
						<?php echo $city . ", " . $state . " " . $zip; ?>
					</td>
				</tr>
				<tr>
					<td class="listitem"><b>Phone:</b></td>
					<td class="listrow">
						<?php echo UIUtil::formatPhone($phone); ?>
					</td>
				</tr>
				<tr>
					<td class="listitem"><b>Email:</b></td>
					<td class="listrow">
						<?php echo $email; ?>
					</td>
				</tr>
				<tr>
					<td class="listitem"><b>Primary Contact:</b></td>
					<td class="listrow">
						<?php echo $primary; ?>
					</td>
				</tr>
				<tr>
					<td class="listitem"><b>Account DOB:</b></td>
					<td class="listrow">
						<?php echo $reg_date; ?>
					</td>
				</tr>
				<tr>
					<td class="listitem"><b>Total Users:</b></td>
					<td class="listrow">
						<?php echo $total_num_users; ?>
					</td>
				</tr>
				<tr>
					<td class="listitem"><b>License Limit:</b></td>
					<td class="listrow">
						<?php echo $license_limit; ?>
					</td>
				</tr>
				<tr>
					<td class="listitem"><b>Change Logo:</b></td>
					<td class="listrow">
						<form method="post" name='modify_logo' enctype="multipart/form-data" action='?'>
						<input type="file" name="logo_upload" />
						<input type="submit" value="Save" />
						</form>
					</td>
				</tr>
			</table>
		</td>
	</tr>
</table>