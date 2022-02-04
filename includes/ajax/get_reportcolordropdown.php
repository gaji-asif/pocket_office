<?php 

include '../common_lib.php'; 
pageSecure('reports.php');
  
$item = $_GET['item'];
  
$sql = "select color_id, color from colors where material_id='".mysqli_real_escape_string(DBUtil::Dbcont(),$item)."' order by color asc";
$res_brands = DBUtil::query($sql);

$color_dropdown_array = array();
while(list($color_id, $color) = mysqli_fetch_row($res_brands))
{
	$color_dropdown_array[] = array('color_id' => $color_id, 'color' => $color);
}

if(empty($color_dropdown_array))
{
	$color_dropdown_array[] = array('color_id' => '', 'color' => 'No Color');
}

echo json_encode($color_dropdown_array);