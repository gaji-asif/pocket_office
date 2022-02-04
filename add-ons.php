<?php
include 'includes/common_lib.php';

UserModel::isAuthenticated();

if($_SESSION['ao_founder']!=1)

  die("Insufficient Rights");



/****** UPLOAD LOGO ******/


?>

<?=ViewUtil::loadView('doc-head')?>

    <h1 class="page-title"><i class="icon-cogs"></i><a href="<?=ROOT_DIR?>/system.php">System</a> - Add ons</h1>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
   

      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center'>
              <td>Add ons available</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td id='configcontainer'></td>
      </tr>
      <tr>
        <td colspan=2>
          <?php
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
    //'Waste Calculator' => '',
    'Code Generator' => 'code-generator',
      'Vent Calculator' => 'vent-calculator',
    //'Add Ons' => 'add-ons'
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
        </td>
      </tr>

      
      <tr><td>&nbsp;</td></tr>
    </table>
  </body>
</html>

