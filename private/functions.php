<?php
function format_category_name($name){
	
	$name = strtolower($name);
	
	$name = str_replace(" ","-",$name);
	
	return $name ;	
}
function datechange($date,$timezone)
{

$timestamp = strtotime($date);
date_default_timezone_set($timezone); // GMT -7

$newDateString = date('F d, Y  h:i A', $timestamp);
return $newDateString;
}
function n_digit_random($digits) {
  $temp = "";

  for ($i = 0; $i < $digits; $i++) {
    $temp .= rand(0, 9);
  }

  return (int)$temp;
}

function generateRandomString($length = 10) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $length; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}
function myurl_encode($id)
{

$passstring=$id*12345;
$encrypted_string=encrypt_decrypt($passstring);
$param=urlencode($encrypted_string);
return $param;

}
function GetRoles($value)
{
	
	//prd($value);
	switch ($value) {
	case "1":
		$controller='Dashboard';
		$action='index';
		return array($controller,$action);
        break;
    case "2":
		$controller='static';
		$action='siteconfigs';
		return array($controller,$action);
        break;
	 case "3":
		$controller='slider';
		$action='index';
		return array($controller,$action);
        break;
	 case "4":
		$controller='static';
		$action='index';
		return array($controller,$action);
        break;
	case "5":
		$controller='user';
		$action='index';
		return array($controller,$action);
        break;
	case "6":
		$controller='subadmin';
		$action='index';
		return array($controller,$action);
        break;
	case "7":
		$controller='Subscription';
		$action='index';
		return array($controller,$action);
        break;
	case "8":
		$controller='instrument';
		$action='index';
		return array($controller,$action);
        break;
	
   
	
	
  
}
	
}

function GetActions($value)
{
		switch ($value) {
		case "1":
		$action_array=array('index');
		return $action_array;
		case "2":
		$action_array=array('siteconfigs');
		return $action_array;
		break;
		case "3":
		$action_array=array("index","add","edit","getmedia","_unlink_media_image","_handle_uploaded_image","remove");
		return $action_array;
		break;
		case "4":
		$action_array=array("index","showmailtemplates","editmailtemplate","gettemplates","index","add","edit","viewpage","getpages","removepages","contentblock","addblock","editcontentblock","viewblock","removeblock","getblocks","graphicmedia","addgraphicmedia","editgraphicmedia","deletegraphicmedia","_unlink_media_image","_handle_uploaded_image");
		return $action_array;
		break;
		case "5":
		$action_array=array("index","verified","blocked","checkemail","logintofront","account","password","getusers","addschooluser","schooluser","plan","teacheruser","studentuser","schoolsubadminuser","subscribed","viewreport","image","getschoolusers","getsubscribedusers","remove","_unlink_user_image","_handle_profile_image");
		return $action_array;
		break;	
		case "6":
		$action_array=array("index","verifiedsubadmin","blockedsubadmin","addsubadmin","getsubadmins","setstatus","removesubadmin","viewsubadmin");
		return $action_array;
		case "7":
		$action_array=array("index","addplan","getpalns","remove","_unlink_user_image","_handle_profile_image");
		return $action_array;
		break;	
		case "8":
		$action_array=array("index","add","getinstrument","instrumentname","remove","_unlink_user_image","_handle_profile_image");
		return $action_array;
		break;	
	
		
		
		}
		
}
function encrypt_decrypt($string)
 {
   
    $string_length=strlen($string);
    $encrypted_string=""; 
    /**
    *For each character of the given string generate the code
    */
    for ($position = 0;$position<$string_length;$position++){        
        $key = (($string_length+$position)+1);
        $key = (255+$key) % 255;
        $get_char_to_be_encrypted = SUBSTR($string, $position, 1);
        $ascii_char = ORD($get_char_to_be_encrypted);
        $xored_char = $ascii_char ^ $key;  //xor operation
        $encrypted_char = CHR($xored_char);
        $encrypted_string .= $encrypted_char;
    } 
    /**
    *Return the encrypted/decrypted string
    */
    return $encrypted_string;
 }
function myurl_decode($id)
{
$passstring=urldecode(stripslashes($id));
$decrypted_string= encrypt_decrypt($passstring);
$Tutors_id= $decrypted_string/12345;
return $Tutors_id;

}
 function DeleteDirfileupload($path)
{
 

   if (is_dir($path.'/thumbnail')) 
   {
  	 $image_source=$path.'/thumbnail';
  	 $dir12 = @ dir($image_source.'/');
   
    while (($file = $dir12->read()) !== false)
    {
     @unlink($image_source.'/'.$file);  
   }
   
   rmdir($image_source);
   }
   
   if (is_dir($path)) {
   $image_source1=$path;
   $dir12 = @ dir($image_source1.'/');
  
    while (($file = $dir12->read()) !== false)
    {
     @unlink($image_source1.'/'.$file);  
   }
   
   
   rmdir($image_source1);
   }
   
 
}

function shuffleThis($list) { 
    if (!is_array($list)) return $list; 

    $keys = array_keys($list); 
    shuffle($keys); 
    $random = array(); 
    foreach ($keys as $key) { 
        $random[$key] = $list[$key]; // CHANGE HERE that preserves the keys
    }
    return $random; 
}
function randomPassword() {
    $alphabet = "abcdefghijklmnopqrstuwxyzABCDEFGHIJKLMNOPQRSTUWXYZ0123456789";
    $pass = array(); //remember to declasre $pass as an array
    $alphaLength = strlen($alphabet) - 1; //put the length -1 in cache
    for ($i = 0; $i < 8; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass); //turn the array into a string
}
function shuffle_me($shuffle_me) { 
   $randomized_keys = array_rand($shuffle_me, count($shuffle_me)); 
   foreach($randomized_keys as $current_key) { 
       $shuffled_me[$current_key] = $shuffle_me[$current_key]; 
   } 
   return $shuffled_me; 
} 

/* Match Passwordss */
function match_old_password($value){
	$loggedUser = Zend_Session::namespaceGet(ADMIN_AUTH_NAMESPACE);
 	if(isset($loggedUser['storage']) and !empty($loggedUser['storage'])){
  		return $loggedUser['storage']->user_password === md5($value);
	};
	return false ;
}
function match_old_password_front($value){
	$logged_identity = Zend_Auth::getInstance()->getInstance();
	if($logged_identity->hasIdentity()){
		$logged_identity = $logged_identity->getIdentity();
		return $logged_identity->user_password === md5($value);
	}
	return false ;
}


function genratePassword($string){
	return $string." ".rand(1,999);
}
function formatImageName($images_name){
	
	return str_replace(
		array(
			"/"," ",
		),
		array(
			"-","-"
		),
		$images_name
		
	);
	
}

function getUserImage($user_image , $full_url = false){

	if($user_image!="" and file_exists(PROFILE_IMAGES_PATH."/".$user_image)){
		$image_url =$user_image;
	}else{
		$image_url = "default.png";
	}
	
	if(!$full_url){
		return $image_url; 
	}
			
	switch($full_url){
		
		case '60': return $image_url = HTTP_PROFILE_IMAGES_PATH."/60/$image_url"; break;
		case '160': return  $image_url = HTTP_PROFILE_IMAGES_PATH."/160/$image_url"; break;
		case 'thumb': return  $image_url = HTTP_PROFILE_IMAGES_PATH."/thumb/$image_url"; break;
		default: return  $image_url = HTTP_PROFILE_IMAGES_PATH."/thumb/$image_url"; break;
		
		
	}	
}






function loadClass($classpath){
	$cpath = str_replace("_", "/", $classpath);
	include( $classpath);
}


/* FourSquare API for Get Places  */

function FourSquare($lat ,$lng){
		
	$categories = array(
			"Casino"=>"4bf58dd8d48988d17c941735",
			"Multiplex"=>"4bf58dd8d48988d180941735",
			"Zoo"=>"4bf58dd8d48988d17b941735",
			"Burger Joint"=>"4bf58dd8d48988d16c941735",
			"Café"=>"4bf58dd8d48988d16d941735",
			"Coffee Shop"=>"4bf58dd8d48988d1e0931735",
			"Fast Food Restaurant"=>"4bf58dd8d48988d16e941735",
			"Restaurant"=>"4bf58dd8d48988d1c4941735",
			"Library"=>"4bf58dd8d48988d12f941735",
			"Hospital"=>"4bf58dd8d48988d196941735",
			"Bank"=>"4bf58dd8d48988d10a951735",
			"Department Store"=>"4bf58dd8d48988d1f6941735",
			"Wine Shop"=>"4bf58dd8d48988d119951735",
			"Gym"=>"4bf58dd8d48988d176941735",
			"Mall"=>"4bf58dd8d48988d1fd941735",
			"Bus Line"=>"4bf58dd8d48988d12b951735",
			"Train"=>"4bf58dd8d48988d12a951735",
		);
  	
	$catIds = implode(",",array_values($categories));
	
	$url = "https://api.foursquare.com/v2/venues/search?ll=$lat,$lng&categoryId=$catIds&oauth_token=KQXALOQX0PDLIDHH1PANCROEKH2MRPMVEGQFXN1P0D2XTHHC&v=20140101&radius=500&limit=50";
	
	$response = @file_get_contents($url); 
	
	$json = json_decode($response);
	 
	$other_stuffs = array();
	$i=0;
	foreach ($json->response->venues as $result){

		$temp_array = array();
		$place = $result ;
		$temp_array['name']=$place->name;
		$temp_array['latitude']=$place->location->lat;
		$temp_array['longitude']=$place->location->lng;
		
		if(isset($place->location->city))
			$temp_array['city']=$place->location->city;
		else
			$temp_array['city']="";
		
		$temp_array['category']= $place->categories[0]->shortName;
		
		switch($temp_array['category']){
			case "American": $icon_name="default_color.png"; break;
			case "Burgers": $icon_name="burger_color.png"; break;
			case "Bus": $icon_name="busstation_color.png"; break;
			case "Café": $icon_name="coffeeshop_color.png"; break;
			case "Cineplex": $icon_name="movietheater_color.png"; break;
			case "Coffee Shop": $icon_name="cafe_color.png"; break;
			case "Department Store": $icon_name="departmentstore_color.png"; break;
			case "Gastropub": $icon_name="casino_color.png"; break;
			case "Grocery Store": $icon_name="departmentstore_color.png"; break;
			case "Gym": $icon_name="gym_color.png"; break;
			case "Gym / Fitness": $icon_name="gym_color.png"; break;
			case "Hotel": $icon_name="financial_color.png"; break;
			case "Mall": $icon_name="mall_color.png"; break;
			case "Medical School": $icon_name="medical_color.png"; break;
			case "Tea Room": $icon_name="cafe_color.png"; break;
			default : $icon_name="waterpark_color.png"; break ;
		}
		
		$temp_array['icon']= $icon_name ;// "maps/default.png";//$icon_name; //$place->categories[0]->icon->prefix."32".$place->categories[0]->icon->suffix;
		$other_stuffs[] = $temp_array;
		$i++; 
	}    
		
	
	return $other_stuffs ; 
		
 		
}







function isImage($name,$type){
	
	switch($type){
		case "admin profile" :
  			if(!empty($name) and file_exists(ROOT_PATH."/".ADMIN_PROFILE."/".$name)){
				return $name;
			}else{
				return "avatar.png";
			}
			
		break ;
		case "" : break ;
		case "" : break ;
		case "" : break ;
		
	}
	
	
}

function blogCommentCount($blogId){
	$db = Zend_Registry::get('db');
	$totalComment=$db->select()->from('blog_comments',array('count(blog_comment_id)as totalcomment'))->join('users','user_id=blog_comment_user_id')->where('blog_comment_blog_id='.$blogId.' and blog_comment_status="1" ')->query()->fetch();
	return $totalComment['totalcomment'];
	
}

function MY_setLayout($layout){
	Zend_Layout::getMvcInstance()->setLayout($layout);
}


/* Return Difference Between Two Dates */

function getDiffrence($date1 ,$date2 , $in ='d'){
	
 	
  	$diff = abs(strtotime($date2) - strtotime($date1));
 	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor($diff / 60 /  60);
	$minites = floor($diff / 60 );
	
	switch($in){
  		case 'd':
 			$op_days = $years*365 + $months*30 + $days; 
			return $op_days ;
 			/* Return No of Days Between Two Days*/
		break;
		
		case 'h':
 			$hours = $hours % 24;
			
			if($minites!=0){
				return  $hours +1;
			}
			return $hours ;
		/* Return No of Hours After Day */
		break;
  	
	}



 	
	if($years){
		return $years." years " ;
	}
	elseif($months){
		return $months." months "; 
	}
	elseif($days){
		return $days." days ";
	}
	elseif($hours){
		return $hours." hours ";
	}
	else{
		return $minites." minites ";
	} 
	
	//printf("%d years, %d months, %d days\n", $years, $months, $days);
	
	//2 days
	
}




##----------------------##
## ** THEJAMSTOP **
##   change_to_language
##-----------------------##

function change_to_language($str){
	return $str; 
}

##----------------------##
## ** THEJAMSTOP **
##   saveThumbnail
##-----------------------##
function saveThumbnail( $file , $width , $height , $outputfile , $propotional=false , $corp=false , $priority='H' ,$oversize=false ){

	$use_linux_commands = false ;
	$oversize=false ;   
	

	/* For File  */
	$output = 'file' ;   
	$outputfile = ROOT_PATH."/".$outputfile;
    $info = @getimagesize($file);
    $image = '';

    $final_width = 0;
    $final_height = 0;
    list($width_old, $height_old) = $info;
 
    if (@$proportional) {
      if ($width == 0) $factor = $height/$height_old;
      elseif ($height == 0) $factor = $width/$width_old;
      else {
	  if($priority=='H'){$factor = max ( $width / $width_old, $height / $height_old);   }
	  else{ $factor = min ( $width / $width_old, $height / $height_old);   }}
 		
		
		if($oversize){
			$ratio=$width_old/ $height_old ;
			if($priority=='H'){
			
				
				
				
				 $final_height = round ($height );
				 $final_width =  round ($height *$ratio);
				 	
				 
			}else{
				 $final_width = round ($width);
				 $final_height = round ($width * $factor);	
			}
			 	
		
		}elseif($width_old > $width || $height_old > $height) { 
 			 $final_width = round ($width_old * $factor);
      		 $final_height = round ($height_old * $factor);		
		
		} 	else {
		$final_width = $width_old; 
		$final_height = $height_old;
		}
		
		
    
 
    }
    else {
	
		if($oversize){
		
			 $final_width =  $width  ;
      		 $final_height =  $height  ;		
		
		}else{
	
			  $final_width = ( $width <= 0 ) ? $width_old : $width;
			  $final_height = ( $height <= 0 ) ? $height_old : $height;
	  	}
    }


     switch ( $info[2] ) {
      case 1://case IMAGETYPE_GIF:
        $image = imagecreatefromgif($file);
      break;
      case 2://case IMAGETYPE_JPEG:
        $image = imagecreatefromjpeg($file);
      break;
      case 3://case IMAGETYPE_PNG:
        $image = imagecreatefrompng($file);
      break;
      default:
        return false;
    }
  
  
    $image_resized = imagecreatetruecolor( $final_width, $final_height );
 
    if ( ($info[2] == 1) || ($info[2] == 3) ) {  //if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
     
	  $trnprt_indx = imagecolortransparent($image);

      // If we have a specific transparent color
      if ($trnprt_indx >= 0) {
 
        // Get the original image's transparent color's RGB values
        $trnprt_color    = imagecolorsforindex($image, $trnprt_indx);
 
        // Allocate the same color in the new image resource
        $trnprt_indx    = imagecolorallocate($image_resized, $trnprt_color['red'], $trnprt_color['green'], $trnprt_color['blue']);
 
        // Completely fill the background of the new image with allocated color.
        imagefill($image_resized, 0, 0, $trnprt_indx);
 
        // Set the background color for new image to transparent
        imagecolortransparent($image_resized, $trnprt_indx);
 
 
      } 
      // Always make a transparent background color for PNGs that don't have one allocated already
      elseif ($info[2] == 3) { //elseif ($info[2] == IMAGETYPE_PNG) {

        // Turn off transparency blending (temporarily)
        imagealphablending($image_resized, false);
 
        // Create a new transparent color for image
        $color = imagecolorallocatealpha($image_resized, 0, 0, 0, 127);
 
        // Completely fill the background of the new image with allocated color.
        imagefill($image_resized, 0, 0, $color);
 
        // Restore transparency blending
        imagesavealpha($image_resized, true);
      }
    }
 
    imagecopyresampled($image_resized, $image, 0, 0, 0, 0, $final_width, $final_height, $width_old, $height_old);
 
  
 
  
    switch ( strtolower($output) ) {
      case 'browser':
        $mime = image_type_to_mime_type($info[2]);
        header("Content-type: $mime");
        $output = NULL;
      break;
      case 'file':
        $output = $outputfile;
      break;
      case 'return':
        return $image_resized;
      break;
      default:
      break;
    }
 
    switch ( $info[2] ) {
      case IMAGETYPE_GIF:
        imagegif($image_resized);
      break;
      case IMAGETYPE_JPEG:
        imagejpeg($image_resized, $output);
      break;
      case IMAGETYPE_PNG:
        imagepng($image_resized, $output);
      break;
      default:
        return false;
    }
}

##----------------------##
## ** THEJAMSTOP **
##   getAdminData
##-----------------------##
function getAdminData(){
 	$db = Zend_Registry::get('db');
 	return $db->select()->from('admin')->where('admin_id=1')->query()->fetch();
}


function isMobile($obj=false){
	
	$user_agent = new Zend_Http_UserAgent();
  	$bool = "Zend_Http_UserAgent_Mobile"==get_class($user_agent->getDevice());
	if($obj){
		if(!$bool){
			$obj->redirect('');
		}
	}
	return $bool ;
}



function getMonthName($date , $short = false){
 	$monthNames  = array("January", "February", "March", "April", "May", "June",  "July", "August", "September", "October", "November", "December" );
	$name  = $monthNames[date("m", strtotime($date))-1] ;
	if($short)
		return substr($name,0,3);
	return $name ;
}


function getDayName($date , $short = false){
  	switch(date("D", strtotime($date))){
 		case 'Mon': return ucwords('monday');
		case 'Tue': return ucwords('tuesday');
		case 'Wed': return ucwords('wednesday');
		case 'Thu': return ucwords('thursday');
		case 'Fri': return ucwords('friday') ;
		case 'Sat': return ucwords('saturday');
		case 'Sun': return ucwords('sunday');
		
	}
	
	return ;
 }

function getDMYFormat($data , $saperater="."){
	
	$timestamp = strtotime($data); 
	
	$format ="d".$saperater."m".$saperater."Y" ;
	
	$new_date = date($format , $timestamp);
	
	return $new_date ;
	
}

function showPostTime($date){
 	// 2 years, 3 months and 2 days format
	
	$date1 = $date;
	$date2 = date('Y-m-d H:i:s') ;
 	$diff = abs(strtotime($date2) - strtotime($date1));
 	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor($diff / 60 /  60);
	$minites = floor($diff / 60 );

 	
	if($years){
		return $years." years " ;
	}
	elseif($months){
		return $months." months "; 
	}
	elseif($days){
		return $days." days ";
	}
	elseif($hours){
		return $hours." hours ";
	}
	else{
		return $minites." minites ";
	} 
	
	//printf("%d years, %d months, %d days\n", $years, $months, $days);
	
	//2 days
	
}

function CommentPostedTime($date){
	
	$date1 = $date;
	$date2 = date('Y-m-d H:i:s') ;
 	$diff = abs(strtotime($date2) - strtotime($date1));
 	$years = floor($diff / (365*60*60*24));
	$months = floor(($diff - $years * 365*60*60*24) / (30*60*60*24));
	$days = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
	$hours = floor($diff / 60 /  60);
	$minites = floor($diff / 60 );

 	
	if($years){
		return $years." Year " ;
	}
	elseif($months){
		return $months." Month"; 
	}
	elseif($days){
		return $days." day ";
	}
	elseif($hours){
		return $hours." h ";
	}
	else{
		return $minites." m ";
	}

}



function setBlockDate($date){
	$monthNames  = array("January", "February", "March", "April", "May", "June",  "July", "August", "September", "October", "November", "December" );
	$name  = $monthNames[date("m", strtotime($date))-1] ;
	return 	date("d", strtotime($date)).' '.substr($name,0,3) ;
}

function getYear($date){
	return  date("Y", strtotime($date)) ;
}

function getCommentTime($date){
	return  date("D", strtotime($date)).' at '.date("g:i A", strtotime($date)) ;
}

function addNewPost($post_apv_id,$post_type){
 	$user  = isLogged(true);
	$user_id = $user->user_id ; 
 	$dataInsert = array();
	$dataInsert["post_user_id"] =$user_id;
	$dataInsert["post_apv_id"] =$post_apv_id;
 	$dataInsert["post_type"] =$post_type;
 	$db = Zend_Registry::get('db');
 	$db->insert('posts',$dataInsert);
}

function deletePost($post_apv_id,$post_type){
	$db = Zend_Registry::get('db');
	$condition = "post_apv_id = ".$post_apv_id." and post_type=".(int)$post_type."";
 	$db->delete('posts',$condition);
}

function countryName($countryId){
	$db = Zend_Registry::get('db');
	$countrydata=$db->query('select name from countries where country_id='.$countryId)->fetch();
 	return $countrydata['name'];
}



function isLogged($info=false)
{	
	$user = Zend_Auth::getInstance()->getIdentity();	 
	if($user){ 
		if($info) return $user  ;
		else return $user->user_id ;
	}else{
		return false;	
	}
}


function setHeaderBlock($rContent , $lContent="YOUR ACCOUNT"){
	$str = '<div class="second-container-heading"><div class="container"><div class="row-fluid"><h3>'.$lContent.'</h3>
			<h3 class="span3">'.$rContent.'</h3></div></div></div>';
	Zend_Registry::set('heading-block', $str); 

}

function getSeoUrl($str){
	$str=strtolower(trim($str)) ;	
	$str=str_ireplace(" ","-",$str) ;	
	return $str;
}



function addQueryString($qvar,$qvalue){
	$qa=explode("&",$_SERVER['QUERY_STRING'] ) ;
 	$query_string=array();	
	foreach($qa as $v){
		$vars=explode("=",$v) ;
		if($vars[0]!=$qvar && $vars[0]!=""){
			$query_string[]=@$vars[0]."=".@$vars[1];	
		}
		
	}
	$query_string[]=$qvar."=".$qvalue;	
	$qstr=implode("&",$query_string ) ;	
	return $qstr ;
	
}

function resetSeoUrl($str){
	 
	$str=str_ireplace("-"," ",$str) ;
	$str=str_ireplace(".html","",($str)) ; 	
	return ucwords($str);  
}
function setFlashErrorMessage($message){	 
	$registry = Zend_Registry::getInstance();
	$registry->set("flash_error",$message);
}

function getFileExtension($filename=''){	
	
	$ext=@array_pop(explode(".",$filename));
	return $ext ;
	
}
 function showDateOnly($dateTimeStr) 
{ 
    $array = explode(" ",$dateTimeStr); 
	return $array[0];
}

function formatDate($dateTimeStr){
	$dtobj=strtotime($dateTimeStr);
	$format=date("F j, Y",$dtobj) ;
	return $format ;
}
function formatDateTime($dateTimeStr){
	$dtobj=strtotime($dateTimeStr);
	$format=date("g:i a F j, Y ",$dtobj) ;
	return $format ;
}
function trimValues(&$value) 
{ 
    $value = trim($value); 
}

function shortString($str,$length=200,$addDots=false){
	$substr=substr($str,0,$length);
	
	if($addDots){return $substr.'...' ;}
	else{ return $substr ;}
}


function test_print($item, $key)
{
    echo "$key holds $item\n";
}


 function nicetime($date) { if(empty($date)) { return "No date provided"; }
	$periods = array("second", "minute", "hour", "day", "week", "month", "year", "decade");
	$lengths = array("60","60","24","7","4.35","12","10");
	$now = time();
	$unix_date = strtotime($date);
	if(empty($unix_date)) { return "Bad date"; }
	if($now > $unix_date) {
	$difference = $now - $unix_date;
	$tense = "ago";
	} else {
	$difference = $unix_date - $now;
	$tense = "from now";
	}
	for($j = 0; $difference >= $lengths[$j] && $j < count($lengths)-1; $j++) { $difference /= $lengths[$j]; }
	$difference = round($difference);
	if($difference != 1) { $periods[$j].= "s"; }
	return "$difference $periods[$j] {$tense}";
}



     function prn($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    function pr($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
    }

    function prd($var)
    {
        echo '<pre>';
        print_r($var);
        echo '</pre>';
        die;
    }

    function gcm($var)
    {
        if (is_object($var))
            $var = get_class($var);
        echo '<pre>';
        prn(get_class_methods($var));
        echo '</pre>';
    }

    function getActivationKey($string)
    {
        $key = md5($string);
        return $key;
    }

    function getClientBrowserName()
    {
        $u_agent = $_SERVER['HTTP_USER_AGENT'];
        $ub = '';
        if (preg_match('/MSIE/i', $u_agent))
        {
            $ub = "Internet Explorer";
        }
        elseif (preg_match('/Firefox/i', $u_agent))
        {
            $ub = "Mozilla Firefox";
        }
        elseif (preg_match('/Safari/i', $u_agent))
        {
            $ub = "Apple Safari";
        }
        elseif (preg_match('/Chrome/i', $u_agent))
        {
            $ub = "Google Chrome";
        }
        elseif (preg_match('/Flock/i', $u_agent))
        {
            $ub = "Flock";
        }
        elseif (preg_match('/Opera/i', $u_agent))
        {
            $ub = "Opera";
        }
        elseif (preg_match('/Netscape/i', $u_agent))
        {
            $ub = "Netscape";
        }

        return $ub;
    }

    function getClientOS()
    {
        $user_agent = $_SERVER['HTTP_USER_AGENT'];

        $os_platform = "Unknown OS Platform";

        $os_array = array (
            '/windows nt 6.2/i' => 'Windows 8',
            '/windows nt 6.1/i' => 'Windows 7',
            '/windows nt 6.0/i' => 'Windows Vista',
            '/windows nt 5.2/i' => 'Windows Server 2003/XP x64',
            '/windows nt 5.1/i' => 'Windows XP',
            '/windows xp/i' => 'Windows XP',
            '/windows nt 5.0/i' => 'Windows 2000',
            '/windows me/i' => 'Windows ME',
            '/win98/i' => 'Windows 98',
            '/win95/i' => 'Windows 95',
            '/win16/i' => 'Windows 3.11',
            '/macintosh|mac os x/i' => 'Mac OS X',
            '/mac_powerpc/i' => 'Mac OS 9',
            '/linux/i' => 'Linux',
            '/ubuntu/i' => 'Ubuntu',
            '/iphone/i' => 'iPhone',
            '/ipod/i' => 'iPod',
            '/ipad/i' => 'iPad',
            '/android/i' => 'Android',
            '/blackberry/i' => 'BlackBerry',
            '/webos/i' => 'Mobile'
        );

        foreach ($os_array as $regex => $value)
        {

            if (preg_match($regex, $user_agent))
            {
                $os_platform = $value;
            }
        }

        return $os_platform;
    }

    function loggedUser()
    {
        $x = new Zend_Auth_Storage_Session('Admin_Auth');
        $y = $x->read();
        //prd($y);
        if (empty($y))
            return 0;
        else
            return $y;
    }
	
	function loggedUserFront()
    {
    	$x = new Zend_Auth_Storage_Session('User_Auth');
    	$y = $x->read();
    	//prd($y);
    	if (empty($y))
    		return 0;
    	else
    		return (object)$y;
    }
	
	
  function checkIsUserLoggedIn($info=false)
    {	
        $user = Zend_Auth::getInstance()->getIdentity();	 
	  	if($user){ 
			if($info) return $user  ;
			else return $user->user_id ;
		}else{
			return false;	
		}
    }
	
	function get_url_contents($url){
 
        $crl = curl_init();
        $timeout = 5;
        curl_setopt ($crl, CURLOPT_URL,$url);
        curl_setopt ($crl, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt ($crl, CURLOPT_CONNECTTIMEOUT, $timeout);
        $ret = curl_exec($crl);
        curl_close($crl);
        return $ret;
}
 

 
