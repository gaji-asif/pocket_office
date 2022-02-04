<?php
	$newid = $id ? '/messaging.php?id='.$id : '/jobs.php?id='.$job_id;
	$removeid = $id ? '' : '/includes/ajax/delete_journal_inbox.php?id='.$journal_id;
	$tooltip = $id ? '' : 'title="Journal" class="journal"';
	$jclass = $id ? '' : 'journal';
	$jicon = $id ? '' : '<i tooltip="" class="icon-file-text-alt"></i>';
	$ricon = $id ? '' : '<i tooltip="" class="icon-remove"></i>';
?>

<tr id="journal-<?=@$journal_id?>" class="cursor-pointer <?=@$row_class?> <?=@$jclass?> "  <?=@$tooltip?>>
	<td rel="change-window-location" data-url="<?=ROOT_DIR?><?=@$newid?>"><?=@$jicon?> <?=@$job_number?></td>
	<td rel="change-window-location" data-url="<?=ROOT_DIR?><?=@$newid?>"><?=@$subject?><?=@$text?></td>
	<td width=110 rel="change-window-location" data-url="<?=ROOT_DIR?><?=@$newid?>"><?=@$lname?>, <?=@$fname[0]?> </td>
	<td class="nobr" rel="change-window-location" data-url="<?=ROOT_DIR?><?=@$newid?>"><?=@$timestamp?></td>
	<td data-journal-id="<?=@$journal_id?>" rel="delete-journal-inbox"><?=@$ricon?></td>
</tr>
