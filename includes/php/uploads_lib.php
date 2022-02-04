<?php

function storeUploadedFile($tmpFile, $path) {
	//move file    

    return move_uploaded_file($tmpFile, $path);
}

function setUploadMeta($uploadId, $type, $name, $value) {
    //get data
    $uploadId = mysqli_real_escape_string(DBUtil::Dbcont(),$uploadId);
    $type = mysqli_real_escape_string(DBUtil::Dbcont(),$type);
    $name = mysqli_real_escape_string(DBUtil::Dbcont(),$name);
    $value = mysqli_real_escape_string(DBUtil::Dbcont(),$value);
    
    //build and execute query
    $sql = "INSERT INTO upload_meta (upload_id, type, meta_name, meta_value)
            VALUES ('$uploadId', '$type', '$name', '$value')";
    $results = DBUtil::query($sql);
}

/**
 * 
 * @param int $jobId
 * @param string $filename
 * @param string $title
 * @return boolean
 */
function storeUploadData($jobId, $filename, $title = NULL) {
    $sql = "INSERT INTO uploads (job_id, user_id, account_id, '$filename', )
            VALUES ('$jobId', '$filename', '$title')";
    
    return !$result ?: TRUE;
}