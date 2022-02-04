<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }


$ac_id = $_SESSION['ao_accountid'];

?>


<tr class="job-tab-content checklist" <?=@$show_content_style?>>
    <td colspan=11 id="checklist_job_area_checkbox">
            
        
    </td>
</tr>


<script type="text/javascript">
    var user_id = '<?=$myJob->salesman_id?>';
    var job_id = '<?=$myJob->job_id?>';
    var df_url = GLOBALS.ajax_dir + '/jobuserchckliststatus.php?id=' + user_id + '&job_id=' + job_id;
    Request.make( df_url, 'checklist_job_area_checkbox', true, true);

    function showchecklist_tab(tab_val)
    {        
        $(".list_details").hide();               
        $(".tab_head").removeClass('active');
        $("#check_"+tab_val).show();
        $("#"+tab_val).addClass('active');
    }


     function checkBoxChecklistChange(val,type,tab_val,status='')
    {                
        user_id = '<?=$myJob->salesman_id?>';
      
        var url = GLOBALS.ajax_dir + '/jobuserchckliststatus.php?id=' + user_id + '&job_id=' + job_id + '&item_id=' + val + '&type=' + type + '&tab=' + tab_val + '&status=' + status;
        
        //make request
        Request.make(url, 'checklist_job_area_checkbox', false, true);

    }

    
    
</script>
