<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('edit_users', TRUE);

$myUser = new User(RequestUtil::get('id'));

if(RequestUtil::get('is_deleted')) {
    RequestUtil::set('is_deleted', 1);
    FormUtil::update('users');
?>

<script>
    Request.makeModal('<?=AJAX_DIR?>/get_userlist.php', 'userscontainer', true, true, true);
</script>
<?php
    die();
}
$curDate = DateUtil::formatMySQLDate();
$date_regex = '/^\d{4}\-\d{2}\-\d{2}$/';
$errors = array();


if(RequestUtil::get('edit-user')) {
    $phone = StrUtil::formatPhoneToSave(RequestUtil::get('phone'));
    $fname = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('fname'));
    $lname = mysqli_real_escape_string(DBUtil::Dbcont(),RequestUtil::get('lname'));
    $email = RequestUtil::get('email');
    $_POST['phone'] = $phone;
    $_POST['is_active'] = RequestUtil::get('is_active') ? 1 : 0;
    $_POST['founder'] = RequestUtil::get('founder') ? 1 : 0;
	$_POST['journal'] = RequestUtil::get('journal') ? 1 : 0;
	$_POST['generalinsbox'] = RequestUtil::get('generalinsbox') ? 1 : 0;
	$_POST['workerinsbox'] = RequestUtil::get('workerinsbox') ? 1 : 0;
	$_POST['calendar_view'] = RequestUtil::get('calendar_view');
	
	$pieces = explode('.',$_FILES["xactimatefileToUpload"]["name"]);
    $file_ext = strtoupper($pieces[sizeof($pieces)-1]);
    $new_filename = mt_rand().time()."_".$pieces[0].".".$pieces[sizeof($pieces)-1];
    $new_path = UPLOADS_PATH. '/xactimate_header/' . $new_filename;
    
    if(empty($fname) || empty($lname)) {
        $errors[] = 'Must enter ALL required fields';
    }
    if($phone && (strlen($phone) != 10 || !ctype_digit($phone))) {
        $errors[] = 'Phone incorrect format';
    }
    if(UserModel::phoneExists($phone) && $phone != $myUser->phone) {
        $errors[] = 'Phone Number in use';
    }
    if(!ValidateUtil::validateEmail($email)) {
        $errors[] = 'Email incorrect format';
    }
    if(UserModel::emailExists($email) && $email != $myUser->email) {
        $errors[] = 'Email in use';
    }
 
     
    
    if(move_uploaded_file($_FILES["xactimatefileToUpload"]["tmp_name"], $new_path))
    {
        $user_id=RequestUtil::get('id');
        $sql = "update users set xactimate_header='".$new_filename."' where 	user_id=".$user_id;
    	DBUtil::query($sql);
    }
    
    if(!count($errors)) {
        FormUtil::update('users');
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_user.php?id=<?=$myUser->getUserID()?>', 'userscontainer', true, true, true);
</script>
<?php
        die();
    }
}
if(RequestUtil::get('edit-password')) {
	$newPassword = RequestUtil::get('password');
	$newPassword2 = RequestUtil::get('password_confirm');

	if($newPassword != $newPassword2) {
		$errors[] = 'Passwords do not match';
	}
	if(strlen($newPassword) < 7) {
		$errors[] = 'New Password too short';
	}

	if(!count($errors)) {
        FormUtil::update('users');
?>
<script>
    Request.makeModal('<?=AJAX_DIR?>/get_user.php?id=<?=$myUser->getUserID()?>', 'userscontainer', true, true, true);
</script>
<?php
        die();
    }
}
$action = RequestUtil::get('action');
if($action == 'xactimate_delete' && ModuleUtil::checkAccess('edit_users')) 
{
    $sql1 = "select xactimate_header from users where user_id=".$myUser->getUserID();
    $res1 = DBUtil::queryToArray($sql1);
    $new_filename ='';
    $sql = "update users set xactimate_header='".$new_filename."' where     user_id=".$myUser->getUserID();
    if(DBUtil::query($sql))
    {
        unlink(UPLOADS_PATH. '/xactimate_header/'.$res1[0]['xactimate_header']);
    }
}

/*
if(RequestUtil::get('insuranceform')) {
	$insuranceform = RequestUtil::getAll();
	//$newPassword2 = RequestUtil::get('password_confirm');

	$pdffileinfo = $insuranceform['iform'];
	//echo '<pre>'; print_r($pdffileinfo); 
	$fname = $pdffileinfo['name'];
	$ftype = $pdffileinfo['type'];
	$fsize = $pdffileinfo['size'];
	$ferror = $pdffileinfo['error'];
	$ftmp = $pdffileinfo['tmp_name'];
	$user_id = $insuranceform['id'];
	$new_path = ROOT_PATH . '/insuranceform/'.$fname ;
	
	if($fname == '' || $fsize <=0 || $ftype != 'application/pdf')
	{
		$errors[] = "Please Upload PDF File";
	}
	
	if($fsize > 10000000)
	{
		$errors[] = "File Size limit should not exceed 10MB.";
	}
	if(!count($errors))
	{
		if(move_uploaded_file($ftmp, $new_path))
    	{
			$sql = "insert into insurancepdfupload(pdfname, user_id, datecreated) values('$fname', $user_id, '$curDate')";
			DBUtil::query($sql);
		}
	?>
	<script>
    Request.makeModal('<?=AJAX_DIR?>/get_user.php?id=<?=$myUser->getUserID()?>', 'userscontainer', true, true, true);
</script>
<?php
        die();
    }
}*/
?>


<?php 
	if(RequestUtil::get('invoice_header')) 
	{	
		$invoiceform = RequestUtil::getAll();

		$fileinfo = $invoiceform['company_logo'];
		$fname = time().$fileinfo['name'];
		$ftype = $fileinfo['type'];
		$fsize = $fileinfo['size'];
		$ferror = $fileinfo['error'];
		$ftmp = $fileinfo['tmp_name'];
		$user_id = $invoiceform['id'];
		$old_logo = $invoiceform['old_logo'];
		$new_path = ROOT_PATH . '/invoice_logo/'.$fname;
		$file_name = '';

		if(move_uploaded_file($ftmp, $new_path))
    	{
			$file_name = $fname;
		    if(!empty($old_logo) && file_exists(ROOT_PATH . '/invoice_logo/'.$old_logo))
				unlink(ROOT_PATH . '/invoice_logo/'.$old_logo);
		}
		if(empty($file_name))
		{
			$file_name = $old_logo;
		}
		$company_name = mysqli_real_escape_string(DBUtil::Dbcont(),$invoiceform['company_name']);
		$company_address = mysqli_real_escape_string(DBUtil::Dbcont(),$invoiceform['company_address']);
		
		$suite_number = $invoiceform['suite_number'];
		$invoice_city = $invoiceform['invoice_city'];
		$invoice_state = $invoiceform['invoice_state'];
		$invoice_zip = $invoiceform['invoice_zip'];
		$invoice_email = $invoiceform['invoice_email'];

		$header_id = $invoiceform['header_id'];
		if(!$header_id)
		{
			$sql = "insert into user_invoice_header(user_id, address,invoice_logo,company_name,suite_number,invoice_city,invoice_state,invoice_zip,invoice_email) values($user_id,'$company_address','$file_name','$company_name','$suite_number','$invoice_city','$invoice_state','$invoice_zip','$invoice_email')";
			DBUtil::query($sql);
		}
		else
		{
			$sql = "update user_invoice_header SET address='$company_address',invoice_logo='$file_name',company_name = '$company_name',suite_number = '$suite_number',invoice_city = '$invoice_city',invoice_state = '$invoice_state',invoice_zip = '$invoice_zip',invoice_email = '$invoice_email' where header_id=$header_id";
			DBUtil::query($sql);
		}

	}
?>



<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Edit <?=$myUser->getDisplayName()?></td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal no-close" onclick="Request.makeModal('<?=AJAX_DIR?>/get_user.php?id=<?=$myUser->getUserID()?>', 'userscontainer', true, true, true)"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors)?>
<div class="margins">
<form method="post" action="?id=<?=$myUser->getMyId()?>" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            User Information
        </td>
    </tr>
    <tr>
        <td class="infocontainernopadding">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="25%" class="listitemnoborder"><b>First Name:</b>&nbsp;<span class="red">*</span></td>
                    <td class="listrownoborder"><input type="text" name="fname" value='<?=$myUser->fname?>'></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Last Name:</b>&nbsp;<span class="red">*</span></td>
                    <td class="listrow"><input type="text" name="lname" value='<?=$myUser->lname?>'></td>
                </tr>
                <tr>
                    <td class="listitem"><b>DBA:</b>&nbsp;<span class="red">*</span></td>
                    <td class="listrow"><input type="text" name="dba" value='<?=$myUser->dba?>'></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Email:</b></td>
                    <td class="listrow"><input type="text" name="email" value='<?=$myUser->email?>'></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Phone:</b></td>
                    <td class="listrow"><input type="text" class="masked-phone" name="phone" value='<?=$myUser->phone?>'></td>
                </tr>
                <tr>
                    <td class="listitem"><b>SMS Carrier:</b></td>
                    <td class="listrow">
                        <select name="sms_carrier">
                            <option value="">No SMS</option>
<?php
$carriers = getAllSmsCarriers();
foreach($carriers as $carrier) {
?>
                            <option value="<?=$carrier['sms_id']?>" <?=$myUser->sms_carrier == $carrier['sms_id'] ? 'selected' : ''?>><?=$carrier['carrier']?></option>
<?php
}
?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="listitem"><b>Access Level:</b></td>
                    <td class="listrow">
                        <select name="level">
<?php
$userLevels = UserModel::getAllLevels();
foreach($userLevels as $userLevel) {
?>
                            <option value="<?=$userLevel['level_id']?>" <?=$myUser->getLevel() == $userLevel['level_id'] ? 'selected' : ''?>><?=$userLevel['level']?></option>
<?php
}
?>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="listitem"><b>Office</b></td>
                    <td class="listrow">
                        <select name="office_id">
                            <option value=''>Default</option>
<?php
$offices = AccountModel::getAllOffices();
foreach($offices as $office) {
?>
                            <option value="<?=$office['office_id']?>" <?=$myUser->office_id == $office['office_id'] ? 'selected' : ''?>><?=$office['title']?></option>
<?php
}
?>
                        </select>
                    </td>
                </tr>
				
				<tr>
                    <td class="listitem"><b>For Journal</b></td>
                    <td class="listrow">
                        <input type="checkbox" value="1" name="journal" <?=$myUser->getJournal() == 1 ? 'checked' : ''?>>
                    </td>
                </tr>
				
                <tr>
                    <td class="listitem"><b>Founder</b></td>
                    <td class="listrow">
                        <input type="checkbox" value="1" name="founder" <?=$myUser->getFounder() == 1 ? 'checked' : ''?>>
                    </td>
                </tr>
                <tr>
                    <td class="listitem"><b>Active</b></td>
                    <td class="listrow">
                        <input type="checkbox" value="1" name="is_active" <?=$myUser->getIsActive() == 1 ? 'checked' : ''?>>
                    </td>
                </tr>
               <?php /*<tr>
                    <td class="listitem"><b>General liability insurance expires Not Needed</b></td>
                    <td class="listrow">
                        <input type="checkbox" value="1" name="generalinsbox" <?=$myUser->getGeneralinsbox() == 1 ? 'checked' : ''?>>
                    </td>
                </tr>
                <tr>
                    <td class="listitem"><b>General liability insurance expires on</b></td>
                    <td class="listrow"><input type="text" class="pikaday" name="generalins" value='<?php echo ($myUser->getGeneralins()!='0000-00-00 00:00:00') ? $myUser->getGeneralins() : ''; ?>' /></td>
                </tr>
                <tr>
                    <td class="listitem"><b>Workers Compensations insurance expires Not Needed</b></td>
                    <td class="listrow">
                        <input type="checkbox" value="1" name="workerinsbox" <?=$myUser->getWorkerinsbox() == 1 ? 'checked' : ''?>>
                    </td>
                </tr>
                <tr>
                    <td class="listitem"><b>Workers Compensations insurance expires on</b></td>
                    <td class="listrow"><input type="text" class="pikaday" name="workerins" value='<?php echo ($myUser->getWorkerins()!='0000-00-00 00:00:00') ? $myUser->getWorkerins() : ''; ?>' /></td>
                </tr>*/?>
                <tr valign="top">
                    <td class="listitem"><b>Notes:</b></td>
                    <td class="listrow"><textarea rows=5 name="notes"><?=$myUser->notes?></textarea></td>
                </tr>
                <tr valign="top">
                    <td class="listitem"><b>Xactimate header :</b></td>
                    <td class="listrow"><input type="file" name="xactimatefileToUpload" id="xactimatefileToUpload"></td>
                </tr>
                <?php
                  $sql1 = "select xactimate_header,calendar_view from users where user_id=".$myUser->getUserID();
                  $res1 = DBUtil::query($sql1);
                  $filename="";
                  if(mysqli_num_rows($res1)!=0)
                  {
                      list($xactimate_header,$calendar_view)=mysqli_fetch_row($res1);
                      if(!empty($xactimate_header))
                      {
                          $filenamearray=explode("_",$xactimate_header);
                  
                ?>
                <tr>
                                <td class="listitem"><b>Uploaded Header :</b></td>
                                <td class="listrow"><?=$filenamearray[1]?>&nbsp;&nbsp;&nbsp;  <a download='<?=$xactimate_header?>' href='<?=UPLOADS_DIR.'/xactimate_header/'.$xactimate_header?>'><i class="icon-download"></i></a>
                                &nbsp;&nbsp;&nbsp;
                                <a href='?id=<?=$myUser->getUserID()?>&action=xactimate_delete' class='boldlink'><i class="icon-trash" style="color:red" > </i></a>

                                </td>
                              </tr>
                              <?php
                        }
                  }
                 ?>
                 
                 <tr>
                    <td class="listitem"><b>Calendar View:</b></td>
                    <td class="listrow">
                        <select name="calendar_view">
                            <option value="w" <?=$calendar_view == 'w' ? 'selected' : ''?>>Week View</option>
                            <option value="m" <?=$calendar_view == 'm' ? 'selected' : ''?>>Month View</option>
                        </select>
                    </td>
                </tr>
                 
                <tr>
                    <td align="right" class="listrow" colspan="2">
                        <input type="submit" name="edit-user" value="Save">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>

<?php 
$user_id = $myUser->getUserID();
$sql = "select * from user_invoice_header where user_id = $user_id";
$invoice_header = DBUtil::queryToArray($sql);
$invoice = array();
if(!empty($invoice_header))
{
	$invoice=$invoice_header[0];
}
$logo = (!empty($invoice['invoice_logo']))?'/invoice_logo/'.$invoice['invoice_logo']:'';
?>
<br />


 
<form method="post" action="?id=<?=$myUser->getUserID()?>" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Invoice Header
        </td>
    </tr>
    <tr>
        <td class="infocontainernopadding">
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Company Name:</b></td>
                    <td class="listrownoborder">
                    	<input type="hidden" name="header_id" value="<?=(!empty($invoice['header_id']))?$invoice['header_id']:''?>" />
                       <input type="text" name="company_name" value="<?=(!empty($invoice['company_name']))?$invoice['company_name']:''?>" />
                    </td>
                </tr>
                
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Address:</b></td>
                    <td class="listrownoborder">
                       <textarea style="width: 50%" class="company_address" name="company_address"><?=(!empty($invoice['address']))?$invoice['address']:''?></textarea>
                       
                    </td>
                </tr>
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Suite Number:</b></td>
                    <td class="listrownoborder">
                       <input type="text" name="suite_number" value="<?=(!empty($invoice['suite_number']))?$invoice['suite_number']:''?>" />
                    </td>
                </tr>
                <tr>
                    <td width="25%" class="listitemnoborder"><b>City:</b></td>
                    <td class="listrownoborder">
                       <input type="text" name="invoice_city" value="<?=(!empty($invoice['invoice_city']))?$invoice['invoice_city']:''?>" />
                    </td>
                </tr>
                <tr>
                    <td width="25%" class="listitemnoborder"><b>State:</b></td>
                    <td class="listrownoborder">
                       <input type="text" name="invoice_state" value="<?=(!empty($invoice['invoice_state']))?$invoice['invoice_state']:''?>" />
                    </td>
                </tr>
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Zip:</b></td>
                    <td class="listrownoborder">
                       <input type="text" name="invoice_zip" value="<?=(!empty($invoice['invoice_zip']))?$invoice['invoice_zip']:''?>" />
                    </td>
                </tr>
                
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Email:</b></td>
                    <td class="listrownoborder">
                       <input type="text" name="invoice_email" value="<?=(!empty($invoice['invoice_email']))?$invoice['invoice_email']:''?>" />
                    </td>
                </tr>
                
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Company Logo:</b></td>
                    <td class="listrownoborder">
                       <input type="file" name="company_logo" />
                       <input type="hidden" name="old_logo" value="<?=(!empty($invoice['invoice_logo']))?$invoice['invoice_logo']:''?>" />
                       <?php if($logo){?>
                       	<img  width="50" src="<?= ROOT_DIR.$logo;?>">
                       	<a title="Download Company Logo" href="<?=ROOT_DIR.$logo;?>" download="" style="margin-left:60px;">
                            <span class="glyphicon glyphicon-download-alt"></span>
                        </a>
                       	<?php }?>
                    </td>
                </tr>
				
				<tr>
                    <td align="right" class="listrow" colspan="2">
                        <input type="submit" name="invoice_header" value="Save">
                    </td>
                </tr>
				
            </table>
            
        </td>
    </tr>
</table>
</form>

<?php
if(ModuleUtil::checkAccess('edit_user_passwords')) {
?>
<br />
<form method="post" action="?id=<?=$myUser->getUserID()?>">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Edit Password
        </td>
    </tr>
    <tr>
        <td class="infocontainernopadding">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td class="listitem">
                        <b>New Password:</b>
                    </td>
                    <td class="listrow">
                        <input type="password" name="password"><br />
                        <span class="smallnote">New Password must be at least 7 characters.</span>
                    </td>
                </tr>
                <tr>
                    <td class="listitem">
                        <b>Confirm New Password:</b>
                    </td>
                    <td class="listrow"><input type="password" name="password_confirm"></td>
                </tr>
                <tr>
                    <td align="right" class="listrow" colspan="2">
                        <input type="submit" name="edit-password" value="Save">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
	
<br />
<?php /*<form method="post" action="?id=<?=$myUser->getUserID()?>" enctype="multipart/form-data">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Upload Insurance Form
        </td>
    </tr>
    <tr>
        <td class="infocontainernopadding">
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Upload Insurance Form:</b></td>
                    <td class="listrownoborder">
                       <input type="file" name="iform" value="" />
                    </td>
                </tr>
				
				<tr>
                    <td align="right" class="listrow" colspan="2">
                        <input type="submit" name="insuranceform" value="Save">
                    </td>
                </tr>
				
            </table>
            
        </td>
    </tr>
</table>
</form>*/?>
	
<?php
}
?>
	
<br />
<?php /*
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Insurance Form
        </td>
    </tr>
    <tr>
        <td class="infocontainernopadding">
			<table border="0" width="100%" cellpadding="0" cellspacing="0">
                
				<tr>
                    
                    <td class="listrownoborder">
						
                       <?php
							$pdfname = UserUtil::getinsurance(RequestUtil::get('id')); 
							//print_r(count($pdfname));
							if(count($pdfname) > 0) {
								foreach($pdfname as $pdf) {
									?>
								<div class="upload-container">
									<div class="preview">
									<a target="_blank" href="<?php echo ROOT_DIR . '/insuranceform/'.$pdf['pdfname'] ?> ">
										<img src="<?php echo ROOT_DIR . '/images/icons/pdf_lg.png'?>" alt="<?php echo $pdf['pdfname']; ?>">
									</a>
									</div>
									<ul>
										<li>
											<a target="_blank" href="<?php echo ROOT_DIR . '/insuranceform/'.$pdf['pdfname'] ?> ">
												<?php echo $pdf['pdfname']; ?>
											</a>
											
										</li>
										<li>
											<?php echo date('M d, Y @ h:i A',strtotime($pdf['datecreated'])); ?>
										</li>
									</ul>
								</div>
								<?php	
								}
							} else {
								echo '<div align="center">No Insurance Form uploded</div>';
							}
						?>
                    </td>
                </tr>
				
				
				
            </table>
            
        </td>
    </tr>
</table> */?>


<br />
<form method="post" action="?id=<?=$myUser->getUserID()?>" onsubmit="return confirm('Are you sure?')">
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Delete User
        </td>
    </tr>
    <tr>
        <td class="infocontainernopadding">
            <table border="0" width="100%" cellpadding="0" cellspacing="0">
                <tr>
                    <td width="25%" class="listitemnoborder"><b>Delete User:</b></td>
                    <td class="listrownoborder">
                        <input type="submit" name="is_deleted" value="Delete">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>

<br />
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            User Permissions
        </td>
    </tr>
    <tr>
        <td class='infocontainer' id='userpermissionscontainer'>
        </td>
    </tr>
</table>

<br />    
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Stage Advancement Access
        </td>
    </tr>
    <tr>
        <td class='infocontainer' id='user-stage-advancement-container'></td>
    </tr>
</table>

<br />
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Stage Notifications
        </td>
    </tr>
    <tr>
        <td class="infocontainer" id="stagenotificationscontainer"></td>
    </tr>
</table>

<br />    
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Checklist Job Access
        </td>
    </tr>
    <tr>
        <td class='infocontainer' id='user-checklist-job-container'></td>
    </tr>
</table>


<br />    
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Code Generator Job Access
        </td>
    </tr>
    <tr>
        <td class='infocontainer' id='user-codegenerator-job-container'></td>
    </tr>
</table>
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            Vent Calculator Job Access
        </td>
    </tr>
    <tr>
        <td class='infocontainer' id='user-ventcalculator-job-container'></td>
    </tr>
</table>

<br />    
<table border="0" cellspacing="0" cellpadding="0" width="100%">
    <tr>
        <td class="smcontainertitle">
            To Do List Job Access
        </td>
    </tr>
    <tr>
        <td class='infocontainer' id='user-todolist-job-container'></td>
    </tr>
</table>


</div>
<script>
    Request.make('<?=AJAX_DIR?>/get_userpermissions.php?id=<?=$myUser->getUserID()?>', 'userpermissionscontainer', false, true);
    Request.make('<?=AJAX_DIR?>/get_userstageadvancement.php?id=<?=$myUser->getUserID()?>', 'user-stage-advancement-container', true, true);
    Request.make('<?=AJAX_DIR?>/edit_stagenotifications.php?id=<?=$myUser->getUserID()?>', 'stagenotificationscontainer', true, true);
    Request.make('<?=AJAX_DIR?>/get_user_chcklistjob.php?id=<?=$myUser->getUserID()?>', 'user-checklist-job-container', true, true);
    Request.make('<?=AJAX_DIR?>/get_user_codegeneratorjob.php?id=<?=$myUser->getUserID()?>', 'user-codegenerator-job-container', true, true);
    Request.make('<?=AJAX_DIR?>/get_user_ventcalculatorjob.php?id=<?=$myUser->getUserID()?>', 'user-ventcalculator-job-container', true, true);
    Request.make('<?=AJAX_DIR?>/get_user_todolistjob.php?id=<?=$myUser->getUserID()?>', 'user-todolist-job-container', true, true);
</script>
</body>
</html>