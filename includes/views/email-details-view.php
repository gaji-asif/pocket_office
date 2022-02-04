<?php
if(empty($myEmail) || get_class($myEmail) !== 'Email') { return; }

?>

<tr class="data-table-row" style="background-color: #DEDEDE;">
    <td colspan="10" class="data-table-cell" style="padding-left:50px;font-size:23px;"><?=$myEmail->subject?>
    </td>
    <td width=100 class="listitemnoborder" >
        <a href="javscript:void(0);"  rel="open-modal" data-script="attach_email.php?id=<?=$myEmail->id?>"  style="background: #35aa47;color: #fff;padding: 5px 10px; padding-top: 6px;display: inline-block;">Attach Job</a>
    </td>
</tr>

<tr>
    <td colspan="11" class="data-table-cell" style="padding-left:50px; padding-top:30px;padding-buttom:30px;"><?=$myEmail->from_name;?> <br>To <?=$myEmail->to_mail;?> 
    </td>
</tr>


<tr>
    <td colspan="11" class="data-table-cell" style="padding-left:50px; padding-top:30px;padding-buttom:30px;"><?=$myEmail->snippet;?> 
    </td>
</tr>

