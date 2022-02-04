<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('edit_customer', TRUE);

$myCustomer = new Customer(RequestUtil::get('id'), FALSE);
if(!$myCustomer) {
    UIUtil::showModalError('Customer not found!');
}

ModuleUtil::canAccessObject('edit_customer', $myCustomer, TRUE);

$errors = array();
if(RequestUtil::get('submit')) {
  	$fname = RequestUtil::get('fname');
  	$lname = RequestUtil::get('lname');
  	$nickname = RequestUtil::get('nickname');
	$address = RequestUtil::get('address');
	$city = RequestUtil::get('city');
	$state = RequestUtil::get('state');
	$zip = RequestUtil::get('zip');
	$phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));
	$phone2 = StrUtil::formatPhoneToSave(RequestUtil::get('phone2'));
	$email = RequestUtil::get('email');
		
    if(empty($address) || empty($city) || empty($zip)) {
        $errors[] = 'Please enter all required fields';
    }
    else {
        if(!($nickname || ($fname && $lname))) {
            $errors[] = 'You must enter a full name or nickname';
        }
        if(strlen($zip) != 5 || !ctype_digit($zip)) {
            $errors[] = 'Zip incorrect format';
        }
        if(!empty($email) && !ValidateUtil::validateEmail($email)) {
            $errors[] = 'Email incorrect format';
        }
        if(!empty($email) && UserModel::emailExists($email) && $email != UserModel::getProperty($_SESSION['ao_userid'], 'email')) {
            $errors[] = 'Email in use';
        }
        if((strlen($phone) != 10 || !ctype_digit($phone)) && !empty($phone)) {
            $errors[] = 'Phone incorrect format';
        }
        if((strlen($phone2) != 10 || !ctype_digit($phone2)) && !empty($phone2)) {
            $errors[] = 'Secondary Phone incorrect format';
        }
    }

    if(!count($errors)) {
    FormUtil::update('customers');
        //$fullAddress = "$address $city $state $zip";
        //$gpsCoords = CustomerModel::getGPSCoords($fullAddress);
        //$_POST['lat'] = $gpsCoords[0];
        //$_POST['long'] = $gpsCoords[1];
        
        
?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_customer.php?id=<?=$myCustomer->getMyId()?>', 'customerscontainer', true, true, true);
</script>
<?php
        die();
    }
}

?>
<script src="http://maps.google.com/maps/api/js?key=AIzaSyA1gf0GrUztgVDDVapXnJlcyYMCilJLubQ" type="text/javascript"></script>
<script type="text/javascript">
function GetCoordinateAndSubmit() {
    var address=  $(".address").val() +" "+$(".city").val() +" "+$(".state").val() +" "+$(".zip").val() ;
geocoder = new google.maps.Geocoder();
  geocoder.geocode( { 'address':  address}, function(results, status) {
      if (status == google.maps.GeocoderStatus.OK) {
         $(".lat").val(results[0].geometry.location.lat());
         $(".long").val(results[0].geometry.location.lat());
          return true;
        }
        else
        {
            return true; 
        }
  });
  return false;
} 
</script>
<form method="post" onsubmit="return GetCoordinateAndSubmit()" name="customer" action="?id=<?=$myCustomer->getMyId()?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            Edit Customer
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>

<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <input type="hidden" name="lat" value="" class="lat">
    <input type="hidden" name="long" value="" class="long">
    <tr>
        <td width="25%" class="listitemnoborder"><b>First Name:</b></td>
        <td class="listrownoborder">
            <input type="text" name="fname" value="<?=$myCustomer->get('fname')?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Last Name:</b></td>
        <td class="listrow"><input type="text" name="lname" value="<?=$myCustomer->get('lname')?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Nickname:</b></td>
        <td class="listrow"><input type="text" name="nickname" value="<?=$myCustomer->get('nickname')?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Address:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrow"><input type="text" class="address" name="address" size=30 value="<?=$myCustomer->get('address')?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>City:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrow"><input type="text" name="city" class="city" value="<?=$myCustomer->get('city')?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>State:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrow">
            <select name="state" class="state" id="state">
<?php
$states_array = getStates();
foreach($states_array as $abbr => $state)
{
?>
                <option value="<?=$abbr?>" <?=$abbr == $myCustomer->get('state') ? 'selected' : ''?>><?=$abbr?></option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Zip:</b>&nbsp;<span class="red">*</span></td>
        <td class="listrow"><input type="text" name="zip" class="zip" maxlength=5 size=5 value="<?=$myCustomer->get('zip')?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Cross Street:</b></td>
        <td class="listrow"><input type="text" name="cross_street" size=30 value="<?=$myCustomer->get('cross_street')?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Phone:</b></td>
        <td class="listrow"><input type="text" class="masked-phone" name="phone" size=10 value="<?=$myCustomer->get('phone')?>"></td>
    </tr>
    <tr>
        <td class="listitem"><b>Secondary Phone:</b></td>
        <td class="listrow"><input type="text" class="masked-phone" name="phone2" size=10 value='<?=$myCustomer->get('phone2')?>'></td>
    </tr>
    <tr>
        <td class="listitem"><b>Email:</b></td>
        <td class="listrow"><input type="text" name="email" size=30 value='<?=$myCustomer->get('email')?>'></td>
    </tr>
    <tr>
        <td colspan=2 class="listrow" align="right">
            <input name="submit" type="submit" value="Save">
            </form>
        </td>
    </tr>
</table>
</form>
</body>
</html>