<?php

include '../common_lib.php'; 

?>

<?=ViewUtil::loadView('doc-head')?>

    <script>
      $(document).ready(function(){
      	loadThisWidget();
      	setInterval('loadThisWidget()', <?php echo $_SESSION['ao_refresh']*1000; ?>);
      });
      
      function loadThisWidget()
      {
      	$('#<?=$_GET['widget']?>container').load('<?=AJAX_DIR?>/<?php echo $_GET['widget']; ?>.php');
      }
    </script>
    <div id="<?=$_GET['widget']?>container"></div>
  </body>
</html>
