<?php

include 'includes/common_lib.php';

UserModel::isAuthenticated();

echo ViewUtil::loadView('doc-head');

if(!ModuleUtil::checkIsFounder()) {

    UIUtil::showListError('You do not have permission to access this.');

}



$id = RequestUtil::get('id');
$provider = DBUtil::getRecord('contacts');

$action = RequestUtil::get('action');

$name = RequestUtil::get('name');



$errors = array();

$info = array();

if (RequestUtil::get('new')) {

    if (!$name) {

        $errors[] = 'Header Name cannot be blank';

    }



    if (!count($errors)) {

        $sql = "INSERT INTO contacts (account_id, contact_name)

                VALUES ('{$_SESSION['ao_accountid']}', '$name')";

        DBUtil::query($sql);

        $info[] = "'$name' successfully added";

    }

} else if ($action === 'del' && $id) {

    $job = DBUtil::getRecord('job_contacts', $id, 'contact_header_id');

    if (count($job)) {

        $errors[] = 'Jobs currently associated - cannot remove';

    }

    

    if (!count($errors)) {

        $sql = "DELETE FROM contacts

                WHERE contact_header_id = '$id'

                    AND account_id = '{$_SESSION['ao_accountid']}'

                LIMIT 1";

        DBUtil::query($sql);

        $info[] = "Contact header successfully removed";

    }

}



?>



<h1 class="page-title"><i class="icon-cogs"></i><a href="/system.php">System</a> - Contact Header</h1>

<div class="btn-group pull-right page-menu">

    <div rel="open-modal" data-script="add_edit_contact_header.php" class="btn btn-success" title="Add Contact Header" tooltip>

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

                <th># ID</th>
                <th>Contact Type</th>
                <th width="20%" class="text-center">Actions</th>

            </tr>

        </thead>

        <tbody>

<?php

$contacts_header = ContactHeaderModel::getAllContactHeaders();

//echo "<pre>";print_r($contacts_header);die;
$count = 1;
foreach($contacts_header as $row) {

?>

                <tr>

                    <td><?=$count++?></td>
                    <td><?=$row['contact_name']?></td>

                    <td class="text-center">

                        <a href="" class="btn btn-small btn-danger" rel="change-window-location" data-url="<?=ROOT_DIR?>/contact-header-list.php?id=<?=$row['contact_header_id']?>&action=del" data-confirm="Are you sure you want to remove contact header '<?=$row['contact_name']?>'?" title="Delete '<?=$row['contact_name']?>'" tooltip>

                            <i class="icon-trash"></i>

                        </a>

                        <a href="" class="btn btn-small" rel="open-modal" data-script="add_edit_contact_header.php?id=<?=$row['contact_header_id']?>" title="Edit '<?=$row['contact_name']?>'" tooltip>

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

