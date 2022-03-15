<?
	$myFile = $_FILES['Filedata']['name'];
	print_r($_SESSION);
	$user_id=$_POST['user_id'];
	$filename = "uploads/voice_".$user_id.'/'. time().$myFile . ".mp4";
	$_SESSION['voice_recording_'.$user_id][$filename]=$filename;
	print_r($_SESSION['voice_recording_'.$user_id][$filename]);
	move_uploaded_file($_FILES['Filedata']['tmp_name'], $filename) or die ("can't move");
	echo "uploaded successfully. :-)";
?>