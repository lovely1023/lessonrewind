<?php
class AjaxController extends Zend_Controller_Action
{
 
    public function init(){ 
		
	}
	
 
  
  
	public function setstatusAction() {
		 
		 
		 $params = $this->getRequest()->getParams() ;
 		 
 		 $db=Zend_Registry::get("db") ;
		 
		 $data = array(			
					 
					
					
					"Classes"=>array(
					'table'      => "Classes",
					'field_id'      => "class_id",
					'field_status'      => 'class_template'),
					"users"=>array(
					'table'      => "users",
					'field_id'      => "user_id",
					'field_status'      => 'user_status'),
					"Instruments"=>array(
					'table'      => "Instruments",
					'field_id'      => "Instrument_id",
					'field_status'      => 'Instrument_active'),
			
					"lesson_student"=>array(
					'table'      => "lesson_student",
					'field_id'      => "l_s_id",
					'field_status'      => $params['fieldstatus']),
				
					
				 
					
  			);
			
		 	if(isset($data[$params['type']])){
				$update_data=array($data[$params['type']]['field_status']=>$params['status']) ;
				try{
					$updated = $db->update($data[$params['type']]['table'], $update_data, $data[$params['type']]['field_id'].' = '.$params['id']);	
					echo json_encode(array("success"=>true,"error"=>false,"message"=> " Status of ".ucwords(str_replace("_"," ",$data[$params['type']]['field_status']))." Successfully Updated "  ));
				}catch(Zend_Exception $e){
					echo json_encode(array("success"=>false,"error"=>true,"exception"=>true,"exception_code"=>$e->getCode(),"message"=>$e->getMessage() ));
				}
			}else{
				echo json_encode(array("success"=>false,"error"=>true,"exception"=>false,"message"=>"Table Not Defined for the Current Request" ));
			}
			

				
			
 			exit();
	}
	
 	
}

