<?php 
class DbAdapter { /* Begin Class DB */
	
	private $_database_connection_file_path ;
	

 	public function __construct(){
		
		//prd(ROOT_PATH);
					//$this->_database_connection_file_path = '/home/tbsdein/public_html/iawebsite/private/db.ini' ; 
					
					//$this->_database_connection_file_path = '/home/cgamdev/public_html/private/db.ini'

		if(SITE_STAGE=='development')
			 $this->_database_connection_file_path = '/var/www/html/Lerry/private/db.ini' ; 
		else
			//$this->_database_connection_file_path = '/home/findtutorz/public_html/private/db.ini' ;
			$this->_database_connection_file_path = '/var/www/lessonrewind/data/www/lessonrewind.com/private/db.ini' ;
			
		
   		$this->_db_connect();
		
 	}
	
	private function _db_connect(){
		
		//prd(SITE_STAGE);
		if(!file_exists($this->_database_connection_file_path)){
			throw new Exception("Invalid file path for database");;
		}
  
   		$dbConfig = parse_ini_file($this->_database_connection_file_path,true);
		$dbConfig = $dbConfig[SITE_STAGE]  ;

 		/*  Connect File For Cron Job */
		$connection  = mysql_connect(
							$dbConfig['resources.db.params.hostname'], 
							$dbConfig['resources.db.params.username'], 
							$dbConfig['resources.db.params.password']
						);
						
 		if(!$connection){
 			throw new Exception( mysql_error() );
 		}
  
		$dbselect = mysql_select_db($dbConfig['resources.db.params.dbname']);
		
 		if(!$dbselect){
			throw new Exception( mysql_error() );
		}
		
		
	}
	
 	public function exec($query){
  		$result = mysql_query($query);
 		if(!$result){
			throw new Exception(mysql_error());
		}
 		return $result; 
	}
	
 	public function hasTable($table_name){
		return $this->exec("describe `$table_name`");
 	}
 	
	public function lastInsertId(){
  		return mysql_insert_id(); 
	}
	
	public function beginTransaction(){
		$this->exec(" SET AUTOCOMMIT=0");
		$this->exec(" START TRANSACTION");
		return true;
	}
	
	public function commit(){
 		$this->exec("COMMIT");
 		return true;		
	}
	
	public function rollBack(){
		 $this->exec("ROLLBACK");
		 return true;		
	}
	
	
  	public function insert($table_name , $data = array()){
  		return $this->Super_Insert($table_name ,  $data);
	}
 
 
	public function update($table_name , $data = array() , $where ){
		return $this->Super_Insert($table_name ,  $data , $where );
	}
	

 	public function delete($table_name , $where){
		if(empty($table_name) or empty($where) ){
			throw new Exception("Invalid Parameters to call Delete");
		}
		return $this->exec("delete from `$table_name` where $where");
 	}
 
  	
	public function fetch($query){
		$result = $this->exec($query);
 		return mysql_fetch_assoc($result) ;
	}
	
	public function fetchAll($query){
		$result = $this->exec($query);
 		$temp = array();
		while($row = mysql_fetch_assoc($result))
			array_push($temp,$row);
		return $temp;
	}
	
 	public function runQuery($query){
  		return $this->fetchAll($query);
 	}
	
	
 	public function Super_Insert( $table_name , $data, $where = false){
		
  		if(empty($table_name) or !is_string($table_name)){
			throw new Exception("Table name cannot be empty or must be a string type");			
		}
		
		if(empty($data) or !is_array($data)){
			throw new InvalidArgumentException(" Argument #2 cannot be empty or must be an associative array type ");			
		}
		
 		$table_defination = $this->hasTable((string)$table_name);
		
		$data_types = array();
		
		while($row = mysql_fetch_assoc($table_defination)){
			$orignal[] = $row['Type'];
			if(preg_match('[int|float|double]',$row['Type'])){
				$data_types[$row['Field']] = "int";
			}else{
				$data_types[$row['Field']] = "string";
			}
		}
		
		foreach($data as $key=>$value){
			if(isset($data_types[$key]) and $data_types[$key]!="int"){
				$data[$key] = "'".$data[$key]."'";
			}
		}
 
 		if($where){
			
			$temp =array();
	
			foreach($data as $key=>$value){
				$temp[] ="`$key`= $value";
			}
	
			$query = "update   `$table_name` set ".implode(",",$temp)." where $where ";
			
		}else{
 			$query = "insert into `$table_name` (".implode(",",array_keys($data)).") values(".implode(",",$data).") ";
 		}
 		
		try{
			
 			$exec_query = $this->exec($query);
			
 			if($where){
				return  (object)array("success"=>true,"error"=>false,"message"=>'successfully ' ,'rows_affected'=>mysql_affected_rows());
			}else{
				return  (object)array("success"=>true,"error"=>false,"message"=>'successfully ' ,'inserted_id'=>mysql_insert_id());
			}
 			
		}catch(Exception $e){
   			$this->reportException($e);
 			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>mysql_errno()) ;
 		}
 	}
	
	
	
   
	public function Super_Get($table_name , $where = 1, $fetchMode = 'fetch', $extra = array()){
		
 		$query = $table_defination = $this->hasTable((string)$table_name);
		
 		$fields = '*';
		
		if(isset($extra['fields']) and  $extra['fields']){
			if(is_array($extra['fields'])){
				
				//$fields = '`'.implode("`,`",$extra['fields']).'`';
				$fields = '';
				foreach($extra['fields'] as $AllFields){
					if(strstr($AllFields,'SUM')!='' || strstr($AllFields,'MAX')!='' || strstr($AllFields,'MIN')!='' || strstr($AllFields,'AVG')!='' || strstr($AllFields,'COUNT')!='' || strstr($AllFields,'GROUP_CONCAT')!='' || strstr($AllFields,'IFNULL')!='' || strstr($AllFields,'CONCAT')!=''){
						$fields.= ''.$AllFields.',';
					}
					else
						$fields.= '`'.$AllFields.'`,';	
				}
				
				
			}else{
				$fields = '`'.implode("`,`",array_map('trim',explode(",",$extra['fields']))).'`';
			}
		}
		//prn($fields);
		$fields=chop($fields,',');
		$query  =  " Select  $fields  from `$table_name` where $where " ;  //$this->select()->from($this->_name,$fields)->where($where);
		//echo $query;
		if(isset($extra['group']) and  $extra['group']){
			$query.= " group by  ".$extra['group'] ; 
		}
		
		if(isset($extra['having']) and  $extra['having']){
			$query.= " having  ".$extra['having'] ; 
		}
		
		if(isset($extra['order']) and  $extra['order']){
			$query.= " order by ".$extra['order'] ; 
			//prd($query);
		}
		
		if(isset($extra['limit']) and  $extra['limit']){
			$query.= " limit  ".$extra['limit'] ; 
		}
 
 		return $fetchMode=='fetch'? $this->fetch($query):$this->fetchAll($query);
		
	}
	
	
	
	 /* 	Insert  / Update Record to the DataBase  	 */
 	public function Super_Delete($table_name , $where = "1"){	
   		try{
 			$deleted_records = $this->delete($table_name ,  $where);
 			return (object)array("success"=>true,"error"=>false,"message"=>"Record Successfully Deleted","deleted_records"=>$deleted_records) ;
  		}
		catch(Exception  $e) {/* Handle Exception Here  */
   			return (object)array("success"=>false,"error"=>true,"message"=>$e->getMessage(),"exception"=>true,"exception_code"=>mysql_errno()) ;
 		}
	}
	
	

}/* End Class DB */



 