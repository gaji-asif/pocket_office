<?php 

include 'includes/common_lib.php';

$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));

pageSecure($this_page->source);



if(@$_GET['action'] == 'toggle_inactive_users') {

    if($_SESSION['ao_show_inactive_users_in_jobs_list'] === true) {

        $_SESSION['ao_show_inactive_users_in_jobs_list'] = false;

    }

    else {

        $_SESSION['ao_show_inactive_users_in_jobs_list'] = true;

    }

}

echo ViewUtil::loadView('doc-head');

echo $this_page->getHeader(TRUE);

?>



<?php
if(ModuleUtil::checkAccess('add_job')) {

?>

<div class="btn-group pull-right page-menu">

    <div rel="open-modal" data-script="add_job.php" class="btn btn-success" title="Add job" tooltip>

        <i class="icon-plus"></i>

    </div>

</div>

<?php

}



if(ModuleUtil::checkAccess('view_jobs')) {

    echo ViewUtil::loadView('job-filters');

?>

<table border=0 cellspacing=0 cellpadding=0 class="main-view-table">

    <?=ViewUtil::loadView('job-header')?>

    <tr>

        <td id="jobscontainer"></td>

    </tr>

    <tr>

        <td colspan=2>

            <div id="btm_spacer" style="display:none;">

                <table border=0>

                    <tr height=200>

                    <td>&nbsp;</td>

                    </tr>

                </table>

            </div>

        </td>

    </tr>

</table>



<script type="text/javascript">

    $(document).ready(function(){
        
       <?php
       	session_start();
		if(!empty($_SESSION['my_job_id'])) {
       ?>
      Request.make("<?=AJAX_DIR?>/get_job.php?id=<?=$_SESSION['my_job_id']?>", 'jobscontainer', true, true);
       <?php unset($_SESSION['my_job_id']); } else { ?>

<?php

if(!empty($_GET['id'])) {

?>

		Request.make('<?=AJAX_DIR?>/get_notes.php?type=jobs&id=<?php echo $_GET['id']; ?>', 'notes', false, true);

<?php

    if(!empty($_GET['tab'])) {

?>



		Request.make('<?=AJAX_DIR?>/get_job.php?id=<?php echo $_GET['id']; ?>&tab=<?=$_GET['tab']?>', 'jobscontainer', true, true);

<?php

    } else {

?>

		Request.make('<?=AJAX_DIR?>/get_job.php?id=<?php echo $_GET['id']; ?>', 'jobscontainer', true, true);

<?php

    }

} else {

?>

		Request.make('<?=AJAX_DIR?>/get_joblist.php', 'jobscontainer', true, true);

<?php

} }

?>

		});

	</script>

<?php

} else {

    echo ModuleUtil::showInsufficientRightsAlert('view_jobs', TRUE);

}

?>

  </body>

</html>

