<?php
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

?>
<style type="text/css">
		.fancybox-image {
    transform-origin: top left; /* IE 10+, Firefox, etc. */
    -webkit-transform-origin: top left; /* Chrome */
    -ms-transform-origin: top left; /* IE 9 */
}
#rotate90 {
    transform: rotate(90deg) translateY(-100%);
    -webkit-transform: rotate(90deg) translateY(-100%);
    -ms-transform: rotate(90deg) translateY(-100%);
}
#rotate180 {
    transform: rotate(180deg) translate(-100%,-100%);
    -webkit-transform: rotate(180deg) translate(-100%,-100%);
    -ms-transform: rotate(180deg) translateX(-100%,-100%);
}
#rotate270 {
    transform: rotate(270deg) translateX(-100%);
    -webkit-transform: rotate(270deg) translateX(-100%);
    -ms-transform: rotate(270deg) translateX(-100%);
}
	</style>
		<div class="upload-container hover-button-container" id="upload-<?=@$upload_id?>">
		    <input class="dowimg" type="checkbox"  value="<?=$file_url?>">
			<div class="preview">
				<?php if( $upload_type!="image"){ ?>
					<a href="<?=$file_url?>" target="_blank"><img src="<?=$preview?>" /></a>
					<?php }else{ ?>
					<a class="fancy" data-fancybox="gallery" href="<?=$file_url?>" ><img height="72" width="72" src="<?=$file_url?>" /></a>
						
                        <?php }?>
			</div>
			<ul class="upload_listing">
				<li><a href="<?=$file_url?>" class="boldlink" target="_blank"><?=$file_title?></a></li>
				<li><a href="<?=ROOT_DIR?>/users.php?id=<?=@$upload['user_id']?>" tooltip><?=@$upload['fname']?> <?=@$upload['lname']?></a></li>
				<li><?=DateUtil::formatDate(@$upload['timestamp'])?> @ <?=DateUtil::formatTime(@$upload['timestamp'])?></li>
				<li><?=$file_size?>kb</li>
				<li><a href="<?=$file_url?>" download><span class="glyphicon glyphicon-download-alt"></span></a></li>
			</ul>
			<div class="btn-group">
<?php
if($edit_uploads)
{
?>
                <div class="btn" rel="open-modal" data-script="<?=JobUtil::getUploadEditScript(@$upload)?>" title="Edit upload" tooltip>
					<i class="icon-ellipsis-vertical"></i>
				</div>
<?php
}
if($delete_uploads)
{
?>
				<div class="btn btn-danger" rel="delete-upload" data-upload-id="<?=@$upload_id?>" title="Remove upload" tooltip>
					<i class="icon-remove"></i>
				</div>
<?php
}
?>
			</div>
		</div>
		 
