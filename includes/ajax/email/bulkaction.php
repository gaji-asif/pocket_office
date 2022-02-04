<?php

include '../../common_lib.php';

$user_id = $_SESSION['ao_userid'];

$type = RequestUtil::get('type');
$rows_arr = RequestUtil::get('rows_arr');
$return_message = '';
$id_arr = implode(',',$rows_arr);
if($type=='ar')
{
    $sql = "UPDATE gmail_import SET delete_status=2 where id IN(".$id_arr.") AND user_id=".$user_id;
    if(DBUtil::query($sql))
    {
        $return_message = 'Success: Selected Email Archived!';
    }
    else
    {
        $return_message = 'error';
    }
}
elseif($type=='dl')
{
    $sql = "UPDATE gmail_import SET delete_status=1 where id IN(".$id_arr.") AND user_id=".$user_id;
    if(DBUtil::query($sql))
    {
        $return_message = 'Success: Selected Email Deleted!';
    }
    else
    {
        $return_message = 'error';
    }
}
elseif($type=='rd')
{
    $sql = "UPDATE gmail_import SET is_read=1 where id IN(".$id_arr.") AND user_id=".$user_id;
    if(DBUtil::query($sql))
    {
        $return_message = 'Success: Selected Email Marked as Read!';
    }
    else
    {
        $return_message = 'error';
    }
}
elseif($type=='ur')
{
    $sql = "UPDATE gmail_import SET is_read=0 where id IN(".$id_arr.") AND user_id=".$user_id;
    if(DBUtil::query($sql))
    {
        $return_message = 'Success: Selected Email Marked as Unread!';
    }
    else
    {
        $return_message = 'error';
    }
}
elseif($type=='s')
{
    $sql = "UPDATE gmail_import SET is_shared='s' where id IN(".$id_arr.") AND user_id=".$user_id;
    if(DBUtil::query($sql))
    {
        $return_message = 'Success: Selected Email has been shared with other user of your company!';
    }
    else
    {
        $return_message = 'error';
    }
}
elseif($type=='p')
{
    $sql = "UPDATE gmail_import SET is_shared='p' where id IN(".$id_arr.") AND user_id=".$user_id;
    if(DBUtil::query($sql))
    {
        $return_message = 'Success: Selected Email has been made private!';
    }
    else
    {
        $return_message = 'error';
    }
}

echo $return_message;