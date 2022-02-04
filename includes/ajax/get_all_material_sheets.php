<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('job_material_sheet', $myJob);

//get permissions
$canDelete = ModuleUtil::checkJobModuleAccess('delete_material_sheet', $myJob);
?>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Materials Sheets</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td class="infocontainernopadding">
            <div class="btn-list-container clearfix">
				<div rel="change-window-location"
                     data-url="<?=AJAX_DIR?>/add_material_sheet.php?id=<?=$myJob->job_id?>"
                     class="btn btn-success"
                     title="Add Material Sheet" tooltip>
					<i class="icon-plus"></i>
				</div>
<?php
$materialSheets = $myJob->fetchMaterialSheets();
foreach($materialSheets as $sheet) {
?>
				<div class="btn-group" id="sheet-<?=$sheet['sheet_id']?>">
					<div class="btn btn-blue" rel="change-window-location" data-url="<?=AJAX_DIR?>/job_materials.php?job_id=<?=$sheet['job_id']?>&sheet_id=<?=$sheet['sheet_id']?>">
						<?=$sheet['label']?>
					</div>
<?php
    if($canDelete) {
?>
					<div class="btn btn-blue" rel="delete-material-sheet" data-sheet-id="<?=$sheet['sheet_id']?>" ><i class="icon-remove"></i></div>
<?php
    }
?>
				</div>
<?php
}
?>
            </div>
        </td>
    </tr>
</table>
</body>
</html>
