<?php
/* 
	Image manupulation script by varun
	This script supports all image files such as JPEG, GIF, PNG etc.
	It also maintains the transparecy of GIF and PNG files.
*/

class Application_Plugin_PluginCustomizer
{	

	/*
 	 *	Unlink Customization Image 
	 */ 
   public function unlink_customizer_image($image_name,$options = array()){
		
		if(empty($image_name))
			return true;
		
		/*Path array*/
		if(isset($options['type']) and $options['type']){
		 		switch($options['type']){
 					case 'shirt': $pathArr = array("directory_path"=>SHIRT_CUST_IMAGES_PATH,"http_path"=>HTTP_SHIRT_CUST_IMAGES_PATH);
					case 'jacket': $pathArr = array("directory_path"=>JACKET_CUST_IMAGES_PATH,"http_path"=>HTTP_JACKET_CUST_IMAGES_PATH); break ;

				}
		}
		/*Path array*/
		
 		if(file_exists($pathArr['directory_path']."/".$image_name)){
			unlink($pathArr['directory_path']."/".$image_name);
		}
		if(file_exists($pathArr['directory_path']."/thumb/".$image_name)){
			unlink($pathArr['directory_path']."/thumb/".$image_name);
		}
		 
 		if(file_exists($pathArr['directory_path']."/60/".$image_name)){
			unlink($pathArr['directory_path']."/60/".$image_name);
		}
		if($pathArr['directory_path']."/160/".$image_name){
			unlink($pathArr['directory_path']."/160/".$image_name);
		}
 		return true; 
	}
	
 	
	/*Upload Category Images*/
	public function upload_customization_image($options = array()){
		
 		global $mySession; 
		
		$return_single_image = false ;
		
		if(isset($options['single']) and $options['single']){
			$return_single_image = true; 
 		}
		
		
		/*Path array*/
		if(isset($options['type']) and $options['type']){
			switch($options['type']){
				case 'shirt': $pathArr = array("directory_path"=>SHIRT_CUST_IMAGES_PATH,"http_path"=>HTTP_SHIRT_CUST_IMAGES_PATH); break ;
				case 'jacket': $pathArr = array("directory_path"=>JACKET_CUST_IMAGES_PATH,"http_path"=>HTTP_JACKET_CUST_IMAGES_PATH); break ;
			}
 		}
		/*Path array*/
		
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
			
			$file_title = $file_title[$file]['name']; 
				
  			$uploaded_image_extension = getFileExtension($name_old);
			
 			$file_title  = str_replace(".".$uploaded_image_extension,"",$file_title);
			
			$file_title = formatImageName($file_title);
  
 			$new_name = $file_title."-".time()."-".rand(1,100000).".".$uploaded_image_extension;
 			
  			$adapter->addFilter('Rename',array('target' => $pathArr['directory_path']."/".$new_name));
			
			 
		
			try{
				$adapter->receive($file);
			}
			catch(Zend_Exception $e){
				return (object) array('success'=>false,"error"=>true,'exception'=>true,'message'=>$e->getMessage(),'exception_code'=>$e->getCode()) ;
			}
			
			$thumb_config = array("source_path"=>$pathArr['directory_path'],"name"=> $new_name);
 			Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
			Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>$pathArr['directory_path']."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
			Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>$pathArr['directory_path']."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
			
			
			if(!$return_single_image){
				$uploaded_image_names[$file]=array('media_path'=>$new_name,'element'=>$file); //=> For Multiple Images
			}
  				
   		
		}/* End Foreach Loop for all images */
		
			if(!$return_single_image){
				return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$uploaded_image_names) ;
			}
			
			
			return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$new_name) ;
			
   	 
 	}
    
	


}
?>