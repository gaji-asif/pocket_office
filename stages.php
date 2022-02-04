<?php
include 'includes/common_lib.php';
UserModel::isAuthenticated();
echo ViewUtil::loadView('doc-head');

$stages = StageModel::getStages();

//sort by stage number
usort($stages, function($a, $b) {
    return $a['stage_num'] - $b['stage_num'];
});

//$requirements = MapUtil::mapTo(StageUtil::getRequirements(), 'stage_req_id');
?>
<div class="padded">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th>Number</th>
                <th>Name</th>
                <th>Requirements</th>
            </tr>
        </thead>
        <tbody>
<?php
foreach($stages as $stage) {
    $requirements = StageModel::getRequirements(MapUtil::get($stage, 'stage_id'));
    $requirementNames = array_map(function($requirement) {
        return MapUtil::get($requirement, 'label') . ' (' . MapUtil::get($requirement, 'stage_req_id') . ')';
    }, $requirements);
?>
            <tr>
                <td><?=MapUtil::get($stage, 'stage_num')?></td>
                <td><?=MapUtil::get($stage, 'stage')?> (<?=MapUtil::get($stage, 'stage_id')?>)</td>
                <td><?=implode('<br />', $requirementNames)?></td>
            </tr>
<?php
}
?>
        </tbody>
    </table>
</div>
</body>
</html>