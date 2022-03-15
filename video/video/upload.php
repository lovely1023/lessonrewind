<?php

		$val=$_POST['x'];
		$user_id=$_REQUEST['user_id'];
		$file = $val; 
		$newfile =time().'_'.'output.mp4'; 
	
		//$_SESSION['user_'.$user_id][$newfile]=$newfile;
		if (!copy($file, $newfile)) { 
		mail('techdemo@techdemolink.co.in',"here",$newfile,$val);
		echo "failed to copy $file...\n"; 
		
		}
		else
		{
			mail('techdemo@techdemolink.co.in',"here",$newfile,$val);
				$pathname=explode("/video/video",realpath(dirname(__FILE__) . ''));
				$newfile_with_path=$pathname[0].'/video/video/'.$newfile;
				$foder_path=$pathname[0].'/public/resources/lession_attach/voice_'.$user_id.'/'.$newfile;
				if(!is_dir($pathname[0].'/public/resources/lession_attach/voice_'.$user_id.'/'))
				{
					
					mkdir($pathname[0].'/public/resources/lession_attach/voice_'.$user_id.'/');
					
				}
				rename($newfile_with_path,$foder_path); 
				echo $newfile;
		}
		
		exit;
