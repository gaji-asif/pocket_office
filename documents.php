<?php

include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);
$id = RequestUtil::get('id');
?>
<?=ViewUtil::loadView('doc-head')?>
<h1 class="page-title"><i class="icon-file-text-alt"></i><?=$this_page->title;?></h1>
<?php
if(ModuleUtil::checkAccess('upload_document')) {
?>
<div class="btn-group pull-right page-menu">
    <div rel="open-modal" data-script="edit_document_groups.php" class="btn" title="Edit document groups" tooltip>
        Groups&nbsp;
        <i class="icon-pencil"></i>
    </div>
    <div rel="open-modal" data-script="upload_document.php" class="btn btn-success" title="Add document" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>
<?php
}
if(ModuleUtil::checkAccess('view_documents')) {
?>
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr valign='middle'>
<?php
$documentGroups = DocumentModel::getAllDocumentGroups();
?>
        <td>
            <select id="document_group_id" class="list-filter-input">
                <option value="">Group</option>
<?php
    foreach($documentGroups as $group) {
?>
                <option value="<?=$group['document_group_id']?>"><?=$group['label']?></option>
<?php
    }
?>
            </select>&nbsp;
            <input type="button" value="Filter" rel="filter-list-btn">&nbsp;
            <input type="button" value="Clear Filters" rel="reset-list-btn">
        </td>
        <td class="text-right list-search">
			<form method="post" rel="filter-list-form" action="<?=AJAX_DIR?>/get_documentlist.php" data-destination="documentscontainer">
			<input type="text" id="search" class="list-filter-input" value="">
            <i class="icon-search"></i>
            </form>
        </td>
    </tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
<?=ViewUtil::loadView('document-header')?>
    <tr>
        <td id="documentscontainer"></td>
    </tr>
</table>
<script>
$(document).ready(function(){
<?php
    if($id) {
?>
    Request.make('<?=AJAX_DIR?>/get_document.php?id=<?=$id?>', 'documentscontainer', true, true);
<?php
    } else {
?>
    Request.make('<?=AJAX_DIR?>/get_documentlist.php', 'documentscontainer', true, true);
<?php
    }
?>
});
</script>
<?php
}
?>
</body>
</html>
