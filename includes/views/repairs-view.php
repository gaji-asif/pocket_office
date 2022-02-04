<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }

$repairs = $myJob->fetchRepairs();
foreach($repairs as $repair) {
    $isCompleted = !empty($repair['completed']);
    $repairTooltip = $isCompleted ? 'Completed ' . DateUtil::formatDate($repair['completed']) . '. Click to view task details.' : 'View Rush Job details';
    $schedule = !empty($repair['startdate']) ? ' - ' . DateUtil::getScheduleWeekLink($repair['startdate']) : '';
?>
<li>
    <i class="icon-plus green"></i>&nbsp;
    <a class="red <?=$isCompleted ? 'line-through' : ''?>" href="" rel="open-modal" data-script="get_repair.php?id=<?=$repair['repair_id']?>" title="<?=$repairTooltip?>" tooltip><?=$repair['fail_type']?></a>
    <span class="smallnote"><?=$schedule?></span>
</li>
<?php
}
?>