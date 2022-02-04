<?php
if(empty($url)) { 
    return;
}
?>
<script>
//    var w = window.parent || window;
    window.top.location = '<?=$url?>';
</script>