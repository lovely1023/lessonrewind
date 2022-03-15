<?php 
global $_allowed_resources ;

	$_allowed_resources = array(
	
 		'admin'=>array(
			'index'=>array(
				"login",
				"logout"
			),
			'user'=>array('activate','signin')
		),
		
		'default'=>array(
			"error",
			"social",
			"index",
			"static",
			"user",
 		)
	);


$_blocked_resources = array(
	'admin'=>array(),
	'site_subadmin'=>array(),
	'school'=>array("profile"=>array("familydashboard","studentdashboard")),
	'teacher'=>array("school","student","family","profile"=>array("dashboard","plansubscription","familydashboard","studentdashboard","lessondahboard"),),
	'student'=>array("school","family","profile"=>array("dashboard","teacherdashboard","subscription","plansubscription","familydashboard","lessondahboard")),
	'schoolsubadmin'=>array("profile"=>array('teacherdashboard',"subscription","plansubscription","familydashboard","studentdashboard","lessondahboard")),
	'family'=>array("school","student","profile"=>array("dashboard"),"profile"=>array('teacherdashboard',"subscription","plansubscription","lessondahboard")),
	
);

