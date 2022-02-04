<?php 
include '../common_lib.php';
UserModel::isAuthenticated();

$page = $_GET['p'];
?>

<ul class="unstyled">
    <li>Dashboard</li>
<?php
$navigationArray = UIModel::getNavList();
foreach($navigationArray as $navigationItem) {
    $pieces = explode('.', $source);
?>
    <li><?=$navigationItem['title']?></li>   
<?php
}
if($_SESSION['ao_founder'] == 1) {
?>
    <li>System</li>
<?php
}
?>
</ul>
