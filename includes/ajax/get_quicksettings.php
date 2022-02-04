<?php
include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_jobs')) {
    die('Insufficient Rights');
}

$myJob = new Job(RequestUtil::get('id'));
$myCustomer = new Customer($myJob->customer_id);

if(!ModuleUtil::checkJobModuleAccess('view_jobs', $myJob)) {
  die('Insufficient Rights');
}

$canSave = FALSE
?>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr valign="top">
        <td width="13">
            <img src="<?= IMAGES_DIR ?>/quickarrow.png">
        </td>
        <td>
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td>
                        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="quickcontainertitle">
                            <tr>
                                <td>Quick Settings:</td>
                                <td align="right">
                                    <i class="icon-remove grey" onclick="closeAllQuickSettings(<?= $myJob->job_id ?>)"></i>
                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
                <tr>
                    <td colspan=2>
                        <table border="0" width="100%" cellpadding="0" cellspacing="0" class="infocontainernopadding">
<?php
//mark_paid
if(ModuleUtil::checkJobModuleAccess('mark_paid', $myJob)) {
    $canSave = TRUE;
?>
                            <tr>
                                <td class="listitem"><b>Paid in Full:</b></td>
                                <td class="listrow"><input type="checkbox" id="<?= $myJob->job_id ?>pif" value="yes" <?= !empty($myJob->pif_date) ? 'checked' : '' ?>></td>
                            </tr>
<?php
}
//modify_insurance
if(!empty($myJob->insurance_id) && ModuleUtil::checkJobModuleAccess('modify_insurance', $myJob)) {
    $canSave = TRUE;
?>
                            <tr>
                                <td class="listitem"><b>Claim Approved:</b></td>
                                <td class="listrow"><input type="checkbox" id="<?= $myJob->job_id ?>approved" value="yes" <?= !empty($myJob->insurance_approval) ? 'checked' : '' ?>></td>
                            </tr>
<?php
}
//assign_job_referral
if(!empty($myJob->referral_id) && ModuleUtil::checkJobModuleAccess('assign_job_referral', $myJob)) {
    $canSave = TRUE;
?>
                            <tr>
                                <td class="listitem"><b>Referral Paid:</b></td>
                                <td class="listrow"><input type="checkbox" id="<?= $myJob->job_id ?>refpaid" value="yes" <?=!empty($myJob->referral_paid) ? 'checked' : ''?>></td>
                            </tr>
<?php
}
if(ModuleUtil::checkAccess('full_job_stage_access')) {
    $canSave = TRUE;
?>
                            <tr>
                                <td class="listitem"><b>Jump Stage:</b></td>
                                <td class="listrow">
                                    <select id="<?= $myJob->job_id ?>stage">
<?php
    $stages = StageModel::getAllStages();
    foreach($stages as $stage) {
?>
                                        <option value="<?=MapUtil::get($stage, 'stage_num')?>" <?=MapUtil::get($stage, 'stage_num') == $myJob->stage_num ? 'selected' : ''?>><?=MapUtil::get($stage, 'stage')?></option>
<?php
    }
?>
                                    </select>
                                    <input type="hidden" id="job" value="<?= $myJob->job_id ?>">
                                </td>
                            </tr>
<?php
}
if($canSave) {
?>
                            <tr>
                                <td colspan=2 align="right" class="listrow">
                                    <input type="button" value="Save" onclick="saveQuickSettings('<?=$myJob->job_id?>', this)">
                                </td>
                            </tr>
<?php
} else {
?>
                            <tr>
                                <td colspan=2 align="center" class="listrow">
                                    <br>
                                    <b>No Options Available</b>
                                    <br>
                                </td>
                            </tr>
<?php
}
?>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>