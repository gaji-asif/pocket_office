<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
?>
<h1 class="page-title"><i class="icon-cogs"></i>Settings</h1>
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr><td colspan=2>&nbsp;</td></tr>
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign='center'>
                    <td>
                        Basic Information
                    </td>
                    <td align="right">
                        <i class="icon-unlock-alt grey" rel="open-modal" data-script="edit_password.php" title="Change Password"></i>&nbsp;
                        <i class="icon-pencil edit grey" rel="open-modal" data-script="edit_user_profile.php" title="Edit Basic Information"></i>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class='infocontainernopadding' id='basicinfocontainer'></td>
    </tr>
</table>
<br />
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign='center'>
                    <td>
                        Interface Settings
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class='infocontainernopadding' id='interfacecontainer'></td>
    </tr>
</table>
<br />
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign='center'>
                    <td>
                        Application Notifications
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="infocontainernopadding" id="app-notifications"></td>
    </tr>
</table>
<br />
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign='center'>
                    <td>
                        Stage Notifications
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class="infocontainer" id="stagenotificationscontainer"></td>
    </tr>
</table>
<br />
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign='center'>
                    <td>
                        Job Subscriptions
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class='infocontainernopadding' id='subscriptions-container'></td>
    </tr>
</table>
<br />
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign='center'>
                    <td>
                        Conversations Being Watched
                    </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td class='infocontainernopadding' id='watching-container'></td>
    </tr>
</table>
<br />
<script type="text/javascript">
    Request.make('<?=AJAX_DIR?>/get_basicinformation.php', 'basicinfocontainer', true, true);
    Request.make('<?=AJAX_DIR?>/get_app_notifications.php', 'app-notifications', true, true);
    Request.make('<?= AJAX_DIR ?>/get_user_subscriptions.php', 'subscriptions-container', true, true);
    Request.make('<?= AJAX_DIR ?>/get_user_watching.php', 'watching-container', true, true);
    Request.make('<?= AJAX_DIR ?>/get_interfacesettings.php', 'interfacecontainer', true, true);
    Request.make('<?= AJAX_DIR ?>/get_stagenotifications.php', 'stagenotificationscontainer', true, true);
</script>
</body>
</html>
