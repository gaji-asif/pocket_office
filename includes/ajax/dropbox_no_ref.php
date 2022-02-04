<?php
include '../common_lib.php';
//=======================================
$job_id = $_POST['jid'];
?>
<?php
	$sqldrp = "select * from dropbox where job_id = '".$job_id."'";
	$resultsdrp = DBUtil::query($sqldrp);
	
    while($rowdrp = mysqli_fetch_array($resultsdrp))
    {
        /*$ext_arr=explode('.',$upload['filename']);           
        $ext=end($ext_arr);             
        if(!in_array(strtolower($ext),$img_ext))
        {*/
            $sqldrp_f = "select * from dropboxfiles where job_id = '".$job_id."' and ref_link = '".$rowdrp['link']."'";
            $resultsdrp_fs = DBUtil::query($sqldrp_f);
            $folders = array();
            while($rowdr = mysqli_fetch_array($resultsdrp_fs)){
                $folders[] = $rowdr;
            }
            $view_data = array(
                'drp_id' => $rowdrp['drop_id'],
                'links' => $rowdrp['link'],
                'job_id' => $job_id,
                'folders' => $folders,
            );
            echo ViewUtil::loadView('job-upload-container-list', $view_data);                
           /* $count++;
        }*/
		
    }
    
    ?>