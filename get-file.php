<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();

$type = RequestUtil::get('type');
$id = RequestUtil::get('id');

if($type === 'uploads') {
    $sql = "SELECT filename
            FROM uploads
            WHERE upload_id = '$id'
                AND account_id = '{$_SESSION['ao_accountid']}'
            LIMIT 1";
    $basePath = UPLOADS_PATH;
} else if($type === 'docs') {
    $sql = "SELECT filename
            FROM documents
            WHERE document_id = '$id'
                AND account_id = '{$_SESSION['ao_accountid']}'
            LIMIT 1";
    $basePath = DOCUMENTS_PATH;
}

$results = DBUtil::query($sql);

//record not found
if(!DBUtil::hasRows($results)) { die(); }

$fileData = DBUtil::fetchAssociativeArray($results);
$path = $basePath . '/' . $fileData['filename'];

//file does not exists
if(!file_exists($path)) { die(); }

$fileHandle = fopen($path, 'rb');
header("Content-Type: " . mime_content_type($path));
header("Content-Length: " . filesize($path));
fpassthru($fileHandle);

exit;