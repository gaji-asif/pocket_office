<?php
include '../../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);
$ss="";
$name = RequestUtil::get('name');
$codegenerator_id = RequestUtil::get('type');
$description = RequestUtil::get('description');
$attachment_file_names="";
$attachment_file_description="";
$id = RequestUtil::get('id');

$errors = array();
if(RequestUtil::get("submit")) 
{
    if(empty($name)) 
    {
        $errors[] = 'Code generator Job name cannot be blank';
    }
    
    if(!count($errors)) 
    {
      /* attachment uploads*/
      for($i=0;$i<count($_FILES['myFile']['name']);$i++)
      {                    
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
             
            if(!empty($attachment_file_names))
                $attachment_file_names.="##".$new_filename;
            else
                $attachment_file_names.=$new_filename; 

            if(move_uploaded_file($_FILES["myFile"]["tmp_name"][$i], $new_path))
            {
              //Done
            }
        }

      }
    
      echo $sql = "INSERT INTO tbl_codegenerator_job (account_id,name,codegenerator_id,description,attachment,attachment_desc, created_by,created_at,is_deleted)
              VALUES ('{$_SESSION['ao_accountid']}','$name', '$codegenerator_id','$description','$attachment_file_names', 
        '$attachment_file_description','{$_SESSION['ao_userid']}',NOW(),'0')";
      
      DBUtil::query($sql);
      ?>

      <script>
        $(document).ready(function()
        {
            try{
               var opener = window.parent;
               opener.location.href="<?=ROOT_DIR?>/code-generator-job.php?id=<?=$id?>";
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
    s+="</td></tr></table></td></tr>";
	$("#attachment-container").append(s);
}
</script>
<!--<?=$ss?>-->
<form method="post" name="knowledgebase" action="?id=<?=$id?>" enctype='multipart/form-data'>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Add Code Generator Job</td>
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
                        <input type="text" name="name" id="name">
                    </td>
                </tr>

                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Type:</b>
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
                          <option value="<?= $row['codegenerator_id'] ?>"><?=$row['codegenerator_name']?></option>
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
                        <textarea name="description" id="description" style="width:100%;" rows="4"></textarea>
                    </td>
                </tr>

              <!--  <tr>
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
                        <input name="submit" type="submit" value="Submit">
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
