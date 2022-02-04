<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_knowledgebase'))
  die("Insufficient Rights");
//echo $_SESSION['ao_founder'];
$display="";
if(!$_SESSION['ao_founder'])$display="style='display:none;'";
$knowledgebase = RequestUtil::get('knowledgebase');
$attachment_name = RequestUtil::get('attachment');
$info=RequestUtil::get('info');
$delete_flag=RequestUtil::get('delete_flag');
if(!empty($delete_flag))
{

  $sql = "update knowledgebase set delete_flag=1 where knowledgebase_id=".$knowledgebase;
           DBUtil::query($sql);
  $knowledgebase=null;         
}
if(empty($knowledgebase) ){

?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <td width=203>Knowledge Base's</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding" id="resulttable" name="resulttable">
       <?php
$sql = "select knowledgebase_id, knowledgebase_name from knowledgebase where account_id='{$_SESSION['ao_accountid']}' and delete_flag=0 order by knowledgebase_name asc";
//echo $sql;
$res = DBUtil::query($sql);
$not_found_header="";
if(mysqli_num_rows($res)==0)
{
	$not_found_header="No Knowledgebase found";
}
?>

      <tr  >
          <td id="not_found_header" name="not_found_header"><b><?=$not_found_header?></b></td>
        </tr>
<tr><td><div id="kresult" name="kresult">
     <?php

if(mysqli_num_rows($res)!=0)
{
	
$i=1;

while(list($knowledgebase_id, $knowledgebase_name)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';
?>
<table width="100%">
 <tr class='<?php echo $class; ?>' valign='top'>

        <td width="50%">    <a href='javascript: Request.make("includes/ajax/get_knowledgebaselist.php?knowledgebase=<?php echo $knowledgebase_id; ?>","knowledgebasecontainer","yes","yes");' class='basiclink'> <?php echo $knowledgebase_name; ?></a>
            </td>
            <td width="25%" <?=$display?>>
            <div rel="open-modal" data-script="edit_knowledgebase.php?knowledgebase_id=<?php echo $knowledgebase_id; ?>" class=""  tooltip="Edit">
        <i class="icon-pencil"></i>
    </div>
    </td>
    <td <?=$display?>  width="25%"><a href='javascript:if(confirm("Are you sure?")) Request.make("includes/ajax/get_knowledgebaselist.php?knowledgebase=<?php echo $knowledgebase_id; ?>&delete_flag=1","knowledgebasecontainer","yes","yes");' tooltip="Delete"><i class="icon-trash" style="color:red" > </i></a></td>

           
           
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
   headername.innerHTML="Knowledge Base";
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
 // $file_dir='https://workflowdev.xactbid.com/workflow/docs/Knowledgebase/' ;
  $file_dir=ROOT_DIR.'/docs/Knowledgebase/';
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

<table border="0" cellpadding="0" cellspacing="0" width="100%" class="smcontainertitle">
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
