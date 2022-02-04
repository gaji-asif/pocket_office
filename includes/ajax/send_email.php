<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');

$id = RequestUtil::get('id');



$action = RequestUtil::get('action');



$job_ids = RequestUtil::get('job_id');



$myJob = new Job($job_ids);



$user = UserModel::get($myJob->user_id);

$from_email = EMAIL_REPLY_TO; //$user['email'];

$from_name = $user['fname'].' '.$user['lname'];

// print_r(RequestUtil::getAll());

$errors = array();

$success = array();

// if($action=='del')

// {

//     $sql = "delete from job_contacts where job_contact_id=".$id." limit 1";

   

//     DBUtil::query($sql);

//      ?>

     <script>

//       Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $job_ids; ?>&tab=contacts', 'jobscontainer',true,true,true);

//     </script>

   <?php

  

// }

// if($action=='edit')

// {

//   $sql = "select * from job_contacts where job_contact_id = '$id' limit 1";

//   $res = DBUtil::query($sql);

//   if(!mysqli_num_rows($res)) {

//       UIUtil::showModalError('Trip not found!');

//   }

//   list($id, $job_id, $contact_header_id,$contact_note)=mysqli_fetch_row($res);

//   if(!empty($_POST['contact_note']))

//   {

//       $error=0;

//       $contact_header = RequestUtil::get('contact_header');

//       $contact_note = RequestUtil::get('contact_note');

//       $sql = "update job_contacts set contact_header_id='".$contact_header."', contact_note='".$contact_note."' where job_contact_id=".$id." limit 1";

//       DBUtil::query($sql);

//       $data['message'] = $contact_note;

//       if(!empty($_POST['send_conractor']))

//       {

//           NotifyUtil::emailFromTemplate('contact_note', $myJob->salesman_id,'',$data);

//       }

      

//   ?>

     <script>

//       Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $job_ids; ?>&tab=contacts', 'jobscontainer',true,true,true);

//     </script>

   <?php

//   }

//   else

//   {

//     $error=1;

//   }



//   $job_contacts = DBUtil::getRecord('job_contacts',$id,'job_contact_id');

// }



// else 

if($action=='send')

{
  if(!empty($_POST['email_note']))
  {   
      $mail = new PHPMailer();
      $email_note       = RequestUtil::get('email_note');
      $email_subject    = RequestUtil::get('email_subject');
      $email_send_to    = RequestUtil::get('email_send_to');
      $email_files      = $_FILES['email_files'];
      $files            = '';
      if (!empty($email_files)) {
        for($i=0; $i < count($email_files['name']); $i++) { 
            $name = $email_files['name'][$i];
            $tmp_name = $email_files['tmp_name'][$i];
            $pieces = explode('.', $name);
            $new_filename = md5(mt_rand() . time()) . "." . end($pieces);
            $new_path = EMAIL_ATTACHMENT . $new_filename;
            if (move_uploaded_file($tmp_name, $new_path)) {
                $files .= EMAIL_ATTACHMENT_URL.$new_filename.',';
                $mail->addAttachment($new_path, $new_filename);
            }
        }
        $files = rtrim($files, ',');
      }
      //Recipients
      $mail->setFrom($from_email, $from_name);
      $email_send = '';
      foreach ($email_send_to as $key => $value) {
        $mail->addAddress( $value );               // Name is optional
        $email_send .= $value.',';
      }
      $email_send = rtrim($email_send, ',');
      $mail->addReplyTo(EMAIL_REPLY_TO, $from_name.' via Xactbid');
      // Content
      $mail->isHTML(true);                                  // Set email format to HTML
      $subject = $mail->Subject = '[Contact #'.$myJob->job_number.'] '.$email_subject;
      $mail->MsgHTML('<p>'.$email_note.'</p>');
      if ($mail->send()) {
        $success[] = 'email is successfully sent.';
        $sql = " INSERT INTO job_email 

              (job_id,

              job_number,

              email_note,

              email_files,

              email_subject,

              email_send_to,

              from_email,

              from_name,

              email_type,

              created_by,

              email_send_to_cc

              ) 

              VALUES 

              (

              '$job_ids',

              '$myJob->job_number',

              '$email_note',

              '$files',

              '$subject',

              '$email_send',

              '$from_email',

              '$from_name',

              1,

              '{$_SESSION['ao_userid']}',
              ''
            )";
        $result = DBUtil::query($sql);

        ?>

          <script>

              Request.makeModal('<?=AJAX_DIR?>/get_job.php?id=<?php echo $job_ids; ?>&tab=email', 'jobscontainer',true,true,true);

          </script>

        <?php
      } else {
        $errors[] = 'some error. please try again.';
      }

  }

}



?>



    <?=AlertUtil::generate($errors, 'error', TRUE)?>

    <?=AlertUtil::generate($success, 'success', TRUE)?>

    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">

      <tr>

        <td>

          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">

            <tr valign="center">

              <td>

                Send Email

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

              $query_str='id='.$id.'&action='.$action.'&job_id='.$job_ids;

            ?>



            <tr>

              <td>

                <form method="post" name='edit_trip' action='?<?php echo $query_str;?>' enctype="multipart/form-data">

                  <table border="0" width="100%" cellspacing="0" cellpadding="0">

                    <tr> 

                      <td>&nbsp;</td> 

                    </tr>

                    <input  type="hidden" name="job_id" value="<?php if(isset($job_ids)) echo $job_ids; ?>">

                    <tr>

                      <td class="listitem" ><b>Email Note:</b> <span class="red">*</span></td>

                      <td class="listitemnoborder">

                          <textarea style="width: 500px;" required="" rows="7" name="email_note"><?php echo isset($job_contacts['contact_note']) ? $job_contacts['contact_note'] : '';?></textarea>

                      </td>

                    </tr>

                    <tr>

                      <td class="listitem" ><b>Attachments </b> <span class="red">*</span></td>

                      <td class="listitemnoborder">

                          <input style="width: 500px;" type="file"  name="email_files[]" multiple="">

                      </td>

                    </tr>

                    <tr>

                      <td class="listitem" ><b>Subject:</b> <span class="red">*</span></td>

                      <td class="listitemnoborder">

                          <input style="width: 500px;" required="" type="text" name="email_subject">

                      </td>

                    </tr>

                    <tr>

                      <td class="listitem" ><b>Send To:</b> <span class="red">*</span></td>

                      <td class="listitemnoborder">

                        <style type="text/css">

                          .tss-multiselect-search {

                            width: 500px;

                          }

                          .testuser .removeuser {

                            display: block;

                            margin-bottom: 10px;

                          }

                          .testuser .removeuser .icon-remove {

                            display: none;

                          }

                        </style>

                          <div class="testuser"></div>

                          <select name="email_send_to[]" required="" class="tss-multi" data-placeholder="Send To" multiple>

                            <?php 



                                if(!empty($myJob->customer_id)) {

                                  $myCustomer = new Customer($myJob->customer_id);
                                  if(!empty($myCustomer->get('email'))) {
                            ?>

                                <optgroup  label="Insured">

                                  <option value="<?= $myCustomer->get('email') ?>"><?= $myCustomer->get('fname') ?> <?= $myCustomer->get('lname') ?> (<?= $myCustomer->get('email') ?>)</option>

                                </optgroup>

                            <?php
                                  }
                                }



                                if(!empty($myJob->salesman_id)) {

                                  $salesman = new User($myJob->salesman_id);

                            ?>

                                <optgroup  label="Customers">

                                  <option value="<?= $salesman->email ?>"><?= $salesman->fname ?> <?= $salesman->lname ?> (<?= $salesman->email ?>)</option>

                                </optgroup>

                            <?php

                                }
                                $subscribers = $myJob->getSubscribers();
                                if(!empty($subscribers)) {
                                  echo '<optgroup  label="Subscribers">';
                                  foreach($subscribers as $subscriber) {
                            ?>
                                    <option value="<?= $subscriber['semail'] ?>"> <?=$subscriber['fname']?> <?=$subscriber['lname']?> (<?=$subscriber['semail']?>) </option>
                            <?php
                                  }
                                  echo '</optgroup>';
                                }

                                if(!empty($myJob->insurance_id)) {

                                  $InsuranceDetail = InsuranceModel::getProviderById($myJob->insurance_id);

                            ?>

                                <optgroup  label="Provider's">

                                  <option value="<?= $InsuranceDetail['email'] ?>"><?= $InsuranceDetail['email'] ?></option>

                                </optgroup>

                            <?php

                                }

                                 if(!empty($myJob->adjuster_email)) {

                            ?>

                                <optgroup  label="Adjuster">

                                  <option value="<?= $myJob->adjuster_email ?>"><?= $myJob->adjuster_name ?> (<?= $myJob->adjuster_email ?>)</option>

                                </optgroup>

                            <?php

                                }
                             ?>
                          </select>

                      </td>

                    </tr>

                    <tr>

                      <td class="listitem"></td>

                      <td class="listitemnoborder" >

                          <input type="submit" class="btn btn-primary" value="Send Email">

                      </td>

                    </tr>

                  </table>

                  </form>

                </td>

            </tr>

          </table>

        </td>

      </tr>

    </table>



  </body>

</html>

<script type="text/javascript">

  // jQuery(document).on('click', '.removeuser .icon-remove', function() {

  //     jQuery(this).parent().remove();

  // });



</script>