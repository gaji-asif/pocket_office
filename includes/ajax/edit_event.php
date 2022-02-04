<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('event_readwrite', TRUE);

//get event
$event = DBUtil::getRecord('events');
if(!$event) {
    UIUtil::showModalError('Event not found!');
}

ModuleUtil::canAccessObject('event_readwrite', $event, TRUE);

//edit event
$errors = array();
if(RequestUtil::get('submit')) {
    $title = RequestUtil::get('title');
    $description = RequestUtil::get('text');
    $startDate = RequestUtil::get('start_date');
    $endDate = RequestUtil::get('end_date');
    $_POST['date'] = DateUtil::formatMySQLDate($startDate) . " " . RequestUtil::get('time');
    
	if(empty($title) || empty($description)) {
		$errors[] = 'Required fields missing';
	}
    if(strtotime($startDate) > strtotime($endDate)) {
		$errors[] = 'Start date must be before end date';
    }
    
	if(!count($errors)) {
        FormUtil::update('events');
?>

<script>
	parent.window.location.href = '<?=DateUtil::getScheduleWeekUrl(RequestUtil::get('startdate'))?>';
</script>
<?php
	die();
	}
}
?>
<form action="?id=<?=MapUtil::get($event, 'event_id')?>" method="post">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Edit Event '<?=MapUtil::get($event, 'title')?>'</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Title:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrownoborder">
            <input type="text" name="title" value="<?=MapUtil::get($event, 'title')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Start Date:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <?php $defaultDate = DateUtil::formatMySQLDate(MapUtil::get($event, 'date')); ?>
            <input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="start_date" value="<?=$defaultDate?>" />
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>End Date:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <?php $defaultDate = MapUtil::get($event, 'end_date'); ?>
            <input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="end_date" value="<?=$defaultDate?>" />
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Start Time:</b>
        </td>
        <td class="listrow">
            <?=FormUtil::getTimePicklist('time', DateUtil::formatMySQLTime(MapUtil::get($event, 'date')))?>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Global:</b>
        </td>
        <td class="listrow">
            <input type="checkbox" name="global" value="1" onchange="$('#grouplist').toggleClass('hidden')" <?=MapUtil::get($event, 'global') ? 'checked' : ''?>>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Description:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <textarea rows="7" name="text"><?=UIUtil::cleanOutput(MapUtil::get($event, 'text'))?></textarea>
        </td>
    </tr>
    <tr>
        <td colspan="3" align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>