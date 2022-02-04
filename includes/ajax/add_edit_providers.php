<?php
include '../common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$id = RequestUtil::get('id');

$provider = DBUtil::getRecord('insurance');
//print_r($provider);

$name = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('insurance'));
$phone_no = RequestUtil::get('phone_no');
$fax_no = RequestUtil::get('fax_no');
$email = RequestUtil::get('email');
$commment = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('commment'));

if($id && !count($provider)) {
    UIUtil::showModalError('Could not retrieve provider data');
}

$autoCreateTasks = TaskUtil::getAutoCreateTasks($id);

$errors = array();
if(RequestUtil::get('submit')) {
    if(empty($name)) {
		$errors[] = 'Required fields missing.';
    }
    
    if(!count($errors)) {
        //update
        if(count($provider)) {
           // print_r($provider); die("p");
            $_POST['insurance']=$name;
            $_POST['commment']=$commment;
            FormUtil::update();
            TaskUtil::updateAutoCreateTasks($id, $autoCreateTasks);
        }
        //new
        else {
            $sql = "INSERT INTO insurance (account_id, insurance, phone_no, fax_no, email, commment)

                VALUES ('{$_SESSION['ao_accountid']}', '$name', '$phone_no', '$fax_no', '$email', '$commment')";

        DBUtil::query($sql);
        }
?>
<script>
    parent.window.location.href = '/workflow/providers.php';
</script>
<?php
        die();
    }
}
?>

<form method="post" name="provider" action="?id=<?=$id?>">
<input name="table" type="hidden" value="insurance" />
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td><?=count($provider) ? 'Edit' : 'Add'?> Insurance Provider</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td class="listitem" width="25%">
            <b>Provider Name:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input name="insurance" type="text" class="form-control" value="<?=MapUtil::get($provider, 'insurance')?>" />
        </td>
    </tr>


    <tr>
        <td class="listitem" width="25%">
            <b>Phone No:</b>
        </td>
        <td class="listrow">
            <input name="phone_no" type="text" class="masked-phone" value="<?=MapUtil::get($provider, 'phone_no')?>" />
        </td>
    </tr>

    <tr>
        <td class="listitem" width="25%">
            <b>Fax No:</b>
        </td>
        <td class="listrow">
            <input name="fax_no" type="text" class="masked-phone" value="<?=MapUtil::get($provider, 'fax_no')?>" />
        </td>
    </tr>

    <tr>
        <td class="listitem" width="25%">
            <b>Email Address:</b>
        </td>
        <td class="listrow">
            <input name="email" type="text" class="form-control" value="<?=MapUtil::get($provider, 'email')?>" />
        </td>
    </tr>

    <tr>
        <td class="listitem" width="25%">
            <b>Comment:</b>
        </td>
        <td class="listrow">
           
            <textarea rows="4" cols="6" name="commment" class="form-control"> <?=MapUtil::get($provider, 'commment')?> </textarea>
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