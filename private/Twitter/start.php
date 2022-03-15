<?php

include 'EpiCurl.php';
include 'EpiOAuth.php';
include 'EpiTwitter.php';
include 'secret.php';
$consumer_key = '78EnS8I8rYH4wbbnWg';
$consumer_secret = 'EZs5pFy8H0KNApCtHMm1BiQrA6UfLfIhuaDkYfE45m0';
$twitterObj = new EpiTwitter($consumer_key, $consumer_secret);

echo '<a href="' . $twitterObj->getAuthenticateUrl() . '">Authorize with Twitter</a>';
?>

