<?php
require_once '../includes/common_lib.php';

//check for system user
UserModel::systemUserCheck();
?>

<?=ViewUtil::loadView('payment-head')?>
<div class="container-fluid">
<div class="row-fluid">
	<div class="span12">
		<div class="hero-unit">
			<h1>Success</h1>
			<p>You payment successfully done.</p>
		</div>	
		<div class="hero-unit span2" style="padding: 10px;">
			<ul class="nav nav-list">				
				<li><a href="<?=ROOT_DIR?>">Back to App</a></li>
			</ul>
		</div>		
	</div>
</div>