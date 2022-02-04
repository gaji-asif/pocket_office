<?php 

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('view_users'))
  die("Insufficient Rights");

$user_id = $_GET['id'];

if($user_id=='')
  die();

$myUser = new User($user_id);

if($myUser->notes!='')
{
  print<<<HTML
    <table border="0" cellspacing="0" cellpadding="0" width="100%">
      <tr>
        <td class="listitem" width="25%"><b>User Notes:</b></td>
        <td class="listrow">$myUser->notes</td>
      </tr>
    </table>
HTML;
}
?>