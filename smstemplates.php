<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder']!=1)
  die("Insufficient Rights");


$action = $_GET['action'];
$id = $_GET['id'];

if(isset($_POST['id']))
{
  $id = $_POST['id'];
  $subject = $_POST['subject'];
  $body = $_POST['body'];
  $is_active = $_POST['active'];

  if($subject=='' || $body=='')
  {
    UIUtil::showAlert('Required Fields Missing');
  }
  else
  {
    $body = mysqli_real_escape_string(DBUtil::Dbcont(),$body);
    $sql = 'update sms_templates set subject="'.$subject.'", text="'.$body.'", is_active="'.$is_active.'" where sms_template_id="'.$id.'" and account_id="'.$_SESSION['ao_accountid'].'" limit 1';
    DBUtil::query($sql)or die(mysqli_error());
    UIUtil::showAlert('Template Updated');
  }
}
else if($id!='' && $action=='revert')
{
  $sql = "select hook from sms_templates where sms_template_id='".$id."' and account_id='".$_SESSION['ao_accountid']."' limit 1";
  $res = DBUtil::query($sql)or die(mysqli_error());
  list($hook)=mysqli_fetch_row($res);

  if($hook!='')
  {
    $sql = "select subject, text from sms_templates_default where hook='".$hook."' limit 1";
    $res = DBUtil::query($sql)or die(mysqli_error());
    list($subject, $text, $is_active)=mysqli_fetch_row($res);

    $sql = 'update sms_templates set subject="'.$subject.'", text="'.$text.'", is_active="'.$is_active.'" where sms_template_id="'.$id.'" and account_id="'.$_SESSION['ao_accountid'].'" limit 1';
    DBUtil::query($sql)or die(mysqli_error());
    UIUtil::showAlert("Template successfully reverted to original");
  }
}
else if($action=='revertall')
{
  $sql = "select subject, text, hook from sms_templates_default";
  $res = DBUtil::query($sql)or die(mysqli_error());

  while(list($subject, $text, $hook, $is_active)=mysqli_fetch_row($res))
  {
    $sql = 'update sms_templates set subject="'.$subject.'", text="'.$text.'", is_active="'.$is_active.'" where hook="'.$hook.'" and account_id="'.$_SESSION['ao_accountid'].'"';
    DBUtil::query($sql)or die(mysqli_error());
  }
  UIUtil::showAlert("All templates successfully reverted to originals");
}

$account_hooks = array('[>ACCOUNTURL<]', '[>ACCOUNTNAME<]');
$account_values = array(ACCOUNT_URL, $_SESSION['ao_accountname']);

$from_hooks = array('[>FROMFNAME<]', '[>FROMLNAME<]', '[>FROMEMAIL<]', '[>FROMUSERNAME<]', '[>FROMPASSWORD<]');
$from_values = array('John', 'Smith', 'john.smith@' . DOMAIN, 'john.smith', 'password123');

$to_hooks = array('[>TOFNAME<]', '[>TOLNAME<]', '[>TOEMAIL<]', '[>TOUSERNAME<]', '[>TOPASSWORD<]');
$to_values = array('Jane', 'Smith', 'jane.smith@' . DOMAIN, 'jane.smith', 'password123');

$job_hooks = array('[>JOBNUMBER<]', '[>JOBID<]', '[>CUSTFNAME<]', '[>CUSTLNAME<]', '[>CUSTADDRESS<]', '[>CUSTCITY<]', '[>CUSTSTATE<]', '[>CUSTZIP<]', '[>CUSTPHONE<]', '[>SALESMANFNAME<]', '[>SALESMANLNAME<]', '[>HASH<]', '[>STAGENUM<]', '[>CSVSTAGES<]');
$job_values = array('ABC12345', '1111', 'John', 'Doe', '123 Any Street', 'Anytown', 'AL', '12345', '555-555-5555', 'Dave', 'Miller', '30cfbbc70a63835d9d0a83132ddb1111', '2', 'Estimate Request');

$task_hooks = array('[>CONTRACTORFNAME<]', '[>CONTRACTORLNAME<]', '[>TASKTYPE<]', '[>DURATION<]', '[>NOTES<]');
$task_values = array('Mike', 'Johnson', 'Gutters', '5', 'Notes on the Gutter Task');

$event_hooks = array('[>EVENTTITLE<]', '[>EVENTSTARTDATE<]', '[>EVENTENDDATE<]', '[>EVENTDESCRIPTION<]', '[>EVENTTIME<]');
$event_values = array('Company Event', '12-05-2013', '12-05-2013', 'Company event to celebrate', '1:00 PM');

$journal_hooks = array('[>JOURNALTEXT<]');
$journal_values = array('Went to property and assessed damage on roof and gutters');

?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - SMS Text Templates</h1>
    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr>
        <td>
          <b>Jump to:</b>
          <select id='jump' onchange='window.location.hash=this.value;'>
            <option value=''></option>
<?php
$sql = "select sms_template_id, subject from sms_templates where account_id='".$_SESSION['ao_accountid']."' order by subject asc";
$res = DBUtil::query($sql)or die(mysqli_error());
while(list($id, $subject)=mysqli_fetch_row($res))
{
?>
            <option value='template<?php echo $id; ?>'><?php echo $subject; ?></option>
<?php
}
?>
          </select>
		  <input type='button' value='REVERT ALL TEMPLATES' onclick='if(confirm("Are you sure you want to revert ALL changes made to SMS Templates?")){window.location="smstemplates.php?action=revertall";}'>
        </td>
      </tr>
      <tr>
        <td>
          <table border=0 width="100%" align='left' cellpadding=0 cellspacing=0 class='containertitle'>
            <tr>
              <td width=250>
                Subject
              </td>
              <td>
                Body
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border=0 width="100%" align='left' cellpadding=5 cellspacing=0 class='infocontainernopadding'>
<?php
$sql = "select sms_template_id, subject, text, is_active from sms_templates where account_id='".$_SESSION['ao_accountid']."' order by subject asc";
$res = DBUtil::query($sql)or die(mysqli_error());

$i=1;
while(list($id, $subject, $body, $is_active)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';

  $checked = '';
  if($is_active == '1')
  {
      $checked = 'checked';
  }

 ?>
            <tr valign='top' class='<?php echo $class; ?>'>
              <td class='smalltitle' width=239>
                <a name='template<?php echo $id; ?>'></a>
                <form method='post' name='template<?php echo $id; ?>' action='#template<?php echo $id; ?>' style='margin-bottom:0;'>
                <?php echo $subject; ?>
                <input type='text' name='subject' size=30 value='<?php echo $subject; ?>'>
                <input name='id' value='<?php echo $id; ?>' type='hidden'>
                <input type='submit' value='Save'>
                <div class="navuserinfo">Active: <input type='checkbox' name='active' value='1' <?php echo $checked; ?> /></div>
                <input type='button' value='Revert to Original' onclick='if(confirm("Are you sure? This cannot be undone!")){window.location="?action=revert&id=<?php echo $id; ?>";}'>
              </td>
              <td>
                <textarea name='body' style='width:100%;' rows=5><?php echo trim($body); ?></textarea>
                </form>
              </td>
            </tr>
<?php
  $body = str_replace($account_hooks, $account_values, $body);
  $body = str_replace($from_hooks, $from_values, $body);
  $body = str_replace($to_hooks, $to_values, $body);
  $body = str_replace($job_hooks, $job_values, $body);
  $body = str_replace($task_hooks, $task_values, $body);
  $body = str_replace($event_hooks, $event_values, $body);
  $body = str_replace($journal_hooks, $journal_values, $body);
?>
            <tr valign='top' class='<?php echo $class; ?>'>
              <td></td>
              <td>
                <b>Preview:</b>
                <table style='background: #ffffff; border: 1px solid #0085CB; font-family: courier; font-size: 12px; margin-left: 2px;' width='200' cellspacing=0 cellpadding=2>
                  <tr>
                    <td>
                      Subject: <?php echo $subject; ?>
                      <br>
                      <?php echo $body; ?>
                    </td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr valign='top' class='<?php echo $class; ?>'>
              <td colspan=2 align='center'>
                <a href='#' class='boldlink'>Top</a>
              </td>
            </tr>
<?php
  $i++;
}
?>
          </table>
        </td>
      </tr>
      <tr><td colspan=2>&nbsp;</td></td>
    </table>
  </body>
</html>
