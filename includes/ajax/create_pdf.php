<?php
include '../common_lib.php'; 
$myJob = new Job(RequestUtil::get('job_id'));
$header=array();
$address ='';
$city_state='';
$invoice_header = UserModel::getSalesmanDetails($myJob->salesman_id);
if(count($invoice_header)>0)
{
    $header=$invoice_header[0];
}
$logo = (!empty($header['invoice_logo']))?'/invoice_logo/'.$header['invoice_logo']:'/logos/'.$_SESSION['ao_logo'];

$customer = new Customer($myJob->customer_id);
if(count($customer)>0)
{
    $address = (!empty($customer->get('address')))?$customer->get('address'):'';
    $city_state = (!empty($customer->get('city')))?$customer->get('city'):'';
    $city_state .= (!empty($customer->get('state')))?', '.$customer->get('state'):'';
    $city_state .= (!empty($customer->get('zip')))?' '.$customer->get('zip'):'';
}


$data = json_decode(stripslashes($_POST['data']));


$uploaded_data=array();
$index=0;
foreach ($data as $file)
{       
       $name = basename($file);
       
       $sql ='select title from uploads where user_id='.$_SESSION["ao_userid"].' AND job_id='.$myJob->job_id.' AND filename="'.$name.'"';
       $upload_details = DBUtil::queryToArray($sql);
       
       $path =  UPLOADS_PATH. '/'.$name ;
       $url =  UPLOADS_DIR. '/'.$name ;
       if(file_exists($path))
       {
          $uploaded_data[$index]['file_url'] = $url; 
          $uploaded_data[$index]['file_name'] =$upload_details[0]['title']; 
          $flag=1;
          $index++;
       }
}

/*
$uploaded_data[0] = 'https://www.xactbid.com/workflow/uploads/6503654a6ef7467630a893841919b951.jpg';
$uploaded_data[1] = 'https://www.xactbid.com/workflow/uploads/6503654a6ef7467630a893841919b951.jpg';
$uploaded_data[2] = 'https://www.xactbid.com/workflow/uploads/0ac904c0713419b737ddcd472d87f33c.jpg';
$uploaded_data[3] = 'https://www.xactbid.com/workflow/uploads/35167ea66ca309dea5d958aedd100cf5.jpg';
$uploaded_data[4] = 'https://www.xactbid.com/workflow/uploads/b1e0eebe70de91b3ebcfb0db098d2139.jpg';
$uploaded_data[5] = 'https://www.xactbid.com/workflow/uploads/a557e8f6b6c7c31c09de645b6695f6e7.jpg';
$uploaded_data[6] = 'https://www.xactbid.com/workflow/uploads/05a5dbd481143d0fc0ab7a60e22f106e.jpg';
$uploaded_data[7] = 'https://www.xactbid.com/workflow/uploads/64a04f1424f950425748591b96e4cf0e.jpg';*/
// $_SESSION['ao_logo']
$viewData = array(
  'uploaded_data'=>$uploaded_data,
  'logo' => ROOT_DIR . '/' . $logo,
  'address' => $address,
  'city_state' => $city_state,
  'policy' => MetaUtil::get($myJob->meta_data, 'insurance_policy'),
  'claim' => $myJob->claim,
  'salesman' =>$myJob->salesman_fname." ".$myJob->salesman_lname
);        

$html = ViewUtil::loadView('pdf/uploaded_image_to_pdf', $viewData);

$pdf_title ='Uploaded Image';
$filename = PdfUtil::generatePDFFromHtml($html, $pdf_title, true, UPLOADS_PATH.'/generated_pdf');

$filename=UPLOADS_DIR.'/generated_pdf/'.$filename;

echo $filename;
?>
