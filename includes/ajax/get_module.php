<?php
include '../common_lib.php';
UserModel::isAuthenticated();

if($_SESSION['ao_founder']!=1 || $_GET['id']=='')
  die("Insufficient Rights");

$sql = "select title, description from modules where module_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' limit 1";
$res = DBUtil::query($sql);

?>

<table width="100%" border="0" class="data-table" cellpadding=2 cellspacing="0">

<?php
if(mysqli_num_rows($res)==0) {
?>
    <tr><td align="center" colspan=2><b>Module Not Found</b></td></tr>
<?php
}

list($title, $description)=mysqli_fetch_row($res);
?>

  <tr valign='top'>
    <td class="smalltitle" width=250><?php echo $title; ?></td>
    <td><?php echo $description; ?></td>
  </tr>
  <tr><td colspan=2>&nbsp;</td></tr>
  <tr>
    <td width=250>&nbsp;</td>
    <td><b>Permissions:</b></td>
  </tr>

<?php
$sql = "select levels.level from levels, module_access".
       " where levels.level_id=module_access.level and module_access.account_id='".$_SESSION['ao_accountid']."' and module_access.module_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."'".
       " order by levels.level_id desc";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
  echo "<tr><td width=250>&nbsp;</td><td>No Permissions</td></tr>";

while(list($level)=mysqli_fetch_row($res))
{
?>
  <tr><td width=250>&nbsp;</td><td><?php echo $level; ?></td></tr>
<?php
}
?>

  <tr><td colspan=10>&nbsp;</td></tr>
  <tr>
    <td colspan=10 class='infofooter'>
		<a href="javascript:Request.make('includes/ajax/get_modulelist.php', 'modulescontainer', true, true);" class='basiclink'>
			<i class="icon-double-angle-left"></i>&nbsp;Back
		</a>
    </td>
  </tr>
</table>