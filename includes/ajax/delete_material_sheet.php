<?php
include '../common_lib.php';
if(!UserModel::loggedIn()) {
	JsonUtil::error('Unauthorized');
	die();
}

//get material sheet by id
$mySheet = JobUtil::getMaterialSheetById(RequestUtil::get('sheetid'));

//get job by id
$myJob = new Job(MapUtil::get($mySheet, 'job_id'));

if (!$mySheet || !$myJob->exists()) {
    JsonUtil::error('Invalid reference');
    die();
}

if(!ModuleUtil::checkJobModuleAccess('delete_material_sheet', $myJob)) {
    JsonUtil::error('Invalid permissions');
    die();
} else {
    //build and execute query to delete sheet and sheet data
    $sql = "DELETE FROM sheets
			WHERE sheet_id = '{$mySheet['sheet_id']}'
			LIMIT 1";
    $results_sheets = DBUtil::query($sql);
    $sql = "DELETE FROM sheet_items
			WHERE sheet_id = '{$mySheet['sheet_id']}'
			LIMIT 1";
    $results_sheet_items = DBUtil::query($sql);

    if (!$results_sheets || !$results_sheet_items) {
        JsonUtil::error('Operation failed');
    } else {
        JsonUtil::success('Material sheet succesfully removed');
    }
    die();
}