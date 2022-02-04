<?php
if(empty($document)) { return; }
?><tr class="data-table-row" rel="make-request" data-action="<?=AJAX_DIR?>/get_document.php?id=<?=$document['document_id']?>" data-destination="documentscontainer">
	
	<td class='data-table-cell smalltitle'><img src="<?=IMAGES_DIR?>/icons/<?=$document['filetype']?>.png" alt="" > <?=$document['document']?></td>
	<td width="20%" class="data-table-cell"><?=$document['label']?>&nbsp;</td>
	<td width="20%" class="data-table-cell"><?=DateUtil::formatDate($document['timestamp'])?></td>
	<td width="20%" class="data-table-cell"><?=$document['owner']?></td>
</tr>