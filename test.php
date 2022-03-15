<?
echo phpinfo();die;
					$PROPERTY_VIDEO_PATH=__DIR__;
					$uploadFileName=__DIR__.'/videodd.flv';
					error_reporting(1);
					$filename=time();
					$video="VID_1".$filename.".".pathinfo($uploadFileName, PATHINFO_EXTENSION);
					//rename($PROPERTY_VIDEO_PATH.'/'.$uploadFileName,$PROPERTY_VIDEO_PATH.'/'.$video);
					$if=$uploadFileName;
					$of=$PROPERTY_VIDEO_PATH.'/public/resources/lession_attach/VID_'.$filename.'.mp4';
					$newname='VID_'.$filename.'.mp4';
					//echo $if.'<br/>';
					//echo $of.'<br/>';
					$ffmpegPath = __DIR__."/ffmpeg";
					//$time =exec("$ffmpegPath  -i $uploadFileName -ar 22050 -ab 32 -f flv -s 320x240 videodd.flv 2>&1", $output, $ret);
				//	print_r($output);
					//print_r($ret);die;
					//print_r($time); die;
					$time = exec($ffmpegPath.' -i "'.$uploadFileName.'" 2>&1 | grep Duration | cut -d " " -f 4 | sed s/,//');
					/*print_r($time);
					die;*/
					
					//$cmd=$ffmpegPath." -i ".$if." -b 300 -s 320x240 -vcodec xvid -ab 32 -ar 24000 -acodec aac ".$of;
					//$cmd=$ffmpegPath." -i ".$if." -vcodec libx264 -pix_fmt yuv420p -profile:v baseline -preset slow -crf 22 -movflags +faststart  ".$of;
					$cmd=$ffmpegPath." -i ".$uploadFileName."   -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";
					/*$cmd="$ffmpegPath -i ".$if." -s 320x240 -r 30000/1001 -b 200k -bt 240k -vcodec libx264 -coder 0 -bf 0 -refs 1 -flags2 -wpred-dct8x8 -level 30 -maxrate 10M -bufsize 10M -acodec libfaac -ac 2 -ar 48000 -ab 192k ".$of;*/
				//$cmd=$ffmpegPath." -i ".$if." ".$of." -y 2>&1";
					//echo $cmd;die;
					 $out=exec($cmd,$output,$ret);
					  print_r($cmd);
					   echo "<br>";
					 echo "<br>";
					   echo "<br>";
					 print_r($output);
					 echo "<br>";
					 echo "<br>";
					   echo "<br>";
					 print_r($ret);die;
					 $b=explode('.',$of);
					$c=$b[0];  
					 $ffmpeg = 'ffmpeg';
					$vthumbImagename=$c.".jpg";
					//$strNewFile=$pathvideo.$newname;
					$outputFile=$vthumbImagename;
					echo '<br>';
					echo $outputFile;
					echo '<br>';
					echo $of;
					$cmdnew=$ffmpegPath." -i ".$of." -ss 00:00:01.000 -vframes 1 ".$outputFile." -y 2>&1";
					
					$outnew=exec($cmdnew,$outputFile,$retnew);
					echo '<br>';
					print_r($outnew);
					 print_r($retnew);
					 die;
					exec($cmd." 2>&1", $output);
					echo "<pre>";
					var_dump($output);
					echo "</pre>";
					//$out=exec($cmd,$output,$ret);
					echo "enter";die;
							
							 
					
					$b=explode('.',$newname);
					$c=$b[0];  
					$ffmpeg = '/ffmpeg';
					$vthumbImagename=$c.".jpg";
					$strNewFile=PROPERTY_VIDEO_PATH.'/'.$newname;
					$outputFile=PROPERTY_VIDEO_PATH.'/'.$vthumbImagename;
					$cmdnew="public/fileffmpeg/ffmpeg -i ".$strNewFile." -ss 00:00:05.000 -t 00:00:50.000 -pix_fmt rgb24 -r 1  -vframes 5 -s 240x170 ".$outputFile." -y 2>&1";
					$outnew=exec($cmdnew,$outputFile,$retnew);
								
?>