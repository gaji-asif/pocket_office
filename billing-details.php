<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1)
	die("Insufficient Rights");

$id = RequestUtil::get('id');
$date = RequestUtil::get('date');
$w_date = RequestUtil::get('w_date');
?>

<?= ViewUtil::loadView('doc-head') ?>


<h1 class="page-title"><i class="icon-question"></i><?=(!empty($this_page->title))?$this_page->title:''?></h1>

    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center' >
              <td id="headername" name="headername">
                Time Records
              </td>
              <td style="text-align:right;padding-right:10px;padding-top:5px;" id="searchbox" name="searchbox">
              <?php
              $account_id=$_SESSION['ao_accountid'];
              ?>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class='infocontainer' id='knowledgebasecontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/billing/get_job_timer.php?id=<?=$id?>&date=<?=$date?>&w_date=<?=$w_date?>', 'knowledgebasecontainer', true, true);
          </script>
        </td>
      </tr>
    </table>
