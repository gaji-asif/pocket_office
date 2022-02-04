<?php
include '../common_lib.php';
if(!UserModel::loggedIn()) {
    die();
}
$concatSql = UIUtil::getFirstLast() ? "concat(fname, ' ', lname)" : "concat(lname, ', ', fname)";
$sql = "SELECT $concatSql AS name, username
        FROM users
        WHERE account_id = '{$_SESSION['ao_accountid']}'";
JsonUtil::out(DBUtil::queryToArray($sql));