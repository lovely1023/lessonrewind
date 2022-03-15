<?php 


 /*  Connect File For Cron Job */
$dbConfig = parse_ini_file(APPLICATION_PATH . '/configs/db.ini',false);


/*  Connect File For Cron Job */
$connection  = mysql_connect($dbConfig['resources.db.params.hostname'] , $dbConfig['resources.db.params.username'] , $dbConfig['resources.db.params.password']);

if(!$connection){
	die('Could not connect: ' . mysql_error());
}


$dbselect = mysql_select_db($dbConfig['resources.db.params.dbname']);


if(!$dbselect){
	die('Could not connect: ' . mysql_error());
}


