<?php

include 'includes/common_lib.php';

$search_term = $_GET['search'];
if(isset($_GET['id']))
{
	$search_term = $_GET['id'];
}

?>

<?=ViewUtil::loadView('doc-head')?>

<h1 class="page-title"><i class="icon-search"></i>Search</h1>
<?php
if(!empty($search_term))
{
  UserModel::storeBrowsingHistory($search_term, 'search_16', 'search.php', $search_term);
  if(ModuleUtil::checkAccess('view_jobs'))
  {
?>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr>
        <td>
          <table border=0 width='100%'>
			  <tr valign="center">
              <td width=16><img src='<?=IMAGES_DIR?>/icons/briefcase_16.png'></td>
              <td><a href='jobs.php' class='navlink'>Jobs<a></td>
            </tr>
          </table>
        </td>
      </tr>
      <?=ViewUtil::loadView('job-header', array('quick_settings' => false))?>
      <tr>
        <td id='jobscontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/search_joblist.php?search=<?=$search_term?>', 'jobscontainer', true, true);
          </script>
        </td>
      </tr>
    </table>
<?php
  }
  if(ModuleUtil::checkAccess('view_users'))
  {
?>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr>
        <td>
          <table border=0 width='100%'>
            <tr valign='center'>
              <td width=16><img src='<?=IMAGES_DIR?>/icons/user_16.png'></td>
              <td><a href='users.php' class='navlink'>Users<a></td>
            </tr>
          </table>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td>Name</td>
              <td width=250>DBA</td>
              <td width=200>Account DOB</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td id='userscontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/search_userlist.php?search=<?=$search_term?>', 'userscontainer', true, true);
          </script>
        </td>
    </tr>
    </table>
<?php
  }
  if(ModuleUtil::checkAccess('view_customers'))
  {
?>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr>
        <td>
          <table border=0 width='100%'>
            <tr valign='center'>
              <td width=16><img src='<?=IMAGES_DIR?>/icons/address_16.png'></td>
              <td><a href='customers.php' class='navlink'>Customers<a></td>
            </tr>
          </table>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign='center'>
              <td>Customer Name</td>
              <td width=205>Timestamp</td>
              <td width=200>Added By</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td id='customerscontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/search_customerlist.php?search=<?=$search_term?>', 'customerscontainer', true, true);
          </script>
        </td>
    </tr>
    </table>
<?php
  }
  if(ModuleUtil::checkAccess('view_documents'))
  {
?>
    <table border=0 cellspacing=0 cellpadding=0 class="main-view-table">
      <tr>
        <td>
          <table border=0 width='100%'>
            <tr valign='center'>
              <td width=16><img src='<?=IMAGES_DIR?>/icons/folder_16.png'></td>
              <td><a href='documents.php' class='navlink'>Documents<a></td>
            </tr>
          </table>
        </td>
      </tr>
	  <?=ViewUtil::loadView('document-header')?>
      <tr>
        <td id='documentscontainer'></td>
      </tr>
      <tr>
        <td>
          &nbsp;
          <script type='text/javascript'>
            Request.make('<?=AJAX_DIR?>/search_documentlist.php?search=<?=$search_term?>', 'documentscontainer', true, true);
          </script>
        </td>
    </tr>
    </table>
<?php
  }
}
?>
</body>
</html>
