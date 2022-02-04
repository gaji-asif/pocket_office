<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder']!=1)
  die("Insufficient Rights");


$id = $_GET['id'];

if(isset($_POST['group']))
{
  $id = $_POST['group'];
  $sql = "delete from usergroups_link where usergroup_id='".$id."';";
  DBUtil::query($sql)or die(mysqli_error());

  $user_list=$_POST['users'];

  while(list ($key,$user_id) = @each ($user_list))
  {
    if($user_id!='')
    {
      $sql = " insert into usergroups_link values(0, '".$id."', '".$user_id."');";
      DBUtil::query($sql)or die(mysqli_error());
    }
  }
  UIUtil::showAlert('List Updated');
}

$sql = "select label from usergroups where usergroup_id='".$id."' limit 1";
$res = DBUtil::query($sql)or die(mysqli_error());

if(mysqli_num_rows($res)==0)
  die('Invalid User Group Data');

list($label)=mysqli_fetch_row($res);

?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - <a href="/usergroups.php">User Groups</a> - <?=$label?></h1>
    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr>
        <td>
          <table border=0 width="100%" align='left' cellpadding=0 cellspacing=0 class='containertitle'>
            <tr>
              <td>
                User Name
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <form name='userlist' action='?' method='post' style='margin-bottom:0;'>
          <input type='hidden' name='group' value='<?php echo $id; ?>'>
          <table border=0 width="100%" align='left' cellpadding=2 cellspacing=0 class='infocontainernopadding'>
<?php
$sql = "select users.fname, users.lname, users.user_id, usergroups_link.usergroups_link_id from users left join usergroups_link on (usergroups_link.user_id=users.user_id and usergroups_link.usergroup_id='".$id."') where users.account_id='".$_SESSION['ao_accountid']."' and users.is_active=1 and users.is_deleted=0 order by users.lname asc";
$res = DBUtil::query($sql)or die(mysqli_error());

while(list($fname, $lname, $user_id, $link)=mysqli_fetch_row($res))
{
  $checked = '';
  $fullname = $lname.", ".$fname;
  if($link!='')
  {
    $checked = 'checked';
    $fullname = "<b>".$lname.", ".$fname."</b>";
  }
?>
            <tr>
              <td width=20>
                <input type='checkbox' name=users[] value='<?php echo $user_id; ?>' <?php echo $checked; ?>>
              </td>
              <td><?php echo $fullname; ?></td>
            </tr>
<?php
}
?>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border=0 width="100%" align='left' cellpadding=0 cellspacing=0>
            <tr>
              <td align='right'>
                <input type='submit' value='Submit'>
                <input type='reset' value='Reset'>
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
    </table>
  </body>
</html>