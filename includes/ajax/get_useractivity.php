<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('view_user_history', TRUE);
?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                User Activity History
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
          <table border="0" cellpadding="0" cellspacing="0" width="100%" class='infocontainer'>
            <tr valign='top'>
              <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
                  <tr>
                    <td>Action</td>
                    <td width=200>Timestamp</td>
                    <td width=150>Job ID</td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding">
<?php
$sql = "select history.action, history.job_id, jobs.job_number, date_format(history.timestamp, '%c-%e-%Y @ %r') from history, jobs where jobs.job_id=history.job_id and history.user_id='".$_GET['id']."' order by history.timestamp desc";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
                  <tr>
                    <td colspan=2 align="center"><b>No History Found</b></td>
                  </tr>
<?php
}
$i=1;
while(list($action, $job_id, $job_num, $timestamp)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';
?>
                  <tr class='<?php echo $class; ?>'>
                    <td style='font-size: 10px;'><?php echo $action; ?></td>
                    <td width=195><?php echo $timestamp; ?></td>
                    <td width=150><a href="javascript: parent.location='<?=ROOT_DIR?>/jobs.php?id=<?php echo $job_id; ?>';" class='basiclink'><?php echo $job_num; ?></a></td>
                  </tr>
<?php
  $i++;
}
?>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>