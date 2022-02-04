<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$id = RequestUtil::get('id');
$title = RequestUtil::get('title');
$phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));
$fax = StrUtil::formatPhoneToSave(RequestUtil::get('fax'));
$address = RequestUtil::get('address');
$city = RequestUtil::get('city');
$zip = RequestUtil::get('zip');

$office = DBUtil::getRecord('offices');
if(empty($office)) {
    UIUtil::showModalError('Office not found!');
}

$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($title) || empty($address) || empty($city) || empty($zip)) {
		$errors[] = 'Required fields missing';
	}
	if(strlen($zip) != 5 || !ctype_digit($zip)) {
		$errors[] = 'Zip incorrect format';
	}
	if(strlen($phone) != 10 || !ctype_digit($phone)) {
		$errors[] = 'Phone incorrect format';
	}
	if($fax != '' && (strlen($fax) != 10 || !ctype_digit($fax))) {
		$errors[] = 'Fax incorrect format';
	}
	
    if(!count($errors)) {
        FormUtil::update('offices');
?>
<script>
    parent.window.location.href = '/modoffices.php';
</script>
<?php
        die();
	}
}
?>
<form method="post" action="?id=<?=$id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            Edit Office <?=MapUtil::get($office, 'title')?>
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td class="listitem" width="25%">
            <b>Title:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="title" value="<?=htmlentities($office['title'])?>">
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Phone:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" class="masked-phone" size=10 name="phone" value="<?= htmlentities($office['phone']) ?>">
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Fax:</b>
        </td>
        <td class="listrow">
            <input type="text" class="masked-phone" size=10 name="fax" value="<?=htmlentities($office['fax'])?>">
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Address:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="address" value="<?=htmlentities($office['address'])?>">
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>City:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name="city" value="<?=htmlentities($office['city'])?>">
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>State:</b>
        </td>
        <td class="listrow">
            <select name="state" id="state">
<?php
$states = getStates();
foreach($states as $abbr => $state) {
?>
                <option value="<?=$abbr?>" <?=$office['state'] == $abbr ? 'selected' : ''?>><?=$abbr?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Zip:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=10 maxlength=10 name="zip" value="<?=htmlentities($office['zip']) ?>">
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