<?php
if(!$myUser->exists()) { return; }
$userId = $myUser->get('user_id');
$date = DateUtil::formatDate($myUser->get('reg_date'));
$activeStr = $myUser->getIsActive() ? '' : ' - <font color="red">INACTIVE</font>';
$displayName = $myUser->getDisplayName(FALSE);
$displayName = $myUser->isDeleted() ? "<s>$displayName</s>" : $displayName;
?>
<tr class="data-table-row" valign="middle" onclick="Request.make('<?=AJAX_DIR?>/get_notes.php?id=<?=$userId?>&type=users', 'notes', false, true); Request.make('<?=AJAX_DIR?>/get_user.php?id=<?=$userId?>', 'userscontainer', true, true);">
    <td class="data-table-cell smalltitle"><?=$displayName?><?=$activeStr?></td>
    <td width="30%" class="data-table-cell">
        <?=$myUser->get('dba')?>
    </td>
    <td width="15%" class="data-table-cell">
        <?=$date?>
    </td>

    <td width="15%" class="data-table-cell">
    	<?php if($myUser->get('is_active'))
        	    echo 'Active';
        	else
        		echo 'Inactive';

        ?>
    </td>
</tr>