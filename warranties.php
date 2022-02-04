<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1)
	die("Insufficient Rights");

if (isset($_POST['warrantyid']))
{
	$warranty_id = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['warrantyid']);
	$color = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['color']);

	$sql = "update warranties set color='" . $color . "' where warranty_id='$warranty_id' limit 1";
	DBUtil::query($sql) or die(mysqli_error());
}

if (isset($_POST['warranty']))
{
	$label = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['warranty']);
	$color = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['color']);
	if (!empty($label) && !empty($color))
	{
		$sql = " insert into warranties (account_id, label, color) values('{$_SESSION['ao_accountid']}', '$label', '$color')";
		DBUtil::query($sql) or die(mysqli_error());
	}
	else
		UIUtil::showAlert('Required Information Missing');
}

$warranty_id = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']);
if ($_GET['action'] == 'del' && !empty($warranty_id))
{
	$sql = "select id from job_meta where meta_name = 'job_warranty' and meta_value = '$warranty_id' limit 1";
	$res = DBUtil::query($sql) or die(mysqli_error());
	if (mysqli_num_rows($res) != 0)
		UIUtil::showAlert('Jobs Currently Associated - Cannot Remove');
	else
	{
		$sql = "delete from warranties where warranty_id = '$warranty_id' and account_id = '{$_SESSION['ao_accountid']}' limit 1";
		DBUtil::query($sql) or die(mysqli_error());
	}
}
?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Warranties</h1>
	<table border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
      <tr>
        <td>
          <table border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
            <tr>
              <td>
                Add Warranty
              </td>
            </tr>
          </table>
        <table border="0" cellpadding="0" cellspacing="0" width="100%" class="infocontainer">
            <tr>
                <td width="15%">
                <form method='post' action='?' name='addprovider' style='margin-bottom:0;'>
                <b>Warranty Name:</b>
              </td>
              <td>
                <input type='text' name='warranty' size=30>
              </td>
            </tr>
            <tr>
              <td width=100>
                <b>Color:</b>
              </td>
              <td>
                <input size=10 class="color {hash:true}" rel="star-color-picker" data-preview-id="preview-star" value="CCC" name='color'>
                <i id="preview-star" class="icon-star" style="color: #CCC;"></i>
              </td>
            </tr>
            <tr>
              <td colspan=2 align='right'>
                <input type='submit' value='Add'>
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
<br />
    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr>
        <td>
          <table border=0 width='100%' align='left' cellpadding=0 cellspacing=0 class='containertitle'>
            <tr>
              <td width=250>
                Warranties
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border=0 width='100%' align='left' cellpadding=2 cellspacing=0 class='infocontainernopadding'>
<?php
$warranties = JobUtil::getAllWarranties();
foreach($warranties as $key => $warranty) {
  $class='odd';
  if($key%2==0)
    $class='even';
?>
<tr class='<?php echo $class; ?>'>
	<td>
		<table border="0" cellpadding="2" cellspacing="0" width="100%">
			<tr>
				<td width=30><a href='javascript:if(confirm("Are you sure?")){window.location="?id=<?php echo $warranty['warranty_id']; ?>&action=del";}'><img src='<?=ROOT_DIR?>/images/icons/delete.png'></td>
				<td><i class="icon-star" id="preview-star-<?php echo $warranty['warranty_id']; ?>" style="color: <?=$warranty['color']?>"></i> <b><?php echo $warranty['label']; ?></b></td>
				<td align='right'>
					<form method='post' action='?' style='margin-bottom:0;'>
					<b>Edit Color:</b>
					<input size=10 class="color {hash:true}" id="color-input-<?php echo $warranty['warranty_id']; ?>" rel="star-color-picker" data-preview-id="preview-star-<?php echo $warranty['warranty_id']; ?>" value="<?=$warranty['color']?>" name='color'>
					<input type='hidden' name='warrantyid' value='<?php echo $warranty['warranty_id']; ?>'>
					<input type='submit' value='Go'>
					<input type='button' value='Reset' onclick="$('#color-input-<?php echo $warranty['warranty_id']; ?>')[0].color.fromString('<?=$warranty['color']?>'); $('#color-input-<?php echo $warranty['warranty_id']; ?>').trigger('change');">
					</form>
				</td>
			</tr>
		</table>
	</td>
</tr>
<?php
  $i++;
}
if(sizeof($warranties)==0)
{
?>
            <tr>
              <td align="center"><b>No Warranties Found</b></td>
            </tr>
<?php
}
?>
          </table>
        </td>
      </tr>
      <tr><td>&nbsp;</td></td>
    </table>
  </body>
</html>
