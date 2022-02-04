<?php

include 'includes/common_lib.php';

$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));

pageSecure($this_page->source);

?>

<?=ViewUtil::loadView('doc-head')?>
<?php
$display="";
if(!$_SESSION['ao_founder'])$display="style='display:none;'";
?>

<h1 class="page-title"><i class="icon-question"></i><?=$this_page->title?></h1>

    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center' >
              <td id="headername" name="headername">
                Storm Report
              </td>
              <table class="data-table" width="100%">
  <tbody><tr>
    <td>
      <div class="container">
        <div class="row pillbox">
          <div class="col span-2">
            <div><a href="/storm-reportv1.php" class="btn btn-blue btn-block">Storm Reports V1</a></div>
            
            
          </div>
           <div class="col span-2">
            <div><a href="/storm-reportv2.php" class="btn btn-blue btn-block">Storm Reports V2</a></div>
             
            
          </div>
        </div>
      </div>
    </td>
  </tr>
</tbody></table>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          &nbsp;
         
        </td>
      </tr>
    </table>

     <script type='text/javascript'>
            //Request.make('<?=AJAX_DIR?>/get_knowledgebaselist.php', 'knowledgebasecontainer', true, true);
          </script>
  </body>
</html>
