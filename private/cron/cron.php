<?php
error_reporting(E_ALL);
ini_set("display_errors","on");
define("SITE_HTTP_URL", "http://www.lessonrewind.com");
define('HTTP_TEMP_PATH', SITE_HTTP_URL.'/public/resources/lession_attach');

defined('APPLICATION_PATH') || define('APPLICATION_PATH', realpath(dirname(__FILE__) . '/application'));
defined('ROOT_PATH') || define('ROOT_PATH', realpath(dirname(__FILE__) . ''));

defined('ROOT_PATH1') || define('ROOT_PATH1',realpath(dirname(dirname(__DIR__))));
define('TEMP_PATH', ROOT_PATH1.'/public/resources/lession_attach');
//define('SITE_STAGE','development');

if(preg_match('/192.168/',$_SERVER['REMOTE_ADDR'])){
	define('SITE_STAGE','development');	 //development
 	define("VARUN_TEST",true);
	define("TEST",true);
}else{
	define('SITE_STAGE','production');
	define("VARUN_TEST",false);
	define("TEST",false);
}

date_default_timezone_set("UTC");
//date_default_timezone_set("asia/singapore");
require_once realpath(dirname(__DIR__)).'/functions.php';
require_once ROOT_PATH.'/Library/function_library.php';
require_once realpath(dirname(__DIR__)).'/ZiggeoPhpSdk-master/Ziggeo.php';
//require_once "application/configs/cron_db.php";
require_once ""."Library/DbAdapter.php";
require_once ""."Library/User.php";

$user = new User();

$user->updatelesson();
