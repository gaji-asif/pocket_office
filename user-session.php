<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
?>
<pre>
TIME ZONE: <?=DEFAULT_TIMEZONE?>
</pre>
<pre>
PHP TIME: <?=DateUtil::formatMySQLTimestamp()?>
</pre>
<pre>
OFFSET: <?=DateUtil::getOffset()?>
</pre>
<pre>
<?=print_r($_SESSION)?>
</pre>