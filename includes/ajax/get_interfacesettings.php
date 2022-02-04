<?php
include '../common_lib.php';
UserModel::isAuthenticated();

if(isset($_GET['widget']) && $_GET['widget']!='' && $_GET['state']!='')
{
  $widget = $_GET['widget'];
  $state = $_GET['state'];

  $sql = "update settings set ".mysqli_real_escape_string(DBUtil::Dbcont(),$widget)."='".mysqli_real_escape_string(DBUtil::Dbcont(),$state)."' where user_id='".$_SESSION['ao_userid']."' limit 1";
  DBUtil::query($sql);
  $_SESSION['ao_'.$widget]=$state;
}
else if(isset($_GET['action']) && $_GET['action']=='results' && $_GET['value']!='')
{
  $sql = "update settings set num_results = '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['value'])."' where user_id='".$_SESSION['ao_userid']."' limit 1";
  DBUtil::query($sql);
  $_SESSION['ao_numresults'] = $_GET['value'];
}

$sql = "select num_results from settings where user_id='".$_SESSION['ao_userid']."' limit 1";
$res = DBUtil::query($sql);

list($num_results)=mysqli_fetch_row($res);

?>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr valign="top">
    <td>
      <table border="0" width="100%" cellpadding="0" cellspacing="0">
        <tr>
          <td class="listitem" width="25%"><b>Dashboard Widgets:</b></td>
          <td class="listrow">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tr valign="center">
                <td width=20><img src='<?=IMAGES_DIR?>/icons/info_16.png'></td>
                <td class='smallnote'>Click to <b>Activate</b> / <i>Deactivated</i></td>
              </tr>
            </table>
          <td>
        </tr>
<?php
if(ModuleUtil::checkAccess('view_schedule'))
{
?>
        <tr>
          <td class="listitemnoborder">&nbsp;</td>
          <td class="listrownoborder">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width=20><img src='<?=IMAGES_DIR?>/icons/calendar_16.png'></td>
<?php
  if($_SESSION['ao_widget_today']==0)
  {
    $class='inactive';
    $state=1;
  }
  else
  {
    $class='navlink';
    $state=0;
  }
?>
                <td><a href='javascript: Request.make("includes/ajax/get_interfacesettings.php?widget=widget_today&state=<?php echo $state; ?>", "interfacecontainer", "", "yes");' class='<?php echo $class; ?>'>Today</a></td>
              </tr>
            </table>
          </td>
        </tr>
<?php
}
?>
        <tr>
          <td class="listitemnoborder">&nbsp;</td>
          <td class="listrownoborder">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width=20><img src='<?=IMAGES_DIR?>/icons/email_16.png'></td>
<?php
  if($_SESSION['ao_widget_inbox']==0)
  {
    $class='inactive';
    $state=1;
  }
  else
  {
    $class='navlink';
    $state=0;
  }
?>
                <td><a href='javascript: Request.make("includes/ajax/get_interfacesettings.php?widget=widget_inbox&state=<?php echo $state; ?>", "interfacecontainer", "", "yes");' class='<?php echo $class; ?>'>Inbox</a></td>
              </tr>
            </table>
          </td>
        </tr>
<?php
if(ModuleUtil::checkAccess('view_announcements'))
{
?>
        <tr>
          <td class="listitemnoborder">&nbsp;</td>
          <td class="listrownoborder">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width=20><img src='<?=IMAGES_DIR?>/icons/flag_16.png'></td>
<?php
  if($_SESSION['ao_widget_announcements']==0)
  {
    $class='inactive';
    $state=1;
  }
  else
  {
    $class='navlink';
    $state=0;
  }
?>
                <td><a href='javascript: Request.make("includes/ajax/get_interfacesettings.php?widget=widget_announcements&state=<?php echo $state; ?>", "interfacecontainer", "", "yes");' class='<?php echo $class; ?>'>Recent Announcements</a></td>
              </tr>
            </table>
          </td>
        </tr>
<?php
}
if(ModuleUtil::checkAccess('view_documents'))
{
?>
        <tr>
          <td class="listitemnoborder">&nbsp;</td>
          <td class="listrownoborder">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width=20><img src='<?=IMAGES_DIR?>/icons/folder_16.png'></td>
<?php
  if($_SESSION['ao_widget_documents']==0)
  {
    $class='inactive';
    $state=1;
  }
  else
  {
    $class='navlink';
    $state=0;
  }
?>
                <td><a href='javascript: Request.make("includes/ajax/get_interfacesettings.php?widget=widget_documents&state=<?php echo $state; ?>", "interfacecontainer", "", "yes");' class='<?php echo $class; ?>'>Recent Documents</a></td>
              </tr>
            </table>
          </td>
        </tr>
<?php
}
if(ModuleUtil::checkAccess('view_jobs'))
{
?>
        <tr>
          <td class="listitemnoborder">&nbsp;</td>
          <td class="listrownoborder">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
              <tr>
                <td width=20><img src='<?=IMAGES_DIR?>/icons/briefcase_16.png'></td>
<?php
  if($_SESSION['ao_widget_urgent']==0)
  {
    $class='inactive';
    $state=1;
  }
  else
  {
    $class='navlink';
    $state=0;
  }
?>
                <td><a href='javascript: Request.make("includes/ajax/get_interfacesettings.php?widget=widget_urgent&state=<?php echo $state; ?>", "interfacecontainer", "", "yes");' class='<?php echo $class; ?>'>Urgent Jobs</a></td>
              </tr>
            </table>
          </td>
        </tr>
<?php
}
?>
        <tr>
            <td class="listitem"><b>Name Order:</b></td>
            <td class="listrow">
                <select data-key="name_order" data-alert="true" class="onchange-set-meta" data-type="user">
                    <?=UIUtil::getNameOrderOptions()?>
                </select>
            </td>
        </tr>
      </table>
    </td>
  </tr>
</table>