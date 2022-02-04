<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');

$sql = "select events.user_id, events.title, events.text, date_format(events.date, '%l:%i %p'), date_format(events.date, '%Y-%m-%d'), date_format(events.timestamp, '%Y-%m-%d @ %l:%i %p'), users.fname, users.lname, events.global".
       " from events, users".
       " where event_id='".$_GET['id']."'".
       " and users.user_id=events.user_id".
       " limit 1";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
  die('Invalid Event Data');

list($user_id, $title, $text, $time, $date, $added, $fname, $lname, $global)=mysqli_fetch_row($res);

if($global==1)
  $global_str='Yes';
else $global_str='No';

if($_GET['action']=='del' && $user_id==$_SESSION['ao_userid'])
{
    $sql = "delete from events where event_id='".$_GET['id']."' limit 1";
    DBUtil::query($sql);
?>

<script>

  Request.makeModal('<?=AJAX_DIR?>/widget_today.php', 'widget_today', true, true, true);

</script>
<?php
}

?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                View Event Detail
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
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td width="25%" class="listitemnoborder"><b>Title:</b></td>
              <td class="listrownoborder"><?php echo $title ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Creator:</b></td>
              <td class="listrow"><?php echo $fname." ".$lname; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Date:</b></td>
              <td class="listrow"><?php echo $date; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Time:</b></td>
              <td class="listrow"><?php echo $time; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Global:</b></td>
              <td class="listrow"><?php echo $global_str; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Description:</b></td>
              <td class="listrow"><?php echo prepareText($text); ?></td>
            </tr>
            <tr>
              <td class="listitemnoborder"></td>
              <td class="listrownoborder"><span class='smallnote'>Added <?php echo $added; ?></span></td>
            </tr>
<?php
if($user_id==$_SESSION['ao_userid'])
{
?>
            <tr>
              <td colspan=2>
                <table border="0" width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width=20 class="listrow"><img src='<?=IMAGES_DIR?>/icons/delete.png'></td>
                    <td class="listrow"><a onclick='return confirm("Are you sure?");' href='?action=del&id=<?php echo $_GET['id']; ?>' class='basiclink'>Delete</a></td>
                  </tr>
                </table>
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