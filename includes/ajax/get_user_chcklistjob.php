<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('edit_users'))
	die("Insufficient Rights");

$ac_id = $_SESSION['ao_accountid'];
//get data
$userId = RequestUtil::get('id');
$chcklistjob_id = RequestUtil::get('chcklistjob_id');
$chckAll = RequestUtil::get('chckAll');
$chkListId = RequestUtil::get('chkListId');
//get user
$myUser = new User($userId);

$sql = "SELECT tbl_checklist_job_id from user_checklist_job_access where account_id=".$ac_id." and user_id=".$userId;
$access_list = DBUtil::queryToArray($sql);
$userAccess=array();
foreach($access_list as $access)
{
	$userAccess[]=$access['tbl_checklist_job_id'];
}


//edit
if($chcklistjob_id) {
	//remove from exceptions table
   if(in_array($chcklistjob_id,$userAccess)) {
    		$sql = "DELETE FROM user_checklist_job_access
    				WHERE user_id = '$userId' AND tbl_checklist_job_id = '$chcklistjob_id'
    				LIMIT 1";
    	}
    	else {
    		
    		$sql = "INSERT INTO user_checklist_job_access (user_id, account_id, tbl_checklist_job_id)
    					VALUES ('$userId', '{$_SESSION['ao_accountid']}', '$chcklistjob_id')";
    		
    	}
    	//execute query and rebuild data arrays upon success
	if(DBUtil::query($sql)) {
		$sql = "SELECT tbl_checklist_job_id from user_checklist_job_access where account_id=".$ac_id." and user_id=".$userId;
		$access_list = DBUtil::queryToArray($sql);
		$userAccess=array();
		foreach($access_list as $access)
		{
			$userAccess[]=$access['tbl_checklist_job_id'];
		}
	}

	
}
else
{
    if($chkListId)
	{
	    
            $sqlDelete = "";
    	    $sql = "SELECT tbl_checklist_job_id from tbl_checklist_job where is_deleted ='0' and checklist_id=".$chkListId." order by order_num asc";
            $checklist = DBUtil::queryToArray($sql);
            
             $sql = "INSERT INTO user_checklist_job_access (user_id, account_id, tbl_checklist_job_id)
    					VALUES ";
            $i = 0;
            foreach($checklist as $r)
            {
                $sqlDelete = "DELETE FROM user_checklist_job_access
    				WHERE user_id = '$userId' AND tbl_checklist_job_id = '{$r['tbl_checklist_job_id']}'
    				LIMIT 1";
    				DBUtil::queryToArray($sqlDelete);
                if(count($checklist)>1 && $i > 0)
                {
    		        $sql .=  ", ('$userId', '{$_SESSION['ao_accountid']}', '{$r['tbl_checklist_job_id']}')";
                }
                else
                {
                    $sql .=  " ('$userId', '{$_SESSION['ao_accountid']}', '{$r['tbl_checklist_job_id']}')";
                }
                $i++;
            }
            if($chckAll == '1')
            {
                DBUtil::queryToArray($sql);
            }

    	
    	$sql = "SELECT tbl_checklist_job_id from user_checklist_job_access where account_id=".$ac_id." and user_id=".$userId;
		$access_list = DBUtil::queryToArray($sql);
		$userAccess=array();
		foreach($access_list as $access)
		{
			$userAccess[]=$access['tbl_checklist_job_id'];
		}
	}
}
?>
<table border="0" width="100%">

<tr>		
	


<?php


$sql = "SELECT checklist_id,checklist_name,account_id from tbl_checklist where account_id=".$ac_id." order by order_num asc";
//echo $sql;
$checklist = DBUtil::queryToArray($sql);

$wdth = (100/count($checklist)).'%';

foreach($checklist as $r)
{

    $checkedAll = '';
    $classAll = '';
	
    $sql = "SELECT
                t1.tbl_checklist_job_id
            FROM
                (
                SELECT
                    a.tbl_checklist_job_id,
                    a.account_id,
                    a.is_deleted,
                    a.checklist_id
                FROM
                    tbl_checklist_job AS a
                WHERE
                    a.checklist_id = {$r['checklist_id']} AND a.is_deleted = '0'
            ) AS t1
            LEFT JOIN(
                SELECT
                    b.tbl_checklist_job_id,
                    b.account_id
                FROM
                    user_checklist_job_access AS b
                WHERE
                    b.user_id = $userId
            ) AS t2
            ON
                t1.tbl_checklist_job_id = t2.tbl_checklist_job_id AND t1.account_id = t2.account_id
            WHERE
                t1.checklist_id = {$r['checklist_id']} AND t2.tbl_checklist_job_id IS NULL";
          $joblist = DBUtil::queryToArray($sql);
          if($joblist)
          {
              $checkedAll = '';
              $classAll = '';
          }
          else
          {
              $checkedAll = 'checked';
              $classAll = 'Red';
          }
    
?>

<th width="<?=$wdth?>" style="text-align: left;"><input type="checkbox" rel="toggle-get-user-chcklistjob" id="<?=$r['checklist_id']?>"  data-chklist-id="<?=$r['checklist_id']?>" data-checklist-name="<?=$r['checklist_name']?>"  <?=$checkedAll?>/>&nbsp;&nbsp;<span class="<?=$classAll?>"><?=$r['checklist_name']?></span></th>

<?php } ?>
</tr>

<tr>
<?php foreach($checklist as $row)
{?>
<td width="<?=$wdth?>" style="vertical-align:top;">
<table>

<?php $sql = "SELECT t1.tbl_checklist_job_id,t1.name,t2.checklist_name from tbl_checklist_job as t1 left 
                            join tbl_checklist as t2 on t2.checklist_id=t1.checklist_id  
                            where t1.account_id=".$ac_id." and t1.checklist_id=".$row['checklist_id']." and t1.is_deleted='0'
                            order by t1.order_num asc";
  
$joblist = DBUtil::queryToArray($sql);
foreach($joblist as $job_row)
{
	$class = '';
	$checked = '';
	$exception = false;
	if(in_array($job_row['tbl_checklist_job_id'],$userAccess)) {
		$checked = 'checked';
		$exception = true;
	}

	if($exception) {
		$class = "red";
	}
?>
	<tr>
		<td width="20"><input type="checkbox" name="<?=$job_row['checklist_name']?>" rel="edit-user-checklist-job-access" data-user-id="<?=$userId?>"  data-chcklistjob-id="<?=$job_row['tbl_checklist_job_id']?>" <?=$checked?> /></td>
		<td id = "<?=$job_row['tbl_checklist_job_id']?>" class="<?=$class?>"><?=$job_row['name']?></td>
	</tr>
<?php }?>
</table>
</td>
<?php }?>
</tr>
</table>
