<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('edit_users'))
	die("Insufficient Rights");

$ac_id = $_SESSION['ao_accountid'];
//get data
$userId = RequestUtil::get('id');
$todolistjob_id = RequestUtil::get('todolistjob_id');

//get user
$myUser = new User($userId);

$sql = "SELECT tbl_todolist_job_id from todolist_user_access where account_id=".$ac_id." and user_id=".$userId;
$access_list = DBUtil::queryToArray($sql);
$userAccess=array();
foreach($access_list as $access)
{
	$userAccess[]=$access['tbl_todolist_job_id'];
}


//edit
if($todolistjob_id) {
	//remove from exceptions table
	if(in_array($todolistjob_id,$userAccess)) {
		$sql = "DELETE FROM todolist_user_access
				WHERE user_id = '$userId' AND tbl_todolist_job_id = '$todolistjob_id'
				LIMIT 1";
	}
	else {
		
		$sql = "INSERT INTO todolist_user_access (user_id, account_id, tbl_todolist_job_id)
					VALUES ('$userId', '{$_SESSION['ao_accountid']}', '$todolistjob_id')";
		
	}

	//execute query and rebuild data arrays upon success
	if(DBUtil::query($sql)) {
		$sql = "SELECT tbl_todolist_job_id from todolist_user_access where account_id=".$ac_id." and user_id=".$userId;
		$access_list = DBUtil::queryToArray($sql);
		$userAccess=array();
		foreach($access_list as $access)
		{
			$userAccess[]=$access['tbl_todolist_job_id'];
		}
	}
}
?>
<table border="0" width="100%">

<tr>		
	


<?php


$sql = "SELECT todolist_id,todolist_name from tbl_todolist where account_id=".$ac_id." order by order_num asc";
//echo $sql;
$todolist = DBUtil::queryToArray($sql);

$wdth = (100/count($todolist)).'%';

foreach($todolist as $r)
{?>
<th width="<?=$wdth?>" style="text-align: left;padding-left: 20px;"><?=$r['todolist_name']?></th>

<?php } ?>
</tr>

<tr>
<?php foreach($todolist as $row)
{?>
<td width="<?=$wdth?>" style="vertical-align:top;">
<table>

<?php $sql = "SELECT t1.tbl_todolist_job_id,t1.name from tbl_todolist_job as t1 left 
                            join tbl_todolist as t2 on t2.todolist_id=t1.todolist_id  
                            where t1.account_id=".$ac_id." and t1.todolist_id=".$row['todolist_id']." and t1.is_deleted='0'
                            order by t1.order_num asc";
  
$joblist = DBUtil::queryToArray($sql);

foreach($joblist as $job_row)
{
	$class = '';
	$checked = '';
	if(in_array($job_row['tbl_todolist_job_id'],$userAccess)) {
		$checked = 'checked';
		$class = "red";
	}
	
?>
	<tr>
		<td width="20"><input type="checkbox" rel="edit-user-todolist-job-access" data-user-id="<?=$userId?>"  data-todolistjob-id="<?=$job_row['tbl_todolist_job_id']?>" <?=$checked?> /></td>
		<td class="<?=$class?>"><?=$job_row['name']?></td>
	</tr>
<?php }?>
</table>
</td>
<?php }?>
</tr>
</table>