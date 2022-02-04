



<div class="journal-container hover-button-container clearfix" id="email-<?=MapUtil::get(@$emails, 'email_id')?>">

    <ul class="journal-info">

        <!-- <li><?=UserUtil::getDisplayName(@$emails['created_by'])?></li> -->

        <li><?=MapUtil::get(@$emails, 'from_name')?> <!-- (<?=MapUtil::get(@$emails, 'from_email')?>) --></li>

        <li><?=DateUtil::formatDate(@$emails['created_at'])?>&nbsp;@&nbsp;<?=DateUtil::formatTime(@$emails['timestamp'])?></li>

    </ul>

    <div class="journal-copy" style="font-style: normal;font-size: 12px;">

        <style type="text/css">

            .journal-copy p{

                margin: 0 0 5px !important;

            }

        </style>

        <?php

            $journalText = StrUtil::convertMentionsToLinks(@$emails['text']);

        ?>

        <h5  style="margin: 0;padding: 0;"><b>Email</b></h5>

        <p>Status: <?= $emails['email_type'] == 1 ? 'Send' : $emails['email_type'] ==2 ? 'Replay' : $emails['email_type'] == 3 ? 'Forward' : ''; ?></p>

        <p><b><?=MapUtil::get(@$emails, 'email_subject')?></b></p>

        <p><?=MapUtil::get(@$emails, 'email_note')?></p>

        <p>Email sent to: <?=MapUtil::get(@$emails, 'email_send_to')?></p>

        <p>

            <?php 

                if (!empty($emails['email_files'])) {

                    

                    $files = explode(',', $emails['email_files']);

                    foreach ($files as $key => $value) {



                        $pieces = explode('.', $value);

                        $extension = end($pieces);
                        $ipattach = substr($value, strrpos($value, '/') + 1);
                        echo '<a href="'.$value.'" target="_blanck" style="display: block;
                                margin: 5px;">
                                <img src="'.IMAGES_DIR.'/icons/660400.svg" width="20" height="20" style="    margin-right: 10px;">
                                '.$ipattach.'
                            </a>';
                        // if ($extension=='png' || $extension=='jpg' || $extension=='jpeg' || $extension=='gif' || $extension=='tif') {



                        //     echo '<a href="'.EMAIL_ATTACHMENT_URL.$value.'" target="_blanck"><img src="'.EMAIL_ATTACHMENT_URL.$value.'" width="25" height="25">'.$value.'</a>';

                        // } else {

                        //     echo '<iframe class="" src="https://docs.google.com/viewer?url='.EMAIL_ATTACHMENT_URL.$value.'&embedded=true" frameborder="0" scrolling="no" width="225" height="142"></iframe>';

                        // }

                    }

                }

            ?>

        </p>

    </div>

<?php

    if(ModuleUtil::checkJobModuleAccess('delete_journals', $myJob)) {

?>

        <div class="btn btn-danger btn-small" rel="delete-email" data-email-id="<?=MapUtil::get(@$emails, 'email_id')?>" title="Delete Email" tooltip>

            <i class="icon-remove"></i>

        </div>

<?php

    }

?>

</div>