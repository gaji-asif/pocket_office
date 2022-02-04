<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_documents'))
  die("Insufficient Rights");

if(moduleOwnership('view_documents'))
    $ownership = "(documents.user_id=".$_SESSION['ao_userid'].") and";

$sql = "select documents.document_id, documents.document, documents.filetype, documents.timestamp, users.fname, users.lname, documents.user_id".
       " from documents, users".
       " where ".$ownership." (documents.document like '%".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['search'])."%' || documents.description like '%".$_GET['search']."%') and documents.account_id='".$_SESSION['ao_accountid']."' and documents.user_id=users.user_id".
       " order by timestamp desc";

$res = DBUtil::query($sql);
$num_rows = mysqli_num_rows($res);

?>

<table width="100%" border="0" class="data-table" cellpadding=5 cellspacing="0">
  <tr>
    <td colspan=4>
      <b>Searching '<?php echo $_GET['search']; ?>' - <?php echo $num_rows ?> result(s) found</b>
    </td>
  </tr>
<?php
$i=1;
while(list($document_id, $document, $filetype, $date, $fname, $lname, $user_id)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';

  $date = DateUtil::formatDateTime($date);

  $icon='<img src="images/icons/'.$filetype.'.png">';
?>

  <tr class='<?php echo $class; ?>' valign='middle' onclick="window.location='documents.php?id=<?php echo $document_id; ?>';" onmouseover='hoverRow(this);' onmouseout='hoverRowOut(this, "<?php echo $class; ?>");'>
    <td width=16>
      <?php echo $icon; ?>
    </td>
    <td class='data-table-cell'>
      <b><?php echo $document; ?></b>
    </td>
    <td width=200 class='data-table-cell'>
      <?php echo $date; ?>
    </td>
    <td width=200 class='data-table-cell'>
      <?php echo $lname.", ".$fname; ?>
    </td>
  </tr>

<?php
  $i++;
}
?>
</table>