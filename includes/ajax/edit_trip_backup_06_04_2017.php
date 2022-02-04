<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('modify_suppliers', TRUE);

$mes_id = RequestUtil::get('id');

$sql = "select * from measurment where mes_id = '$mes_id' limit 1";
$res = DBUtil::query($sql);

if(!mysqli_num_rows($res)) {
    UIUtil::showModalError('Supplier not found!');
}

list($mes_id, $job_id, $trip_date, $start_time, $end_time, $trip_area, $order_no, $client_name, $client_address, $report_status)=mysqli_fetch_row($res);

if(isset($_POST['trip_date']) && $supplier_id)
{
  $error=0;
  $temp_email = $email;

  $trip_date=$_POST['trip_date'];
  $start_time=$_POST['start_time'];
  $end_time=$_POST['end_time'];
  $trip_area=$_POST['trip_area'];
  $order_no=$_POST['order_no'];
  $gmap_url=$_POST['gmap_url'];
  $client_name=$_POST['client_name'];
  $client_address=$_POST['client_address'];
  $status=$_POST['status'];


  if($error==0)
  {
    $trip_date = mysqli_real_escape_string(DBUtil::Dbcont(),$trip_date);
    $start_time = mysqli_real_escape_string(DBUtil::Dbcont(),$start_time);
    $end_time = mysqli_real_escape_string(DBUtil::Dbcont(),$end_time);
    $trip_area = mysqli_real_escape_string(DBUtil::Dbcont(),$trip_area);
    $order_no = mysqli_real_escape_string(DBUtil::Dbcont(),$order_no);

    $gmap_url = mysqli_real_escape_string(DBUtil::Dbcont(),$gmap_url);
    $client_name = mysqli_real_escape_string(DBUtil::Dbcont(),$client_name);
    $client_address = mysqli_real_escape_string(DBUtil::Dbcont(),$client_address);
    $status = mysqli_real_escape_string(DBUtil::Dbcont(),$status);

    $sql = "update measurment set trip_date='".$trip_date."', start_time='".$start_time."', end_time='".$end_time."', trip_area='".$trip_area."', order_no='".$order_no."', g_map_url='".$gmap_url."', client_name='".$client_name."', client_address='".$client_address."', report_status='".$status."' where mes_id=".$mes_id." limit 1";
    DBUtil::query($sql);

?>

  <script>

    Request.makeModal('<?=AJAX_DIR?>/get_suppliers.php', 'suppliers-list', true, true, true);

  </script>
<?php
  }
}

$sql = "select * from measurment where mes_id = '$mes_id' limit 1";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
  die("Invalid Content");

list($mes_id, $job_id, $trip_date, $start_time, $end_time, $trip_area, $order_no, $g_map_url, $client_name, $client_address, $report_status)=mysqli_fetch_row($res);
?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Edit Trip
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php

if($error==1)
{
?>
            <tr>
              <td colspan=2 style='color: red; font-size: 11px;' class="listrownoborder">
                <b>Errors Found!</b>
                <?php echo $error_msg; ?>
              </td>
            </tr>
<?php
}
?>
          <tr>
            <td>
              <form method="post" name='customer' action='?id=<?php echo $mes_id; ?>'>
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                  <tr>
                    <td class="listitemnoborder" width="25%"><b>Trip Date:</b>&nbsp;<span class="red">*</span></td>
                    <td class="listrownoborder">
                      <input type="text" name='trip_date' value='<?php echo $trip_date; ?>'>
                    </td>
                  </tr>
                  <tr>
                    <td width="25%" class="listitem"><b>Start Time:</b></td>
                    <td class="listrow">
                      <input type="text" name='start_time' value='<?php echo $start_time; ?>'>
                    </td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>End Time:</b>&nbsp;<span class="red">*</span></td>
                    <td class="listrow">
                      <input type="text" name='end_time' value='<?php echo $end_time; ?>'>
                    </td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Trip Area:</b></td>
                    <td class="listrow">
                      <input type="text" name='trip_area' value='<?php echo $trip_area; ?>'>
                    </td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Order No:</b></td>
                    <td class="listrow">
                      <input type="text" name='order_no' value='<?php echo $order_no; ?>'>
                    </td>
                  </tr>
                  <tr>
                    <td class="listitem"><b>Client Name:</b></td>
                    <td class="listrow">
                      <input type="text" name='client_name' value='<?php echo $client_name; ?>'>
                    </td>
                  </tr>

                  <tr>
                    <td class="listitem"><b>Client Address:</b></td>
                    <td class="listrow">
                      <input type="text" name='client_address' value='<?php echo $client_address; ?>'>
                    </td>
                  </tr>

                  <tr>
                    <td class="listitem"><b>Report Status:</b></td>
                    <td class="listrow">
                      <select name="status" >
                          <option value="Report Not Generated">Report Not Generated</option>
                          <option value="Report Generated">Report Generated</option>
                          <option value="On Hold">On Hold</option>
                      </select>
                    </td>
                  </tr>

                </table>
              </td>
            </tr>
          </table>
          <table border="0" width="100%" cellpadding="0" cellspacing="0">
            <tr>
              <td align="right" class="listrow">
                  <input type="button" value="Delete" onclick='if(confirm("Are you sure?")){window.location="edit_trip.php?id=<?php echo $mes_id; ?>&action=del";}'>
                  <input type="submit" value="Save">
                </form>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>