<?php
class Application_Model_slider extends Zend_Db_Table_Abstract
{
	protected $_name = 'slider_images';
	private $primary ="slider_image_id";
	
	
 	public function init(){
		 
	}
	
	
	
	public function add($data , $id = false){
		global $mySession ; 

  		try{
			if($id){
				$updated_record  = $this->update($data,$this->primary."=".$id);
				return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Updated","row_affected"=>$updated_record) ;
 			}else{
				$insertedId = $this->insert($data);
				return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Inserted","inserted_id"=>$insertedId) ;
 			}
		}catch(Zend_Exception $e){
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
		}
		
  	}
	
 	
	
	public function getMedia($id){
		$media = $this->find($id);
		
		if($media->count())
			return $media->current()->toArray();
		
		return false; 
		
		
	}
	
	
  
 	
 	public function getCategoryList(){
		
		$this->_name = "media_category";
		$this->primary = "media_category_id";
		
 		
 		$OptionsArr = array();
		$OptionsArr[0]['key'] = "";
		$OptionsArr[0]['value'] = "Select Category";
 		
		$k = 1;
		
		foreach($this->fetchAll() as $values)
		{
			$OptionsArr[$k]['key'] = $values['media_category_id'];
			$OptionsArr[$k]['value'] = $values['media_category_name'];
			$k++;
		}
		
 		return $OptionsArr;
	}
	
	
	
	public function getGallery($id){
		
		$gallery = $this->find($id);
		
		if($gallery->count())
			return $gallery->current()->toArray();
		
		return false; 
			
	}
	
	
	public function fetchImages($param = array()){
		
		$gallery = $this->getAdapter()->select()
					->from('slider_images')
					->where('slider_image_status= "1"')
					->order('rand()')->query()->fetchAll();
					
		return $gallery; 
		
		
	}
	
	
	
	public function unlink_image($id){
		
		$media_info = $this->getMedia($id);
		
		if(!$media_info)
			return false ;
 		 
		if($media_info['slider_image_path']!="" and file_exists(SITE_ROOT_DIR.MEDIA."/".$media_info['slider_image_path'])){
			unlink(SITE_ROOT_DIR.MEDIA."/".$media_info['slider_image_path']);
  		}
		
		return true ;
		
	}
	
 	
	

}