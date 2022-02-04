<?php include '../../common_lib.php';
//echo $_SESSION['ao_founder'];
$display="";
if(!$_SESSION['ao_founder'])$display="style='display:none;'";

$id = RequestUtil::get('id');
$todolist_job_id = RequestUtil::get('todolist_job_id');
$attachment_name = RequestUtil::get('attachment');
$info=RequestUtil::get('info');
$delete_flag=RequestUtil::get('delete_flag');
if(!empty($delete_flag))
{

    $sql = "update tbl_todolist_job set is_deleted=1 where tbl_todolist_job_id=".$todolist_job_id;
           DBUtil::query($sql);
  $todolist_job_id=null;         
}
if(empty($todolist_job_id) ){

?>

<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <td width="60%">Name</td>
          <td width="20%">Type</td>
          <td width="20%">Action</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding" id="resulttable" name="resulttable">
       <?php
        $ac_id = $_SESSION['ao_accountid'];

        $sql = "SELECT t1.tbl_todolist_job_id, t1.name,t2.todolist_name from tbl_todolist_job as t1
        left join tbl_todolist as t2 on t2.todolist_id=t1.todolist_id  
        where t1.account_id=".$ac_id." and t1.todolist_id='".$id."' and t1.is_deleted='0' order by t1.order_num,t1.tbl_todolist_job_id";
        
        //$res = DBUtil::query($sql);
        $checklist = DBUtil::queryToArray($sql);
        $not_found_header="";
        if(empty($checklist))
        {
        	$not_found_header="No Add-ons found";
        }
        ?>

      <tr  >
          <td colspan="3" id="not_found_header" name="not_found_header"><b><?=$not_found_header?></b></td>
        </tr>
<tr><td><div id="kresult" name="kresult">
     <?php
	
$i=1;

  foreach($checklist as $row)
  {
    $class='odd';
    if($i%2==0)
      $class='even';
    ?>
    <table width="100%">
     <tr class='<?php echo $class; ?>' valign='top'>

          <td width="60%">    
            <a href='javascript: Request.make("includes/ajax/todolist/get_joblist.php?id=<?=$id?>&todolist_job_id=<?php echo $row['tbl_todolist_job_id']; ?>","knowledgebasecontainer","yes","yes");' class='basiclink'> <?php echo $row['name']; ?></a>
          </td>
          <td width="20%" <?=$display?>>
              <?php echo $row['todolist_name']; ?>
          </td>
          <td <?=$display?>  width="20%">

              <div title="Edit" style="cursor: pointer;float: left;" rel="open-modal" data-script="todolist/edit_job.php?id=<?=$id?>&todolist_job_id=<?php echo $row['tbl_todolist_job_id']; ?>" class=""  tooltip="Edit">
              <i class="icon-pencil"></i>
              </div>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;

              <a title="Delete"  href='javascript:if(confirm("Are you sure?")) Request.make("includes/ajax/todolist/get_joblist.php?id=<?=$id?>&todolist_job_id=<?php echo $row['tbl_todolist_job_id']; ?>&delete_flag=1","knowledgebasecontainer","yes","yes");' tooltip="Delete"><i class="icon-trash" style="color:red" > </i></a>
          </td>

             
             
            </tr>
            </table>
  <?php
          $i++;
    }

        ?>
        </div>
        </td>
        </tr>
<tr><td class="infofooter"><a href="<?=ROOT_DIR?>/to-do-list.php" class="basiclink"><i class="icon-double-angle-left"></i>&nbsp;Back</a></td></tr>
   

    <script>

    $(document).ready(function(){

      try{
     var headername = document.getElementById("headername");
     headername.innerHTML="To Do List job";
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
        $file_dir=ROOT_DIR.'/docs/todolist/';
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


      <a href='javascript: Request.make("includes/ajax/todolist/get_joblist.php?id=<?=$id?>&todolist_job_id=<?=$tbl_todolist_job_id?>","knowledgebasecontainer","yes","yes");' class='basiclink'>
              <i class="icon-double-angle-left"></i>&nbsp;Back
            </a>

        <?php
  }
else
{
$sql = "select tbl_todolist_job_id,todolist_id,name,attachment,description,attachment_desc from tbl_todolist_job where tbl_todolist_job_id='{$todolist_job_id}' ";
$res = DBUtil::query($sql);
list($tbl_todolist_job_id,$todolist_id,$name,$attachment,$description,$attachment_desc)=mysqli_fetch_row($res);
$header_name=$name;

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
<td width="100%"><?=$description?></td>
</tr>
<tr>
          <td colspan=2>&nbsp;</td>
        </tr>
        <tr>
          <td class='infofooter' colspan=2>
			<a href='javascript: Request.make("includes/ajax/todolist/get_joblist.php?id=<?=$id?>","knowledgebasecontainer","yes","yes");' class='basiclink'>
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
