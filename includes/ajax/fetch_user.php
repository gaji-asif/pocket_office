<?
include '../common_lib.php';
$term = $_GET[ "term" ];
$firstLast = UIUtil::getFirstLast();
if(MetaUtil::get($accountMetaData, 'show_inactive_users_in_lists') == '1') {
    $usersList = UserModel::getAll(TRUE, $firstLast);
} else {
    $usersList = UserModel::getAll(FALSE, $firstLast);
}

$companies=array();
$term = $_GET[ "term" ];
foreach($usersList as $user) {
    $companies[]=array( "label" => $user['fname']." ".$user['lname'], "value" => $user['user_id'] );
}

$result = array();
foreach ($companies as $company) {
   $companyLabel = $company[ "label" ];
   if ( strpos( strtoupper($companyLabel), strtoupper($term) )!== false ) {
      array_push( $result, $company );
   }
}

$result[] = array(
      "id" => "null",
      "text" => "No Referral Assigned"
   );
echo json_encode( $result );
?>