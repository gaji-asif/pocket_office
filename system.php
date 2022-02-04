<?php
include 'includes/common_lib.php';

UserModel::isAuthenticated();

if($_SESSION['ao_founder']!=1)

  die("Insufficient Rights");



/****** UPLOAD LOGO ******/

if(isset($_FILES['logo_upload']))
{
	//create new file destination
	$new_file_name = md5(time()) . '-' . $_FILES['logo_upload']['name'];
	$new_file_destination = ROOT_PATH.'/logos/' . $new_file_name;
	//validation
	$logo_result_message = array();
	if(!is_uploaded_file($_FILES['logo_upload']['tmp_name']))
	{
		$logo_result_message[] = 'Upload failed';
	}

	if(getimagesize($_FILES['logo_upload']['tmp_name']) === false)
	{
		$logo_result_message[] = 'Upload must be an image';
	}

	if(!move_uploaded_file($_FILES['logo_upload']['tmp_name'], $new_file_destination))
	{
		$logo_result_message[] = 'File write process failed';
	}
  //echo "<pre>";print_r($logo_result_message);exit;
	//save  
	if(empty($logo_result_message))
	{
		//update database
		$sql = "update accounts set logo = '$new_file_name' where account_id = '{$_SESSION['ao_accountid']}' limit 1";
		DBUtil::query($sql) or die(mysqli_error());
		//success message
		UIUtil::showAlert('Logo successfully modified');
		//unlink old logo
		@unlink(ROOT_PATH.'/logos/'. $_SESSION['ao_logo']);
		//set logo session variable
		$_SESSION['ao_logo'] = $new_file_name;
	}
	else
	{
		UIUtil::showAlert('Error(s) modifying logo: ' . implode(', ', $logo_result_message));
	}
}
/*************************/
?>

<?=ViewUtil::loadView('doc-head')?>

    <h1 class="page-title"><i class="icon-cogs"></i>System</h1>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center'>
              <td>Company Profile</td>
                <td align="right">
                    <i class="icon-pencil edit grey" rel="open-modal" data-script="edit_company.php" title="Edit Company Profile" tooltip></i>
                </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td id='companycontainer'></td>
      </tr>
      <tr>
        <td colspan=2>
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/get_company.php', 'companycontainer', true, true);
          </script>
        </td>
      </tr>

      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center'>
              <td>Configuration</td>
            </tr>
          </table>
        </td>
      </tr>

      <tr>
        <td id='configcontainer'></td>
      </tr>
      <tr>
        <td colspan=2>
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/get_config.php', 'configcontainer', true, true);
          </script>
        </td>
      </tr>

      <tr><td colspan=2>&nbsp;</td></tr>
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center'>
              <td>Security Levels</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td id='levelscontainer'></td>
      </tr>
      <tr>
        <td colspan=2>
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/get_levelslist.php', 'levelscontainer', true, true);
          </script>
        </td>
      </tr>
      <tr><td>&nbsp;</td></tr>
    </table>
  </body>
</html>

