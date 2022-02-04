<?php
$current_file_name = basename($_SERVER['SCRIPT_NAME'], '.php');

$navigation_array = array(
	'Inbox' => 'inbox',
	'Send' => 'send',
	'Draft' => 'draft',
	'All Mail' => 'all_mail',
	'Spam' => 'spam',
);
?>
<div class="well sidebar-nav">
	<ul class="nav nav-list">
		<li class="nav-header">Navigation</li>
<?php

	foreach($navigation_array as $label => $file_name)
	{
?>
		<li><a href="<?=$file_name?>.php"><?=$label?></a></li>
<?php
	}
?>
		<li><a href="<?=ROOT_DIR?>">Back to App</a></li>
	</ul>
</div>