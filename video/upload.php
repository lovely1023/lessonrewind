<?php

		$val=$_POST['x'];
		$user_id=$_REQUEST['user_id'];
		$file = $val; 
		$newfile =time().'_'.'output.mp4'; 
		$path=realpath(dirname(__FILE__));
		
		//$_SESSION['user_'.$user_id][$newfile]=$newfile;
		//mail('techdemo@techdemolink.co.in',"Video Recording","ok",$val);
		
		if (!copy($file, $path.'/'.$newfile)) 
		{ 
			echo "failed to copy $file...\n"; 
			
			//mail('techdemo@techdemolink.co.in',"Video Recording",$val."+++++++++++".$path.$newfile);
		}
		else
		{
				$pathname=explode("/video",realpath(dirname(__FILE__) . ''));
				//mail('techdemo@techdemolink.co.in',"else",$pathname[0]);
				$newfile_with_path=$pathname[0].'/video/'.$newfile;
				$foder_path=$pathname[0].'/public/resources/lession_attach/voice_'.$user_id.'/'.$newfile;
				if(!is_dir($pathname[0].'/public/resources/lession_attach/voice_'.$user_id.'/'))
				{
					mkdir($pathname[0].'/public/resources/lession_attach/voice_'.$user_id.'/');
				}
				rename($newfile_with_path,$foder_path); 
				echo $newfile;
		}
		
		exit;
