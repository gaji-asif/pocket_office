<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<tr class="job-tab-content uploads" <?=@$show_content_style?>>
    <td colspan=11>
        <div>
            <ul class="nav nav-tabs">
                <li id="photo_tab" class="active"><a href="javascript:void(0);" onclick="showPhotos();">Photos</a></li>
                <li id="docs_tab"><a href="javascript:void(0);" onclick="showDocs();">Documents</a></li>
                <li ><input id="select-all-check" type="checkbox" onclick="checkuncheck()"> Select All</li>
            </ul>
            <div class="clearfix">
                <div id="image" style="min-height:30px;">
                    <script>
			            $(function() {
                          $("a.fancy").fancybox();
                          
                          /*afterLoad': function() 
                           { 
                            var angle = 0; 
                             $(".fancybox-slide").append('<button style="float:right;" id="button">Rotate</button>');
                            $("#button").click(function(){
                               // alert(1);
                                angle = (angle+90)%360;
                                className = "rotate"+angle;;
                            $(".fancybox-image").attr("id",'');
                            $(".fancybox-image").attr("id",className);
                            });  
                           }*/
                          }); 
                        </script>
<script>
function checkuncheck()
 {  
      
      if($("#select-all-check").prop("checked") == true){
           $(".dowimg").prop("checked", true);
      }
      else
      {
           $(".dowimg").prop("checked", false);
      }
                            
 }
   function createzip()
    { 
        $(".dowzip").hide();
        var arr=[];
        var i =0;
        if ($('.dowimg').is(':checked')) {
            $( '.dowimg').each(function() {
                if ($(this).is(':checked'))
                {
                 arr[i++]=$(this).val();
                }
            });
            var jsonString = JSON.stringify(arr);
               $.ajax({
                    type: "POST",
                    url: "<?=AJAX_DIR?>/create_zip.php",
                    data: {data : jsonString}, 
                    cache: false,
                    success: function(r){
                     if(r!='error'){
                        //$(".dowzip").show();
                       // $(".dowzip").attr("href",r);
                        $('.dowimg').prop('checked', false);
                        window.open(r, '_blank');
                        }
                        else
                        {
                        alert('Error!!!');
                        }
                        //alert(r);
                    }
                });
        }
        else
        {
         alert('Please select at least a record!!!');
        }
    }
		</script>
                    <?php
                    $count = 0;
                    $img_ext=array('jpg','png','gif','jpeg','bmp');
                    foreach($myJob->uploads_array as $upload_id => $upload)
                    {    
                        $ext_arr=explode('.',$upload['filename']);               
                        $ext=end($ext_arr);                 
                        if(in_array(strtolower($ext),$img_ext))
                        {
                            $view_data = array(
                                'upload_id' => $upload_id,
                                'upload' => $upload,
                                'myJob' => $myJob,
                            );
                            echo ViewUtil::loadView('job-upload-container', $view_data);                    
                            $count++;
                        }
                    }
                   
                    ?>
                  <div class="clearfix"></div>
                  <div class="button-cont clearfix"> 
                  <a onclick="createzip()" href="javascript:void(0)" class="genzip btn btn-blue btn-block">Download Zip </a>
                  <!--<a target="_blank" style="display:none" class="dowzip btn btn-blue btn-block">Click Here To Download</a>-->
                  </div>
                </div>

                <div id="docs" style="min-height:30px;">
                    <?php
                    $count = 0;
                    foreach($myJob->uploads_array as $upload_id => $upload)
                    {
                        $ext_arr=explode('.',$upload['filename']);           
                        $ext=end($ext_arr);             
                        if(!in_array(strtolower($ext),$img_ext))
                        {
                            $view_data = array(
                                'upload_id' => $upload_id,
                                'upload' => $upload,
                                'myJob' => $myJob,
                            );
                            echo ViewUtil::loadView('job-upload-container', $view_data);                
                            $count++;
                        }
                    }
                    
                    ?>
                </div>
            </div>
        </div>
        <div style="border:1px dotted;" title="Drag file and drop here!">
            <div class="dropzone-message alert alert-info">
                <strong>Heads up!</strong> You can drag files directly below to automatically upload and save them to this job.
            </div>
            <div  id="dropzone" class="clearfix" data-itemtype="job_file" data-itemid="<?=$myJob->job_id?>">
               <!-- Drag and drop file here-->
            </div>
        </div>
    </td>
</tr>

<script type="text/javascript">
$("#docs").hide();
function showPhotos()
{
    $("#image").show();
    $("#docs").hide();
    $("#photo_tab").addClass('active');
    $("#docs_tab").removeClass('active');
}
function showDocs()
{
    $("#image").hide();
    $("#docs").show();
    $("#photo_tab").removeClass('active');
    $("#docs_tab").addClass('active');
}


</script>