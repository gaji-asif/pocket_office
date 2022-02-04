<?php
include '../common_lib.php'; 

echo ViewUtil::loadView('doc-head');

ModuleUtil::checkIsFounder(TRUE);

$id = RequestUtil::get('id');
$action = RequestUtil::get('action');
$title = RequestUtil::get('title');
$editTitle = RequestUtil::get('edittitle');

$errors = array();
if($id && $action == 'del') {
    $sql = "SELECT material_id
            FROM materials
            WHERE category_id = '$id'
                AND active = 1
            LIMIT 1";
    $results = DBUtil::query($sql);

    if(DBUtil::hasRows($results)) {
        $errors[] = 'Materials currently associate - cannot remove';
    }
    
    if(!count($errors)) {
        $sql = "DELETE FROM categories
                WHERE category_id = '$id'
                    AND account_id='{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
    }
}


if(RequestUtil::get('submit-new')) {
    if(!$title) {
        $errors[] = 'Title cannot be empty';
    }
    
    if(!count($errors)) {
        $sql = "INSERT INTO categories
                VALUES (NULL, '{$_SESSION['ao_accountid']}', '$title')";
        DBUtil::query($sql);
    }
}

if(RequestUtil::get('submit-edit')) {
    if(!$editTitle) {
        $errors[] = 'Title cannot be empty';
    }
    
    if(!count($errors)) {
        $sql = "UPDATE categories
                SET category = '$editTitle'
                WHERE category_id = '$id'
                    AND account_id='{$_SESSION['ao_accountid']}'
                LIMIT 1";
        DBUtil::query($sql);
    }
}
?>
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr valign="center">
        <td>Modify Categories</td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal reload-parent"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitemnoborder">
            <b>Add Category:</b>
        </td>
        <td class="listrownoborder">
            <form action="?" method="post" name="category">
            <input type="text" name="title">
            <input name="submit-new" type="submit" value="Add">
            </form>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem"><b>Current Categories:</b></td>
        <td class="listrow">
<?php
$categories = MaterialModel::getAllCategories();
foreach($categories as $category) {
?>
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tr>
                    <td width="20">
                        <a href="" onclick="if(confirm('Are you sure?')){window.location='?action=del&id=<?=MapUtil::get($category, 'category_id')?>';} return false;">
                          <img src="<?=IMAGES_DIR?>/icons/delete.png">
                        </a>
                    </td>
                    <td>
                        <form method="post" action="" name="brand">
                            <input type="text" name="edittitle" value="<?=MapUtil::get($category, 'category')?>">
                            <input type="hidden" name="id" value="<?=MapUtil::get($category, 'category_id')?>">
                            <input name="submit-edit" type="submit" value="Edit">
                        </form>
                    </td>
                </tr>
            </table>
<?php
}
?>
        </td>
    </tr>
</table>
</body>
</html>
