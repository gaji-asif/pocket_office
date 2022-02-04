<?php 
include '../common_lib.php'; 

if(!UserModel::loggedIn())
  echo 'out';
?>