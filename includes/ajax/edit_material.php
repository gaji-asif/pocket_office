<?php

include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

$id = RequestUtil::get('id');
$colorId = RequestUtil::get('color_id');
$action = RequestUtil::get('action');
$material = DBUtil::getRecord('materials');

if(empty($material)) {
    UIUtil::showModalError('Material not found!');
}

$errors = array();
$info = array();
if($action == 'add_color') {
    $color = RequestUtil::get('new_color');
    if(empty($color)) {
        $errors[] = 'Color cannot be empty';
    }
    
    if(!count($errors)) {
        $sql = "INSERT INTO colors (material_id, color)
                VALUES ('$id', '$color')";
        DBUtil::query($sql);
        $info[] = "Material color '$color' successfully added";
    }
} else if($action == 'delete_color') {
    $sheets = DBUtil::getRecord('sheet_items', $colorId, 'color_id');
    if(!empty($sheets)) {
        $errors[] = 'Jobs currently associated - cannot remove';
    }
    
    if(!count($errors)) {
        $sql = "DELETE FROM colors
                WHERE color_id = '$colorId'
                LIMIT 1";
        DBUtil::query($sql);
        $info[] = 'Material color successfully deleted';
    }
} else if(RequestUtil::get('submit')) {
    $title = RequestUtil::get('material');
    $_POST['active'] = RequestUtil::get('active', 0);
    
    if(empty($title)) {
        $errors[] = 'Title cannot be empty';
    }
    
    if(!count($errors)) {
        FormUtil::update('materials');
?>
<script>
    parent.window.location.reload();
</script>
<?php
        die();
    }
}
?>
<form method="post" action="?id=<?=$id?>">
<table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <td>
            Edit Material
        </td>
        <td align="right">
            <i class="icon-remove grey btn-close-modal"></i>
        </td>
    </tr>
</table>
<?=AlertUtil::generate($errors, 'error', TRUE)?>
<?=AlertUtil::generate($info, 'info', TRUE)?>
<table border="0" width="100%" align="left" cellpadding="0" cellspacing="0" class="infocontainernopadding">
    <tr>
        <td width="25%" class="listitem">
            <b>Title:</b>&nbsp;<span class="red">*</span>
        </td>
        <td class="listrow">
            <input type="text" name="material" value="<?=htmlentities(MapUtil::get($material, 'material'))?>">
        </td>
    </tr>
    <tr>
        <td width="25%" class="listitem">
            <b>Active:</b>
        </td>
        <td class="listrow">
            <input type="checkbox" name="active" value="1" <?=MapUtil::get($material, 'active') ? 'checked' : ''?>>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Brand:</b>
        </td>
        <td class="listrow">
            <select name="brand_id">
                <option value="-1">Varies</option>
<?php
$brands = MaterialModel::getAllBrands();
foreach($brands as $brand) {
?>
                <option value="<?=MapUtil::get($brand, 'brand_id')?>" <?=MapUtil::get($brand, 'brand_id') == MapUtil::get($material, 'brand_id') ? 'selected' : ''?>>
                    <?=MapUtil::get($brand, 'brand')?>
                </option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Category:</b>
        </td>
        <td class="listrow">
            <select name="category_id">
<?php
$categories = MaterialModel::getAllCategories();
foreach($categories as $category) {
?>
                <option value="<?=MapUtil::get($category, 'category_id')?>" <?=MapUtil::get($category, 'category_id') == MapUtil::get($material, 'category_id') ? 'selected' : ''?>>
                    <?=MapUtil::get($category, 'category')?>
                </option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Price:</b>
        </td>
        <td class="listrow">
            <input type="text" name="price" value="<?=MapUtil::get($material, 'price')?>" />&nbsp;USD
        </td>
    </tr>
    <tr>
        <td class="listitem">
            <b>Unit:</b>
        </td>
        <td class="listrow">
            <select name="unit_id">
<?php
$units = getAllUnits();
foreach($units as $unit) {
?>
                <option value="<?=MapUtil::get($unit, 'unit_id')?>" <?=MapUtil::get($unit, 'unit_id') == MapUtil::get($material, 'unit_id') ? 'selected' : ''?>>
                    <?=MapUtil::get($unit, 'unit')?>
                </option>
<?php
}
?>
            </select>
        </td>
    </tr>
    <tr valign="top">
        <td class="listitem">
            <b>Description:</b>
        </td>
        <td class="listrow">
            <textarea name="info" rows="7"><?=htmlentities(MapUtil::get($material, 'info'))?></textarea>
        </td>
    </tr>
    <tr valign="top">
        <td width="25%" class="listitem">
            <b>Colors:</b>
        </td>
        <td class="listrow">
            <ul class="job-items-list">
<?php
/*$sqlColor = "SELECT *
                FROM colors
                WHERE material_id = '$id'
                ORDER BY color_id DESC";
        $colors = DBUtil::queryToArray($sqlColor);*/
$colors = MaterialModel::getMaterialColors($id);
foreach($colors as $color) {
?>
                <li>
                    <i class="icon-trash red action" 
                       rel="change-window-location" 
                       data-url="?id=<?=$id?>&action=delete_color&color_id=<?=$color['color_id']?>" 
                       data-confirm="Are you sure you want to delete material color '<?=$color['color']?>'?"></i>&nbsp;
                    <?=$color['color']?>
                </li>
<?php
}
?>
            </ul>
        </td>
    </tr>
    <tr>
        <td class="listitem"><b>Add Color:</b></td>
        <td colspan=2 class="listrow">
            <input type="text" name="new_color">
            <input type="button" name="add_color" value="Add">
        </td>
    </tr>
    <tr>
        <td align="right" colspan=2 class="listrow">
            <input type="submit" name="submit" value="Save">
        </td>
    </tr>
</table>
</form>
<script>
$(function() {
    $('input[name="add_color"]').click(function() {
        var color = $('input[name="new_color"]').val();

        if(!color.length) { return; }
        
        window.location = '?id=<?=$id?>&action=add_color&new_color=' + color;
    });
});
</script>
</body>
</html>
