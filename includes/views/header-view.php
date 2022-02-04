<div class="clearfix">
<?php
if($logged_in) {
?>
    <div class="icon clock-16">Browsing History:</div>
    <div id="browsing-container"></div>
    <script>
        Request.make('<?=AJAX_DIR?>/get_browsing_new.php', 'browsing-container', false, true);
    </script>
<?php
}
?>
</div>
