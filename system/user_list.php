
<div class="control-group <?=@$post_error_classes['user']?>">
	<label class="control-label" for="user">User</label>
	<div class="controls">
	<select class="span6" id="user" name="user">
	<?php
	$users = UserModel::getAllInSystem();
	foreach($users as $userId => $user) 
	{
	?>
		<option value="<?=$userId?>" <?=$_SESSION['ao_userid'] == $userId ? 'selected' : ''?>>
	        <?=$user['lname']?>, <?=$user['fname']?> (<?=$user['account_name']?>) - <?=$user['level']?>
	    </option>
	<?php
	}
	?>
	</select>
	</div>
</div>