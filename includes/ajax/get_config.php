<?php
include '../common_lib.php';
UserModel::isAuthenticated();

if($_SESSION['ao_founder']!=1)
  die("Insufficient Rights");

//build array
/*
$configArray = array(
	'App Configuration' => 'app-configuration',
	'Email Templates' => 'emailtemplates',
	'SMS Templates' => 'smstemplates',
	'Insurance Providers' => 'providers',
	'Job Origins' => 'joborigins',
	'Job Types' => 'job_types',
	'Jurisdictions' => 'jurisdictions',
	'Materials' => 'modmaterials',
	'Offices' => 'modoffices',
	'Status Holds' => 'statusholds',
	'Task Types' => 'tasktypes',
	'User Groups' => 'usergroups',
	'Warranties' => 'warranties',
    'Stages' => 'stages-list',
    'Actions' => 'action-list',
    'Contact Header' => 'contact-header-list'
);
*/
$configArray = array(
	'App Configuration' => 'app-configuration',
	'Email Templates' => 'emailtemplates',
	'SMS Templates' => 'smstemplates',
	'Insurance Providers' => 'providers',
	'Job Origins' => 'joborigins',
	'Job Types' => 'job_types',
	'Jurisdictions' => 'jurisdictions',
	
	'Status Holds' => 'statusholds',
	'Task Types' => 'tasktypes',
	'User Groups' => 'usergroups',
	
    'Stages' => 'stages-list',
    'Actions' => 'action-list',
    'Contact Header' => 'contact-header-list',
    'Knowledge Base' => 'knowledgebase_mod',
    'Software License Checkout' => 'software_license_checkout_mod',
    'Check List' => 'check-list',
    'Add Ons' => 'add-ons',
    'To Do List' => 'to-do-list'
);
//sort
ksort($configArray);

//break into columns
$cols = 4;
$span = 12 / $cols;
$configArray = array_chunk($configArray, ceil(count($configArray) / $cols), true);
?>
<table class="data-table" width="100%">
	<tr>
		<td>
			<div class="container">
				<div class="row pillbox">
<?php
foreach($configArray as $column)
{
?>
					<div class="col span-<?=$span?>">
<?php
	foreach($column as $label => $script)
	{
?>
						<div><a href="<?=ROOT_DIR?>/<?=$script?>.php" class="btn btn-blue btn-block"><?=$label?></a></div>
<?php
	}
?>
					</div>
<?php
}
?>
				</div>
			</div>
		</td>
	</tr>
</table>