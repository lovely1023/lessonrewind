<?php 
class Development extends DbAdapter {
	
 	/*
		Send Notification to Developer and save the exception
	 */
	public function reportException($exceptionObject){
   
   		$exception_data = array(
			"request"=> json_encode($_REQUEST),
			"exception_info"=>addslashes($exceptionObject->__toString()),
			"date"=>date("Y-m-d H:i:s")
		);
  		$this->insert("log_exceptions", $exception_data);
		
		$exception_template = $this->Super_Get("email_templates","emailtemp_key = 'exception_message_template'");	
		
		$email_options = array(
			'subject' => " Runtime Exception  : ".$exceptionObject->getMessage(),
			'message' => $exception_template['emailtemp_content'],
			'receiver_email' => "",
			'receiver_name' => "",
		);
		
		$message_replace_array = array(
			'{get_message}'=>$exceptionObject->getMessage(),
			'{_request}'=> isset($_SERVER)?"<pre>".print_r($_SERVER,true)."</pre>":"",
			'{_param}'=> "<pre>".print_r($_REQUEST,true)."</pre>",
			'{website_link}'=>SITE_URL,
			'{_toString}'=>"".$exceptionObject->__toString()."",
		);
  	
		$messge = str_replace( array_keys($message_replace_array) , array_values($message_replace_array) ,$email_options['message']); 
		
		$developers = json_decode(DEVELOPERS);
		
		foreach($developers as $mail_address=>$name){
			$email_options["receiver_email"] = $mail_address ;
			$email_options["receiver_name"] = $name ;
			$email_options["message"] = str_replace( array("{developer_email}","{developer_name}") , array($mail_address,$name) ,$messge); 
 			send_mail($email_options);
		}
 	} 
	
	
}

