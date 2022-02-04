<?php
if(!ModuleUtil::checkAccess('view_documents') || empty($stageDocuments) || !is_array($stageDocuments)) { return; }
?>
<tr valign="top">
    <td class="listitem"><b>Stage Documents:</b></td>
    <td class="listrow no-padding">
        <ul class="advancement-requirements">
<?php
foreach($stageDocuments as $stageDocument) { 
?>
            <li>
                <a href="<?=($stageDocument['filetype'] != 'image' && $stageDocument['filetype'] != 'pdf') ? 'http://docs.google.com/viewer?url=' : ''?><?=ACCOUNT_URL?>/docs/<?=$stageDocument['filename']?>" target="blank" data-type="document" data-id="<?=$stageDocument['document_id']?>" tooltip>
                    <?=$stageDocument['document']?>
                </a>
            </li>
<?php
}
?>
        </ul>
    </td>
</tr>