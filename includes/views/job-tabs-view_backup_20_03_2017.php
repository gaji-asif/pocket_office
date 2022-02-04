<?php
$stages = StageModel::getAllStages($groupByStageNum = TRUE);
$jobActions = JobUtil::getActions();
$numStages = count($stages);
if($numStages>0)
    $percentage = floor(($myJob->stage_num / $numStages) * 100);
else
    $percentage=0;

$percentage = $percentage > 100 ? 100 : $percentage;
?>
<tr>
    <td colspan=11>
        <table width='100%' border=0 cellpadding=5 cellspacing=0 style='border-top:1px solid #cccccc;'>
            <tr>
                <td colspan="10">
                    <table border=0 width='100%' border=0 cellpadding=5 cellspacing=0>
                        <tr valign='middle'>
                            <td>
                                <div class="bar">
                                    <div class="percentage" style="width: 0"><?=$percentage?>%</div>
                                </div>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
            <tr>
<?php
foreach($tabsArray as $key => $tab) {
    $active = ($tab == $cur_tab) || (empty($cur_tab) && $key == 0) ? 'active' : '';
?>
                <td class="job-tab-link <?=$active?> <?=$tab?>" rel="switch-job-tab" data-tab="<?=$tab?>"><?=ucfirst($tab)?></td>
<?php
}
?>
                <td class="job-tab-link-filler">
                    <table border=0>
                        <tr>
                            <td width=20 align='center'><img src='<?= ROOT_DIR ?>/images/icons/bookmark_16.png'></td>
                            <td id='bookmark'>
<?php

$active = '';
if(!JobUtil::jobIsBookmarked($myJob->job_id)) {
    $bookmarkLinkText = 'Bookmark';
}
else {
    $bookmarkLinkText = 'Remove Bookmark';
    $active = 'active';
}
?>
                                    <a href="#" data-job-id="<?=$myJob->job_id?>" class="basiclink bookmark-link <?=$active?>"><?=$bookmarkLinkText?></a>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    </td>
</tr>
<tr>
    <td colspan=11>
        <table width='100%' border=0>
            <tr valign='top'>
                <td colspan=11>
                    <table border=0>
<?php


if(defined('NEW_JOB_ACTIONS') && ModuleUtil::checkJobModuleAccess('modify_job', $myJob)) 
{
?>
    <tr>
        <td>
            <div class="btn-group">
                <div class="btn btn-blue btn-small btn-drop-down">
                    <i class="icon-pencil" />
                    <?php
                    ob_start();
                    $numLists = 1;
                    ?>
                    <ul>
                    <?php

                        $numJobAction = count($jobActions);
                    	foreach($jobActions as $i => $jobAction) 
                        {
                    		if(ModuleUtil::checkJobModuleAccess(MapUtil::get($jobAction, 'hook'), $myJob)) 
                            { 
                    ?>
                            <li onclick="applyOverlay('<?=MapUtil::get($jobAction, 'script')?>?id=<?=$myJob->job_id?>')"><?=MapUtil::get($jobAction, 'action')?></li>
                            <?php
                                if(($i + 1) % 10 == 0 && $i != ($numJobAction - 1)) {
                                    $numLists++;
                            ?>
                    </ul>
                    <ul>
                    <?php
                                }
                            }
                        }
                    ?>
                    </ul>
                    <?php
                    $lists = ob_get_clean();
                    ?>
                    <div style="width: <?=($numLists * 155) + 8?>px;"><?=$lists?></div>
                </div>
                <!--<div class="btn btn-blue btn-small btn-drop-down">Stage</div>-->
                </div>
<?php
}

if(!defined('NEW_JOB_ACTIONS') && ModuleUtil::checkJobModuleAccess('modify_job', $myJob)) 
{
?>
                    <tr>
                        <td>
                            <b>Actions:</b>
                            <select name='myactions' id='myactions'>
                            <?php
                            	foreach($jobActions as $jobAction) 
                                {
                            		if(ModuleUtil::checkJobModuleAccess(MapUtil::get($jobAction, 'hook'), $myJob))
                                    {
                            ?>
                                        <option value="<?=MapUtil::get($jobAction, 'script')?>?id=<?=$myJob->job_id?>"><?=MapUtil::get($jobAction, 'action')?></option>
                            <?php
                            		}
                            	}
                            ?>
                            </select>
                            <input type="button" value="Go" onclick="applyOverlay($('#myactions').val());">
                        </td>
                    </tr>
<?php
}

if(!defined('NEW_JOB_ACTIONS') && ModuleUtil::checkAccess('full_job_stage_access')) 
{
?>
    <tr>
        <td>
            <b>Jump to Stage:</b>
            <select name='mystages' id='mystages'>
            <?php
                reset($stages);
                foreach($stages as $stage) {
                    if(stageAdvanceAccess($stage['stage_num'])) {
            ?>
                        <option value="<?=$stage['stage_num']?>" <?=($stage['stage_num'] == $myJob->stage_num ? 'selected' : '')?>><?=$stage['stage']?></option>
            <?php
                    }
                }
            ?>
            </select>
            <input type="button" value="Go" onclick="changeStage('<?=$myJob->job_id?>', $('#mystages').val());">
<?php
}
?>
            </td>
        </tr>
    </table>
</td>
<td align='right'>
    <table cellspacing=0 border=0>
    <?php
        if($myJob->pif_date == '') {
            $iconStr = "<img src='" . ROOT_DIR . "/images/icons/dollar_grey_16.png'>";
        if(ModuleUtil::checkAccess('mark_paid') || (moduleOwnership('mark_paid') && (JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id == $_SESSION['ao_userid'] || $myJob->user_id == $_SESSION['ao_userid'])))
            $iconStr = "<span onmouseover='this.style.cursor=\"pointer\";' onclick='if(confirm(\"Are you sure you want to mark paid?\")){Request.make(\"includes/ajax/get_job.php?action=paid&id=" . $myJob->job_id . "\",\"jobscontainer\",\"yes\",\"yes\");}'><img title='Mark Paid' src='" . ROOT_DIR . "/images/icons/dollar_grey_16.png' tooltip></span>";
    ?>
                <tr>
                    <td rowspan=2 align='right'><?php echo $iconStr; ?></td>
                    <td class='smalltitle' width=100>Job Not Paid</td>
                </tr>
    <?php
        }
        else 
        {
            $iconStr = "<img src='" . ROOT_DIR . "/images/icons/dollar_32.png'>";
            if((ModuleUtil::checkAccess('mark_paid') || (moduleOwnership('mark_paid') && (JobUtil::isSubscriber($myJob->job_id) || $myJob->salesman_id == $_SESSION['ao_userid'] || $myJob->user_id == $_SESSION['ao_userid']))))
            $iconStr = "<span onmouseover='this.style.cursor=\"pointer\";' onclick='if(confirm(\"Are you sure you want to mark unpaid?\")){Request.make(\"includes/ajax/get_job.php?action=unpaid&id=" . $myJob->job_id . "\",\"jobscontainer\",\"yes\",\"yes\");}'><img title='Mark Unpaid' src='" . ROOT_DIR . "/images/icons/dollar_32.png' tooltip></span>";
    ?>
            <tr>
                <td rowspan=3 align='right'width=32><?php echo $iconStr; ?></td>
                <td width=100><b>Job Paid in Full</b></td>
            </tr>
            <tr>
                <td class='smallnote'>
                    on <?=DateUtil::formatDate($myJob->pif_date)?>
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
<script>
$(function() {
    $('div.percentage').animate({width: '<?=$percentage?>%'}, (50 * <?=$percentage?>), 'easeInOutQuart');
});
</script>