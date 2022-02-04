<?php
include '../../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);
$ss="";

$todolist_job_id=RequestUtil::get('todolist_job_id');
$name = RequestUtil::get('name');
$todolist_id = RequestUtil::get('type');
$job_color = RequestUtil::get('job_color');
$description = RequestUtil::get('description');
$attachment_name=RequestUtil::get('attachment_name');
$delete_flag=RequestUtil::get('delete_flag');
$file_desc=RequestUtil::get('file_desc');
$id = RequestUtil::get('id');
if(!empty($delete_flag))
{
    $sql = "select attachment,attachment_desc from tbl_todolist_job where tbl_todolist_job_id=".$todolist_job_id;
    $res = DBUtil::query($sql);
    list($attachment,$file_descriptions)=mysqli_fetch_row($res);
    $attachment_array=explode("##",$attachment);
    $new_attachment="";
    $file_descriptions_array=explode("##",$file_descriptions);
    $new_file_descriptions="";

    for($i=0;$i<count($attachment_array);$i++)
    {
       if($attachment_name==$attachment_array[$i])  
       {
           continue;
       }
       else
       {
           $new_attachment.=$attachment_array[$i]."##";
           $new_file_descriptions.=$file_descriptions_array[$i]."##";
       }        
    }
    $new_attachment=trim($new_attachment,"##");
    $new_file_descriptions=trim($new_file_descriptions,"##");
  
    $sql = "update tbl_todolist_job set attachment='". $new_attachment."',attachment_desc='". $new_file_descriptions."' where tbl_todolist_job_id=".$todolist_job_id;

    DBUtil::query($sql);  
}
if(!empty($attachment_name) && empty($delete_flag))
{
    $sql = "select attachment,attachment_desc from tbl_todolist_job where tbl_todolist_job_id=".$todolist_job_id;
    $res = DBUtil::query($sql);
    list($attachment,$file_descriptions)=mysqli_fetch_row($res);
    $attachment_array=explode("##",$attachment);
    $file_descriptions_array=explode("##",$file_descriptions);
    $new_attachment="";
    $new_file_descriptions="";
    $j=0;
    for($i=0;$i<count($attachment_array);$i++)
    {
        if($attachment_name==$attachment_array[$i])  
        {
           $file_descriptions_array[$i]=trim($file_desc)==""?"N/A":$file_desc;
        }
        $new_attachment.=$attachment_array[$i]."##"; 
        $new_file_descriptions.=$file_descriptions_array[$i]."##";
    }

    $new_attachment=trim($new_attachment,"##");
    $new_file_descriptions=trim($new_file_descriptions,"##");
  
    $sql = "update tbl_todolist_job set attachment='". $new_attachment."',attachment_desc='". $new_file_descriptions."' where tbl_todolist_job_id=".$todolist_job_id;
           DBUtil::query($sql); 
           
}
$errors = array();
if(RequestUtil::get("submit"))
{

      $attachment_attachment=RequestUtil::get('attachment');
      $attachment_file_description=RequestUtil::get('file_descriptions');

      if(empty($name))
      {
        $errors[] = 'Name cannot be blank';
      }
      if(empty($todolist_id))
      {
        $errors[] = 'Please select Type!';
      }
      if(empty($job_color))
      {
        $errors[] = 'Color cannot be blank';
      }
      
      if(!count($errors)) 
      {
          $sql = "update tbl_todolist_job set name='".$name."',todolist_id=".$todolist_id.", description='".$description."',color='". $job_color."'   where tbl_todolist_job_id=".$todolist_job_id;
             DBUtil::query($sql);
      ?>


      <script>
      $(document).ready(function()
      {
          try{
             // alert('loaded');
             var opener = window.parent;
             opener.location.reload();
             var closebutton = $('.btn-close-modal');   
             closebutton.trigger('click');
            }
           catch(e)
           {
            alert(e);
           }
      });
        
      </script>
      <?php
      } 
}


?>




<script type="text/javascript">
tinymce.init({
    branding: false,
    selector: "textarea",

plugins: [
    "advlist autolink lists link image charmap print preview anchor",
    "searchreplace visualblocks code fullscreen",
    "insertdatetime media table contextmenu paste "
],
toolbar: "insertfile undo redo | styleselect | bold italic | alignleft aligncenter      alignright alignjustify | bullist numlist outdent indent | link image"
});
</script>
<script type="text/javascript">
function add_attachment_field()
{

  var s="";
  s+="<tr><td class='listrownoborder'><input width='25%'' type='file' name='myFile[]'>";
    s+="</td></tr></table></td></tr>";
  $("#attachment-container").append(s);
}
</script>
<!--<?=$ss?>-->

<?php
if(RequestUtil::get("todolist_job_id")) 
{
$todolist_job_id = RequestUtil::get('todolist_job_id');

$sql = "select tbl_todolist_job_id,todolist_id,name,description,attachment,attachment_desc,color from tbl_todolist_job where tbl_todolist_job_id=".$todolist_job_id;
//echo $sql;
$res = DBUtil::query($sql);


//$res = DBUtil::query($sql);
list($todolist_job_id, $todolist_id,$name,$description,$attachment,$file_descriptions,$color)=mysqli_fetch_row($res);
$file_dir=DOCUMENTS_PATH. '/todolist/' ;
$attachment_array=explode("##",$attachment);
$file_descriptions_array=explode("##",$file_descriptions);
$attachment_display="<tr>";
for($i=0;$i<count($attachment_array);$i++)
{
    $attachment_display.="<td><a href='".$file_dir.$attachment_array[$i]."'>".$attachment_array[$i]."</a>
  <br>".$file_descriptions_array[$i]."</td>";
    $result=($i+1)%1;
    if($result==0)
    {
      $attachment_display.="</tr>";
      if($i<(count($attachment_array)-1))
      $attachment_display.="<tr>";
    }
    else
    {

    }

}
}

?>
<form method="post" name="knowledgebase" action="?id=<?=$id?>" enctype='multipart/form-data'>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Update To Do List Job</td>
                    <td align="right">
                        <i class="icon-remove grey btn-close-modal"></i>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php
if(count($errors)) {
?>
    <tr>
        <td><?=AlertUtil::generate($errors)?></td>
    </tr>
<?php
}
?>
    <tr>
        <td class="infocontainernopadding">
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Name:</b>&nbsp;<span class="red">*</span>

                    </td>
                    <td class="listrownoborder">
                        <input type="hidden" id="todolist_job_id" name="todolist_job_id" value="<?=$todolist_job_id?>" />
                        <input type="text" name="name" id="name" value="<?=$name?>">
                    </td>
                </tr>

                 <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Type:</b>&nbsp;<span class="red">*</span>

                    </td>
                    <td class="listrownoborder">
                        <?php
                        $sql = "SELECT todolist_id, todolist_name from tbl_todolist
                              where account_id='{$_SESSION['ao_accountid']}' order by order_num asc";
                        $todolist = DBUtil::queryToArray($sql);                        
                        ?>
                        <select name="type" id="type">
                          <option value="">Choose Later</option>
                        <?php
                        foreach($todolist as $row)
                        {
                        ?>
                          <option value="<?= $row['todolist_id'] ?>" <?php echo ($row['todolist_id']==$todolist_id)?'selected="selected"':''?>><?=$row['todolist_name']?></option>
                        <?php
                        }
                        ?>
                      </select>
                    </td>
                </tr>

                <tr>
                    <td class="listitem">
                        <b>Color:</b>&nbsp;<span class="red">*</span>
                    </td>
                    <td class="listrow">
                        <input name="job_color" class="form-control sm color {hash:true}" value="<?=$color?>" />
                    </td>
                </tr>
               
                <tr valign="top">
                    <td class="listitem">
                        <b>Description:</b>
                    </td>
                    <td class="listrow">
                        <textarea name="description" id="description" style="width:100%;" rows="4">
                          
                          <?=$description?>
                        </textarea>
                    </td>
                </tr>


                <!--<tr>
                <td width="25%" class="listitemnoborder">
                        <b>Attachment</b>&nbsp;<span class="red">*</span>
                    </td>
                <td >
                <table id="attachment-container" name="attachment-container" width="50%">
                <tr>                   
                    <td class="listrownoborder">
                        <input width="25%" type="file" name="myFile[]">                        
                    </td>
                    <td>&nbsp;&nbsp;
                    <input type="text" name="myFileDescription[]">
                    </td>
                    </tr>
                    </table>
                    </td>

                </tr>
                <tr>
                   <td width="25%" class="listitemnoborder">
             
                    <input type="button"  onclick="add_attachment_field()" value="Add Attachment" ></input>
                   </td>
                   <td class="listrownoborder">
                   </td>
                </tr>-->

                <tr>
                    <td align="right" colspan="2" class="listrow">
                        <input type="hidden" id="attachment" name="attachment" value="<?=$attachment?>" />
                        <input type="hidden" id="file_descriptions" name="file_descriptions" value="<?=$file_descriptions?>" />

                        <input name="submit" type="submit" value="Save">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>

</body>
</html>
