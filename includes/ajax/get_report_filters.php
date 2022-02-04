<?php
include '../common_lib.php';
if(!UserModel::checkNavAccess('reports.php', TRUE)) {
    return;
}

$type = RequestUtil::get('type');
if(!$type) {
    echo AlertUtil::generate(array("Cannot find '$type' filters"));
    return;
}

echo ViewUtil::loadView("report-filters-$type");
