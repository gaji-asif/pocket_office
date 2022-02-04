<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('modify_suppliers', TRUE);

$supplierId = RequestUtil::get('id');

$sql = "select * from suppliers where supplier_id = '$supplierId' limit 1";
$res = DBUtil::query($sql);

if(!mysqli_num_rows($res)) {
    UIUtil::showModalError('Supplier not found!');
}

list($supplier_id, $name, $contact, $email, $phone, $fax)=mysqli_fetch_row($res);

if($_GET['action']=='del') {
    $sql = "select sheet_id from sheets where supplier_id='".$supplier_id."' limit 1";
    $res = DBUtil::query($sql);

    if(mysqli_num_rows($res)) {
?>

<script>
    alert('Jobs Currently Associated - Cannot Remove');
</script>
<?php
    }
    else
    {
        $sql = "delete from suppliers where supplier_id='".$supplier_id."' limit 1";
        DBUtil::query($sql);
        $sql = "delete from suppliers_link where supplier_id='".$supplier_id."' and account_id='".$_SESSION['ao_accountid']."' limit 1";
        DBUtil::query($sql);
?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_suppliers.php', 'suppliers-list', true, true, true);
</script>
<?php
        die();
    }
}


if(isset($_POST['name']) && $supplier_id)
{
  $temp_email = $email;

  $name=$_POST['name'];
  $contact=$_POST['contact'];
  $phone=StrUtil::formatPhoneToSave($_POST['phone']);
  $email=$_POST['email'];
  $fax=StrUtil::formatPhoneToSave($_POST['fax']);

  if($name==''||$email==''||$phone=='')
  {
    $error=1;
    $error_msg .= "<br />Must enter ALL required fields";
  }
  else
  {
    if(!ValidateUtil::validateEmail($email))
    {
      $error=1;
      $error_msg .= "<br />Email incorrect format";
    }
    if(UserModel::emailExists($email) && $email!=$temp_email)
    {
      $error=1;
      $error_msg .= "<br />Email in use";
    }
    if(strlen($phone)!=10 || !ctype_digit($phone))
    {
      $error=1;
      $error_msg .= "<br />Phone incorrect format";
    }
    if(strlen($fax)!=10 || !ctype_digit($fax))
    {
      $error=1;
      $error_msg .= "<br />Fax incorrect format";
    }
  }

  if($error==0)
  {
    $name = mysqli_real_escape_string(DBUtil::Dbcont(),$name);
    $contact = mysqli_real_escape_string(DBUtil::Dbcont(),$contact);
    $email = mysqli_real_escape_string(DBUtil::Dbcont(),$email);
    $phone = mysqli_real_escape_string(DBUtil::Dbcont(),$phone);
    $fax = mysqli_real_escape_string(DBUtil::Dbcont(),$fax);

    $sql = "update suppliers set supplier='".$name."', contact='".$contact."', email='".$email."', phone='".$phone."', fax='".$fax."' where supplier_id=".$supplier_id." limit 1";
    DBUtil::query($sql);
?>

<script>

  Request.makeModal('<?=AJAX_DIR?>/get_suppliers.php', 'suppliers-list', true, true, true);

</script>
<?php
  }
}

$sql = "select * from suppliers where supplier_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id'])."' limit 1";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
  die("Invalid Content");

list($supplier_id, $name, $contact, $email, $phone, $fax)=mysqli_fetch_row($res);

?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Edit Supplier
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
if($error==1)
{
?>
            <tr>
              <td colspan=2 style='color: red; font-size: 11px;' class="listrownoborder">
                <b>Errors Found!</b>
                <?php echo $error_msg; ?>
              </td>
            </tr>
<?php
}
?>
          <tr>
            <td>
              <form method="post" name='customer' action='?id=<?php echo $supplier_id; ?>'>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="listitemnoborder" width="25%"><b>Supplier:</b>&nbsp;<span class="red">*</span></td>
                    <td class="listrownoborder"><input type="text" name='name' value='<?php echo $name; ?>'></td>
                  </tr>
                  <tr>
                    <td width="25%" class="listitem"><b>Contact:</b></td>
                    <td class="listrow"><input type="text" name='contact' value='<?php echo $contact; ?>'></td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Phone:</b>&nbsp;<span class="red">*</span></td>
                    <td class="listrow"><input type="text" class="masked-phone" name='phone' value='<?php echo $phone; ?>'></td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Fax:</b></td>
                    <td class="listrow"><input type="text" class="masked-phone" name='fax' value='<?php echo $fax; ?>'></td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Email:</b></td>
                    <td class="listrow"><input type="text" name='email' value='<?php echo $email; ?>'></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="right" class="listrow">
                  <input type="button" value="Delete" onclick='if(confirm("Are you sure?")){window.location="edit_supplier.php?id=<?php echo $supplier_id; ?>&action=del";}'>
                  <input type="submit" value="Save">
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>