<?php
if(!isset($info) || empty($info) || !is_array($info)) { return; }
?>
<ul>
<?php
foreach($info as $label => $value) {
    if(empty($value)) { continue; }
?>
    <li>
        <b><?=$label?>:</b>&nbsp;
        <?=$value?>
    </li>
<?php
}
?>
</ul>