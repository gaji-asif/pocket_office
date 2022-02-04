<?php

if(empty($myJob) || get_class($myJob) !== 'Job') { return; }



//echo "<pre>";print_r($myJob);exit;

$j_id=$myJob->job_id;

?>



<tr class="job-tab-content contacts" <?=@$show_content_style?>>

    <td colspan=11>

        <div>

            <ul class="nav nav-tabs" >

                <li id="list_tab" class="active">

                    <a href="javascript:void(0);">Contacts List</a>

                </li>

                <li id="add_tab" rel="open-modal" data-script="edit_contact.php?id=&action=add&job_id=<?=$j_id?>">

                    <a title="Add Contact Note" class="btn btn-success" href="javascript:void(0);" > <i class="icon-plus"></i> </a>

                </li>

            </ul>

            <div class="clearfix" style="padding:15px;">

                <div id="list_meas" style="min-height:30px;">

                    <table class="table-bordered table-condensed table-hover table-padded table-striped"  width="100%">

                    <thead>

                        <tr>

                            <th data-sort="string">#Id</th>

                            <th data-sort="string">Contact Header</th>

                            <th data-sort="string">Contact Note</th>
                            
                            <th data-sort="string">Sender</th>

                            <th data-sort="string">Contact Time</th>

                        </tr>

                    </thead>

                    <tbody id="contacts-list">



                        <?php

                        $contacts=UserModel::getContactsList($myJob->job_id);
                        

                        $i=0;

                        foreach($contacts as $row) {

                            $i++;

                        ?>

                            <tr title="Click to edit contact note" rel="open-modal" data-script="edit_contact.php?id=<?=$row['job_contact_id']?>&action=edit&job_id=<?=$j_id?>">

                                <td><?=$i?></td>

                                <td><?=$row['contact_header']?></td>

                                <td><?=prepareText($row['contact_note'])?></td>
                                
                                <td><?=$row['fname'].' '.$row['lname']?></td>

                                <td><?=date('M d,Y @ h:i A',strtotime($row['created_at']))?></td>

                            </tr>

                        <?php }

                        ?>



                    </tbody>

                    </table>



                </div>

                

            </div>

        </div>        

        

    </td>

</tr>



