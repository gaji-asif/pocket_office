<?php
require __DIR__ . '/vendor/autoload.php';
include '../../includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1) {
  die('Insufficient Rights');
}


function deleteMail($id)
{
   $query = "UPDATE gmail_import SET delete_status=1 WHERE id=$id";
    $result = DBUtil::queryToArray($query);
    return true;
}



if (isset($_GET['id']) !== '') {
   $response = new stdClass();
    $ids = $_GET['id'];
    
    foreach($ids as $id)
    {
       $result = deleteMail($id);
    }
  if($result)
  {
      $response->status = 1;
      $response->msg = 'Mail deleted.';
      $response->result = array();
      header("Content-type:application/json");
      echo json_encode($response);
  }
}
else
{
  $response = new stdClass();
  $response->status = 0;
  $response->msg = 'Mail Id and  is required.';
  $response->result = array();
  echo json_encode($response);
}