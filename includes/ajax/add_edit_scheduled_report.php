<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
$myUser = UserModel::getMe();
if(!$myUser->exists()) {
    UIUtil::showModalError('Could not retrieve user data');
}

$savedReport = new SavedReport(RequestUtil::get('saved_report_id'), FALSE);
if(!$savedReport->exists()) {
    UIUtil::showModalError('Saved report not found');
}

//try to get existing scheduled report
$sql = "SELECT *
        FROM scheduled_reports
        WHERE user_id = '{$myUser->getMyId()}'
            AND saved_report_id = '{$savedReport->getMyId()}'
            AND active = 1
            AND account_id = '{$_SESSION['ao_accountid']}'
        LIMIT 1";
$scheduledReport = DBUtil::queryToMap($sql);

$day = MapUtil::get($scheduledReport, 'week_day');
$dayFrequency = MapUtil::get($scheduledReport, 'day_frequency');
$weekFrequency = MapUtil::get($scheduledReport, 'week_frequency');
$isDaily = !is_null($dayFrequency);
$isWeekly = !is_null($weekFrequency);


$errors = array();
if(RequestUtil::get('delete')) {
    $scheduleReportId = MapUtil::get($scheduledReport, 'scheduled_report_id');
    $sql = "UPDATE scheduled_reports
            SET active = 0
            WHERE scheduled_report_id = $scheduleReportId
            LIMIT 1";
    DBUtil::query($sql);
?>
<script>
    parent.deleteOverlay();
    die();
</script>
<?php
} else if(RequestUtil::get('submit')) {
    $scheduleInterval = RequestUtil::get('schedule_interval');
    $isDaily = $scheduleInterval == 'daily';
    $isWeekly = $scheduleInterval == 'weekly';
    $dailyDay = RequestUtil::get('daily_day');
    $weeklyDay = RequestUtil::get('weekly_day');
    $dayFrequency = RequestUtil::get('day_frequency');
    $weekFrequency = RequestUtil::get('week_frequency');
    if($isDaily) {
        if(!$dailyDay) {
            $errors[] = 'Please select a daily start day';
        }
        if(!$dayFrequency) {
            $errors[] = 'Please select a daily interval';
        }
        
        //nullify weekly stuff and set week day
        RequestUtil::set('week_frequency', NULL);
        $day = $dailyDay;
        
    } else if($isWeekly) {
        if(!$weeklyDay) {
            $errors[] = 'Please select a weekly day';
        }
        if(!$weekFrequency) {
            $errors[] = 'Please select a weekly interval';
        }
        
        //nullify daily stuff and set week day
        RequestUtil::set('day_frequency', NULL);
        $day = $weeklyDay;
        
    } else {
        $errors[] = 'Please select daily or weekly interval';
    }
    
    if(!$errors) {
        //reset last_sent
        RequestUtil::set('last_sent', NULL);
        
        //set week day
        RequestUtil::set('week_day', $day);
        
        //update
        if($scheduledReport) {
            $sql = FormUtil::createUpdateSql('scheduled_reports', MapUtil::get($scheduledReport, 'scheduled_report_id'));
        }
        //insert
        else {
            $sql = "INSERT INTO scheduled_reports (account_id, user_id, saved_report_id, "
                    . ($isDaily ? 'day_frequency' : 'week_frequency') . ", week_day)
                    VALUES ('{$myUser->get('account_id')}', '{$myUser->getMyId()}', '{$savedReport->getMyId()}', '"
                    . ($isDaily ? $dayFrequency : $weekFrequency) . "', '$day')";
        }
        DBUtil::query($sql);
?>
<script>
    parent.deleteOverlay();
    die();
</script>
<?php
    }
}
?>
<form method="post" action="?saved_report_id=<?=$savedReport->getMyId()?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>Add/Edit Scheduled Report</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td class="listitem"><b>Saved Report:</b></td>
        <td class="listrow"><input type="text" value="<?=$savedReport->getDisplayName()?>" disabled></td>
    </tr>
    <tr>
        <td class="listitem"><b>Daily:</b></td>
        <td class="listrow"><input type="radio" name="schedule_interval" value="daily" <?=$isDaily ? 'checked' : ''?>></td>
    </tr>
    <tr class="daily_show">
        <td class="listitem"><b>Start Day:</b></td>
        <td class="listrow">
            <select name="daily_day" class="tss-select"><?=UIUtil::getWeekdayOptions($isDaily ? $day : NULL)?></select>
        </td>
    </tr>
    <tr class="daily_show">
        <td class="listitem"><b>Interval:</b></td>
        <td class="listrow">
            <select name="day_frequency" class="tss-select"><?=UIUtil::getDayIntervalOptions($isDaily ? $dayFrequency : NULL)?></select>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Weekly:</b></td>
        <td class="listrow"><input type="radio" name="schedule_interval" value="weekly"<?=$isWeekly ? 'checked' : ''?>></td>
    </tr>
    <tr class="weekly_show">
        <td class="listitem"><b>Interval:</b></td>
        <td class="listrow">
            <select name="week_frequency" class="tss-select"><?=UIUtil::getWeekIntervalOptions($isWeekly ? $weekFrequency : NULL)?></select>
        </td>
    </tr>
    <tr class="weekly_show">
        <td class="listitem"><b>Day:</b></td>
        <td class="listrow">
            <select name="weekly_day" class="tss-select"><?=UIUtil::getWeekdayOptions($isWeekly ? $day : NULL)?></select>
        </td>
    </tr>
    <tr>
        <td colspan=2 class="listrow" align="right">
            <input rel="change-window-location"
                   data-url="?delete=1&id=<?=$savedReport->getMyId()?>"
                   data-confirm="Are you sure you want to remove this scheduled report?"
                   type="button"
                   value="Remove" <?=!$scheduledReport ? 'disabled' : ''?>>
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
<script>
var $radios = $('[name="schedule_interval"]');
$(function() {
    $radios.change(handleRadioChange).change();
});
    
function handleRadioChange() {
    var val = $('[name="schedule_interval"]:checked').val();
    
    $('[class$="_show"]').hide();
    $('.' + val + '_show').show();
}
</script>
</body>
</html>