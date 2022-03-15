<?php
class Application_Model_Navigation extends Zend_Db_Table_Abstract
{
	protected $_name = 'navigation_menu' , $primary;
	
 	public function init(){
		$this->primary = "menu_id";
	}
	
	
	
	/* Add / Edit Page Information */
	public function add($data , $id = false   ){
		// ================ add  ==========
		date_default_timezone_set('America/Los_Angeles');	// PDT time
		// ================================
		
 		$data['menu_updated']=date('Y-m-d H:i:s');
		try{
		
 			if($id){
				$rows_affected = $this->update($data , $this->primary."=".$id);
				return (object)array("success"=>true,"error"=>false,"message"=>"Menu Successfully Updated","rows_affected"=>$rows_affected) ;
			}
 			$inserted_id = $this->insert($data);
			return (object)array("success"=>true,"error"=>false,"message"=>"New Menu Successfully Added to the database","inserted_id"=>$inserted_id) ;
 		}catch(Zend_Exception $e){
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
 		}
	}
	
	
	public function getMenu($menu_id , $join = false){
 
    		return $this->find($menu_id)->count()?$this->find($menu_id)->current()->toArray():false;
 	}
	
	
 	public function getContentBlock($page_id){
		$this->_name= "content_block";
   		return $this->find($page_id)->count()?$this->find($page_id)->current()->toArray():false;
 	}
	
	
	public function fetchBlocks(){
		$this->_name= "content_block";
		return $this->fetchAll()->toArray();	
	}
	
	
	public function getFooterMenu(){
		
		$result = $this->getAdapter()->select()->from($this->_name)
			//->join('pages','page_id=menu_page_id')
			->where("menu_status= ?",'1')
			->where("menu_show IN ('Footer','Both')")
			->query()
			->fetchAll();
			
		return $result ;
		
		
	}
	
	
	public function getHeaderMenu(){
		
		$all_menu = $this->select()
					->where("menu_status = ? ","1")
					->where("menu_show IN ('Header','Both')")
					->order('menu_parent_id')
					->query()
					->fetchAll();
		
		$menus = array();
		
		$prev_parent = "";
		/* All Menus Nested Array */
		
 		foreach($all_menu as $key=>$values){
			
			
			/* Case for All Root Menus Level #1 */
			if(!$values['menu_parent_id']){ /* Root Menu */
				$menus[$values['menu_id']] = array();
				$menus[$values['menu_id']]['menu']= $values;
 				continue;
			}
			
			
			
			/* case for all child values Level #2 */
			
			if(isset($menus[$values['menu_parent_id']])){
				if(!isset($menus[$values['menu_parent_id']]['submenu'])){
					 $menus[$values['menu_parent_id']]['submenu'] = array();
				}
 				$menus[$values['menu_parent_id']]['submenu'][$values['menu_id']] = $values;
 				continue;
			}
			
			
			
 			 
		}
		
   		return $menus;
		
		
		$result = $this->getAdapter()->select()->from($this->_name)
			//->join('pages','page_id=menu_page_id')
			->where("menu_status= ?",'1')
			->where("menu_show IN ('Footer','Both')")
			->query()
			->fetchAll();
			
		return $result ;
		
		
	}
	
	
	
	
	public function getParentMenuList(){
		$OptionsArr = array();
		$OptionsArr[0]['key'] = "0";
		$OptionsArr[0]['value'] = " No Parent ";
 		
		$k = 1;
		
		 
		
		foreach($this->fetchAll("menu_parent_id =0 ") as $values){
			$OptionsArr[$k]['key'] = $values['menu_id'];
			$OptionsArr[$k]['value'] = $values['menu_title'];
			$k++;
		}
		
  		return $OptionsArr;
 			
	}
	


	
	
}