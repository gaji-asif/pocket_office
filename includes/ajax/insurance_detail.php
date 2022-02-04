<?php
include '../common_lib.php'; 
$insurance_id=$_GET['insurance_id'];

//echo $insurance_id; 

$providers_details = InsuranceModel::getProviderById($insurance_id);
//print_r($providers_details);

$ph = $providers_details['phone_no'];
$fax = $providers_details['fax_no'];
$mail = $providers_details['email'];
$commment = $providers_details['commment'];

echo " <b>Phone:</b> $ph  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Fax:</b> $fax  &nbsp;&nbsp;&nbsp;&nbsp;&nbsp; <b>Mail:</b> $mail <br><br><b>Comment:</b> $commment";

?>