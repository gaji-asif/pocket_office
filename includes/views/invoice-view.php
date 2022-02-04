<div class="journal-container hover-button-container clearfix" id="journal-<?=MapUtil::get(@$journal, 'journal_id')?>">
    <ul class="journal-info">
        <li><?=UserUtil::getDisplayName(@$journal['user_id'])?></li>
        <li><?=DateUtil::formatDate(@$journal['timestamp'])?>&nbsp;@&nbsp;<?=DateUtil::formatTime(@$journal['timestamp'])?></li>
        <li><?=StageModel::getCSVStagesByStageNum(@$journal['stage_num'])?></li>
    </ul>
    <div class="journal-copy">
<?php
$journalText = StrUtil::convertMentionsToLinks(@$journal['text']);
?>
        <?=UIUtil::cleanOutput($journalText, FALSE)?>
    </div>
<?php
//	if((ModuleUtil::checkAccess('delete_journals') && !moduleOwnership('delete_journals')) || (moduleOwnership('delete_journals') && (JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id == $_SESSION['ao_userid'] || $myJob->user_id == $_SESSION['ao_userid'] || @$journal['user_id'] == $_SESSION['ao_userid']))) {
if(ModuleUtil::checkJobModuleAccess('delete_journals', $myJob)) {
?>
    <div class="btn btn-danger btn-small" rel="delete-journal" data-journal-id="<?=MapUtil::get(@$journal, 'journal_id')?>" title="Delete journal" tooltip>
        <i class="icon-remove"></i>
    </div>
<?php
}
?>
</div>