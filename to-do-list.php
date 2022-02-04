<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}
$todolist_name = RequestUtil::get('todolist_name');
$id = RequestUtil::get('id');
$action = RequestUtil::get('action');

if(RequestUtil::get('submit-new')) {

    if(!empty($todolist_name)) { 
        $sql = "INSERT INTO tbl_todolist (account_id,todolist_name) VALUES ('{$_SESSION['ao_accountid']}', '$todolist_name')";        
        DBUtil::query($sql);        
        UIUtil::showAlert('New Code To-Do-List added');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(RequestUtil::get('submit-edit')) {
    if(!empty($todolist_name) && !empty($id)) {
        $sql = 'update tbl_todolist set todolist_name = "'.$todolist_name.'" where todolist_id = '.$id;
        DBUtil::query($sql);
        UIUtil::showAlert('Code To-Do-List modified');
    } else {
        UIUtil::showAlert('Required information missing');
    }
}

if(!empty($id) && $action == 'del') {
    
        $sql = "DELETE FROM tbl_todolist
                WHERE todolist_id = '$id' and account_id='{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
        UIUtil::showAlert('A To-Do-List record has been removed.');
    
}
?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="<?=ROOT_DIR?>/system.php">System</a> - <a href="<?=ROOT_DIR?>/add-ons.php">Add ons</a> - To Do List</h1>
<table border="0" cellpadding="0" cellspacing="0" width="100%" class="main-view-table">
    <tr>
        <td>
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="containertitle">
                <tr>
                    <td>To Do List</td>
                </tr>
            </table>
            <form method="post" action="?" name="add-stages">
            <table border="0" cellpadding="0" cellspacing="0" width="100%" class="infocontainer">

                <tr>
                    <td>
                        <input type="text" name="todolist_name" size="50" value="">
                        <input name="submit-new" type="submit" value="Add">
                        <input type="button" value="Set Order" class="btn btn-blue pull-right"  data-script="todolist_order.php" rel="open-modal" ></input>
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
                     To Do List
                      
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
$todolist = DBUtil::queryToArray('select * from tbl_todolist where account_id = '.$_SESSION['ao_accountid'].' order by order_num');
//sort by stage number
/*usort($stages, function($a, $b) {
    return $a['stage_num'] - $b['stage_num'];
});*/

foreach($todolist as $key => $stage) {
    $class = $key % 2 == 0 ? 'even' : 'odd';
    ?>
    <form method="post" action="?">
  <tr class="<?=$class?>">
      
      <td>
            <input type="hidden" name="id" value="<?=$stage['todolist_id']?>">
            <input  size="50" type="text" name="todolist_name" value="<?=$stage['todolist_name']?>" id="todolist_name-<?=$stage['todolist_id']?>" />
            <input name="submit-edit" type="submit" value="Edit">
            <a href="<?=ROOT_DIR?>/to-do-list-job.php?id=<?=$stage['todolist_id']?>" class="btn btn-small btn-success" title="View '<?=$stage['todolist_name']?>' job list" tooltip>            
                View
            </a>
            <a href="<?=ROOT_DIR?>/to-do-list.php?action=del&id=<?=$stage['todolist_id']?>" class="btn btn-small btn-danger" title="Delete '<?=$stage['todolist_name']?>'" tooltip>            
                <i class="icon-trash"></i>
            </a>
        </td>
  </tr>
      </form>
    <?php
}
if(!count($todolist)) {
?>
                <tr>
                    <td align="center"><b>No Do List Found</b></td>
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
