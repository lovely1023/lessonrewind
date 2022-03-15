<?php

$val=$_POST['x'];
		
		$file = $val; 
		$newfile =time().'output.mp4'; 
	//	rename(ROOT_PATH.'/public/resources/video/output.mp4', ROOT_PATH.'/public/resources/video/'.time().'img506.jpg');
		if (!copy($file, $newfile)) { 
		echo "failed to copy $file...\n"; 
		} 
		exit;
