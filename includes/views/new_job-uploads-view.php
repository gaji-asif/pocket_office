 
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
  <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<?php
if(empty($myJob) || get_class($myJob) !== 'Job') { return; }
?>
<tr class="job-tab-content uploads" <?=@$show_content_style?>>
    <td colspan=11>
        <div class="dropzone-message alert alert-info">
            <strong>Heads up!</strong> You can drag files directly below to automatically upload and save them to this job.
        </div>
        

  <ul class="nav nav-tabs">
    <li class="active"><a data-toggle="tab" href="#image">Photos</a></li>
    <li><a data-toggle="tab" href="#docs">Documents</a></li>
  </ul>

<div id="dropzone" class="clearfix tab-content" data-itemtype="job_file" data-itemid="<?=$myJob->job_id?>">
    <div id="image" class="tab-pane fade in active">
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

</div>

<div id="docs" class="tab-pane fade">
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
    </td>
</tr>