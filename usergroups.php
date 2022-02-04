<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder']!=1)
  die("Insufficient Rights");

if(isset($_POST['group']))
{
  $label = $_POST['group'];
  if($label!='')
  {
    $sql = " insert into usergroups values(0, '".$_SESSION['ao_accountid']."', '".mysqli_real_escape_string(DBUtil::Dbcont(),$label)."')";
    DBUtil::query($sql)or die(mysqli_error());
    UIUtil::showAlert('New Group Added');
  }
  else UIUtil::showAlert('Required Information Missing');
}
if($_GET['action']=='del' && $_GET['id']!='')
{
    $sql = "delete from usergroups where usergroup_id='".$_GET['id']."' and account_id='".$_SESSION['ao_accountid']."' limit 1";
    DBUtil::query($sql)or die(mysqli_error());
}

?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - User Groups</h1>
    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr>
        <td>
          <form method='post' action='?' name='addgroup' style='margin-bottom:0;'>
          <input type='text' name='group' size=30>
          <input type='submit' value='Add'>
          </form>
        </td>
      </tr>
      <tr>
        <td>
          <table border=0 width="100%" align='left' cellpadding=0 cellspacing=0 class='containertitle'>
            <tr>
              <td width=250>
                Group Label
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border=0 width="100%" align='left' cellpadding=2 cellspacing=0 class='infocontainernopadding'>
<?php
$sql = "select usergroups.usergroup_id, usergroups.label, count(usergroups_link.usergroups_link_id) from usergroups left outer join usergroups_link on (usergroups_link.usergroup_id=usergroups.usergroup_id) where account_id='".$_SESSION['ao_accountid']."' group by usergroups.usergroup_id order by label asc";
$res = DBUtil::query($sql)or die(mysqli_error());

$i=1;
while(list($id, $label, $count)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';
?>
            <tr class='<?php echo $class; ?>'>
              <td width=16><a href='javascript:if(confirm("Are you sure?")){window.location="?id=<?php echo $id; ?>&action=del";}'><img src='<?=ROOT_DIR?>/images/icons/delete.png'></td>
              <td><a href='usergrouplist.php?id=<?php echo $id; ?>' class='basiclink'><?php echo $label; ?></a><span class='smallnote'> (<?php echo $count; ?>)</span></td>
            </tr>
<?php
  $i++;
}
?>
          </table>
        </td>
      </tr>
      <tr><td>&nbsp;</td></td>
    </table>
  </body>
</html>