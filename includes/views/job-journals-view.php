<?php
$firstLast = UIUtil::getFirstLast();
//print_r(get_class($myJob));
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }
?>
<tr class="job-tab-content journals" <?=@$show_content_style?>>
    <td colspan="11">
        <div class="journal-quick-add clearfix">
            <div class="row">
                <textarea placeholder="Add journal" rel="mention"></textarea> 
            </div>
            <div class="row">
                <div class="col span-6">
					<div class="testuser"></div>
					<?php
					$selectusers = UserModel::getjobID($showInactiveUsers, $firstLast, $_REQUEST['id']);
					foreach($selectusers as $selectuser) { 
						$uid = $selectuser['user_id']; ?>
						<div id="user-id-<?php echo $uid ?>" class="rmuser">
								<?php	echo $unm = $selectuser['select_label']; ?> 
					
								<i data-user-id="<?=@$uid?>" rel="delete-journal-user" class="icon-remove" style="cursor: pointer;"></i>
						</div>
					<?php		
					}
?>
						
                    <select name="recipients[]" class="tss-multi" data-placeholder="Recipients" multiple>
<?php
	//echo "Comes2.<pre>"; print_r($_REQUEST['id']);
	
$showInactiveUsers = AccountModel::getMetaValue('show_inactive_users_in_lists');

$dropdownUserLevels = AccountModel::getMetaValue('assign_journal_recipient_user_dropdown');
$users = [];
if($_SESSION['ao_accountid'] == 24 && in_array($_SESSION['ao_level'],[3,4,5]))
{
    $concatSql = $firstLast ? "concat(fname, ' ', lname)" : "concat(lname, ', ', fname)";

    $sql = "SELECT user_id, $concatSql as select_label FROM users  WHERE account_id = '{$_SESSION['ao_accountid']}' AND `username` = 'hailmail' ";
    
    $users = DBUtil::queryToArray($sql, 'user_id');
}
else
{
    $users = !empty($dropdownUserLevels)
            ? UserModel::getAllByLevel($dropdownUserLevels, $showInactiveUsers, $firstLast)
            : UserModel::getAll($showInactiveUsers, $firstLast);
}
foreach($users as $user) {
						$unm2[] = $user['select_label'];
?>
                        <option value="<?=$user['user_id']?>"><?=$user['select_label']?></option>
<?php
}
?>
                    </select>
					
					<?php
						/*print_r($unm2);
						$name = array_intersect($unm,$unm2))
						$name1 = array_push($unm2, $name);
						print_r($name1); */
					?>
					
                </div>
                <div class="col span-6">
                    <div class="btn-group pull-right">
                        <div class="btn btn-blue btn-small" rel="journal-quick-add" data-job-id="<?=$myJob->job_id?>" title="Post journal" tooltip>
                            <i class="icon-save"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
<?php
foreach($myJob->journals_array as $journal) {
    $viewData = array(
        'myJob' => $myJob,
        'journal' => $journal
    );
    echo ViewUtil::loadView('journal', $viewData);
}
?>
    </td>
</tr>
<script>
<?php
if(AccountModel::getMetaValue('allow_mentions')) {
?>
UI.atWho($('[rel="mention"]'));
<?php
}
?>

	jQuery(document).on('click', '.icon-remove', function() {
    jQuery(this).parent().remove();
});


	
</script>
