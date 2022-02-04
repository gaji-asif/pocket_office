<?php

include '../common_lib.php';
ModuleUtil::checkAccess('view_users', TRUE, TRUE);
$action = RequestUtil::get('action');
$myUser = new User(RequestUtil::get('id'), FALSE);
if(!$myUser->exists()) {
    UIUtil::showListError('User not found!');
}
$sql1 = "select xactimate_header from users where user_id=".$myUser->getUserID();
$res1 = DBUtil::queryToArray($sql1);
UserModel::storeBrowsingHistory("{$myUser->fname[0]} {$myUser->lname}", 'icon-user', 'users.php', $myUser->getUserID());
if($action == 'restore' && ModuleUtil::checkAccess('edit_users')) {
    RequestUtil::set('is_deleted', 0);
    FormUtil::update('users');
    $myUser = new User($myUser->getUserID());
}
if($action == 'xactimate_delete' && ModuleUtil::checkAccess('edit_users')) 
{
    $sql1 = "select xactimate_header from users where user_id=".$myUser->getUserID();
    $res1 = DBUtil::queryToArray($sql1);
    $new_filename = '';
    $sql = "update users set xactimate_header='".$new_filename."' where     user_id=".$myUser->getUserID();
    if(DBUtil::query($sql))
    {
        unlink(UPLOADS_PATH. '/xactimate_header/'.$res1[0]['xactimate_header']);
    }
}
?>
<table width="100%" border="0" class="data-table" cellpadding="5" cellspacing="0">
<?=ViewUtil::loadView('user-list-row', array('myUser' => $myUser))?>
<?php
if($myUser->isDeleted()) {
    if(ModuleUtil::checkAccess('edit_users')) {
        $restore_str = "<a href='javascript: if(confirm(\"Are you sure?\")){Request.make(\"includes/ajax/get_user.php?id=".$myUser->getUserID()."&action=restore\",\"userscontainer\",\"yes\",\"yes\");}' class='boldlink'>Restore User</a>";
        if(UserModel::licenseLimit() <= UserModel::numCurrentUsers()) {
            $restore_str = "<span style='font-size:12px;font-weight:normal;'>You account has a License User Limit of <b>".UserModel::licenseLimit()."</b>. You currently have <b>".UserModel::numCurrentUsers()."</b> users.<br /> To restore this user, please delete another user first or raise your License User Limit.</span>";
        }
    }
?>
    <tr>
        <td colspan="3" class="smalltitle" align="center" style="border-top:1px solid #cccccc;">
            <br />
            THIS USER HAS BEEN DELETED
            <br /><br />
            <?=$restore_str?>
        </td>
    </tr>
<?php
} else {
?>
    <tr>
        <td colspan="4" style="border-top:1px solid #cccccc;">
            <div id="view-actions">
                <div class="btn-group pull-right">
<?php
    if($myUser->getUserID() !== $_SESSION['ao_userid']) {
?>
                <div rel="open-chat" data-user-id="<?=$myUser->getUserID()?>" class="btn btn-small" title="Chat" tooltip>
                    <i class="icon-comments-alt"></i>
                </div>
<?php
    }
    if(ModuleUtil::checkAccess('edit_users')) {
?>
                    <div rel="open-modal" data-script="edit_user.php?id=<?=$myUser->getUserID()?>" class="btn btn-small" title="Edit User" tooltip>
                        <i class="icon-pencil"></i>
                    </div>
                    <div rel="send-credentials" data-user-id="<?=$myUser->getUserID()?>" class="btn btn-small" title="Send User Credentials" tooltip>
                        <i class="icon-unlock-alt"></i>
                    </div>
<?php

    }

    if(ModuleUtil::checkAccess('view_user_history')&&!$myUser->isDeleted()) {
?>

                    <div rel="open-modal" data-script="get_useraccess.php?id=<?=$myUser->getUserID()?>" class="btn btn-small" title="Access History" tooltip>
                        <i class="icon-key"></i>
                    </div>
                    <div rel="open-modal" data-script="get_useractivity.php?id=<?=$myUser->getUserID()?>" class="btn btn-small" title="Activity" tooltip>
                        <i class="icon-bar-chart"></i>
                    </div>
                    <div rel="open-modal" data-script="get_user_browsing_history.php?id=<?=$myUser->getUserID()?>" class="btn btn-small" title="Recent Browsing History" tooltip>
                        <i class="icon-time"></i>
                    </div>
<?php
    }
?>
                </div>
                <div class="pull-right" id="view-action-info"></div>
            </div>
        </td>
    </tr>
  <tr>
    <td colspan="4">
      <table width="100%" border="0" cellpadding="0" cellspacing="0">
        <tr>
          <td class="smalltitle">User Profile:</td>
        </tr>
        <tr valign='top'>
          <td>
            <table border="0" width="100%" class='listtable' cellpadding="0" cellspacing="0">
              <tr>
                <td width=150 class="listitemnoborder"><b>Username:</b></td>
                <td class="listrownoborder"><?=$myUser->username?></td>
              </tr>
              <tr>
                <td class="listitem"><b>Access Level:</b></td>
                <td class="listrow"><?=$myUser->level_title?></td>
              </tr>
              <tr>
                <td class="listitem"><b>Office:</b></td>
                <td class="listrow"><?=$myUser->office?></td>
              </tr>
              <tr>
                <td class="listitem"><b>Account DOB:</b></td>
                <td class="listrow"><?=$myUser->reg_date?></td>
              </tr>
              <tr>
                <td class="listitem"><b>DBA:</b></td>
                <td class="listrow"><?=$myUser->dba?></td>
              </tr>
              <tr>
                <td class="listitem"><b>Email:</b></td>
                <td class="listrow"><?=$myUser->email?></td>
              </tr>
              <tr>
                <td class="listitem"><b>Phone:</b></td>
                <td class="listrow"><?=UIUtil::formatPhone($myUser->phone)?></td>
              </tr>
              <tr>
                <td class="listitem"><b>Tasks Assigned:</b></td>
<?php
$sql = "select task_type.task from task_type, task_link where task_type.task_type_id=task_link.task_type_id and task_link.user_id='".$myUser->getUserID()."' order by task_type.task asc";

$res = DBUtil::query($sql);
if(mysqli_num_rows($res)==0)
{
?>
                <td class="listrow">None Assigned</td>
<?php
}
else
{
  $i=1;
  while(list($task_type)=mysqli_fetch_row($res))
  {
    if($i>1)
    {
?>
              <tr>
                <td class="listitemnoborder">&nbsp;</td>
                <td class="listrownoborder"><?=$task_type?></td>
              </tr>
<?php
    }
    else
    {
?>
                <td class="listrow"><?=$task_type?></td>
              </tr>
<?php
    }
    $i++;
  }
}
?>
              </tr>
              <tr>
                <td class="listitem"><b>Notes:</b></td>
                <td class="listrow"><?=$myUser->notes?></td>
              </tr>
<?php

  $sql = "select navaccess_id from nav_access where navigation_id=23 and account_id='".$_SESSION['ao_accountid']."' and level='".$_SESSION['ao_level']."' limit 1";
  $res = DBUtil::query($sql);
  if(mysqli_num_rows($res)!=0)
  {
?>

              <tr>
                <td class="listitem"><b>Message:</b></td>
                <td class="listrow"><input type="button" value="Compose" rel="open-modal" data-script="composemessageprofile.php?id=<?=$myUser->getUserID()?>"></td>
              </tr>
<?php
  }
?>


<?php
  $sql1 = "select xactimate_header from users where user_id=".$myUser->getUserID();
  $res1 = DBUtil::query($sql1);
  $filename="";
  if(mysqli_num_rows($res1)!=0)
  {
      list($xactimate_header)=mysqli_fetch_row($res1);
      if(!empty($xactimate_header)){
          $filenamearray=explode("_",$xactimate_header);
  
?>
<tr>
                <td class="listitem"><b>Xactimate Header :</b></td>
                <td class="listrow"><?=$filenamearray[1]?>&nbsp;&nbsp;&nbsp;  <a download='<?=$xactimate_header?>' href='<?=UPLOADS_DIR.'/xactimate_header/'.$xactimate_header?>'><i class="icon-download"></i></a>
                &nbsp;&nbsp;&nbsp;
                <a href='javascript: if(confirm("Are you sure?")){Request.make("includes/ajax/get_user.php?id=<?=$myUser->getUserID()?>&action=xactimate_delete","userscontainer","yes","yes");}' class='boldlink'><i class="icon-trash" style="color:red" > </i></a>

                </td>
              </tr>
              <?php
  }
  }
              ?>

            </table>
          </td>
        </tr>
      </table>
    </td>
  </tr>
<?php
}
?>

  <tr><td colspan=10>&nbsp;</td></tr>
  <tr>
    <td colspan=10 class='infofooter'>
      <a href="javascript:clearElement('notes'); Request.make('<?=AJAX_DIR?>/get_userlist.php', 'userscontainer', true, true);" class='basiclink'>
		<i class="icon-double-angle-left"></i>&nbsp;Back
	  </a>
    </td>
  </tr>
</table>