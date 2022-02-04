<?php
include '../common_lib.php'; 
foreach (glob( '../../docs/*.zip') as $del) 
       {
         unlink($del);
       }
$data = json_decode(stripslashes($_POST['data']));
$zip = new ZipArchive();
$zipname=date('Y-M-D h:i:s.').'zip';
 $zip_name = DOCUMENTS_PATH. '/'.$zipname; // Zip name
$zip->open($zip_name,  ZipArchive::CREATE);
foreach ($data as $file)
{
       $name = basename($file);
        $path =  ROOT_PATH. '/uploads/'.$name ;
       if(file_exists($path))
           {
              $zip->addFromString(basename($path),  file_get_contents($path)); 
              $flag=1;
           }
    
}
if( $flag==1)
{
    echo ROOT_DIR."/docs/".$zipname;
}
else
{
    echo 'error';
}
$zip->close();
?>
