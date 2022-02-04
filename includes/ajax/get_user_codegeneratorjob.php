<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('edit_users'))
	die("Insufficient Rights");

$ac_id = $_SESSION['ao_accountid'];
//get data
$userId = RequestUtil::get('id');
$codegeneratorjob_id = RequestUtil::get('codegeneratorjob_id');
$codegenerator_id= RequestUtil::get('codegenerator_id');
$checkedAll = RequestUtil::get('checkedAll');
// get user
$myUser = new User($userId);

$sql = "SELECT tbl_codegenerator_job_id from user_codegenerator_job_access where account_id=".$ac_id." and user_id=".$userId;
//echo $sql;
$access_list = DBUtil::queryToArray($sql);
$userAccess=array();
foreach($access_list as $access)
{
	$userAccess[]=$access['tbl_codegenerator_job_id'];
}


//edit
if($codegeneratorjob_id) {
	//remove from exceptions table
	if(in_array($codegeneratorjob_id,$userAccess)) {
		 $sql = "DELETE FROM user_codegenerator_job_access
				WHERE user_id = '$userId' AND tbl_codegenerator_job_id = '$codegeneratorjob_id' LIMIT 1";
	}
	else {
		
		$sql = "INSERT INTO user_codegenerator_job_access (user_id, account_id, tbl_codegenerator_job_id, has_access)
					VALUES ('$userId', '{$_SESSION['ao_accountid']}', '$codegeneratorjob_id','1')";
		
	}
    //echo $sql;
	//execute query and rebuild data arrays upon success
	if(DBUtil::query($sql)) {
		$sql = "SELECT tbl_codegenerator_job_id from user_codegenerator_job_access where account_id=".$ac_id." and user_id=".$userId;
		$access_list = DBUtil::queryToArray($sql);
		$userAccess=array();
		foreach($access_list as $access)
		{
			$userAccess[]=$access['tbl_codegenerator_job_id'];
		}
	}
	//echo $sql;
}
else if($codegenerator_id)
{
            $sqlDelete = "";
    	    $sql = "SELECT tbl_codegenerator_job_id from tbl_codegenerator_job where is_deleted ='0' and codegenerator_id=".$codegenerator_id." order by order_num asc";
            $codegenerator = DBUtil::queryToArray($sql);
            //echo($sql);
             $sql = "INSERT INTO user_codegenerator_job_access (user_id, account_id, tbl_codegenerator_job_id)
    					VALUES ";
            $i = 0;
            foreach($codegenerator as $r)
            {
                $sqlDelete = "DELETE FROM user_codegenerator_job_access
    				WHERE user_id = '$userId' AND tbl_codegenerator_job_id = '{$r['tbl_codegenerator_job_id']}'
    				LIMIT 1";
    				
    				DBUtil::queryToArray($sqlDelete);
                if(count($codegenerator)>1 && $i > 0)
                {
    		        $sql .=  ", ('$userId', '$ac_id', '{$r['tbl_codegenerator_job_id']}')";
                }
                else
                {
                    $sql .=  " ('$userId', '$ac_id', '{$r['tbl_codegenerator_job_id']}')";
                }
                $i++;
            }
            if($checkedAll == '1')
            {
                //echo($sql);
                DBUtil::queryToArray($sql);
            }

    	
    	$sql = "SELECT tbl_codegenerator_job_id from user_codegenerator_job_access where account_id=".$ac_id." and user_id=".$userId;
    	
		$access_list = DBUtil::queryToArray($sql);
		$userAccess=array();
		foreach($access_list as $access)
		{
			$userAccess[]=$access['tbl_codegenerator_job_id'];
		}
}


if( $_GET['action']=='codegeneratorownership')
{
 if($_GET['checked'] == 'true')
 {
   $sql = "INSERT INTO user_codegenerator_job_access (user_id, account_id, tbl_codegenerator_job_id, has_access)
                        VALUES ('".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."', '$ac_id', '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['codegeneratorjob_id'])."','1')";
      //echo $sql;
      DBUtil::query($sql);
 }
  else 
  {
       $sql = "DELETE FROM user_codegenerator_job_access WHERE user_id = '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' AND tbl_codegenerator_job_id = '".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['codegeneratorjob_id'])."'";
       //echo $sql;
       DBUtil::query($sql);
  }
    
}
?>
<table border="0" width="100%">

<tr>		

<tr>
          <td colspan=2>

            <b>Code Generator:</b>

            <span class='smallnote'>

              <br />A = Access

            

            </span>

          </td>

 	</tr>


<?php
$sql = "SELECT codegenerator_id,codegenerator_name from tbl_codegenerator where account_id=".$ac_id." order by order_num asc";
//echo $sql;
$codegenerator = DBUtil::queryToArray($sql);
$wdth = (100/count($codegenerator)).'%';

foreach($codegenerator as $r)
{
    $checkedAll = '';
    $classAll = '';
	
    $sql = "SELECT
                t1.tbl_codegenerator_job_id
            FROM
                (
                SELECT
                    a.tbl_codegenerator_job_id,
                    a.account_id,
                    a.is_deleted,
                    a.codegenerator_id
                FROM
                    tbl_codegenerator_job AS a
                WHERE
                    a.codegenerator_id = {$r['codegenerator_id']} AND a.is_deleted = '0'
            ) AS t1
            LEFT JOIN(
                SELECT
                    b.tbl_codegenerator_job_id,
                    b.account_id
                FROM
                    user_codegenerator_job_access AS b
                WHERE
                    b.user_id = $userId
            ) AS t2
            ON
                t1.tbl_codegenerator_job_id = t2.tbl_codegenerator_job_id AND t1.account_id = t2.account_id
            WHERE
                t1.codegenerator_id = {$r['codegenerator_id']} AND t2.tbl_codegenerator_job_id IS NULL";
                //echo $sql;
          $joblistAll = DBUtil::queryToArray($sql);
          if($joblistAll)
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
<th width="<?=$wdth?>" style="text-align: left;padding-left: 3px;"><input type="checkbox" onchange="redirectCodeAll(this)" data2='id=<?php echo $userId; ?>&checkedAll=1&codegenerator_id=<?=$r['codegenerator_id']?>' data1='id=<?php echo $userId; ?>&checkedAll=0&codegenerator_id=<?=$r['codegenerator_id']?>'  <?=$checkedAll?>/><span  style="text-align: left;padding-left: 5px;"><?=$r['codegenerator_name']?></span></th>

<?php } ?>
</tr>

<tr>
<?php foreach($codegenerator as $row)
{?>
<td width="<?=$wdth?>" style="vertical-align:top;">
<table>
 <tr>
                      
                      <td width="20" align="center"><b>A</b></td>
                      <td width="20" align="center"><b>O</b></td>
                      <td>&nbsp;&nbsp;</td>
                      
                    </tr>
<?php $sql = "SELECT t1.tbl_codegenerator_job_id,t1.name from tbl_codegenerator_job as t1 left 
                            join tbl_codegenerator as t2 on t2.codegenerator_id=t1.codegenerator_id  
                            where t1.account_id=".$ac_id." and t1.codegenerator_id=".$row['codegenerator_id']." and t1.is_deleted='0'
                            order by t1.order_num asc";
  
$joblist = DBUtil::queryToArray($sql);
//echo "<pre>";print_r($userAccess);die;
foreach($joblist as $job_row)
{
	$class = '';
	$checked = '';
	$checked2 = '';
	$checked3 = '';
	if(in_array($job_row['tbl_codegenerator_job_id'],$userAccess)) {
		$checked = 'checked';
		
	}
	if($exception) {
		$class = "red";
	}
	//echo $checked;
	 $sql2 = "SELECT * FROM user_codegenerator_job_access WHERE user_id=".$userId." AND account_id=".$ac_id." AND tbl_codegenerator_job_id=".$job_row['tbl_codegenerator_job_id'];
        //echo    $sql2;             
                        $alevel = DBUtil::queryToArray($sql2);
                          if($job_row['tbl_codegenerator_job_id']==$alevel[0]['tbl_codegenerator_job_id']) {
                             $checked2 = 'checked';
                        }
?>
	<tr>
	<!--	<td width="20"><input type="checkbox" rel="edit-user-codegenerator-job-access" data-user-id="<?=$userId?>"  data-chcklistjob-id="<?=$job_row['tbl_codegenerator_job_id']?>" <?=$checked?> /></td>-->
		                <td align="center" width=20>
		 

                       
                        
            <input onchange="redirectCode(this)" data2='id=<?php echo $userId; ?>&action=codegeneratorownership&checked=true&codegeneratorjob_id=<?=$job_row['tbl_codegenerator_job_id']?>' data1='id=<?php echo $userId; ?>&action=codegeneratorownership&checked=&codegeneratorjob_id=<?=$job_row['tbl_codegenerator_job_id']?>' type="checkbox"  <?=$checked?>/>
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
    function redirectCode(checkboxElem) {
  if (checkboxElem.checked) {
     Request.make('get_user_codegeneratorjob.php?'+checkboxElem.getAttribute('data2'),'user-codegenerator-job-container', false, true);
  } else {
    Request.make('get_user_codegeneratorjob.php?'+checkboxElem.getAttribute('data1'),'user-codegenerator-job-container', false, true);
  }
}
  function redirectCodeAll(checkboxElem) {
  if (checkboxElem.checked) {
     Request.make('get_user_codegeneratorjob.php?'+checkboxElem.getAttribute('data2'),'user-codegenerator-job-container', false, true);
  } else {
    Request.make('get_user_codegeneratorjob.php?'+checkboxElem.getAttribute('data1'),'user-codegenerator-job-container', false, true);
  }

}
</script>