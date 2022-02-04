<?php

include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);

echo ViewUtil::loadView('doc-head');

if(isset($_POST['to']))
{
	$to = $_POST['to'];
	$body = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['body']);
	$subject = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['subject']);
	$sql = "insert into messages values(0, '".$_SESSION['ao_accountid']."', '".$_SESSION['ao_userid']."', '".$subject."', '".$body."', now())";
	DBUtil::query($sql)or die(mysqli_error());

	$message_id = mysqli_insert_id();
	$sql = "insert into message_link values(0, '".$message_id."', '".$to."', null, 0)";
	DBUtil::query($sql)or die(mysqli_error());

	NotifyUtil::emailFromTemplate('new_message', $to);
//	emailFromTemplate('new_message',$to,'','','','');
?>
	<script type='text/javascript'>
		alert("Message Sent");
	</script>
<?php
}
?>
<script type='text/javascript'>
  function checkReply()
  {
    body = document.getElementById("body").value;
    if(body=='')
    {
      alert("Message cannot be blank");
      return false;
    }
    return true;
  }
</script>

<h1 class="page-title"><i class="icon-envelope"></i><?=$this_page->title?></h1>

    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr valign='middle'>
        <td>
          <input type='button' value='Inbox' onclick="Request.make('<?=AJAX_DIR?>/get_messagelist.php', 'messagecontainer', true, true);document.getElementById('tofrom').innerHTML='From';">
          <input type='button' value='Sent' onclick="Request.make('<?=AJAX_DIR?>/get_sentmessagelist.php', 'messagecontainer', true, true);document.getElementById('tofrom').innerHTML='To';">
          <input type='button' value='Trash' onclick="Request.make('<?=AJAX_DIR?>/get_deletedmessagelist.php', 'messagecontainer', true, true);document.getElementById('tofrom').innerHTML='To';">
          <input type='button' value='Compose' onclick="applyOverlay('composemessage.php');">
        </td>
        <td width=20>
          <img src='<?=IMAGES_DIR?>/icons/search_16.png'>
        </td>
        <td width=10>
          <input type='text' id='searchby' value='Search...' onclick='this.value=""'>
        </td>
        <td width=160>
          <input type='button' value='Inbox' onclick="Request.make('<?=AJAX_DIR?>/get_messagelist.php?search='+document.getElementById('searchby').value, 'messagecontainer', true, true);document.getElementById('tofrom').innerHTML='From';">
          <input type='button' value='Sent' onclick="Request.make('<?=AJAX_DIR?>/get_sentmessagelist.php?search='+document.getElementById('searchby').value, 'messagecontainer', true, true);document.getElementById('tofrom').innerHTML='To';">
          <input type='button' value='Trash' onclick="Request.make('<?=AJAX_DIR?>/get_deletedmessagelist.php?search='+document.getElementById('searchby').value, 'messagecontainer', true, true);document.getElementById('tofrom').innerHTML='To';">
        </td>
      </tr>
    </table>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr>
        <td>
          <table width='100%' cellpadding="0" cellspacing="0" class='data-table-header'>
            <tr>
              <td width=26>&nbsp;</td>
              <td>Subject</td>
              <td width="15%">Sent</td>
              <td width="20%" id='tofrom'>From</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td id='messagecontainer'></td>
      </tr>
      <tr>
        <td>
<?php
  if($_GET['id']!='')
  {
?>
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/get_message.php?id=<?php echo $_GET['id']; ?>', 'messagecontainer', true, true);
          </script>
<?php
  }
  else
  {
?>
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/get_messagelist.php', 'messagecontainer', true, true);
          </script>
<?php
  }
?>
        </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
    </table>
  </body>
</html>
