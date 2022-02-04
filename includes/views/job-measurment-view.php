<?php

if(empty($myJob) || get_class($myJob) !== 'Job') { return; }



//echo "<pre>";print_r($myJob);exit;

$j_id=$myJob->job_id;

?>



<tr class="job-tab-content measurment" <?=@$show_content_style?>>

    <td colspan=11>

        <div>

            <ul class="nav nav-tabs" >

                <li id="list_tab" class="active">

                    <a href="javascript:void(0);">Measurement List</a>

                </li>

                <li id="add_tab" rel="open-modal" data-script="edit_trip.php?id=&action=add&job_id=<?=$j_id?>">

                    <a class="btn btn-success" href="javascript:void(0);" > <i class="icon-plus"></i> </a>

                </li>

            </ul>

            <div class="clearfix" style="padding:15px;">

                <div id="list_meas" style="min-height:30px;">

                    <table class="table table-bordered table-condensed table-hover table-padded table-striped">

                    <thead>

                        <tr>

                            <th data-sort="string">#Id</th>

                            <th data-sort="string">Trip Date</th>

                            <th data-sort="string">Start Time</th>

                            <th data-sort="string">End Time</th>

                            <th data-sort="string">Trip Area</th>

                            <th data-sort="string">Order No </th>

                            <th data-sort="string">Client Name </th>

                            <th data-sort="string">Client Address</th>

                            <th data-sort="string">Status</th>

                        </tr>

                    </thead>

                    <tbody id="measurment-list">



                        <?php

                        $measurments=UserModel::getMeasurmentList($myJob->job_id);

                        $i=0;

                        foreach($measurments as $row) {

                            $i++;

                        ?>

                            <tr rel="open-modal" data-script="edit_trip.php?id=<?=$row['mes_id']?>&action=edit&job_id=<?=$j_id?>">

                                <td><?=$i?></td>

                                <td><?=$row['trip_date']?></td>

                                <td><?=$row['start_time']?></td>

                                <td><?=$row['end_time']?></td>

                                <td><?=$row['trip_area']?></td>

                                <td><?=$row['order_no']?></td>

                                <td><?=$row['client_name']?></td>

                                <td><?=$row['client_address']?></td>

                                <td><?=$row['report_status']?></td>

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



