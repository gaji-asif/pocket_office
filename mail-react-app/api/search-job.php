<?php
require __DIR__ . '/vendor/autoload.php';
include '../../includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1) {
  die('Insufficient Rights');
}


function getEmailByLabel($job_no)
{
  $getLabels = new stdClass();

  $sql = "SELECT * FROM `job_email` WHERE job_number LIKE '%$job_no%'";
  
//   echo $sql;die;
  $tasksData = DBUtil::queryToArray($sql);
  $getLabels->data = $tasksData;
  $getLabels->count = count($tasksData);
  return $getLabels;
}

if (isset($_GET['job_no'])  !== '') {
  $response = new stdClass();
  $response = getEmailByLabel($_GET['job_no']);
  header("Content-type:application/json");
  echo json_encode($response);
}