<?php
if(!empty($options) && is_array($options))
{
    $attributes_str = '';
    if(!empty($attributes) && is_array($attributes))
    {
        foreach($attributes as $attribute => $value)
        {
            $attributes_str .= ' ' . $attribute . '="' . $value . '"';
        }
    }
?>
<select<?=$attributes_str?>>
<?php
    foreach($options as $option)
    {
        $selected = '';
        if(!empty($selected_value) && @$option[@$value_key] == $selected_value)
        {
            $selected = 'selected';
        }
?>
    <option value="<?=@$option[@$value_key]?>" <?=$selected?>><?=@$option[@$label_key]?></option>
<?php
    }
?>
</select>
<?php
}
?>