<?php
class Application_Model_Message extends Application_Model_SuperModel
{
	protected $_name = "messages";
	
	public function init(){		
 		
		$this->primary = "message_id";
		 
	}
	
	 
	 
	public function getmessages($msgID=false,$type=false) {
		
		if($type==1) {
		
		 $query = $this->getAdapter()->select()->from(array('m'=>"messages"),array('message_sender_id','message_reciver_id','message_subject','message_content','message_date'))
						->join(array('u'=>"users"),'u.user_id=m.message_sender_id',array('user_first_name','user_last_name','user_id'))
					    //->join(array('fct'=>"federal_cases_types"),'fct.federal_case_id=af.fedral_case_id')
					    ->where('message_id="'.$msgID.'"')->query()->fetch();
		  
		  
		} 
		
		if($type==2) {
			
			 $query = $this->getAdapter()->select()->from(array('m'=>"messages"),array('message_sender_id','message_reciver_id','message_subject','message_content','message_date'))
						->join(array('u'=>"users"),'u.user_id=m.message_reciver_id',array('user_first_name','user_last_name','user_id'))
					    //->join(array('fct'=>"federal_cases_types"),'fct.federal_case_id=af.fedral_case_id')
					    ->where('message_id="'.$msgID.'"')->query()->fetch();
			}	
		 return $query;	
		
		
	}	
	
	public function getallmessages($msgID=false,$type=false)
	{
			 $query = $this->getAdapter()->select()->from(array('m'=>"messages"),array('message_sender_id','message_reciver_id','message_parent_id','message_subject','message_content','message_date'))
					  ->join(array('u'=>"users"),'u.user_id=m.message_sender_id',array('user_first_name','user_last_name','user_id','user_image'))
					  ->where('message_parent_id="'.$msgID.'"')->query()->fetchAll();
			return $query;
		  	
	} 

	public function viewmessage($msg_id){
		
		 
		
		$query = $this->getAdapter()->select()->from(array('m'=>"messages"),array('message_sender_id','message_reciver_id','message_subject','message_content','message_date','message_id'))
						->join(array('u'=>"users"),'u.user_id=m.message_sender_id',array('sender_first_name'=>"u.user_first_name",'sender_last_name'=>"u.user_last_name"))
						->join(array('uc'=>"users"),'uc.user_id=m.message_reciver_id',array('reciver_first_name'=>'uc.user_first_name','reciver_last_name'=>"uc.user_last_name"))
					    ->where('message_id="'.$msg_id.'"')->query()->fetch();
		 
		 
		
		return  $query;
		
		
	}
	 
		
  
}
