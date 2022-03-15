<?php
	defined('ROOT_PATH') || define('ROOT_PATH', getcwd());
	define("SITE_NAME", "lessonrewind.com");
 	define("NAME_OF_SITE", "lessonrewind.com");
	define("ADMIN_AUTH_NAMESPACE", "ADMIN_AUTH");
	define("DEFAULT_AUTH_NAMESPACE", "DEFAULT_AUTH");
 	define("SITE_BASE_URL", dirname($_SERVER['PHP_SELF']));
	define("SITE_HOST_URL", "https://" . $_SERVER['HTTP_HOST']);
	define("SITE_HTTP_URL", "https://" . $_SERVER['HTTP_HOST'] );
	define("APPLICATION_URL", "https://" . $_SERVER['HTTP_HOST'] );
	define("ADMIN_APPLICATION_URL", SITE_HTTP_URL . "/admin");
	define('PRICE_SYMBOL','$');
	define("FRONT_CSS_PATH",SITE_HTTP_URL.'/assets/front/css');
	define("FRONT_JS_PATH",SITE_HTTP_URL.'/assets/front/js');
	define("FRONT_IMAGES_PATH",SITE_HTTP_URL.'/assets/front/img');
	define('ADMIN_CSS_PATH', SITE_HTTP_URL.'/assets/admin/css');
	define('ADMIN_JS_PATH', SITE_HTTP_URL.'/assets/admin/js');
	define('ADMIN_IMAGES_PATH', SITE_HTTP_URL.'/assets/admin/img');
 	define('ADMIN_ASSETS_PATH', SITE_HTTP_URL.'/assets/admin');
	define('ADMIN_PROFILE', '/resources/admin profile images');
	define('PROPERTY_IMAGES', '/resources/property_images');
	define('GALLERY_IMAGES', '/resources/gallery images');
	define('TEAM_IMAGES', '/resources/team members');
	define("EXCEL_VALID_EXTENTIONS","xls,xlsx,csv ,XLS,XLSX,CSV");
	define("IMAGE_VALID_EXTENTIONS","jpg,JPG,png,PNG,jpeg,JPEG");
	define("IMAGE_VALID_SIZE","5MB");
	define("IMG_URL",ROOT_PATH."/assets/img/");
	define("HTTP_IMG_URL",APPLICATION_URL."/assets/img/");
	define('HTTP_SITEIMG_PATH', SITE_HTTP_URL.'/public/site_images');
	define('SITEIMG_PATH', ROOT_PATH.'/public/site_images');
	define('HTTP_TEMP_PATH', SITE_HTTP_URL.'/public/resources/lession_attach');
	define('TEMP_PATH', ROOT_PATH.'/public/resources/lession_attach');
	define('TEMP_VIDEO_PATH', ROOT_PATH.'/video');
	define('HTTP_AN_PATH', SITE_HTTP_URL.'/public/resources/announce_attach');
	define('AN_PATH', ROOT_PATH.'/public/resources/announce_attach');
	/* New Theme Constatns */
	define('HTTP_IMG_PATH', SITE_HTTP_URL.'/public/img');
 
 	define('HTTP_PROFILE_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/profile_images');
 	define('PROFILE_IMAGES_PATH', ROOT_PATH.'/public/resources/profile_images');
	 
	 
	define('HTTP_MEDIA_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/media_images');
	define('MEDIA_IMAGES_PATH', ROOT_PATH.'/public/resources/media_images');
	
	define('HTTP_SLIDER_IMAGES_PATH', SITE_HTTP_URL.'/public/resources/slider_images');
	define('SLIDER_IMAGES_PATH', ROOT_PATH.'/public/resources/slider_images');
	
	define('HTTP_TEACHER_FILES_PATH', SITE_HTTP_URL.'/public/resources/teacher_attachements');
	define('TEACHER_FILES_PATH', ROOT_PATH.'/public/resources/teacher_attachements');
	
	define('HTTP_IMPORT_ATTACH', SITE_HTTP_URL.'/public/resources/import_attach');
	define('IMPORT_ATTACH', ROOT_PATH.'/public/resources/import_attach');
	

	
 	 global $credit_year;
	
 $credit_year=array();
  $credit_year['']='--Select Year--';
 global $credit_month;
  $credit_month=array();
   $credit_month['']='--Select Month--';
 for ($i = 1; $i <= 12; $i++)
{
    $x = ($i < 10) ? '0'.$i : $i;
	$credit_month[$x]=$x;
}

 for($i=date("Y")+15;$i>=date("Y");$i--) {
	 
	 $credit_year[$i]=$i;
	
    }
	
global $card_type;
$card_type=array(
	'Visa'=>'Visa',
	'Discover'=>'Discover',
	'MasterCard'=>'MasterCard',
	'Amex'=>'Amex',
);

global $day_array;

$day_array=array(
	'Mondays'=>'Mondays',
	'Tuesdays'=>'Tuesdays',
	'Wednesdays'=>'Wednesdays',
	'Thursdays'=>'Thursdays',
	'Fridays'=>'Fridays',
	'Saturdays'=>'Saturdays',
	'Sundays'=>'Sundays',
);
global $account_type;

$account_type=array(''=>'Select Account Type',
					'1'=>'Bronze',
					'2'=>'Silver',
					'3'=>'Gold',
					'4'=>'Platinum',
					'5'=>'Platinum Plus'
);

global $roleArr;
	$roleArr=array(
		'1'=>'Dashboard',
		'2'=>'Site Configurations',
		'3'=>'Slider Images',
		'4'=>'Static Content',
		'5'=>'User Management',
		'6'=>'Sub admin Management',
		'7'=>'Manage Subscription Plan',	
		'8'=>'Manage Instruments',
	);
	
global $videoext;

$videoext=array('wmv','wmx','wm','avi','divx','flv','mov','mpeg','qt','mpg','mpe','mp4','m4v','ogv','webm','mkv');


