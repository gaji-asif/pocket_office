<?php

include '../../common_lib.php'; 

echo ViewUtil::loadView('doc-head');



$id = RequestUtil::get('id');
$todolist_job_id = RequestUtil::get('todolist_job_id');


?>

<table class="data-table-header" cellpadding="0" cellspacing="0" border="0" style="margin-bottom: 50px;">

    <tr valign="center">

        <td align="center" style="font-size: 30px;padding: 10px;">To Do List Job Details</td>

        <!--<td align="right">

            <i class="icon-remove grey btn-close-modal"></i>

        </td>-->

    </tr>

</table>



<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">

   <?php $sql = "select tbl_todolist_job_id,todolist_id,name,attachment,description,attachment_desc from tbl_todolist_job where tbl_todolist_job_id='{$todolist_job_id}' ";
  $res = DBUtil::query($sql);
  //print_r(mysqli_fetch_row($res)); 
  list($tbl_todolist_job_id,$todolist_id,$name,$attachment,$description,$attachment_desc)=mysqli_fetch_row($res);
  //$file_dir=DOCUMENTS_PATH. '/Knowledgebase/' ;
  $file_dir=ROOT_DIR.'/docs/todolist/' ;
  $file_names_array=explode("##",$attachment);
  $file_attachment_desc_array=explode("##",$attachment_desc);
  $attachment_display="<tr>";
  $header_name=$name;
  if(empty($file_names_array[0])){$attachment_display="No attachments</tr>";}
  else
  {
    $filepath=ROOT_DIR."/docs/todolist/";
    for($i=0;$i<count($file_names_array);$i++)
    {
        $attachment_display.="<td><a target='_blank' href='".$file_dir.$file_names_array[$i]."'>".$file_names_array[$i]."</a>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;".$file_attachment_desc_array[$i]."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a download='".$file_names_array[$i]."' href='".$file_dir.$file_names_array[$i]."'><i class=\"icon-download\"></i></a>
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
          <td width="300px;"><b>Job Name:</b></td>
          <td width="100%"><?=$name?></td>
      </tr>

      <tr>
          <td width="300px;"><b>Description:</b></td>
          <td width="100%"><?=$description?></td>
      </tr>
      <!--<tr>
          <td colspan="2">
          <h4>Attached documents</h4>
          <table border="0" cellpadding="0" cellspacing="0" width="100%" class="smcontainertitle">
          <?=$attachment_display?>
          </table>
          </td>
      </tr>-->
  
    </table>
</table>


</body>

</html>

