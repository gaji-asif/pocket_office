<?php
include '../common_lib.php';
?>
<?php
$id = $_REQUEST['id'];

$sql = "delete from share_url  where id = '".$id."'"; //exit;
DBUtil::query($sql);
?>

<table width="100%" border="0" cellspacing="3" cellpadding="3">
  <tr>
    <td width="10%"><strong>ID</strong></td>
    <td width="26%"><strong>Event Name</strong></td>
    <td width="35%"><strong>URL</strong></td>
    <!--<td><strong>Share By</strong></td>-->
    <td width="15%"><strong>Status</strong></td>
    <td width="14%"><strong>Action</strong></td>
  </tr>
<?php
foreach(getShareUrl() as $result){
	
	?>

     <tr >
        <td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php echo $result['id']; ?></td>
        <td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php echo $result['event_name']; ?></td>
 
        <td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php echo substr($result['url'], 0, 40); ?></td>
        <!--<td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php echo $result['fname'].' '.$result['lname']; ?></td>-->
        <td onclick="Request.make('<?=AJAX_DIR?>/share_url_edit_form.php?id=<?php echo $result['id']; ?>', 'editForm', true, true);"><?php if($result['status'] == 'y'){ echo 'Enable'; } else { echo 'Disable'; } ?></td>
        
         <td><a  onclick="getDeleteData('<?php echo $result['id']; ?>')" href="Javascript:void(0)">[Delete]</a></td>
         
<!--        <td><a  onclick="Request.make('<?=AJAX_DIR?>/share_url_delete_form.php?id=<?php echo $result['id']; ?>', 'deleteRow', true, true);" href="Javascript:void(0)">[Delete]</a></td>-->

    </tr>
    <?php
	}
?>
   <tr>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <td>&nbsp;</td>
    <!--<td>&nbsp;</td>-->
    <td>&nbsp;</td>
    <td>&nbsp;</td>
  </tr>
</table>

<script>
function getDeleteData(id){
	
	var result = confirm("Want to delete?");
	if (result) {
		
		Request.make('<?=AJAX_DIR?>/share_url_delete_form.php?id='+id, 'deleteRow', true, true);	
		
	}
	
}
</script>
