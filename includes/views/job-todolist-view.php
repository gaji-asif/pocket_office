<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }


$ac_id = $_SESSION['ao_accountid'];
//echo "<pre>";print_r($myJob);die;
?>


<tr class="job-tab-content todolist" <?=@$show_content_style?>>
    <td colspan=11 id="todolist_job_area_checkbox">
            
        
    </td>
</tr>


<script type="text/javascript">
    var user_id = '';
    var job_id = '<?=$myJob->job_id?>';
    var df_url = GLOBALS.ajax_dir + '/jobusertodoliststatus.php?id=' + user_id + '&job_id=' + job_id;
    //alert(df_url);
    Request.make(df_url, 'todolist_job_area_checkbox', true, true);

    function showtodolist_tab(tab_val)
    {        
        $(".list_details").hide();               
        $(".tab_head").removeClass('active');
        $("#todo_"+tab_val).show();
        $("#"+tab_val).addClass('active');
    }


    function checkBoxChange(val,type,tab_val,status='')
    {                
        var url = GLOBALS.ajax_dir + '/jobusertodoliststatus.php?id=' + user_id + '&job_id=' + job_id + '&item_id=' + val + '&type=' + type + '&tab=' + tab_val + '&status=' + status;
        
        //make request
        Request.make(url, 'todolist_job_area_checkbox', false, true);

    }

    function saveDateChange(date_val,tab_val,item_id)
    {                
        var url = GLOBALS.ajax_dir + '/jobusertodoliststatus.php?id=' + user_id + '&job_id=' + job_id + '&item_id=' + item_id + '&type=' + type + '&tab=' + tab_val + '&date_val=' + date_val;
        
        //make request
        Request.make(url, 'todolist_job_area_checkbox', false, true);

    }

    

    
    
</script>
