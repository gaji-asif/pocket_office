<?php
//get jobs within a 10 miles radius
$job_radius = 10;
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
$firstLast = UIUtil::getFirstLast();

$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('assign_job_salesman', $myJob, TRUE);
$myCustomer = new Customer($myJob->customer_id);

if(RequestUtil::get('submit')) {
    FormUtil::update('jobs');
    $myJob->storeSnapshot();

	if(RequestUtil::get('salesman')) {
        NotifyUtil::notifyFromTemplate('add_job_salesman', RequestUtil::get('salesman'), null, array('job_id' => $myJob->job_id));
    } else {
        NotifyUtil::notifyFromTemplate('remove_job_salesman', $myJob->salesman_id, null, array('job_id' => $myJob->job_id));
    }

	JobModel::saveEvent($myJob->job_id, 'Assigned New Job Salesman');
?>
<script>
parent.window.location.href = '<?=ROOT_DIR?>/jobs.php?id=<?=$myJob->job_id?>';
</script>
<?php
    die();
}
?>
<script>
$(document).ready(function() {
    $('#salesman-picklist').change(function() {
        Request.make('get_userjobstotal.php?id=' + $(this).val(), 'jobstotal', false, true);
    }).change();
});
</script>
<link href = "https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
			  integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
			  crossorigin="anonymous"></script>
<form method="post" name="salesmen" action="?id=<?=$myJob->job_id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>
            Assign Contractor
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>User:</b>
        </td>
        <td class="listrownoborder">
            <input type="hidden" id="salesman-picklist" name="salesman">
            <select id="salesman-picklist" name="salesman">
                <option value=""></option>
<?php
$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');
$dropdownUserLevels = AccountModel::getMetaValue('assign_job_salesman_user_dropdown');
$salesmen = !empty($dropdownUserLevels)
            ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
            : UserModel::getAll($showInactiveUsers, $firstLast);
foreach($salesmen as $salesman) {
    if($salesman['is_active'] == 1 && $salesman['is_deleted'] == 0) {
?>
                    <option value="<?= $salesman['user_id'] ?>" <?php if($myJob->salesman_id == $salesman['user_id']){ echo 'selected'; } ?>><?=$salesman['select_label']?></option>
<?php
    } else if($myJob->salesman_id == $salesman['user_id']) {
        $userStatus = $salesman['is_deleted'] ? 'Deleted' : 'Inactive';

?>
                    <option value="<?= $salesman['user_id'] ?>" selected><?="{$salesman['lname']}, {$salesman['fname']} ($userStatus)"?></option>
<?php
    }
}
?>
            </select>
            <span id="jobstotal"></span>
        </td>
    </tr>
    <tr>
        <td colspan=2 align="right" class="listrow">
            <input type="submit" name="submit" value="Save">
        </td>
    </tr>
</table>
</form>


<?php
		$pdfname = UserUtil::getinsurance($myJob->salesman_id); 
		//print_r(count($pdfname));
		if(count($pdfname) > 0) {
			foreach($pdfname as $pdf) {
				?>
			<div class="upload-container">
				<div class="preview">
				<a target="_blank" href="<?php echo ROOT_DIR . '/insuranceform/'.$pdf['pdfname'] ?> ">
					<img src="<?php echo ROOT_DIR . '/images/icons/pdf_lg.png'?>" alt="<?php echo $pdf['pdfname']; ?>">
				</a>
				</div>
				<ul>
					<li>
						<a target="_blank" href="<?php echo ROOT_DIR . '/insuranceform/'.$pdf['pdfname'] ?> ">
							<?php echo $pdf['pdfname']; ?>
						</a>
						
					</li>
					<li>
						<?php echo date('M d, Y @ h:i A',strtotime($pdf['datecreated'])); ?>
					</li>
				</ul>
			</div>
			<?php	
			}
		} else {
			echo '<div align="center">No  Xactimate Headers, Contacts Uploaded</div>';
		}
	?>
	<div style="clear:both;"></div>
	
<br />
<?php

$view_data = array(
	'map_id' => 'assign-salesman-map',
	'latitude' => $myCustomer->get('lat'),
	'longitude' => $myCustomer->get('long')
);
echo ViewUtil::loadView('maps/basic', $view_data);

//use a bounding rectangle to limit the number of rows we need to check in the database and speed things up
$bounds = JobUtil::getBoundingRectangle($myCustomer->get('lat'), $myCustomer->get('long'), $job_radius);
$lat = 0;
if(!empty($myCustomer->get('lat')))
{
    $lat = $myCustomer->get('lat');
}

$sql = "SELECT j.job_id, ((ACOS(SIN({$lat} * PI() / 180) * SIN(c.lat * PI() / 180) + COS({$lat} * PI() / 180) * COS(c.lat * PI() / 180) * COS(({$myCustomer->get('long')} - c.long) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS distance
        FROM jobs j, customers c
		WHERE j.account_id = '".$_SESSION['ao_accountid']."'
			AND c.customer_id != '{$myCustomer->getMyId()}'
			AND c.customer_id = j.customer_id
			AND c.long >= ".mysqli_real_escape_string(DBUtil::Dbcont(),$bounds['lon_min'])."
			AND c.long <= ".mysqli_real_escape_string(DBUtil::Dbcont(),$bounds['lon_max'])."
			AND c.lat >= ".mysqli_real_escape_string(DBUtil::Dbcont(),$bounds['lat_min'])."
			AND c.lat <= ".mysqli_real_escape_string(DBUtil::Dbcont(),$bounds['lat_max'])."
			AND j.timestamp
		HAVING distance <= $job_radius
		ORDER BY j.timestamp DESC
		LIMIT 100";
$results = DBUtil::query($sql);

?>
<script>
    $(window).on('load', function(){
        var map = $('#assign-salesman-map');
<?php
while(list($job_id) = mysqli_fetch_row($results)) {
//	break;
	$temp_job = new Job($job_id);
	$tempCustomer = new Customer($temp_job->customer_id);

	$distance = round(MapUtil::calculateDistanceFromLatLong(
        array(
            'latitude'	=> $myCustomer->get('lat'),
            'longitude'	=> $myCustomer->get('long')
        ), array(
            'latitude' => $tempCustomer->get('lat'),
            'longitude'	=> $tempCustomer->get('long')
        )),2);
	$view_data = array(
		'job' => $temp_job,
		'customer' => $tempCustomer,
		'distance' => $distance
	);
	$bubble_content = ViewUtil::loadView('map-bubble-content', $view_data)
?>
        map.jHERE('marker', [<?=$tempCustomer->get('lat')?>, <?=$tempCustomer->get('long')?>], {
            icon: '<?=IMAGES_DIR?>/icons/map/nearby-marker.png',
            anchor: {x: 12, y: 12},
            click: function(event){
                map.jHERE('bubble', [<?=$tempCustomer->get('lat')?>, <?=$tempCustomer->get('long')?>], {
                    content: '<?=$bubble_content?>'
                });
            }
        });
<?php
}
?>
    });
</script>
</body>
</html>