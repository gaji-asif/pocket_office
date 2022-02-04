<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}
$checklist_name = RequestUtil::get('ventcalculator_name');
$id = RequestUtil::get('id');
$action = RequestUtil::get('action');

if(RequestUtil::get('submit-new')) {

    if(!empty($checklist_name)) { 
        $sql = "INSERT INTO tbl_ventcalculator (account_id,ventcalculator_name) VALUES ('{$_SESSION['ao_accountid']}', '$checklist_name')";        
        DBUtil::query($sql);        
        UIUtil::showAlert('New Vent Calculator Added');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(RequestUtil::get('submit-edit')) {
    if(!empty($checklist_name) && !empty($id)) {
        $sql = 'update tbl_ventcalculator set ventcalculator_name = "'.$checklist_name.'" where ventcalculator_id = '.$id;
        DBUtil::query($sql);
        UIUtil::showAlert('Ventcal Culator modified');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(!empty($id) && $action == 'del') {
    
        $sql = "DELETE FROM tbl_ventcalculator
                WHERE ventcalculator_id = '$id' and account_id='{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        UIUtil::showAlert('A Vent Calculator record has been removed.');
    
}
?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="<?=ROOT_DIR?>/system.php">System</a> - <a href="<?=ROOT_DIR?>/add-ons.php">Add ons</a> - Vent Calculator Job</h1>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
                <tr>
                    <td>Vent Calculator Job</td>
                </tr>
            </table>
            <form method="post" action="?" name="add-stages">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="infocontainer">

                <tr>
                    <td>
                        <input type="text" name="ventcalculator_name" size="50" value="">
                        <input name="submit-new" type="submit" value="Add">
                        <input type="button" value="Set Order" class="btn btn-blue pull-right"  data-script="job_ventcalculator_order.php" rel="open-modal" ></input>
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
                    <td>
                     Vent Calculator Job
                      
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
$checklist = DBUtil::queryToArray('select * from tbl_ventcalculator where account_id = '.$_SESSION['ao_accountid'].' order by order_num');
//sort by stage number
/*usort($stages, function($a, $b) {
    return $a['stage_num'] - $b['stage_num'];
});*/

foreach($checklist as $key => $stage) {
    $class = $key % 2 == 0 ? 'even' : 'odd';
    ?>
    <form method="post" action="?">
	<tr class="<?=$class?>">
	    
	    <td>
            <input type="hidden" name="id" value="<?=$stage['ventcalculator_id']?>">
            <input  size="50" type="text" name="ventcalculator_name" value="<?=$stage['ventcalculator_name']?>" id="checklist_name-<?=$stage['ventcalculator_id']?>" />
            <input name="submit-edit" type="submit" value="Edit">
            <a href="<?=ROOT_DIR?>/vent-calculator-job.php?id=<?=$stage['ventcalculator_id']?>" class="btn btn-small btn-success" title="View '<?=$stage['ventcalculator_name']?>' job list" tooltip>            
                View
            </a>
            <a href="<?=ROOT_DIR?>/vent-calculator.php?action=del&id=<?=$stage['ventcalculator_id']?>" class="btn btn-small btn-danger" title="Delete '<?=$stage['ventcalculator_name']?>'" tooltip>            
                <i class="icon-trash"></i>
            </a>
        </td>
	</tr>
	    </form>
		<?php
}
if(!count($checklist)) {
?>
                <tr>
                    <td align="center"><b>No data Found</b></td>
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
