<?




				$file_path= '/home/academyofmusik07/public_html/lessonrewind.com/Chrysanthemum.jpg';		
					
					//echo ROOT_PATH.'/public/resources/user_videos/'.$inserted_data['video_path']; die;
					
					$dest_path = '/home/academyofmusik07/public_html/lessonrewind.com/video';
					$fielname=time().'Chrysanthemum.jpg';
					
					rename ($file_path,$dest_path.'/'.$fielname);
					
					echo("done");die;
								
?>