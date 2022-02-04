<?php

include 'includes/common_lib.php';

/////////////////////////////////////
$usersOwnIdCheck = userOwnIdCheck($_SESSION['ao_userid']);
if(count($usersOwnIdCheck) > 0){
$flag = 1;
} else {
	
if($_REQUEST['jay']==1){
	//echo 'yes';
//	} else {
//		echo 'no';

	

	
function current_url() {
    if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on')   
         $url = "https://";   
    else  
         $url = "http://";   
    // Append the host(domain name, ip) to the URL.   
    $url.= $_SERVER['HTTP_HOST'];   
    
    // Append the requested resource location to the URL   
    $url.= $_SERVER['REQUEST_URI'];    
      
    return $url; 
}

$usersCheck = userIdCheck($_SESSION['ao_userid'],current_url());

if(count($usersCheck) > 0){
	$flag = '1'; 
	//exit;
	} else {
		 echo 'You have no permission.'; exit;
		}
	}
}
///////////////////////////////////////////////////////////////////

$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));

pageSecure($this_page->source);

$firstLast = UIUtil::getFirstLast();



$sql = "select calendar_view from users where user_id=".$_SESSION['ao_userid'];

$user_data = DBUtil::queryToArray($sql);

$default_view ='week';

if($user_data && $user_data[0]['calendar_view']=='m')

	$default_view = 'month';

?>



<?=ViewUtil::loadView('doc-head')?>



<h1 class="page-title"><i class="icon-calendar"></i><?=$this_page->title?></h1>



    <table border="0" cellpadding="5" cellspacing="0" width="100%">

      <tr>

        <td colspan=2>

            

            

            

            

            <!--- New Added >> -->

             <?php



if(ModuleUtil::checkAccess('add_user'))



{



?>



<div class="btn-group pull-right page-menu">

    <div rel="open-modal" data-script="share_url.php" class="btn btn-success" title="Add user" tooltip>

        <i class="icon-plus"></i>

    </div>

</div>



<?php



}



?>





<form method="get" id="urlsave" name="urlsave">

<?php //echo $_SESSION['urlResultText']; ?>

<input type="hidden" id="urlResultText" value=""/>

</form>



<script type='text/javascript'>



	function urlSet(){

	

		var str = document.getElementById('urlResultText').value;
		var sessData = btoa(str);
		//alert(sessData);

		Request.make('<?=AJAX_DIR?>/share_url_session_set.php?url='+sessData, 'urlSet', true, true);

	

	}



</script>



<span id="urlSet" ></span>

<div ><strong>Shareable link:</strong>

<span id="urlResult">Result</span>

            <!--- New Added << -->

            

            

            

            

            

          <form style='margin-bottom:0;' name='form'>

            <input type="hidden" class="list-filter-input ignore-filter-reset" id="m" value=""/>

            <input type="hidden" class="list-filter-input ignore-filter-reset" id="Y" value=""/>





      <select id='customer' class="list-filter-input">

          <option value="" selected disabled>Customer</option>

          <option value=" and j.salesman is null">No Customer Assigned</option>

          <?php

          $showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');

          $dropdownUserLevels = AccountModel::getMetaValue('job_salesman_filter_user_dropdown');

          $salesmen = !empty($dropdownUserLevels)

                      ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)

                      : UserModel::getAll($showInactiveUsers, $firstLast);

          foreach($salesmen as $salesman) {

          ?>

              <option value=' and j.salesman=<?= $salesman['user_id'] ?>'><?= $salesman['select_label'] ?></option>



          <?php

              }

          ?>

      </select>



      <select id="provider" class="list-filter-input">

          <option value="" selected disabled>Provider</option>

          <?php

          $providers = InsuranceModel::getAllProviders();

          foreach ($providers as $provider) {

          ?>

              <option value=" and j.insurance_id = <?=MapUtil::get($provider, 'insurance_id')?>"><?=MapUtil::get($provider, 'insurance')?></option>

          <?php

          }

          ?>

      </select>





      <select id='user' class="list-filter-input">

				<option value="">User</option>

<?php

$users_array = UserModel::getAll(TRUE, $firstLast);

foreach($users_array as $user)

{

?>

              <option value="<?=$user['user_id']?>"><?=$user['select_label']?></option>

<?php

}

?>

            </select>

            <select id='type' class="list-filter-input">

                <option value=''>Type</option>

        				<option value='appointment'>Appointment</option>

        				<option value='delivery'>Delivery</option>

        				<option value='event'>Event</option>

        				<option value='repair'>Repair</option>

                <option value='todolist'>To Do List</option>

<?php

$taskTypes = TaskModel::getAllTaskTypes();

foreach ($taskTypes as $taskType) {

?>

                <option value='task_type=<?= $taskType['task_type_id'] ?>'><?= $taskType['task'] ?></option>

<?php

}

?>

            </select>

            <input type='button' value='Month View' onclick="filterList('<?=AJAX_DIR?>/get_calmonth.php', 'schedulecontainer');">

            <input type='button' value='Week View' onclick="filterList('<?=AJAX_DIR?>/get_calweek.php', 'schedulecontainer');">

            <input type='button' value='Clear Filters' onclick="resetFilterListInputs(); filterList('<?=AJAX_DIR?>/get_cal<?=$default_view?>.php', 'schedulecontainer');">

          </form>

        </td>

      </tr>

    </table>

<?php

if(ModuleUtil::checkAccess('view_schedule')) {

?>

    <table border=0 cellspacing=0 cellpadding=0 class="schedule-table">

      <tr>

        <td id='schedulecontainer'></td>

      </tr>

      <tr>

        <td>

          &nbsp;

          

          

          <!-- New Added >> -->

          <?php

		  if($_REQUEST['schedule'] != ""){?>

          <script type='text/javascript'>

		  Request.make('<?php echo $_REQUEST['schedule']; ?>', 'schedulecontainer', true, true);

	  	  </script>

          <?php } else { ?>

          <script type='text/javascript'>

		  Request.make('<?=AJAX_DIR?>/get_cal<?=RequestUtil::get('view', 'week')?>.php?ws=<?=RequestUtil::get('ws')?>', 'schedulecontainer', true, true);

          </script>

          <?php } ?>

          <!-- New Added << -->

          

          

          

           <!--

          <script type='text/javascript'>

            Request.make('<?=AJAX_DIR?>/get_cal<?=$default_view?>.php?ws=<?=RequestUtil::get('ws')?>', 'schedulecontainer', true, true);

          </script>

          -->

        </td>

      </tr>

    </table>

<?php

} else {

    echo ModuleUtil::showInsufficientRightsAlert('view_schedule', TRUE);

}

?>

  </body>

</html>

