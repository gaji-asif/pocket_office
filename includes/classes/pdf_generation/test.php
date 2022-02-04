<?php
define('FPDF_FONTPATH','/home/roofing/www/assure/includes/classes/pdf_generation/font/');
//above line is import to define, otherwise it gives an error : Could not include font metric file
require('fpdf.php');



$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','BU',15);
$pdf->Cell(40,10,'Hello World!');
$pdf->Output();
?>