<?php
require_once '../includes/common_lib.php';

//$draft_url = "https://www.googleapis.com/gmail/v1/users/testapi968104@gmail.com/drafts";

$authUrl = "https://accounts.google.com/o/oauth2/token";
$redirectURL ="http://xactimates.com/workflow/system/inbox.php";

$curl = curl_init();

curl_setopt($curl, CURLOPT_URL, $authUrl);
curl_setopt($curl, CURLOPT_POST, TRUE);
curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
curl_setopt($curl, CURLOPT_POSTFIELDS, array(
    'code' => $code,
    'client_id' => "825539693292-vrugbbt1n4i8mi5g7q731ir5756bpm62.apps.googleusercontent.com",
    'client_secret' => "FqKPVtJOrrj7PlVFxCnquaiX",
    'redirect_uri' => $redirectURL,
    'grant_type' => 'authorization_code'
));

$http_data = curl_exec($curl);  
curl_close($curl);

?>

<?=ViewUtil::loadView('system-configuration-head', array('title' => 'Modules'))?>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
<?=ViewUtil::loadView('system-configuration-sidebar')?>
				</div>
				<div class="span9">
					<div class="page-header">
						<h1>Inbox</h1>
					<div>
						<table class="table table-bordered table-condensed table-hover table-striped">
							<thead>
								<tr>
									<th>Action</th>
									<th>Subject</th>
									<th>Content</th>
								</tr>
							</thead>
							<tbody>

								<tr class="<?=$row_class?>">
								<td colspan="3">
									
                <?php echo "<pre>";print_r($http_data);die;?>
								</td>
								</tr>

							</tbody>
						</table>
					</div>
				</div>
			</div>


 	
<?=ViewUtil::loadView('system-configuration-footer')?>