<?php

include '../common_lib.php';
if(!viewWidget('widget_announcements')||!ModuleUtil::checkAccess('view_announcements')) { die(); }

//$cache_key = "widgetAnnouncements::{$_SESSION['ao_accountid']}::{$_SESSION['ao_level']}";

$sql = "select announcement_id, subject, timestamp".
       " from announcements".
       " where account_id='".$_SESSION['ao_accountid']."' and min_level>='".$_SESSION['ao_level']."'".
       " order by timestamp desc".
       " limit 20";

$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
<h1 class="widget">No Announcements Found</h1>
<?php
}
else
{
?>
<table class="table table-condensed table-striped table-hover table-widget">
	<thead>
		<tr>
			<th>Subject</th>
			<th>Timestamp</th>
		</tr>
	</thead>
	<tbody>
<?php
	while(list($id, $subject, $timestamp)=mysqli_fetch_row($res))
	{
		$timestamp = DateUtil::formatDateTime($timestamp);
		$row_class = '';
		if(!AnnouncementModel::isRead($id))
		{
			$row_class = 'info';
		}

		$view_data = array(
			'row_class' => $row_class,
			'id' => $id,
			'subject' => $subject,
			'timestamp' => $timestamp
		);
		echo ViewUtil::loadView('widgets/announcement-row', $view_data);
	}
?>
	</tbody>
</table>
<?php
}
?>