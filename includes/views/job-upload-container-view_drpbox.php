<?php
require '../drpbox/vendor/autoload.php';
//check if file exists
$file_path = UPLOADS_PATH . '/' . @$upload['filename'];
$file_url = UPLOADS_DIR . '/' . @$upload['filename'];
//$file_url = ROOT_DIR . '/get-file.php?type=uploads&id=' . @$upload_id;
if(!file_exists($file_path)){
//    LogUtil::getInstance()->logNotice("Job upload file doesn't exist - '$file_path'");
    return;
}

//get file size and title
$file_size = ceil(filesize($file_path) / 1000);
$file_title = @$upload['filename'];
if(!empty($upload['title'])) {
    $file_title = @$upload['title'];
}

//get preview image
$upload_type = JobUtil::getUploadType(@$upload['filename']);
$preview = IMAGES_DIR . '/icons/' . $upload_type . '_lg.png';
//if($upload_type == 'image') {
//    $preview = IMAGE_RESIZE . '?width=72&height=72&image=' . $file_url;
//}

//get edit and delete permissions
$delete_uploads = false;
if(ModuleUtil::checkAccess('delete_uploads') || (moduleOwnership('delete_uploads') && (JobUtil::isSubscriber(@$myJob->job_id) || @$myJob->salesman_id == $_SESSION['ao_userid'] || @$myJob->user_id == $_SESSION['ao_userid']))) {
    $delete_uploads = true;
}

$edit_uploads = false;
if(ModuleUtil::checkAccess('edit_uploads') || (moduleOwnership('edit_uploads') && (JobUtil::isSubscriber(@$myJob->job_id) || @$myJob->salesman_id == $_SESSION['ao_userid'] || @$myJob->user_id == $_SESSION['ao_userid']))) {
    $edit_uploads = true;
}

$dropbox = new Dropbox\Dropbox('eq2LBxiD9LAAAAAAAAILaQjzqm7kYs0781azSHnI57xaKfK8xm4SXRiI71uIY0us');
/*$sqlj = "select * from jobs where job_id = '".$myJob->job_id."'";
$resultsj = DBUtil::query($sqlj);
$jobs = mysqli_fetch_array($resultsj);

$sqlfolc = "select * from users where user_id = '".$jobs['salesman']."'";
$resultsc = DBUtil::query($sqlfolc);
$customer = mysqli_fetch_array($resultsc);
$folder = $customer['fname'].'_'.$customer['lname'];

$sqlfol = "select * from customers where customer_id = '".$jobs['customer_id']."'";
$results = DBUtil::query($sqlfol);
$user = mysqli_fetch_array($results);
$cuser = $user['lname'].'_'.$user['fname'];*/
$link = $dropbox->files->get_temporary_link($upload['path_display']);
//echo $upload['path_display'];
if(substr_count($upload['path_display'],'.doc') > 0 && substr_count($upload['path_display'],'.png') == 0)
	$preview = IMAGES_DIR . '/icons/word_lg.png';
elseif(substr_count($upload['path_display'],'.xls') > 0)
{
	$preview = IMAGES_DIR . '/icons/word_lg.png';
}
elseif(substr_count($upload['path_display'],'.pdf') > 0)
{
	$preview = IMAGES_DIR . '/icons/pdf_lg.png';
}
else
{
	$preview = $link['link'];
}

/*echo '<pre>';
print_r($link);*/

?>
		<div class="upload-container hover-button-container" id="upload-<?=@$upload_id?>">
			<div class="preview">
				<a href="<?php echo $link['link']?>" target="_blank"><!--<img src="<?php //$preview?>" />--><img src="<?php echo $preview?>" width="185" width="185"/></a>
			</div>
			<!--<ul class="upload_listing">
				<li><a href="<?=$file_url?>" class="boldlink" target="_blank"><?=$file_title?></a></li>
				<li><a href="<?=ROOT_DIR?>/users.php?id=<?=@$upload['user_id']?>" tooltip><?=@$upload['fname']?> <?=@$upload['lname']?></a></li>
				<li><?=DateUtil::formatDate(@$upload['timestamp'])?> @ <?=DateUtil::formatTime(@$upload['timestamp'])?></li>
				<li><?=$file_size?>kb</li>
			</ul>-->
			<div class="btn-group">
<?php
if($edit_uploads)
{
?>
               <!-- <div class="btn" rel="open-modal" data-script="<?=JobUtil::getUploadEditScript(@$upload)?>" title="Edit upload" tooltip>
					<i class="icon-ellipsis-vertical"></i>
				</div>-->
<?php
}
if($delete_uploads)
{
?>
				<!--<div class="btn btn-danger" rel="delete-upload" data-upload-id="<?=@$upload_id?>" title="Remove upload" tooltip>
					<i class="icon-remove"></i>
				</div>-->
<?php
}
?>
			</div>
		</div>