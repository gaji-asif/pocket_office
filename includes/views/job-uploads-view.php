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
            <!--<ul class="nav nav-tabs">
                <li id="photo_tab" class="active comonlink"><a href="javascript:void(0);" onclick="showPhotos();">Photos</a></li>
                <li id="docs_tab"><a class="comonlink" href="javascript:void(0);" onclick="showDocs();">Documents</a></li>
                <li ><input id="select-all-check" type="checkbox" onclick="checkuncheck()"> Select All</li>
				<li id="drop_tab"><a class="comonlink" href="javascript:void(0);" onclick="showDrop();">DropBox</a><a class="plus-icon" href="#" data-toggle="modal" data-target="#myModal">+</a></li>
				<li id="dropl_tab"><a class="comonlink" href="javascript:void(0);" onclick="showdroplink();">DropBox Links</a></li>
				<li id="drive_tab"><a class="comonlink" href="javascript:void(0);" onclick="showdrive();">Google Drive</a></li>	
            </ul>-->
            <ul class="nav nav-tabs">
                <li id="photo_tab" class="active comonlink"><a href="javascript:void(0);" onclick="commonhideshow('image','photo_tab');">Photos</a></li>
                <li id="docs_tab" class="comonlink"><a  href="javascript:void(0);" onclick="commonhideshow('docs','docs_tab');">Documents</a></li>
                <li ><input id="select-all-check" type="checkbox" onclick="checkuncheck()"> Select All</li>
                <li id="drop_tab" class="comonlink"><a  href="javascript:void(0);" onclick="commonhideshow('drop','drop_tab');">DropBox</a><a class="plus-icon" href="#" onclick='fnchangetext("Dropbox Link")' data-toggle="modal" data-target="#myModal">+</a></li>
                <li id="drive_tab" class="comonlink"><a  href="javascript:void(0);" onclick="commonhideshow('drive','drive_tab');">Google Drive</a><a class="plus-icon" onclick='fnchangetext("Google Drive")' href="#" data-toggle="modal" data-target="#myModal">+</a></li>
                <li id="dropl_tab" class="comonlink"><a  href="javascript:void(0);" onclick="commonhideshow('droplink','dropl_tab');">Shared Links</a></li>
                
            </ul>
            <div class="clearfix">
                <div id="image" class="comondiv" style="min-height:30px;">
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
                            $('.dowimg').prop('checked', false);
                            window.open(r, '_blank');
                        }
                        else
                        {
                            alert('Error!!!');
                        }
                    }
                });
        }
        else
        {
         alert('Please select at least a record!!!');
        }
    }
    
    function createpdf()
    { 
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
            var job_id=$('#jid').val();
            $(this).attr('disabled',true)
               $.ajax({
                    type: "POST",
                    url: "<?=AJAX_DIR?>/create_pdf.php",
                    data: {job_id:job_id,data : jsonString}, 
                    cache: false,
                    success: function(r){
                        if(r!='error'){
                            $('.dowimg').prop('checked', false);
                            window.open(r, '_blank');
                        }
                        else
                        {
                            alert('Error!!!');
                        }
                        $(this).attr('disabled',false)
                    },
                    error: function(r){
                        $(this).attr('disabled',false)
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
                  <a onclick="createzip()" href="javascript:void(0)" class="genzip btn btn-blue btn-block" style="float: left;width: 200px;margin-left: 5px;">Download Zip </a>
                  <a onclick="createpdf()" href="javascript:void(0)" class="genpdf btn btn-blue btn-block"  style="float: left;width: 200px; margin-top: 0px">Download Pdf </a>
                  </div>
                </div>

                <div id="docs" class="comondiv" style="min-height:30px;display:none">
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
				<div id="drop" class="comondiv" style="min-height:30px;display:none">
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
				<div id="droplink" class="comondiv" style="min-height:30px;display:none">
                    <?php
					$sqldrp = "select * from dropbox where job_id = '".$myJob->job_id."'";
					$resultsdrp = DBUtil::query($sqldrp);
					
                    while($rowdrp = mysqli_fetch_array($resultsdrp))
                    {
                        /*$ext_arr=explode('.',$upload['filename']);           
                        $ext=end($ext_arr);             
                        if(!in_array(strtolower($ext),$img_ext))
                        {*/
                            $sqldrp_f = "select * from dropboxfiles where job_id = '".$myJob->job_id."' and ref_link = '".$rowdrp['link']."'";
                            $resultsdrp_fs = DBUtil::query($sqldrp_f);
                            $folders = array();
                            while($rowdr = mysqli_fetch_array($resultsdrp_fs)){
                                $folders[] = $rowdr;
                            }
                            $view_data = array(
                                'drp_id' => $rowdrp['drop_id'],
                                'links' => $rowdrp['link'],
                                'job_id' => $myJob->job_id,
                                'folders' => $folders,
                                'dropboxs' => $rowdrp,
                            );
                            echo ViewUtil::loadView('job-upload-container-list', $view_data);                
                           /* $count++;
                        }*/
						
                    }
                    
                    ?>
				</div>
				<div id="drive" class="comondiv" style="min-height:30px;display:none">
                <a href="javascript:void(0)" onclick="window.open('<?php echo 'https://'.$_SERVER['HTTP_HOST']?>/drive/google-drive.html','','width=1200,height=800,scrollbar=yes')" style="padding-left:5px;">Open Google Drive</a>
				<div class="dropzone-message alert alert-info">                   
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
          <h4 class="modal-title" id='sharedlinktext'>Dropbox Link</h4>
        </div>
		<div id='loadid' style='display:none;'></div>
        <div class="modal-body">
		<input type="hidden" name="jid" id="jid" value="<?=$myJob->job_id?>" />
         <input type="text" class="form-control" id="links" name="link" placeholder="Add Link">
		 <input type="button" class="btn btn-primary btn-block showbutton" id="btn1" value="Submit">
          <img src="images/loading14.gif" id='hideloading' style='width: 100%;display: none;'>
        </div>
        
      </div>
     </div> 
    </div>

  </div>
  
</form>

<script type="text/javascript">
function fndropboxgetcontent(links,jid){
    $.ajax({
          method: "post",
          url: "<?php echo AJAX_DIR?>/dropboxgetcontent.php",
          data: { link:links }
        })
      .done(function( msg ) {
            $('#loadid').html(msg);
            setTimeout(function(){ 
                href = showdat(links,jid);
                
            }, 3000);
            
      });
}
function showdat(links,jid){
    var href = '';
    $('ol li a').each(function(){
        href += $(this).attr('href')+'__';
    });
    setTimeout(function(){ 
                $.ajax({
                      type: "POST",
                      url: "<?php echo AJAX_DIR?>/update_dropbox_file.php",
                      data: {jid : jid,links : links,href:href},
                      cache: false,
                      success: function(data){
                         //location.reload();
                         //droplink
                         $.ajax({
                              type: "POST",
                              url: "<?php echo AJAX_DIR?>/dropbox_no_ref.php",
                              data: {jid : jid},
                              cache: false,
                              success: function(data){
                                 //location.reload();
                                 $('#droplink').html(data);
                                 $('#myModal').hide();
                                 $('#myModal').hide();
                                 $('#links').val('');
                                 $('.modal-backdrop').hide();
                                 $('#loadid').html('');
								 $('#hideloading').hide();
                                 $('.showbutton').show();
                                }
                            });
                        }
                    });
                
            }, 1000);
    
    //alert(href);
}
/*$("#docs").hide();
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
}*/

$(document).ready(function() {
    
 
 
	$("#btn1").click(function(){
	  
		var jid = $('#jid').val();
		var links = $('#links').val();
		if(links==''){
		  alert('Please enter link');
		  return false;
		}
		$('#hideloading').show();
		$('.showbutton').hide();
		$.ajax({
		  type: "POST",
		  url: "<?php echo AJAX_DIR?>/update_dropbox.php",
		  data: {jid : jid,links : links},
		  cache: false,
		  success: function(data){
			 //alert(data);
			if(data == 'success') {
                var str = links;
                var n = str.search("www.dropbox.com");
                if(n >=1){
                    var href = fndropboxgetcontent(links,jid);    
                }else{
                    $.ajax({
                      type: "POST",
                      url: "<?php echo AJAX_DIR?>/dropbox_no_ref.php",
                      data: {jid : jid},
                      cache: false,
                      success: function(data){
                         //location.reload();
                         $('#droplink').html(data);
                         
                         $('#myModal').hide();
                         $('#links').val('');
                         $('.modal-backdrop').hide();
                         $('#hideloading').hide();
                         $('.showbutton').show();
                        }
                    });
                }
			 	
			}
			else
			{
			 	alert(data);
		  	}
		}
		});
		$('.modal-open').css('overflow','visible');
	});
});

$("#droplink").hide();
$("#drive").hide();
function showPhotos()
{
    $("#image").show();
    $("#docs").hide();
	$("#drop").show();
	$("#droplink").hide();
	$("#drive").hide();
    $("#photo_tab").addClass('active');
    $("#docs_tab").removeClass('active');
	$("#drop_tab").removeClass('active');
	$("#drop_tab").removeClass('active');
	$("#dropl_tab").removeClass('active');
	$("#drive_tab").removeClass('active');
}
function showDocs()
{
    $("#image").hide();
    $("#docs").show();
	$("#drop").hide();
	$("#drop").show();
	$("#droplink").hide();
	$("#drive").hide();
    $("#photo_tab").removeClass('active');
    $("#docs_tab").addClass('active');
	$("#drop_tab").removeClass('active');
	$("#drop_tab").removeClass('active');
	$("#dropl_tab").removeClass('active');
	$("#drive_tab").removeClass('active');
}
function showDrop()
{
    $("#image").hide();
    $("#docs").hide();
	$("#drop").show();
	$("#droplink").hide();
	$("#drive").hide();
    $("#photo_tab").removeClass('active');
    $("#docs_tab").removeClass('active');
	$("#drop_tab").addClass('active');
	$("#dropl_tab").removeClass('active');
	$("#drive_tab").removeClass('active');
}
function showdroplink()
{
    $("#image").hide();
    $("#docs").hide();
	$("#drop").hide();
	$("#droplink").show();
	$("#drive").hide();
    $("#photo_tab").removeClass('active');
    $("#docs_tab").removeClass('active');
	$("#drop_tab").removeClass('active');
	$("#dropl_tab").addClass('active');
	$("#drive_tab").removeClass('active');
}
function showdrive()
{
	
	$("#image").hide();
    $("#docs").hide();
	$("#drop").hide();
	$("#droplink").show();
	$("#drive").show();
    $("#photo_tab").removeClass('active');
    $("#docs_tab").removeClass('active');
	$("#drop_tab").removeClass('active');
	$("#dropl_tab").removeClass('active');
	$("#drive_tab").addClass('active');
}

function commonhideshow(divactive,linkactive){
    $(".comondiv").removeClass('active');
    $(".comonlink").removeClass('active');

    $(".comondiv").hide();
    $("#"+divactive).show();

    $("#"+divactive).addClass('active');
    $("#"+linkactive).addClass('active');
    
}

function fnchangetext(divtext){
    $('#sharedlinktext').text(divtext);
}

function fndeletefolder(job_id,folderlink,drop_id){
    var r = confirm("Are you sure? you want to delete!");
    if (r == false) {
      return;
    }

    $.ajax({
      type: "POST",
      url: "<?php echo AJAX_DIR?>/dropbox_delete_link.php",
      data: {job_id : job_id,folderlink : folderlink,drop_id : drop_id},
      cache: false,
      success: function(data){
         $.ajax({
              type: "POST",
              url: "<?php echo AJAX_DIR?>/dropbox_no_ref.php",
              data: {jid : job_id},
              cache: false,
              success: function(data){
                 //location.reload();
                 $('#droplink').html(data);
                }
            });
        }
    });

     
}

</script>