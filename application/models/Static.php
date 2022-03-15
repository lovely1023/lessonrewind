<?php
class Application_Model_Static extends Application_Model_SuperModel
{
	 
	
	/* Add / Edit Page Information */
	public function add($table_name,$data,$id=false){
 		
		$this->_name= $table_name;
		
		try{
 			
 			if($id){

				$rows_affected = $this->update($data ,$id);

				return (object)array("success"=>true,"error"=>false,"message"=>"Content Successfully Updated","rows_affected"=>$rows_affected) ;
			}
 			
			$inserted_id = $this->insert($data);
			
			return (object)array("success"=>true,"error"=>false,"message"=>"New Page Successfully Added to the database","inserted_id"=>$inserted_id) ;
			
 		}catch(Zend_Exception $e){
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
 		}
	}
	 
	 
	public function getPage($id){
		
		$this->_name = "pages";
		
		return $this->select()->where("page_id = ? ",$id)->query()->fetch();
		
		
		
		
	}
	
	public function getContentBlock($id = false){
		
		$this->_name = "content_block";
		
		if($id){
			return $this->select()->where("content_block_id = ? ",$id)->query()->fetch();
		}
		
		return $this->select()->query()->fetchAll();
		
 	}
	
	
	
	/* ================= Static Functions Related To Site Config Table =============================== */
	
	public function getConfigs($type=false){
		$this->_name = "config";
		$where = "1";
		if($type){
			$where = "config_group='".strtoupper($type)."'";
		}
		return $this->select()->where($where)->query()->fetchAll();
	}
	
	
	
	
	/* /////////////////////////////////// END === > Static Functions Related To Site Config Table \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\*/
	
	
	
	/* ================= Static Functions Related To Graphic Media Table =============================== */
	
	public function getMedia($option = false){
		
		$this->_name = "graphic_media";
		
		$result = $this->select();
		
 		if($option){
			$where = "media_id=$option";
			return  $result->where(" media_id = ? ",$option )->query()->fetch();
		}
		 
		return $result->query()->fetchAll();
	}
	
	
	
	
	/* /////////////////////////////////// END === > Static Functions Related To Site Config Table \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\*/
	
	
	
	/* ================= Static Functions Related To Email Templates Table =============================== */
   	public function  getTemplate($param = false){
		$this->_name = "email_templates";
 		if($param){
			if(is_array($param)){
			}else{
				$result = $this->find($param);
				if($result->count())
					return $result->current()->toArray();
				return false ;
			}
		}
	}
	
	/* /////////////////////////////////// END === > Static Functions Related To Email Templates Table \\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\\*/
	

	
	 
	
	   
	
}