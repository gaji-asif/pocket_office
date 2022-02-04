<?php
require_once '../includes/common_lib.php';

//check for system user
UserModel::systemUserCheck();
?>

<?=ViewUtil::loadView('system-configuration-head', array('title' => 'Job Actions'))?>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
<?=ViewUtil::loadView('system-configuration-sidebar')?>
				</div>
				<div class="span9">
					<div class="page-header">
						<h1>Job Actions</h1>
					</div>
				</div>
			</div>
<?=ViewUtil::loadView('system-configuration-footer')?>