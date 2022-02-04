<?php
if(!$myAnnouncement) { return; }


?>
<tr class="data-table-row" rel="make-request" data-action="includes/ajax/get_announcement.php?id=<?=$myAnnouncement->getMyId()?>" data-destination="announcements-container">
    <td width="2%" align="center" class="job-row-icon"><i class="icon-bullhorn <?=$myAnnouncement->isRead() ? 'light-gray' : 'green'?>"></i></td>
	<td class='data-table-cell smalltitle'><?=$myAnnouncement->get('subject')?></td>
	<td width="18%" class="data-table-cell"><?=$myAnnouncement->getCreatorDisplayName()?>&nbsp;</td>
	<td width="18%" class="data-table-cell"><?=DateUtil::formatDate(@$document['timestamp'])?></td>
</tr>