<?php
include '../common_lib.php';
ModuleUtil::checkAccess('view_announcements', TRUE, TRUE);
$id = RequestUtil::get('id');
$announcement = new Announcement($id, FALSE);
if(!$announcement->exists()) {
    UIUtil::showListError('Announcement not found!');
}
UserModel::storeBrowsingHistory(MapUtil::get($announcement, 'subject'), 'icon-bullhorn', 'announcements.php', $id);
$announcement->markRead();
?>

<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">
<?php
$view_data = array(
	'myAnnouncement' => $announcement
);
echo ViewUtil::loadView('announcement-list-row', $view_data);
?>
</table>
<table width="100%" border="0" class="data-table" cellpadding="5" cellspacing="0">
    <tr>
        <td colspan="4">
            <div class="btn-group pull-right">
<?php
if(ModuleUtil::checkAccess('view_user_history')) {
?>
                <div rel="open-modal" data-script="get_announcementhistory.php?id=<?=$id?>" class="btn btn-small" title="Access History" tooltip>
                    <i class="icon-time"></i>
                </div>
<?php
}
if(ModuleUtil::checkAccess('edit_announcements')) {
?>
                <div rel="make-request" data-action="<?=AJAX_DIR?>/get_announcementlist.php?action=del&id=<?=$id?>" data-destination="announcements-container" data-confirm="Delete <?=$subject?>?" class="btn btn-small" title="Delete Announcement" tooltip>
                    <i class="icon-remove"></i>
                </div>
<?php
}
?>
            </div>
        </td>
    </tr>
    <tr>
        <td colspan=4>
          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr><td class="smalltitle">Announcement:</td></tr>
            <tr>
                <td colspan=4>
                    <table border="0" width="100%" cellpadding="0" cellspacing="0" class='listtable'>
                        <tr valign='top'>
                            <td width="25%" class="listitemnoborder">
                                <b>Author:</b>
                            </td>
                            <td class="listrownoborder">
                                <?=UserUtil::getDisplayName($announcement->get('user_id'))?>
                            </td>
                        </tr>
                        <tr valign='top'>
                            <td width="25%" class="listitem">
                                <b>Created:</b>
                            </td>
                            <td class="listrow">
                                <?=DateUtil::formatDateTime($announcement->get('timestamp'))?>
                            </td>
                        </tr>
                        <tr valign='top'>
                            <td width="25%" class="listitem">
                                <b>Announcement:</b>
                            </td>
                            <td class="listrow">
                                <?=$announcement->get('body')?>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td colspan=4 class="infofooter">
            <a href="javascript:Request.make('includes/ajax/get_announcementlist.php', 'announcements-container', true, true);">
                <i class="icon-double-angle-left"></i>&nbsp;Back
            </a>
        </td>
    </tr>
</table>