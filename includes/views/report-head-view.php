<?php
$me = UserModel::getMe();
$addressObj = $me->get('office_id') ? new Office($me->get('office_id')) : new Account($_SESSION['ao_accountid']);
?>
<table border="0" width="960" align="center">
    <tr valign="bottom">
        <td>
            <?=AccountModel::getLogoImageTag()?>
            <br>
            <?=$addressObj->getFullAddress()?>
            <br>
            Phone: <?=UIUtil::formatPhone($addressObj->get('phone'))?>
<?php
if($addressObj->get('fax')) {
?>
            <br>
            <b>Fax:</b> <?=UIUtil::formatPhone($addressObj->get('fax'))?>
<?php
}
?>
        </td>
        <td align="right">
            <div style='font-size: 35px; font-weight: bold;'><?=@$title?></div>
<?php
if(isset($sub_title)){
?>
            <div style='font-size: 18px; font-weight: bold;'><?=$sub_title?></div>
<?php
}
?>
            Created <?=DateUtil::formatDate()?>
            <br />
            <a href="<?= ROOT_DIR ?>/reports.php" class="minilink">Back</a> | <a href="javascript: print();" class="minilink">Print</a>
<?php
if(@$allowCsv) {
?>
             | <a href="?csv=1&<?=$_SERVER['QUERY_STRING']?>" class="minilink">Export as CSV</a>
<?php
}
?>
        </td>
    </tr>
    <tr><td colspan="2">&nbsp;</td></tr>
</table>