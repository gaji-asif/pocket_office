<?php
include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);
$id = RequestUtil::get('id');
?>
<?=ViewUtil::loadView('doc-head')?>
<h1 class="page-title"><i class="icon-bullhorn"></i><?=$this_page->title;?></h1>
<?php
if(ModuleUtil::checkAccess('write_announcements')) {
?>
<div class="btn-group pull-right page-menu">
    <div rel="open-modal" data-script="add_announcement.php" class="btn btn-success" title="Add announcement" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>
<?php
}
?>

<?php
if(ModuleUtil::checkAccess('view_announcements')) {
?>
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr>
        <td class="text-right list-search">
            <form method="post" rel="filter-list-form" action="<?=AJAX_DIR?>/get_announcementlist.php" data-destination="announcements-container">
            <input type="text" id="search" class="list-filter-input" value="">
            <i class="icon-search"></i>
            </form>
        </td>
    </tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
<?=ViewUtil::loadView('announcement-header')?>
    <tr>
        <td id="announcements-container"></td>
    </tr>
</table>
<script type='text/javascript'>
    Request.make('<?=AJAX_DIR?>/<?=$id ? "get_announcement.php?id=$id" : 'get_announcementlist.php'?>', 'announcements-container', true, true);
</script>
<?php
} else {
    echo ModuleUtil::showInsufficientRightsAlert('view_announcements', TRUE);
}
?>
</body>
</html>
