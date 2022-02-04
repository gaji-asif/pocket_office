<?php
if(!$name) { return; }
$jurisdictions = CustomerModel::getAllJurisdictions();
?>
<select name="filter_<?=$name?>[]" class="tss-multi" multiple>
<?php
foreach($jurisdictions as $jurisdiction) {
?>
    <option value="<?=MapUtil::get($jurisdiction, 'jurisdiction_id')?>">
        <?=UIUtil::cleanOutput(MapUtil::get($jurisdiction, 'location'))?>
    </option>
<?php
}
?>
</select>