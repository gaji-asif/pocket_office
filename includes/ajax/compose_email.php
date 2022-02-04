<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
<style type="text/css">
  .listitem{
    font-size: 15px;
  }
  .compose_email tr{
    margin-bottom: 10px;
  }
</style>
<?php
include '../common_lib.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

include 'phpmailer/src/Exception.php';
include 'phpmailer/src/PHPMailer.php';
include 'phpmailer/src/SMTP.php';


$emailList = EmailModel::getEmailList();
$allLeads = EmailModel::getLeadList();
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');
$myUser = UserModel::getMe();
$user_name = $myUser->fname . " " . $myUser->lname;
$user_id = $_SESSION['ao_userid'];
$user_email = $myUser->email;

if(!$myUser->exists()) {
    UIUtil::showModalError('Could not retrieve user data');
}

$id = RequestUtil::get('id');
$action = RequestUtil::get('action');
$errors = array();
$success = array();

if(!empty($_POST['email_note']))
{ 
    $email_note       = RequestUtil::get('email_note');
    $email_subject    = RequestUtil::get('email_subject');
    $email_send_to    = RequestUtil::get('email_send_to');
    $from_email    = RequestUtil::get('from_email');

    $lead_no    = RequestUtil::get('lead_no');
    $email_files = $_FILES['email_files']['name'];
    $mail = new PHPMailer(true); 
    try {
    
    //echo $lead_no;exit;

    if(isset($_FILES['email_files']['name'])) {
      $uploadfile = $_SERVER['DOCUMENT_ROOT'].'/ums/xactbid/uploads/email_files/'.$email_files;
      move_uploaded_file($_FILES['email_files']['tmp_name'], $uploadfile);
    }
    else{
      $uploadfile = '';
    }

    // Server settings
    //$mail->SMTPDebug = SMTP::DEBUG_SERVER; // for detailed debug output
     // echo "test"; exit;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->SMTPAuth = true;
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port = 587;

    $mail->Username = 'asif01728@gmail.com'; // YOUR gmail email
    $mail->Password = 'Jhalaktangail9'; // YOUR gmail password
    $mail->From = $from_email;

    $full_name = '';
    if(!empty($from_email)){
      $from_email_details = EmailModel::fromEmailDetails($from_email);
      $full_name = $from_email_details[0]['fname']." ".$from_email_details[0]['lname'];
      // echo "<pre>";
      // print_r($from_email_details);
    }
   

    // Sender and recipient settings 
    $mail->setFrom($from_email, $full_name);
    $mail->addAddress($email_send_to, $email_send_to);
    $mail->addReplyTo($from_email, $full_name); // to set the reply to
    $mail->addAttachment($uploadfile, 'User uploaded File');

    // Setting the email content
    $mail->IsHTML(true);
    $mail->Subject = $email_subject;
    $mail->Body = $email_note."<br><br> <strong>Thanks</strong>";
    $mail->AltBody = 'Plain text message body for non-HTML email client. Gmail SMTP email body.';

    $mail->send();
    // echo "Email message sent.";

    $success[] = 'email has been sent successfully.';
    $sql = "INSERT INTO job_email (
          job_id,
          email_note,
          email_files,
          email_subject,
          email_send_to,
          from_email,
          from_name,
          email_type,
          created_by,
          email_send_to_cc) 
          VALUES 
          (
          '$lead_no',
          '$email_note',
          '$uploadfile',
          '$email_subject',
          '$email_send_to',
          '$from_email',
          '$from_email',
          1,
          '$user_id',
          ''
          )";
        $result = DBUtil::query($sql);

        // echo "<meta http-equiv='refresh' content='0'>";

    


} catch (Exception $e) {
    //echo "Error in sending email. Mailer Error: {$mail->ErrorInfo}";
    $errors[] = 'some error. please try again.';
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

                <strong>Send New Email</strong>

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

              //$query_str='id='.$id.'&action='.$action.'&job_id='.$job_ids;

            ?>



            <tr>

              <td>

                <!-- <form method="post" name='edit_trip' action='?<?php echo $query_str;?>' enctype="multipart/form-data"> -->

                  <form method="post" name='edit_trip' action='' enctype="multipart/form-data">

                  <table class="compose_email" border="0" width="100%" cellspacing="0" cellpadding="0">

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

                      <td class="listitem" ><b>Subject:</b> <span class="red">*</span></td>

                      <td class="listitemnoborder">

                          <input style="width: 500px;" required="" type="text" name="email_subject" class="form-control">

                      </td>

                    </tr>
                    
                    
                    <tr>
                      <td class="listitem" ><b>Send To:</b> <span class="red">*</span></td>
                      <td class="listitemnoborder">                      
                      <link rel="stylesheet" href="../../css/selectsupport/chosen.css">

                        <style type="text/css">
                          .testuser .removeuser {
                            display: block;
                            margin-bottom: 10px;
                          }

                          .testuser .removeuser .icon-remove {
                            display: none;
                          }

                          .custom {
                            background: #fff;
                            border: 1px solid #dcdcdc;
                            border-left-width: 3px;
                            clear: both;
                            color: #666;
                            cursor: pointer;
                            font-size: 12px;
                            height: 25px;
                            line-height: 25px;
                            margin: 0;
                            outline: 0;
                            overflow: hidden;
                            padding: 0 4px;
                            text-overflow: ellipsis;
                            white-space: nowrap;
                            width: 500px;
                          }
                        </style>
                          <div class="testuser"></div>
                         <!--  <select name="email_send_to[]" required="" class="chosen-select custom" data-placeholder="Choose..." multiple>
                          <?php 
                                foreach($emailList as $email_ls) {
                             ?>
                             <option value="<?=$email_ls['email']?>"><?=$email_ls['email']?></option>
                             <?php } ?>
                          
                        </select>  --> 

                       <input style="width: 500px;" required="" type="text" name="email_send_to" class="form-control">                   
                          
                      </td>
                    </tr>


                    <tr>
                      <td class="listitem" ><b>Lead :</b> <span class="red">*</span></td>
                      <td class="listitemnoborder">                      
                      
                          <div class="testuser"></div>
                          <select name="lead_no" required="" class="chosen-select custom form-control" data-placeholder="Select lead no">
                            <option value="">Select Lead No</option>
                             <?php 
                                foreach($allLeads as $allLead) {
                             ?>
                             <option value="<?=$allLead['job_id']?>"><?=$allLead['job_number']?>(<?php echo $allLead['fname'].' '.$allLead['lname']?>)</option>
                             <?php } ?>
                          
                        </select>                        
                          
                      </td>
                    </tr>
                    
                    <tr>
                      <td class="listitem"><b>Send From:</b> <span class="red">*</span></td>
                      
                      <td class="listitemnoborder">
                          <div class="testuser"></div>
                          <select name="from_email" required="" class="custom form-control" data-placeholder="Send From">
                            <option value="">Select From</option>
                            <option value="<?=$myUser->email?>"><?=$myUser->email?></option>
                          </select>
                      </td>
                    </tr>
                    <tr>

                      <td class="listitem" ><b>Attach Your File </b> <span class="red">*</span></td>

                      <td class="listitemnoborder">

                          <input style="width: 500px;" type="file"  name="email_files">

                      </td>

                    </tr>


                    
                    <tr>

                      <td class="listitem"></td>

                      <td class="listitemnoborder" >

                          <input style="margin-top: 20px;" type="submit" class="btn btn-primary" value="Send Email">

                          <br><br>
                          <small style="color: gray; font-size: 15px;">Please keep patience. It may take 2-3 sec for sending this Email.</small>

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

  <script src="../../css/selectsupport/jquery-3.2.1.min.js" type="text/javascript"></script>
  <script src="../../css/selectsupport/chosen.jquery.js" type="text/javascript"></script>
  <script src="../../css/selectsupport/init.js" type="text/javascript" charset="utf-8"></script>

  </body>

</html>

<script type="text/javascript">

  // jQuery(document).on('click', '.removeuser .icon-remove', function() {
  //     jQuery(this).parent().remove();
  // });



</script>