<?php
include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('view_suppliers'))
    die("Insufficient Rights");
?>

<?php
$sql = "SELECT s.*
        FROM suppliers s, suppliers_link sl
        WHERE s.supplier_id = sl.supplier_id
            AND sl.account_id = '{$_SESSION['ao_accountid']}'
        ORDER BY s.supplier ASC";
$results = DBUtil::query($sql);

$i = 1;
while (list($id, $supplier, $contact, $email, $phone, $fax) = mysqli_fetch_row($results)) {
?>
        <tr <?=ModuleUtil::checkAccess('modify_suppliers') ? 'rel="open-modal" data-script="edit_supplier.php?id=' . $id . '"': ''?>>
            <td><b><?=$supplier?></b></td>
            <td><?=$contact?></td>
            <td><?=UIUtil::formatPhone($phone)?></td>
            <td><?=UIUtil::formatPhone($fax)?></td>
            <td><?=$email?></td>
        </tr>
<?php
}
if(!DBUtil::hasRows($results)) {
?>
        <tr>
            <td colspan="10" align="center">
                <b>No Suppliers Found</b>
            </td>
        </tr>
<?php
}