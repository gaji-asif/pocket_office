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
    $resultsPerPage = 5;

    $where = "t1.label_name = '{$_GET['label_name']}' AND t1.user_id = $currentUser AND delete_status=0";
    if($_GET['terms'])
    {
       $where .=" AND delete_status=0 AND (t1.from_name like '%{$_GET['terms']}%' OR t1.to_mail like '%{$_GET['terms']}%' OR t1.subject like '%{$_GET['terms']}%' OR t1.snippet like '%{$_GET['terms']}%' OR t2.job_number like '%{$_GET['terms']}%')";
    }

    $query = "SELECT count(t1.id) as total_rows
            FROM gmail_import as t1
            LEFT JOIN jobs as t2 ON t2.job_id=t1.job_id
            WHERE $where";
    //echo $query;die;
    $result = DBUtil::queryToArray($query);
    
    $tot_rows = (!empty($result) && $result[0])?$result[0]['total_rows']:0;
   
    $numberOfPage = ceil($tot_rows / $resultsPerPage);
    if (!isset($_GET['page'])) {
      $page = 1;
    } else {
      $page = $_GET['page'];
    }
    $pageFirstResult = ($page-1) * $resultsPerPage; 

    $sql = "SELECT t1.id, t1.message_id, t1.thread_id, t1.label_name, t1.from_name, t1.to_mail, t1.subject, t1.snippet, t1.mail_date
        FROM gmail_import as t1
        LEFT JOIN jobs as t2 ON t2.job_id=t1.job_id
        WHERE $where
        LIMIT $pageFirstResult, $resultsPerPage";
    $tasksData = DBUtil::queryToArray($sql);
    $getEmailList->data = $tasksData;
    $getEmailList->page = $page;
    $getEmailList->numberOfPage = $numberOfPage;
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

  $currentUser = $_SESSION['ao_userid'];
  $sql = "SELECT label_name FROM gmail_import WHERE user_id = $currentUser AND delete_status=0 GROUP BY label_name";
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