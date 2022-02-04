<?php
if(!$name) { return; }
$customers = CustomerModel::getAllCustomers(UIUtil::getFirstLast());
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($customers as $customer) {
?>
    <option value="<?=MapUtil::get($customer, 'customer_id')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($customer, 'select_label'))?>
    </option>
<?php
}
?>
</select>