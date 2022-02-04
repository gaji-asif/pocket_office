<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');

//build and execute query
$event_id = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']);
$sql = "SELECT e.*, u.fname, u.lname
        FROM events e, users u
        WHERE event_id = '$event_id'
            AND u.user_id = e.user_id
        LIMIT 1";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res) == 0) {
    die('Invalid Event Data');
}
$event = mysqli_fetch_array($res);

$canEditEvent = ModuleUtil::checkAccess('event_readwrite') && ModuleUtil::canAccessObject('event_readwrite', $event);

//get usergroup data
$user_groups = ScheduleModel::getEventUserGroups($event_id);

//build user groups string
if(!empty($user_groups)) {
    $labels = array();
    foreach($user_groups as $user_group) {
        $labels[] = $user_group['label'];
    }
    $user_groups_str = implode(', ', $labels);
}

$global_str = 'No';
if($event['global'] == 1) {
    $global_str='Yes';
}

$event_time = DateUtil::formatTime($event['date']);
if($event['all_day'] == 1) {
	$event_time = 'All Day Event';
}

if($_GET['action'] == 'del' && $event['user_id'] == $_SESSION['ao_userid']) {
    $sql = "delete from events where event_id='$event_id' limit 1";
    DBUtil::query($sql);
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_calweek.php', 'schedulecontainer', true, true, true);
</script>
<?php
	die();
}
?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                View Event Detail
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td width="25%" class="listitemnoborder"><b>Title:</b></td>
              <td class="listrownoborder"><?=MapUtil::get($event, 'title')?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Creator:</b></td>
              <td class="listrow"><?=$event['lname']?>, <?=$event['fname']?> </td>
            </tr>
            <tr>
              <td class="listitem"><b>Start Date:</b></td>
              <td class="listrow"><?=DateUtil::formatDate($event['date'])?></td>
            </tr>
            <tr>
              <td class="listitem"><b>End Date:</b></td>
              <td class="listrow"><?=DateUtil::formatDate($event['end_date'])?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Time:</b></td>
              <td class="listrow"><?=$event_time?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Global:</b></td>
              <td class="listrow"><?=$global_str?></td>
            </tr>
            <tr>
              <td class="listitem"><b>User Groups:</b></td>
              <td class="listrow"><?=$user_groups_str?></td>
            </tr>
            <tr valign='top'>
              <td class="listitem"><b>Description:</b></td>
              <td class="listrow"><?=UIUtil::cleanOutput(MapUtil::get($event, 'text'), FALSE)?></td>
            </tr>
<?php
if($canEditEvent) {
?>
            <tr>
              <td colspan=2 class="listrow text-right">
                  <div class="btn-group">
                      <div class="btn btn-small btn-danger" 
                           title="Delete"
                           rel="change-window-location"
                           data-url="?action=del&id=<?=MapUtil::get($event, 'event_id')?>"
                           data-confirm="Are you sure you want to delete event '<?=MapUtil::get($event, 'title')?>'?" 
                           tooltip><i class="icon-trash"></i></div>
                      <div class="btn btn-small btn-default" 
                           title="Edit"
                           rel="change-window-location"
                           data-url="edit_event.php?&id=<?=MapUtil::get($event, 'event_id')?>"
                           tooltip><i class="icon-pencil"></i></div>
                  </div>
              </td>
            </tr>
<?php
}
?>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
