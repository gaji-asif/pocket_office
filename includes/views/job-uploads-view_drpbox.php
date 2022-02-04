<?php
require '../drpbox/vendor/autoload.php';
//$dropbox = new Dropbox\Dropbox('eq2LBxiD9LAAAAAAAAILaQjzqm7kYs0781azSHnI57xaKfK8xm4SXRiI71uIY0us');
//$list_data = $dropbox->files->list_folder('/xactbidjobfiles');

$dropbox = new Dropbox\Dropbox('eq2LBxiD9LAAAAAAAAILaQjzqm7kYs0781azSHnI57xaKfK8xm4SXRiI71uIY0us');
$sqlj = "select * from jobs where job_id = '".$myJob->job_id."'";
$resultsj = DBUtil::query($sqlj);
$jobs = mysqli_fetch_array($resultsj);

$sqlfolc = "select * from users where user_id = '".$jobs['salesman']."'";
$resultsc = DBUtil::query($sqlfolc);
$customer = mysqli_fetch_array($resultsc);
$folder = $customer['fname'].'_'.$customer['lname'];

$sqlfol = "select * from customers where customer_id = '".$jobs['customer_id']."'";
$results = DBUtil::query($sqlfol);
$user = mysqli_fetch_array($results);
$cuser = $user['lname'].'_'.$user['fname'];

$list_folder = $dropbox->files->list_folder('/xactbid/'.$folder.'/'.$cuser.'/');

if(empty($myJob) || get_class($myJob) !== 'Job') { return; }
?>

<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
<style>
.nav-tabs > li {position:relative}
.nav-tabs > li > a.drop-box {padding-right:40px}
.nav-tabs > li > a.plus-icon{position: absolute; right: 0px; top: 2px; border: 0; background: transparent; width: 14px; height: 17px; border-radius: 100%;  background: #000;    display: inline-block;    width: 16px;    height: 16px;    padding: 0;    color: #fff;    font-size: 17px;    text-align: center;    line-height: 19px;}
.modal-wrap1{}
.modal-wrap1 .modal-header{border-bottom:0; padding-bottom:0}
.modal-wrap1 .form-control{min-height:46px; box-shadow:none; border:#ccc 1px solid; margin-bottom:10px}
.modal-wrap1 .form-control:hover, .modal-wrap1 .form-control:focus{box-shadow:none; border-color:#ccc}
.modal-wrap1 .btn{padding-top:12px; padding-bottom:12px; font-size:18px}
.modal-wrap1 .btn-primary{background:#4d90fe; border:0}
.modal-wrap1 .btn-primary:hover{background:#347df3}
.modal-wrap1 .modal-body{padding-bottom:25px}

</style>
<tr class="job-tab-content uploads" <?=@$show_content_style?>>
    <td colspan=11>
        <div>
            <ul class="nav nav-tabs">
                <!--<li id="photo_tab" class="active"><a href="javascript:void(0);" onclick="showPhotos();">Photos</a></li>
                <li id="docs_tab"><a href="javascript:void(0);" onclick="showDocs();">Documents</a></li>-->
				<li id="drop_tab"><a href="javascript:void(0);" onclick="showDrop();">DropBox</a><a class="plus-icon" href="#" data-toggle="modal" data-target="#myModal">+</a></li>
				<li id="dropl_tab"><a href="javascript:void(0);" onclick="showdroplink();">DropBox Links</a></li>
				<li id="drive_tab"><a href="javascript:void(0);" onclick="showdrive();">Google Drive</a></li>	
            </ul>
            <div class="clearfix">
                <!--<div id="image" style="min-height:30px;">
                    <?php
                    /*$count = 0;
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
                   */
                    ?>
                </div>-->

                <!--<div id="docs" style="min-height:30px;">
                    <?php
                    /*$count = 0;
					$img_ext=array('jpg','png','gif','jpeg','bmp');
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
                    */
                    ?>
                </div>-->
				<div id="drop" style="min-height:30px;">
                    <?php
					//print_r($list_data);
                    $count = 0;
                    foreach($list_folder['entries'] as $upload_id => $upload)
                    {
                        /*$ext_arr=explode('.',$upload['filename']);           
                        $ext=end($ext_arr);             
                        if(!in_array(strtolower($ext),$img_ext))
                        {*/
                            $view_data = array(
                                'upload_id' => $upload_id,
                                'upload' => $upload,
                                'myJob' => $myJob,
                            );
                            echo ViewUtil::loadView('job-upload-container', $view_data);                
                           /* $count++;
                        }*/
						
                    }
                    
                    ?>
                </div>
				<div id="droplink" style="min-height:30px;">
                    <?php
					$sqldrp = "select * from dropbox where job_id = '".$myJob->job_id."'";
					$resultsdrp = DBUtil::query($sqldrp);
					
                    while($rowdrp = mysqli_fetch_array($resultsdrp))
                    {
                        /*$ext_arr=explode('.',$upload['filename']);           
                        $ext=end($ext_arr);             
                        if(!in_array(strtolower($ext),$img_ext))
                        {*/
                            $view_data = array(
                                'drp_id' => $rowdrp['drop_id'],
                                'links' => $rowdrp['link'],
                            );
                            echo ViewUtil::loadView('job-upload-container-list', $view_data);                
                           /* $count++;
                        }*/
						
                    }
                    
                    ?>
                </div>
				<div id="drive" style="min-height:30px;">
				<div class="dropzone-message alert alert-info">
                    <a href="javascript:void(0)" onclick="window.open('<?php echo 'https://'.$_SERVER['HTTP_HOST']?>/drive/google-drive.html','','width=1200,height=800,scrollbar=yes')">Open Google Drive</a>
					</div>
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
<form method="post">
<div class="modal fade" id="myModal" role="dialog">

    <div class="modal-dialog">
    
      <!-- Modal content-->
      <div class="modal-content">
	    <div class="modal-wrap1">
        <div class="modal-header">
          <button type="button" class="close" data-dismiss="modal">&times;</button>
          <h4 class="modal-title">Dropbox Link</h4>
        </div>
		
        <div class="modal-body">
		<input type="hidden" name="jid" id="jid" value="<?=$myJob->job_id?>" />
         <input type="text" class="form-control" id="links" name="link" placeholder="Add Link">
		 <input type="button" class="btn btn-primary btn-block" id="btn1" value="Submit">
        </div>
        
      </div>
     </div> 
    </div>

  </div>
</form>
<script type="text/javascript">
$(document).ready(function() {
	$("#btn1").click(function(){
		var jid = $('#jid').val();
		var links = $('#links').val();
		$.ajax({
		  type: "POST",
		  url: "<?php echo AJAX_DIR?>/update_dropbox.php",
		  data: {jid : jid,links : links},
		  cache: false,
		  success: function(data){
			 //alert(data);
			 if(data == 'success') {
			 	//$("#myModal").modal('hide');
				//location.reload();
			}
			else
			{
			 	//alert(data);
		  	}
		}
		});
	});
});

$("#droplink").hide();
$("#drive").hide();
function showPhotos()
{
    $("#image").show();
    $("#docs").hide();
	$("#drop").hide();
    $("#photo_tab").addClass('active');
    $("#docs_tab").removeClass('active');
	$("#drop_tab").removeClass('active');
}
function showDocs()
{
    $("#image").hide();
    $("#docs").show();
	$("#drop").hide();
    $("#photo_tab").removeClass('active');
    $("#docs_tab").addClass('active');
	$("#drop_tab").removeClass('active');
}
function showDrop()
{
    //$("#image").hide();
    //$("#docs").hide();
	$("#drop").show();
	$("#droplink").hide();
	$("#drive").hide();
    //$("#photo_tab").removeClass('active');
    //$("#docs_tab").removeClass('active');
	$("#drop_tab").addClass('active');
	$("#dropl_tab").removeClass('active');
	$("#drive_tab").removeClass('active');
}
function showdroplink()
{
    //$("#image").hide();
    //$("#docs").hide();
	$("#drop").hide();
	$("#droplink").show();
	$("#drive").hide();
    //$("#photo_tab").removeClass('active');
    //$("#docs_tab").removeClass('active');
	$("#drop_tab").removeClass('active');
	$("#dropl_tab").addClass('active');
	$("#drive_tab").removeClass('active');
}
function showdrive()
{
	
	$("#drop").hide();
	$("#droplink").hide();
	$("#drive").show();
    //$("#photo_tab").removeClass('active');
    //$("#docs_tab").removeClass('active');
	$("#drop_tab").removeClass('active');
	$("#dropl_tab").removeClass('active');
	$("#drive_tab").addClass('active');
}
</script>