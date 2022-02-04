<?php
if(!$myEmail->exists()) { return; }
$today = strtotime(date('Y-m-d'));



if(@$complete_row !== false) {
?>
<tr class="data-table-row">
<?php
}
if(@$complete_row !== false) {
?>
    <td class="data-table-cell">
        <table border=0 cellpadding=0 cellspacing=0 width='100%' class="jobs-data-table" >
<?php

    if(@$true_job_link === true) {
?>
            <tr id="jobrow<?=$myEmail->id?>" class="job-row" valign='middle' onclick="window.location='<?=ROOT_DIR?>/emails.php?id=<?=$myEmail->id?>';">
<?php
    }
    else {
?>
            <tr id="jobrow<?=$myEmail->id?>" class="job-row" valign='middle'>
<?php
    }
}
?>
                <?php
                $type_class = '';
                if($myEmail->is_read==1){
                    $type_class = 'row_r';
                }
                else
                {
                    $type_class = 'row_u';
                }
                
                $privacy_class = '';
                if($myEmail->is_shared=='s')
                {
                    $privacy_class = 'row_s';
                }
                else
                {
                    $privacy_class = 'row_p';
                }
                
                
                ?>
                <td width="5%" class="job-row-icon">
                    <input onclick="inner_checkbox();" id="checkbox_<?=$myEmail->id?>" class="email_row_checkbox <?=$type_class?> <?=$privacy_class?>" style="margin-left: 18px;height:18px; width:18px; " type="checkbox" value="<?=$myEmail->id?>" name="selectcheckbox">
                </td>
                <td width="24%" class="<?=$holdClass?>" data-type="job" data-id="<?=$myEmail->job_id?>"  onclick="Request.make('<?= AJAX_DIR ?>/get_email.php?id=<?= $myEmail->id ?>', 'emailscontainer', true, true);">
                    <?php $from_arr = explode(' ',$myEmail->from_name);

                    //echo "<pre>";print_r($from_arr);
                    if(count($from_arr)>1)
                    unset($from_arr[count($from_arr)-1]);
                    $from_name = implode($from_arr);
                    $search = '"';
                    $from_name = str_replace($search, '', $from_name);
                    $search = "'";
                    $from_name = str_replace($search, '', $from_name);

                    $_name_title = '';
                    if(strlen($from_name)>40){
                        $_name_title = $from_name;
                        $from_name = substr($from_name,0,37).'...';
                    }
                    ?>
                    <?php if($myEmail->is_read==1){?>
                    <?=$from_name?>
                    <?php }else{?>
                    <b><?=$from_name?> </b>
                    <?php }?>
                </td>
                <td width="50%"  data-type="customer"  onclick="Request.make('<?= AJAX_DIR ?>/get_email.php?id=<?= $myEmail->id ?>', 'emailscontainer', true, true);">
                    <?php 
                    $max = 95;
                    $subject = $myEmail->subject;
                    $sub_lenth = strlen($myEmail->subject);
                    if($sub_lenth>$max)
                    {
                        $subject = substr($myEmail->snippet,0,90).'...';
                    }

                    $snippet = "";                    
                    if($sub_lenth<90)
                    {
                        $snippet_lenth = strlen($myEmail->snippet);
                        $tot_length = $sub_lenth+$snippet_lenth;                       
                        if($tot_length>$max)
                        {
                            $snippet = substr($myEmail->snippet,0,(90-$sub_lenth)).'...';
                        }
                        else
                        {
                            $snippet = $myEmail->snippet;
                        }
                    }
                   //echo "<pre>";print_r($myEmail);die;
                   //echo $_SESSION['ao_userid'];die
                    
                    ?>
                    <?php if($myEmail->is_read==1){?>
                    <?=$subject?>
                    <?php }else{?>
                    <b><?=$subject?> </b>
                    <?php }?>
                    
                    <?php if(!empty($snippet)){?>
                        &nbsp;<span style="color:#b9babb"> <?=$snippet?> </span>
                    <?php }?>
                </td>
                <td width="11%">
                    <?php if($myEmail->user_id==$_SESSION['ao_userid']){?>
                   <select style="width: 70px;z-index: 999;" name="privacy_type" id='privacy_type' onchange="privacyChange(<?=$myEmail->id?>,this);" >
                        <option value="">Select</option>
                        <option value="s" <?=($myEmail->is_shared=='s')?'selected="selected"':''?>>Shared</option>
                        <option value="p" <?=($myEmail->is_shared=='p')?'selected="selected"':''?>>Private</option>
                    </select>
                    <?php }?>
                </td>
                <td width="10%"  onclick="Request.make('<?= AJAX_DIR ?>/get_email.php?id=<?= $myEmail->id ?>', 'emailscontainer', true, true);"><?=$myEmail->create_date?></td>
                
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
