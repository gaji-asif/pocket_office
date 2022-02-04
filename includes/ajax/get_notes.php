<?php
include '../common_lib.php';
UserModel::isAuthenticated();
$type = RequestUtil::get('type');
$id = RequestUtil::get('id');
$passed_note_id = RequestUtil::get('noteid');
$action = RequestUtil::get('action');

if($passed_note_id != '' && $action == 'del') {
  $sql = "delete from notes where note_id='$passed_note_id' and user_id='{$_SESSION['ao_userid']}' limit 1";
  DBUtil::query($sql);
}

if($id!=''&&$type!='')
{
  $sql = "select note_id, subject, global, user_id from notes where type='".$type."' and id='".$id."' and (user_id='".$_SESSION['ao_userid']."' || notes.global=1) order by timestamp desc";
  $res = DBUtil::query($sql);

?>
    <table border="0" cellpadding="0" cellspacing="0" width="100%" style='background-color: #F5F5F5; border-bottom: 1px solid #cccccc;'>
      <tr>
        <td width=20 align="center">
          <img src='<?=IMAGES_DIR?>/icons/clipboard_16.png'>
        </td>
        <td width=40>
          <b>Notepad:</b>
        </td>
        <td>
          <table border="0">
            <tr height=20>
<?php

  if(mysqli_num_rows($res)==0)
    echo "<td style='font-size: 11px; padding-right:5px;'><i>No Notes Found</i></td>";
$i=0;
  while(list($note_id, $subject, $global, $user_id)=mysqli_fetch_row($res))
  {
    $format_subject = substr(trim($subject), 0, 15);
    if(strlen($format_subject) < strlen(trim($subject)))
      $format_subject.="...";
?>
    <td align="right" style='padding-left: 4px; border-left: 1px solid #cccccc;'><img src='<?=IMAGES_DIR?>/icons/unknown.png'></td>
    <td>
      <a href="javascript:viewNote('<?php echo $note_id; ?>', '<?php echo $type; ?>', '<?php echo $id; ?>')" class='browsinglink' target='main'><?php echo $format_subject; ?></a>
<?php
    if($global!=1||$user_id==$_SESSION['ao_userid'])
    {
?>
      <a title='Delete Note' href="javascript:deleteNote('<?php echo $note_id; ?>', '<?php echo $type; ?>', '<?php echo $id; ?>')" class='browsingdellink' tooltip>x</a>
<?php
    }
?>
    </td>
<?php
    $i++;
  }


?>
              <td width=20 style='border-left: 1px solid #cccccc; padding-left: 4px;' align="center"><img src='<?=IMAGES_DIR?>/icons/add_16.png'></td>
              <td width="25%">
                <a title="Add New Note" href="" rel="open-modal" data-script="add_note.php?type=<?php echo $type; ?>&id=<?php echo $id; ?>" class="basiclink" tooltip>
                  Add Note
                </a>
              </td>
            </tr>
          </table>
        </td>
        <td></td>
      </tr>
<?php

  if($passed_note_id!=''&&$action=='view')
  {
    $sql = "select subject, note, date_format(timestamp,'%m-%d-%Y (%l:%i%p)'), users.fname, users.lname, notes.user_id, notes.global from notes, users where users.user_id=notes.user_id and notes.note_id='".$passed_note_id."' and (notes.user_id='".$_SESSION['ao_userid']."' || notes.global=1) limit 1";
    $res = DBUtil::query($sql);

    if(mysqli_num_rows($res)!=0)
    {
      list($subject, $note, $timestamp, $fname, $lname, $user_id, $global)=mysqli_fetch_row($res);
      $subject = prepareText($subject);
      $note = prepareText($note);

      if($global==1)
        $global_str = 'Yes';
      else $global_str = 'No'
?>

      <tr><td colspan=5>&nbsp;</td></tr>
      <tr>
        <td colspan=5 >
          <table class='listtable' style='background-color: #ffffff; margin-left: 10px;' width='600 ' border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td width="25%" class="listitemnoborder">
                <b>Subject:</b>
              </td>
              <td class="listrownoborder"><?php echo $subject; ?></td>
            </tr>
            <tr>
              <td width="25%" class="listitem">
                <b>Author:</b>
              </td>
              <td class="listrow"><a href="users.php?id=<?=$user_id ?>" tooltip><?php echo $lname.", ".$fname; ?></a></td>
            </tr>
            <tr>
              <td width="25%" class="listitem">
                <b>Added:</b>
              </td>
              <td class="listrow"><?php echo $timestamp; ?></td>
            </tr>
            <tr>
              <td width="25%" class="listitem">
                <b>Global:</b>
              </td>
              <td class="listrow"><?php echo $global_str; ?></td>
            </tr>
            <tr valign='top'>
              <td width="25%" class="listitem">
                <b>Note:</b>
              </td>
              <td class="listrow"><?php echo $note; ?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td colspan=5 >
          <table style='margin-left: 10px;' width='600 ' border="0" cellpadding="0" cellspacing="0">
            <tr><td colspan=5 align="right"><a href="javascript: Request.make('<?=AJAX_DIR?>/get_notes.php?id=<?php echo $id; ?>&type=<?php echo $type; ?>', 'notes', '', true);" class='basiclink'>Close</a></td></tr>
          </table>
        </td>
      </tr>
      <tr><td colspan=5>&nbsp;</td></tr>
<?php


    }
  }
?>
    </table>
<?php
}

?>
