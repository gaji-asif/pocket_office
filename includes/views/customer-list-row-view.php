<?php
if(empty($myCustomer) || get_class($myCustomer) !== 'Customer') { return; }
?>
<tr class="data-table-row" valign="middle" onclick="Request.make('<?=AJAX_DIR?>/get_notes.php?id=<?=$myCustomer->getMyId() ?>&type=customer', 'notes', false, true); Request.make('<?= AJAX_DIR ?>/get_customer.php?id=<?=$myCustomer->getMyId() ?>', 'customerscontainer', true, true);">
	<td class="data-table-cell smalltitle">
		<?=$myCustomer->getDisplayName()?>
	</td>
	<td class="data-table-cell" width="30%">
		<?=$myCustomer->get('nickname')?>
	</td>
	<td width="15%" class="data-table-cell">
		<?=DateUtil::formatDateTime($myCustomer->get('timestamp'))?>
	</td>
	<td width="20%" class="data-table-cell">
		<?=$myCustomer->get('user_fname')?> <?=$myCustomer->get('user_lname')?>
	</td>
</tr>