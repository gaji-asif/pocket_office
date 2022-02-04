<?php


?>
		<div class="upload-container hover-button-container" style='overflow-y: auto;overflow-x: hidden;' id="upload-<?=@$upload_id?>">
			<div class="preview text-left" style="text-align: left;">
				<a href="<?php echo $links?>" target="_blank" title='<?php echo $links?>'><i style='color:#35aa47;font-size: 20px;' class="icon-folder-open"> </i> Folder</a>
				<a style="float: right;" href="javascript:;" onclick="fndeletefolder('<?php echo $job_id?>','<?php echo $links?>','<?php echo $drp_id;?>');"  title='Delete'><i style='color:#35aa47;font-size: 20px;' class="icon-trash"> </i> Delete</a>
				<hr>
				<?php 
				if(!empty($folders)){
					foreach($folders as $folder){ 
							$drp = explode('/',$folder['link']);
							
						?>
						<a href="<?php echo $folder['link'];?>" target="_blank"><i style='color:#35aa47;font-size: 20px;' class="icon-file"> </i> <?php echo urldecode(substr($drp[count($drp)-1],0,-5));?></a><br>
						
						
					<?php }
				}
				//echo '<pre>';print_r($folders);echo '</pre>';?>
			</div>
		
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