<?php

include 'includes/common_lib.php';

UserModel::isAuthenticated();

echo ViewUtil::loadView('doc-head');

if(!ModuleUtil::checkIsFounder()) {

    UIUtil::showListError('You do not have permission to access this.');

}



$id = RequestUtil::get('id');
$provider = DBUtil::getRecord('insurance');

$action = RequestUtil::get('action');

$name = RequestUtil::get('name');



$errors = array();

$info = array();

if (RequestUtil::get('new')) {

    if (!$name) {

        $errors[] = 'Name cannot be blank';

    }



    if (!count($errors)) {

        $sql = "INSERT INTO insurance (account_id, insurance)

                VALUES ('{$_SESSION['ao_accountid']}', '$name')";

        DBUtil::query($sql);

        $info[] = "'$name' successfully added";

    }

} else if ($action === 'del' && $id) {

    $job = DBUtil::getRecord('jobs', $id, 'insurance_id');

    if (count($job)) {

        $errors[] = 'Jobs currently associated - cannot remove';

    }

    

    if (!count($errors)) {

        $sql = "DELETE FROM insurance

                WHERE insurance_id = '$id'

                    AND account_id = '{$_SESSION['ao_accountid']}'

                LIMIT 1";

        DBUtil::query($sql);

        $info[] = "Insurance provider successfully removed";

    }

}



?>



<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Insurance Providers</h1>

<div class="btn-group pull-right page-menu">

    <div rel="open-modal" data-script="add_edit_providers.php" class="btn btn-success" title="Add Insurance Provider" tooltip>

        <i class="icon-plus"></i>

    </div>

</div>


<div class="list-table-container">

    <div class="container">

      <!--  <div class="row">

            <form method="post" action="?" name="provider">

            <input type="text" name="name" size="50">

            <input type="submit" name="new" value="Add">

            </form>

        </div>  -->

<?php

if(count($errors)) {

?>

        <div class="row">

            <?=AlertUtil::generate($errors, 'error', FALSE)?>

        </div>

<?php

}

if(count($info)) {

?>

        <div class="row">

            <?=AlertUtil::generate($info, 'info', FALSE)?>

        </div>

<?php

}

?>

    </div>

    <table class="table table-bordered table-condensed table-striped">

        <thead>

            <tr>

                <th>Name</th>
                <th>Phone No</th>
                <th>Fax No</th>
                <th>Email</th>
                <th>Comment</th>

                <th width="10%" class="text-center">Actions</th>

            </tr>

        </thead>

        <tbody>

<?php

$providers = InsuranceModel::getAllProviders();

foreach($providers as $provider) {

?>

                <tr>

                    <td><?=$provider['insurance']?></td>
                    <td ><?=UIUtil::formatPhone($provider['phone_no'])?></td>
                    <td><?=UIUtil::formatPhone($provider['fax_no'])?></td>
                    <td><?=$provider['email']?></td>
                    <td><?=$provider['commment']?></td>

                    <td class="text-center">
                        
                        <a href="" class="btn btn-small btn-blue" rel="open-modal" data-script="add_edit_providers_notes.php?id=<?=$provider['insurance_id']?>" title="Carrier note for '<?=$provider['insurance']?>'" tooltip>

                            <i class="icon-comments-alt"></i>

                        </a> 
                        
                        <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/providers.php?id=<?=$provider['insurance_id']?>&action=del" data-confirm="Are you sure you want to remove insurance provider '<?=$provider['insurance']?>'?" title="Delete '<?=$provider['insurance']?>'" tooltip>

                            <i class="icon-trash"></i>

                        </a>

          <a href="" class="btn btn-small" rel="open-modal" data-script="add_edit_providers.php?id=<?=$provider['insurance_id']?>" title="Edit '<?=$provider['insurance']?>'" tooltip>

                            <i class="icon-pencil"></i>

                        </a> 

                    </td>

                </tr>

<?php

  $i++;

}

?>

        </tbody>

    </table>

</div>

</body>

</html>

