<?php
include '../../common_lib.php'; 
$id = RequestUtil::get('id');
$codegenerator_job_id = RequestUtil::get('codegenerator_job_id');
$data = json_decode(stripslashes($_POST['data']));
$data= implode(",",$data);
$sql = "select tbl_codegenerator_job_id,codegenerator_id,name,attachment,description,attachment_desc from tbl_codegenerator_job where tbl_codegenerator_job_id IN (".$data.")";
$codegenerator = DBUtil::queryToArray($sql);
foreach ($codegenerator as $row)
{
$string.= '<table style="margin-bottom: 50px;" class="data-table-header" cellpadding="0" cellspacing="0" border="0" >
    <tr valign="center">
        <td  style="font-size: 30px;float: left;">'.$row['name'].'</td>
    </tr>
</table>
<table   cellspacing="0" cellpadding="2" style="width: 595pt;" align="center" class="infocontainernopadding">
  
      <tr>
          
          <td >'.$row['description'].'</td>
      </tr>
   
</table>';
}
$string='<html><body><style>.infocontainernopadding img {width: 550pt;}</style>'.$string.'</body></html>';
unlink_recursive("/home/xactbid/public_html/workflow/pdflib/files/", "pdf");
require_once dirname(__FILE__).'/../../../pdflib/vendor/autoload.php';
use Spipu\Html2Pdf\Html2Pdf;
use Spipu\Html2Pdf\Exception\Html2PdfException;
use Spipu\Html2Pdf\Exception\ExceptionFormatter;
try {
    ob_start();
    $content = $string;
    $html2pdf = new Html2Pdf('P',  'A4', 'fr', true, 'UTF-8', 0);
     $html2pdf->pdf->SetDisplayMode('fullpage');
    $html2pdf->setDefaultFont('Arial');
    $html2pdf->setTestTdInOnePage(false);
    $html2pdf->writeHTML($content);
    //$html2pdf->output(date('Y-M-D h:i:s.').pdf, 'F');
    $filename=date('Y-M-D h:i:s.').'pdf';
    $html2pdf->Output('/home/xactbid/public_html/workflow/pdflib/files/'.$filename,'F');
} catch (Html2PdfException $e) {
    $html2pdf->clean();
    $formatter = new ExceptionFormatter($e);
   //echo $formatter->getHtmlMessage();
   $flag='error';
}
if($flag!='error')
echo ROOT_DIR."/pdflib/files/".$filename;

function unlink_recursive($dir_name, $ext) {

    // Exit if there's no such directory
    if (!file_exists($dir_name)) {
        return false;
    }

    // Open the target directory
    $dir_handle = dir($dir_name);

    // Take entries in the directory one at a time
    while (false !== ($entry = $dir_handle->read())) {

        if ($entry == '.' || $entry == '..') {
            continue;
        }

        $abs_name = "$dir_name/$entry";

        if (is_file($abs_name) && preg_match("/^.+\.$ext$/", $entry)) {
            if (unlink($abs_name)) {
                continue;
            }
            return false;
        }

        // Recurse on the children if the current entry happens to be a "directory"
        if (is_dir($abs_name) || is_link($abs_name)) {
            unlink_recursive($abs_name, $ext);
        }

    }

    $dir_handle->close();
    return true;

}
    ?>


