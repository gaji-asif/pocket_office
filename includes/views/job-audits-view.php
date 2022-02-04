<?php
if(!is_array($history) || empty($history)) { return; }

foreach($history as $change) {
?>
<div><?=UserUtil::getDisplayName(MapUtil::get($history, 'user_id'), FALSE, TRUE)?></div>
<?php
}