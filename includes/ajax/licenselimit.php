<?php 
include '../common_lib.php';
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkAccess('add_user', TRUE);
?>
    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
      <tr>
        <td>                                                                                 
          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
            <tr valign="center">
              <td width=16>
                <img src='<?=IMAGES_DIR?>/icons/warning_16.png'>
              </td>
              <td>
                Licence Limit
              </td>
              <td align="right">
              <i class="icon-remove grey btn-close-modal"></i>
              </td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td class="infocontainernopadding">
          <table width="100%" border="0" cellpadding="0" cellspacing="0">
            <tr>
              <td class="listrownoborder">
                You account has a License User Limit of <b><?php echo UserModel::licenseLimit(); ?></b>. You currently have <b><?php echo UserModel::numCurrentUsers(); ?></b> users.
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
  </body>
</html>