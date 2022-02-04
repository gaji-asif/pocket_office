<?php

$firstLast = UIUtil::getFirstLast();

//print_r(get_class($myJob));

if(empty($myJob) || get_class($myJob) !== 'Job') { return; }



$j_id=$myJob->job_id;

?>

<tr class="job-tab-content email" <?=@$show_content_style?>>

    <td colspan="11">

        <div>

            <ul class="nav nav-tabs" >



                <li id="list_tab" class="active">



                    <a class="btn" href="javascript:void(0);">Email List</a>



                </li>



                <li id="add_tab" rel="open-modal" data-script="send_email.php?id=&action=send&job_id=<?=$j_id?>">



                    <a title="Send Email" class="btn btn-success" href="javascript:void(0);" > Send Email </a>



                </li>



            </ul>

        </div>

        <div class="clearfix" style="padding:15px;">



            <div id="list_meas" style="min-height:30px;">



                <?php

                    $sql = "select * from job_email where job_id = ".$j_id." AND is_deleted = 1 order by created_at desc";

                    $result = DBUtil::queryToArray($sql);

                    foreach($result as $emails) {

                        $viewData = array(

                            'myJob' => $myJob,

                            'emails' => $emails

                        );

                        echo ViewUtil::loadView('email', $viewData);

                    }

                ?>

            </div>

        </div>

    </td>

</tr>

<script>

    <?php

        if(AccountModel::getMetaValue('allow_mentions')) {

    ?>

            UI.atWho($('[rel="mention"]'));

    <?php

        }

    ?>



    jQuery(document).on('click', '.icon-remove', function() {

        jQuery(this).parent().remove();

    });





    

</script>

