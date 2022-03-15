<?php
class Application_Model_SuperModel extends Zend_Db_Table_Abstract
{
 	protected $_name = "";
	
	public function init(){		
		
	}
	
  	/* 	Insert  / Update Record to the DataBase 
 	 */
 	public function Super_Insert($table_name ,$data , $where = false){	
	
		$this->_name = $table_name;
				
		try{			
			if($where){
				
				$updated_records = $this->getAdapter()->update($table_name ,$data , $where);
				
				return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Updated","row_affected"=>$updated_records) ;
			}
			
			$insertedId = $this->getAdapter()->insert($table_name,$data); 

 			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Inserted","inserted_id"=>$this->getAdapter()->lastInsertId()) ;
 		}
		catch(Zend_Exception  $e) {/* Handle Exception Here  */
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
 		}
	}
	
  	/*   */
 		public function Super_Get($table_name , $where = 1, $fetchMode = 'fetch', $extra = array(),$joinArr=array()){
		
		$this->_name = $table_name;
		
		$fields = array('*');
		if(isset($extra['fields']) and  $extra['fields']){
			if(is_array($extra['fields'])){
				$fields = $extra['fields'];
			}else{
				$fields = explode(",",$extra['fields']);
			}
		}
		$query  = $this->getAdapter()->select()->from($this->_name,$fields)->where($where);
		
		/* Join Conditions */
		if(isset($joinArr)){
			foreach($joinArr as $newCondition){ 
				if($newCondition[2]=='full')
					$query->join($newCondition[0],$newCondition[1],$newCondition[3]);
				else
					$query->joinLeft($newCondition[0],$newCondition[1],$newCondition[3]);	
			}
		}
		//echo $query;die;
		
		
		if(isset($extra['group']) and  $extra['group']){
			$query = $query->group($extra['group']);
		}
		
		if(isset($extra['having']) and  $extra['having']){
			$query = $query->having($extra['having']);
		}
		
		if(isset($extra['order']) and  $extra['order']){
			$query = $query->order($extra['order']);
		}
		
		if(isset($extra['limit']) and  $extra['limit']){
			$query = $query->limit($extra['limit']);
		}
 		/* If Pagging is Required */
		if(isset($extra['pagination']) and  $extra['pagination'] and !isset($extra['pagination_result'])){
			return $query;
		}
		if(isset($extra['pagination']) and  $extra['pagination'] and isset($extra['pagination_result']) and  $extra['pagination_result']){
			return array($query,$query->query()->fetchAll());
		}
		
		
 		
		return $fetchMode=='fetch'? $query->query()->fetch():$query->query()->fetchAll();
	 }
	 
	 /* 	Insert  / Update Record to the DataBase 
 	 */
 	public function Super_Delete($table_name , $where = "1"){	
   		try{
			
			$deleted_records = $this->getAdapter()->delete($table_name ,  $where);
 			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Deleted","deleted_records"=>$deleted_records) ;
  		}
		catch(Zend_Exception  $e) {/* Handle Exception Here  */
			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>$e->getCode()) ;
 		}
	}
	
	 
	 
	 
 	public function PrepareSelectOptions_withdefault($tabelname ,$fieldname1,$fieldname2,$where,$order,$default_value=false)
	{	
	
		if(!$order)
		$result = $this->getAdapter()->select()->from($tabelname)->where($where);
		else  
		$result = $this->getAdapter()->select()->from($tabelname)->where($where)->order($order);
		//echo $result; die;
		$data= $result->query()->fetchAll() ; 
		
		$getdata=array();
		if($default_value)
		{
		$getdata['']=$default_value;
		}
		for ($i = 0; $i < count($data); $i++) 
		{

		$getdata[$data[$i][$fieldname1]]= $data[$i][$fieldname2];

		}
			
		
		return $getdata;
	}
	
	public function PrepareSelectOptions_withdefault_user($tabelname ,$fieldname1,$fieldname2,$fieldname3,$where,$order,$default_value=false)
	{	
	
		if(!$order)
		$result = $this->getAdapter()->select()->from($tabelname)->where($where);
		else  
		$result = $this->getAdapter()->select()->from($tabelname)->where($where)->order($order);
		$data= $result->query()->fetchAll() ; 
		$getdata=array();
		if($default_value)
		{
		$getdata['']=$default_value;
		}
		for ($i = 0; $i < count($data); $i++) 
		{
			$getdata[$data[$i][$fieldname1]]= $data[$i][$fieldname2].', '.$data[$i][$fieldname3];
		}
		return $getdata;
		
	}
	
	public function insertusername($user_type)
	{
			$userLogged = isLogged(true);
			$school_data=array();
			if($userLogged->user_type=='school')
			{
					$school_data=(array)$userLogged;
			}
			else
			{
				$school_id=$userLogged->user_school_id;	
				$school_data=$this->Super_Get("users","user_id='".$school_id."'","fetch");
			}
			
			$prefix_name=str_replace(' ','',$school_data['user_school_name']);
			if($user_type=='teacher')
			{
				$prefix_name=$prefix_name."Teacher_";
			}
			else if($user_type=='student')
			{
				$prefix_name=$prefix_name."Student_";	
			}
			else if($user_type=='schoolsubadmin')
			{
				$prefix_name=$prefix_name."SubAdmin_";	
			}
			else if($user_type=='family')
			{
				$prefix_name=$prefix_name."Family_";	
			}
			$num=1;
			$user_name='';
			$get_user=$this->Super_Get("users","user_type='".$user_type."' and user_school_id='".$school_data['user_id']."'","fetch",array("limit"=>1,"order"=>array("user_id DESC")));
		
			$num = mt_rand(100000,999999); 
			$user_name=$prefix_name.$num;
			$unique_status=0;
		
			 do
			 {
				
				 $check_user=array();
				 $check_user=$this->Super_Get("users","user_username='".$user_name."'","fetch");
				 
				 if(empty($check_user))
				 {
						$unique_status=1;	
						
				 }
				 else
				 {
					$num = mt_rand(100000,999999); 
					$user_name=$prefix_name.$num;
				 }
			 }
			 while($unique_status==0); 
			
			 return $user_name;
	}
	
	public function insertuniqueusername($firt,$last)
	{
		
			$username=$firt.$last;
			$digits=n_digit_random(4);
			$uniquename=$username.$digits;
		
	
			$status=0;
			do
			{
				$check_user=array();
				$check_user=$this->Super_Get("users","user_username='".$uniquename."'","fetch");
				
				
				if(empty($check_user))
				{
					$status=1;	
				}
				
			}
			while ($status==0);
			
			return $uniquename;
			
	}
	
	public function insertstudentname($first,$last,$school_id)
	{
		$count=1;
		$status=0;
		$uniquelast=$last;
		
			do
			{
				$uniquelast=$last.$count;
				
				$check_student_exists=array();
				$check_student_exists=$this->Super_Get("users","user_first_name='".trim($first)."' and user_last_name='".trim($uniquelast)."' and user_type='student' and user_school_id='".$school_id."'","fetch");	
				
				$count=$count+1;
				if(empty($check_student_exists))
				{
					$status=1;	
				}
				
			}
			while ($status==0);
		
			return $uniquelast;
	}
	
	

	
	 
	 
}