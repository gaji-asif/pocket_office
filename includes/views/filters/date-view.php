<?php
if(!$name) { return; }
$defaultDate = '';
?>
<input class="pikaday" data-default="<?=$defaultDate?>" type="text" name="filter_<?=$name?>" value="<?=$defaultDate?>" />