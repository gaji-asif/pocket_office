<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1)
	die ("Insufficient Rights");

//user list configuration array
$userListConfiguration = array(
	'assign_job_salesman_user_dropdown' => 'Assign Job Salesman User List',
	'assign_job_referral_user_dropdown' => 'Assign Job Referral User List',
	'assign_job_subscriber_user_dropdown' => 'Assign Job Subscriber User List',
	'assign_journal_recipient_user_dropdown' => 'Assign Journal Recipient User List',
	'assign_repair_contractor_user_dropdown' => 'Assign Repair Contractor User List',
	'assign_task_contractor_user_dropdown' => 'Assign Task Contractor User List',
	'assign_job_canvasser_user_dropdown' => 'Assign Job Canvasser User List',
	'assign_job_referral_user_dropdown' => 'Assign Job Referral User List',
	'assign_job_salesman_user_dropdown' => 'Assign Job Salesman User List',
	'assign_task_contractor_user_dropdown' => 'Assign Task Contractor User List',
	'job_salesman_filter_user_dropdown' => 'Job Salesman Filter User List'
);

//get data
$userlevels = UserModel::getAllLevels();
$accountMetaData = AccountModel::getAllMetaData();

?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - App Configuration</h1>
<table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
	<tr>
        <td>
			<table border=0 width="100%" align="left" cellpadding=0 cellspacing=0 class="containertitle">
				<tr>
					<td>Tasks</td>
				</tr>
			</table>
        </td>
	</tr>
	<tr>
        <td>
			<table border=0 width="100%" align="left" cellpadding=2 cellspacing=0 class="infocontainernopadding" id="interface-config">
                <tr>
					<td class="listitemnoborder" width="30%">
						<b>Require Task Stage:</b>
					</td>
					<td class="listrownoborder">
                        <input type="checkbox" 
                               data-key="require_task_stage"
                               data-alert="true" 
                               class="onchange-set-meta" 
                               data-type="account"
                               <?=MetaUtil::get($accountMetaData, 'require_task_stage') ? 'checked' : ''?> />
					</td>
				</tr>
            </table>
        </td>
    </tr>
</table>
<br />
<table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
	<tr>
        <td>
			<table border=0 width="100%" align="left" cellpadding=0 cellspacing=0 class="containertitle">
				<tr>
					<td>Users</td>
				</tr>
			</table>
        </td>
	</tr>
	<tr>
        <td>
			<table border=0 width="100%" align="left" cellpadding=2 cellspacing=0 class="infocontainernopadding" id="interface-config">
				<tr>
					<td class="listitemnoborder" width="30%">
						<b>User Session Expiration:</b>
					</td>
					<td class="listrownoborder">
						<select data-key="user_session_timeout" data-alert="true" class="onchange-set-meta" data-type="account">
							<option value="<?=(60*15)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*15)){?> selected <?php }?>>15 minutes</option>
							<option value="<?=(60*30)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*30)){?> selected <?php }?>>30 minutes</option>
							<option value="<?=(60*60*1)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*1)){?> selected <?php }?>>1 hour</option>
							<option value="<?=(60*60*2)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*2)){?> selected <?php }?>>2 hours</option>
							<option value="<?=(60*60*4)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*4)){?> selected <?php }?>>4 hours</option>
							<option value="<?=(60*60*6)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*6)){?> selected <?php }?>>6 hours</option>
							<option value="<?=(60*60*8)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*8)){?> selected <?php }?>>8 hours</option>
							<option value="<?=(60*60*12)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*12)){?> selected <?php }?>>12 hours</option>
							<option value="<?=(60*60*24*1)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*24*1)){?> selected <?php }?>>1 day</option>
							<option value="<?=(60*60*24*7)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*24*7)){?> selected <?php }?>>1 Week</option>
							<option value="<?=(60*60*24*14)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*24*14)){?> selected <?php }?>>2 Weeks</option>
							<option value="<?=(60*60*24*20)?>" <?php if( MetaUtil::get($accountMetaData, 'user_session_timeout') == (60*60*24*20)){?> selected <?php }?>>1 Month</option>
						</select>
					</td>
				</tr>
				<tr>
					<td class="listitemnoborder" width="30%">
						<b>Page Auto Refesh:</b>
					</td>
					<td class="listrownoborder">
						<select data-key="user_auto_refresh" data-alert="true" class="onchange-set-meta" data-type="account">
						    <option value="<?=(60*0)?>" <?php if( MetaUtil::get($accountMetaData, 'user_auto_refresh') == (60*0)){?> selected <?php }?>>0 minute</option>
							<option value="<?=(60*1)?>" <?php if( MetaUtil::get($accountMetaData, 'user_auto_refresh') == (60*1)){?> selected <?php }?>>1 minute</option>
							<option value="<?=(60*5)?>" <?php if( MetaUtil::get($accountMetaData, 'user_auto_refresh') == (60*5)){?> selected <?php }?>>5 minutes</option>
							<option value="<?=(60*10)?>" <?php if( MetaUtil::get($accountMetaData, 'user_auto_refresh') == (60*10)){?> selected <?php }?>>10 minutes</option>
						</select>
					</td>
				</tr>
                <tr>
					<td class="listitem" width="30%">
						<b>Allow Mentions:</b>
					</td>
					<td class="listrow">
                        <input type="checkbox" 
                               data-key="allow_mentions"
                               data-alert="true" 
                               class="onchange-set-meta" 
                               data-type="account"
                               <?=MetaUtil::get($accountMetaData, 'allow_mentions') ? 'checked' : ''?> />
					</td>
				</tr>
            </table>
        </td>
    </tr>
</table>
<br />
<table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
	<tr>
        <td>
			<table border=0 width="100%" align="left" cellpadding=0 cellspacing=0 class="containertitle">
				<tr>
					<td>
						User Lists
					</td>
				</tr>
			</table>
        </td>
	</tr>
	<tr>
        <td>
			<table border=0 width="100%" align="left" cellpadding=2 cellspacing=0 class="infocontainernopadding" id="interface-config">
				<tr>
					<td class="listitemnoborder" width="30%">
						<b>Show Inactive Users In Lists:</b>
					</td>
					<td class="listrownoborder">
						<input type="checkbox" 
                               data-key="show_inactive_users_in_lists" 
                               data-alert="true" 
                               class="onchange-set-meta" 
                               data-type="account"
                               <?=MetaUtil::get($accountMetaData, 'user_session_timeout') ? 'checked' : ''?> />
					</td>
				</tr>
<?php
foreach($userListConfiguration as $metaKey => $title) {
    $selectedVals = explode(',', MetaUtil::get($accountMetaData, $metaKey, ''));
?>
				<tr>
					<td class="listitem" width="30%">
						<b><?=$title?>:</b><br />
                        <span class="smallnote">No groups selected will show all users.</span>
					</td>
					<td class="listrow">
						<select data-key="<?=$metaKey?>" data-alert="true" class="onchange-set-meta multi-select" data-type="account" multiple="true">
<?php
	foreach($userlevels as $userLevel) {
?>
                            <option value="<?=$userLevel['level_id']?>" <?=in_array(MapUtil::get($userLevel, 'level_id'), $selectedVals) ? 'selected' : ''?>>
                                <?=MapUtil::get($userLevel, 'level')?>
                            </option>
<?php
	}
	reset($userlevels);
?>
						</select>
					</td>
				</tr>
<?php
}
?>
			</table>
        </td>
	</tr>
</table>
<script type="text/javascript">
    $(function() {
        $('select.multi-select').multiSelect();
    });
</script>
</body>
</html>
