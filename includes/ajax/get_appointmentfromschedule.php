<?php
include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');

$sql = "select appointments.appointment_id, appointments.user_id, appointments.title, appointments.job_id, date_format(appointments.datetime, '%l:%i %p'), date_format(appointments.datetime, '%Y-%m-%d'), appointments.text, date_format(appointments.timestamp, '%Y-%m-%d @ %l:%i %p'), users.fname, users.lname from appointments, users where appointments.appointment_id='" . $_GET['id'] . "' and appointments.user_id=users.user_id limit 1";
$res = DBUtil::query($sql);

if(!DBUtil::hasRows($res)) {
    UIUtil::showModalError('Appointment not found!');
}

list($appointment_id, $user_id, $title, $job_id, $time, $date, $description, $added, $fname, $lname) = mysqli_fetch_row($res) ;

$myJob = new Job($job_id);

ModuleUtil::checkJobModuleAccess('view_job_appointment', $myJob, TRUE);

?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                View Appointment Detail
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
          <table border="0" cellpadding="0" cellspacing="0" width="100%">
            <tr>
              <td width="25%" class="listitemnoborder"><b>Title:</b></td>
              <td class="listrownoborder"><?php echo $title ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Creator:</b></td>
              <td class="listrow"><?php echo $fname." ".$lname; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Date:</b></td>
              <td class="listrow"><?php echo $date; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Time:</b></td>
              <td class="listrow"><?php echo $time; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Job Number:</b></td>
              <td class="listrow"><?php echo $myJob->job_number; ?></td>
            </tr>
            <tr>
              <td class="listitem"><b>Salesman:</b></td>
              <td class="listrow"><?php echo $myJob->salesman_fname." ".$myJob->salesman_lname; ?></td>
            </tr>
            <tr valign='top'>
              <td class="listitem"><b>Description:</b></td>
              <td class="listrow"><?=UIUtil::cleanOutput($description, FALSE)?></td>
            </tr>
            <tr>
              <td class="listitemnoborder"></td>
              <td class="listrownoborder"><span class='smallnote'>Added <?php echo $added; ?></span></td>
            </tr>
            <tr>
              <td align="right" colspan=2 class="listrow">
                <input type='button' value='Go to Job' onclick="parent.location='<?=ROOT_DIR?>/jobs.php?id=<?php echo $myJob->job_id; ?>';">
              </td>
            </tr>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html  >