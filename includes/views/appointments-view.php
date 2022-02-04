<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }

$appointments = $myJob->fetchAppointments();
foreach($appointments as $appointment) {
?>
<li>
    <i class="icon-calendar"></i>&nbsp;
    <a href="" rel="open-modal" data-script="get_appointment.php?id=<?=$appointment['appointment_id']?>" title="View appointment" tooltip>
        <?=$appointment['title']?>
    </a>
    <span class="smallnote">- <?=DateUtil::getScheduleWeekLink($appointment['datetime'])?></span>
</li>
<?php
}
?>