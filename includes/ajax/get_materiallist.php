<?php

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_materials'))
  die("Insufficient Rights");

$category = RequestUtil::get('cat');
$brand =  RequestUtil::get('brand');;


if(!$brand && !$category) {
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <td width=203>Material Category</td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding">
<?php
$sql = "select category_id, category from categories where account_id='{$_SESSION['ao_accountid']}' order by category asc";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
        <tr>
          <td><b>No Categories</b></td>
        </tr>
<?php
}
$i=1;
while(list($cat_id, $cat)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';
?>
        <tr class='<?php echo $class; ?>'>
          <td><a href='javascript: Request.make("includes/ajax/get_materiallist.php?cat=<?php echo $cat_id; ?>","materialscontainer","yes","yes");' class='basiclink'><?php echo $cat; ?></a></td>
        </tr>
<?php
  $i++;
}
?>
      </table>
    </td>
  </tr>
</table>
<?php
}

else if($brand==''&&$category!='')
{
  $sql = "select category from categories where category_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$category)."'";
  $res = DBUtil::query($sql);
  list($cat)=mysqli_fetch_row($res);
?>
<table border="0" cellpadding="0" cellspacing="0" width="100%">
  <tr valign='top'>
    <td>
      <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
        <tr>
          <td width=203><?php echo $cat; ?></td>
        </tr>
      </table>
    </td>
  </tr>
  <tr>
    <td>
      <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding">
<?php
if(mysqli_num_rows($res)==0)
{
?>
        <tr>
          <td><b>Category Not Found</b></td>
        </tr>
<?php
}
else
{
  $sql = "select brand_id, brand from brands where account_id='".$_SESSION['ao_accountid']."' order by brand asc";
  $res_brands = DBUtil::query($sql);
  if(mysqli_num_rows($res_brands)==0)
  {
  ?>
          <tr>
            <td><b>Category Not Found</b></td>
          </tr>
  <?php
  }
  while(list($brand_id, $brand)=mysqli_fetch_row($res_brands))
  {
    $sql = "select materials.material_id, materials.material, materials.info, units.unit, materials.price".
           " from materials, units".
           " where materials.category_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$category)."' and materials.brand_id='".$brand_id."' and materials.account_id='".$_SESSION['ao_accountid']."' and units.unit_id=materials.unit_id and materials.active=1".
           " order by materials.material asc";
    $res_materials = DBUtil::query($sql);
    if(mysqli_num_rows($res_materials)!=0)
    {
?>
          <tr>
            <td colspan=2>&nbsp;</td>
          </tr>
          <tr>
            <td colspan=2 style='border-bottom: solid 1px #999999;'><b><?php echo $brand." (".mysqli_num_rows($res_materials)."):"; ?></b></td>
          </tr>
<?php
      $i=1;
      while(list($material_id, $material, $info, $unit, $price)=mysqli_fetch_row($res_materials))
      {
        $class='odd';
        if($i%2==0)
          $class='even';

        $material = stripslashes($material);
        $info = stripslashes($info);
?>
          <tr class='<?php echo $class; ?>' valign='top'>
            <td width=300><?php echo $material; ?></td>
            <td class='smallnote'>
              <?php echo $info; ?>
              <br />
              <b>Unit:</b> <?php echo $unit; ?>
              <br />
              <b>Price:</b> $<?php echo $price; ?>
            </td>
          </tr>
<?php
        $i++;
      }
    }
  }

  $sql = "select materials.material_id, materials.material, materials.info, units.unit, materials.price".
         " from materials, units".
         " where materials.category_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$category)."' and materials.brand_id='-1' and materials.account_id='".$_SESSION['ao_accountid']."' and units.unit_id=materials.unit_id and materials.active=1".
         " order by materials.material asc";
  $res_materials = DBUtil::query($sql);
  if(mysqli_num_rows($res_materials)!=0)
  {
?>
          <tr>
            <td colspan=2>&nbsp;</td>
          </tr>
          <tr>
            <td colspan=2 style='border-bottom: solid 1px #999999;'><b><?php echo "Varies (".mysqli_num_rows($res_materials)."):"; ?></b></td>
          </tr>
<?php
    $i=1;
    while(list($material_id, $material, $info, $unit, $price)=mysqli_fetch_row($res_materials))
    {
      $class='odd';
      if($i%2==0)
        $class='even';

      $material = stripslashes($material);
      $info = stripslashes($info);
?>
          <tr class='<?php echo $class; ?>' valign='top'>
            <td width=300><?php echo $material; ?></td>
            <td class='smallnote'>
              <?php echo $info; ?>
              <br />
              <b>Unit:</b> <?php echo $unit; ?>
              <br />
              <b>Price:</b> $<?php echo $price; ?>
            </td>
          </tr>
<?php
      $i++;
    }
  }
}
?>
        <tr>
          <td colspan=10>&nbsp;</td>
        </tr>
        <tr>
          <td class='infofooter' colspan=10>
			<a href='javascript: Request.make("includes/ajax/get_materiallist.php","materialscontainer","yes","yes");' class='basiclink'>
				<i class="icon-double-angle-left"></i>&nbsp;Back
			</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>
</table>
<?php
}
?>