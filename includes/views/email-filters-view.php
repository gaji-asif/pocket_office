<table border="0" cellspacing="0" cellpadding="0" width="100%" class="main-view-table">
	<tr>
		<td></td>
	</tr>
</table>

<style>
    b {
        font-weight: 800;
    }
</style>
<!-- Filter Search Area-->
<table border="0" cellspacing="0" cellpadding="0" width="100%" id="email_filters" class="main-view-table">
    <tr valign="middle">
        <td width="80">
            <input  id="action_check_val" type="checkbox" value="" rel="list-filter-input" style="width: 25px; height:25px;margin-left: 15px;margin-top: auto; float:left">
            <select id="action_check"  name="action_check" class="list-filter-input" style="width: 20px; border-left: none; height:25px;">
                <option value='a'>All</option>
                <option value='n' selected>None</option>
                <option value='row_r'>Read</option>
                <option value='row_u'>Unread</option>
                <option value='row_s'>Shared</option>
                <option value='row_p'>Private</option>
            </select>
        </td>

        <td width="500">
            <a href="javscript:void(0);"  rel="open-modal" data-script="compose_email.php"><i class="icon-plus"></i> Compose</a>
            <input  id="inbox_filter" type="button" value="Inbox" rel="email-filter-list-btn" class="active_email">
            <input id="draft_filter" type="button" value="Draft" rel="email-filter-list-btn">
            <input id="outbox_filter" type="button" value="Outbox" rel="email-filter-list-btn">
            <input id="send_filter" type="button" value="Sent" rel="email-filter-list-btn">
            <input id="archive_filter" type="button" value="Archive" rel="email-filter-list-btn">
        </td>
        
        <td>
            <select id="action_take" name="action_take" class="list-filter-input" style="margin-left: 20px;display:none;" onchange="return actionTake(this);" >
                <option value=''>Action</option>
                <option value='ar'>Archive</option>
                <option value='dl'>Delete</option>
                <option value='rd'>Mark as Read</option>
                <option value='ur'>Mark as Unread</option>
                <option value='s'>Shared</option>
                <option value='p'>Private</option>
            </select>
        </td>

        <td class="text-right list-search">
			<form method="post" rel="filter-email-list-form" action="<?=AJAX_DIR?>/get_emaillist.php" data-destination="emailscontainer">
            <!-- <a href="javascript:void(0);" onclick="advance('a');" class="toggle" title="Advance Search" tooltip rel="reset-list-btn">
                <i class="icon-filter"></i>
            </a> -->
            <input  class="list-filter-input"  type="hidden" name="email_folder" id="email_folder" value="Inbox">
            <input  class="list-filter-input"  type="hidden" name="prev_folder" id="prev_folder" value="Inbox">
            <input type="text" id="emailsearch" class="list-filter-input" value="" autocomplete="off" style="background:#fff;border: #ccc 1px solid;
    height: 26px;color: #333 !important;">
            <i class="icon-search"></i>
			</form>
        </td>
    </tr>
</table>


<script type="text/javascript">

    $("#action_check_val").click(function(){
        $('.email_row_checkbox').prop('checked', this.checked);
        if(this.checked)
            $("#action_take").show();
        else
            $("#action_take").hide();
    });
    
    
    function inner_checkbox(){
        var rows_selected = [];
        $(".email_row_checkbox:checked").each(function(){
            rows_selected.push($(this).val());
        });
        
        if(rows_selected.length>0)
        {
            $("#action_take").show();
        }
        else
        {
            $("#action_take").hide();
        }
    }
     
    
    $("#action_check").change(function(){
        var selected_val = $(this).val();
        $("#action_take").show();
        if(selected_val=='row_r'){
            $('.email_row_checkbox').prop('checked',false);
            $('.row_r').prop('checked', true);
        }
        else if(selected_val=='row_u'){
            $('.email_row_checkbox').prop('checked',false);
            $('.row_u').prop('checked', true);
        }
        else if(selected_val=='row_s'){
            $('.email_row_checkbox').prop('checked',false);
            $('.row_s').prop('checked', true);
        }
        else if(selected_val=='row_p'){
            $('.email_row_checkbox').prop('checked',false);
            $('.row_p').prop('checked', true);
        }
        else if(selected_val=='n'){
            $('.email_row_checkbox').prop('checked',false);
            $('#action_check_val').prop('checked', false);
            $("#action_take").hide();
            $("#action_take").val("");
        }
        else if(selected_val=='a'){
            $('.email_row_checkbox').prop('checked', true);
            $('#action_check_val').prop('checked', true);
        }
    });
    
    

	function advance(v)
	{		
		if(v=='a')
		{
			$("#advance_job_filters").show();
			$("#job_filters").hide();
            $("#closed_job").attr("checked",false);
		}
		else if(v=='n')
		{
			$("#job_filters").show();
			$("#advance_job_filters").hide();
            $("#closed_job").attr("checked",false);
		}
		else if(v=='r')
		{
			$("#closed_job").attr("checked",false);
		}
	}

    function advanceSearch()
    {            
         $('[rel="advance-filter-email-list-form"]').trigger('submit');
    }

    $(document).ready(function() {
        $("#closed_job").click(function() {
            var checked = $(this).is(':checked');
            if (checked) {
                $("#hidden_closed_job").val('all');
            } else {
                $("#hidden_closed_job").val('');
            }
        });
    });
    
    function privacyChange(id,type)
    {
        privacy_type = type.value;
        //alert(privacy_type);
        $.ajax({
            url: '<?php echo AJAX_DIR;?>/email/saveprivacy.php',
            data: {id:id,type:privacy_type},
            success: function(data) {    
                if(data=='fail')
                {
                    alert("Error: something went wrong, Please try again!");
                }
                else
                {
                    $("#checkbox_"+id).prop('checked', false);
                    if(privacy_type=='s'){
                        $("#checkbox_"+id).removeClass('row_p');
                        $("#checkbox_"+id).addClass('row_s');
                        alert("Success: Email has been shared with other user of your company!");
                    }
                    else{
                        $("#checkbox_"+id).removeClass('row_s');
                        $("#checkbox_"+id).addClass('row_p');
                        alert("Success: Email has been made private!");
                    }
                }
            },
        });
        
        //alert(type);
    }
    
    function actionTake(type)
    {
        action_type = type.value;
        var rows_arr = [];
        $(".email_row_checkbox:checked").each(function(){
            rows_arr.push($(this).val());
        });
        if(action_type!='')
        {
            if(rows_arr.length>0)
            {
                $.ajax({
                    url: '<?php echo AJAX_DIR;?>/email/bulkaction.php',
                    data: {type:action_type,rows_arr:rows_arr},
                    success: function(data) {    
                        if(data=='fail')
                        {
                            alert("Error: something went wrong, Please try again!");
                        }
                        else
                        {
                            $("#action_take").val("");
                            alert(data);
                            $('[rel="filter-email-list-form"]').trigger('submit');
                        }
                    },
                });
            }
            else
            {
                alert("Error: No Email Selected! Please select atleast one email!");
            }
        
        }
        
        //alert(type);
    }
    
</script>
