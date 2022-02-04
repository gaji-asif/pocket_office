<?php
include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);

//center of the USA
$gpsCoords = array('39.8282', '-98.5795');
$radius = RequestUtil::get('radius');
$zoom = 5;
$address = RequestUtil::get('address');

//get address
if($address) {
	$gpsCoords = CustomerModel::getGPSCoords($address);
	$radius = $radius ?: 10;

	if($radius) {
		$zoom = 8;

		//determine zoom
		if($radius > 40) {
			$zoom++;
		}
		else if($radius > 30) {
			$zoom++;
		}
		else if($radius > 20) {
			$zoom+=2;
		}
		else if($radius > 10) {
			$zoom+=2;
		}
		else {
			$zoom+=3;
		}
	}
}
?>
<?=ViewUtil::loadView('doc-head')?>
<div id="jobs-map"></div>
<?php
$view_data = array(
	'map_id' => 'jobs-map',
	'latitude' => $gpsCoords[0],
	'longitude' => $gpsCoords[1],
	'zoom' => $zoom
);
echo ViewUtil::loadView('maps/basic', $view_data);
?>
<div id="search-jobs-map-container">
	<form id="search-jobs-map" method="get">
		<input type="text" name="address" value="<?=$address?>"/>
		<label for="label">Radius</label>
		<select name="radius">
<?php
for($i = 5; $i <= 50; $i+=5) {
?>
			<option value="<?=$i?>" <?=$radius == $i ? 'selected' : ''?>><?=$i?></option>
<?php
}
?>
		</select>
		<input type="submit" value="Search" />
	</form>
</div>

<script type="text/javascript">
	$(window).on('load', function(){
		var map = $('#jobs-map');
<?php
$jobs = JobUtil::getForMap($gpsCoords[0], $gpsCoords[1], $radius);
foreach($jobs as $job) {
//	break;
	$tempJob = new Job($job['job_id']);
	$tempCustomer = new Customer($tempJob->customer_id);

	$distance = round(MapUtil::calculateDistanceFromLatLong(array('latitude'	=> $gpsCoords[0], 'longitude'	=> $gpsCoords[1]),array('latitude' => $tempCustomer->get('lat'), 'longitude'	=> $tempCustomer->get('long'))), 2);
	$view_data = array(
		'job' => $tempJob,
		'customer' => $tempCustomer,
		'distance' => $distance,
		'select_salesman_icon' => false,
		'basic_link' => true
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