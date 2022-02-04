<?php
error_reporting(0);
error_reporting(E_ALL);

include '../common_lib.php';
if(!ModuleUtil::checkAccess('view_jobs'))
die("Insufficient Rights");

$mail_id = RequestUtil::get('id');
$myEmail = new Email($mail_id);
//ModuleUtil::checkJobModuleAccess('view_jobs', $myJob, TRUE, TRUE);

?>

<table width="100%" border="0" class="data-table" cellpadding="0" cellspacing="0">

<?php
$viewData = array(
	'myEmail' => $myEmail,
	'quick_settings_spacer' => true,
	'row_class' => 'no-hover',
    'key' => 0
);
//echo "<pre>";print_r($viewData);die;

echo ViewUtil::loadView('email-details', $viewData);

?>

  <tr><td colspan=11>&nbsp;</td></tr>

  <tr class='odd'>
    <td colspan=11 class='infofooter'>
      <table border="0" cellpadding="0" cellspacing="0" width="100%">
        <tr>
          <td>
            <a href="javascript:clearElement('notes'); Request.make('<?=AJAX_DIR?>/get_emaillist.php?<?=$_SESSION['ao_full_joblist_query_string']?>', 'emailscontainer', true, true);" class='basiclink'>
				<i class="icon-double-angle-left"></i>&nbsp;Back
			</a>
          </td>
        </tr>
      </table>
    </td>
  </tr>

</table>

<script>
function refresh(closeModal, callback) {
    Request.make('<?=AJAX_DIR?>/get_email.php?id=<?=$mail_id?>', 'emailscontainer', true, true, function() {
        if(closeModal) {
            deleteOverlay();
        }
        Functions.executeCallback(callback);
    });
}
</script>