<?php
include '../common_lib.php'; 
$jobId = RequestUtil::get('job_id');
$sheetId = RequestUtil::get('sheet_id');
$myJob = new Job($jobId, FALSE);
if(!$myJob->exists()) {
    UIUtil::showModalError('Job not found!');
}
ModuleUtil::checkJobModuleAccess('job_material_sheet', $myJob, TRUE);

$mySheet = new Sheet($sheetId, FALSE);
if(!$mySheet->exists()) {
    UIUtil::showModalError('Material sheet not found!');
}

if($sheetId!='' && $_GET['i']!='' && $_GET['q']!='' && $_GET['q']!=0)
{
    $i = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['i']);
    $q = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['q']);
    $c = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['c']);
    if(empty($c))
       $c=0;

  $sql = "insert into sheet_items (`sheet_id`, `user_id`,`material_id`,`color_id`,`quantity`,`timestamp`) values('".$sheetId."', '".$_SESSION['ao_userid']."', '".$i."', '".$c."', '".$q."', now())";
  
  DBUtil::query($sql);

  $sql = "select job_id from sheets where sheet_id='".$sheetId."' limit 1";
  $res = DBUtil::query($sql);
  list($id)=mysqli_fetch_row($res);

  JobModel::saveEvent($id, "Added Materials");
}
else if($sheetId!='')
{
  $sql = "select job_id from sheets where sheet_id='".$sheetId."' limit 1";
  $res = DBUtil::query($sql);
  list($id)=mysqli_fetch_row($res);
}

$sheet_item_id = $_GET['item'];
$qty = $_GET['qty'];
$action = $_GET['action'];

if($sheet_item_id!='')
{
  if($action=='del')
  {
    $sql = "delete from sheet_items where sheet_item_id='".$sheet_item_id."' limit 1";
    DBUtil::query($sql);
    JobModel::saveEvent($id, "Deleted Materials");
  }
  else if($qty>0)
  {
    $sql = "update sheet_items set quantity='".$qty."' where sheet_item_id='".$sheet_item_id."' limit 1";
    DBUtil::query($sql);
    JobModel::saveEvent($id, "Modified Material Quantity");
  }
}

echo $mySheet->getMaterialsList();