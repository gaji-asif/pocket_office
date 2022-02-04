<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('job_material_sheet'))
  die("Insufficient Rights");

$item = $_GET['item'];
if($item=='')
  die("No Item Defined");

$sql = "select color_id, color from colors where material_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$item)."' order by color asc";
$res_brands = DBUtil::query($sql);

?>
      <select id='color' class="form-control sm">
        <option value=''>No Color</option>
<?php

while(list($color_id, $color)=mysqli_fetch_row($res_brands))
{
?>
        <option value='<?php echo $color_id; ?>'><?php echo $color; ?></option>
<?php
}

$sql = "select units.unit, materials.info from units, materials where units.unit_id=materials.unit_id and materials.material_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$item)."' limit 1";
$res_unit = DBUtil::query($sql);
list($unit, $info)=mysqli_fetch_row($res_unit)
?>
      </select>
      <input class="form-control sm" type="text" size=1 id='qty' value='0' onclick='this.value="";'> <span class='smallnote'><b>Unit:</b> <?php echo $unit; ?></span>
      <input type='button' value='Add' onclick='addMaterial(<?php echo $_GET['sheet']; ?>, <?=$_GET['jobid']?>);'>
      <br /><b>Description:</b> <?=prepareText($info)?>