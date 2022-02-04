<?php
require __DIR__ . '/vendor/autoload.php';
include '../../includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1) {
  die('Insufficient Rights');
}

function getEmailByLabel($labelName)
{
  $getEmailList = new stdClass();
  if ($labelName !== '') {
    $currentUser = $_SESSION['ao_userid'];
    $resultsPerPage = 10;
    
    if(!empty($_GET['order']))
    {
        $order_by = 'ORDER BY create_date DESC';
    }
    else
    {
         $order_by = 'ORDER BY create_date ASC';
    }
    
    $query = "SELECT id, message_id, thread_id, label_name, from_name, to_mail, subject, snippet, mail_date
        FROM gmail_import WHERE label_name = '{$_GET['label_name']}' AND delete_status=0 $order_by";
    $result = DBUtil::queryToArray($query);
    $numberOfPage = ceil(count($result) / $resultsPerPage);
    if (!isset($_GET['page'])) {
      $page = 1;
    } else {
      $page = $_GET['page'];
    }
    $pageFirstResult = ($page-1) * $resultsPerPage; 
    
 
    
    $sql = "SELECT id, message_id, thread_id, label_name, from_name, to_mail, subject, snippet, mail_date
        FROM gmail_import WHERE label_name = '{$_GET['label_name']}' AND delete_status=0  $order_by LIMIT $pageFirstResult, $resultsPerPage";
    $tasksData = DBUtil::queryToArray($sql);
    $getEmailList->data = $tasksData;
    $getEmailList->page = $page;
    $getEmailList->numberOfPage = count($result);
    $getEmailList->count = count($tasksData);
    $getEmailList->labels = getLabelsByUser();
  } else {
    $getEmailList->error = true;
    $getEmailList->msg = 'No record found';
  }
  return $getEmailList;
}

function getLabelsByUser()
{
  $getLabels = new stdClass();
    
    if(!empty($_GET['order']))
    {
        $order_by = 'ORDER BY create_date DESC';
    }
    else
    {
         $order_by = 'ORDER BY create_date ASC';
    }
    
  $currentUser = $_SESSION['ao_userid'];
  $sql = "SELECT label_name FROM gmail_import WHERE delete_status=0 GROUP BY label_name $order_by";
  $tasksData = DBUtil::queryToArray($sql);
  $getLabels->data = $tasksData;
  $getLabels->count = count($tasksData);
  return $getLabels;
}

if (isset($_GET['label_name']) && $_GET['label_name'] !== '') {
  $response = new stdClass();
  $response = getEmailByLabel($_GET['label_name']);
  header("Content-type:application/json");
  echo json_encode($response);
}
if (isset($_GET['get_labels']) && $_GET['get_labels'] === "1") {
  $response = new stdClass();
  $response = getLabelsByUser();
  header("Content-type:application/json");
  echo json_encode($response);
}
