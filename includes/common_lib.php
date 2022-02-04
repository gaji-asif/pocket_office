<?php

//header("Location: maintenance");
//die();
ini_set('display_errors', 'Off');
// header('Access-Control-Allow-Origin: *');
// header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token,Authorization');
// header('Access-Control-Allow-Credentials: true');
ini_set('session.gc_maxlifetime', 30*24*60*60); // 30 days
ini_set('session.gc_probability', 1);
ini_set('session.gc_divisor', 100);
ini_set('session.cookie_secure', FALSE);
ini_set('session.use_only_cookies', TRUE);
session_start();

//Required for startup
include_once 'utils/AssureUtil.php';
include_once 'utils/DBUtil.php';
include_once 'utils/FileUtil.php';
include_once 'utils/ErrorUtil.php';
include_once 'utils/RequestUtil.php';
include_once 'models/AssureModel.php';
include_once 'models/UserModel.php';

//  include_once '../models/UserModel.php';
// include_once '../models/EmailModel.php';
// include_once '../models/AssureModel.php';

FileUtil::init();

//CONFIG
include_once 'config/global.php';

//logging
ini_set('log_errors', 1);
ini_set('error_log', LOG_PATH);

//timezone
@date_default_timezone_set(DEFAULT_TIMEZONE);

//shutdown handlers

register_shutdown_function('ErrorUtil::fatalShutdownHandler');

//connect to db
if(isset($_SESSION['database_name'])) {
    DBUtil::connect($_SESSION['database_name']);
}

//clean request data
// RequestUtil::cleanseRequest();

//OLD LIBS
include_once 'php/system_lib.php';
include_once 'php/uploads_lib.php';
include_once 'php/ui_lib.php';
$link = $_SERVER['PHP_SELF'];
$link_array = explode('/',$link);
$page = end($link_array);
/*if($_SESSION['ao_userid']=='110')
{
    echo $page;die;
}*/
if($page!='chat.php' && $page!='get_tooltip.php' && $page!='toggle_bookmark.php')
{
    $sql = 'delete from job_viewer where user_id = '.$_SESSION['ao_userid'];
    DBUtil::query($sql);
}

    
function logout($jsRedirect = FALSE) {
    
	$_SESSION = array();
	session_destroy();
    if(!$jsRedirect) { return; }
?>
    <script>
        var obj = window.parent || window;
        obj.location.href = "/";
    </script>
<?php
}

function prepareText($str) {
    return stripslashes(str_replace(array("\\r\\n", "\\r", "\\n"), "<br />", $str));
//    return (nl2br($str));
//	$str = nl2br($str);
//	$str = stripslashes($str);
	$str = str_replace("\r\n", "<br/>\r\n", $str);
	$str = str_replace("\n", "<br/>\n", $str);
//	$str = stripslashes($str);/
	//$str = str_replace("<", "&lt;", $str);
	//$str = str_replace(">", "&gt;", $str);

	return $str;
}

function stageAdvanceAccess($stageNumber) {
	$sql = "SELECT sa.stage_access_id, usa.id
            FROM stages s
            LEFT JOIN stage_access sa ON sa.stage_id = s.stage_id AND sa.level_id = '{$_SESSION['ao_level']}'
            LEFT JOIN user_stage_access usa ON usa.stage_id = s.stage_id AND usa.user_id = '{$_SESSION['ao_userid']}'
            WHERE s.account_id = '{$_SESSION['ao_accountid']}'
                AND s.stage_num = '$stageNumber'";
    $results = DBUtil::queryToArray($sql);
    if(!empty($results) && (!empty($results[0]['stage_access_id']) || !empty($results[0]['id'])))
    {
    	return true;
    }
    else
    {
    	return false;
    }
    //return DBUtil::hasRows(DBUtil::query($sql));
}

function moduleOwnership($hook)
{
	$sql = "select module_access.ownership, exceptions.onoff, exceptions.ownership" .
		" from modules" .
		" left join" .
		" exceptions on (exceptions.module_id=modules.module_id and exceptions.user_id='" . $_SESSION['ao_userid'] . "')" .
		" left join" .
		" module_access on" .
		" (module_access.module_id=modules.module_id and module_access.account_id='" . $_SESSION['ao_accountid'] . "' and module_access.level='" . $_SESSION['ao_level'] . "')" .
		" where modules.hook='" . $hook . "'" .
		" limit 1";

	$res = DBUtil::query($sql);
	list($group_ownership, $exception_onoff, $exception_ownership) = mysqli_fetch_row($res);

	if(($group_ownership == '1' && $exception_onoff != 1) || ($exception_onoff == 1 && $exception_ownership == 1))
		return true;
	return false;
}

function viewWidget($hook) {
    return ($_SESSION['ao_' . $hook] == 1);
}

function pageSecure($source) {
	if(!UserModel::loggedIn()) {
		parentRedirect(ACCOUNT_URL);
	}
	else {
		$sql = "select nav_access.navaccess_id from nav_access, navigation" .
			" where nav_access.account_id='" . $_SESSION['ao_accountid'] . "' and navigation.source='" . $source . "' and nav_access.level = '" . $_SESSION['ao_level'] . "' and nav_access.navigation_id=navigation.navigation_id" .
			" limit 1";
		$res = DBUtil::query($sql);
		if(mysqli_num_rows($res) == 0)
			die("Insufficient Rights");
	}
}

function parentRedirect($url) {
    echo ViewUtil::loadView('js/top-redirect', array('url' => $url));
    die();
}

function getStates()
{
	return array(
		'AL' => "Alabama",
		'AK' => "Alaska",
		'AZ' => "Arizona",
		'AR' => "Arkansas",
		'CA' => "California",
		'CO' => "Colorado",
		'CT' => "Connecticut",
		'DE' => "Delaware",
		'DC' => "District Of Columbia",
		'FL' => "Florida",
		'GA' => "Georgia",
		'HI' => "Hawaii",
		'ID' => "Idaho",
		'IL' => "Illinois",
		'IN' => "Indiana",
		'IA' => "Iowa",
		'KS' => "Kansas",
		'KY' => "Kentucky",
		'LA' => "Louisiana",
		'ME' => "Maine",
		'MD' => "Maryland",
		'MA' => "Massachusetts",
		'MI' => "Michigan",
		'MN' => "Minnesota",
		'MS' => "Mississippi",
		'MO' => "Missouri",
		'MT' => "Montana",
		'NE' => "Nebraska",
		'NV' => "Nevada",
		'NH' => "New Hampshire",
		'NJ' => "New Jersey",
		'NM' => "New Mexico",
		'NY' => "New York",
		'NC' => "North Carolina",
		'ND' => "North Dakota",
		'OH' => "Ohio",
		'OK' => "Oklahoma",
		'OR' => "Oregon",
		'PA' => "Pennsylvania",
		'RI' => "Rhode Island",
		'SC' => "South Carolina",
		'SD' => "South Dakota",
		'TN' => "Tennessee",
		'TX' => "Texas",
		'UT' => "Utah",
		'VT' => "Vermont",
		'VA' => "Virginia",
		'WA' => "Washington",
		'WV' => "West Virginia",
		'WI' => "Wisconsin",
		'WY' => "Wyoming");
}

function getAllUnits()
{
	$sql = "select unit_id, unit from units order by unit asc";
	return DBUtil::queryToArray($sql);
}

function getAllSmsCarriers()
{
	$sql = "select sms_id, carrier from sms order by carrier asc";
	return DBUtil::queryToArray($sql);
}




/**
 * 
 *
 * Deep Mazumder weather API call
 */
error_reporting(E_ALL);

//$url = <<<EOD
//https://graphical.weather.gov/xml/sample_products/browser_interface/ndfdXMLclient.php?whichClient=NDFDgenMultiZipCode&zipCodeList=80642+80290+80014&product=time-series&begin=2020-05-10T00%3A00%3A00&end=2020-05-14T00%3A00%3A00&Unit=e&wspd=wspd&dryfireo=dryfireo&ptornado=ptornado&phail=phail&pxtornado=pxtornado&pxhail=pxhail&ptotsvrtstm=ptotsvrtstm&pxtotsvrtstm=pxtotsvrtstm&Submit=Submit
//EOD;

// TRY THE REMOTE WEB SERVICE
//$response = new GET_Response_Object($url);

// SHOW THE WORK PRODUCT

//if (!$response->document) var_dump($response);

// SHOW HOW TO RETRIEVE DATA FROM THE API
//$obj = SimpleXML_Load_String($response->document);

// ACTIVATE THIS TO SEE THE ENTIRE OBJECT
//print_r($obj);

//print_r($obj->data);
//print_r($obj->data->location);



Class GET_Response_Object
{
    public $href, $title, $http_code, $errno, $info, $document;

    public function __construct($href, $user=NULL, $pass=NULL, $get_array=[], $title=NULL)
    {
        // ACTIVATE THIS TO AVOID TIMEOUT FOR LONG RUNNING SCRIPT
        // set_time_limit(10);

        // STORE THE CALL INFORMATION
        $this->href  = $href;
        $this->title = $title;

        // PREPARE THE GET STRING
        $get_string = http_build_query($get_array);
        if ($get_string) $get_string = '?' . $get_string;

        // MAKE THE REQUEST
        if (!$response = $this->my_curl($href, $user, $pass, $get_string))
        {
            // ACTIVATE THIS TO SEE THE ERRORS AS THEY OCCUR
            trigger_error("Errno: $this->errno; HTTP: $this->http_code; URL: $this->href", E_USER_WARNING);
        }
        else
        {
            return $response;
        }
    }

    protected function my_curl($url, $user, $pass, $get_string, $timeout=3)
    {
        // PREPARE THE CURL CALL
        $curl = curl_init();

        // HEADERS AND OPTIONS APPEAR TO BE A FIREFOX BROWSER REFERRED BY GOOGLE
        $header[] = "Accept: text/xml,application/xml,application/xhtml+xml,text/html;q=0.9,text/plain;q=0.8,image/png,*/*;q=0.5";
        $header[] = "Cache-Control: max-age=0";
        $header[] = "Connection: keep-alive";
        $header[] = "Keep-Alive: 300";
        $header[] = "Accept-Charset: ISO-8859-1,utf-8;q=0.7,*;q=0.7";
        $header[] = "Accept-Language: en-us,en;q=0.5";
        $header[] = "Pragma: "; // BROWSERS USUALLY LEAVE THIS BLANK

        // SET THE CURL OPTIONS - SEE http://php.net/manual/en/function.curl-setopt.php
        curl_setopt( $curl, CURLOPT_URL,            $url . $get_string  );
        curl_setopt( $curl, CURLOPT_USERAGENT,      'Mozilla/5.0 (Windows NT 6.1; rv:44.0) Gecko/20100101 Firefox/44.0'  );
        curl_setopt( $curl, CURLOPT_HTTPHEADER,     $header  );
        curl_setopt( $curl, CURLOPT_REFERER,        'http://www.google.com'  );
        curl_setopt( $curl, CURLOPT_ENCODING,       'gzip,deflate'  );
        curl_setopt( $curl, CURLOPT_AUTOREFERER,    TRUE  );
        curl_setopt( $curl, CURLOPT_RETURNTRANSFER, TRUE  );
        curl_setopt( $curl, CURLOPT_FOLLOWLOCATION, TRUE  );
        curl_setopt( $curl, CURLOPT_TIMEOUT,        $timeout  );
        curl_setopt( $curl, CURLOPT_HTTPAUTH,       CURLAUTH_ANY );
        curl_setopt( $curl, CURLOPT_USERPWD,        "$user:$pass" );

        curl_setopt( $curl, CURLOPT_VERBOSE,        TRUE   );
        curl_setopt( $curl, CURLOPT_FAILONERROR,    TRUE   );

        // SET THE LOCATION OF THE COOKIE JAR (THIS FILE WILL BE OVERWRITTEN)
        curl_setopt( $curl, CURLOPT_COOKIEFILE,     'cookie.txt' );
        curl_setopt( $curl, CURLOPT_COOKIEJAR,      'cookie.txt' );

        // IF USING SSL, THIS INFORMATION MAY BE IMPORTANT
        // http://php.net/manual/en/function.curl-setopt.php#110457
        // http://php.net/manual/en/function.curl-setopt.php#115993
        // http://php.net/manual/en/function.curl-setopt.php#113754
        // REDACTED IN 2015 curl_setopt( $curl, CURLOPT_SSLVERSION, 3 );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYHOST, FALSE  );
        curl_setopt( $curl, CURLOPT_SSL_VERIFYPEER, FALSE  );

        // RUN THE CURL REQUEST AND GET THE RESULTS
        $this->document  = curl_exec($curl);
        $this->errno     = curl_errno($curl);
        $this->info      = curl_getinfo($curl);
        $this->http_code = $this->info['http_code'];
        curl_close($curl);

        return $this;
    }
}


function getaddress($lat,$lng)
  {
     $url = 'http://maps.googleapis.com/maps/api/geocode/json?key=AIzaSyA1gf0GrUztgVDDVapXnJlcyYMCilJLubQ&latlng='.trim($lat).','.trim($lng).'&sensor=false';
     $json = @file_get_contents($url);
     $data=json_decode($json);
     $status = $data->status;
     if($status=="OK")
     {
       return $data->results[0]->formatted_address;
     }
     else
     {
       return false;
     }
  }
  
  
  
  	//////////////////////////////////////////////////// Share URL >>	
	
	function getShareUrl() {
		
        $sql = "SELECT 
		
		share_url.id,
		share_url.event_name,
		share_url.user_select,
		share_url.url,
		share_url.share_by,
		share_url.status,
		
		users.fname,
		users.lname

		 from share_url left join users on share_url.id = users.user_id";
        return DBUtil::queryToArray($sql);
		
    }
	
	
	function getUserData($id) {
        $sql = "SELECT * from share_url where id = '".$id."'";
        return DBUtil::queryToArray($sql);
		//    return DBUtil::hasRows(DBUtil::query($sql));
    }
	
	function userIdCheck($userId,$url) {
		
	   $sql = "SELECT * FROM share_url WHERE FIND_IN_SET(".$userId.",user_select) AND url = '".$url."' ";
       return DBUtil::queryToArray($sql);
		
    }
	
	function userOwnIdCheck($userId) {
	   $sql = "SELECT * FROM share_url where  share_by = '".$userId."'";
       return DBUtil::queryToArray($sql);
    }
	
	/////////////////////////////////////////////// Share URL >>
	
	

    // ================  Time to Decimal Hour ======================//
    function timeInDecimalHours($time) 
    {
        //$time_in_hour = ($time/3600*24)*100;
        $time_in_hour = $time/60;
        $time_in_hour = number_format($time_in_hour , 3);
        return $time_in_hour;
    }
    