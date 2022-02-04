<?php
include '../common_lib.php';
UserModel::isAuthenticated();

$myUser = UserModel::getMe();
if(!$myUser->exists()) {
    UIUtil::showListError('Could not retrieve user data');
}

?>
<table border="0" width="100%" cellpadding="0" cellspacing="0">
    <tr>
        <td width="50%">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Full Name:</b></td>
                    <td class="listrownoborder"><?=$myUser->fname . " " . $myUser->lname?></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Username:</b></td>
                    <td class="listrow"><?=$myUser->username?></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Access Level:</b></td>
                    <td class="listrow"><?=$myUser->level_title?><?=$myUser->getFounder() ? ' (<i>Founder</i>)' : ''?></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Account DOB</b></td>
                    <td class="listrow"><?=$myUser->dob?></td>
                </tr>
                <tr>
                    <td class="listrow" colspan="2">&nbsp;</td>
                </tr>
            </table>
        </td>
        <td width="50%">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Account Name:</b></td>
                    <td class="listrownoborder"><?=$myUser->account_name?></td>
                </tr>
                <tr>
                    <td class="listitem"><b>DBA:</b></td>
                    <td class="listrow"><?=$myUser->dba?></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Email:</b></td>
                    <td class="listrow"><a href="mailto:<?=$myUser->email?>"><?=$myUser->email?></a></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Phone:</b></td>
                    <td class="listrow"><?=UIUtil::formatPhone($myUser->phone)?></td>
                </tr>
                <tr>
                    <td class="listitem"><b>SMS Carrier:</b></td>
                    <td class="listrow"><?=$myUser->sms_name?></td>
                </tr>
            </table>
        </td>
    </tr>
</table>