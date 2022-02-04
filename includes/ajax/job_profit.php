<?php
include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));

ModuleUtil::checkJobModuleAccess('profitability_readwrite', $myJob, TRUE);

if(isset($_POST['commission']))
{
  $sql = "update profit_sheets set commission='".$_POST['commission']."' where profit_sheet_id='".$myJob->profit_sheet_id."' limit 1";
  DBUtil::query($sql);
}

if($myJob->profit_sheet_id=='')
{
  $sql = "insert into profit_sheets values(0, '".$myJob->job_id."', '".$_SESSION['ao_userid']."', 40, now())";
  DBUtil::query($sql);
  $myJob->profit_sheet_id = DBUtil::getInsertId();
?>

<script>
  Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', false, true);
</script>
<?php
}

//&a='+document.getElementById('amt').value+'&n='+document.getElementById('note').value+'&c='+document.getElementById('type').value

$sql = "select commission from profit_sheets where profit_sheet_id='".$myJob->profit_sheet_id."' limit 1";
$res = DBUtil::query($sql);
list($commission_percentage)=mysqli_fetch_row($res);

?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Job Profitability
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class='infocontainer'>
          <table border="0" width="100%">
            <tr>
              <td>
                <table border="0" width="100%" cellpadding="0" cellspacing="0" class='listtable'>
                  <tr>
                    <td class="listitemnoborder" width=125><b>Commission %:</b></td>
                    <td class="listrownoborder">
                      <form method="post" action='job_profit.php?id=<?php echo $myJob->job_id; ?>'>
                      <select name='commission'>
<?php
for($i=0; $i<101; $i+=5)
{
  $selected = '';
  if($i==$commission_percentage)
    $selected = 'selected';
?>
                        <option value='<?php echo $i; ?>' <?php echo $selected; ?>><?php echo $i; ?>%</option>
<?php
}
?>
                      </select>
                      <input type="submit" value="Save">
                      </form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
              <td class="smalltitle"><b>Add Item:<b></td>
            </tr>
            <tr>
              <td>
                <table border="0" width="100%" cellpadding="0" cellspacing="0" class='listtable'>
                  <tr>
                    <form name='invoice' id='invoice'>
                    <td width=125 class="listitemnoborder"><b>Description:</b></td>
                    <td class="listrownoborder">
                      <input type="text" size=60 id='note'>
                    </td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Amount:</b></td>
                    <td class="listrow">
                      <input type="text" size=10 id='amt'>
                    </td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Type:</b></td>
                    <td class="listrow">
                      <select id='type'>
                        <option value='charge'>Charge</option>
                        <option value='credit'>Credit</option>
                      </select>
                      <input type='button' value='Add' onclick="Request.make('<?=AJAX_DIR?>/get_profitlist.php?id=<?php echo $myJob->job_id; ?>&a='+document.getElementById('amt').value+'&n='+document.getElementById('note').value+'&t='+document.getElementById('type').value, 'profitcontainer', false, true); document.invoice.reset(); Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $myJob->job_id; ?>', 'jobscontainer', fa;se, true);">
                      </form>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
              <td>
                <table border="0" class='smcontainertitle' width="100%" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width=23>&nbsp;</td>
                    <td>Description</td>
                    <td width=127>Credit</td>
                    <td width=125'>Charge</td>
                  </tr>
                </table>
                <table border="0" class="infocontainernopadding" id='profitcontainer' width="100%" cellpadding=2 cellspacing="0">
                  <tr>
                    <td colspan=3>
                      <table border="0" cellpadding="0" cellspacing="0" width="100%">
                      <script>
                        Request.make('<?=AJAX_DIR?>/get_profitlist.php?id=<?php echo $myJob->job_id; ?>', 'profitcontainer', false, true);
                      </script>
                      </table>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td align="right" colspan=3>
                <input type='button' value='Print' onclick='window.location="get_profitprint.php?id=<?php echo $myJob->job_id; ?>";'>
                <input type='button' value='Print Paysheet' onclick='window.location="get_paysheetprint.php?id=<?php echo $myJob->job_id; ?>";'>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>