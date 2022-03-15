<?php

ini_set('post_max_size', '750M');
ini_set('upload_max_filesize', '750M');
ini_set('max_execution_time', '5000');
ini_set('max_input_time', '5000');
ini_set('memory_limit', '1000M');
// To include functions file
// Define path to application directory

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . ''));

//defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . ''));
//date_default_timezone_set("UTC");
define('SITE_STAGE','production');	 //development | testing | production
define("TEST",true);

// To include constants file
require_once "private/constants.php";
require_once "private/functions.php";
require_once "private/site_assets.php";/* Site Assets Array */
require_once "private/public_access.php";/* Site Assets Array */
require_once "public/plugins/Class.PayFlow.php";
include_once "private/Facebook/facebook.php";
include_once "private/Twitter/twitteroauth.php";
include_once "private/Twitter/twconfig.php";
include_once "public/plugins/phpexcelreader/excel_reader/excel_reader.php";

// Define application environment
defined('APPLICATION_ENV') || define('APPLICATION_ENV', (getenv('APPLICATION_ENV') ? getenv('APPLICATION_ENV') : SITE_STAGE));

 // Ensure library/ is on include_path
	set_include_path(implode(PATH_SEPARATOR,
	array(
		realpath(APPLICATION_PATH),
		realpath('private/Zend'),  // For live environment(Client Server) change this path to 'private/Zend' and place the Zend folder into private directory
		realpath('private/'),
		APPLICATION_PATH . '/controllers',
		APPLICATION_PATH . '/forms',
		get_include_path(),
)));




/** Zend_Application */
require_once 'private/Zend/Application.php';
$application = new Zend_Application(APPLICATION_ENV, 'private/application.ini');
$autoloader = Zend_Loader_Autoloader::getInstance();
$autoloader->registerNamespace('Twitter_');
$application->bootstrap()->run();
