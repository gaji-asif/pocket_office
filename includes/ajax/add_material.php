<?php
include '../common_lib.php'; 
echo ViewUtil::loadView('doc-head');
ModuleUtil::checkIsFounder(TRUE);

if(!count(MaterialModel::getAllBrands()) || !count(MaterialModel::getAllCategories())) {
    echo UIUtil::showModalError('Please add brands and cateories first');
}

$materialName = RequestUtil::get('material_name');
$brand = RequestUtil::get('brand');
$category = RequestUtil::get('category');
$unit = RequestUtil::get('unit');
$description = RequestUtil::get('description');
$price = RequestUtil::get('price');

$errors = array();
if(RequestUtil::get("submit")) {
    if(empty($materialName)) {
        $errors[] = 'Material name cannot be blank';
    }
    
    if(!count($errors)) {
        $sql = "INSERT INTO materials 
                VALUES (NULL, '$category', '$brand', '$unit', '{$_SESSION['ao_accountid']}', '$materialName', '$description', '$price', 1,0,0)";
        DBUtil::query($sql);
?>

<script>
    window.location = 'edit_material.php?id=<?=DBUtil::getInsertId()?>';
</script>
<?php
    }
}
?>

<form method="post" name="material" action="?">
<table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
    <tr>
        <td>
            <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">
                <tr valign="center">
                    <td>Add Materials</td>
                    <td align="right">
                        <i class="icon-remove grey btn-close-modal"></i>
                    </td>
                </tr>
            </table>
        </td>
    </tr>
<?php
if(count($errors)) {
?>
    <tr>
        <td><?=AlertUtil::generate($errors)?></td>
    </tr>
<?php
}
?>
    <tr>
        <td class="infocontainernopadding">
            <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">
                <tr>
                    <td width="25%" class="listitemnoborder">
                        <b>Material Name:</b>&nbsp;<span class="red">*</span>
                    </td>
                    <td class="listrownoborder">
                        <input type="text" name="material_name">
                    </td>
                </tr>
                <tr>
                    <td class="listitem">
                        <b>Brand:</b>
                    </td>
                    <td class="listrow">
                        <select name="brand">
                            <option value="-1">Varies</option>
<?php
$brands = MaterialModel::getAllBrands();
foreach($brands as $brand) {
?>
                            <option value="<?=MapUtil::get($brand, 'brand_id')?>"><?=MapUtil::get($brand, 'brand')?></option>
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
                        <select name="category">
<?php
$categories = MaterialModel::getAllCategories();
foreach($categories as $category) {
?>
                            <option value="<?=MapUtil::get($category, 'category_id')?>"><?=MapUtil::get($category, 'category')?></option>
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
                        <input type="text" name="price" value="<?=$price?>"> USD
                    </td>
                </tr>
                <tr>
                    <td class="listitem">
                        <b>Unit:</b>
                    </td>
                    <td class="listrow">
                        <select name="unit">
<?php
$units = getAllUnits();
foreach($units as $unit) {
?>
                            <option value="<?=MapUtil::get($unit, 'unit_id')?>"><?=MapUtil::get($unit, 'unit')?></option>
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
                        <textarea name="description" style="width:100%;" rows="4"></textarea>
                    </td>
                </tr>
                <tr>
                    <td align="right" colspan="2" class="listrow">
                        <input name="submit" type="submit" value="submit">
                    </td>
                </tr>
            </table>
        </td>
    </tr>
</table>
</form>
</body>
</html>
