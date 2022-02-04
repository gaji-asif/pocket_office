<?php
if(empty($myJob) || get_class($myJob) !== 'Job' || empty($requirements) || !is_array($requirements)) { return; }
?>
<tr valign="top">
    <td class="listitem"><b>Stage Requirements:</b></td>
    <td class="listrow no-padding">
        <ul class="advancement-requirements">
<?php
foreach($requirements as $requirement) { 
    $specialInstructions = $requirement['special_instructions'];
    
    $rel = 'open-modal';
    $data = "data-script=\"{$requirement['script']}?id={$myJob->job_id}\"";
    
    //switch tabs...
    if(StrUtil::startsWith($specialInstructions, 'switch-job-tab::')) {
        $rel = 'switch-job-tab';
        $pieces = explode('::', $specialInstructions);
        $data = 'data-tab="' . end($pieces) . '"';
    }
    
    //check if completed
    $requirementComplete = $myJob->checkRequirement($requirement['query'], $specialInstructions);
            
    //module access
    $hasModuleAccess = ModuleUtil::checkJobModuleAccess($requirement['hook'], $myJob);
?>
            <li class="<?=$requirementComplete ? 'requirement-completed' : ''?>" title="<?=htmlentities($requirement['description'], ENT_QUOTES)?>" tooltip>
                <?php if($hasModuleAccess) { ?><a href="" rel="<?=$rel?>" <?=$data?>><?php } ?>
                    <?=$requirement['label']?>
                <?php if($hasModuleAccess) { ?></a><?php } ?>
            </li>
<?php
}
?>
        </ul>
    </td>
</tr>