<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$title = RequestUtil::get('title');
$phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));
$fax = StrUtil::formatPhoneToSave(RequestUtil::get('fax'));
$address = RequestUtil::get('address');
$city = RequestUtil::get('city');
$state = RequestUtil::get('state');
$zip = RequestUtil::get('zip');

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
		$sql = "INSERT INTO offices
                VALUES (0, '".$_SESSION['ao_accountid']."', '$title', '$phone', '$fax', '$address', '$city', '$state', '$zip')";
		DBUtil::query($sql);
?>
<script>
    parent.window.location.href = '/modoffices.php';
</script>
<?php
        die();
	}
}

?>
<form method="post" name='offices' action='?'>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            New Office
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td class="listitemnoborder" width="25%">
            <b>Title:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrownoborder">
            <input type="text" size=35 name='title' value='<?=$title?>'>
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Phone:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" class="masked-phone" size=10 name='phone' value='<?=$phone?>'>
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Fax:</b>
        </td>
        <td class="listrow">
            <input type="text" class="masked-phone" size=10 name='fax' value='<?=$fax?>'>
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>Address:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name='address' value='<?=$address?>'>
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>City:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" size=35 name='city' value='<?=$city?>'>
        </td>
    </tr>
    <tr>
        <td class="listitem" width="25%">
            <b>State:</b>
        </td>
        <td class="listrow">
            <select name="state" id="state">
<?php
$states = getStates();
foreach($states as $abbr => $state) {
?>
                <option value="<?=$abbr?>"><?=$abbr?></option>
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
            <input type="text" name='zip' value='<?=$zip?>'>
        </td>
    </tr>
    <tr>
        <td colspan=2 class="listrow" align="right">
            <input name="submit" type="submit" value="Save">
            </form>
        </td>
    </tr>
</table>
</body>
</html>