<?php
if(!$myJob->exists() || !$myJob instanceof job || !is_array($jobAction)) {
    return;
}
$scriptUrl = "{$jobAction['script']}?id={$myJob->job_id}"; 
?>
<i class="icon-pencil icon-grey inline-job-action-link" rel="open-modal" data-script="<?=$scriptUrl?>" title="<?=$tooltip?>" tooltip></i>