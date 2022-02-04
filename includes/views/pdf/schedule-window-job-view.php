<style>
	div.logo > img {height: 75px;}
	h1 {font-size: 24px;}
	h2 {font-size: 20px;}
	.label {font-weight: bold;}
</style>
<div class="logo"><img class="logo" src="<?=@$logo?>" /></div>
<h1>Window Job Schedule Form</h1>
<table width="100%" cellpadding="3">
	<tr>
		<td class="label">Customer:</td>
		<td><?=@$meta_data['schedule_window_job_customer']['meta_value']?></td>
		<td class="label">Job #:</td>
		<td><?=@$job_number?></td>
	</tr>
	<tr>
		<td class="label">Phone:</td>
		<td><?=@$meta_data['schedule_window_job_phone']['meta_value']?></td>
		<td class="label">Job DOB:</td>
		<td><?=@$meta_data['schedule_window_job_startdate']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Address:</td>
		<td><?=@$meta_data['schedule_window_job_address']['meta_value']?></td>
		<td class="label">Salesman:</td>
		<td><?=@$meta_data['schedule_window_job_salesman']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">City/State/Zip:</td>
		<td><?=@$meta_data['schedule_window_job_city']['meta_value']?>, <?=@$meta_data['schedule_window_job_state']['meta_value']?> <?=@$meta_data['schedule_window_job_zip']['meta_value']?></td>
		<td class="label">Salesman Phone:</td>
		<td><?=@$meta_data['schedule_window_job_phone2']['meta_value']?></td>
	</tr>
	<tr>
		<td colspan="2" class="label">Agree Upon Price w/ Contractor:</td>
		<td colspan="2"><?=@$meta_data['schedule_window_job_agreed_upon_price']['meta_value']?></td>
	</tr>
</table>
<h2>Job Details</h2>
<table width="100%" cellpadding="3">
	<tr>
		<td class="label"># of Windows:</td>
		<td><?=@$meta_data['schedule_window_job_no_window']['meta_value']?></td>
		<td class="label">Marked?:</td>
		<td><?=@$meta_data['schedule_window_job_marked']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Window on Story #:</td>
		<td><?=@$meta_data['schedule_window_job_window_story']['meta_value']?></td>
		<td class="label">Side of House:</td>
		<td><?=@$meta_data['schedule_window_job_window_side']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Type of window:</td>
		<td><?=@$meta_data['schedule_window_job_window_type']['meta_value']?></td>
		<td class="label">Window Color:</td>
		<td><?=@$meta_data['schedule_window_job_window_color']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Screen:</td>
		<td><?=@$meta_data['schedule_window_job_glazing_bead']['meta_value']?></td>
		<td class="label">Glazing Bead:</td>
		<td><?=@$meta_data['schedule_window_job_window_screen']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label" colspan="2">Window Dimensions:</td>
		<td colspan="2"><?=@$meta_data['schedule_window_job_window_dimension_x']['meta_value']?> X <?=@$meta_data['schedule_window_job_window_dimension_y']['meta_value']?> (Rough opening)</td>
	</tr>
</table>
<h2>Description Of Damage</h2>
<p><?=nl2br(@$meta_data['schedule_window_job_des_damage']['meta_value'])?></p>
<hr />
<h2>Additional Details</h2>
<p><?=nl2br(@$meta_data['schedule_window_job_specific_detail']['meta_value'])?></p>