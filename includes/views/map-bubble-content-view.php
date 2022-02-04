<?php

	$salesmanName = 'No Salesman';

    $customerName = RequestUtil::escapeString(@$customer->getDisplayName(TRUE));



	$target = 'target=\"main\"';

	if(@$basic_link === true) {

		$target = '';

	}



	$selectIcon = "&nbsp;<a title=\"Select User\" href=\"\" rel=\"select-job-salesman\" data-salesman-id=\"{$job->salesman_id}\" class=\"icon-check\" tooltip></a>";

	if(@$select_salesman_icon === false) {

		$selectIcon = '';

	}



	if(!empty($job->salesman_id)) {

        $salesmanName = "{$job->salesman_lname}, {$job->salesman_fname}";

		$salesmanName = "<a title=\"View User\" $target href=\"" . ROOT_DIR . "/users.php?id={$job->salesman_id}\" tooltip>$salesmanName</a>$selectIcon";

	}

?>

<ul><li>Job #: <a title="Go to job" <?=$target?> href="<?=ROOT_DIR?>/jobs.php?id=<?=@$job->job_id?>"><?=@$job->job_number?></a></li><li>Salesman: <?=$salesmanName?><li>Customer: <a <?=$target?> href="<?=ROOT_DIR?>/customers.php?id=<?=@$customer->getMyId()?>" tooltip><?=$customerName?></a><br /></li><li><?=@$customer->getFullAddress()?></li><li>DOB: <?=DateUtil::formatDate(@$job->timestamp)?></li><li><?=@$distance?> miles</li></ul>