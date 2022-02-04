<?php
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('view_user_history', TRUE);
?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td>
                User Browsing History
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
          <table border="0" cellpadding="0" cellspacing="0" width="100%" class='infocontainer'>
            <tr valign='top'>
              <td>
                <table border="0" cellpadding="0" cellspacing="0" width="100%" class='smcontainertitle'>
                  <tr>
                    <td>Item</td>
                  </tr>
                </table>
              </td>
            </tr>
            <tr>
              <td>
                <table border="0" cellspacing="0" cellpadding=2 width="100%" class="infocontainernopadding">
<?php
//icon conversion table
//TODO: run script to modify database records for new icons...
$iconConversions = array(
	'briefcase_16' => 'icon-briefcase',
	'flag_16' => 'icon-bullhorn',
	'search_16' => 'icon-search',
	'address_16' => 'icon-book',
	'email_16' => 'icon-envelope',
	'user_16' => 'icon-user',
	'default' => 'icon-file'
);

$browsingHistory = UserModel::getBrowsingHistory(100);
if(!count($browsingHistory)) {
?>
                  <tr>
                    <td colspan=2 align="center"><b>No History Found</b></td>
                  </tr>
<?php
}
foreach($browsingHistory as $key => $item) {
    $class = 'odd';
    if($key%2 == 0) {
        $class = 'even';
    }
?>
                  <tr class="<?=$class?>">
                    <td>
                        <i class="<?=(isset($iconConversions[$item['icon']])) ? $iconConversions[$item['icon']] : $iconConversions['default']?>"></i>&nbsp;
                        <a href="" onclick="parent.location = '<?=ROOT_DIR?>/<?=$item['script']?>?id=<?=$item['item_id']?>'; return false;">
                            <?=$item['title']?>
                        </a>
                    </td>
                  </tr>
<?php
  $i++;
}
?>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>