<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('add_job', TRUE);
$firstLast = UIUtil::getFirstLast();
$customers = CustomerModel::getAllCustomers($firstLast);

if(RequestUtil::get("submit")) {
	$fname = mysqli_real_escape_string(DBUtil::Dbcont(), RequestUtil::get('fname'));
	$lname = mysqli_real_escape_string(DBUtil::Dbcont(), RequestUtil::get('lname'));
	$nickname = mysqli_real_escape_string(DBUtil::Dbcont(), RequestUtil::get('nickname'));
	$address = mysqli_real_escape_string(DBUtil::Dbcont(), RequestUtil::get('address'));
	$city = mysqli_real_escape_string(DBUtil::Dbcont(), RequestUtil::get('city'));
	$state = RequestUtil::get('state');
	$zip = RequestUtil::get('zip');
	$cross = RequestUtil::get('cross');
	$phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));
	$phone2 = StrUtil::formatPhoneToSave(RequestUtil::get('phone2'));
	$email = RequestUtil::get('email');
	$type = RequestUtil::get('type');
    if(empty($type))
    {
        $type=0;
    }
	$note = RequestUtil::get('note');
	$origin = RequestUtil::get('origin');
    if(empty($origin))
    {
        $origin=0;
    }
    $po_number = RequestUtil::get('po_number');
	$existingCustomer = RequestUtil::get('excustomer');
    $jurisdiction = RequestUtil::get('jurisdiction');
    if(empty($jurisdiction))
    {
        $jurisdiction=0;
    }
    
    $errors = array();
	if(!$existingCustomer) {
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
	}

	if(!count($errors)) {
		$jobHash = md5(mt_rand() . mt_rand() . mt_rand());

		if(empty($existingCustomer)) {
			$newAddress = "$address $city $state $zip";
			$gpsData = CustomerModel::getGPSCoords($newAddress);

			$sql = "INSERT INTO customers
                    VALUES (NULL, '{$_SESSION['ao_accountid']}', '$fname', '$lname', '$nickname', '{$_SESSION['ao_userid']}',
                    '$address', '$city', '$state', '$zip', '{$gpsData[0]}', '{$gpsData[1]}', '$phone',
                    '$phone2', '$email', '$cross', now())";
           
            DBUtil::query($sql);

			$existingCustomer = DBUtil::getInsertId();
		}

		$salesman = 'NULL';
		if(RequestUtil::get('salesman')) {
			$salesman = RequestUtil::get('salesman');
        }

		$provider = 'NULL';
		if(RequestUtil::get('provider')) {
			$provider = RequestUtil::get('provider');
        }

        $referral = 'NULL';
		if(RequestUtil::get('referral')) {
			$referral = RequestUtil::get('referral');
        }
        
        $sql = "select stage_num,order_num from stages where account_id='" . $_SESSION['ao_accountid'] . "' order by order_num asc limit 1";
        $res = DBUtil::queryToArray($sql);
        $stage_num = 1;
        if(isset($res[0]))
        {
            $stage_num = $res[0]['stage_num'];
        }
		$jobNumber = strtoupper(substr(md5(rand() . rand()), 0, 8));
		$sql = "INSERT INTO jobs
                VALUES (NULL, '$jobNumber', '$existingCustomer', '{$_SESSION['ao_accountid']}', $stage_num, curdate(), 
                    '{$_SESSION['ao_userid']}', $salesman, $referral, NULL, $provider, NULL, NULL,NULL,NULL,NULL,NULL, 0, '$type',
                    '$note','$po_number' ,'$origin', '$jurisdiction', NULL, now(), '$jobHash')";
	//echo "<pre>";print_r($sql);die();
        DBUtil::query($sql);

		$newJobId = DBUtil::getInsertId();
        
        //store snapshot
        $myJob = new Job($newJobId);
        $myJob->storeSnapshot();

        UserModel::startWatchingConversation($newJobId, 'job');
		if(RequestUtil::get('salesman')) {
			NotifyUtil::notifyFromTemplate('add_job_salesman', $salesman, NULL, array('job_id' => $newJobId));
            UserModel::startWatchingConversation($newJobId, 'job', RequestUtil::get('salesman'));
		}

		if(RequestUtil::get('referral')) {
			NotifyUtil::notifyFromTemplate('referral_assigned', $referral, NULL, array('job_id' => $newJobId));
            UserModel::startWatchingConversation($newJobId, 'job', RequestUtil::get('referral'));
		}

		JobModel::saveEvent($newJobId, 'Added New Job');
		
		session_start();
		$_SESSION['my_job_id']=$newJobId;
?>

<script>
//Request.make("<?=AJAX_DIR?>/get_job.php?id=<?=$newJobId?>", 'jobscontainer', true, true);
window.parent.location.href ="/jobs.php"; 
//	Request.makeModal("<?=AJAX_DIR?>/get_job.php?id=<?=$newJobId?>", 'jobscontainer', true, true, true);
</script>
<?php
		die();
	}
}

//get account meta data
$accountMetaData = AccountModel::getAllMetaData();


?>
<link href = "https://code.jquery.com/ui/1.12.1/themes/ui-lightness/jquery-ui.css" rel = "stylesheet">
<script src="https://code.jquery.com/ui/1.12.1/jquery-ui.js"
              integrity="sha256-T0Vest3yCU7pafRw9r+settMBX6JkKN06dqBnpQ8d30="
              crossorigin="anonymous"></script>
<script>
$(document).ready(function() {
    $('[name="excustomer"]').change(toggleNewCustomer);
});

function toggleNewCustomer() {
    if($(this).val().length) {
        $('.add-new-customer').hide();
    } else {
        $('.add-new-customer').show();
    }
}
</script>
<script src="https://polyfill.io/v3/polyfill.min.js?features=default"></script>
<form action="?" method="post" name="job">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Add New Job</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?php if(!empty($errors)){?>
    <?=AlertUtil::generate($errors, 'error', TRUE)?>
<?php }?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainer">
    <tr valign="top">
        <td width="48%">
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tr>
                    <td class="smalltitle">Insured</td>
                </tr>
                <tr>
                    <td>
                        <select name="excustomer" style="width: 100%;">
                            <option value="">Add New Insured</option>
<?php
foreach($customers as $customer) {
?>
                            <option value="<?=MapUtil::get($customer, 'customer_id')?>"><?=MapUtil::get($customer, 'select_label')?></option>
<?php
}
?>
                        </select>
                    </td>
                </tr>
                <tr><td>&nbsp;</td></tr>
                <tr class="add-new-customer">
                    <td class="smalltitle">Add New Insured</td>
                </tr>
                <tr class="add-new-customer">
                    <td>
                        <table border="0" width="100%" cellpadding="0" cellspacing="0" class="listtable">
                            <tr>
                                <td class="listitemnoborder" width="25%"><b>First Name:</b></td>
                                <td class="listrownoborder"><input type="text" name="fname" value="<?=$fname?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Last Name:</b></td>
                                <td class="listrow"><input type="text" name="lname" value="<?=$lname?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Nickname:</b></td>
                                <td class="listrow"><input type="text" name="nickname" value="<?=$nickname?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Address:</b>&nbsp;<span class="red">*</span></td>
                                <td class="listrow">
                                    <input type="text" id="address" name="address" value="<?=$address?>" autocomplete="off">
                                </td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>City:</b>&nbsp;<span class="red">*</span></td>
                                <td class="listrow"><input type="text" id ="city" name="city" value="<?= $city ?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>State:</b>&nbsp;<span class="red">*</span></td>
                                <td class="listrow">
                                    <select id="state" name="state">
<?php
$states = getStates();
foreach($states as $abbr => $state)
{
?>
                                        <option value="<?=$abbr?>"><?=$abbr?></option>
<?php
}
?>
                                    </select>
                                </td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Zip:</b>&nbsp;<span class="red">*</span></td>
                                <td class="listrow"><input type="text" id ="city" name="zip" maxlength="5" value="<?=$zip?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Cross Street:</b></td>
                                <td class="listrow"><input type="text" name="cross" value="<?=$cross?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Phone:</b></td>
                                <td class="listrow"><input type="text" name="phone" class="masked-phone" value="<?=$phone?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Secondary Phone:</b></td>
                                <td class="listrow"><input type="text" name="phone2" class="masked-phone" value="<?=$phone2?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Email:</b></td>
                                <td class="listrow"><input type="text" name="email" value="<?=$email?>" autocomplete="off"></td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
        </td>
        <td width="4%">&nbsp;</td>
        <td width="48%">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td colspan="2" class="smalltitle">Job Information</td>
                </tr>
                <tr>
                    <td>
                        <table border="0" width="100%" cellpadding="0" cellspacing="0" class="listtable">
                            <tr>
                                <td class="listitemnoborder" width="25%"><b>Job Origin:</b></td>
                                <td class="listrownoborder">
                                    <?php $origins = JobUtil::getAllOrigins(); ?>
                                    <?=ViewUtil::generatePicklist($origins, 'origin_id', 'origin', NULL, array('name' => 'origin'))?>
                                </td>
                            </tr>
<?php
if(ModuleUtil::checkAccess('assign_job_salesman')) {
?>
                            <tr>
                                <td class="listitem">
                                    <b>Referral:</b>
                                </td>
                                <td class="listrow">
                                    <select name="referral" onchange="Request.make('get_usernotes.php?id='+this.value, 'referralnotes', false, true);">
                                        <option value="">No Referral</option>
<?php
	if(empty($accountMetaData['add_job_referral_user_dropdown']['meta_value'])) {
		$salesmen = UserModel::getAll(FALSE, $firstLast);
	}
	else {
		$salesmen = UserModel::getAllByLevel($accountMetaData['add_job_referral_user_dropdown']['meta_value'], FALSE, $firstLast);
	}

	foreach($salesmen as $salesman) {
?>
                                        <option value="<?=$salesman['user_id']?>"><?="{$salesman['select_label']}"?></option>
<?php
	}
?>
                                    </select>
                                </td>
                            </tr>
<?php
}
?>
                            <tr>
                                <td id="referralnotes" colspan="2"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Job Type:</b></td>
                                <td class="listrow">
                                    <?=ViewUtil::generatePicklist(JobUtil::getAllJobTypes(), 'job_type_id', 'job_type', NULL, array('name' => 'type'))?>
                                </td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>Job Type Note:</b></td>
                                <td class="listrow"><input type="text" name="note" size="30" value="<?=$note?>" autocomplete="off"></td>
                            </tr>
                            <tr>
                                <td class="listitem"><b>PO Number:</b></td>
                                <td class="listrow"><input type="text" name="po_number" size="30" value="<?=$po_number?>" autocomplete="off"></td>
                            </tr>
<?php

if(ModuleUtil::checkAccess('assign_job_salesman')) {
?>
                            <tr>
                                <td class="listitem">
                                    <b>Customer:</b>
                                </td>
                                <td class="listrow">
                                    <input type="hidden" id="auto-salesman" name="salesman" >
                                    <input  id = "autocomplete-5">
                            <script>
                                  $(function() {
                                    $( "#autocomplete-5" ).autocomplete({
                                       source: "<?=AJAX_DIR?>/fetch_salesmen.php",
                                       select: function( event , ui ) {
                                                event.preventDefault();
                                                $("#autocomplete-5").val(ui.item.label);
                                                $("#auto-salesman").val(ui.item.value);
                                            },
                                      focus: function(event, ui) {
                                                event.preventDefault();
                                                $("#autocomplete-5").val(ui.item.label);
                                            }
                                    });
                                    
                                 });
                            </script> 
                                </td>
                            </tr>
<?php
}
?>
                            <tr>
                                <td class="listitem">
                                    <b>Provider:</b>
                                </td>
                                <td class="listrow">
                                    <input type="hidden" id="auto-provider" name="provider" >                     
                                    <input  id = "autocomplete-7">
                                    <script>
                                          $(function() {
                                            $( "#autocomplete-7" ).autocomplete({
                                               source: "<?=AJAX_DIR?>/fetch_provider.php",
                                               select: function( event , ui ) {
                                                        event.preventDefault();
                                                        $("#autocomplete-7").val(ui.item.label);
                                                        $("#auto-provider").val(ui.item.value);
                                                    },
                                              focus: function(event, ui) {
                                                        event.preventDefault();
                                                        $("#autocomplete-7").val(ui.item.label);
                                                    }
                                            });
                                            
                                         });
                                    </script> 
                                </td>
                            </tr>
                            <tr>
                                <td class="listitem">
                                    <b>Jurisdiction:</b>
                                </td>
                                <td class="listrow">
                                    <input type="hidden" id="auto-jurisdiction" name="jurisdiction" > 
                                    <input  id = "autocomplete-6">
                                    <script>
                                          $(function() {
                                            $( "#autocomplete-6" ).autocomplete({
                                               source: "<?=AJAX_DIR?>/fetch_jurisdiction.php",
                                               select: function( event , ui ) {
                                                        event.preventDefault();
                                                        $("#autocomplete-6").val(ui.item.label);
                                                        $("#auto-jurisdiction").val(ui.item.value);
                                                    },
                                              focus: function(event, ui) {
                                                        event.preventDefault();
                                                        $("#autocomplete-6").val(ui.item.label);
                                                    }
                                            });
                                            
                                         });
                                    </script> 

                                </td>
                            </tr>
                        </table>
                    </td>
                </tr>
            </table>
            <table border="0" width="100%">
                <tr>
                    <td align="right">
                        <input name="submit" type="submit" value="Save">
                        </form>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
<script>

let autocomplete;
let address1Field;
let postalField;

function initAutocomplete() {
  address1Field = document.querySelector("#address");
  address2Field = document.querySelector("#cross");
  postalField = document.querySelector("#zip");
  // Create the autocomplete object, restricting the search predictions to
  // addresses in the US and Canada.
  autocomplete = new google.maps.places.Autocomplete(address1Field, {
    componentRestrictions: { country: ["us", "ca","in"] },
    fields: ["address_components", "geometry"],
    types: ["address"],
  });
  address1Field.focus();
  // When the user selects an address from the drop-down, populate the
  // address fields in the form.
  autocomplete.addListener("place_changed", fillInAddress);
}

function fillInAddress() {
  // Get the place details from the autocomplete object.
  const place = autocomplete.getPlace();
  let address1 = "";
  let postcode = "";

  // Get each component of the address from the place details,
  // and then fill-in the corresponding field on the form.
  // place.address_components are google.maps.GeocoderAddressComponent objects
  // which are documented at http://goo.gle/3l5i5Mr
  for (const component of place.address_components) {
    const componentType = component.types[0];

    switch (componentType) {
      case "street_number": {
        address1 = `${component.long_name} ${address1}`;
        break;
      }

      case "route": {
        address1 += component.short_name;
        break;
      }

      case "postal_code": {
        postcode = `${component.long_name}${postcode}`;
        break;
      }

      case "postal_code_suffix": {
        postcode = `${postcode}-${component.long_name}`;
        break;
      }
      case "locality":
        document.querySelector("#city").value = component.long_name;
        break;
      case "administrative_area_level_1": {
        document.querySelector("#state").value = component.short_name;
        break;
      }
  }
  }

  address1Field.value = address1;
  postalField.value = postcode.substring(1, 4);
  // After filling the form with address components from the Autocomplete
  // prediction, set cursor focus on the second address line to encourage
  // entry of subpremise information such as apartment, unit, or floor number.
  if(postcode=="")
  {
      postalField.focus();
  }
  else
  {
    address2Field.focus();
  }
}

    

</script>
</script>
   <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyA1gf0GrUztgVDDVapXnJlcyYMCilJLubQ&callback=initAutocomplete&libraries=places&v=weekly"
      async defer></script>
</body>
</html>