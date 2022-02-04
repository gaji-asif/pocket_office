<?php

include '../common_lib.php';
if(!viewWidget('widget_journals')||!ModuleUtil::checkAccess('view_jobs')) { die(); }

$journals_array = UserModel::getJournals();
if(empty($journals_array))
{
?>
<h1 class="widget">No Journals Found</h1>
<?php
}
else
{
?>
<table class="table table-condensed table-striped table-hover table-widget">
	<thead>
		<tr>
			<th>Job #</th>
			<th>Journal</th>
			<th>Author</th>
			<th>Timestamp</th>
		</tr>
	</thead>
	<tbody>
<?php
	foreach($journals_array as $journal_id => $journal)
	{
		$timestamp = DateUtil::formatDateTime($journal['timestamp']);
		$journal_str = prepareText($journal["text"]);
		if(strlen($journal_str) > 5)
		{
			$journal_str = substr(trim($journal_str), 0, 50) . '...';
		}

		$view_data = array(
			'job_id' => $journal['job_id'],
			'job_number' => $journal['job_number'],
			'journal_str' => $journal_str,
			'timestamp' => $timestamp,
			'fname' => $journal['fname'],
			'lname' => $journal['lname']
		);
		echo ViewUtil::loadView('widgets/journal-row', $view_data);
	}
?>
	</tbody>
</table>
<?
}