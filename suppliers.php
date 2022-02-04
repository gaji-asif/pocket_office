<?php

include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);

?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-truck"></i><?php echo $this_page->title; ?></h1>
<?php
if(ModuleUtil::checkAccess('modify_suppliers'))
{
?>
<div class="btn-group pull-right page-menu">
    <div rel="open-modal" data-script="add_supplier.php" class="btn btn-success" title="Add supplier" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>
<?php
}
?>
            
<?php
if(ModuleUtil::checkAccess('view_customers'))
{
?>
<div class="list-table-container">
    <table class="table table-bordered table-condensed table-hover table-padded table-striped">
        <thead>
            <tr>
                <th data-sort="string">Supplier</th>
                <th data-sort="string">Contact</th>
                <th data-sort="string">Phone</th>
                <th data-sort="string">Fax</th>
                <th data-sort="string">Email</th>
            </tr>
        </thead>
        <tbody id="suppliers-list"></tbody>
    </table>
</div>
<script type="text/javascript">
$(function() {
    Request.make('<?=AJAX_DIR?>/get_suppliers.php', 'suppliers-list', true, true);
//    $('.table').stupidtable();
});
</script>
<?php
}
?>
</body>
</html>
