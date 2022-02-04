<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_software_license_checkout'))
  die("Insufficient Rights");
//echo $_SESSION['ao_founder'];
$display="";
if(!$_SESSION['ao_founder'])$display="style='display:none;'";

$software_license_checkout_id_clicked=RequestUtil::get('software_license_checkout_id_clicked');
//echo $software_license_checkout_id_clicked;
$user_id=$_SESSION['ao_userid'];


$sql = "select fname, lname from users where  user_id=".$user_id;
//echo $sql;
$res = DBUtil::query($sql);
list($fname, $lname)=mysqli_fetch_row($res);


$user_name=$fname." ".$lname;
echo $user_name;
if(!empty($software_license_checkout_id_clicked)){
    $sql1 = "select checked_out_by from software_license_checkout where  software_license_checkout_id=".$software_license_checkout_id_clicked;
//echo $sql1;
$res12 = DBUtil::query($sql1);
list($checked_out_by)=mysqli_fetch_row($res12);
echo $checked_out_by;
if($checked_out_by=="Available"){


$sql = "update software_license_checkout set checked_out_by='".$user_name."',checked_out_time=now()  where software_license_checkout_id=".$software_license_checkout_id_clicked;
}
else
{
 $sql = "update software_license_checkout set checked_out_by='Available',checked_out_time=NULL  where software_license_checkout_id=".$software_license_checkout_id_clicked;
   
    
}
//echo $sql;
           DBUtil::query($sql);
}
//$now =( new DateTime())->format("Y-m-d H:i:s");
   // echo var_dump($now);
?>
<style>

</style>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <td width=203>Software License's</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding" id="resulttable" name="resulttable">
       <?php
$sql = "select software_license_checkout_id, software_type,software_link,login,password,company_license_used_for,checked_out_by,checked_out_time,checked_in_time from software_license_checkout where  delete_flag=0 order by software_type asc";
//echo $sql;
$res = DBUtil::query($sql);
$not_found_header="";
if(mysqli_num_rows($res)==0)
{
	$not_found_header="No Software License found";
}
?>

      <tr  >
          <td id="not_found_header" name="not_found_header"><b><?=$not_found_header?></b></td>
        </tr>
<tr><td>
     <style>
.listtable th,td
{
    
    text-align:left;
}
</style>
    <div id="softresult" name="softresult">
       
        
    <table  class="listtable" width="100%">
     <tr valign='top' >
      <th width="10%" >Software Type</th>
      <th width="10%">Software Link</th>
      <th width="15%">Login</th>
      <th width="15%">Password</th>
      <th width="20%">Company License is used for</th>
      <th width="10%">Checked out by</th>
      <th width="20%">How Long Has it been Checked out? </th>
     </tr>
     </table>
     <?php

if(mysqli_num_rows($res)!=0)
{
	
$i=1;

while(list($software_license_checkout_id, $software_type,$software_link,$login,$password,$company_licence_used_for,$checked_out_by,$checked_out_time,$checked_in_time)=mysqli_fetch_row($res))
{
    //echo $software_licence_checkout_id;
    //if(!empty($checked_out_time))echo $checked_out_time;
    
  $class='odd';
  if($i%2==0)
    $class='even';
    
    $sql = "select checked_out_time from software_license_checkout where  software_license_checkout_id=".$software_license_checkout_id;
//echo $sql;
$res1 = DBUtil::query($sql);
$check_out_time="";
list($checked_out_time)=mysqli_fetch_row($res1);
if(!empty($checked_out_time))
{
   // echo var_dump($checked_out_time);
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
    //echo "--->".(($diff-$sec)/60)." min"." ".($sec)." sec";
    
   
}
else
$check_out_time="";

?>
<table  class="listtable" width="100%">
 <tr class='<?php echo $class; ?>' valign='top'>

       

        <td width="10%" ><?=$software_type?></td>
      <td width="10%"><a target="_blank" href="microsoft-edge:<?=$software_link?>"><?=$software_link?></a></td>
      <td width="15%"><?=$login?></td>
      <td width="15%"><?=$password?></td>
      <td width="20%"><?=$company_licence_used_for?></td>
      <td width="10%"><a href='javascript: Request.make("includes/ajax/get_softwarelicense_display_list.php?software_license_checkout_id_clicked=<?=$software_license_checkout_id?>","softwarelicensecontainer","yes","yes");' class='basiclink'><?=$checked_out_by?></a></td>
      <td width="20%"><?=$check_out_time?> </td>
           
           
        
           
           
          </tr>
          </table>
<?php
        $i++;
      }

      ?>
      </div>
      </td>
      </tr>
      <?php
}
 
 ?>
  <script>

  $(document).ready(function(){

    try{
   var headername = document.getElementById("headername");
   headername.innerHTML="Software License";
   var searchbox= document.getElementById("searchbox");
   searchbox.style.display="block";
   //alert(headername);
 }catch(e){alert(e);}
    
});

  
  </script>
  