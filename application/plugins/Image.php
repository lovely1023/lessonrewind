<?php
/* 
	Image manupulation script by varun
	This script supports all image files such as JPEG, GIF, PNG etc.
	It also maintains the transparecy of GIF and PNG files.
*/

class Application_Plugin_Image
{	

	/*
	 *	Univeral Image Uploader
	 */
	public function universal_upload($options = array()){
	
  		$return_single_image = true ;
		
		if(isset($options['multiple']) and $options['multiple']){
			$return_single_image = false ; 
 		}
		
		if(!isset($options['directory']) or !is_dir($options['directory']))
			return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"No Such Directory Exists ",'files_upload'=>0,'media_path'=>array());
			
 		$uploaded_image_names = array();
	 
		$adapter = new Zend_File_Transfer_Adapter_Http();
	
		$files = $adapter->getFileInfo();
  		 
		$uploaded_image_names = array();
		
		$new_name = false; 
   		
		foreach ($files as $file => $info) { /* Begin Foreach for handle multiple images */
		 
  			$name_old = $adapter->getFileName($file);
			
			if(empty($name_old)){
				continue ;			
			}
			
			$file_title  = $adapter->getFileInfo($file);
			
			
			if($file_title[$file]['size']==0)
				continue; 
				
			$file_title = $file_title[$file]['name']; 
				
  			$uploaded_image_extension = getFileExtension($name_old);
			
 			$file_title  = str_replace(".".$uploaded_image_extension,"",$file_title);
			
			$file_title = formatImageName($file_title);
  
 			$new_name = $file_title."-".time()."-".rand(1,100000).".".$uploaded_image_extension;
 			
  			$adapter->addFilter('Rename',array('target' => $options['directory']."/".$new_name));
			
			try{
				$adapter->receive($file);
			}
			catch(Zend_Exception $e){
				return (object) array('success'=>false,"error"=>true,'exception'=>true,'message'=>$e->getMessage(),'exception_code'=>$e->getCode()) ;
			}
			
			$thumb_config = array("source_path"=>$options['directory'],"name"=> $new_name);
			
			if(isset($options['thumbs']) and is_array($options['thumbs'])){
				
				foreach($options['thumbs'] as $key=>$value){
					
					$width = isset($value['size'])?$value['size']:(isset($value['width'])?$value['width']:"220");
					$height = isset($value['height'])?$value['height']:(isset($value['ratio'])?$value['ratio']*$width:"330");
					Application_Plugin_ImageCrop :: uploadThumb(	array_merge($thumb_config,
							array(
								"width"=>$width,
								"height"=>$height,
								"crop"=>true,
								"ratio"=>false,
								"destination_path"=>$options['directory']."/$key")
							));
				}
				
 			}else{
				
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>$options['directory']."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>$options['directory']."/160","crop"=>true ,"size"=>160,"ratio"=>false)));	
			}
			
 			if(!$return_single_image){
				$uploaded_image_names[$file]=array('media_path'=>$new_name,'element'=>$file,'name'=>$file_title); //=> For Multiple Images
			}
  				
   		
		}/* End Foreach Loop for all images */
		
		if(!$return_single_image){
			return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$uploaded_image_names) ;
		}
			
		return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$new_name) ;
 	}
	
	
	
 	
	
	
	/* 
	 *	Universal Unlink Image
	 */
	public function universal_unlink($image_name , $options = array()){
		
		if(empty($image_name))
			return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"Image Name is Empty",'files_unlink'=>0);
		
		if($options!="" and !is_array($options)){
			$options = array('directory'=>$options) ;
 		}
 		
		if(!isset($options['directory']) or !is_dir($options['directory']))
			return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"No Such Directory Exists ",'files_unlink'=>0);
			
		 
   		$directory = new RecursiveDirectoryIterator($options['directory']);
		$flattened = new RecursiveIteratorIterator($directory);
		
		 
		// Make sure the path does not contain "/.Trash*" folders and ends eith a .php or .html file
		//$files = new RegexIterator($flattened, '#^(?:[A-Z]:)?(?:/(?!\.Trash)[^/]+)+/[^/]+\.(?:php|html|jpg)$#Di');
		
		$image_name = str_replace(array("(",")"),array("\(","\)"),$image_name);
		
  		$files = new RegexIterator($flattened, "/$image_name/");
		
 		$unlink_count = 0;
		
		foreach($files as $file) {
 			unlink($file);
 			$unlink_count++;
		}
 		
		return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"Images are successfully removed",'files_unlink'=> $unlink_count );
		
	}
	
	
	/* 
	 *	Universal Unlink Image
	 */
	public function universal_rename($old_name , $new_name, $options = array()){
		
		if(empty($old_name) or empty($new_name))
			return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"Old Name and New Name is Empty",'files_unlink'=>0);
		
		if($options!="" and !is_array($options)){
			$options = array('directory'=>$options) ;
 		}
 		
		if(!isset($options['directory']) or !is_dir($options['directory']))
			return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"No Such Directory Exists ",'files_unlink'=>0);
			
		 
   		$directory = new RecursiveDirectoryIterator($options['directory']);
		$flattened = new RecursiveIteratorIterator($directory);
		
		 
		// Make sure the path does not contain "/.Trash*" folders and ends eith a .php or .html file
		//$files = new RegexIterator($flattened, '#^(?:[A-Z]:)?(?:/(?!\.Trash)[^/]+)+/[^/]+\.(?:php|html|jpg)$#Di');
 		$image_name = str_replace(array("(",")"),array("\(","\)"),$old_name);
		
  		$files = new RegexIterator($flattened, "/$image_name/");
		
 		$unlink_count = 0;
		
		foreach($files as $file) {
			
		//	pr($file);
 			//unlink($file);
 			$unlink_count++;
		}
		
		//prd("Sda");
 		
		return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"Images are successfully removed",'files_unlink'=> $unlink_count );
		
	}
	
	
	
	
	public function simple_rename($old_name , $new_name, $options = array()){
		
		if(empty($old_name) or empty($new_name))
			return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"Old Name and New Name is Empty",'files_unlink'=>0);
		
		if($options!="" and !is_array($options)){
			$options = array('directory'=>$options) ;
 		}
 		
		if(!isset($options['directory']) or !is_dir($options['directory']))
			return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"No Such Directory Exists ",'files_unlink'=>0);
			
		 
  		
		if(file_exists($options['directory']."/".$old_name)){
 			rename($options['directory']."/".$old_name,$options['directory']."/".$new_name);
 		}
  		
		return (object)array('success'=>true,'error'=>false,'exception'=>false ,'message'=>"Images are successfully removed",'files_rename'=>1);
		
	}
	
	
	
 
	/*
	 *	Universal Upload Image Thumbs
	 */
	public function univeral_upload_thumb(){
		
		
		
	}
	
	
	
	/*
	 *	Universal Crop Image 
	 *	Author - Varun Sharma
	 *
	 *	Paramerter
	 *		IN - size [Width and Height will set accoding to the Size ] - INT - Optional - Default => (160)
	 *			 width [ Width of Required Image ] - INT  - Optional 
	 *			 height [ Height of Required Image ] - INT  - Optional 
	 *			 source_directory [Source Directory Name]  - STRING - Required 
	 *			 destination [Source Directory Name] - STRING - Required 
	 *			 _w [ Width of Croppping Frame ] - INT - Required  
	 *			 _h [ Height of Croppping Frame ] - INT - Required 
	 *			 _x [ X - Co-ordindate Offset ] - INT - Required 
	 *			 _y [ X - Co-ordindate Offset ]	- INT - Required 
	 *
	 *	[Sample Array]
			array(
				"source_directory" => PROFILE_IMAGES_PATH,
				"name"=>"varun_kumar.jpg",
				"target_name"=>"bunty.jpg",
				'_w'=>$posted_data['w'],
				'_h'=>$posted_data['h'],
				'_x'=>$posted_data['x'],
				'_y'=>$posted_data['y'],
   			)
			
			
		array(
				"source_directory" => PROFILE_IMAGES_PATH,
				"name"=>"varun_kumar.jpg",
				'_w'=>$posted_data['w'],
				'_h'=>$posted_data['h'],
				'_x'=>$posted_data['x'],
				'_y'=>$posted_data['y'],
				'destination'=>array(
					"60"=>array(
						"size"=>60,
						"target_name"=>"bunty.jpg"
					),
					"160"=>array(
						"size"=>160
					),
					"thumb"=>array(
						"size"=>300
					)
				)
 			);		
	*/
	public function universal_crop_image($options = array()){
		
		
		/* Parameters Validation */
		if(!isset($options['_w']) or !isset($options['_h']) or !isset($options['_x']) or !isset($options['_y']) or !isset($options['source_directory']) or !isset($options['name'])){
			return (object)array('success'=>false , 'error'=>true ,'message'=>"Missing Required Arguments , Please Check Again!");
		}
		
 		/* Setup For Default Values  */
		!isset($options['quality'])?$options['quality']=75:"";
		!isset($options['target_name'])?$options['target_name']=$options['name']:"";
		!isset($options['destination'])?$options['relative_path']=true:"";
		!isset($options['destination'])?$options['destination']="":"";
	 
 		 
 		if(is_array($options['destination'])){
			
			foreach($options['destination'] as $key=>$values){
				
				$width = $height = 160 ;
				$target_name = $options['target_name'] ;
							
				if(isset($options['relative_path']) and $options['relative_path']==false){
					$destination_path = $values['destination'];
				}else{
					$destination_path = $options['source_directory'].'/'.$key ;
				}
				
				if(isset($values['size']) and !empty($values['size'])){
					$width = $height = $values['size'] ;
 				}
				
 				if(isset($values['width']) and !empty($values['width'])){
					$width = $values['width'] ;
 				}
				
				if(isset($values['height']) and !empty($values['height'])){
					$height = $values['height'] ;
 				}
				
				if(isset($values['target_name']) and !empty($values['target_name'])){
					$target_name = $values['target_name'] ;
 				}
				
				 
  				$this->_crop(array(
								'_w'=>$options['_w'],
								'_h'=>$options['_h'],
								'_x'=>$options['_x'],
								'_y'=>$options['_y'],
								'quality'=>$options['quality'],
								'source_path'=>$options['source_directory']."/".$options['name'],
								'destination_path'=>$destination_path,
								'target_name'=>$target_name,
								'width'=>$width,
								'height'=>$height
 							)
						);
  			}
   		}else{
			
				$width = $height = 160 ;
				$target_name = $options['target_name'] ;
							
				if(isset($options['relative_path']) and $options['relative_path']==false){
					$destination_path = $options['destination'];
				}else{
					$destination_path = $options['source_directory'].'/' ;
				}
				
				if(isset($options['size']) and !empty($options['size'])){
					$width = $height = $options['size'] ;
 				}
				
 				if(isset($options['width']) and !empty($options['width'])){
					$width = $options['width'] ;
 				}
				
				if(isset($options['height']) and !empty($options['height'])){
					$height = $options['height'] ;
 				}
				
				if(isset($options['target_name']) and !empty($options['target_name'])){
					$target_name = $options['target_name'] ;
 				}
				
			
			if(isset($options['relative_path']) and $options['relative_path']==false){
				$destination_path = $options['destination'];
			}else{
				$destination_path = $options['source_directory'].'/'.$options['destination'] ;
			}
			
			$this->_crop(array(
					'_w'=>$options['_w'],
					'_h'=>$options['_h'],
					'_x'=>$options['_x'],
					'_y'=>$options['_y'],
					'quality'=>$options['quality'],
					'source_path'=>$options['source_directory']."/".$options['name'],
					'destination_path'=>$destination_path,
					'target_name'=>$target_name,
					'width'=>$width,
					'height'=>$height
				)
			);
			
 		}
		
 		return (object)array('success'=>true , 'error'=>false  ,'message'=>"Image(s) Croped Successfully !");

 		
	}
	
	
	
	/* Perform Crop Operation */
	private function _crop($options){
	 		
  		
   		$image_source_path = $options['source_path'];
		$image_destination_path = $options['destination_path']."/".$options['target_name'] ;
		
		$dst_r = ImageCreateTrueColor(  $options['width'], $options['height'] );
		
		list($imagewidth, $imageheight, $imageType) = getimagesize($image_source_path);
		
		$imageType = image_type_to_mime_type($imageType);
		
		switch($imageType) {
			case "image/gif":$source = imagecreatefromgif($image_source_path);break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":$source = imagecreatefromjpeg($image_source_path);break;
			case "image/png":
			case "image/x-png":$source = imagecreatefrompng($image_source_path); break;
		}
		
		
		imagecopyresampled($dst_r,$source,0,0,$options['_x'],$options['_y'],$options['width'],$options['height'],$options['_w'],$options['_h']);

		switch($imageType) {
			case "image/gif":imagegif($dst_r,$image_destination_path);break;
			case "image/pjpeg":
			case "image/jpeg":
			case "image/jpg":imagejpeg($dst_r, $image_destination_path , $options['quality']);break;
			case "image/png":
			case "image/x-png": imagepng($dst_r, $image_destination_path); break;
		}
			
		
		return true ;
		
		
	}
	
	
	
	public function universal_image_show(){
		
		
		
	}
	
}
?>