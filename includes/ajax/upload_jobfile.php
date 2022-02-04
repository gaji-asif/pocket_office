<?php
set_time_limit(120);

include '../common_lib.php';
if(!ModuleUtil::checkAccess('upload_job_file'))
	die('Insufficient Rights');

echo ViewUtil::loadView('doc-head');

$myJob = new Job(RequestUtil::get('id'));

if(moduleOwnership('upload_job_file') && (!JobUtil::isSubscriber($myJob->job_id) && $myJob->salesman_id != $_SESSION['ao_userid'] && $myJob->user_id != $_SESSION['ao_userid']))
	die("Insufficient Rights");

if(isset($_POST["submit"]))
{
	set_time_limit(300);
	$titles = $_POST['title'];

	foreach ($_FILES['file']['error'] as $key => $error)
	{
		if($error == 0) {
			$pieces = explode('.', $_FILES["file"]["name"][$key]);
			$new_filename = md5(mt_rand() . mktime()) . "." . end($pieces);
			$new_path = ROOT_PATH . '/uploads/' . $new_filename;

			move_uploaded_file($_FILES["file"]["tmp_name"][$key], $new_path) or die("Problems with upload");
			$sql = "INSERT INTO uploads (job_id, user_id, account_id, filename, title, timestamp)
                    VALUES ('{$myJob->job_id}', '{$_SESSION['ao_userid']}', '{$_SESSION['ao_accountid']}', '$new_filename', '" . mysqli_real_escape_string(DBUtil::Dbcont(),$titles[$key]) . "', now())";
            echo $sql;die;        
            DBUtil::query($sql);
		}
	}
	JobModel::saveEvent($myJob->job_id, "Job File(s) Uploaded");
	NotifyUtil::notifySubscribersFromTemplate('upload_job_file', $_SESSION['ao_userid'], array('job_id' => $myJob->job_id));
	?>

	<script>
		Request.makeModal('<?=AJAX_DIR ?>/get_job.php?tab=uploads&id=<?php echo $myJob->job_id; ?>', 'jobscontainer', true, true, true);
	</script>
	<?php
	die();
}
?>
<script src="<?=ROOT_DIR ?>/uploadify/swfobject.js"></script>
<script src="<?=ROOT_DIR ?>/uploadify/jquery.uploadify.v2.1.4.min.js"></script>
<script>
	$(document).ready(function() {
		$('#file_upload').uploadify({
			'uploader': '<?=ROOT_DIR ?>/uploadify/uploadify.swf',
			'script': '<?=ROOT_DIR ?>/includes/php/manage_file_uploads.php?action=upload_product_image_file',
			'cancelImg': '<?=ROOT_DIR ?>/uploadify/cancel.png',
			'folder': '<?=ROOT_DIR ?>/uploads',
			'auto': true,
			'scriptData': {'session_id': '<?php echo session_id(); ?>', 'id': '<?php echo $myJob->job_id; ?>'},
			'multi': true,
			'onComplete': function(event, ID, fileObj, response, data) {
				//alert(response);
				response = $.parseJSON(response);
				if($('#fileProcessingResultsNoResults') != null)
				{
					$('#fileProcessingResultsNoResults').remove();
				}

				messageColor = 'green';
				if(response.status != 'success')
				{
					messageColor = "red";
				}

				$('<li style="color: ' + messageColor + '">' + response.message + '</li>').appendTo('#fileProcessingResults');
			}

		});
	});
</script>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
	<tr>
        <td>
			<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
				<tr valign="center">
					<td>Upload Files</td>
					<td align="right">
						<i class="icon-remove grey btn-close-modal no-close" onclick="Request.makeModal('<?=AJAX_DIR ?>/get_job.php?tab=uploads&id=<?=$myJob->job_id ?>', 'jobscontainer', true, true, true);"></i>
					</td>
				</tr>
			</table>
        </td>
	</tr>
	<tr>
        <td class="infocontainernopadding"><div style="margin: 5px 30px;">
			<form id="form2" name="form2" method="post" action="">
				<p><strong>Mass upload files</strong></p>
				<p>    <input id="file_upload" name="file_upload" type="file" />
				</p>
				<p><strong>Upload results</strong></p>
				<ul id="fileProcessingResults"><li id="fileProcessingResultsNoResults">No file has been processed yet</li></ul>
			</form>
			<div>-OR-</div> </div><hr/>
			<form enctype='multipart/form-data' action='?id=<?php echo $_GET['id']; ?>' method="post">
			<table width="100%" border="0" cellpadding="0" cellspacing="0">
				<tr>
					<td width="25%" class="listitemnoborder">
						<b>File:</b>
					</td>
					<td class="listrownoborder">
						<input type='file' name='file[]'>
					</td>
				</tr>
				<tr>
					<td class="listitemnoborder">
						<b>Title:</b>
					</td>
					<td class="listrownoborder">
						<input type="text" name='title[]'>
					</td>
				</tr>
				<tr>
					<td class="listitem">
						<b>File:</b>
					</td>
					<td class="listrow">
						<input type='file' name='file[]'>
					</td>
				</tr>
				<tr>
					<td class="listitemnoborder">
						<b>Title:</b>
					</td>
					<td class="listrownoborder">
						<input type="text" name='title[]'>
					</td>
				</tr>
				<tr>
					<td class="listitem">
						<b>File:</b>
					</td>
					<td class="listrow">
						<input type='file' name='file[]'>
					</td>
				</tr>
				<tr>
					<td class="listitemnoborder">
						<b>Title:</b>
					</td>
					<td class="listrownoborder">
						<input type="text" name='title[]'>
					</td>
				</tr>
				<tr>
					<td class="listitem">
						<b>File:</b>
					</td>
					<td class="listrow">
						<input type='file' name='file[]'>
					</td>
				</tr>
				<tr>
					<td class="listitemnoborder">
						<b>Title:</b>
					</td>
					<td class="listrownoborder">
						<input type="text" name='title[]'>
					</td>
				</tr>
				<tr>
					<td class="listitem">
						<b>File:</b>
					</td>
					<td class="listrow">
						<input type='file' name='file[]'>
					</td>
				</tr>
				<tr>
					<td class="listitemnoborder">
						<b>Title:</b>
					</td>
					<td class="listrownoborder">
						<input type="text" name='title[]'>
					</td>
				</tr>
				<tr>
					<td align="right" colspan=2 class="listrow">
						<input type="submit" name="submit" value='Upload'>
					</td>
				</tr>
			</table>
			</form>
        </td>
	</tr>
</table>
</body>
</html>