<?php
include '../common_lib.php';
UserModel::isAuthenticated();

$job_id = $_GET['id'];

$sql = "select bookmark_id from bookmarks where user_id='" . $_SESSION['ao_userid'] . "' and job_id='" . mysqli_real_escape_string(DBUtil::Dbcont(),$job_id) . "' limit 1";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res) == 0) {
    $sql = "insert into bookmarks values(0, '" . mysqli_real_escape_string(DBUtil::Dbcont(),$job_id) . "', '" . $_SESSION['ao_userid'] . "', now())";
}
else {
    $sql = "delete from bookmarks where job_id='" . mysqli_real_escape_string(DBUtil::Dbcont(),$job_id) . "' and user_id='" . $_SESSION['ao_userid'] . "' limit 1";
}
DBUtil::query($sql);
?>