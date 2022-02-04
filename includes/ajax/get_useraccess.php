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
                User Access History
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
                    <td width=200>Timestamp</td>
                    <td>IP Address</td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding">
<?php
$sql = "select date_format(timestamp, '%c-%e-%Y @ %r'), ip_address from access where user_id='".$_GET['id']."' order by timestamp desc";
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
while(list($timestamp, $ip)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';
?>
                  <tr class='<?php echo $class; ?>'>
                    <td width=195><?php echo $timestamp; ?></td>
                    <td><?php echo $ip; ?></td>
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