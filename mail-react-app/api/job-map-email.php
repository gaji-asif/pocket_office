<?php
require __DIR__ . '/vendor/autoload.php';
include '../../includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1) {
  die('Insufficient Rights');
}


function updateEmailJob($id,$job_id)
{
   $query = "UPDATE gmail_import SET job_id=$job_id WHERE id=$id";
    $result = DBUtil::queryToArray($query);
    return true;
}



if (isset($_GET['job_id']) && $_GET['id'] !== '') {
    $job_id =  $_GET['job_id'];
    $id =  $_GET['id'];
  $response = new stdClass();
  $result = updateEmailJob($id,$job_id);
  if($result)
  {
      $response->status = 1;
      $response->msg = 'Jod Id updated.';
      $response->result = array();
      header("Content-type:application/json");
      echo json_encode($response);
  }
}
else
{
  $response = new stdClass();
  $response->status = 0;
  $response->msg = 'Jod Id and E-mail Id is required.';
  $response->result = array();
  echo json_encode($response);
}