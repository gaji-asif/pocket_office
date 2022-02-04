<?php



$GLOBALS['databases'] = array();

$GLOBALS['database_connections'] = array();



$parsedUrl = parse_url($_SERVER['SERVER_NAME']);



//print '<pre>';print_r($parsedUrl);

//environment detection

if($_SERVER['SERVER_NAME'] != 'localhost') {

	//database connections array

	$GLOBALS['databases'] = array(

		'assure' => array(

			'host' => 'xactbid.com',

			'database' => 'xactbid_web',

			'user' => 'xactbid_webapp',

			'password' => 'o&ZX!f5]z-lf',

		)

	);

	define('ROOT_DIR', 'https://www.xactbid.com/workflow');

	define('ACCOUNT_URL', 'https://www.xactbid.com/workflow/');

        define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/workflow');

        define('LOG_PATH', $_SERVER['DOCUMENT_ROOT'] . '/workflow/assure_logs/');

        define('APP_NAME', 'Xactbid');

        define('ALERTS_EMAIL', 'alerts@workflow365.co');

        define('DOMAIN', $parsedUrl['host']);

        define('CRON_DATABASE', 'workflow_performance');

        define('CRON_DATABASE', 'workflow_performance');

}  else {

	//database connections array

	$GLOBALS['databases'] = array(

		'assure' => array(

			/*'host' => '127.0.0.1',*/

			'host' => 'localhost',

			'database' => 'workflow',

			'user' => 'root',

			'password' => '',

		)

	);



	define('ROOT_DIR', 'https://localhost/workflow');

	define('ACCOUNT_URL', 'https://localhost/workflow');

    define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/workflow');

    define('LOG_PATH', $_SERVER['DOCUMENT_ROOT'] . '/workflow/assure_logs/');

    define('APP_NAME', 'Xactbid');

    define('ALERTS_EMAIL', 'alerts@workflow365.com');

    define('DOMAIN', $parsedUrl['path']);

    //define('DOMAIN', $parsedUrl['host']);

    define('CRON_DATABASE', 'assure');

    define('DISABLE_EMAIL', TRUE);

    define('NEW_JOB_ACTIONS', TRUE);

    define('DEVELOPMENT', TRUE);

}

DBUtil::setDatabases($GLOBALS['databases']);

//file system

define('AJAX_DIR', ROOT_DIR . '/includes/ajax');

define('IMAGES_DIR', ROOT_DIR . '/images');

define('INCLUDES_PATH', ROOT_PATH . '/includes');

define('UPLOADS_PATH', ROOT_PATH . '/uploads');

define('UPLOADS_DIR', ROOT_DIR . '/uploads');

define('DOCUMENTS_PATH', ROOT_PATH . '/docs');

define('LOGOS_PATH', ROOT_DIR . '/logos');

define('FPDF_FONTPATH', INCLUDES_PATH . '/classes/pdf_generation/font/');

define('IMAGE_RESIZE', ROOT_DIR . '/includes/php/image.php');



//dimensions

define('DEFAULT_WINDOW_HEIGHT', 400);

define('ROWS_PER_PAGE_DENOMINATOR', 31);

define('NEW_ROWS_PER_PAGE_DENOMINATOR', 39);



//system configuration portal

define('SYS_CONFIG_PAGE_TITLE', 'Xactbid');



define('ITERATION', 'v1.1');

define('YAHOOAPI', '89PE7KDV34FsRWULvAR6f27XjFDSL0GcCmaE49P60YuWbeGFmEJ65Slr8zLL2X3NcZNk7IQXzPNLWViEhZdZxO');

define('TOTAL_NOTIFICATIONS', 10);



define('DEFAULT_TIMEZONE', 'America/Denver');



define('CRON_KEY', 'lDzn35WWI8y1moi');



//open weather app id

define('OPEN_WEATHER_APP_ID', '5c74418a34fc96b28a2b89b8bebfb473');