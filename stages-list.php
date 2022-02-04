<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}
$stage = RequestUtil::get('stage');
$id = RequestUtil::get('id');
$action = RequestUtil::get('action');

if(RequestUtil::get('submit-new')) {

    if(!empty($stage)) { 
        $sql = "INSERT INTO stages (account_id,stage) VALUES ('{$_SESSION['ao_accountid']}', '$stage')";        
        DBUtil::query($sql);

        $stages = DBUtil::queryToArray('select max(stage_num) as stage_num from stages');
        $id_num=0;
        if(count($stages)>0)
        {
            foreach($stages as $row) {
                $id_num=$row['stage_num']+1;
            }
        }

        $id=0;
        $stage_id = DBUtil::queryToArray('select max(stage_id) as stage_id from stages');        
        if(count($stage_id)>0)
        {
            foreach($stage_id as $row) {
                $id=$row['stage_id'];
            }
        }

        if(!empty($id)) {
            $sql = 'update stages set stage_num = "'.$id_num.'" where stage_id = '.$id;           
            DBUtil::query($sql);
        }          

        UIUtil::showAlert('New Stage added');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(RequestUtil::get('submit-edit')) {
    if(!empty($stage) && !empty($id)) {
        $sql = 'update stages set stage = "'.$stage.'" where stage_id = '.$id;
        DBUtil::query($sql);
        UIUtil::showAlert('Stage modified');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(!empty($id) && $action == 'del') {
    $job_stage = DBUtil::queryToArray("select id from job_stages where stage_id=$id");
    if(count($job_stage)) {
        UIUtil::showAlert('Jobs currently associated - cannot remove');
    } else {
        $sql = "DELETE FROM stages
                WHERE stage_id = '$id' and account_id='{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        UIUtil::showAlert('Stage Has been Removed');
    }
}
?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Stages</h1>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
                <tr>
                    <td>Add Stages</td>
                </tr>
            </table>
            <form method="post" action="?" name="add-stages">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="infocontainer">
                <tr>
                    <td width="15%">
                        <b>Stage Name:</b>
                    </td>
                    <td>
                        <input type="text" name="stage" size="30" value="">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" align="right">
                        <input name="submit-new" type="submit" value="Add">
                    </td>
                </tr>
            </table>
	    </form>
        </td>
    </tr>
</table>
<br />
<table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
    <tr>
        <td>
            <table align="center" border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
                <tr>
                    <td width="250">
                      Stages
                    </td>
                  <td>
                    <input type="button" value="Change Order" class="btn btn-blue pull-right"  data-script="job_stage_order.php" rel="open-modal" ></input>
                    <!--<input type="button" value="Change Order" tooltip="" class="btn btn-blue"></input>-->
                  </td>
                </tr>
            </table>
        </td>
    </tr>
    <tr>
        <td>
            <table align="center" border="0" cellpadding="2" cellspacing="0" width="100%" class="infocontainernopadding">
<?php
//$stages = StageModel::getStages();
$stages = DBUtil::queryToArray('select * from stages where account_id = '.$_SESSION['ao_accountid'].' order by order_num');
//sort by stage number
/*usort($stages, function($a, $b) {
    return $a['stage_num'] - $b['stage_num'];
});*/

foreach($stages as $key => $stage) {
    $class = $key % 2 == 0 ? 'even' : 'odd';
    ?>
    <form id="stage_<?=$stage['stage_id']?>" method="post" action="?">
	<tr class="<?=$class?>">
	    <td width="20">
            <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/stages-list.php?id=<?=$stage['stage_id']?>&action=del" data-confirm="Are you sure you want to remove stage '<?=$stage['stage']?>'?" title="Delete '<?=$stage['stage']?>'" tooltip>
                <i class="icon-trash"></i>
            </a>
        </td>
	    <td>
                    <input type="text" name="stage" value="<?=$stage['stage']?>" id="stage-name-<?=$stage['stage_id']?>" />
        </td>
        <td align="right">
                <b>Edit Stage:</b>
                <input type="hidden" name="id" value="<?=$stage['stage_id']?>">
                <input name="submit-edit" type="submit" value="Go">
                <input type="button" value="Reset" onclick="$('#stage-name-<?=$stage['stage_id']?>').val('<?=$stage['stage']?>');">
        </td>
	</tr>
	    </form>
		<?php
}
if(!count($stages)) {
?>
                <tr>
                    <td align="center"><b>No Stage Found</b></td>
                </tr>
<?php
}
?>
            </table>
        </td>
    </tr>
</table>
</body>
</html>