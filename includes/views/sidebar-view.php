<div id="sidebar">
    <ul class="unstyled" id="navlist">
        <li rel="change-frame-location" data-url="dashboard.php">
            <i class="icon-dashboard" title="Dashboard" tooltip></i>
            <span>Dashboard</span>
        </li>
<?php
$navigationArray = UIModel::getNavList();
foreach($navigationArray as $navigationItem) {
    
    $title=$navigationItem['title'];
    if($title=="Jobs Map"||$title=="Materials"||$title=="Suppliers")
    continue;
//    $pieces = explode('.', $source);
?>
        <li rel="change-frame-location" data-url="<?=$navigationItem['source']?>">
            <i class="icon-<?=$navigationItem['icon']?>" title="<?=$navigationItem['title']?>" tooltip></i>
            <span><?=$navigationItem['title']?></span>
        </li>   
<?php
}
if($_SESSION['ao_founder'] == 1) {
?>
        <li rel="change-frame-location" data-url="system.php">
            <i class="icon-cogs" title="System" tooltip></i>
            <span>System</span>
        </li>
<?php
}
?>
        <li rel="toggle-sidebar-width">
            <i class="icon-reorder"></i>
        </li>
    </ul>
</div>
