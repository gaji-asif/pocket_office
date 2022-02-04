<?php 
include '../common_lib.php';

$account_id = RequestUtil::get('account_id');
$level_id = RequestUtil::get('level_id');
$users = UserModel::getUserByAccount($account_id,$level_id);
			
$user_html='';
foreach($users as $userId => $row) 
{

	$user_html .='<option value="'.$row['user_id'].'" ';

	if($_SESSION['ao_userid'] == $row['user_id'])
		$user_html .=' selected';

    $user_html .=' >'.$row['lname'].','.$row['fname'].' - '.$row['level'];
    $user_html .=' </option>';
}

echo $user_html;exit;