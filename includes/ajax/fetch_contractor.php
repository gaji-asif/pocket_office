<?
include '../common_lib.php';
$firstLast = UIUtil::getFirstLast();
$term = $_GET[ "term" ];
$accountMetaData = AccountModel::getAllMetaData();
$salesmen = UserModel::getAllByLevel($accountMetaData['assign_task_contractor_user_dropdown']['meta_value'], FALSE, $firstLast);
$companies=array();
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