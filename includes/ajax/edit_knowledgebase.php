<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);
$ss="";
$knowledgebase_name = RequestUtil::get('knowledgebase_name');
$description = RequestUtil::get('description');
$knowledgebase_tag=RequestUtil::get('knowledgebase_tag');
$knowledgebase_id=RequestUtil::get('knowledgebase_id');

$attachment_name=RequestUtil::get('attachment_name');
$attachment_name = mysqli_real_escape_string(DBUtil::Dbcont(),$attachment_name);

$delete_flag=RequestUtil::get('delete_flag');

$file_desc=RequestUtil::get('file_desc');
$file_desc = mysqli_real_escape_string(DBUtil::Dbcont(),$file_desc);

if(!empty($delete_flag))
{
    $sql = "select file_names,file_descriptions from knowledgebase where knowledgebase_id=".$knowledgebase_id;
    $res = DBUtil::query($sql);
    list($file_names,$file_descriptions)=mysqli_fetch_row($res);
    $file_names_array=explode("##",$file_names);
    $file_descriptions_array=explode("##",$file_descriptions);
    $new_file_names="";
    $new_file_descriptions="";
    
    for($i=0;$i<count($file_names_array);$i++)
    {
         if($attachment_name==$file_names_array[$i])  
         {
             continue;
         }
         else
         {
             $new_file_names.=$file_names_array[$i]."##";
            $new_file_descriptions.=$file_descriptions_array[$i]."##";
            
             
         }
        
    }
    $new_file_names=mysqli_real_escape_string(DBUtil::Dbcont(),trim($new_file_names,"##"));
    $new_file_descriptions=mysqli_real_escape_string(DBUtil::Dbcont(),trim($new_file_descriptions,"##"));
  
    $sql = "update knowledgebase set file_names='". $new_file_names."',file_descriptions='". $new_file_descriptions."' where knowledgebase_id=".$knowledgebase_id;
    DBUtil::query($sql);  
           
}

if(!empty($attachment_name)&&empty($delete_flag))
{
    $sql = "select file_names,file_descriptions from knowledgebase where knowledgebase_id=".$knowledgebase_id;
    $res = DBUtil::query($sql);
    list($file_names,$file_descriptions)=mysqli_fetch_row($res);
    $file_names_array=explode("##",$file_names);
    $file_descriptions_array=explode("##",$file_descriptions);
    $new_file_names="";
    $new_file_descriptions="";
    $j=0;
    for($i=0;$i<count($file_names_array);$i++)
    {
        if($attachment_name==$file_names_array[$i])  
        {
             $file_descriptions_array[$i]=trim($file_desc)==""?"N/A":$file_desc;
        }
        
        $new_file_names.=$file_names_array[$i]."##";
        $new_file_descriptions.=$file_descriptions_array[$i]."##";
     
        
    }
    $new_file_names=mysqli_real_escape_string(DBUtil::Dbcont(),trim($new_file_names,"##"));
    $new_file_descriptions=mysqli_real_escape_string(DBUtil::Dbcont(),trim($new_file_descriptions,"##"));

  
   $sql = "update knowledgebase set file_names='". $new_file_names."',file_descriptions='". $new_file_descriptions."' where knowledgebase_id=".$knowledgebase_id;
           DBUtil::query($sql); 
  
           
}
$errors = array();
if(RequestUtil::get("submit"))
{
     $attachment_file_names=mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('file_names'));
     $attachment_file_description=mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('file_descriptions'));

    if(empty($knowledgebase_name))
      {
        $errors[] = 'Knowledge Base name cannot be blank';
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

           $new_filename = mt_rand().time()."_".$pieces[0].".".$pieces[sizeof($pieces)-1];
           $new_path = DOCUMENTS_PATH. '/Knowledgebase/' . $new_filename;

        if(!empty($pieces[0]))
        {
           
           $filedescription=$_REQUEST['myFileDescription'][$i];
         if(empty($filedescription)) $filedescription="N/A";
         if(!empty($attachment_file_description))
           $attachment_file_description.="##".$filedescription;
         else
           $attachment_file_description.=$filedescription;

           if(!empty($attachment_file_names))
             $attachment_file_names.="##".$new_filename;
           else
             $attachment_file_names.=$new_filename; 

           if(move_uploaded_file($_FILES["myFile"]["tmp_name"][$i], $new_path))
              {
                echo "uploaded";
              }
         } 
         }



    if(!count($errors)) {
        $sql = "update knowledgebase set 
        
        knowledgebase_name='".addslashes($knowledgebase_name)."', 
        info='".addslashes($description)."', 
        search_tag='".addslashes($knowledgebase_tag)."',
        file_names='".$attachment_file_names."',
        file_descriptions='".$attachment_file_description."' 
        
        where knowledgebase_id=".$knowledgebase_id;
           DBUtil::query($sql);
?>

<!-- <script>
    window.location = 'edit_knowledgebase.php?id=<?=DBUtil::getInsertId()?>';
</script> -->

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
    s+="</td><td>&nbsp;&nbsp;<input type='text' name='myFileDescription[]'>";
    s+="</td></tr></table></td></tr>";
  $("#attachment-container").append(s);
}
</script>
<!--<?=$ss?>-->

<?php
if(RequestUtil::get("knowledgebase_id")) 
{
$knowledgebase_id = RequestUtil::get('knowledgebase_id');

$sql = "select knowledgebase_id,knowledgebase_name,info,search_tag,file_names,file_descriptions from knowledgebase where knowledgebase_id=".$knowledgebase_id;
//echo $sql;
$res = DBUtil::query($sql);


//$res = DBUtil::query($sql);
list($knowledgebase_id, $knowledgebase_name,$info,$search_tag,$file_names,$file_descriptions)=mysqli_fetch_row($res);
$file_dir=DOCUMENTS_PATH. '/Knowledgebase/' ;
$file_names_array=explode("##",$file_names);
$file_descriptions_array=explode("##",$file_descriptions);
$attachment_display="<tr>";
for($i=0;$i<count($file_names_array);$i++)
{
  $attachment_display.="<td><a href='".$file_dir.$file_names_array[$i]."'>".$file_names_array[$i]."</a>
  <br>".$file_descriptions_array[$i]."</td>";
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
<form method="post" name="knowledgebase" action="?" enctype='multipart/form-data'>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Update Knowledge Base</td>
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
                        <b>Knowledge Base Name:</b>&nbsp;<span class="red">*</span>

                    </td>
                    <td class="listrownoborder">
                        <input type="hidden" id="knowledgebase_id" name="knowledgebase_id" value="<?=$knowledgebase_id?>" />
                        <input type="text" name="knowledgebase_name" id="knowledgebase_name" value="<?=$knowledgebase_name?>">
                    </td>
                </tr>

               
                <tr valign="top">
                    <td class="listitem">
                        <b>Description:</b>
                    </td>
                    <td class="listrow">
                        <textarea name="description" id="description" style="width:100%;" rows="4">
                          
                          <?=$info?>
                        </textarea>
                    </td>
                </tr>

                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Tag:</b>&nbsp;<span class="red">*</span>
                    </td>
                    <td class="listrownoborder">
                        <input type="text" name="knowledgebase_tag" id="knowledgebase_tag" value="<?=$search_tag?>">
                    </td>
                </tr>

                <tr>
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
                </tr>

                <tr>
                    <td align="right" colspan="2" class="listrow">
                        <input type="hidden" id="file_names" name="file_names" value="<?=$file_names?>" />
                        <input type="hidden" id="file_descriptions" name="file_descriptions" value="<?=$file_descriptions?>" />

                        <input name="submit" type="submit" value="submit and close">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
<?php
$file_names_array=explode("##",$file_names);
$file_descriptions_array=explode("##",$file_descriptions);

$display_string="";
for($i=0;$i<count($file_names_array);$i++)
{
    //$display_string="";
    $j=($i+1)%3;
    if(($i==0||$j==0))
    {
       
       if($j==0)
       $display_string.="</tr><tr>";
       else
       $display_string.="<tr>";
    }
   // echo "hh=".strpos($file_names_array[$i],"_");
    $file_name=$file_names_array[$i];
    $file_description=$file_descriptions_array[$i]=="N/A"?"":$file_descriptions_array[$i];
    $file_display_name=substr($file_name,strpos($file_name,"_")+1);
    
    $file_description = stripslashes($file_description);
    $file_display_name = stripslashes($file_display_name);
     
    $display_string.="<td>".$file_display_name."</td><td><input id='file_description_".$i."' name='file_description_".$i."' type='text' value=";
    $display_string.='"'.$file_description.'"';
    $display_string.="></input></td><td><a title='Update attachment description' href='#' onclick='save_attachment(\"".$knowledgebase_id."\",\"". $file_name."\",\"".$i."\")'><i class='icon-save'  > </i></a></td>";
    $display_string.="<td><a href='javascript:if(confirm(\"Are you sure?\")) window.location.href=\"".AJAX_DIR."/edit_knowledgebase.php?knowledgebase_id=".$knowledgebase_id."&attachment_name=".$file_name."&delete_flag=1\";' title=\"Delete attachment\"><i class=\"icon-trash\" style=\"color:red\"> </i></a></td>";
    $display_string.="<td>&nbsp;&nbsp&nbsp;&nbsp;</td>";
    
    if($i==(count($file_names_array)-1))
    $display_string.="</tr>";
    
    
    
}
?>
<table><caption><b>Attachments</b></caption>
    <?=$display_string?>
</table>

<script>
function save_attachment(kid,att_name,id)
{
 
    try{
   
   var file_desc_textbox='file_description_'+id;
   var file_description=document.getElementById(''+file_desc_textbox).value;
  
    window.location.href="edit_knowledgebase.php?knowledgebase_id="+kid+"&attachment_name="+att_name+"&file_desc="+file_description;
    }catch(e){alert(e);}
}

function delete_attachment()
{
    
    
}
</script>

</body>
</html>
