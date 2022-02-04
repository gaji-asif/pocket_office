<?php

include '../../common_lib.php';

$user_id = $_SESSION['ao_userid'];

$id = RequestUtil::get('id');
$type = RequestUtil::get('type');

$myEmail = new Email($id);

$return_message = '';
if($myEmail->is_shared!=$type)
{
    $sql = "UPDATE gmail_import SET is_shared='".$type."' where id=".$id."";
    if(DBUtil::query($sql))
    {
        $return_message = 'success';
    }
    else
    {
        $return_message = 'error';
    }
}
else
{
    $return_message = 'exist';
}


echo $return_message;