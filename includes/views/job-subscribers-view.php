<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }
?>
<tr class="job-tab-content subscribers" <?=@$show_content_style?>>
	<td colspan=11>
		<div class="pillbox clearfix">
<?php
$modify_subscribers = false;
if(ModuleUtil::checkJobModuleAccess('modify_job_subscribers', $myJob)) {
	$modify_subscribers = true;
?>
			<a href="" rel="open-modal" data-script="add_subscriber.php?id=<?php echo $myJob->job_id; ?>" class="btn btn-success" title="Add subscriber" tooltip><i class="icon-plus"></i></a>
<?php
}
if(!empty($myJob->salesman_id)) {
?>
            <a href="<?=ROOT_DIR?>/users.php?id=<?=$myJob->salesman_id?>" class="btn btn-blue" tooltip><?=$myJob->salesman_fname?> <?=$myJob->salesman_lname?></a>
<?php
}

$subscribers = $myJob->getSubscribers();
foreach($subscribers as $subscriber) {
?>

			<div class="btn-group" id="subscriber-<?=$subscriber['subscriber_id']?>">
				<a href="<?=ROOT_DIR?>/users.php?id=<?=$subscriber['user_id']?>" class="btn btn-blue" tooltip>
                    <?=$subscriber['fname']?> <?=$subscriber['lname']?>
                </a>
<?php
    if($modify_subscribers) {
?>
				<a href="" rel="delete-subscriber" data-subscriber-id="<?=$subscriber['subscriber_id']?>" class="btn btn-blue" title="Remove subscriber" tooltip>
                    <i class="icon-remove"></i>
                </a>
<?php
    }
?>
			</div>
<?php
}
?>

<!--			<a class="btn">Default Button <i class="icon-remove"></i></a>
			<div class="btn-group">
				<a class="btn">Default Button <i class="icon-remove"></i></a>
				<a class="btn btn-primary"><i class="icon-plus"></i> Primary Button</a>
				<a class="btn btn-warning">Warning Button <i class="icon-remove"></i></a>
				<a class="btn btn-danger">Danger Button <i class="icon-remove"></i></a>
				<a class="btn disabled">Disabled Button <i class="icon-remove"></i></a>
			</div>-->
		</div>
	</td>
</tr>