<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_software_license_checkout'))
  die("Insufficient Rights");
//echo $_SESSION['ao_founder'];
$display="";
if(!$_SESSION['ao_founder'])$display="style='display:none;'";
$softwarelicense = RequestUtil::get('softwarelicense');

$delete_flag=RequestUtil::get('delete_flag');
if(!empty($delete_flag))
{

  $sql = "update software_license_checkout set delete_flag=1 where software_license_checkout_id=".$softwarelicense;
           DBUtil::query($sql);
  $softwarelicense=null;         
}
if(empty($softwarelicense) ){

?>
<style>
.listtable td,th
{
    text-align: left;
}
    
    
</style>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <td width=203>Software licenses's</td>
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
	$not_found_header="No Software license found";
}
?>

      <tr  >
          <td id="not_found_header" name="not_found_header"><b><?=$not_found_header?></b></td>
        </tr>
<tr><td><div id="softresult" name="softresult">
    <table width="100%" class="listtable">
     <tr valign='top'>
      <th width="30%" >Software Type</th>
      <th width="30%">Software Link</th>
      <th width="30%">Login</th>
      <th width="5%">Edit</th>
      <th width="5%">Delete</th>
     </tr>
     </table>
     <?php

if(mysqli_num_rows($res)!=0)
{
	
$i=1;

while(list($software_license_checkout_id, $software_type,$software_link,$login,$password,$company_license_used_for,$checked_out_time,$checked_in_time)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';
?>
<table width="100%" class="listtable">
 <tr class='<?php echo $class; ?>' valign='top'>

        <td width="30%"><?=$software_type?></td>
        <td width="30%"><?=$software_link?></td>
        <td width="30%"><?=$login?></td>
            <td width="5%" <?=$display?>>
            <div rel="open-modal" data-script="edit_softwarelicense.php?software_license_checkout_id=<?php echo $software_license_checkout_id; ?>" class=""  tooltip="Edit">
        <i class="icon-pencil"></i>
    </div>
    </td>
    <td <?=$display?>  width="5%"><a href='javascript:if(confirm("Are you sure?")) Request.make("includes/ajax/get_softwarelicenselist.php?softwarelicense=<?php echo $software_license_checkout_id; ?>&delete_flag=1","softwarelicensecontainer","yes","yes");' tooltip="Delete"><i class="icon-trash" style="color:red" > </i></a></td>

           
           
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
<!--
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
  <?php

}
else
{
  $header_name="";
if(isset($attachment_name) )
{
  //$header_name=$attachment_name;
  $header_name=$info;
  $file_dir='https://workflowdev.xactbid.com/workflow/docs/Knowledgebase/' ;
  $file_path=$file_dir."/".$attachment_name;
$file_ext=explode(".",$attachment_name);
$file_ext=strtolower($file_ext[1]);
$display_string="";
switch($file_ext)
{
case "jpg":
$display_string="<img src=\"".$file_path."\" />";
break;
case "png":
$display_string="<img src=\"".$file_path."\" />";
break;
case "gif":
$display_string="<img src=\"".$file_path."\" />";
break;
case "pdf":
$display_string="<embed src=\"".$file_path."\" width=\"100%\" height=\"500\"></embed>";
break;
default:
$display_string="No preview Available";

}
//$display_string.="<br>".$info;


echo $display_string."<br><br>";
  ?>


<a href='javascript: Request.make("includes/ajax/get_knowledgebaselist.php?knowledgebase=<?=$knowledgebase?>","knowledgebasecontainer","yes","yes");' class='basiclink'>
        <i class="icon-double-angle-left"></i>&nbsp;Back
      </a>

  <?php
}
else
{
$sql = "select knowledgebase_id,knowledgebase_name,info,search_tag,file_names,file_descriptions from knowledgebase where knowledgebase_id='{$knowledgebase}' ";
$res = DBUtil::query($sql);
list($knowledgebase_id, $knowledgebase_name,$info,$search_tag,$file_names,$file_descriptions)=mysqli_fetch_row($res);
//$file_dir=DOCUMENTS_PATH. '/Knowledgebase/' ;
$file_dir='/workflow/docs/Knowledgebase/' ;
$file_names_array=explode("##",$file_names);
//echo var_dump($file_names_array);
$file_descriptions_array=explode("##",$file_descriptions);
$attachment_display="<tr>";
$header_name=$knowledgebase_name;
if(empty($file_names_array[0])){$attachment_display="No attachments</tr>";}
else
{
$filepath=ROOT_DIR."/docs/knowledgebase/";
for($i=0;$i<count($file_names_array);$i++)
{
  $attachment_display.="<td><a target='_blank' href='".$file_dir.$file_names_array[$i]."'>".$file_descriptions_array[$i]."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a download='".$file_names_array[$i]."' href='".$file_dir.$file_names_array[$i]."'><i class=\"icon-download\"></i></a>
  </td>";
  $result=($i+1)%3;
 if($result==0)
{
$attachment_display.="</tr>";
if($i<(count($file_names_array)-1))
$attachment_display.="<tr>";
}
else
{

}

}

}

?>
<style>


.smcontainertitle tr:nth-child(even) {background-color: #f2f2f2;}
</style>

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="table smcontainertitle">
<!-- <tr>
<td>Knowledge Base:</td>
<td><?=$knowledgebase_name?></td>
</tr> -->
<tr>
<td width="300px;"><b>Description:</b></td>
<td width="100%"><?=$info?></td>
</tr>
<tr>
<td><b>Tags</b></td>
<td><?=$search_tag?></td>
</tr>
<tr>
<td colspan="2">
<h4>Attached documents</h4>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="smcontainertitle">
<?=$attachment_display?>
</table>


</td>

</tr>
<tr>
          <td colspan=2>&nbsp;</td>
        </tr>
        <tr>
          <td class='infofooter' colspan=2>
			<a href='javascript: Request.make("includes/ajax/get_knowledgebaselist.php","knowledgebasecontainer","yes","yes");' class='basiclink'>
				<i class="icon-double-angle-left"></i>&nbsp;Back
			</a>
          </td>
        </tr>
      </table>
	</table>




	<?php
}


?>
  <script>

  $(document).ready(function(){

    try{
   var headername = document.getElementById("headername");
   headername.innerHTML="<?=$header_name?>";
   var searchbox= document.getElementById("searchbox");
   searchbox.style.display="none";
   //alert(headername);
 }catch(e){alert(e);}
    
});

  
  </script>
  <?php

}
      ?>
