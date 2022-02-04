<?php

//header("Location: maintenance");

//die();

ini_set('session.gc_maxlifetime', 14*24*60*60); // 14 days

ini_set('session.gc_probability', 1);

ini_set('session.gc_divisor', 100);

ini_set('session.cookie_secure', FALSE);

ini_set('session.use_only_cookies', TRUE);

session_start();

//print_r($_SESSION);

//Required for startup

include_once 'utils/AssureUtil.php';

include_once 'utils/DBUtil.php';

include_once 'utils/FileUtil.php';

FileUtil::init();



//CONFIG

include_once 'config/global.php';



//logging

ini_set('log_errors', 1);

ini_set('error_log', LOG_PATH);



//timezone

date_default_timezone_set(DEFAULT_TIMEZONE);



//shutdown handlers

register_shutdown_function('ErrorUtil::fatalShutdownHandler');

//print_r($_SESSION);

//connect to db

if(isset($_SESSION['database_name'])) {

    DBUtil::connect($_SESSION['database_name']);

}else{

//$_SESSION['database_name']='workflow_performance';

 //DBUtil::connect('workflow_performance');

}



//clean request data

RequestUtil::cleanseRequest();



//OLD LIBS

include_once 'php/system_lib.php';

include_once 'php/uploads_lib.php';

include_once 'php/ui_lib.php';



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

$res='';

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
    return DBUtil::hasRows(DBUtil::query($sql));

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