<?php
include '../../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);
$ss="";

$codegenerator_job_id=RequestUtil::get('codegenerator_job_id');
$name = RequestUtil::get('name');
$codegenerator_id = RequestUtil::get('type');
$description = RequestUtil::get('description');
$attachment_name=RequestUtil::get('attachment_name');
$delete_flag=RequestUtil::get('delete_flag');
$file_desc=RequestUtil::get('file_desc');
$id = RequestUtil::get('id');
if(!empty($delete_flag))
{
    $sql = "select attachment,attachment_desc from tbl_codegenerator_job where tbl_codegenerator_job_id=".$codegenerator_job_id;
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
  
    $sql = "update tbl_codegenerator_job set attachment='". $new_attachment."',attachment_desc='". $new_file_descriptions."' where tbl_codegenerator_job_id=".$codegenerator_job_id;

    DBUtil::query($sql);  
}
if(!empty($attachment_name) && empty($delete_flag))
{
    $sql = "select attachment,attachment_desc from tbl_codegenerator_job where tbl_codegenerator_job_id=".$codegenerator_job_id;
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
  
    $sql = "update tbl_codegenerator_job set attachment='". $new_attachment."',attachment_desc='". $new_file_descriptions."' where tbl_codegenerator_job_id=".$codegenerator_job_id;
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
      /* attachment uploads*/
      for($i=0;$i<count($_FILES['myFile']['name']);$i++)
      {
          $pieces = explode('.',$_FILES["myFile"]["name"][$i]);
    
          $file_ext = strtoupper($pieces[sizeof($pieces)-1]);

          switch($file_ext)
          {
              case 'ZIP':
              case 'RAR':
                $type = "archive";
              break;
              case 'JPG':
              case 'PNG':
              case 'GIF':
              case 'BMP':
                $type = "image";
              break;
              case 'PDF':
                $type = "pdf";
              break;
              case "PPTX":
              case "PPT":
               $type = "powerpoint";
              break;
              case "DOCX":
              case "DOC":
               $type = "word";
              break;
              case "XLSX":
              case "XLS":
                $type = "excel";
              break;
              default:
              $type = "unknown";
          }

          $new_filename = time().".".$pieces[sizeof($pieces)-1];
          $new_path = DOCUMENTS_PATH. '/codegenerator/' . $new_filename;

          if(!empty($pieces[0]))
          {          
               $filedescription=$_REQUEST['myFileDescription'][$i];
               if(empty($filedescription)) $filedescription="N/A";
               if(!empty($attachment_file_description))
                 $attachment_file_description.="##".$filedescription;
               else
                 $attachment_file_description.=$filedescription;

              if(!empty($attachment_attachment))
                $attachment_attachment.="##".$new_filename;
              else
                 $attachment_attachment.=$new_filename; 

              if(move_uploaded_file($_FILES["myFile"]["tmp_name"][$i], $new_path))
              {
                //echo "uploaded";
              }
          } 
      }
      if(!count($errors)) 
      {
          $sql = "update tbl_codegenerator_job set name='".$name."',codegenerator_id=".$codegenerator_id.", description='".$description."',attachment='".$attachment_attachment."',attachment_desc='".$attachment_file_description."'  where tbl_codegenerator_job_id=".$codegenerator_job_id;
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
if(RequestUtil::get("codegenerator_job_id")) 
{
$codegenerator_job_id = RequestUtil::get('codegenerator_job_id');

$sql = "select tbl_codegenerator_job_id,codegenerator_id,name,description,attachment,attachment_desc from tbl_codegenerator_job where tbl_codegenerator_job_id=".$codegenerator_job_id;
//echo $sql;
$res = DBUtil::query($sql);


//$res = DBUtil::query($sql);
list($codegenerator_job_id, $codegenerator_id,$name,$description,$attachment,$file_descriptions)=mysqli_fetch_row($res);
$file_dir=DOCUMENTS_PATH. '/codegenerator/' ;
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
                    <td>Update Code Generator Job</td>
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
                        <input type="hidden" id="codegenerator_job_id" name="codegenerator_job_id" value="<?=$codegenerator_job_id?>" />
                        <input type="text" name="name" id="name" value="<?=$name?>">
                    </td>
                </tr>

                 <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Type:</b>&nbsp;<span class="red">*</span>

                    </td>
                    <td class="listrownoborder">
                        <?php
                        $sql = "SELECT codegenerator_id, codegenerator_name from tbl_codegenerator
                              where account_id='{$_SESSION['ao_accountid']}' order by order_num asc";
                        $codegenerator = DBUtil::queryToArray($sql);                        
                        ?>
                        <select name="type" id="type">
                          <option value="">Choose Later</option>
                        <?php
                        foreach($codegenerator as $row)
                        {
                        ?>
                          <option value="<?= $row['codegenerator_id'] ?>" <?php echo ($row['codegenerator_id']==$codegenerator_id)?'selected="selected"':''?>><?=$row['codegenerator_name']?></option>
                        <?php
                        }
                        ?>
                      </select>
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
<?php
$attachment_array=explode("##",$attachment);
$file_descriptions_array=explode("##",$file_descriptions);
$display_string="";
for($i=0;$i<count($attachment_array);$i++)
{
    if(!empty($attachment_array[$i]))
    {
      $j=($i+1)%1;
      if(($i==0||$j==0))
      {       
         if($j==0)
            $display_string.="</tr><tr>";
         else
            $display_string.="<tr>";
      }
     // echo "hh=".strpos($attachment_array[$i],"_");
      $file_name=$attachment_array[$i];
      $file_description=$file_descriptions_array[$i]=="N/A"?"":$file_descriptions_array[$i];
      $file_display_name=$file_name;
      $display_string.="<td width='10%'>".($i+1).".</td><td>".$file_display_name."</td><td><input id='file_description_".$i."' name='file_description_".$i."' type='text' value='".$file_description."'></input></td><td><a title='Update attachment description' href='#' onclick='save_attachment(\"".$codegenerator_job_id."\",\"". $file_name."\",\"".$i."\")'><i class='icon-save'  > </i></a>";
      $display_string.="&nbsp;&nbsp&nbsp;&nbsp;<a href='javascript:if(confirm(\"Are you sure?\")) window.location.href=\"".AJAX_DIR."/codegenerator_job/edit_job.php?id=<?=$id?>&codegenerator_job_id=".$codegenerator_job_id."&attachment_name=".$file_name."&delete_flag=1\";' title=\"Delete attachment\"><i class=\"icon-trash\" style=\"color:red\"> </i></a></td>";
      
      if($i==(count($attachment_array)-1))
      $display_string.="</tr>";
    }
}
if(!empty($display_string)){
?>
<!--<table width="40%" style="font-size: 15px;">
    <thead><tr><th colspan="3">Attachments</th></tr></thead>
    <?=$display_string?>
</table> -->
<?php }?>


<script>
function save_attachment(kid,att_name,id)
{
 
    try{
   
   var file_desc_textbox='file_description_'+id;
   var file_description=document.getElementById(''+file_desc_textbox).value;
  
    window.location.href="edit_job.php?codegenerator_job_id="+kid+"&attachment_name="+att_name+"&file_desc="+file_description;
    }catch(e){alert(e);}
}

function delete_attachment()
{
    
    
}
</script>
</body>
</html>
