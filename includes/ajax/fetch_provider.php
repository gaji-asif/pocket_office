<?
include '../common_lib.php';
$term = $_GET[ "term" ];
$providers = InsuranceModel::getAllProviders();
$result = array();
foreach($providers as $provider) {
   $companyLabel = $provider[ "insurance" ];
   if ( strpos( strtoupper($companyLabel), strtoupper($term) )!== false ) {
      $company=array( "label" => $provider['insurance'], "value" => $provider['insurance_id'] );
      array_push( $result, $company );
   }
}
echo json_encode( $result );
?>