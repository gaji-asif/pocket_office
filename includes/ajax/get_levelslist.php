<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

if($_SESSION['ao_founder']!=1)
  die("Insufficient Rights");

$sql = "select level_id, level from levels order by level_id asc";
$res = DBUtil::query($sql);

?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">

<?php

if(mysqli_num_rows($res)==0) {
?>
    <tr><td align="center"><b>No Levels Found</b></td></tr>
<?php
}
$i=1;
while(list($level_id, $level)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';
?>
  <tr class='<?php echo $class; ?>' valign='top'>
    <td><a href='javascript: Request.make("includes/ajax/get_level.php?id=<?php echo $level_id; ?>", "levelscontainer", "yes", "yes");' class='basiclink'><?php echo $level; ?></a></td>
  </tr>
<?php  
  $i++;
}

?>
</table>