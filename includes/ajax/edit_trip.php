<?php

include '../common_lib.php';

echo ViewUtil::loadView('doc-head');



$mes_id = RequestUtil::get('id');

$action = RequestUtil::get('action');

$job_ids = RequestUtil::get('job_id');



if($action=='edit')

{

  $sql = "select * from measurment where mes_id = '$mes_id' limit 1";

  $res = DBUtil::query($sql);



  if(!mysqli_num_rows($res)) {

      UIUtil::showModalError('Trip not found!');

  }



  list($mes_id, $job_id, $trip_date, $start_time, $end_time, $trip_area, $order_no, $g_map_url, $client_name, $client_address, $report_status,$top_image,$east_image,$west_image,$north_image,$south_image,$street_image)=mysqli_fetch_row($res);



  if(isset($_POST['trip_date']))

  {

    $error=0;



    if($error==0)

    {

      $trip_date = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['trip_date']);

      $start_time = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['start_time']);

      $end_time = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['end_time']);

      $trip_area = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['trip_area']);

      $order_no = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['order_no']);



      $gmap_url = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['gmap_url']);

      $client_name = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['client_name']);

      $client_address = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['client_address']);

      $status = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['status']);



      if(isset($_FILES))

      {

          $img=uploadImage();    

          if(!empty($img['pic1']))

            $top_image=$img['pic1'];

          if(!empty($img['pic2']))

            $east_image=$img['pic2'];

          if(!empty($img['pic3']))

            $west_image=$img['pic3'];

          if(!empty($img['pic4']))

            $north_image=$img['pic4'];

          if(!empty($img['pic5']))

            $south_image=$img['pic5'];

          if(!empty($img['pic6']))

            $street_image=$img['pic6'];

      }

      $sql = "update measurment set trip_date='".$trip_date."', start_time='".$start_time."', end_time='".$end_time."', trip_area='".$trip_area."', order_no='".$order_no."', g_map_url='".$gmap_url."', client_name='".$client_name."', client_address='".$client_address."', report_status='".$status."', top_image='".$top_image."',east_image='".$east_image."',west_image='".$west_image."',north_image='".$north_image."',south_image='".$south_image."',street_image='".$street_image."' where mes_id=".$mes_id." limit 1";

      

      DBUtil::query($sql);



  ?>



    <script>



      Request.makeModal('<?=AJAX_DIR?>/get_trip.php', 'suppliers-list', true, true, true);



    </script>

  <?php

    }

    else

    {



    }

  }





  $sql = "select * from measurment where mes_id = '$mes_id' limit 1";

  $res = DBUtil::query($sql);



  if(mysqli_num_rows($res)==0)

    die("Invalid Content");



  list($mes_id, $job_id, $trip_date, $start_time, $end_time, $trip_area, $order_no, $g_map_url, $client_name, $client_address, $report_status,$top_image,$east_image,$west_image,$north_image,$south_image,$street_image)=mysqli_fetch_row($res);

}

else if($action=='add')

{

  if(isset($_POST['trip_date']))

  {

    $error=0;    



    if($error==0)

    {

      $trip_date = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['trip_date']);

      $start_time = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['start_time']);

      $end_time = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['end_time']);

      $trip_area = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['trip_area']);

      $order_no = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['order_no']);



      $gmap_url = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['gmap_url']);

      $client_name = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['client_name']);

      $client_address = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['client_address']);

      $status = mysqli_real_escape_string(DBUtil::Dbcont(),$_POST['status']);



      if(isset($_FILES))

      {

          $img=uploadImage();      

          $top_image=$img['pic1'];

          $east_image=$img['pic2'];

          $west_image=$img['pic3'];

          $north_image=$img['pic4'];

          $south_image=$img['pic5'];

          $street_image=$img['pic6'];



      }



      $sql = "insert into measurment (job_id,trip_date,start_time,end_time,trip_area,order_no,g_map_url,client_name,client_address,report_status,top_image,east_image,west_image,north_image,south_image,street_image)  VALUES ('$job_ids','$trip_date','$start_time','$end_time','$trip_area','$order_no','$gmap_url','$client_name','$client_address','$status','$top_image','$east_image','$west_image','$north_image','$south_image','$street_image')";

   

      DBUtil::query($sql);



  ?>



    <script>



      Request.makeModal('<?=AJAX_DIR?>/get_trip.php', 'suppliers-list', true, true, true);



    </script>

  <?php

    }

    else

    {



    }

  }

}



function uploadImage()

{

    $img_arr=array();

    for($i=1;$i<=count($_FILES);$i++)

    {

      $field='pic'.$i;

      $new_filename = mt_rand().time().".".$_FILES[$field]['name'];     

      $new_path =   ROOT_PATH.'/uploads/measurment/'. $new_filename;   

      

      

      if(isset($_FILES[$field]) && $_FILES[$field]['name']!='')

      {

        if(move_uploaded_file($_FILES[$field]["tmp_name"], $new_path))

        {

          $img_arr[$field]=$new_filename;

        }  

        else{

          $img_arr[$field]='';

        }

      }

      else{

          $img_arr[$field]='';

        }



    }      

   

    return $img_arr;

}



$image_link='noimagefound.jpg';



$img_dir=ROOT_DIR.'/uploads/measurment/';



if(empty($top_image))

  $top_image=$image_link;

if(empty($east_image))

  $east_image=$image_link;

if(empty($west_image))

  $west_image=$image_link;

if(empty($north_image))

  $north_image=$image_link;

if(empty($south_image))

  $south_image=$image_link;

if(empty($street_image))

  $street_image=$image_link;





?>

    <table border="0" cellspacing="0" cellpadding="0" width="100%" align="center">

      <tr>

        <td>

          <table class="data-table-header" cellpadding="0" cellspacing="0" border="0">

            <tr valign="center">

              <td>

                Edit Measurement

              </td>

              <td align="right">

              <i class="icon-remove grey btn-close-modal"></i>

              </td>

            </tr>

          </table>

        </td>

      </tr>

      <tr id="add_meas">

        <td class="infocontainernopadding">

          <table width="100%" border="0" cellspacing="0" cellpadding="0">

<?php



$query_str='id='.$mes_id.'&action='.$action;



if($error==1)

{

?>

            <tr>

              <td colspan=2 style='color: red; font-size: 11px;' class="listrownoborder">

                <b>Errors Found!</b>

                <?php echo $error_msg; ?>

              </td>

            </tr>

<?php

}

?>

          <tr>

            <td>

              <form method="post" name='edit_trip' action='?<?php echo $query_str;?>' enctype="multipart/form-data">

                <table border="0" width="100%" cellspacing="0" cellpadding="0">

                    <tr> 

                    <td>&nbsp;

                    </td> 

                    </tr>

                    <tr>                  

                    <input  type="hidden" name="job_id" value="<?php if(isset($job_ids)) echo $job_ids; ?>">

                    <td class="listitem" width="125" ><b>Trip Date:</b></td>

                    <td class="listitemnoborder">

                        <input class="pikaday" type="text" name="trip_date" value="<?php if(isset($trip_date)) echo $trip_date; ?>">

                    </td>



                    <td class="listitem" width="125" ><b>Start Time:</b></td>

                    <td class="listitemnoborder">

                        <input type="text" name="start_time" value="<?php if(isset($start_time)) echo $start_time; ?>">

                    </td>



                    <td class="listitem" width="125"><b>End Time:</b></td>

                    <td class="listitemnoborder">

                        <input type="text" name="end_time" value="<?php if(isset($end_time)) echo $end_time; ?>">

                    </td>



                  </tr>                  

                  

                  <tr>

                    <td class="listitem"width="125"><b>Trip Area:</b></td>

                    <td class="listitemnoborder">

                        <input type="text" name="trip_area" value="<?php if(isset($trip_area)) echo $trip_area; ?>">

                    </td>



                    <td class="listitem"width="125"><b>Order No:</b></td>

                    <td class="listitemnoborder">

                        <input type="text" name="order_no" value="<?php if(isset($order_no)) echo $order_no; ?>">

                    </td>



                    <td class="listitem"width="125"><b>G Map Url:</b></td>

                    <td class="listitemnoborder">

                        <input type="text" name="gmap_url" value="<?php if(isset($g_map_url)) echo $g_map_url; ?>">

                    </td>                   

                  </tr>

               



                  <tr>

                    <td class="listitem"width="125"><b>Client Name:</b></td>

                    <td class="listitemnoborder">

                        <input type="text" name="client_name" value="<?php if(isset($client_name)) echo $client_name; ?>">

                    </td>



                    <td class="listitem"width="125"><b>Client Address:</b></td>

                    <td class="listitemnoborder">

                        <input type="text" name="client_address" value="<?php if(isset($client_address)) echo $client_address; ?>">

                    </td>



                    <td class="listitem"width="125"><b>Status:</b></td>

                    <td class="listitemnoborder">

                        <select name="status">

                            <option <?php if(isset($report_status) && $report_status=='Report Not Generated') echo 'selected';?> value="Report Not Generated">Report Not Generated</option>

                            <option <?php if(isset($report_status) && $report_status=='Report Generated') echo 'selected';?> value="Report Generated">Report Generated</option>

                            <option <?php if(isset($report_status) && $report_status=='On Hold') echo 'selected';?> value="On Hold">On Hold</option>

                        </select>

                        

                    </td>

                  </tr>



                  <tr>

              <td  colspan="6" class="listitemnoborder row_class">

     

                <div class="upload_part">

                 <h2> Upload Image:</h2>

                <div class="upload-img-block">

                    <div class="upload_img">

                      <img id="pic1" src="<?php echo $img_dir.$top_image;?>" alt="">

                    </div>

                    <input type="file" name="pic1" onchange="readURL(this);" accept="image/*">

                    <p>Top perpendicular view</p>

                </div>

                <div class="upload-img-block">

                    <div class="upload_img">

                        <img id="pic2" src="<?php echo $img_dir.$east_image;?>" alt="">

                    </div>

                    <input type="file" name="pic2" onchange="readURL(this);" accept="image/*">

                    <p>East view</p>

                </div>

                <div class="upload-img-block">

                    <div class="upload_img">

                        <img id="pic3" src="<?php echo $img_dir.$west_image;?>" alt="">

                    </div>

                    <input type="file" name="pic3" onchange="readURL(this);" accept="image/*">

                    <p>West View</p>

                </div>

                <div class="upload-img-block">

                    <div class="upload_img">

                        <img id="pic4" src="<?php echo $img_dir.$north_image;?>" alt="">

                    </div>

                    <input type="file" name="pic4" onchange="readURL(this);" accept="image/*">

                     <p>North View</p>

                </div>

                <div class="upload-img-block">

                    <div class="upload_img">

                        <img id="pic5" src="<?php echo $img_dir.$south_image;?>" alt="">

                    </div>

                    <input type="file" name="pic5" onchange="readURL(this);" accept="image/*">

                     <p>South View</p>

                </div>

                <div class="upload-img-block">

                    <div class="upload_img">

                        <img id="pic6" src="<?php echo $img_dir.$street_image;?>" alt="">

                    </div>

                    <input type="file" name="pic6" onchange="readURL(this);" accept="image/*">

                     <p>Street View</p>

                </div>

                </div>

              </td>                              

            </tr>





                  

                </table>

             

              </td>

            </tr>

          </table>

          <table border="0" width="100%" cellpadding="0" cellspacing="0">

            <tr>

              <td align="right" class="listrow">

                <?php if($action=='edit') {?>

                  <input type="button" value="Delete" onclick='if(confirm("Are you sure?")){window.location="edit_trip.php?id=<?php echo $mes_id; ?>&action=del";}'>

                <?php }?>



                  <input type="submit" value="Save">

                

              </td>

            </tr>

          </table>



          </form>



        </td>

      </tr>

    </table>

  </body>

</html>



<script type="text/javascript">



  function readURL(input) 

  {

        console.log(input.name);

        if (input.files && input.files[0]) {

            var reader = new FileReader();



            reader.onload = function (e) {

                $('#'+input.name)

                    .attr('src', e.target.result)

                    .width(150)

                    .height(150);

            };



            reader.readAsDataURL(input.files[0]);

        }

    }



    </script>