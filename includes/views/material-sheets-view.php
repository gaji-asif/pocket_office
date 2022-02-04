<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }

$materialSheets = $myJob->fetchMaterialSheets();
foreach($materialSheets as $materialSheet) {
    $confirmed = !empty($materialSheet['confirmed']);
    $tooltip = $confirmed ? 'Confirmed ' . DateUtil::formatDate($materialSheet['confirmed']) . '. Click to undo confirm.' : 'Click to confirm';
    $iconClass = $confirmed ? 'green' : 'light-gray';
    $rel = $confirmed ? 'undo-confirm-order' : 'confirm-order';
?>
<li>
    <i class="icon-paper-clip"></i>&nbsp;
    <i class="icon-ok action <?=$iconClass?>" rel="<?=$rel?>" data-job-id="<?=$materialSheet['job_id']?>" data-sheet-id="<?=$materialSheet['sheet_id']?>" title="<?=$tooltip?>" tooltip></i>&nbsp;
    <a href="" rel="open-modal" data-script="job_materials.php?sheet_id=<?=$materialSheet['sheet_id']?>&job_id=<?=$materialSheet['job_id']?>" title="View material sheet" tooltip>
        <?=$materialSheet['label']?>
    </a>
</li>
<?php
}
?>