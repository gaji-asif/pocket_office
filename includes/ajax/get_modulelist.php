<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

if($_SESSION['ao_founder']!=1)
  die("Insufficient Rights");

$sql = "select module_id, title, description from modules order by title asc";
$res = DBUtil::query($sql);

?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">

<?php
if(mysqli_num_rows($res)==0) {
?>
    <tr><td align="center" colspan=2><b>No Modules Found</b></td></tr>
<?php
}

$i=1;
while(list($module_id, $title, $description)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';
?>
  <tr class='<?php echo $class; ?>' valign='top'>
    <td width=250><a href='javascript: Request.make("includes/ajax/get_module.php?id=<?php echo $module_id; ?>", "modulescontainer", "yes", "yes");' class='basiclink'><?php echo $title; ?></a></td>
    <td><?php echo $description; ?></td>
  </tr>
<?php  
  $i++;
}

?>
</table>