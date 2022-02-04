<?
include '../common_lib.php';
$term = $_GET[ "term" ];
$getAllJuri = CustomerModel::getAllJurisdictions();
$result = array();
foreach($getAllJuri as $juri) {
   $companyLabel = $juri[ "location" ];
   if ( strpos( strtoupper($companyLabel), strtoupper($term) )!== false ) {
      $company=array( "label" => $juri['location'], "value" => $juri['jurisdiction_id'] );
      array_push( $result, $company );
   }
}
echo json_encode( $result );
?>