<?php
if(empty($url)) { return; }
?>
<script>
    var w = window.parent || window;
    w.location = '<?=$url?>';
</script>