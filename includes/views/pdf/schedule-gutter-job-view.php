<style>
	div.logo > img {height: 75px;}
	h1 {font-size: 24px;}
	h2 {font-size: 20px;}
	.label {font-weight: bold;}
</style>
<div class="logo"><img class="logo" src="<?=@$logo?>" /></div>
<h1>Gutter Job Schedule Form</h1>
<table width="100%" cellpadding="3">
	<tr>
		<td class="label">Customer:</td>
		<td><?=@$meta_data['schedule_gutter_job_customer']['meta_value']?></td>
		<td class="label">Job #:</td>
		<td><?=@$job_number?></td>
	</tr>
	<tr>
		<td class="label">Phone:</td>
		<td><?=@$meta_data['schedule_gutter_job_phone']['meta_value']?></td>
		<td class="label">Job DOB:</td>
		<td><?=@$meta_data['schedule_gutter_job_startdate']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Address:</td>
		<td><?=@$meta_data['schedule_gutter_job_address']['meta_value']?></td>
		<td class="label">Salesman:</td>
		<td><?=@$meta_data['schedule_gutter_job_salesman']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">City/State/Zip:</td>
		<td><?=@$meta_data['schedule_gutter_job_city']['meta_value']?>, <?=@$meta_data['schedule_gutter_job_state']['meta_value']?> <?=@$meta_data['schedule_gutter_job_zip']['meta_value']?></td>
		<td class="label">Salesman Phone:</td>
		<td><?=@$meta_data['schedule_gutter_job_phone2']['meta_value']?></td>
	</tr>
	<tr>
		<td colspan="2" class="label">Agree Upon Price w/ Contractor:</td>
		<td colspan="2"><?=@$meta_data['schedule_gutter_job_agreed_upon_price']['meta_value']?></td>
	</tr>
</table>
<h2>Job Details</h2>
<table width="100%" cellpadding="3">
	<tr>
		<td class="label">Gutter lineal footage:</td>
		<td><?=@$meta_data['schedule_gutter_job_gutter_l_f']['meta_value']?></td>
		<td class="label">Downspout lineal footage:</td>
		<td><?=@$meta_data['schedule_gutter_job_downspout_l_f']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Gutter Color:</td>
		<td><?=@$meta_data['schedule_gutter_job_gutter_color']['meta_value']?></td>
		<td class="label">Gutter Size:</td>
		<td><?=@$meta_data['schedule_gutter_job_gutter_size']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Downspout Size:</td>
		<td><?=@$meta_data['schedule_gutter_job_downspout_size']['meta_value']?></td>
		<td class="label">Gutter Material:</td>
		<td><?=@$meta_data['schedule_gutter_job_gutter_material']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Gutter Cover Type:</td>
		<td><?=@$meta_data['schedule_gutter_job_cover_type']['meta_value']?></td>
		<td class="label">Gutter Cover Lineal Footage:</td>
		<td><?=@$meta_data['schedule_gutter_job_cover_lineal_footage']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Pitch:</td>
		<td><?=@$meta_data['schedule_gutter_job_pitch']['meta_value']?></td>
		<td class="label">Electrical Outlet Location:</td>
		<td><?=@$meta_data['schedule_gutter_job_electrical_outlet_location']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Stories:</td>
		<td><?=@$meta_data['schedule_gutter_job_stories']['meta_value']?></td>
		<td></td>
		<td></td>
	</tr>
</table>
<h2>Additional Details</h2>
<p><?=nl2br(@$meta_data['schedule_gutter_job_job_details']['meta_value'])?></p>
<hr />
<h2>Tear Off Notes</h2>
<p><?=nl2br(@$meta_data['schedule_gutter_job_tear_off_notes']['meta_value'])?></p>