<?php

include '../common_lib.php';

echo ViewUtil::loadView('doc-head');



$id = RequestUtil::get('id');

$action = RequestUtil::get('action');

$job_ids = RequestUtil::get('job_id');

$myJob = new Job($job_ids);
$contactheder = UserModel::getContactsHeader();
//echo "<pre>";print_r($myJob);die;
$errors = array();
if($action=='del')
{
    $sql = "delete from job_contacts where job_contact_id=".$id." limit 1";
   
    DBUtil::query($sql);
     ?>
    <script>
      Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $job_ids; ?>&tab=contacts', 'jobscontainer',true,true,true);
    </script>
  <?php
  
}
if($action=='edit')
{
  $sql = "select * from job_contacts where job_contact_id = '$id' limit 1";
  $res = DBUtil::query($sql);
  if(!mysqli_num_rows($res)) {
      UIUtil::showModalError('Trip not found!');
  }
  list($id, $job_id, $contact_header_id,$contact_note)=mysqli_fetch_row($res);
  if(!empty($_POST['contact_note']))
  {
      $error=0;
      $contact_header = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('contact_header'));
      $contact_note = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('contact_note'));
      $sql = "update job_contacts set contact_header_id='".$contact_header."', contact_note='".$contact_note."' where job_contact_id=".$id." limit 1";
      DBUtil::query($sql);
      $data['message'] = $contact_note;
      if(!empty($_POST['send_conractor']))
      {
          NotifyUtil::emailFromTemplate('contact_note', $myJob->salesman_id,'',$data);
      }
      
  ?>
    <script>
      Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $job_ids; ?>&tab=contacts', 'jobscontainer',true,true,true);
    </script>
  <?php
  }
  else
  {
    $error=1;
  }

  $job_contacts = DBUtil::getRecord('job_contacts',$id,'job_contact_id');
}

else if($action=='add')
{
  if(!empty($_POST['contact_note']))
  {
      $error=0;
      $contact_header = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('contact_header'));
      $contact_note = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('contact_note'));
      $sql = "insert into job_contacts (job_id,contact_header_id,contact_note,created_by)  VALUES ('$job_ids','$contact_header','$contact_note','{$_SESSION['ao_userid']}')";
      DBUtil::query($sql);
      
      $sql = "select fname,lname from customers where customer_id = '$myJob->customer_id' limit 1";
      $res = DBUtil::query($sql);
      $custom = mysqli_fetch_row($res);
      
      $data['message']='New Contact information has been posted  for job number <b>'.$myJob->job_number.'</b> (Customer: <b>'.$custom[1].' '.$custom[0].'</b>)<br><br> Contact Note: '.$contact_note.'<br><br><br><a href="'.ACCOUNT_URL.'/?p=jobs&id='.$job_ids.'">'.ACCOUNT_URL.'/?p=jobs&id='.$job_ids.'</a>';
      
      
      if(!empty($_POST['send_conractor']))
      {
          NotifyUtil::emailFromTemplate('contact_note', $myJob->salesman_id,'',$data);
      }
  ?>
    <script>
      Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $job_ids; ?>&tab=contacts', 'jobscontainer',true,true,true);
    </script>
  <?php
  }
  else
  {
      $error=1;
  }
}

if(isset($_POST['contact_note']) && $error==1)
    $errors[]='Contact note cannot be blank';

?>

    <?=AlertUtil::generate($errors, 'error', TRUE)?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                Contact Details
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
$query_str='id='.$id.'&action='.$action;
?>

          <tr>

            <td>

              <form method="post" name='edit_trip' action='?<?php echo $query_str;?>' enctype="multipart/form-data">

                <table border="0" width="100%" cellspacing="0" cellpadding="0">

                    <tr> 

                    <td>&nbsp;

                    </td> 

                    </tr>

                    <tr>                  

                    <input  type="hidden" name="job_id" value="<?php if(isset($job_ids)) echo $job_ids; ?>">

                    <td class="listitem" width="200"><b>Contact Header:</b> <span class="red">*</span></td>

                    <td class="listitemnoborder">

                        <select style="width:200px;" name="contact_header">
                            <?php foreach($contactheder as $row){?>
                            <option <?php if(isset($contact_header_id) &&  $row['contact_header_id']==$contact_header_id) echo 'selected';?> value="<?php echo $row['contact_header_id'];?>"><?php echo $row['contact_name'];?></option>
                            <?php }?>

                        </select>
                    </td>

                  </tr>                  

                  

                  <tr>

                    <td class="listitem" ><b>Contact Note:</b> <span class="red">*</span></td>

                    <td class="listitemnoborder">

                        <textarea style="width: 500px;" rows="4" name="contact_note"><?php echo $job_contacts['contact_note'];?></textarea>

                    </td>


                  </tr>

                </table>

             

              </td>

            </tr>

          </table>

          <table border="0" width="100%" cellpadding="0" cellspacing="0">

            <tr>
              <td><input style="margin-left:30px;" type="checkbox" name="send_conractor">Send to Contractor</td>
              <td align="right" class="listrow">

                <?php if($action=='edit') {?>

                  <input type="button" value="Delete" onclick='if(confirm("Are you sure?")){window.location="edit_contact.php?id=<?php echo $id; ?>&job_id=<?php echo $job_ids; ?>&action=del";}'>

                <?php }?>



                  <input type="submit" value="Save">

                

              </td>

            </tr>

          </table>



          </form>



        </td>

      </tr>

    </table>

  </body>

</html>



<script type="text/javascript">



  function readURL(input) 

  {

        console.log(input.name);

        if (input.files && input.files[0]) {

            var reader = new FileReader();



            reader.onload = function (e) {

                $('#'+input.name)

                    .attr('src', e.target.result)

                    .width(150)

                    .height(150);

            };



            reader.readAsDataURL(input.files[0]);

        }

    }



    </script>