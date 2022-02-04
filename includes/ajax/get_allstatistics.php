<?php 
include '../common_lib.php';           
ModuleUtil::checkAccess('view_all_statistics', TRUE, TRUE);

$accountId = $_SESSION['ao_accountid'];
$year = date('Y');

$sql = "SELECT COUNT(user_id) FROM users WHERE account_id = '$accountId' AND is_deleted = 0";
$totalUsers = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(user_id) FROM users WHERE account_id = '$accountId' AND is_deleted = 1";
$recoverableUsers = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(customer_id) FROM customers WHERE account_id = '$accountId'";
$totalCustomers = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(job_id) FROM jobs WHERE account_id = '$accountId'";
$totalJobs = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(job_id) FROM jobs WHERE date_format(timestamp, '%Y') = '$year' AND account_id = '$accountId'";
$totalYtdJobs = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(jobs.job_id) FROM jobs LEFT JOIN status_holds ON (status_holds.job_id = jobs.job_id) WHERE date_format(jobs.timestamp, '%Y') = '$year' AND jobs.account_id = '$accountId' AND status_holds.status_hold_id IS NULL";
$ytdActiveJobs = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(task_id) FROM tasks WHERE account_id = '$accountId'";
$totalTasks = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(task_id) FROM tasks WHERE date_format(timestamp, '%Y') = '$year' AND account_id = '$accountId'";
$totalYtdTasks = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(task_id) FROM tasks WHERE completed is not null AND account_id = '$accountId'";
$totalCompletedTasks = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(repair_id) FROM repairs WHERE account_id = '$accountId'";
$totalRepairs = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(repair_id) FROM repairs WHERE completed is not null AND account_id = '$accountId'";
$totalCompletedRepairs = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(event_id) FROM events WHERE account_id = '$accountId'";
$totalEvents = DBUtil::queryToScalar($sql);

$sql = "SELECT datediff(now(), timestamp) FROM access ORDER BY timestamp ASC";
$daysLive = DBUtil::queryToScalar($sql);

$sql = "SELECT COUNT(access_id) FROM access WHERE account_id = '$accountId'";
$totalHits = DBUtil::queryToScalar($sql);
$avgHits = $daysLive > 0 ? round($totalHits / $daysLive, 1) : 0;
?>
<tr>
    <td width="33%" class="listitemnoborder"><b>Total Users:</b></td>
    <td class="listrownoborder"><?=$totalUsers?></td>
</tr>
<tr>
    <td class="listitem"><b>Recoverable Users:</b></td>
    <td class="listrow"><?=$recoverableUsers?></td>
</tr>
<tr>
    <td class="listitem"><b>Total Customers:</b></td>
    <td class="listrow"><?=$totalCustomers?></td>
</tr>
<tr>
    <td class="listitem"><b>Total Jobs:</b></td>
    <td class="listrow"><?=$totalJobs?></td>
</tr>
<tr>
    <td class="listitem"><b>Total YTD Jobs:</b></td>
    <td class="listrow"><?=$totalYtdJobs?></td>
</tr>
<tr>
    <td class="listitem"><b>YTD Active Jobs:</b></td>
    <td class="listrow"><?=$ytdActiveJobs?></td>
</tr>
<tr>
    <td class="listitem"><b>Total Tasks:</b></td>
    <td class="listrow"><?=$totalTasks?></td>
</tr>
<tr>
    <td class="listitem"><b>Total YTD Tasks:</b></td>
    <td class="listrow"><?=$totalYtdTasks?></td>
</tr>
<tr>
    <td class="listitem"><b>Total Tasks Completed:</b></td>
    <td class="listrow"><?=$totalCompletedTasks?></td>
</tr>
<tr>
    <td class="listitem"><b>Total Repairs:</b></td>
    <td class="listrow"><?=$totalRepairs?></td>
</tr>
<tr>
    <td class="listitem"><b>Total Repairs Completed:</b></td>
    <td class="listrow"><?=$totalCompletedRepairs?></td>
</tr>
<tr>
    <td class="listitem"><b>Total Events:</b></td>
    <td class="listrow"><?=$totalEvents?></td>
</tr>
<tr>
    <td class="listitem"><b>Days Live:</b></td>
    <td class="listrow"><?=$daysLive?></td>
</tr>
<tr>
    <td class="listitem"><b>Daily Access:</b></td>
    <td class="listrow"><?=$avgHits?></td>
</tr>
