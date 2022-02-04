<?
include '../common_lib.php';
$firstLast = UIUtil::getFirstLast();
//$salesmen = UserModel::getAllByLevel($accountMetaData['add_job_salesman_user_dropdown']['meta_value'], FALSE, $firstLast);
$accountMetaData = AccountModel::getAllMetaData();
$salesmen = UserModel::getAllByLevel($accountMetaData['add_job_salesman_user_dropdown']['meta_value'], FALSE, $firstLast);
$companies=array();
//print_r($salesmen );die;
$term = $_GET[ "term" ];
foreach($salesmen as $salesman) {
    $companies[]=array( "label" => $salesman['select_label'], "value" => $salesman['user_id'] );
}
$result = array();
foreach ($companies as $company) {
   $companyLabel = $company[ "label" ];
   if ( strpos( strtoupper($companyLabel), strtoupper($term) )!== false ) {
      array_push( $result, $company );
   }
}
echo json_encode( $result );
?>