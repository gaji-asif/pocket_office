<tr rel="make-request" data-action="<?=AJAX_DIR?>/get_document.php?id=<?=@$document['document_id']?>" data-destination="documentscontainer">
	<td><img src="<?=IMAGES_DIR?>/icons/<?=@$document['filetype']?>.png" /></td>
	<td class='data-table-cell smalltitle'><?=@$document['document']?></td>
	<td class='data-table-cell'><?=@$document['label']?>&nbsp;</td>
	<td class='data-table-cell'><?=DateUtil::formatDate(@$document['timestamp'])?></td>
	<td class='data-table-cell'><?=@$document['owner']?></td>
</tr>