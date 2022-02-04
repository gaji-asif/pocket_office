<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
if ($_SESSION['ao_founder'] != 1)
	die("Insufficient Rights");
?>

<?= ViewUtil::loadView('doc-head') ?>
<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Materials</h1>
<div class="btn-group pull-right page-menu">
    <div rel="open-modal" data-script="add_brand.php" class="btn" title="Edit brands" tooltip>
        Brands&nbsp;
        <i class="icon-pencil"></i>
    </div>
    <div rel="open-modal" data-script="add_category.php" class="btn" title="Edit categories" tooltip>
        Categories&nbsp;
        <i class="icon-pencil"></i>
    </div>
    <div rel="open-modal" data-script="add_material.php" class="btn btn-success" title="Add material" tooltip>
        <i class="icon-plus"></i>
    </div>
</div>
<?php
$categoryId = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['cat']);
$brandId = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['brand']);
$material_id = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['id']);
$action = mysqli_real_escape_string(DBUtil::Dbcont(),$_GET['action']);

if (!empty($categoryId) && !empty($material_id) && $action == 'del')
{
	$sql = "select sheet_item_id from sheet_items where material_id='" . $material_id . "' limit 1";
	$res = DBUtil::query($sql) or die(mysqli_error());
	if (mysqli_num_rows($res) != 0)
		UIUtil::showAlert('Jobs Currently Associated - Cannot Remove');
	else
	{
		$sql = "delete from materials where material_id='" . $material_id . "' and account_id='" . $_SESSION['ao_accountid'] . "' limit 1";
		DBUtil::query($sql) or die(mysqli_error());
		$sql = "delete from colors where material_id='" . $material_id . "'";
		DBUtil::query($sql) or die(mysqli_error());
	}
}

if (empty($brandId) &&  empty($categoryId))
{
?>
    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr valign='top'>
        <td>
          <table border=0 cellpadding=0 cellspacing=0 width='100%' class='smcontainertitle'>
            <tr>
              <td width=203>Material Category</td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border=0 cellspacing=0 cellpadding=2 width='100%' class='infocontainernopadding'>
<?php
$categories_array = MaterialModel::getAllCategories();

if(empty($categories_array))
{
?>
        <tr>
          <td><b>No Categories</b></td>
        </tr>
<?php
}
foreach($categories_array as $i => $categoryId)
{
	$class = 'odd';
	if ($i % 2 == 0)
		$class = 'even';
?>
            <tr class='<?php echo $class; ?>'>
              <td><a href='?cat=<?=$categoryId['category_id']?>' class='basiclink'><?=$categoryId['category']?></a></td>
            </tr>
<?php
}
?>
          </table>
        </td>
      </tr>
    </table>
<?php
}

else if(empty($brandId) && !empty($categoryId))
{
	$current_category = MaterialModel::getCategoryById($categoryId);
?>
    <table border="0" cellpadding="0" cellspacing="0" class="main-view-table">
      <tr valign='top'>
        <td>
          <table border=0 cellpadding=0 cellspacing=0 width='100%' class='smcontainertitle'>
            <tr>
              <td width=203><?=$current_category['category']?></td>
            </tr>
          </table>
        </td>
      </tr>
      <tr>
        <td>
          <table border=0 cellspacing=0 cellpadding=2 width='100%' class='infocontainernopadding'>
<?php
	if(empty($current_category))
	{
?>
            <tr>
              <td align="center"><b>Category Not Found</b></td>
            </tr>
    <?php
	}
	else
	{
		$brands_array = MaterialModel::getAllBrands();
		array_push($brands_array,  array(
			'brand_id' => '-1',
			'brand' => 'Varies'
			)
		);

		foreach($brands_array as $brand) {
			$materials_array = MaterialModel::getMaterialsInCategoryAndInBrand($current_category['category_id'], $brand['brand_id']);
			//echo "<pre>"; print_r($materials_array); echo "</pre>";
			if (sizeof($materials_array) != 0) {
?>
            <tr>
              <td colspan=2>&nbsp;</td>
            </tr>
            <tr>
              <td colspan=2 style='border-bottom: solid 1px #999999;'><b><?php echo $brand['brand']." (".sizeof($materials_array)."):"; ?></b></td>
            </tr>
<?php
				foreach($materials_array as $i => $material)
				{
					$class = 'odd';
					if ($i % 2 == 0)
						$class = 'even';

					$material['material'] = stripslashes($material['material']);
					$material['info'] = stripslashes($material['info']);
					if($material['price'] == 0)
					{
						$material['price'] = 'N/A';
					}

					$active_class = '';
					if($material['active'] == 0)
					{
						$active_class = 'inactive';
					}
?>
            <tr class="<?=$class?> <?=$active_class?>" valign='top'>
              <td width=300>
                <table border=0 width='100%'>
                  <tr>
                    <td width=16>
                      <a href='javascript:if(confirm("Are you sure?")){document.location="?action=del&cat=<?php echo $material['category_id']; ?>&id=<?php echo $material['material_id']; ?>";}'>
                        <img src='<?=ROOT_DIR?>/images/icons/delete.png'>
                      </a>
                    </td>
                    <td width=16>
                      <a href='javascript:applyOverlay("edit_material.php?id=<?php echo $material['material_id']; ?>");'>
                        <img src='<?=ROOT_DIR?>/images/icons/pencil_16.png'>
                      </a>
                    </td>
                    <td>
                      <?php echo $material['material']; ?>
                    </td>
                  </tr>
                </table>
              </td>
              <td class='smallnote'>
                <?php echo $material['info']; ?>
                <br />
                <b>Unit:</b> <?php echo $material['unit']; ?>
                <br>
                <b>Price:</b> <?php echo $material['price']; ?>
              </td>
            </tr>
<?php
				}
			}
		}
	}
?>
            <tr>
              <td colspan=2>&nbsp;</td>
            </tr>
            <tr>
              <td class='infofooter' colspan=2>
                <table border=0 cellpadding=0 cellspacing=0>
                  <tr>
                    <td width=25><img src='<?=ROOT_DIR?>/images/icons/left_16.png'></td>
                    <td><a href='?' class='basiclink'>Back</a></td>
                  </tr>
                </table>
              </td>
            </tr>
          </table>
        </td>
      </tr>
    </table>
<?php
}
?>
  <br>
  </body>
</html>
