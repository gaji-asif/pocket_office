<?php

include '../../common_lib.php';

echo ViewUtil::loadView('doc-head');


$timer_id = RequestUtil::get('timer_id');
$job_id = RequestUtil::get('job_id');
$action = RequestUtil::get('action');

$myJob = new Job($job_id);
//echo "<pre>";print_r($myJob);die;

$sql = "SELECT t1.job_time_record_id,t1.record_date,t1.start_time,t1.end_time,t1.task_id,t1.remarks from job_time_records as t1 
        where t1.job_time_record_id=".$timer_id."";
$usertime = DBUtil::queryToArray($sql);

$timer = $usertime[0];

$errors = array();
$error = 0;
if($action=='submit')
{
  if(!empty($_POST['task_type']))
  {

      $task_id = $_POST['task_type'];
      $remarks = $_POST['remarks'];
      $start_time = $_POST['start_time'];
      $end_time = $_POST['end_time'];

      $sql = "UPDATE job_time_records
                SET end_time=NOW(), status='stop',task_id='$task_id',start_time='$start_time',end_time='$end_time',remarks='$remarks'
                WHERE  job_time_record_id='$timer_id'";
      DBUtil::query($sql);
  ?>
    <script>
      $(document).ready(function()
      {
          try{
             var opener = window.parent;
             opener.location.reload();
             var closebutton = $('.btn-close-modal');   
             closebutton.trigger('click');
             return false;
            }
           catch(e)
           {
              alert(e);
           }
      });        
    </script>

 <?php
  }
  else
  {
      $error=1;
  }
}

if(isset($_POST['task_type']) && $error==1)
    $errors[]='Please Select Type of Task!';


?>

    <?=AlertUtil::generate($errors, 'error', TRUE)?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Working Time
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr id="add_contacts">
        <td class="infocontainernopadding">
          <table width="100%" border="0" cellspacing="0" cellpadding="0">
<?php
$query_str='job_id='.$job_id.'&timer_id='.$timer_id.'&action=submit';
?>

          <tr>

            <td>
              <form method="post" name='stop_timer' action='?<?php echo $query_str;?>' enctype="multipart/form-data">
                <table border="0" width="100%" cellspacing="0" cellpadding="0">
                    <tr> 
                    <td>&nbsp;
                    </td> 
                    </tr>

                    <tr>    
                    <td class="listitem" width="200"><b>Types of task:</b> <span class="red">*</span></td>
                    <td class="listitemnoborder">
                    <?php
                      $sql = "SELECT * from timer_task_types as t1  where status='y'";
                      $task_types = DBUtil::queryToArray($sql);
                    ?>
                        <select style="width:200px;" name="task_type">
                            <option value="">--Select--</option>
                            <?php foreach($task_types as $row){?>
                            <option <?php echo ($timer['task_id']==$row['task_id'])?'selected':'';?> value="<?php echo $row['task_id'];?>"><?php echo $row['name'];?></option>
                            <?php }?>
                        </select>
                    </td>
                  </tr>     

                  <tr>
                    <td class="listitem" ><b>Remarks:</b></td>
                    <td class="listitemnoborder">
                        <textarea style="width: 500px;" rows="4" name="remarks"><?php echo $timer['remarks'];?></textarea>
                    </td>
                  </tr>    

                  <tr>
                      <td class="listitemnoborder"><b>Start Time:</b></td>
                      <td class="listrownoborder">
                          <input type="text" name="start_time" value="<?php echo $timer['start_time'];?>" />
                      </td>
                  </tr>

                  <tr>
                      <td class="listitemnoborder"><b>End Time:</b></td>
                      <td class="listrownoborder">
                          <input type="text" name="end_time" value="<?php echo $timer['end_time'];?>" />
                      </td>
                  </tr>
                </table>

              </td>

            </tr>

          </table>

          <table border="0" width="100%" cellpadding="0" cellspacing="0">

            <tr>
              <td></td>
              <td align="right" class="listrow">
                  <input type="submit" value="Submit">
              </td>

            </tr>

          </table>



          </form>



        </td>

      </tr>

    </table>

  </body>

</html>

