<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

$task_id = intval($_GET['task']);
$on_change = $_GET['onchange'];
$myUser_id = intval($_GET['userid']);

$sql = "select users.user_id, users.fname, users.lname, users.dba from users, task_link where task_link.user_id=users.user_id and task_link.task_type_id='".$task_id."' and task_link.account_id='".$_SESSION['ao_accountid']."' and users.is_active=1 and users.is_deleted=0 order by users.lname asc";
$res = DBUtil::query($sql);

while(list($user_id, $fname, $lname, $dba)=mysqli_fetch_row($res))
{
  $selected = "";
  if($myUser_id==$user_id)
    $selected = "selected";
  
  if(!empty($dba))
    $dba = " (".$dba.")";
  $list.= "<option value='".$user_id."' ".$selected.">".$lname.", ".$fname[0].$dba."</option>\n\r";
}

$list = "<option value=''></option>\n\r${list}";
if(mysqli_num_rows($res)==0)
  $list = "<option value=''>None Available</option>";
  
print<<<HTML
  <select name='contractor' id='contractor' onchange='$on_change'>
    $list
  </select>
HTML;

?>