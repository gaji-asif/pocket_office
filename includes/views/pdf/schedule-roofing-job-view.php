<style>
	div.logo > img {height: 75px;}
	h1 {font-size: 24px;}
	h2 {font-size: 20px;}
	.label {font-weight: bold;}
</style>
<div class="logo"><img class="logo" src="<?=@$logo?>" /></div>
<h1>Roofing Job Schedule Form</h1>
<table width="100%" cellpadding="3">
	<tr>
		<td class="label">Customer:</td>
		<td><?=@$meta_data['schedule_roofing_job_customer']['meta_value']?></td>
		<td class="label">Job #:</td>
		<td><?=@$job_number?></td>
	</tr>
	<tr>
		<td class="label">Phone:</td>
		<td><?=@$meta_data['schedule_roofing_job_phone']['meta_value']?></td>
		<td class="label">Job DOB:</td>
		<td><?=@$meta_data['schedule_roofing_job_startdate']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Address:</td>
		<td><?=@$meta_data['schedule_roofing_job_address']['meta_value']?></td>
		<td class="label">Salesman:</td>
		<td><?=@$meta_data['schedule_roofing_job_salesman']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">City/State/Zip:</td>
		<td><?=@$meta_data['schedule_roofing_job_city']['meta_value']?>, <?=@$meta_data['schedule_roofing_job_state']['meta_value']?> <?=@$meta_data['schedule_roofing_job_zip']['meta_value']?></td>
		<td class="label">Salesman Phone:</td>
		<td><?=@$meta_data['schedule_roofing_job_phone2']['meta_value']?></td>
	</tr>
	<tr>
		<td colspan="2" class="label">Agree Upon Price w/ Contractor:</td>
		<td colspan="2"><?=@$meta_data['schedule_roofing_job_agreed_upon_price']['meta_value']?></td>
	</tr>
</table>
<h2>Job Details</h2>
<table width="100%" cellpadding="3">
	<tr>
		<td class="label">Existing Roof:</td>
		<td><?=@$meta_data['schedule_roofing_job_existing_roof']['meta_value']?></td>
		<td class="label">House Layers/Squares:</td>
		<td><?=@$meta_data['schedule_roofing_job_house']['meta_value']?>/<?=@$meta_data['schedule_roofing_job_house_squares']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label"># of stories:</td>
		<td><?=@$meta_data['schedule_roofing_job_stories']['meta_value']?></td>
		<td class="label">Garage Layers/Squares:</td>
		<td><?=@$meta_data['schedule_roofing_job_garage']['meta_value']?>/<?=@$meta_data['schedule_roofing_job_garage_squares']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">New Roof:</td>
		<td><?=@$meta_data['schedule_roofing_job_new_roof']['meta_value']?></td>
		<td class="label">Shed Layers/Squares:</td>
		<td><?=@$meta_data['schedule_roofing_job_shed']['meta_value']?>/<?=@$meta_data['schedule_roofing_job_shed_squares']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Squares:</td>
		<td><?=@$meta_data['schedule_roofing_job_squares']['meta_value']?></td>
		<td class="label">Patio Layers/Squares:</td>
		<td><?=@$meta_data['schedule_roofing_job_patio']['meta_value']?>/<?=@$meta_data['schedule_roofing_job_patio_squares']['meta_value']?></td>
	</tr>
<?php
$total_layers = @$meta_data_array['schedule_roofing_job_house']['meta_value'] + @$meta_data_array['schedule_roofing_job_garage']['meta_value'] + @$meta_data_array['schedule_roofing_job_shed']['meta_value'] + @$meta_data_array['schedule_roofing_job_patio']['meta_value'];
$total_squares = @$meta_data_array['schedule_roofing_job_house_squares']['meta_value'] + @$meta_data_array['schedule_roofing_job_garage_squares']['meta_value'] + @$meta_data_array['schedule_roofing_job_shed_squares']['meta_value'] + @$meta_data_array['schedule_roofing_job_patio_squares']['meta_value'];
?>
	<tr>
		<td class="label">Gutter Tear Off:</td>
		<td><?=@$meta_data['schedule_roofing_job_gutters_tear_off']['meta_value']?></td>
		<td class="label">Total Layers/Squares:</td>
		<td><?=$total_layers?>/<?=$total_squares?></td>
	</tr>
	<tr>
		<td class="label">Roof Color:</td>
		<td><?=@$meta_data['schedule_roofing_job_job_new_roof_color']['meta_value']?></td>
		<td class="label">What gutters to tear off:</td>
		<td></td>
	</tr>
	<tr>
		<td class="label">Pitch:</td>
		<td><?=@$meta_data['schedule_roofing_job_pitch']['meta_value']?></td>
		<td class="label">Drip Edge Installation:</td>
<?php
$drip_edge_display = @$meta_data['schedule_roofing_job_drip_edge']['meta_value'];
if(@$meta_data['schedule_roofing_job_drip_edge']['meta_value'] == 'Rake')
{
	$drip_edge_display = 'Eave and Rake';
}
?>
		<td><?=$drip_edge_display?></td>
	</tr>
	<tr>
		<td class="label">Gutter Color:</td>
		<td><?=@$meta_data['schedule_roofing_job_gutters_color']['meta_value']?></td>
		<td></td>
		<td></td>
	</tr>
</table>
<h2>Additional Details</h2>
<p><?=nl2br(@$meta_data['schedule_roofing_job_job_details']['meta_value'])?></p>
<hr />
<h2>Tear Off Notes</h2>
<p><?=nl2br(@$meta_data['schedule_roofing_job_tear_off_notes']['meta_value'])?></p>