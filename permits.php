<?php
include 'includes/common_lib.php';
echo ViewUtil::loadView('doc-head');
$page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($page->source);
ModuleUtil::checkAccess('view_jobs', TRUE, TRUE);
echo $page->getHeader();

?>
<div class="list-table-container">
    <table class="table table-bordered table-condensed table-striped">
        <thead>
            <tr>
                <th>Permit Number</th>
                <th>Job Number</th>
                <th>Customer</th>
                <th>Address</th>
                <th>Jurisdiction</th>
                <th>Expires</th>
            </tr>
        </thead>
        <tbody id="permits-container"></tbody>
    </table>
</div>
<script>
    function fetchPermitList(queryString) {
        Request.make('<?=AJAX_DIR?>/get_permitlist.php?' + queryString || '', 'permits-container', true, true);
//        $('.table').stupidtable();
    }
    $(function() {
        fetchPermitList();
    });
</script>
</body>
</html>
