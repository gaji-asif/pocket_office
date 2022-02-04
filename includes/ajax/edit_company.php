<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);
$company = DBUtil::getRecord('accounts', $_SESSION['ao_accountid']);
if(!count($company)) {
    UIUtil::showModalError('Could not retrieve company data');
}

$errors = array();
if(RequestUtil::get('submit')) {
    $accountName = RequestUtil::get('account_name');
    $primaryContact = RequestUtil::get('primary_contact');
    $email = RequestUtil::get('email');
    $phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));
    $address = RequestUtil::get('address');
    $city = RequestUtil::get('city');
    $state = RequestUtil::get('state');
    $zip = RequestUtil::get('zip');
    $jobUnit = RequestUtil::get('job_unit');

    if(empty($accountName) || empty($primaryContact) || empty($email) || empty($phone) || empty($address) || empty($city) || empty($state) || empty($zip) || empty($jobUnit)) {
		$errors[] = 'Required fields missing';
    }
	if(strlen($zip) != 5 || !ctype_digit($zip)) {
		$errors[] = 'Zip incorrect format';
	}
	if(strlen($phone) != 10 || !ctype_digit($phone)) {
		$errors[] = 'Phone incorrect format';
	}
    if(!ValidateUtil::validateEmail($email)) {
        $errors[] = 'Email incorrect format';
    }
    
    if(!count($errors)) {
        FormUtil::update('accounts', $_SESSION['ao_accountid']);
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_company.php', 'companycontainer', true, true, true);
</script>
<?php
        die();
    }
}
?>
<form method="post" action="?">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>Edit Company Profile</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td class="listitem" width="25%">
            <b>Company Name:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="account_name" value="<?=MapUtil::get($company, 'account_name')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Primary Contact:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="primary_contact" value="<?=MapUtil::get($company, 'primary_contact')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Email:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="email" value="<?=MapUtil::get($company, 'email')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Phone:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" class="masked-phone" size=10 name="phone" value="<?=MapUtil::get($company, 'phone')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Address:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="address" value="<?=MapUtil::get($company, 'address')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>City:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="city" value="<?=MapUtil::get($company, 'city')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>State:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <select name="state">
<?php
$states = getStates();
foreach($states as $abbr => $state) {
?>
                <option value="<?=$abbr?>" <?=MapUtil::get($company, 'state') == $abbr ? 'selected' : ''?>><?=$abbr?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Zip:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=10 maxlength=10 name="zip" value="<?=MapUtil::get($company, 'zip')?>">
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Job Unit:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="job_unit" value="<?=MapUtil::get($company, 'job_unit')?>">
        </td>
    </tr>
    <tr>
        <td colspan=2 class="listrow" align="right">
            <input name="submit" type="submit" value="Save">
        </td>
    </tr>
</table>
</form>
</body>
</html>