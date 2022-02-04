<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
UserModel::isAuthenticated();
if($_SESSION['ao_founder'] != 1) {
    die('Insufficient Rights');
}
if(RequestUtil::get('update')) {    
    $input = RequestUtil::get('stage');
    $count = 1;
    foreach($input as $stage_id){
        $sql = "update stages set order_num = ".$count." where stage_id = ".$stage_id;
      
        DBUtil::query($sql);
        $count++;
    }
}
//$job_id = RequestUtil::get('job_id');
$stages = DBUtil::queryToArray('select * from stages where account_id = '.$_SESSION['ao_accountid'].' order by order_num');
?>
<form method="post">
    <input type="hidden" name="account_id" value="<?=$_SESSION['ao_accountid']?>">
    <table style="padding:30px;" cellpadding="10" id="job_stage_table" class="table-striped table-hover">
        <tr>
            <th>Order/Number</th>
            <th>Stage</th>            
        </tr>
        <?php 
        foreach($stages as $stage){         
            $stage_name = StageModel::getStageNameById($stage['stage_id']);
        ?>
        <tr>
            <td><?=$stage['order_num']?></td>
            <td><?=$stage_name?><input type="hidden" name="stage[]" value="<?=$stage['stage_id']?>"></td>            
        </tr>
        <?php } ?>        
    </table>
     <input type="submit" name="update" value="Update">
</form>
<p><i>* Use Drag and Drop to change the order of stages and then, click Update</i></p>
<script src="<?=ROOT_DIR ?>/includes/js/jquery.tablednd.0.7.min.js"></script>
<script type="text/javascript">
$(document).ready(function() {
    $("#job_stage_table").tableDnD();   
});
</script>