<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('modify_suppliers', TRUE);

$name = RequestUtil::get('name');
$contact = RequestUtil::get('contact');
$phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));
$email = RequestUtil::get('email');
$fax = StrUtil::formatPhoneToSave(RequestUtil::get('fax'));

$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($name) || empty($email) || empty($phone)){
        $errors[] = 'Required fields missing';
    } else {
        if(!ValidateUtil::validateEmail($email)) {
          $errors[] = 'Email incorrect format';
        }
        if(UserModel::emailExists($email)) {
          $errors[] = 'Email in use';
        }
        if(strlen($phone) != 10 || !ctype_digit($phone)) {
          $errors[] = 'Phone incorrect format';
        }
        if(!empty($fax) && (strlen($fax) != 10 || !ctype_digit($fax))) {
          $errors[] = 'Fax incorrect format';
        }
    }
    
    if(!count($errors)) {
        $sql = "INSERT INTO suppliers
                VALUES (NULL, '$name', '$contact', '$email', '$phone', '$fax')";
        DBUtil::query($sql);
        $sql = "INSERT INTO suppliers_link
                VALUES (NULL,'" . DBUtil::getInsertId() . "' , '{$_SESSION['ao_accountid']}')";
        DBUtil::query($sql);
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_suppliers.php', 'suppliers-list', true, true, true);
</script>
<?php
        die();
    }
}

?>
<form method="post" name="customer" action="?">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add New Supplier</td>
        <td align="right">
          <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder"><b>Supplier:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrownoborder"><input type="text" name="name" value="<?=$name?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Contact:</b></td>
        <td class="listrow"><input type="text" name="contact" value="<?=$contact?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Phone:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrow"><input type="text" class="masked-phone" name="phone" value="<?=$phone?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Fax:</b></td>
        <td class="listrow"><input type="text" class="masked-phone" name="fax" value='<?php echo $fax; ?>'></td>
    </tr>
    <tr>
        <td class="listitem"><b>Email:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrow"><input type="text" name="email" value='<?php echo $email; ?>'></td>
    </tr>
    <tr>
        <td align="right" class="listrow" colspan="2">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>