<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('job_material_sheet'))
  die("Insufficient Rights");

$cat = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['cat']);
if($cat=='')
  die("No Category Defined");

$sql = "select brand_id, brand from brands where account_id='".$_SESSION['ao_accountid']."' order by brand asc";
$res_brands = DBUtil::query($sql);

?>
      <select id='item' onchange='getColorsDropDown(this.value, <?php echo $_GET['sheet']; ?>, <?php echo $_GET['jobid']; ?>);'>
<?php

while(list($brand_id, $brand)=mysqli_fetch_row($res_brands))
{
  $sql = "select material_id, material".
         " from materials".
         " where category_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$cat)."' and brand_id='".$brand_id."' and account_id='".$_SESSION['ao_accountid']."' and active=1".
         " order by material asc";
  $res_materials = DBUtil::query($sql);
  if(mysqli_num_rows($res_materials)!=0)
  {
?>
        <option value=''>** <?php echo $brand." (".mysqli_num_rows($res_materials).")"; ?> **</option>
<?php
    while(list($material_id, $material)=mysqli_fetch_row($res_materials))
    {
      $material = prepareText($material);
?>
        <option value='<?php echo $material_id; ?>'><?php echo $material; ?></option>
<?php
    }
?>
<?php
  }
}
$sql = "select material_id, material".
       " from materials".
       " where category_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$cat)."' and brand_id='-1' and account_id='".$_SESSION['ao_accountid']."' and active=1".
       " order by material asc";
$res_materials = DBUtil::query($sql);
if(mysqli_num_rows($res_materials)!=0)
{
?>
        <option value=''>** Varies<?php " (".mysqli_num_rows($res_materials).")"; ?> **</option>
<?php
  while(list($material_id, $material)=mysqli_fetch_row($res_materials))
  {
    $material = prepareText($material);
?>
        <option value='<?php echo $material_id; ?>'><?php echo $material; ?></option>
<?php
  }
?>
<?php
}
?>
      </select>