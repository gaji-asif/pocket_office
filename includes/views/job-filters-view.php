<table border="0" cellspacing="0" cellpadding="0" width="100%" class="main-view-table">
	<tr>
		<td>
			
		</td>
	</tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="60%" class="main-view-table">
    <tr>
    	<td class="text-right"><strong>Include Closed Job:</strong>
        <input style="margin:10px 20px;" class="list-filter-input"  type="checkbox" name="closed_job" id="closed_job" value="all"></td>
    	<input  class="list-filter-input"  type="hidden" name="hidden_closed_job" id="hidden_closed_job" value="">
		<td class="text-right">			
            
        </td>
    </tr>
</table>
<table style="display: none;"  border="0" cellspacing="0" cellpadding="0" width="60%" id="advance_job_filters" class="main-view-table">
    <tr>
        
    	<td width="100" rowspan="2"><strong>Filter/Sort:</strong></td>
    	<td width="150">
            <select class="list-filter-input" name="advance_sort" id="advance_sort">
            	<option value="" selected disabled>Sort By</option>
                <option value='j.timestamp desc'>Newest First</option>
                <option value='j.timestamp asc'>Oldest First</option>
                <option value='(datediff(curdate(),j.stage_date)-t.duration) desc'>Urgent First</option>
                <option value='j.job_number asc'>ID Number Asc</option>
                <option value='j.job_number desc'>ID Number Desc</option>
                <option value='c.lname asc'>Last Name A-Z</option>
                <option value='c.lname desc'>Last Name Z-A</option>
            </select>
        </td>
    	<td width="150">
            <select class="list-filter-input" name="search_type" id='search_type'>
                <option value="" selected disabled>Quick Search</option>
                <option value="in">Insured Name</option>
                <option value="cn">Claim Number</option>
                <option value="pn">Policy Number</option>
                <option value="jn">Job Number</option>
                <option value="phn">Phone Number</option>
            </select>
        </td>
		<td class="text-right list-search">
			
            <input style="width: 100%"  type="text" name="advance_search" id="advance_search" class="list-filter-input" value="">
            <i class="icon-search"></i>
		
        </td>
        <td>
        	<input style="width: 50px;padding: 5px 2px;margin-left: 10px;" class="btn btn-blue " type="button" value="Search" rel="filter-list-btn" >
            <input onclick="advance('r');" type="button" value="Clear Filters" rel="reset-list-btn">
        </td>
        <td align="right">
            <i rel="reset-list-btn" href="javascript:void(0);" onclick="advance('n');" class="icon-remove grey btn-close-modal" title="Back to normal search"></i>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" id="job_filters" class="main-view-table">

    <tr valign="middle">
<?php

//if(!moduleOwnership('view_jobs')) {
//get account meta data
$accountMetaData = AccountModel::getAllMetaData();
$firstLast = UIUtil::getFirstLast();

//get filter data
$statuses = JobModel::getAllStatuses();
$stages = StageModel::getAllStages(TRUE);
$jobTypesArray = JobUtil::getAllJobTypes();
$taskTypes = TaskModel::getAllTaskTypes();
$warranties = JobUtil::getAllWarranties();
$providers = InsuranceModel::getAllProviders();
$jurisdictions = CustomerModel::getAllJurisdictions();

?>
        <td width="100" rowspan="2"><strong>Filter/Sort:</strong></td>
        <td width="100">
            <select id="sort" class="list-filter-input">
                <option value='j.timestamp desc'>Newest First</option>
                <option value='j.timestamp asc'>Oldest First</option>
                <option value='(datediff(curdate(),j.stage_date)-t.duration) desc'>Urgent First</option>
                <option value='j.job_number asc'>ID Number Asc</option>
                <option value='j.job_number desc'>ID Number Desc</option>
                <option value='c.lname asc'>Last Name A-Z</option>
                <option value='c.lname desc'>Last Name Z-A</option>
            </select>
        </td>
            <td width="100">
                                    <input type="hidden" id="salesman" name="salesman" >                     
                                    <input placeholder="Customer"  id = "autocomplete-4">
                                    <script>
                                          $(function() {
                                            $( "#autocomplete-4" ).autocomplete({
                                               source: "<?=AJAX_DIR?>/fetch_salesmen.php",
                                               select: function( event , ui ) {
                                                        event.preventDefault();
                                                        $("#autocomplete-4").val(ui.item.label);
                                                        $("#salesman").val(ui.item.value);
                                                    },
                                              focus: function(event, ui) {
                                                        event.preventDefault();
                                                        $("#autocomplete-4").val(ui.item.label);
                                                    }
                                            });
                                            
                                         });
                                    </script> 
                                </td>
<?php
// } else {
//     echo "<td width=150>&nbsp;</td>";
// }
?>
        <td width="100">
            <select id='stage' class="list-filter-input">
                <option value="" selected disabled>Stage/Status</option>
                <option value='and r.repair_id is not null'>Has Expedited Estimate Request</option>
             
                <optgroup label="Stage">
<?php
foreach ($stages as $stage) {
?>
                    <option value='AND j.stage_num=<?=$stage['stage_num'] ?>'><?=$stage['stage'] ?></option>
<?php
}
?>
                </optgroup>

                   <optgroup label="Status Hold">
                    <option value="AND (sh.status_id IS NOT NULL AND (sh.expires IS NULL || sh.expires >= CURDATE()))">All</option>
<?php
foreach ($statuses as $status) {
?>
                    <option value='AND (sh.status_id=<?=$status['status_id'] ?> AND (sh.expires IS NULL || sh.expires >= CURDATE()))'><?= $status['status'] ?></option>
<?php
}
?>
                </optgroup>
                
            </select>
        </td>
        <td width="100">
            <select id='type' class="list-filter-input">
                <option value="" selected disabled>Type</option>
<?php
foreach ($jobTypesArray as $job_type) {
?>
                <option value='and j.job_type=<?= $job_type['job_type_id'] ?>'><?= $job_type['job_type'] ?></option>
<?php
}
?>
            </select>
        </td>
        <td width="100">
            <select id='task_type' class="list-filter-input">
                <option value="" selected disabled>Task</option>
<?php
foreach ($taskTypes as $taskType) {
?>
                <option value='and t.task_type=<?= $taskType['task_type_id'] ?>'><?= $taskType['task'] ?></option>
<?php
}
?>
            </select>
        </td>
        <td width="100">
            <select id="warranty" class="list-filter-input">
                <option value="" selected disabled>Warranty</option>
<?php
foreach ($warranties as $warranty) {
?>
                <option value="AND jm.meta_value = <?=MapUtil::get($warranty, 'warranty_id')?>"><?=MapUtil::get($warranty, 'label')?></option>
<?php
}
?>
            </select>
        </td>
        <td width="100">
                                    <input type="hidden" id="insurance_provider" name="provider" >                     
                                    <input placeholder="Provider"  id = "autocomplete-7">
                                    <script>
                                          $(function() {
                                            $( "#autocomplete-7" ).autocomplete({
                                               source: "<?=AJAX_DIR?>/fetch_provider.php",
                                               select: function( event , ui ) {
                                                        event.preventDefault();
                                                        $("#autocomplete-7").val(ui.item.label);
                                                        $("#insurance_providerr").val(ui.item.value);
                                                    },
                                              focus: function(event, ui) {
                                                        event.preventDefault();
                                                        $("#autocomplete-7").val(ui.item.label);
                                                    }
                                            });
                                            
                                         });
                                    </script> 
                                </td>
        <td colspan="2">&nbsp;</td>
    <tr>
         <td width="100">
                                    <input type="hidden" id="canvasser" name="canvasser" >                     
                                    <input placeholder="Canvasser"  id = "autocomplete-1">
                                    <script>
                                          $(function() {
                                            $( "#autocomplete-1" ).autocomplete({
                                               source: "<?=AJAX_DIR?>/fetch_user.php",
                                               select: function( event , ui ) {
                                                        event.preventDefault();
                                                        $("#autocomplete-1").val(ui.item.label);
                                                        $("#canvasser").val(ui.item.value);
                                                    },
                                              focus: function(event, ui) {
                                                        event.preventDefault();
                                                        $("#autocomplete-1").val(ui.item.label);
                                                    }
                                            });
                                            
                                         });
                                    </script> 
                                </td>

        <td width="100">
                                    <input type="hidden" id="referral" name="referral" >                     
                                    <input placeholder="Referral"  id = "autocomplete-2">
                                    <script>
                                          $(function() {
                                            $( "#autocomplete-2" ).autocomplete({
                                               source: "<?=AJAX_DIR?>/fetch_user.php",
                                               select: function( event , ui ) {
                                                        event.preventDefault();
                                                        $("#autocomplete-2").val(ui.item.label);
                                                        $("#referral").val(ui.item.value);
                                                    },
                                              focus: function(event, ui) {
                                                        event.preventDefault();
                                                        $("#autocomplete-2").val(ui.item.label);
                                                    }
                                            });
                                            
                                         });
                                    </script> 
                                </td>
        <td width="100">
                                    <input type="hidden" id="creator" name="creator" >                     
                                    <input placeholder="Creator"  id = "autocomplete-3">
                                    <script>
                                          $(function() {
                                            $( "#autocomplete-3" ).autocomplete({
                                               source: "<?=AJAX_DIR?>/fetch_user.php",
                                               select: function( event , ui ) {
                                                        event.preventDefault();
                                                        $("#autocomplete-3").val(ui.item.label);
                                                        $("#creator").val(ui.item.value);
                                                    },
                                              focus: function(event, ui) {
                                                        event.preventDefault();
                                                        $("#autocomplete-3").val(ui.item.label);
                                                    }
                                            });
                                            
                                         });
                                    </script> 
                                </td>
        <td>
            <select id="age" class="list-filter-input">
                <option value="" selected disabled>Age</option>
                <option value='and datediff(curdate(), j.stage_date) = 0'>Today</option>
                <option value='and datediff(curdate(), j.stage_date)<=15'>0-15</option>
                <option value='and (datediff(curdate(), j.stage_date)>=16 and datediff(curdate(), j.stage_date)<=30)'>16-30</option>
                <option value='and (datediff(curdate(), j.stage_date)>=31 and datediff(curdate(), j.stage_date)<=45)'>31-45</option>
                <option value='and (datediff(curdate(), j.stage_date)>=46 and datediff(curdate(), j.stage_date)<=60)'>46-60</option>
                <option value='and datediff(curdate(), j.stage_date)>60'>60+</option>
            </select>
        </td>
        <td width="100">
                                    <input type="hidden" id="jurisdiction" name="jurisdiction" > 
                                    <input placeholder="Jurisdiction" id = "autocomplete-6">
                                    <script>
                                          $(function() {
                                            $( "#autocomplete-6" ).autocomplete({
                                               source: "<?=AJAX_DIR?>/fetch_jurisdiction.php",
                                               select: function( event , ui ) {
                                                        event.preventDefault();
                                                        $("#autocomplete-6").val(ui.item.label);
                                                        $("#jurisdiction").val(ui.item.value);
                                                    },
                                              focus: function(event, ui) {
                                                        event.preventDefault();
                                                        $("#autocomplete-6").val(ui.item.label);
                                                    }
                                            });
                                            
                                         });
                                    </script> 

                                </td>
        <td width="100">
            <select id="permit_expires" class="list-filter-input">
                <option value="" selected disabled>Permit Exp</option>
                <option value="0">Today</option>
                <option value="7">Within 7 Days</option>
                <option value="30">Within 30 Days</option>
                <option value="30">Within 60 Days</option>
            </select>
        </td>
        <td>
            <input type="button" value="Filter" rel="filter-list-btn">
            <input onclick="advance('r');" type="button" value="Clear Filters" rel="reset-list-btn">
        </td>
        <td class="text-right list-search">

			<form method="post" rel="filter-list-form" action="<?=AJAX_DIR?>/get_joblist.php" data-destination="jobscontainer">
            <a href="javascript:void(0);" onclick="advance('a');" class="toggle" title="Advance Search" tooltip rel="reset-list-btn">
                <i class="icon-filter"></i>
            </a>
            <input type="text" id="search" class="list-filter-input" value="">
            <i class="icon-search"></i>
			</form>
        </td>
    </tr>
</table>
<link href = "https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
              integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
              crossorigin="anonymous"></script>
<script type="text/javascript">
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
         $('[rel="advance-filter-list-form"]').trigger('submit');
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
</script>

