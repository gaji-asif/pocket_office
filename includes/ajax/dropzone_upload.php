<?php
set_time_limit(0);
include '../common_lib.php';

//check for required post data
$itemType = RequestUtil::get('item_type');
if(!$itemType || empty($_FILES)) {
    JsonUtil::error('Upload failed');
    die();
}

//get data
$itemId = RequestUtil::get('item_id');;
$file = MapUtil::get($_FILES, 'files');
$fileName = mysqli_real_escape_string(DBUtil::Dbcont(),MapUtil::get($file, 'name'));
$fileSizeByte = MapUtil::get($file, 'size');
$fileSize = formatSizeUnits($fileSizeByte);
//get extension
$ext = explode('.', $fileName);
$ext = end($ext);

//new filename
$newFilename = md5(mt_rand() . time()) . ".$ext";

switch($itemType) {
    case 'job_file':
        if(!ModuleUtil::checkAccess('upload_job_file') || !isset($_POST['item_id'])) {
            JsonUtil::error('Insufficient permissions');
            die();
        }
        //set upload path
        $path = UPLOADS_PATH . "/$newFilename";
        
        //set query
        $sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
                VALUES ('$itemId', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$newFilename', '$fileName', now())";
        //echo $sql;die;
        //set callback url
        $callbackUrl = AJAX_DIR . "/get_single_job_upload.php?id=$itemId&upload_id=";
        
        break;
    case 'document':
        if(!ModuleUtil::checkAccess('upload_document')) {
            JsonUtil::error('Insufficient permissions');
            die();
        }
        //set upload path
        $path = DOCUMENTS_PATH . "./$newFilename";
        
        //set query
        $sql = "";
        
        //set callback url
        $callbackUrl = "";
        
        break;
    default:
        die();
        break;
}

if($fileSizeByte > 819200)
{
        if(compress($file['tmp_name'], $path, 10)) {  //storeUploadedFile($file['tmp_name'], $path)
            if(DBUtil::query($sql)) {
                $insertId = DBUtil::getInsertId();
                setUploadMeta($insertId, $itemType, 'upload_mechanism', 'dropzone');
                
                $callbackUrl .= $insertId;
                JsonUtil::out(array(
                    'success' => 'Upload successful',
                    'callback_url' => $callbackUrl
                ));
            }
        }
        else {
            JsonUtil::error('Upload failed');
        }
}else{
    
        if(storeUploadedFile($file['tmp_name'], $path)){
            if(DBUtil::query($sql)) {
                $insertId = DBUtil::getInsertId();
                setUploadMeta($insertId, $itemType, 'upload_mechanism', 'dropzone');
                
                $callbackUrl .= $insertId;
                JsonUtil::out(array(
                    'success' => 'Upload successful',
                    'callback_url' => $callbackUrl
                ));
            }
        }
        else {
            JsonUtil::error('Upload failed');
        }  
    
}


function compress($source, $destination, $quality) {
        $info = getimagesize($source);
        $flag=0;
    if ($info['mime'] == 'image/jpeg') 
    {
        $image = imagecreatefromjpeg($source);
        $flag=1;
    }

    elseif ($info['mime'] == 'image/gif') 
    {
         $image = imagecreatefromgif($source);
         $flag=1;
    }

    elseif ($info['mime'] == 'image/png') 
    {
         $image = imagecreatefrompng($source);
         $flag=1;
    }
    if($flag==1)
    {
        $imgResized = imagescale($image, 1512, 1134, $quality);
        imagejpeg($imgResized, $destination);
        return $destination;
    }
    else
    {
        return storeUploadedFile($source, $destination);
    }
}



//filesize fetch 
function formatSizeUnits($bytes)
{
        $bytes = number_format($bytes / 1024, 2);
        return floor($bytes);
}
?>
 