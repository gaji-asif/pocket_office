<?php

include '../common_lib.php'; 

$account = '';//RequestUtil::get('a');
$email = RequestUtil::get('e');
$results = AuthModel::processForgotPassword($email, $account);

if($results) {
	$viewData = DBUtil::fetchAssociativeArray($results);

	//build body
	$viewData['account_url'] = ACCOUNT_URL;
	$body = ViewUtil::loadView('mail/password-recovery', $viewData);

    //get new mail object
	$mail = new PHPMailer();

	//add from, to, subject, body
	$mail->SetFrom(ALERTS_EMAIL, APP_NAME . " Alerts");
	$mail->AddAddress($email, "$fname $lname");
	$mail->Subject = APP_NAME . ': Password Recovery';
	$mail->MsgHTML($body);

    if($mail->Send()) {
		$return_message = array('message' => 'Password has been sent to your email');
	}  else {
		$return_message = array('message' => 'Email failed to send');
	}
} else {
	LogUtil::getInstance()->logNotice("Failed password recovery - Invalid credentials: $account, $email");
	$return_message = array('message' => 'Account was not found');
}

echo json_encode($return_message);