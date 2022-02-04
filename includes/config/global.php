<?php
$GLOBALS['databases'] = array();

$GLOBALS['database_connections'] = array();



$parsedUrl = parse_url($_SERVER['SERVER_NAME']);



//environment detection

if($_SERVER['SERVER_NAME'] != 'localhost') {

	//database connections array

	$GLOBALS['databases'] = array(

		'pocketoffice_xactbid' => array(

			'host' => 'localhost',

			'database' => 'pocketoffice_xactbid',

			'user' => 'pocketoffice_xactbid',

			'password' => 'Qs(o6X!;m8#L',

		)

	);

	define('ROOT_DIR', 'https://xactbid.pocketofficepro.com');

	define('ACCOUNT_URL', 'https://xactbid.pocketofficepro.com');

        define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT']);

        define('LOG_PATH', $_SERVER['DOCUMENT_ROOT'] . '/assure_logs/');

        define('APP_NAME', 'Xactbid Test Server');

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

			'database' => 'xactbid7_pocket_office',

			'user' => 'root',

			'password' => '',

		)

	);



	define('ROOT_DIR', 'http://localhost/ums/xactbid');

	define('ACCOUNT_URL', 'http://localhost/ums/xactbid');

    define('ROOT_PATH', $_SERVER['DOCUMENT_ROOT'].'/ums/xactbid');

    define('LOG_PATH', $_SERVER['DOCUMENT_ROOT'] . '/ums/xactbid/assure_logs/');

    define('APP_NAME', 'Xactbid Test Server');

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

define('EMAIL_ATTACHMENT', ROOT_PATH . '/uploads/email_attachment/');

define('EMAIL_ATTACHMENT_URL', ROOT_DIR . '/uploads/email_attachment/');

define('EMAIL_REPLY_TO', 'workflow@xactbid.com');

//dimensions

define('DEFAULT_WINDOW_HEIGHT', 400);

define('ROWS_PER_PAGE_DENOMINATOR', 31);

define('NEW_ROWS_PER_PAGE_DENOMINATOR', 39);



//system configuration portal

define('SYS_CONFIG_PAGE_TITLE', 'Xactbid Test Server');



define('ITERATION', 'v1.1');

define('YAHOOAPI', '89PE7KDV34FsRWULvAR6f27XjFDSL0GcCmaE49P60YuWbeGFmEJ65Slr8zLL2X3NcZNk7IQXzPNLWViEhZdZxO');

define('TOTAL_NOTIFICATIONS', 10);



define('DEFAULT_TIMEZONE', 'America/Denver');


define('CRON_KEY', 'lDzn35WWI8y1moi');

//open weather app id

define('OPEN_WEATHER_APP_ID', '5c74418a34fc96b28a2b89b8bebfb473');



//Configure Paypal

define("USE_SANDBOX", TRUE);
$paypalUrl = USE_SANDBOX ? 'https://www.sandbox.paypal.com/cgi-bin/webscr' : 'https://www.paypal.com/cgi-bin/webscr';
define('PAYPAL_URL', $paypalUrl);
//define('PAYPAL_ID', 'Billing@johnsonmgmt.net');
define('PAYPAL_ID', 'dpmzmdr-facilitator@gmail.com');
define('CURRENCY_CODE', 'USD');
define('PAYPAL_CMD', '_xclick');
define('NOTIFY_URL', ROOT_DIR.'/payments/payment.php');
define('RETURN_URL', ROOT_DIR.'/payments/thanks.php');
define('CANCEL_URL', ROOT_DIR);

