<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}
$msg='';
if(RequestUtil::get('update')) {    
    $input = RequestUtil::get('todolist');
    $count = 1;
    foreach($input as $todolist_id){
        $sql = "update tbl_todolist_job set order_num = ".$count." where tbl_todolist_job_id = ".$todolist_id;
      
        DBUtil::query($sql);
        $count++;
        $msg="Changed Order Saved Succesfully!";
    }
}
$id = RequestUtil::get('id');
//$job_id = RequestUtil::get('job_id');
$todolist = DBUtil::queryToArray('select * from tbl_todolist_job where account_id = '.$_SESSION['ao_accountid'].' AND todolist_id='.$id.'  and is_deleted="0" order by order_num');
?>
<form method="post">
    <input type="hidden" name="account_id" value="<?=$_SESSION['ao_accountid']?>">
    <h1 class="page-title"><i class="icon-cogs"></i>Set  To Do List Order</h1>
    <span class="pull-right" style="background-color: #e1e1e1;padding: 10px;text-align: center;"><i class="icon-remove grey btn-close-modal"></i></span>
    <?php if($msg){?>
    <br><span style="color: green; margin-left: 20px;"><?=$msg?></span>
    <?php }?>

    <table style="padding:30px;" cellpadding="10" id="job_stage_table" class="table-striped table-hover">
        <tr>
            <th>Order</th>
            <th>Code generator job</th>            
        </tr>
        <?php 
        foreach($todolist as $stage){  
        ?>
        <tr>
            <td><?=$stage['order_num']?></td>
            <td><?=$stage['name']?><input type="hidden" name="todolist[]" value="<?=$stage['tbl_todolist_job_id']?>"></td>            
        </tr>
        <?php } ?>        
    </table>
     <input type="submit" name="update" value="Update">
</form>
<p><i>* Use Drag and Drop to change the order of todolist job and then, click Update</i></p>
<script src="<?=ROOT_DIR ?>/includes/js/jquery.tablednd.0.7.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#job_stage_table").tableDnD();   
});
</script>