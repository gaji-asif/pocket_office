<?php
require __DIR__ . '/vendor/autoload.php';
include '../../includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1) {
  die('Insufficient Rights');
}


$id = RequestUtil::get('id');
$action = RequestUtil::get('action');
$job_ids = RequestUtil::get('job_id');
$myJob = new Job($job_ids);
$user = UserModel::get($myJob->user_id);
$from_email = EMAIL_REPLY_TO; //$user['email'];
$from_name = $user['fname'].' '.$user['lname'];
// print_r(RequestUtil::getAll());

$errors = array();
$success = array();

if($action=='send')
{
  if(!empty($_POST['email_note']))
  {   
      $mail = new PHPMailer();
      $email_note       = RequestUtil::get('email_note');
      $email_subject    = RequestUtil::get('email_subject');
      $email_send_to    = RequestUtil::get('email_send_to');
      $email_files      = $_FILES['email_files'];
      $files            = '';
      if (!empty($email_files)) {
        for($i=0; $i < count($email_files['name']); $i++) { 
            $name = $email_files['name'][$i];
            $tmp_name = $email_files['tmp_name'][$i];
            $pieces = explode('.', $name);
            $new_filename = md5(mt_rand() . time()) . "." . end($pieces);
            $new_path = EMAIL_ATTACHMENT . $new_filename;
            if (move_uploaded_file($tmp_name, $new_path)) {
                $files .= EMAIL_ATTACHMENT_URL.$new_filename.',';
                $mail->addAttachment($new_path, $new_filename);
            }
        }
        $files = rtrim($files, ',');
      }
      //Recipients
      $mail->setFrom($from_email, $from_name);
      $email_send = '';
      foreach ($email_send_to as $key => $value) {
        $mail->addAddress( $value );               // Name is optional
        $email_send .= $value.',';
      }
      $email_send = rtrim($email_send, ',');
      $mail->addReplyTo(EMAIL_REPLY_TO, $from_name.' via Xactbid');
      // Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $subject = $mail->Subject = '[Contact #'.$myJob->job_number.'] '.$email_subject;
      $mail->MsgHTML('<p>'.$email_note.'</p>');
      if ($mail->send()) {
        $response = new stdClass();
        $response = $email_send_to;
        header("Content-type:application/json");
        echo json_encode($response);
      } else {
        $errors[] = 'some error. please try again.';
      }
  }
}