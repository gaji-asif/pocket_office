<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$id = RequestUtil::get('id');
$contact_header = DBUtil::getRecord('contacts');
$name = RequestUtil::get('contact_name');

if($id && !count($contact_header)) {
    UIUtil::showModalError('Could not retrieve contact header data');
}

$errors = array();
if(RequestUtil::get('submit')) {
    if(empty($name)) {
		$errors[] = 'Required fields missing.';
    }
    
    if(!count($errors)) {
        //update
        if(count($contact_header)) {
            $sql = "UPDATE contacts set contact_name = '$name' WHERE contact_header_id='$id'";
            DBUtil::query($sql);
        }
        //new
        else {
            $sql = "INSERT INTO contacts (account_id, contact_name) VALUES ('{$_SESSION['ao_accountid']}', '$name')";
            DBUtil::query($sql);
        }
?>
<script>
    parent.window.location.href = '/workflow/contact-header-list.php';
</script>
<?php
        die();
    }
}
?>

<form method="post" name="contact_header_id" action="?id=<?=$id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td><?=count($provider) ? 'Edit' : 'Add'?> Contact Header</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td class="listitem" width="25%">
            <b>Contact Type:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input name="contact_name" type="text" class="form-control" style="with:100%;" value="<?=MapUtil::get($contact_header, 'contact_name')?>" />
        </td>
    </tr>

    
    <tr>
        <td colspan="2" align="right" class="listrow">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>