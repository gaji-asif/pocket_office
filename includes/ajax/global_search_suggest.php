<?php
include '../common_lib.php';
$searchTerm = RequestUtil::get('s');
if(!$searchTerm || !UserModel::loggedIn()) {
    die();
}
$terms = explode(' ', trim($searchTerm));

//only one group?
$singleSearch = FALSE;
$searchables = array(
    'jobs',
    'users',
    'customers',
    'documents'
);
if(count($terms) && in_array(strtolower($terms[0]), $searchables)) {
    $singleSearch = TRUE;
    $singleSearchTable = strtolower(array_shift($terms));
}

if(!count($terms)) {
    die();
}
$resultsArray = array();

//jobs
if(ModuleUtil::checkAccess('view_jobs') && (!$singleSearch || ($singleSearch && $singleSearchTable == 'jobs'))) {
    $whereBys = array();
    foreach($terms as $term) {
        $term = trim($term);
        $whereBys[] = "AND (
                            j.job_number LIKE '%$term%'
                            OR c.address LIKE '%$term%' 
                            OR c.fname LIKE '%$term%'
                            OR c.lname LIKE '%$term%'
                            OR c.nickname LIKE '%$term%'
                            OR c.zip LIKE '%$term%'
                            OR j.job_type_note LIKE '%$term%'
                            OR p.number LIKE '%$term%'
                        )";
    }
    
    $jobsExtraSql = '';
    if(moduleOwnership('view_jobs')) {
        $jobsExtraSql = "AND (j.user_id = '{$_SESSION['ao_userid']}' OR j.salesman='{$_SESSION['ao_userid']}')";
    }
    
    $sql = "SELECT j.job_id AS id, CONCAT(j.job_number, ' (', c.lname, ')') AS label, 'jobs.php' AS uri, 'icon-briefcase' as icon
            FROM jobs j
            LEFT JOIN customers c ON (c.customer_id = j.customer_id)
            LEFT JOIN permits p ON (j.job_id = p.job_id)
            WHERE j.account_id = '{$_SESSION['ao_accountid']}'
                " . implode(' ', $whereBys) . "
            $jobsExtraSql
            GROUP BY j.job_id";
    $resultsArray = array_merge($resultsArray, DBUtil::queryToArray($sql));
}

//customers
if(ModuleUtil::checkAccess('view_customers') && (!$singleSearch || ($singleSearch && $singleSearchTable == 'customers'))) {
    $whereBys = array();
    foreach($terms as $term) {
        $term = trim($term);
        $whereBys[] = "AND (
                            c.fname LIKE '%$term%'
                            OR c.lname LIKE '%$term%'
                            OR c.address LIKE '%$term%'
                            OR c.zip LIKE '%$term%'
                        )";
    }
    $customersExtraSql = '';
    if(moduleOwnership('view_customers')) {
		$customersExtraSql = "AND (c.user_id = '{$_SESSION['ao_userid']}' OR (s.user_id = '{$_SESSION['ao_userid']}' AND s.job_id = j.job_id) OR j.user_id={$_SESSION['ao_userid']} OR j.salesman={$_SESSION['ao_userid']} OR j.referral={$_SESSION['ao_userid']}) AND";
	}

	$sql = "SELECT c.customer_id AS id, CONCAT(c.lname, ', ', c.fname) AS label, 'customers.php' AS uri, 'icon-book' as icon
			FROM customers c
            LEFT JOIN jobs j ON (j.customer_id = c.customer_id)
			WHERE c.account_id = '{$_SESSION['ao_accountid']}'
                " . implode(' ', $whereBys) . "
			$customersExtraSql
            GROUP BY c.customer_id";
    $resultsArray = array_merge($resultsArray, DBUtil::queryToArray($sql));
}

//users
if(ModuleUtil::checkAccess('view_users') && (!$singleSearch || ($singleSearch && $singleSearchTable == 'users'))) {
    $whereBys = array();
    foreach($terms as $term) {
        $term = trim($term);
        $whereBys[] = "AND (
                            u.lname LIKE '%$term%'
                            OR u.fname LIKE '%$term%'
                            OR u.dba LIKE '%$term%'
                        )";
    }
    $sql = "SELECT u.user_id AS id, CONCAT(u.lname, ', ', u.fname) AS label, 'users.php' AS uri, 'icon-group' as icon
            FROM users u
            WHERE u.account_id = '{$_SESSION['ao_accountid']}'
                " . implode(' ', $whereBys) . "
            GROUP BY u.user_id";
    $resultsArray = array_merge($resultsArray, DBUtil::queryToArray($sql));
}

//documents
if(ModuleUtil::checkAccess('view_documents') && (!$singleSearch || ($singleSearch && $singleSearchTable == 'documents'))) {
    $whereBys = array();
    foreach($terms as $term) {
        $term = trim($term);
        $whereBys[] = "AND (
                            d.document LIKE '%$term%'
                            OR d.description LIKE '%$term%'
                        )";
    }
    $documentsExtraSql = '';
    if(moduleOwnership('view_documents')) {
        $documentsExtraSql = "d.user_id = '{$_SESSION['ao_userid']}'";
    }

$sql = "SELECT d.document_id AS id, d.document AS label, 'documents.php' AS uri, 'icon-file-text-alt' as icon
        FROM documents d
        WHERE d.account_id = '{$_SESSION['ao_accountid']}'
            " . implode(' ', $whereBys) . "
        GROUP BY d.document_id";
    $resultsArray = array_merge($resultsArray, DBUtil::queryToArray($sql));
}

$count = count($resultsArray);
?>
    <li><?=$count?> Result<?=$count === 1 ? '' : 's'?></li>
<?php

//echo jsonOutput($resultsArray);
foreach($resultsArray as $result) {
?>
    <li>
        <div class="btn btn-small btn-block" rel="select-search-suggestions" data-url="<?=ROOT_DIR?>/<?=$result['uri']?>?id=<?=$result['id']?>">
            <i class="<?=$result['icon']?>"></i>&nbsp;<?=stripslashes($result['label'])?>
        </div>
    </li>
<?php
}