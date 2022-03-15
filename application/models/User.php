<?php
class Application_Model_User extends Zend_Db_Table_Abstract
{
 	protected $_name = 'users' , $primary ;
	private $modelEmail ;
	
	
	public function init(){		
 		
		$this->primary = "user_id";
		
		$this->modelEmail = new Application_Model_Email();
		
	}
	
 	/* 	Add / Update User Information 
	 *	@
	 *  Author  - Varun
	 */
 	public function add($data , $id = false){	

		try{
			
			if($id){
				$updated_records = $this->update($data , $this->primary."=".$id);
				return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Updated","row_affected"=>$updated_records) ;
			}
			
			$insertedId = $this->insert($data); 
			
 			$reset_password_key = md5($insertedId."!@#$%^$%&(*_+".time());
			
			$data_to_update = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key);
			
			$this->update($data_to_update, 'user_id = '.$insertedId);
			
 			$data['pass_resetkey'] = $reset_password_key ;
			$data['user_reset_status'] = "1" ;
			$data['last_inserted_id'] = $insertedId ;
			
			$isSend = $this->modelEmail->sendEmail('registration_email',$data);
			$isSend = $this->modelEmail->sendEmail('newuser_added',$data);
 			
 			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Inserted","inserted_id"=>$insertedId) ;
 		}
		catch(Zend_Exception  $e) {/* Handle Exception Here  */
			
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
 		}
	}
	

	
	
	
	/* 	Check Email Address Existance 
	 *	@
	 *  Author  - Varun
	 */
	public function checkEmail($email,$id=false){	
	
		$query =  $this->select()->where("user_email = '".$email."' or user_username='".$email."'"); 
		
		if(!$id)
			return  $query->query()->fetch();	 	

 		    return  $query->where("user_id != '".$id."'")->query()->fetch(); 	
 	}
	
	
	
	
	
	/* 	Reset Password Email 
	 *	@
	 *  Author  - Varun
	 */
  	public function resetPassword($user_email){
 		/* Update Reset Status */
		
		$user = $this->get(array("where"=>"user_email='$user_email'")) ;
		
 		
		$reset_password_key = md5($user['user_id']."!@#$%^".$user['user_created'].time());
		$data_to_update = array(
							"user_reset_status"=>"1",
							"pass_resetkey"=>$reset_password_key,
 		    			  );
		
 	  
        $this->update($data_to_update, 'user_id = '.$user['user_id']);
		
		$user['pass_resetkey'] = $reset_password_key ;
 		$user['user_reset_status'] = "1" ;
		
		$email = $this->modelEmail->sendEmail('reset_password',$user);
		
		if($email->success)
			return true;
	
 		return false ;  
	}
	
	
		##-------------------------##
	## Check Twt User exittance
	##--------------------------##	 
	public function checkTwtUserExistance($uid){	
		
 		
		if($uid)		
		return  $this->select()->where("oauth_uid = '".$uid."' and oauth_provider = '2'")->query()->fetch(); 	
 	}
	
	
 	/* 	Reset Password Email 
	 *	@
	 *  Author  - Varun
	 */
	 public function get($param = false ){
		 	 
		 if(is_array($param)){
			 
			 if(isset($param['key'])){
				$result = $this->fetchAll("pass_resetkey='".$param['key']."'");
				if($result->count()){
					return $result->current()->toArray();
				}
				return false ;
 			 }
			 
			 if(isset($param['where'])){
				$result = $this->fetchAll($param['where']);
				if($result->count()){
					return $result->current()->toArray();
				}
				return false ;
 			 }   			 
		 }
		 
 		 $user = $this->find($param) ;
		 
		 return $user->count()?$user->current()->toArray():false;
		  		 
	 }
	 
	 
	 
	 /* Get User Counts */
	 public function getCount($param = array()){

		 $this->_name = isset($param['table'])? $param['table']: "users"; 
		 
		 $field_name = isset($param['key'])?$param['key']:"user_id";
		 
		 $where = " user_type!='admin' and  ";
		 
		 $where .= isset($param['where'])?$param['where']:"1";
		 
		 
		 $data = $this->getAdapter()->select()->from($this->_name,new Zend_Db_Expr(" count($field_name) as count"))->where($where)->query()->fetch(); 
		 return $data['count'];
	 }
	 
	 
	 
	 
	 
	 
	 
	 
	 /*  Update the Auth Storage for the Logged User
	 *	@
	 *  Author  - Varun
	 */
	 private function updateAuth($userID)
	 {	
		$userData = $this->getUserData($userID);
		$authStorage=Zend_Auth::getInstance()->getStorage();
		$auth_updated= $authStorage->write((object)$userData);
		return true;
 	}
 	public function deleteUsers($user_ids)
	{
		$uId = $this->delete('user_id IN('.$user_ids.')');
		return $uId;
	}
 
}