<?php

require_once 'private/ZiggeoPhpSdk-master/Ziggeo.php';
$ziggeo = new Ziggeo('80022bf8c53e76bfb6c1bebccefc6113', 'e07460938f50cd611b59a5f764676e48', '384609c33a387b25c597cd8bb1790c96'); 
$token = $_GET['token']; 

$videoBuffer = $ziggeo->videos()->download_video($token) ; 
$filename="video.mp4";
file_put_contents("video.mp4", $videoBuffer); 

 //header('Content-Disposition: attachment; filename="' . basename($file).'"');
	
				$fullPath = "video.mp4"; 
			if ($fd = fopen ($fullPath, "r")) {
				
				$fsize = filesize($fullPath);
				$path_parts = pathinfo($fullPath);
				
				$ext = strtolower($path_parts["extension"]);
				switch ($ext) {
					case "pdf";
					header("Content-type: application/pdf"); 
					header("Content-type: application/doc"); 
					header('Content-Disposition: attachment; filename="' . basename($path_parts["basename"]).'"');
					break;
					default;
					header('Content-Description: File Transfer');
					header("Content-type: application/octet-stream");
					header('Content-Disposition: attachment; filename="' . basename($path_parts["basename"]).'"');
					//header("Content-Disposition: filename=\"".basename($path_parts["basename"])."\"");
					header('Content-Transfer-Encoding: binary');
   					 header('Expires: 0');
  				  header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
   					 header('Pragma: public');
				}
				header("Content-length: $fsize");
				header("Cache-control: private"); 
				while(!feof($fd)) {
					$buffer = fread($fd, 2048);
					echo $buffer;
    }
}
fclose ($fd);
			
 ?>