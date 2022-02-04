<?php

include '../common_lib.php'; 
if(!ModuleUtil::checkAccess('job_summary'))
  die("Insufficient Rights");

echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));

if(moduleOwnership('job_summary') && (!JobUtil::isSubscriber($myJob->job_id) && $myJob->salesman_id!=$_SESSION['ao_userid'] && $myJob->user_id!=$_SESSION['ao_userid']))
  die("Insufficient Rights");

$myCustomer = new Customer($myJob->customer_id);

$me = UserModel::getMe();
$addressObj = $me->get('office_id') ? new Office($me->get('office_id')) : new Account($_SESSION['ao_accountid']);

ob_start();
?>
    <table border="0" cellspacing="0" cellpadding="0" width='800' align="center">
<?php
if($myJob->sheet_id!='') {
  $mySheet = new Sheet($myJob->job_id);
}

if($myJob->midroof!='')
  $midroof = "<br /><font color=\"red\">Mid Insp: {$myJob->midroof}</font>";
?>
      <tr valign='bottom'>
        <td align="center">
          <?=AccountModel::getLogoImageTag()?>
          <br>
          <?=$addressObj->getFullAddress()?>
          <br>
          Phone: <?=UIUtil::formatPhone($addressObj->get('phone'))?>
<?php
if($addressObj->get('fax')) {
?>
          <br>
          <b>Fax:</b> <?=UIUtil::formatPhone($addressObj->get('fax'))?>
<?php
}
?>
        </td>
        <td style='font-size: 35px; font-weight: bold;' width=800 align="right">Job Summary (<?php echo $myJob->job_number; ?>)<?php echo $midroof; ?></td>
      </tr>
    </table>
    <br><br>
    <table border="0" width='800' align="center">
      <tr>
        <td>
          <span class='mainpagetitle'>Customer Info</span>
        </td>
      </tr>
      <tr>
        <td style='border: 1px solid black;'>
          <table border="0" cellspacing="0" cellpadding=2 width="100%" style='font-size: 16px;'>
            <tr>
              <td colspan=4><b><?=$myCustomer->getDisplayName()?></b></td>
            </tr>
            <tr>
              <td colspan=4><?=$myCustomer->getFullAddress()?></td>
            </tr>
            <tr><td>&nbsp;</td></tr>
            <tr>
              <td width=50><b>Phone:</b></td>
              <td width=150><?php echo UIUtil::formatPhone($myCustomer->get('phone')); ?></td>
              <td width=50><b>Email:</b></td>
              <td><?php echo $myCustomer->get('email'); ?></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
        <br><br>
    <table border="0" width='800' align="center">
      <tr>
        <td>
          <span class='mainpagetitle'>Job Details</span>
        </td>
      </tr>
      <tr>
        <td style='border: 1px solid black;'>
          <table border="0" cellspacing="0" cellpadding=2 width="100%" style='font-size: 16px;'>
            <tr>
              <td width=75><b>Job Number:</b></td>
              <td width=150><?php echo $myJob->job_number; ?></td>
              <td width=75><b>DOB:</b></td>
              <td width=150><?php echo $myJob->dob; ?></td>
              <td width=75><b>Creator:</b></td>
              <td><?php echo $myJob->user_fname." ".$myJob->user_lname; ?></td>
            </tr>
            <tr>
              <td><b>Jurisdiction:</b></td>
              <td><?php echo $myJob->jurisdiction; ?></td>
              <td><b>Permit #:</b></td>
              <td><?php echo $myJob->permit; ?></td>
              <td><b>Expiration:</b></td>
              <td><?php echo $myJob->permit_expire; ?></td>
            </tr>
            <tr>
              <td><b>Salesman:</b></td>
              <td colspan=5><?php echo $myJob->salesman_fname." ".$myJob->salesman_lname; ?></td>
            </tr>
            <tr>
              <td><b>Job Type:</b></td>
              <td><?php echo $myJob->job_type; ?></td>
              <td><b>Type Note:</b></td>
              <td colspan=3><?php echo $myJob->job_type_note; ?></td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <br><br>
    <table border="0" width='800' align="center">
      <tr>
        <td>
          <span class='mainpagetitle'>Job Tasks</span>
        </td>
      </tr>
      <tr>
        <td style='border: 1px solid black;'>
          <table border="0" cellspacing="0" cellpadding=2 width="100%">
<?php

$sql = "select task_type.task, users.fname, users.lname, tasks.duration, tasks.start_date".
           " from tasks, task_type, users".
           " where users.user_id=tasks.contractor and tasks.task_type=task_type.task_type_id and tasks.job_id='".$myJob->job_id."'";
$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
            <tr><td><center><b>No Tasks</b></center></td></tr>
<?php
}
while(list($task, $fname, $lname, $duration, $start_date)=mysqli_fetch_row($res))
{
?>
            <tr><td class="smalltitle"><?php echo $task; ?></td></tr>
            <tr>
              <td width=70><b>Contractor:</b></td>
              <td width=150><?php echo $fname." ".$lname; ?></td>
              <td width=70><b>Start Date:</b></td>
              <td width="25%"><?php echo $start_date; ?></td>
              <td width="25%"><b>Allotted Duration:</b></td>
              <td><?php echo $duration; ?></td>
            </tr>
            <tr colspan=2><td>&nbsp;</td></tr>
<?php
}

?>
          </table>
        </td>
      </tr>
    </table>
    <br><br>
    <table border="0" width='800' align="center">
      <tr>
        <td>
          <span class='mainpagetitle'>Notes</span>
        </td>
      </tr>
      <tr height=200>
        <td style='border: 1px solid black;'>
          <table border="0" cellspacing="0" cellpadding=2 width="100%">
            <tr>
              <td>&nbsp;</td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
    <br><br>
    <table border="0" width='800' align="center">
      <tr>
        <td>
          <center>Generated by <b><?=APP_NAME?></b></center>
        </td>
      </tr>
    </table>
<script>
    $(document).ready(function(){
        window.print();
    });
</script>
</body>
</html>
<?php

$str = ob_get_clean();
echo $str;

/*$file = time().'.doc';
touch($file);
$fh = fopen($file,'w');
fwrite($fh,$str);
fclose($fh);*/

/*
$to = 'cbm3384@gmail.com';
$subject = 'Material Order';
$headers = "MIME-Version: 1.0\r\n";
$headers .= "Content-type: text/html; charset=iso-8859-1\r\n";
$headers  .= "From: ".$from_email." \r\n";

mail($to, $subject, $str, $headers);
*/
?>
