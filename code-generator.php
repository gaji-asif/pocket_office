<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}
$checklist_name = RequestUtil::get('codegenerator_name');
$id = RequestUtil::get('id');
$action = RequestUtil::get('action');

if(RequestUtil::get('submit-new')) {

    if(!empty($checklist_name)) { 
        $sql = "INSERT INTO tbl_codegenerator (account_id,codegenerator_name) VALUES ('{$_SESSION['ao_accountid']}', '$checklist_name')";        
        DBUtil::query($sql);        
        UIUtil::showAlert('New Code generator added');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(RequestUtil::get('submit-edit')) {
    if(!empty($checklist_name) && !empty($id)) {
        $sql = 'update tbl_codegenerator set codegenerator_name = "'.$checklist_name.'" where codegenerator_id = '.$id;
        DBUtil::query($sql);
        UIUtil::showAlert('Code generator modified');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(!empty($id) && $action == 'del') {
    
        $sql = "DELETE FROM tbl_codegenerator
                WHERE codegenerator_id = '$id' and account_id='{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        UIUtil::showAlert('A Code generator record has been removed.');
    
}
?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="<?=ROOT_DIR?>/system.php">System</a> - <a href="<?=ROOT_DIR?>/add-ons.php">Add ons</a> - Code generator</h1>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
                <tr>
                    <td>Code generator</td>
                </tr>
            </table>
            <form method="post" action="?" name="add-stages">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="infocontainer">

                <tr>
                    <td>
                        <input type="text" name="codegenerator_name" size="50" value="">
                        <input name="submit-new" type="submit" value="Add">
                        <input type="button" value="Set Order" class="btn btn-blue pull-right"  data-script="job_codegenerator_order.php" rel="open-modal" ></input>
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
                     Code generator
                      
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
$checklist = DBUtil::queryToArray('select * from tbl_codegenerator where account_id = '.$_SESSION['ao_accountid'].' order by order_num');
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
            <input type="hidden" name="id" value="<?=$stage['codegenerator_id']?>">
            <input  size="50" type="text" name="codegenerator_name" value="<?=$stage['codegenerator_name']?>" id="checklist_name-<?=$stage['codegenerator_id']?>" />
            <input name="submit-edit" type="submit" value="Edit">
            <a href="<?=ROOT_DIR?>/code-generator-job.php?id=<?=$stage['codegenerator_id']?>" class="btn btn-small btn-success" title="View '<?=$stage['codegenerator_name']?>' job list" tooltip>            
                View
            </a>
            <a href="<?=ROOT_DIR?>/code-generator.php?action=del&id=<?=$stage['codegenerator_id']?>" class="btn btn-small btn-danger" title="Delete '<?=$stage['codegenerator_name']?>'" tooltip>            
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
