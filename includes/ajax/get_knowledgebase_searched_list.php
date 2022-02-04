<?php
include '../common_lib.php';
$search_key=trim($_REQUEST['search_key']);
//$sql = "select knowledgebase_id, knowledgebase_name from knowledgebase where account_id='{$_SESSION['ao_accountid']}' and knowledgebase_name like '%".$search_key."%' and delete_flag=0 order by knowledgebase_name asc";
$sql="";
if(empty($search_key)){
    $sql = "select knowledgebase_id, knowledgebase_name from knowledgebase where delete_flag=0 order by knowledgebase_name asc";

}
else
{

//$sql = "select knowledgebase_id, knowledgebase_name from knowledgebase where knowledgebase_name like '%".$search_key."%' and delete_flag=0 order by knowledgebase_name asc";
$sql = "select knowledgebase_id, knowledgebase_name from knowledgebase where ( knowledgebase_name like '%".$search_key."%' or search_tag like '%".$search_key."%' ) and delete_flag=0 order by knowledgebase_name asc";

    
}
//echo $sql;
$res = DBUtil::query($sql);
$search_result="0";
if(mysqli_num_rows($res)!=0)
{
	$i=1;
	$search_result="";
while(list($knowledgebase_id, $knowledgebase_name)=mysqli_fetch_row($res))
{
$search_result.="{\"id\":\"".$knowledgebase_id."\",\"kname\":\"".$knowledgebase_name."\"}";
if($i<mysqli_num_rows($res))$search_result.=",";
$i++;
}
$search_result="{\"knowledgebase\":[".$search_result."]}";
//$search_result="[".$search_result."]";
}
echo $search_result;
?>