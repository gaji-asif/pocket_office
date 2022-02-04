<?php
include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);
$id = RequestUtil::get('id');
?>
<?=ViewUtil::loadView('doc-head')?>
<span id="notes"></span>
<h1 class="page-title"><i class="icon-book"></i><?=$this_page->title;?></h1>
<?php
if(ModuleUtil::checkAccess('view_customers')) {
?>
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
    <tr valign='middle'>
        <td>
            <select id="sort" class="list-filter-input">
                <option value='order by c.lname asc'>Last Name A-Z</option>
                <option value='order by c.lname desc'>Last Name Z-A</option>
                <option value='order by c.timestamp desc'>Newest First</option>
                <option value='order by c.timestamp asc'>Oldest First</option>
            </select>&nbsp;
            <input type="button" value="Sort" rel="filter-list-btn">
        </td>
        <td class="text-right list-search">
            <form method="post" rel="filter-list-form" action="<?=AJAX_DIR?>/get_customerlist.php" data-destination="customerscontainer">
            <input type="text" id="search" class="list-filter-input" value="">
            <i class="icon-search"></i>
            </form>
        </td>
    </tr>
</table>
<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
<?=ViewUtil::loadView('customer-header')?>
    <tr>
        <td id="customerscontainer"></td>
    </tr>
    <tr><td colspan=2>&nbsp;</td></tr>
</table>
<script type="text/javascript">
$(document).ready(function(){
<?php
	if($id) {
?>		
    Request.make('<?=AJAX_DIR?>/get_notes.php?type=customers&id=<?php echo $id; ?>', 'notes', false, true);
    Request.make('<?=AJAX_DIR?>/get_customer.php?id=<?php echo $id; ?>', 'customerscontainer', true, true);
<?php
	} else {
?>
    Request.make('<?=AJAX_DIR?>/get_customerlist.php', 'customerscontainer', true, true);
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
