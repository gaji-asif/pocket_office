<?php 



include '../common_lib.php'; 

if(!ModuleUtil::checkAccess('edit_users'))

  die("Insufficient Rights");



$myUser = new User(RequestUtil::get('id'));

?>



<table width="100%" border="0" cellpadding="0" cellspacing="0">



<?php



$action = $_GET['action'];

$module_id = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['module']);



if($action=='add_ex_on')

  $sql = "insert into exceptions values(0, '".$module_id."', '".$myUser->getUserID()."', 0, 1)";

if($action=='add_ex_off')

  $sql = "insert into exceptions values(0, '".$module_id."', '".$myUser->getUserID()."', 0, 0)";

if($action=='add_ex_ownon')

  $sql = "insert into exceptions values(0, '".$module_id."', '".$myUser->getUserID()."', 1, 1)";

if($action=='add_ex_ownoff')

  $sql = "insert into exceptions values(0, '".$module_id."', '".$myUser->getUserID()."', 0, 1)";

if($action=='del_ex')

  $sql = "delete from exceptions where module_id='".$module_id."' and user_id='".$myUser->getUserID()."' limit 1";


if($sql!='')

  DBUtil::query($sql);



?>

  <tr>

    <td align="center" colspan=2>

      <table width='95%' border="0">

        <tr>

          <td colspan=2>

            <b>Modules:</b>

            <span class='smallnote'>

              <br />A = Access

              <br />O = Ownership required

              <br /><font color="red">Exception to Group</font>

            </span>

          </td>

        </tr>

        <tr>

          <td>

            <table border="0" width="100%">

              <tr valign='top'>

                <td width='50%'>

                  <table border="0" width="100%">

                    <tr>

                      <td align="center" width=20><b>A</b></td>

                      <td align="center" width=20><b>O</b></td>

                      <td>&nbsp;</td>

                    </tr>


                      

<?php

$sql = "select modules.module_id, modules.title, module_access.module_access_id, module_access.ownership as access_ownership, modules.ownership as ownership_enabled, exceptions.exception_id, exceptions.ownership as exception_ownership, exceptions.onoff as exception_onoff". 

       " from modules". 

       " left join". 

       " exceptions on(modules.module_id=exceptions.module_id and exceptions.user_id='".$myUser->getUserID()."')".

       " left join".

       " module_access on (modules.module_id=module_access.module_id)".

       " and module_access.account_id='".$_SESSION['ao_accountid']."' and module_access.level='".$myUser->getLevel()."'".

       " group by modules.module_id".

       " order by modules.title asc";

$res = DBUtil::query($sql);



$row_limit = round(mysqli_num_rows($res)/2);

$i=0;

while(list($module_id, $title, $access, $owner, $has_ownership, $exception_id, $exception_ownership, $exception_onoff)=mysqli_fetch_row($res))

{

  if($i==$row_limit)

  {

?>

                  </table>

                </td>

                <td width='50%'>

                  <table border="0" width="100%">

                    <tr>

                      <td align="center" width=16><b>A</b></td>

                      <td align="center" width=16><b>O</b></td>

                      <td>&nbsp;</td>

                    </tr>   

<?php

    $i=0;

  }

  if($exception_id=='')

  {

    $checked = '';

    $action = "add_ex_on";

    if($access!='')

    {

      $checked = "checked";

      $action = "add_ex_off";

    }

      

//ADD or ADD

?>

        <tr>

          <td align="center" width=16>

            <input type='checkbox' class='a_check' name='<?php echo $module_id; ?>' <?php echo $checked; ?> onclick='Request.make("get_userpermissions.php?id=<?php echo $myUser->getUserID(); ?>&module=<?php echo $module_id; ?>&action=<?php echo $action; ?>","userpermissionscontainer","","yes")'>

          </td>

<?php

    $disabled = 'disabled';

    if($has_ownership==1)

      $disabled = '';  

    $checked_owner = '';

    $action = "add_ex_ownon";

    if($owner==1)

    {

      $checked_owner = "checked";

      $action = "add_ex_ownoff";

    }



//ADD

?>

          <td align="center" width=16>

            <input <?php echo $disabled; ?> class='o_check' type='checkbox' name='<?php echo $module_id; ?>' <?php echo $checked_owner; ?> onclick='Request.make("get_userpermissions.php?id=<?php echo $myUser->getUserID(); ?>&module=<?php echo $module_id; ?>&action=<?php echo $action; ?>","userpermissionscontainer","","yes")'>

          </td>

          <td colspan=2 style='font-size: 11px;'><?php echo $title; ?></td>

        </tr>

<?php

  }

  else

  {

    $checked = '';

    $action = "edit_ex_on";

    if($exception_onoff==1)

    {

      $checked = "checked";

      $action = "edit_ex_on";

    }



//EDIT or DEL

?>

        <tr>

          <td align="center" width=16>

            <input disabled type='checkbox' <?php echo $checked; ?>>

          </td>

<?php

    $checked_owner = '';

    if($exception_ownership==1)

      $checked_owner = "checked";

?>

          <td align="center" width=16>

            <input disabled type='checkbox' <?php echo $checked_owner; ?>>

          </td>

          <td align="center" width=16>

            <a href='javascript:Request.make("get_userpermissions.php?id=<?php echo $myUser->getUserID(); ?>&modid=<?php echo $level_id; ?>&module=<?php echo $module_id; ?>&action=del_ex","userpermissionscontainer","","yes")'>

              <img src='<?=IMAGES_DIR?>/icons/delete.png' border="0">

            </a>

          </td>

          <td style='color: red; font-size: 11px;'><?php echo $title; ?></td>

        </tr>

<?php

  }

  $i++;

}

?>

                  </table>

                </td>

              </tr>

            </table>

          </td>

        </tr>

      </table>

    </td>

  </tr>

</table>


