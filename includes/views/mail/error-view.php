<?php
if(!$error) { return; }
?>
Fatal Error:<br /><br />
Username: <?=$_SESSION['ao_username']?> <br />
First Name: <?=$_SESSION['ao_fname']?> <br />
Username: <?=$_SESSION['ao_lname']?> <br /><br />
Date: <?=date('r')?><br /><br />
<pre><?=print_r($error)?></pre>