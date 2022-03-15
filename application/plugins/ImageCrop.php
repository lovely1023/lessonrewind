<?php

/* Image manupulation script by varun
This script supports all image files such as JPEG, GIF, PNG etc.
It also maintains the transparecy of GIF and PNG files.
*/

class Application_Plugin_ImageCrop
{	

	/*
	 * Paramerts in Param Array
	 * 		source_path => String (Required)
	 *		destination_path => String (Optional) , Default - source_path
	 *		name => String (Required)
	 *		ratio => Bool (Optional) ,Default - Maintain the actual ratio true
	 *		relative_path => Bool (Optional)  Default - false ( Required Full Path of the soruce and destincation file )
	 *											true ( Rquired relative path with respect to root folder)
	 *		size => Integer (Optional) Default - 100
	 *		
	 */
   
	
	public static function uploadThumb($params=array()){

		$path_prefix = "";

		$destination_path = false ;
		
		if(isset($params['relative_path']) and $params['relative_path']){
			$path_prefix = ROOT_PATH;
		}
		
 		if(isset($params['source_path']) and $params['source_path']){
			$source_path = $path_prefix.$params['source_path']."/";
			if(isset($params['name']) and $params['name']){
				$source_image_path  =$source_path.$params['name'];
			}
		}
		
 		if(isset($params['destination_path']) and $params['destination_path']){
			$destination_path =  $path_prefix.$params['destination_path']."/";
			
			if(isset($params['name']) and $params['name']){
				$destination_image_path = $destination_path.$params['name'];
			}
		}
		
  		
		if(file_exists($source_image_path)){
			
 			
			if(!$destination_path){
  				$destination_path =  $source_path ."thumb/";
				$destination_image_path = $destination_path.$params['name'];
			}
			
   			if(!is_dir($destination_path)){
				mkdir($destination_path); 
			}
			
			
			
			
			$image_config =array("image"=>$source_image_path , "width"=>"100","height"=>100);
			
			if(isset($params['size'])){
				$image_config['width']= $image_config['height'] = $params['size'];
  			}
			
			if(isset($params['crop'])){
				$image_config['crop'] = $params['crop'] ;
  			}
			
			if(isset($params['ratio']) and $params['ratio']==false){
 				$image_config['noprop'] = true ;
			}
 			
			
			if(isset($params['width'])){
				$image_config['width']= $params['width'];
  			}
			
			if(isset($params['height'])){
				$image_config['height']= $params['height'];
  			}
			
			 
 			self::drawGdImage($destination_image_path,$image_config) ;
			
 			return $params['name'];
			 
		}
		
		return false; 
 		
	} 
 

	public static function drawGdImage($output_file=false,$_PARAMS = false){
	
 		$image_location = $_PARAMS['image']; // Image url set in the URL. ex: thumbit.php?image=URL
		
		
		/* default Setting  */
		$proportional = true ; 
		$priority='W';
		$oversize=false ; 
		$rotate = false; 
		$crop = false ;
		
		/* Required Settings */	
		if(isset($_PARAMS['noprop']))	$proportional=false;
		if(isset($_PARAMS['prt']))	$priority = strtoupper($_PARAMS['prt']); 
		if(isset($_PARAMS['oversize']))	$oversize=true;
		if(isset($_PARAMS['rotate']))	$rotate = $_PARAMS['rotate'];
		if(isset($_PARAMS['crop']))	$crop = $_PARAMS['crop'];
		
		
		$info = getimagesize($image_location);
		list($width_old, $height_old , $source_type) = $info;
		
	
		$width_constant = $width  = isset($_PARAMS['width'])?(int)$_PARAMS['width']: (int)$width_old; // Max thumbnail width.
		$height_constant = $height = isset($_PARAMS['height'])?(int)$_PARAMS['height']:  (int)$height_old;// Max thumbnail height.   
		
		
		
		$final_width = 0;
		$final_height = 0;
		
		
		if($crop){
		 
			
			 
			switch ($source_type) {
				case IMAGETYPE_GIF:
					$source_gdim = imagecreatefromgif($image_location);
					break;
				case IMAGETYPE_JPEG:
					$source_gdim = imagecreatefromjpeg($image_location);
					break;
				case IMAGETYPE_PNG:
					$source_gdim = imagecreatefrompng($image_location);
					break;
			}
			
			
			
			
			$source_aspect_ratio = $width_old / $height_old;
			$desired_aspect_ratio = $width_constant / $height_constant;
			
			
			if ($source_aspect_ratio > $desired_aspect_ratio) {
				/*
				 * Triggered when source image is wider
				 */
				$temp_height = $height_constant;
				$temp_width = ( int ) ($height_constant * $source_aspect_ratio);
			} else {
				/*
				 * Triggered otherwise (i.e. source image is similar or taller)
				 */
				$temp_width = $width_constant;
				$temp_height = ( int ) ($width_constant / $source_aspect_ratio);
			}
			
			/*
			 * Resize the image into a temporary GD image
			 */
			 
		 
			$temp_gdim = imagecreatetruecolor($temp_width, $temp_height);
			
			if ($info[2] == 3) {  
				// Turn off transparency blending (temporarily)
				imagealphablending($temp_gdim, false);
				
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($temp_gdim, 0, 0, 0, 127);
				
				// Completely fill the background of the new image with allocated color.
				imagefill($temp_gdim, 0, 0, $color);
				
				// Restore transparency blending
				imagesavealpha($temp_gdim, true);
			}
		
		
			imagecopyresampled(
				$temp_gdim,
				$source_gdim,
				0, 0,
				0, 0,
				$temp_width, $temp_height,
				$width_old, $height_old
			);
			
			/*
			 * Copy cropped region from temporary image into the desired GD image
			 */
			
			$x0 = ($temp_width - $width_constant) / 2;
			$y0 = ($temp_height - $height_constant) / 2;
			
			$desired_gdim = imagecreatetruecolor($width_constant, $height_constant);
			
			
			
			if ($info[2] == 3) { 
			
				// Turn off transparency blending (temporarily)
				imagealphablending($desired_gdim, false);
				
				// Create a new transparent color for image
				$color = imagecolorallocatealpha($desired_gdim, 0, 0, 0, 127);
				
				// Completely fill the background of the new image with allocated color.
				imagefill($desired_gdim, 0, 0, $color);
				
				// Restore transparency blending
				imagesavealpha($desired_gdim, true);
			}
		
		
		
			imagecopy(
				$desired_gdim,
				$temp_gdim,
				0, 0,
				$x0, $y0,
				$width_constant, $height_constant
			);
			
			/*
			 * Render the image
			 * Alternatively, you can save the image in file-system or database
			 */
			
 			switch ($info[2]) {
			  case IMAGETYPE_GIF:
				imagegif($desired_gdim);
			  break;
			  case IMAGETYPE_JPEG:
				imagejpeg($desired_gdim, $output_file ,80);
			  break;
			  case IMAGETYPE_PNG:
				imagepng($desired_gdim, $output_file);
			  break;
			  default:
				return false;
			}
			 return true  ;
		}else{
	
			if ($proportional){
				
				if($priority=='H'){
					$factor = max ( $width / $width_old, $height / $height_old);  
				}else{ 
					$factor = min ( $width / $width_old, $height / $height_old);   
				}
		
				if($oversize){
					
					$ratio = $width_old /$height_old ;
							
					if($priority=='H'){
						 $final_height = round ($height);
						 $final_width =  round ($height *$ratio);
					}else{
						 $final_width = round ($width);
						 $final_height = round ($width * $factor);	
					}
						
				
				}elseif($width_old > $width || $height_old > $height) { 
					 $final_width = round ($width_old * $factor);
					 $final_height = round ($height_old * $factor);		
				
				}else {
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
					
		}
		
		
	  
		switch ( $info[2] ) {
		 case IMAGETYPE_GIF:
			$image = imagecreatefromgif($image_location);
		  break;
		 case IMAGETYPE_JPEG:
			$image = imagecreatefromjpeg($image_location);
		  break;
		 case IMAGETYPE_PNG:
			$image = imagecreatefrompng($image_location);
		  break;
		  default:
			return false;
		}
	
	  
	  
		
		if($rotate){
			$image = imagerotate($image, $rotate, 0,-1);	
		}
	
	  
		$image_resized = imagecreatetruecolor( $final_width, $final_height );
		
		
		
		
		if(($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
			
			
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
	 
	
	   
			switch ( $info[2] ) {
			  case IMAGETYPE_GIF:
				imagegif($image_resized);
			  break;
			  case IMAGETYPE_JPEG:
				imagejpeg($image_resized, $output_file ,75);
			  break;
			  case IMAGETYPE_PNG:
				imagepng($image_resized, $output_file);
			  break;
			  default:
				return false;
			}
			
 	}
 	
	
	
	
	
	public static function uploadImageAndThumb($src_file,$dest_path,$new_file_name,$thumbnail=true){

			$destination_file_path=$dest_path."".$new_file_name;

			if(move_uploaded_file($src_file ,$destination_file_path)){
				
				if($thumbnail){
					$image_locationExt=getFileExtension($new_file_name);
					if(!is_dir($dest_path."thumbs/")){ mkdir($dest_path."thumbs/"); }
					$thumbnail_file=	$dest_path."thumbs/".$new_file_name;//		str_ireplace($image_locationExt,"_thumb".$image_locationExt,$new_file_name);
				
					$params=array("image"=>$destination_file_path,"width"=>"100","height"=>100);
					self::drawGdImage($thumbnail_file,$params) ;
				}
				
				return $new_file_name ;
			}else{
				return false ;
			}

	
} 



	
 }
 
 
?>