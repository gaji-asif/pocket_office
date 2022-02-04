<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

if(isset($_POST['subject']))
{
	$subject = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['subject']);
	$body = $_POST['body'];
	$to = $_POST['user'];
	$group = $_POST['group'];

	if($to == '' && $group == '')
		UIUtil::showAlert('Please choose a recipient or group');
	else if($subject == '')
		UIUtil::showAlert('Please enter a subject');
	else if($body == '')
		UIUtil::showAlert('Please enter a message');
	else
	{
		$body = mysqli_real_escape_string(DBUtil::Dbcont(),$body);
		$sql = "insert into messages values(0, '" . $_SESSION['ao_accountid'] . "', '" . $_SESSION['ao_userid'] . "', '" . $subject . "', '" . $body . "', now())";
		DBUtil::query($sql);

		$message_id = DBUtil::getInsertId();

		if($group == '')
		{
			$sql = "insert into message_link values(0, '" . $message_id . "', '" . $to . "', null, 0)";
			DBUtil::query($sql);

			NotifyUtil::emailFromTemplate('new_message', $to);
		}
		else
		{
			if($group == 'ALL')
				$sql = "select user_id from users where account_id='" . $_SESSION['ao_accountid'] . "' and user_id<>'" . $_SESSION['ao_userid'] . "' and is_active=1 and is_deleted=0 order by lname asc";
			else
				$sql = "select usergroups_link.user_id from usergroups_link, users where usergroups_link.user_id=users.user_id and usergroups_link.usergroup_id='" . $group . "' and users.is_active=1 and users.is_deleted=0";
			$res = DBUtil::query($sql);
			while (list($user_id) = mysqli_fetch_row($res))
			{
				$sql = "insert into message_link values(0, '" . $message_id . "', '" . $user_id . "', null, 0)";
				DBUtil::query($sql);

				NotifyUtil::emailFromTemplate('new_message', $user_id);
			}
		}
?>

<script>
	alert("Message Sent");
	parent.deleteOverlay();
</script>
<?php
		die();
	}
}

$users_array = UserModel::getAll(TRUE, $firstLast);

?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Compose Message
              </td>
              <td align="right">
                <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
if(sizeof($users_array) == 0)
{
?>
            <tr>
              <td colspan=2 style='color: red' class="listrownoborder">
                <b>No Users Found</b>
              </td>
            </tr>
<?php
}
else
{
?>
            <tr>
              <td width="25%" class="listitemnoborder">
                <b>To User:</b>
              </td>
              <td class="listrownoborder">
                <form method="post" name='message' action='?'>
                  <select name='user'>
                    <option value=''></option>
<?php
  foreach($users_array as $user)
  {
?>
                    <option value='<?=$user['user_id']?>'><?=$user['lname'] . ", " . $user['fname']?></option>
<?php
  }
?>
                  </select>
              </td>
            </tr>
<?php
  if(ModuleUtil::checkAccess("message_group"))
  {
      $groups = UserModel::getAllUserGroups();
?>
            <tr>
              <td class="listitem">&nbsp;</td>
              <td class="listrow">
                <b>OR</b>
              </td>
            </tr>
            <tr>
              <td class="listitem">
                <b>To Group:</b>
              </td>
              <td class="listrow">
                  <select name='group'>
                    <option value=''></option>
                    <option value='ALL'><?php echo $label; ?>All Users</option>
<?php
    foreach($groups as $group)
    {
?>
                    <option value='<?=$group['usergroup_id']?>'><?=$group['label']?></option>
<?php
    }
?>
                  </select>
              </td>
            </tr>
<?php
  }
?>
            <tr>
              <td class="listitem"><b>Subject:</b></td>
              <td class="listrow"><input type="text" size=40 name='subject' value='<?php echo $subject; ?>'></td>
            </tr>
            <tr valign='top'>
              <td class="listitem"><b>Message:</b></td>
              <td class="listrow">
                <textarea name='body' style='width:100%;' rows=15><?php echo $body; ?></textarea>
              </td>
            </tr>

            <tr>
              <td colspan=2 align="right" class="listrow">
                  <input type="submit" value='Send'>
                </form>
              </td>
            </tr>
<?php
}
?>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>