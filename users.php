<?php

include 'includes/common_lib.php';
$this_page = new pageinfo(basename($_SERVER['SCRIPT_NAME']));
pageSecure($this_page->source);

$id = RequestUtil::get('id');

?>

<?=ViewUtil::loadView('doc-head')?>

    <span id='notes'></span>
    
<h1 class="page-title"><i class="icon-group"></i><?=$this_page->title;?></h1>

<?php
if(ModuleUtil::checkAccess('add_user'))
{
?>
<div class="btn-group pull-right page-menu">
    <div rel="open-modal" data-script="add_user.php" class="btn btn-success" title="Add user" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>
<?php
}


if(ModuleUtil::checkAccess('view_users'))
{
?>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr valign='middle'>
        <td width=40>
          <b>Sort:</b>
        </td>
        <td width=10>
          <select id="sort" class="list-filter-input">
            <option value='order by lname asc'>Last Name A-Z</option>
            <option value='order by lname desc'>Last Name Z-A</option>
            <option value='order by dba asc'>DBA A-Z</option>
            <option value='order by dba desc'>DBA Z-A</option>
            <option value='order by reg_date desc'>Newest First</option>
            <option value='order by reg_date asc'>Oldest First</option>
          </select>
        </td>
        <td>
          <input type="button" value="Sort" rel="filter-list-btn">
        </td>
        <td class="text-right list-search">
			<form method="post" rel="filter-list-form" action="<?=AJAX_DIR?>/get_userlist.php" data-destination="userscontainer">
			<input type="text" id="search" class="list-filter-input" value="">
            <i class="icon-search"></i>
            </form>
        </td>
      </tr>
    </table>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center'>
              <td>Name</td>
              <td width="30%">DBA</td>
              <td width="15%">Account DOB</td>
              <td width="15%">Status</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td id="userscontainer"></td>
      </tr>
      <tr><td colspan=2>&nbsp;</td></tr>
    </table>
	<script type="text/javascript">
		$(document).ready(function(){
//			console.log('document ready', arguments);
<?php
	if($id) {
?>
            Request.make('<?=AJAX_DIR?>/get_notes.php?type=users&id=<?=$id?>', 'notes', false, true);
            Request.make('<?=AJAX_DIR?>/get_user.php?id=<?=$id?>', 'userscontainer', true, true);
<?php
	}
	else {
?>
            Request.make('<?=AJAX_DIR?>/get_userlist.php', 'userscontainer', true, true);
<?php
	}
}
?>
		});
	</script>
  </body>
</html>
