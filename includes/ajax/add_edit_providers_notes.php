<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$action = RequestUtil::get('action');
$id = RequestUtil::get('id');
$note = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('note'));
$note_id = RequestUtil::get('note_id');
$errors = array();
$provider = DBUtil::getRecord('insurance');

$sql="select * from insurance_notes where insurance_note_id='$note_id' order by insurance_note_id desc";
$note_arr = DBUtil::queryToArray($sql);
if(RequestUtil::get('submit')) {
    if(empty($note)) {
		$errors[] = 'Required fields missing.';
    }
    
    if(!count($errors)) {
        if(count($note_arr)>0)
        {
            $sql = "UPDATE insurance_notes SET notes='$note'  WHERE insurance_note_id='$note_id'";
            DBUtil::query($sql);
        }
        else
        {
            $sql = "INSERT INTO insurance_notes (insurance_id, notes,created_at)
                VALUES ('$id','$note',NOW())";
            DBUtil::query($sql);
        }
            
    }
}
if($action=='del')
{
    $sql = "DELETE FROM insurance_notes  WHERE insurance_note_id='$note_id'";
    DBUtil::query($sql);
}
if($action=='edit')
{
    if(count($note_arr)==0)
    {
        $errors[] = 'Record not found.';
    }
}
?>

<form method="post" name="provider" action="?id=<?=$id?>">
<input name="note_id" type="hidden" value="<?= $note_id ?>" />
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Edit/Add Insurance Note</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td class="listitem" width="25%">
            <b>Provider Name:</b>
        </td>
        <td class="listrow">
            <input disabled name="insurance" type="text" class="form-control" value="<?=MapUtil::get($provider, 'insurance')?>" />
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Note:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <textarea rows="4" cols="6" name="note" class="form-control"> <?=$note_arr[0]['notes']?> </textarea>
        </td>
    </tr>
    
    <tr>
        <td colspan="2" align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
<br>
<br>

<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Insurance Notes</td>
    </tr>
</table>

    <table class="table-bordered table-condensed table-striped" width="100%">
        <thead>
            <tr>
                <th>#</th>
                <th>Note</th>
                <th>Date</th>
                <th width="10%" class="text-center">Actions</th>
            </tr>

        </thead>

        <tbody>

<?php

$sql="select * from insurance_notes where insurance_id='$id' order by insurance_note_id desc";
$notes = DBUtil::queryToArray($sql);
$i=1;
if(count($notes)>0)
{
foreach($notes as $row) {
?>
    <tr>
        <td><?=$i?></td>
        <td ><?=$row['notes']?></td>
        <td><?=$row['created_at']?></td>

        <td class="text-center">
            
            <a href="?id=<?=$id?>&action=del&note_id=<?=$row['insurance_note_id']?>" class="btn btn-small btn-danger" data-confirm="Are you sure you want to remove insurance Note?" title="Delete" tooltip>
                <i class="icon-trash"></i>
            </a>

            <a href="?id=<?=$id?>&action=edit&note_id=<?=$row['insurance_note_id']?>" class="btn btn-small"  title="Edit" tooltip>
                <i class="icon-pencil"></i>
            </a> 

        </td>

    </tr>

<?php

  $i++;

}
}
else
{
?>

<tr>
        
        <td class="text-center" colspan="4">
            No Notes Found!
            

        </td>

    </tr>

<?php
}
?>

        </tbody>

    </table>

</body>
</html>