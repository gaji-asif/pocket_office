<?php

include '../common_lib.php';

echo ViewUtil::loadView('doc-head');

ModuleUtil::checkAccess('add_user', TRUE);



$errors = array();

if(RequestUtil::get('submit')) {

    $fname = ucwords(strtolower(trim(RequestUtil::get('fname'))));

    $lname = ucwords(strtolower(trim(RequestUtil::get('lname'))));

    $dba = RequestUtil::get('dba');

    $username = RequestUtil::get('username');

    $email = RequestUtil::get('email');

    $phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));

    $sms = RequestUtil::get('sms');
    if(empty($sms))
    {
        $sms = 0;
    }

    $level = RequestUtil::get('level');

    $founder = RequestUtil::get('founder', 0);

    $notes = RequestUtil::get('notes');

    $office = RequestUtil::get('office', 'NULL');



    if(empty($fname) || empty($lname) || empty($username) || empty($email) || empty($phone)) {

        $errors[] = 'Required fields missing';

    } else {

        if(!ctype_alnum($username)) {

            $errors[] = 'Username can only be letters or numbers';

        }

        if(strlen($username)<6) {

            $errors[] = 'Username Must be at least 6 characters';

        }

        if(UserModel::usernameExists($username)) {

            $errors[] = 'Username in use';

        }

        if(!ValidateUtil::validateEmail($email)) {

            $errors[] = 'Email incorrect format';

        }

        if(UserModel::emailExists($email)) {

            $errors[] = 'Email in use';

        }

        if($phone && (strlen($phone) != 10 || !ctype_digit($phone))) {

            $errors[] = 'Phone incorrect format';

        }

        if(UserModel::phoneExists($phone)) {

            $errors[] = 'Phone Number in use';

        }

    }

    

    if(!count($errors)) {

        $password = UserUtil::generatePassword();

        

        $sql = "INSERT INTO users (username, fname, lname, password, dba, email, phone, sms_carrier, level, reg_date, account_id, founder, notes, office_id)

                VALUES ('$username', '$fname', '$lname', '$password', '$dba', '$email', '$phone', '$sms', '$level', CURDATE(), '{$_SESSION['ao_accountid']}', '$founder', '$notes', $office)";
        
        DBUtil::query($sql);

        $newUserID = DBUtil::getInsertId();



        $sql = "INSERT INTO settings VALUES (0, '$newUserID', 15, 5, 180, 400, 1, 1, 1, 1, 1, 1, 1)";

        DBUtil::query($sql);

        UserModel::logAccess($newUserID);

        NotifyUtil::emailFromTemplate('new_user', $newUserID);

?>



<script>

    Request.makeModal('<?=AJAX_DIR?>/get_userlist.php', 'userscontainer', true, true, true);

</script>

<?php

        die();

    }

}



?>

<form method="post" name="user" action="?">

<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">

    <tr valign="center">

        <td>

          Add New User

        </td>

        <td align="right">

          <i class="icon-remove grey btn-close-modal"></i>

        </td>

    </tr>

</table>

<?=AlertUtil::generate($errors, 'error', TRUE)?>

<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">

    <tr valign="top">

        <td width="25%" class="listitem"><b>First Name:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow"><input type="text" name="fname" value="<?=$fname?>"></td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Last Name:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow"><input type="text" name="lname" value="<?=$lname?>"></td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Username:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow">

            <input type="text" name="username" value="<?=$username?>">

            <br />Letters and numbers and at least 6 characters

        </td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>DBA:</b></td>

        <td class="listrow"><input type="text" name="dba" value="<?=$dba?>"></td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Email:</b>&nbsp;<span class="red">*</span></td>

        <td class="listrow"><input type="text" id="emailform" name="email" value="<?=$email?>"></td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Phone:</b></td>

        <td class="listrow"><input type="text" name="phone" class="masked-phone" value="<?=$phone?>"></td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>SMS Carrier:</b></td>

        <td class="listrow">

            <select name="sms">

                <option value="">No SMS</option>

<?php

$carriers = getAllSmsCarriers();

foreach($carriers as $carrier) {

?>

                <option value='<?=$carrier['sms_id']?>'><?=$carrier['carrier']?></option>

<?php

}

?>

            </select>

        </td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Notes:</b></td>

        <td class="listrow"><textarea rows="5" style="width: 100%;"><?=$notes?></textarea></td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Access Level:</b></td>

        <td class="listrow">

            <select name="level">

<?php

$userLevels = UserModel::getAllLevels();

foreach($userLevels as $userLevel) {

?>

                <option value='<?=$userLevel['level_id']?>'><?=$userLevel['level']?></option>

<?php

}

?>

            </select>

        </td>

    </tr>

    <tr valign="top">

        <td class="listitem"><b>Office</b></td>

        <td class="listrow">

            <select name="office">

                <option value="">Default</option>

<?php

$offices = AccountModel::getAllOffices();

foreach($offices as $office) {

?>

                <option value="<?=$office['office_id']?>"><?=$office['title']?></option>

<?php

}

?>

            </select>

        </td>

    </tr>

    <tr>

        <td class="listitem"><b>Founder</b></td>

        <td class="listrow">

            <input type="checkbox" value="1" name="founder">

        </td>

    </tr>

    <tr>

        <td class="listrow" align="right" colspan="2">

            <input name="submit" type="submit" value="Save">

        </td>

    </tr>

</table>

</form>

</body>

</html>