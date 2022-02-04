<?php
require_once '../includes/common_lib.php';

//check for system user
UserModel::systemUserCheck();
?>

<?=ViewUtil::loadView('system-configuration-head')?>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
<?=ViewUtil::loadView('system-configuration-sidebar')?>
				</div>
				<div class="span9">
					<div class="hero-unit">
						<h1>Welcome</h1>
						<p>This is the <?=APP_NAME?> System Configuration portal. Please choose an operation from the left to begin.</p>
					</div>
				</div>
			</div>
<?=ViewUtil::loadView('system-configuration-footer')?>