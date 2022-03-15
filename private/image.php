<?php
/* Image manupulation script by Amit Sharma 
This script supports all image files such as JPEG, GIF, PNG etc.
It also maintains the transparecy of GIF and PNG files.
*/


$proportional = true ; 
$output = 'browser' ; 
$priority='H' ;
$use_linux_commands = false ;   
$oversize=false ;   

$file 	= $_GET['image']; // Image url set in the URL. ex: thumbit.php?image=URL
$width  = $_GET['width']; // Max thumbnail width.
$height = $_GET['height'];// Max thumbnail height.

   
if(isset($_GET['noprop'])){$proportional=false ; }
 
 if(isset($_GET['savefile'])){$output = 'file' ;  $outputfile =  $_GET['savefile'];}

if(isset($_GET['oversize']) || isset($_GET['os'])){$oversize=true ; } 
 
 
if(isset($_GET['prt'])){
if($_GET['prt']=='H' || $_GET['prt']=='h'){$priority='H' ;}
if($_GET['prt']=='W' || $_GET['prt']=='w'){$priority='W' ;}
} 
 
    $info = getimagesize($file);
    $image = '';

    $final_width = 0;
    $final_height = 0;
    list($width_old, $height_old) = $info;
 
    if ($proportional) {
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
 
    if ( ($info[2] == 1) || ($info[2] == 3) ) { //if ( ($info[2] == IMAGETYPE_GIF) || ($info[2] == IMAGETYPE_PNG) ) {
     
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
 
    
   
?>