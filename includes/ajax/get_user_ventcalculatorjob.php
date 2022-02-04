<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('edit_users'))
	die("Insufficient Rights");

$ac_id = $_SESSION['ao_accountid'];
//get data
$userId = RequestUtil::get('id');
$ventcalculatorjob_id = RequestUtil::get('ventcalculatorjob_id');

//get user
$myUser = new User($userId);

 $sql = "SELECT tbl_ventcalculator_job_id from user_ventcalculator_job_access where account_id=".$ac_id." and user_id=".$userId;
$access_list = DBUtil::queryToArray($sql);
$userAccess=array();
foreach($access_list as $access)
{
	$userAccess[]=$access['tbl_ventcalculator_job_id'];
}


//edit
if($ventcalculatorjob_id ) {
 
	//remove from exceptions table
	if(in_array($ventcalculatorjob_id,$userAccess)) {
		 $sql = "DELETE FROM user_ventcalculator_job_access
				WHERE user_id = '$userId' AND tbl_ventcalculator_job_id = '$ventcalculatorjob_id' LIMIT 1";
			 
	}
	else {
		
		    $sql = "INSERT INTO user_ventcalculator_job_access (user_id, account_id, tbl_ventcalculator_job_id, has_access)
					VALUES ('$userId', '{$_SESSION['ao_accountid']}', '$ventcalculatorjob_id','1')"; 
				 
		
	}

	//execute query and rebuild data arrays upon success
	if(DBUtil::query($sql)) {
		$sql = "SELECT tbl_ventcalculator_job_id from user_ventcalculator_job_access where account_id=".$ac_id." and user_id=".$userId;
		$access_list = DBUtil::queryToArray($sql);
		$userAccess=array();
		foreach($access_list as $access)
		{
			$userAccess[]=$access['tbl_ventcalculator_job_id'];
		}
	}
}

?>
<table border="0" width="100%">

<tr>		

<tr>
          <td colspan=2>

            <b>Vent Calculator:</b>

            <span class='smallnote'>

              <br />A = Access

            

            </span>

          </td>

 	</tr>


<?php
$sql = "SELECT ventcalculator_id,ventcalculator_name from tbl_ventcalculator where account_id=".$ac_id." order by order_num asc";
//echo $sql;
$ventcalculator = DBUtil::queryToArray($sql);
$wdth = (100/count($ventcalculator)).'%';

foreach($ventcalculator as $r)
{
?>
<th width="<?=$wdth?>" style="text-align: left;padding-left: 20px;"><?=$r['ventcalculator_name']?></th>

<?php } ?>
</tr>

<tr>
<?php foreach($ventcalculator as $row)
{?>
<td width="<?=$wdth?>" style="vertical-align:top;">
<table>
 <tr>
                      <td width="20" align="center"><b>A</b></td>
                      <td width="20" align="center"><b>O</b></td>
                      <td>&nbsp;</td>
                    </tr>
<?php $sql = "SELECT t1.tbl_ventcalculator_job_id,t1.name from tbl_ventcalculator_job as t1 left 
                            join tbl_ventcalculator as t2 on t2.ventcalculator_id=t1.ventcalculator_id  
                            where t1.account_id=".$ac_id." and t1.ventcalculator_id=".$row['ventcalculator_id']." and t1.is_deleted='0'
                            order by t1.order_num asc";
  
$joblist = DBUtil::queryToArray($sql);
foreach($joblist as $job_row)
{
	$class = '';
	$checked = '';
	$checked2 = '';
	$checked3 = '';
	if(in_array($job_row['tbl_ventcalculator_job_id'],$userAccess)) {
		$checked = 'checked';
		
	}
	if($exception) {
		$class = "red";
	}
	
	 $sql2 = "SELECT * FROM user_ventcalculator_job_access WHERE user_id=".$userId." AND account_id=".$ac_id." AND tbl_ventcalculator_job_id=".$job_row['tbl_ventcalculator_job_id'];
                        $alevel = DBUtil::queryToArray($sql2);
                          if($job_row['tbl_ventcalculator_job_id']==$alevel[0]['tbl_ventcalculator_job_id']) {
                             $checked2 = 'checked';
                        }
?>
	<tr>
	<!--	<td width="20"><input type="checkbox" rel="edit-user-ventcalculator-job-access" data-user-id="<?=$userId?>"  data-chcklistjob-id="<?=$job_row['tbl_ventcalculator_job_id']?>" <?=$checked?> /></td>-->
		                <td align="center" width=20>
		 

                       
                        
            <input onchange="redirect(this)" data2='id=<?php echo $userId; ?>&action=ventcalculatorownership&checked=true&ventcalculatorjob_id=<?=$job_row['tbl_ventcalculator_job_id']?>' data1='id=<?php echo $userId; ?>&action=ventcalculatorownership&checked=&ventcalculator_id=<?=$job_row['tbl_ventcalculator_job_id']?>' type="checkbox"  <?=$checked?>/>
          </td>
		<td class="<?=$class?>"><?=$job_row['name']?></td>
	</tr>
<?php }?>
</table>
</td>
<?php }?>
</tr>
</table>
<script>
    function redirect(checkboxElem) {
  if (checkboxElem.checked) {
     Request.make('get_user_ventcalculatorjob.php?'+checkboxElem.getAttribute('data2'));
  } else {
    Request.make('get_user_ventcalculatorjob.php?'+checkboxElem.getAttribute('data1'));
  }
}
</script>