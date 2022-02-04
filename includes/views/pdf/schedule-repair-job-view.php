<style>
	div.logo > img {height: 75px;}
	h1 {font-size: 24px;}
	h2 {font-size: 20px;}
	.label {font-weight: bold;}
</style>
<div class="logo"><img class="logo" src="<?=@$logo?>" /></div>
<h1>Repair Job Schedule Form</h1>
<table width="100%" cellpadding="3">
	<tr>
		<td class="label">Customer:</td>
		<td><?=@$meta_data['schedule_repair_job_customer']['meta_value']?></td>
		<td class="label">Job #:</td>
		<td><?=@$job_number?></td>
	</tr>
	<tr>
		<td class="label">Phone:</td>
		<td><?=@$meta_data['schedule_repair_job_phone']['meta_value']?></td>
		<td class="label">Job DOB:</td>
		<td><?=@$meta_data['schedule_repair_job_startdate']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Address:</td>
		<td><?=@$meta_data['schedule_repair_job_address']['meta_value']?></td>
		<td class="label">Salesman:</td>
		<td><?=@$meta_data['schedule_repair_job_salesman']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">City/State/Zip:</td>
		<td><?=@$meta_data['schedule_repair_job_city']['meta_value']?>, <?=@$meta_data['schedule_repair_job_state']['meta_value']?> <?=@$meta_data['schedule_repair_job_zip']['meta_value']?></td>
		<td class="label">Salesman Phone:</td>
		<td><?=@$meta_data['schedule_repair_job_phone2']['meta_value']?></td>
	</tr>
	<tr>
		<td colspan="2" class="label">Agree Upon Price w/ Contractor:</td>
		<td colspan="2"><?=@$meta_data['schedule_repair_job_agreed_upon_price']['meta_value']?></td>
	</tr>
</table>
<h2>Job Details</h2>
<table width="100%" cellpadding="3">
	<tr>
		<td class="label">House:</td>
		<td><?=@$meta_data['schedule_repair_job_house']['meta_value']?></td>
		<td class="label">Garage:</td>
		<td><?=@$meta_data['schedule_repair_job_garage']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Shed:</td>
		<td><?=@$meta_data['schedule_repair_job_shed']['meta_value']?></td>
		<td class="label">Patio:</td>
		<td><?=@$meta_data['schedule_repair_job_patio']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Gutters:</td>
		<td><?=@$meta_data['schedule_repair_job_gutters']['meta_value']?></td>
		<td class="label">Color:</td>
		<td><?=@$meta_data['schedule_repair_job_color']['meta_value']?></td>
	</tr>
	<tr>
		<td class="label">Total LF:</td>
		<td><?=@$meta_data['schedule_repair_job_total_l_f']['meta_value']?></td>
		<td class="label">Downspout:</td>
		<td><?=@$meta_data['schedule_repair_job_downspout']['meta_value']?></td>
	</tr>
</table>
<h2>Additional Details</h2>
<p><?=nl2br(@$meta_data['schedule_repair_job_repair_details']['meta_value'])?></p>