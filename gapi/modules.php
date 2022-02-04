<?php
require_once '../includes/common_lib.php';

//check for system user
UserModel::systemUserCheck();

//get all modules
$modules_array = ModuleUtil::getAll();

//add new module
if(isset($_POST['module-name']))
{
	//empty errors array
	$post_errors = array();

	//empty post error classes array
	$post_error_classes = array();

	//error checking
	if(isset($modules_array[$_POST['module-hook']]))
	{
		$post_error_classes['module-hook'] = 'error';
		$post_errors[] = 'Hook in use';
	}
	if(empty($_POST['module-name']))
	{
		$post_error_classes['module-name'] = 'error';
		$post_errors[] = 'Name cannot be empty';
	}
	if(empty($_POST['module-hook']))
	{
		$post_error_classes['module-hook'] = 'error';
		$post_errors[] = 'Hook cannot be empty';
	}
	if(empty($_POST['module-description']))
	{
		$post_error_classes['module-description'] = 'error';
		$post_errors[] = 'Description cannot be empty';
	}
	if(!in_array($_POST['module-ownership'], array(0, 1)))
	{
		$post_error_classes['module-ownership'] = 'error';
		$post_errors[] = 'Ownership must be Yes or No';
	}

	//no errors, add to database
	if(empty($post_errors))
	{
		//build and execute query
		$sql = "INSERT INTO modules (title, description, hook, ownership)
				VALUES ('{$_POST['module-name']}', '{$_POST['module-description']}', '{$_POST['module-hook']}', '{$_POST['module-ownership']}')";
		DBUtil::query($sql) or die(mysqli_error());

		//get insert id
		$new_module_id = mysqli_insert_id();

		//reload list
		header("Location: ?highlight=$new_module_id");
		die();
	}
}

//delete module
if(!empty($_GET['delete']))
{
	//get escaped value
	$module_id = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['delete']);

	//make sure module isn't in use
	if(!ModuleUtil::moduleIsInUse($module_id))
	{
		//build and execute query
		$sql = "DELETE FROM modules WHERE module_id = '$module_id' LIMIT 1";
		DBUtil::query($sql) or die(mysqli_error());

		$_SESSION['system_global_alert'] = array(
			'type' => 'success',
			'message' => 'Module successfully removed'
		);

		//reload list
		header("Location: modules.php");
		die();
	}
	else
	{
		$_SESSION['system_global_alert'] = array(
			'type' => 'error',
			'message' => 'Module currently in use - cannot remove'
		);

		//reload list
		header('Location: modules.php');
		die();
	}
}
?>

<?=ViewUtil::loadView('system-configuration-head', array('title' => 'Modules'))?>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
<?=ViewUtil::loadView('system-configuration-sidebar')?>
				</div>
				<div class="span9">
					<div class="page-header">
						<h1>Modules</h1>
					</div>
<?php
if(!empty($_SESSION['system_global_alert']))
{
?>
					<div class="alert alert-<?=$_SESSION['system_global_alert']['type']?>">
						<?=$_SESSION['system_global_alert']['message']?>
					</div>
<?php
	unset($_SESSION['system_global_alert']);
}
?>
					<div class="well well-small">
						<h3>Add Module</h3>
<?php
if(!empty($post_errors))
{
?>
						<div class="alert alert-error">
							<ul class="unstyled">
								<li><strong>Errors Found</strong></li>
<?php
	foreach($post_errors as $error)
	{
?>
								<li><?=$error?></li>
<?php
	}
?>
							</ul>
						</div>
<?php
}
?>
						<form class="form-horizontal" action="?" name="add-module" method="post">
							<div class="control-group <?=@$post_error_classes['module-name']?>">
								<label class="control-label" for="module-name">Module Name</label>
								<div class="controls">
									<input type="text" class="span6" id="module-name" name="module-name" value="<?=@$_POST['module-name']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['module-hook']?>">
								<label class="control-label" for="module-hook">Module Hook</label>
								<div class="controls">
									<input type="text" class="span6" id="module-hook" name="module-hook" value="<?=@$_POST['module-hook']?>" />
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['module-description']?>">
								<label class="control-label" for="module-description">Module Description</label>
								<div class="controls">
									<textarea id="module-description" name="module-description" class="span6"><?=@$_POST['module-description']?></textarea>
								</div>
							</div>
							<div class="control-group <?=@$post_error_classes['module-ownership']?>">
								<label class="control-label" for="module-ownership">Ownership</label>
								<div class="controls">
									<select id="module-ownership" name="module-ownership" class="span2">
										<option value="0">No</option>
										<option value="1">Yes</option>
									</select>
								</div>
							</div>
							<div class="form-actions">
								<button type="reset" class="btn">Reset</button>
								<button type="submit" class="btn btn-primary">Submit</button>
							</div>
						</form>
					</div>
					<div>
						<table class="table table-bordered table-condensed table-hover table-striped">
							<thead>
								<tr>
									<th>Module</th>
									<th>Description</th>
									<th>Hook</th>
									<th>Ownership</th>
									<th>Actions</th>
								</tr>
							</thead>
							<tbody>
<?php
foreach($modules_array as $module)
{
	//ownership
	$ownership = 'Yes';
	if($module['ownership'] == 0)
	{
		$ownership = 'No';
	}

	//highly newly added
	$row_class = '';
	if($_GET['highlight'] == $module['module_id'])
	{
		$row_class = 'success';
	}
?>
								<tr class="<?=$row_class?>">
									<td><?=$module['title']?></td>
									<td><?=$module['description']?></td>
									<td><?=$module['hook']?></td>
									<td><?=$ownership?></td>
									<td>
										<a href="?delete=<?=$module['module_id']?>" rel="delete-module" data-module-title="<?=$module['title']?>" title="Delete <?=$module['title']?>"><i class="icon-trash"></i></a>
										<a href=""><i class="icon-pencil"></i></a>
										<a href=""><i class="icon-info-sign"></i></a>
									</td>
								</tr>
<?php
}
?>
							</tbody>
						</table>
					</div>
				</div>
			</div>
<?=ViewUtil::loadView('system-configuration-footer')?>