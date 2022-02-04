<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
        
$myJob = new Job(RequestUtil::get('id'));
ModuleUtil::checkJobModuleAccess('assign_job_customer', $myJob, TRUE);

$errors = array();
if(RequestUtil::get('submit')) {
    $customerId = RequestUtil::get('customer_id');
    if(!$customerId) {
        $errors[] = 'Required fields missing';
    }
    
    if(!count($errors)) {
        FormUtil::update('jobs');
        $myJob->storeSnapshot();

        JobModel::saveEvent($myJob->job_id, 'Assigned New Job Customer');
        NotifyUtil::notifySubscribersFromTemplate('add_job_customer', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
?>

<script>
	Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?=$myJob->job_id?>', 'jobscontainer', true, true, true);
</script>
<?php
        die();
    }
}
?>
<form method="post" name="customer" action='?id=<?=$_GET['id']?>'>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Assign Customer</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
      <td width="25%" class="listitemnoborder">
        <b>Customer:</b>&nbsp;<span class="red">*</span>
      </td>
      <td class="listrownoborder">
          <select name='customer_id'>
<?php
$customers = CustomerModel::getAllCustomers(UIUtil::getFirstLast());
foreach($customers as $customer) {
?>
            <option value="<?=$customer['customer_id']?>" <?=$myJob->customer_id == $customer['customer_id'] ? 'selected' : ''?>><?=$customer['select_label']?></option>
<?php
}
?>
        </select>
    </td>
  </tr>
  <tr>
      <td colspan=2 align="right" class="listrow">
          <input type="submit" name="submit" value="Save">
      </td>
  </tr>
</table>
</form>
</body>
</html>