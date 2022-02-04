<?php
include '../../common_lib.php';
$search_key=trim($_REQUEST['search_key']);
//$sql = "select knowledgebase_id, knowledgebase_name from knowledgebase where account_id='{$_SESSION['ao_accountid']}' and knowledgebase_name like '%".$search_key."%' and delete_flag=0 order by knowledgebase_name asc";
$sql="";
$ac_id = $_SESSION['ao_accountid'];
$id = RequestUtil::get('id');
if(empty($search_key)){
    $sql = "SELECT t1.tbl_checklist_job_id, t1.name,t2.checklist_name from tbl_checklist_job as t1
        left join tbl_checklist as t2 on t2.checklist_id=t1.checklist_id  
        where t1.account_id=".$ac_id." and t1.checklist_id=".$id." and t1.is_deleted='0' order by t1.tbl_checklist_job_id desc";
}
else
{
	$sql = "SELECT t1.tbl_checklist_job_id, t1.name,t2.checklist_name from tbl_checklist_job as t1
        left join tbl_checklist as t2 on t2.checklist_id=t1.checklist_id  
        where t1.account_id=".$ac_id." and t1.checklist_id=".$id." and ( t1.name like '%".$search_key."%' or t2.checklist_name like '%".$search_key."%' ) and t1.is_deleted='0' order by t1.tbl_checklist_job_id desc";    
}
//echo $sql;
$res = DBUtil::query($sql);
$search_result="0";
if(mysqli_num_rows($res)!=0)
{
	$i=1;
	$search_result="";
	while(list($tbl_checklist_job_id, $name,$checklist_name)=mysqli_fetch_row($res))
	{
		$search_result.="{\"id\":\"".$tbl_checklist_job_id."\",\"kname\":\"".$name."\",\"type\":\"".$checklist_name."\"}";
		if($i<mysqli_num_rows($res))$search_result.=",";
		$i++;
	}
	$search_result="{\"joblist\":[".$search_result."]}";
	//$search_result="[".$search_result."]";
}
echo $search_result;
?>