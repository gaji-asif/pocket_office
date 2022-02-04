<?php
if(!$myJob->exists()) { return; }
$myCustomer = new Customer($myJob->customer_id);
$stages = JobUtil::getCSVStages($myJob->job_id);
$stageAge = $myJob->getStageAge();
$diff = $stageAge - $myJob->duration;

//are all requirements complete
$allRequirementsMet = @$myJob->requirementsMet() && StageModel::getFinalStageNum() != @$myJob->stage_num;

$completed_row_color='';
if($myJob->stage_num==StageModel::getFinalStageNum())
{
    $completed_row_color=' job_completed_row';
}

//duration
$duration = $myJob->duration;

$color = "";
if($duration != '9999' && $diff > -3) {
    if($diff < 0)
        $color = "yellow";
    else if($diff < 6)
        $color = "orange";
    else
        $color = "red";
}
if($duration == '9999' || empty($duration))
    $duration = "No Limit";

//repair
$repairStr = $myJob->hasOpenRepairs() ? ", <span style='color: red; font-weight: bold;'>Expedited Estimate Request</span>" : '';

//pif
$paidTitle = 'Job Not Paid in Full';
if($myJob->pif_date != '') {
    $paidTitle = 'Job Paid in Full on ' . DateUtil::formatDate($myJob->pif_date);
    $paidIconClass = 'green';
}

//get stage requirements
$stageRequirements = $myJob->fetchStageRequirements(NULL, TRUE, TRUE);
$programRequirement = isset($stageRequirements['Program/Non Program Docs']) ? $stageRequirements['Program/Non Program Docs'] : NULL;
if($programRequirement) {
    //complete?
    $requirementComplete = $myJob->checkRequirement($programRequirement['query'], $programRequirement['special_instructions']);
    
    //program docs complete
    $approvedTitle = '';
    if($requirementComplete) {
        $approvedIconClass = 'yellow';
        $approvedTitle = 'Program/Non Program Docs Complete';
    }

    //referral paid
    if(!empty($myJob->insurance_approval) && $requirementComplete) {
        $approvedIconClass = 'green';
        $approvedTitle = "Program/Non Program Docs Complete and Claim Approved on " . DateUtil::formatDate($myJob->insurance_approval);
    }
} else {
    //claim approved
    $approvedTitle = "Claim Not Approved";
    if(!empty($myJob->insurance_approval)) {
        $approvedIconClass = 'yellow';
        $approvedTitle = "Claim Approved on " . DateUtil::formatDate($myJob->insurance_approval);
    }

    //referral paid
    if(!empty($myJob->referral_paid)) {
        $approvedIconClass = 'green';
        $approvedTitle = "Referral Paid on " . DateUtil::formatDate($myJob->referral_paid);
    } 
}

$today = strtotime(date('Y-m-d'));
$expirationDate = empty($myJob->status_hold_expires) ? $today : strtotime($myJob->status_hold_expires);

//status hold and stage
//<b>#{$myJob->stage_num}:</b>
$stage_str = "{$myJob->getCSVStages()}$repairStr";
$holdClass = $myJob->getStageClass();
if(!empty($myJob->status_hold_id) && $expirationDate >= $today) {
    $holdClass = 'hold';
    $stage_str = "<b>HOLD:</b> {$myJob->status_hold}";
    if(!empty($myJob->status_hold_expires)) {
        $stage_str .= ' (exp. ' . DateUtil::formatShortDate($myJob->status_hold_expires) . ')';
    }
}

//warranty
$warrantyIconClass = 'icon-star-empty';
$warrantyIconStyle = '';
$warrantyTitle = 'No Warranty';
$warrantyId = MetaUtil::get($myJob->meta_data, 'job_warranty');
if(!empty($warrantyId)) {
	$warranties = JobUtil::getAllWarranties();
	$warrantyIconClass = 'icon-star';
	$warrantyIconStyle = "color: {$warranties[$warrantyId]['color']};";
	$warrantyTitle = "Warranty: {$warranties[$warrantyId]['label']}";
}

//contract
//$hasContract = $myJob->hasUpload('contract');
$contractIconClass = 'icon-file-text-alt';
$contractTitle = 'No Contract';
if($myJob->hasUpload('contract')) {
    $contractIconClass = 'icon-file-text-alt yellow';
    $contractTitle = 'Contract Uploaded';
}


//payment
if($myJob->hasCredit('final')) {
    $paymentTitle = 'Job final payment received';
    $paymentIcon = 'icon-smile';
    $paymentIconClass = 'green';
} else if($myJob->hasCredit('1st')) {
    $paymentTitle = 'Job 1st payment received';
    $paymentIcon = 'icon-smile';
    $paymentIconClass = 'yellow';
} else {
    $paymentTitle = '';
    $paymentIcon = 'icon-meh';
    $paymentIconClass = '';
}






if(@$complete_row !== false) {
?>
<tr class="data-table-row <?=@$allRequirementsMetClass?>">
<?php
}
if(@$quick_settings !== false && @$quick_settings_spacer !== true) {
?>
    <td width=10 class="settings-container data-table-cell">
        <div>
			<i class="icon-cog grey" rel="show-quick-settings" data-job-id="<?=$myJob->job_id ?>" title="Quick Settings" tooltip></i>
            <div class="quick-settings-container" id="quick-settings-container-job<?=$myJob->job_id ?>"></div>
        </div>
    </td>
<?php
}
if(@$quick_settings_spacer === true) {
?>
    <td width="20">&nbsp;</td>
<?php
}
if(@$complete_row !== false) {
?>
    <td class="data-table-cell">
        <table border=0 cellpadding=0 cellspacing=0 width='100%' class="jobs-data-table <?=@$completed_row_color?>" <?=@$rowExtras?>>
<?php

    if(@$true_job_link === true) {
?>
            <tr id="jobrow<?=$myJob->job_id?>" class="job-row" valign='middle' onclick="window.location='<?=ROOT_DIR?>/jobs.php?id=<?=$myJob->job_id?>';">
<?php
    }
    else {
?>
            <tr id="jobrow<?=$myJob->job_id?>" class="job-row" valign='middle' onclick="Request.make('<?=AJAX_DIR?>/get_notes.php?id=<?= $myJob->job_id ?>&type=jobs', 'notes', false, true); Request.make('<?= AJAX_DIR ?>/get_job.php?id=<?= $myJob->job_id ?>', 'jobscontainer', true, true);">
<?php
    }
}
?>
                <td width="10%" class="job-row-icon">
                    <i class="<?=$paymentIcon?> list-row <?=@$paymentIconClass?>" title="<?=$paymentTitle?>" tooltip></i>
                    <i class="icon-usd list-row <?=@$paidIconClass?>" title="<?=$paidTitle?>" tooltip></i>
                    <i class="icon-shield list-row <?=@$approvedIconClass?>" title="<?=$approvedTitle?>" tooltip></i>
                    <i class="<?=$contractIconClass?> list-row" title="<?=$contractTitle?>" tooltip></i>
                    <i class="<?=$warrantyIconClass?> list-row" style="<?=$warrantyIconStyle?>" title="<?=$warrantyTitle?>" tooltip></i>
                </td>
                <td width="10%" class="<?=$holdClass?>" data-type="job" data-id="<?=$myJob->job_id?>" tooltip>
                    <b><?=$myJob->job_number?></b>
                    <?=$allRequirementsMet ? '&nbsp;<i class="icon-circle-arrow-right green next-stage"></i>' : ''?>
                </td>
                <td width="13%" class="<?=$holdClass?>" data-type="customer" data-id="<?=$myCustomer->getMyId()?>" tooltip>
                    <?=UIUtil::cleanOutput($myCustomer->getDisplayName())?>
                </td>
                <td width="15%" class="<?=$holdClass?>">
                    <?=$myCustomer->get('address')?>
                </td>
                <td width="10%" class="<?=$holdClass?>" <?=!empty($myJob->salesman_id) ? "data-type=\"user\" data-id=\"{$myJob->salesman_id}\" tooltip" : ''?>>
<?php
if(!empty($myJob->salesman_lname)) {
    echo $myJob->salesman_lname . ", " . $myJob->salesman_fname[0];
}
?>
                </td>
                <td width="22%" class="<?=$holdClass?>" <?=@$statusCellExtras?>><?=$stage_str?></td>
                <td width="6%" align='center' class="<?=$holdClass?>">
                    <?php echo $myJob->job_type; ?>
                </td>
                <td width="6%" align='center' class="<?=$holdClass?>">
                    <?php echo $myJob->getAgeDays(); ?>
                </td>
                <td width="8%" style='color:<?php echo $color; ?>;' class="<?=$holdClass?>">
                    <b><?php echo $stageAge; ?></b>
                    <span style='font-size: 10px;'> / <?php echo $duration; ?></span>
                </td>
<?php
if(@$complete_row !== false) {
?>
            </tr>
        </table>
    </td>
</tr>
<?php
}
?>