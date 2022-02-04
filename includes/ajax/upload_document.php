<?php

set_time_limit (0);

include '../common_lib.php';
if(!ModuleUtil::checkAccess('upload_document'))
  die('Insufficient Rights');

echo ViewUtil::loadView('doc-head');

if(isset($_POST["submit"]))
{
  set_time_limit(300);

  $title = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['title']);
  $desc = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['description']);
  $stage = intval($_POST['stage']);
  $group = intval($_POST['document_group']);

  if($title==''||$desc=='')
    $error_msg = '<br />Must enter all required fields';
  else
  {
    $pieces = explode('.',$_FILES["document"]["name"]);

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

    $new_filename = mt_rand().time().".".$pieces[sizeof($pieces)-1];
    $new_path = DOCUMENTS_PATH . '/' . $new_filename;
    //echo $_FILES["document"]["tmp_name"];
    //print_r($_FILES);exit;
    if(move_uploaded_file($_FILES["document"]["tmp_name"], $new_path))
    {
      $sql = "insert into documents values(0, '".$_SESSION['ao_accountid']."', '".$title."', '".$desc."', '".$new_filename."', '".$type."', '".$_SESSION['ao_userid']."', '".$stage."',  now())";
      DBUtil::query($sql);

      if(!empty($group))
      {
          $last_insert_id = DBUtil::getInsertId();
          $sql = "insert into document_group_link (document_id, document_group_id)
                  values($last_insert_id, $group)";
          DBUtil::query($sql);
      }


?>

<script>

  Request.makeModal('<?=AJAX_DIR?>/get_documentlist.php', 'documentscontainer', true, true, true);
  //setTimeout("window.close()", "1000");

</script>
<?php
    }
    else
    {
      switch ($_FILES['document']['error'])
      {
      case 1:
        die('<p> The file is bigger than this PHP installation allows</p>');
        break;
      case 2:
        die('<p> The file is bigger than this form allows</p>');
        break;
      case 3:
        die('<p> Only part of the file was uploaded</p>');
        break;
      case 4:
        die('<p> No file was uploaded</p>');
        break;
      }
    }
  }
}

?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Upload Document
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
<?php
if(isset($error_msg) && $error_msg!='')
{
?>
            <tr>
              <td colspan=2 style='color: red; font-size: 11px;' class="listrownoborder">
                <b>Errors Found!</b>
                <?php echo $error_msg; ?>
              </td>
            </tr>
<?php
}
?>
            <tr>
              <td class="listitemnoborder">
                <form enctype='multipart/form-data' action='?' method="post">
                <b>File:</b>
              </td>
              <td>
                <input type='file' name='document'>
              </td>
            </tr>
            <tr>
              <td width="25%" class="listitem"><b>Title:</b>&nbsp;<span class="red">*</span></td>
              <td class="listrow">
                <input type="text" name='title' size=30>
              </td>
            </tr>
<?php
$documentGroups = DocumentModel::getAllDocumentGroups();
if(!empty($documentGroups))
{
?>
            <tr>
              <td width="25%" class="listitem"><b>Group:</b></td>
              <td class="listrow">
                <select name="document_group">
                    <option value=""></option>
<?php
    foreach($documentGroups as $group)
    {
?>
                    <option value="<?=$group['document_group_id']?>"><?=$group['label']?></option>
<?php
    }
?>
                </select>
              </td>
            </tr>
<?php
}
?>
            <tr>
              <td class="listitem"><b>Stage:</b></td>
              <td class="listrow">
                <select name='stage'>
                  <option value=''></option>
<?php
$stages_array = StageModel::getAllStages();

foreach($stages_array as $stage)
{
?>
                  <option value='<?=$stage['stage_id']?>'><?=$stage['stage']?></option>
<?php
}
?>
                </select>
              </td>
            </tr>
            <tr valign='top'>
              <td class="listitem"><b>Description:</b>&nbsp;<span class="red">*</span></td>
              <td class="listrow">
                <textarea name='description' rows=5 style='width:100%;'></textarea>
            </tr>
            <tr>
              <td align="right" colspan=2 class="listrow">
                  <input type="submit" name="submit" value='Upload'>
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>
