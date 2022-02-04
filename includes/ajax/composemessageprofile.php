<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');

if(isset($_POST['subject']))
{
	$subject = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['subject']);
	$body = $_POST['body'];
	$to = $_POST['user'];

	if($subject == '')
		UIUtil::showAlert('Please enter a subject');
	else if($body == '')
		UIUtil::showAlert('Please enter a message');
	else
	{
		$body = mysqli_real_escape_string(DBUtil::Dbcont(),$body);
		$sql = "insert into messages values(0, '" . $_SESSION['ao_accountid'] . "', '" . $_SESSION['ao_userid'] . "', '" . $subject . "', '" . $body . "', now())";
		DBUtil::query($sql);

		$message_id = DBUtil::getInsertId();

		$sql = "insert into message_link values(0, '" . $message_id . "', '" . $to . "', null, 0)";
		DBUtil::query($sql);

		NotifyUtil::emailFromTemplate('new_message', $to);
?>

	<script>
	  alert("Message Sent");
	  parent.deleteOverlay();
</script>
<?php
		die();
	}
}

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
<?php
$myUser = new User(RequestUtil::get('id'));
?>
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
            <tr>
              <td width="25%" class="listitemnoborder">
                <b>To User:</b>
              </td>
              <td class="listrownoborder">
                <form method="post" name='message' action='?id=<?php echo $myUser->getUserID(); ?>'>
                <input type='hidden' name='user' value='<?php echo $myUser->getUserID(); ?>'>
                <?php echo $myUser->fname." ".$myUser->lname; ?>
              </td>
            </tr>
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
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>