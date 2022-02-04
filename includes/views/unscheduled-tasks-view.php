<?php
if(empty($unscheduledTasks) || !is_array($unscheduledTasks)) { return; }
?>
<tr valign="top">
    <td class="listitem"><b>Unscheduled Tasks:</b></td>
    <td class="listrow no-padding">
        <ul class="advancement-requirements">
<?php
foreach($unscheduledTasks as $unscheduledTask) { 
?>
            <li class="<?=UIUtil::getContrast($unscheduledTask['color'])?>" style="background-color: <?=$unscheduledTask['color']?>;">
                <a href="" rel="open-modal" data-script="get_task.php?id=<?=$unscheduledTask['task_id']?>">
                    <?=$unscheduledTask['task']?>
                </a>
            </li>
<?php
}
?>
        </ul>
    </td>
</tr>