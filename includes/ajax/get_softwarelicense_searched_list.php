<?php
include '../common_lib.php';
$search_key=trim($_REQUEST['search_key']);
//$sql = "select knowledgebase_id, knowledgebase_name from knowledgebase where account_id='{$_SESSION['ao_accountid']}' and knowledgebase_name like '%".$search_key."%' and delete_flag=0 order by knowledgebase_name asc";
$sql="";
if(empty($search_key)){
    $sql = "select software_license_checkout_id, software_type,software_link,login,password,company_license_used_for,checked_out_by,checked_out_time from software_license_checkout where delete_flag=0 order by software_type asc";

}
else
{

$sql = "select software_license_checkout_id, software_type,software_link,login,password,company_license_used_for,checked_out_by,checked_out_time from software_license_checkout where company_license_used_for like '%".$search_key."%' and delete_flag=0 order by software_type asc";
}
//echo $sql;
$res = DBUtil::query($sql);
$search_result="0";
if(mysqli_num_rows($res)!=0)
{
	$i=1;
	$search_result="";
while(list($software_license_checkout_id, $software_type,$software_link,$login,$password,$company_license_used_for,$checked_out_by,$checked_out_time)=mysqli_fetch_row($res))
{
    $check_out_time="";
    if(!empty($checked_out_time))
{
   
    
    $diff =  strtotime('now') - strtotime($checked_out_time);
     $diff_sec=$diff%60;
    $diff_min=($diff-$diff_sec)/60;
    $rem_min=0;
    $diff_hour=0;
    if($diff_min>59)
    {
      $rem_min= $diff_min%60; 
      $diff_hour=($diff_min-$rem_min)/60;
      if($diff_hour>23)
      {
           $rem_hour= $diff_hour%24; 
           $diff_day=($diff_hour-$rem_hour)/24;
           $check_out_time= $diff_day." day ".$rem_hour." hour ".$rem_min." min ".$diff_sec." sec";
      }
      else
      {
      $check_out_time=$diff_hour." hour ".$rem_min." min ".$diff_sec." sec";
      }
    }
    
    else
    {
     $check_out_time=$diff_min." min ".$diff_sec." sec";
    }
    
    
    
    
    
    
    
}
else
$check_out_time="";

$search_result.="{\"software_license_checkout_id\":\"".$software_license_checkout_id."\",\"software_type\":\"".$software_type."\",\"software_link\":\"".$software_link."\",\"login\":\"".$login."\",\"password\":\"".$password."\",\"company_license_used_for\":\"".$company_license_used_for."\",\"checked_out_by\":\"".$checked_out_by."\",\"checked_out_time\":\"".$check_out_time."\"}";
if($i<mysqli_num_rows($res))$search_result.=",";
$i++;
}
$search_result="{\"softwarelicense\":[".$search_result."]}";
//$search_result="[".$search_result."]";
}
echo $search_result;
?>