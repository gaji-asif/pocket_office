<?php

include 'includes/common_lib.php';

$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));

pageSecure($this_page->source);

?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-paper-clip"></i><?=$this_page->title?></h1>

    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center'>
              <td>
                Materials
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class='infocontainer' id='materialscontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/get_materiallist.php', 'materialscontainer', true, true);
          </script>
        </td>
      </tr>
    </table>
  </body>
</html>
