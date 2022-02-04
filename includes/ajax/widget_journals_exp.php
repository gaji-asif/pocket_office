<?php

include '../common_lib.php'; 
if(!viewWidget('widget_journals')||!ModuleUtil::checkAccess('view_jobs')) { die(); }

echo ViewUtil::loadView('doc-head');
?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Recent Journals
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
          <table width="100%" border="0" cellpadding=5 cellspacing="0">
<?php
$sql = "select jobs.job_id, jobs.job_number, journals.text".
       " from journals, users, jobs".
       " left join subscribers on (subscribers.job_id=jobs.job_id)".
       " left join tasks on (tasks.job_id=jobs.job_id)".
       " where journals.job_id=jobs.job_id and (jobs.salesman='".$_SESSION['ao_userid']."' or jobs.referral='".$_SESSION['ao_userid']."' or tasks.contractor='".$_SESSION['ao_userid']."' or subscribers.user_id='".$_SESSION['ao_userid']."') group by journals.journal_id order by journals.journal_id desc limit 20";

$res = DBUtil::query($sql);

if(mysqli_num_rows($res)==0)
{
?>
            <tr valign='top'>
              <td style='font-weight: bold;' align="center"><b>No Journals Found</b></td>
            </tr>
<?php
}
else
{
?>
            <tr valign='top'>
              <td colspan=3 class='widgetheader'>
                <table width="100%" border="0" cellpadding="0" cellspacing="0">
                  <tr>
                    <td width=112 style='padding-left:4px;'><b>ID Number</b></td>
                    <td><b>Journal</b></td>
                  </tr>
                </table>
              </td>
            </tr>
<?php
}

$i=1;
while(list($job_id, $job_num, $journal)=mysqli_fetch_row($res))
{
  $class='odd';
  if($i%2==0)
    $class='even';

  $journal_str = prepareText($journal);
  if(strlen($journal_str)>5)
    $journal_str = substr(trim($journal_str), 0, 50)."...";

?>
            <tr valign='top' class='<?php echo $class; ?>' onclick='parent.location="<?=ROOT_DIR?>/jobs.php?tab=journals&id=<?php echo $job_id; ?>";' onmouseover='hoverRow(this);' onmouseout='hoverRowOut(this, "<?php echo $class; ?>");'>
              <td width="25%"><b><?php echo $job_num; ?></b></td>
              <td class='smallnote'><?php echo $journal_str; ?></td>
            </tr>
<?php
  $i++;
}
?>
          </table>
        </tr>
      </td>
    </table>
  </body>
</html>