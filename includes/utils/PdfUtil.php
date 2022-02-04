<?php

/**
 * @author cmitchell
 */
class PdfUtil extends AssureUtil {
    
    public static function generatePDFFromHtml($html, $title, $saveFile = FALSE, $saveToPath = NULL) {
        //get pdf library
        include(INCLUDES_PATH . '/tcpdf/tcpdf.php');
        include(INCLUDES_PATH . '/tcpdf/config/lang/eng.php');
		
        //create new pdf document
        $pdf = new TCPDF('P', 'pt', 'A4');

        //set document information
        $pdf->SetCreator(APP_NAME);
        $pdf->SetTitle($title);

        // remove default header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // set default monospaced font
        $pdf->SetDefaultMonospacedFont('helvetica');

        //set some language-dependent strings
        $pdf->setLanguageArray($l);

        //set font
        $pdf->SetFont('helvetica', '', 10);

        //add a page
        $pdf->AddPage();

        //generate pdf name
        $pdfName = StrUtil::makeStringMachineSafe($title) . '_' . time() . '.pdf';
       
        //create pdf     
        $pdf->writeHTML($html);   
        //save or output
        if($saveFile == true)
        {
            $pdf->Output($saveToPath . '/' . $pdfName, 'F');
        }
        else
        {
            $pdf->Output($pdfName . '.pdf', 'I');
        }

        return $pdfName;
    }
    
}