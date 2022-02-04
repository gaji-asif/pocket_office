<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('job_message_history', $myJob, TRUE);
$jobMessages = JobUtil::getMessageHistory($myJob->job_id);
?>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
	<tr>
		<td>
			<table width="100%" class="data-table-header" cellpadding="0" cellspacing="0" border="0">
				<tr valign="center">
					<td>
						View Job Message History
					</td>
					<td align="right">
						<i class="icon-remove grey btn-close-modal"></i>
					</td>
				</tr>
			</table>
		</td>
	</tr>
	<tr>
		<td class="infocontainernopadding">
<?php
foreach($jobMessages as $message) {
    $message['body'] = str_replace("<html>", "", MapUtil::get($message, 'body'));
    $message['body'] = str_replace("</html>", "", MapUtil::get($message, 'body'));
    $message['timestamp'] = DateUtil::formatDateTime(MapUtil::get($message, 'timestamp'));

?>
			<div style="border-bottom: 2px solid #CCC;">
				<table cellpadding="0" cellspacing="0" border="0" width="100%" style="<?=$style?>">
					<tr>
						<td class="listitemnoborder" width="25%"><b>Message Type:</b></td>
						<td class="listrownoborder"><?=MapUtil::get($message, 'type')?></td>
					</tr>
					<tr>
						<td class="listitem"><b>Timestamp:</b></td>
						<td class="listrow"><?=MapUtil::get($message, 'timestamp')?></td>
					</tr>
					<tr>
						<td class="listitem"><b>Delivery Address:</b></td>
						<td class="listrow"><?=MapUtil::get($message, 'to_email')?></td>
					</tr>
					<tr>
						<td class="listitem"><b>Subject:</b></td>
						<td class="listrow"><?=MapUtil::get($message, 'subject')?></td>
					</tr>
					<tr valign="top">
						<td class="listitem"><b>Message Body:</b></td>
						<td class="listrow"><?=MapUtil::get($message, 'body')?></td>
					</tr>
				</table>
			</div>
<?php
}
?>
		</td>
	</tr>
</table>
</body>
</html>