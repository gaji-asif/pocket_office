<?php

class Sheet extends AssureClass {

    public $sheet_id;
    public $job_id;
    public $user_id;
    public $user_fname;
    public $user_lname;
    public $label;
    public $size;
    public $notes;
    public $timestamp;
    public $account_id;
    public $delivery_date;
    public $confirmed;
    public $supplier_id;
    public $supplier_name;
    public $supplier_contact;
    public $supplier_phone;
    public $supplier_fax;
    public $supplier_email;

    protected function construct($id, $dieIfNotFound = TRUE) {
        RequestUtil::set('ignore_cache', 1);
        $record = DBUtil::getRecord('sheets', $id);
        
        if (!count($record)) {
            if ($dieIfNotFound) {
                die('Material sheet not found');
            } else {
                return FALSE;
            }
        }
        list($this->sheet_id, $this->job_id, $this->user_id, $this->account_id, $this->supplier_id, $this->label, $this->size, $this->notes, $this->delivery_date, $this->confirmed, $this->timestamp) = array_values($record);
        $this->build($record);

        $this->setUserNames();
        if (!empty($this->supplier_id)) {
            $this->setSupplierData();
        }
        
        return TRUE;
    }

    function setUserNames() {
        $sql = "select fname, lname from users where user_id='" . $this->user_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->user_fname, $this->user_lname) = mysqli_fetch_row($res);
    }

    function setSupplierData() {
        $sql = "select * from suppliers where supplier_id='" . $this->supplier_id . "' limit 1";
        $res = DBUtil::query($sql);
        list($this->supplier_id, $this->supplier_name, $this->supplier_contact, $this->supplier_email, $this->supplier_phone, $this->supplier_fax) = mysqli_fetch_row($res);
    }

    function getMaterialsList() {
        $sql = "select sheet_items.sheet_item_id, sheet_items.quantity, materials.material, colors.color, materials.price, truncate((materials.price * sheet_items.quantity),2), units.unit, materials.brand_id" .
                " from units,materials,sheet_items" .
                " left join colors" .
                " on colors.color_id=sheet_items.color_id" .
                " where units.unit_id=materials.unit_id and sheet_items.sheet_id='" . $this->sheet_id . "' and sheet_items.material_id=materials.material_id";
        $res = DBUtil::query($sql);

        if (mysqli_num_rows($res) == 0)
            echo "<tr><td align='center'><b>No Materials</b></td></tr>";

        $i = 0;
        $total_cost = 0;
        while (list($sheet_item_id, $qty, $material, $color, $price, $line_total, $unit, $brand_id) = mysqli_fetch_row($res)) {
            $class = 'odd';
            if ($i % 2 == 0)
                $class = 'even';

            $material = stripslashes($material);

            $total_cost += $line_total;

            $plus = $qty + 1;
            $minus = $qty - 1;
            if ($minus == 0) {
                $minus = 'del';
            }

            if ($brand_id == '-1') {
                $brand = "Varies";
            } else {
                $brand_sql = "select brand from brands where brand_id='" . $brand_id . "' limit 1";
                $brand_res = DBUtil::query($brand_sql);
                list($brand) = mysqli_fetch_row($brand_res);
            }
            ?>
            <tr class='<?= $class ?>' valign='center'>
                <td><?= $material ?></td>
                <td width=96 class='smallnote'><?= $brand ?></td>
                <td width=148 class='smallnote'><?= $color ?></td>
                <td width=42 align='center' class='smallnote'><?= $unit ?></td>
                <td width=52 align='right'><?= $price ?></td>
                <td width=52 align='right' id='qty'>
            <?= $qty ?>
                </td>
                <td width=52 align='right'><?= $line_total ?></td>
                <td width=62 align='right'>
                    <a class='basiclink' href='javascript: changeQty("<?= $this->sheet_id ?>", "<?= $this->job_id ?>", "<?= $minus ?>", "<?= $sheet_item_id ?>"); Request.makeParent("<?= AJAX_DIR ?>/get_job.php?id=<?= $this->job_id ?>", "jobscontainer", "", "yes", "");'><img src='<?= IMAGES_DIR ?>/icons/minus.png'></a>
                    <a class='basiclink' href='javascript: changeQty("<?= $this->sheet_id ?>", "<?= $this->job_id ?>", "<?= $plus ?>", "<?= $sheet_item_id ?>"); Request.makeParent("<?= AJAX_DIR ?>/get_job.php?id=<?= $this->job_id ?>", "jobscontainer", "", "yes", "");'><img src='<?= IMAGES_DIR ?>/icons/add.png'></a>
                    <a class='basiclink' href='javascript: changeQty("<?= $this->sheet_id ?>", "<?= $this->job_id ?>", "del", "<?= $sheet_item_id ?>"); Request.makeParent("<?= AJAX_DIR ?>/get_job.php?id=<?= $this->job_id ?>", "jobscontainer", "", "yes", "");'><img src='<?= IMAGES_DIR ?>/icons/delete.png'></a>
                </td>
            </tr>
                    <?php
                    $i++;
                }
                $total_cost = number_format($total_cost, 2, '.', '');
                if ($total_cost > 0) {
                    ?>
            <tr><td style='border-top: 1px solid #cccccc;' align='right' colspan=8><b>Cost:</b> $<?= $total_cost ?></td></td>
            <?php
        }
    }

}
?>