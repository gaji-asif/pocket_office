<?php
$options = '';
if(@$disable_search === true)
{
	$options .= 'disable_search: true';
}
?>
<script>
	$('.chosen').chosen({<?=$options?>});
</script>