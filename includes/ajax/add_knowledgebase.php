<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);
$ss="";
$knowledgebase_name = addslashes(RequestUtil::get('knowledgebase_name'));
$description = addslashes(RequestUtil::get('description'));
$knowledgebase_tag= addslashes(RequestUtil::get('knowledgebase_tag'));
$attachment_file_names="";
$attachment_file_description="";


$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($knowledgebase_name)) {
        $errors[] = 'Knowledge base name cannot be blank';
    }
    else
    {
    $sql = "select knowledgebase_name from knowledgebase where knowledgebase_name like '".$knowledgebase_name."'  and delete_flag=0 order by knowledgebase_name asc";

$res = DBUtil::query($sql);

if(!mysqli_num_rows($res)==0)
{
    $errors[] = 'Knowledge base name exist';
}
}
   if(!count($errors)) {
    /* attachment uploads*/
    for($i=0;$i<count($_FILES['myFile']['name']);$i++){
        
         
    $pieces = explode('.',$_FILES["myFile"]["name"][$i]);
    //echo var_dump($pieces);

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
   // echo "uploaded";
    }
}

}



    
        $sql = "INSERT INTO knowledgebase 
                VALUES (NULL, '$knowledgebase_name', '$description', '$knowledgebase_tag','$attachment_file_names','$attachment_file_description', '{$_SESSION['ao_accountid']}',0)";
       DBUtil::query($sql);
?>

<script>
$(document).ready(function()
{
    try{
       var opener = window.parent;
       opener.location.href="<?=ROOT_DIR?>/knowledgebase.php";
     var closebutton = $('.btn-close-modal');   
     closebutton.trigger('click');
     
     
     }
     catch(e){alert(e);}
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
<form method="post" name="knowledgebase" action="?" enctype='multipart/form-data'>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Add Knowledge Base</td>
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
                        <input type="text" name="knowledgebase_name" id="knowledgebase_name">
                    </td>
                </tr>

               
                <tr valign="top">
                    <td class="listitem">
                        <b>Description:</b>
                    </td>
                    <td class="listrow">
                        <textarea name="description" id="description" style="width:100%;" rows="4"></textarea>
                    </td>
                </tr>

                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Tag:</b>&nbsp;<span class="red">*</span>
                    </td>
                    <td class="listrownoborder">
                        <input type="text" name="knowledgebase_tag" id="knowledgebase_tag">
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
                        <input name="submit" type="submit" value="Submit and Close">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
<script>

</script>

</body>
</html>
