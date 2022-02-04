<?php 
include '../common_lib.php';
UserModel::isAuthenticated();
$myUser = UserModel::getMe();
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" width="100%"> 
        <tr>
          <td width=16><img src='<?=IMAGES_DIR?>/icons/user_16.png'></td>
          <td width="25%"><b>Account Level:</b></td>
          <td>
<?php 
echo $myUser->level_title;
if($_SESSION['ao_founder']==1)
  echo " (<i>Founder</i>)"; 
?>
          </td>
        </tr>
      </table>
    </td>
  </tr>
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <td width=203>Module</td>
          <td>Description</td>
          <td width=85 align="center">Ownership</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding"> 
<?php

if($_SESSION['ao_founder']==1)
  $sql = "select title, description from modules order by title asc";
else $sql = "select modules.title, modules.description, module_access.ownership from modules, module_access".
            " where module_access.module_id=modules.module_id and module_access.account_id='".$_SESSION['ao_accountid']."' and module_access.level='".$_SESSION['ao_level']."'".
            " order by modules.title asc";
            
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
        <tr>
          <td width=16><img src='<?=IMAGES_DIR?>/icons/warning_16.png'></td>
          <td><b>No Permissions</b></td>
        </tr>
<?php
}
$i=1;
while(list($title, $description, $ownership)=mysqli_fetch_row($res))
{
  $owner_img = '&nbsp;';
  if($ownership==1 && $_SESSION['ao_founder']==0)
    $owner_img = "<img src='<?=IMAGES_DIR?>/icons/tick_16.png'>";
  
  $class='odd';
  if($i%2==0)
    $class='even';
?>
        <tr class='<?php echo $class; ?>'>
          <td width=200><b><?php echo $title; ?></b></td>
          <td><?php echo $description; ?></td>
          <td width=85 align="center"><?php echo $owner_img; ?></td>
        </tr>  
<?php
  $i++;
}
?>
      </table>
    </td>
  </tr>
  
  
  
</table>