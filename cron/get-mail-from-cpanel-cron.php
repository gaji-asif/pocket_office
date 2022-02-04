<?php
$dbhost = 'localhost';
$dbuser = 'xactbid_webapp';
$dbpass = 'o&ZX!f5]z-lf';
$conn = mysqli_connect($dbhost, $dbuser, $dbpass, 'xactbid_web');
	$response = get_web_page("https://xactbid.com/get_mail.php");
	$resArr = array();
	$resArr = json_decode($response);
	if (!empty($resArr)) {
		foreach ($resArr as $key => $value) {
			$sql = "select * from jobs where job_number = '".trim(str_replace('#', '', $value->job_number))."'";
	    	$query = mysqli_query($conn, $sql);
	    	$job = mysqli_fetch_assoc($query);
	    	if (!empty($job)) {

				$from_email = $value->from_email;
				$from_name = $value->from_name;
		      	$email_note       = $value->email_note;
		      	$email_subject    = $value->email_subject;
		      	$email_send    = $value->email_send_to;

		        $sql = " INSERT INTO job_email 

		              (job_id,

		              job_number,

		              email_note,

		              email_files,

		              email_subject,

		              email_send_to,

		              from_email,

		              from_name,

		              email_type,

		              created_by,

		              email_send_to_cc

		              ) 

		              VALUES 

		              (

		              ".$job['job_id'].",

		              '".$job['job_number']."',

		              '$email_note',

		              '$value->attachments',

		              '$email_subject',

		              '$email_send',

		              '$from_email',

		              '$from_name',

		              '".$value->email_type."',

		              'cron',
		              ''
		            )";
		        mysqli_query($conn, $sql);
	    	}
		}
		
	}

	function get_web_page($url) {
	    $options = array(
	        CURLOPT_RETURNTRANSFER => true,   // return web page
	        CURLOPT_HEADER         => false,  // don't return headers
	        CURLOPT_FOLLOWLOCATION => true,   // follow redirects
	        CURLOPT_MAXREDIRS      => 10,     // stop after 10 redirects
	        CURLOPT_ENCODING       => "",     // handle compressed
	        CURLOPT_USERAGENT      => "test", // name of client
	        CURLOPT_AUTOREFERER    => true,   // set referrer on redirect
	        CURLOPT_CONNECTTIMEOUT => 120,    // time-out on connect
	        CURLOPT_TIMEOUT        => 120,    // time-out on response
	    ); 

	    $ch = curl_init($url);
	    curl_setopt_array($ch, $options);

	    $content  = curl_exec($ch);

	    curl_close($ch);

	    return $content;
	}
 ?>