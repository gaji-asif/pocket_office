<?php
require_once '../includes/common_lib.php';

//check for system user
UserModel::systemUserCheck();

//switch user

if(isset($_POST['user'])) {
	
	if(switchToUser($_POST['user'])) {
		$successMessage = "Successfully switched to {$_SESSION['ao_fname']} {$_SESSION['ao_lname']} ({$_SESSION['ao_accountname']})";
	} else {
		$errors[] = 'Failed to switch to user';
	}
}
?>

<?=ViewUtil::loadView('system-configuration-head', array('title' => 'Add Account'))?>
<div class="container-fluid">
	<div class="row-fluid">
		<div class="span3">
<?=ViewUtil::loadView('system-configuration-sidebar')?>
		</div>
		<div class="span9">
			<div class="page-header">
				<h1>Switch User</h1>
			</div>
			<div class="well well-small">
<?php
if(!empty($errors)) {
?>
			<div class="alert alert-error">
				<ul class="unstyled">
					<li><strong>Errors Found</strong></li>
	<?php
	foreach($errors as $error) {
	?>
						<li><?=$error?></li>
	<?php
	}
	?>
				</ul>
			</div>
<?php
}
if(!empty($successMessage)) {
?>
			<div class="alert alert-success">
				<strong><?=$successMessage?></strong>
			</div>
<?php
}
?>
			<form class="form-horizontal" action="?" name="switch-user" method="post">	

				<div class="control-group <?=@$post_error_classes['account']?>">
					<label class="control-label" for="account">Account</label>
					<div class="controls">
					<select class="span6" id="account" name="account" onchange="getUserList();">
					<option value="">All</option>
					<?php
						$accounts = UserModel::getAllAccounts();
						
						foreach($accounts as $account => $row) 
						{
					?>
							<option value="<?=$row['account_id']?>" <?=$_SESSION['ao_accountid'] == $row['account_id'] ? 'selected' : ''?>>
				                <?=$row['account_name']?>
				            </option>
					<?php
						}
					?>
					</select>
					</div>
				</div>

				<div class="control-group <?=@$post_error_classes['account']?>">
					<label class="control-label" for="level">Account</label>
					<div class="controls">
					<select class="span6" id="level" name="level" onchange="getUserList();">
					<option value="">All</option>
					<?php
						$levels = UserModel::getAllLevel();
						
						foreach($levels as $id => $row) 
						{
					?>
							<option value="<?=$row['level_id']?>" <?=$_SESSION['ao_level'] == $row['level_id'] ? 'selected' : ''?>>
				                <?=$row['level']?>
				            </option>
					<?php
						}
					?>
					</select>
					</div>
				</div>


				<div class="control-group <?=@$post_error_classes['user']?>">
				    <label class="control-label" for="user">User</label>
				    <div class="controls">
				    <select class="span6" id="user" name="user">
				    <?php
				    $users = UserModel::getUserByAccount($_SESSION['ao_accountid'],$_SESSION['ao_level']);
				    ?>				    
				    <?php 
				    foreach($users as $userId => $row) 
				    {
				    ?>
				        <option value="<?=$row['user_id']?>" <?=$_SESSION['ao_userid'] == $row['user_id'] ? 'selected' : ''?>>
				            <?=$row['lname']?>, <?=$row['fname']?> - <?=$row['level']?>
				        </option>
				    <?php
				    }
				    ?>
				    </select>
				    </div>
				</div>

				



				<div class="form-actions">
					<button type="reset" class="btn">Reset</button>
					<button type="submit" class="btn btn-primary">Submit</button>
				</div>
			</form>
		</div>
	</div>
</div>
<?=ViewUtil::loadView('system-configuration-footer')?>

<script type="text/javascript">
function getUserList()
{	
	var account_id=$("#account").val();
	var level_id=$("#level").val();
	var query_str="account_id=" + account_id+"&level_id="+level_id;
	
    $.ajax({
        url: '<?php echo AJAX_DIR;?>/get_accountuser.php',
        data: query_str,        
        type: 'POST',
        success: function(result){
            $("#user").html(result);
        }
    });        
}
</script>