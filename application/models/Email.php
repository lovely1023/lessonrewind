<?php
class Application_Model_Email extends Zend_Db_Table_Abstract
{
	protected $_name = 'email_templates';
	public $primary ="" , $modelStatic; 
	 
	
	
	public function init(){
		
   		$table_info = $this->info('primary');
		$this->primary = $table_info ['1'];
		$this->modelStatic = new Application_Model_Static();
 	}
	/* 	Add / Update User Information 
	 *	@
	 *  Author  - Varun
	 */
	 public function sendEmail($type = false ,$data = false){
		 
  		 $mail = new Zend_Mail();
		 
		 $site_config = Zend_Registry::get("site_config");
		 
 		 $SenderName = ""; $SenderEmail = "";$ReceiverName = ""; $ReceiverEmail = "";
		 
		 $admin_info = $this->modelStatic->getAdapter()->select()->from("users")->where("user_id =1")->query()->fetch();
		 
  		 if(!$type){
			 return  (object) array("error"=>true , "success"=>false , "message"=>" Please Define Type of Email");
		}
		
		
 		 
 		switch($type){
			
			case  'reset_password' :  /* begin  : Reset Password Email */
				
				$template = $this->modelStatic->getTemplate('reset_password');
 				
				$ReceiverEmail = $data['user_email'];
				$ReceiverName =  $data['user_email'];
				
				
				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
				if($data['user_type']=="1" or $data['user_type']=="2"){
 					$resetlink = SITE_HTTP_URL."/admin/resetpassword?key=".$data['pass_resetkey'];
 					//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
				
				}else{
					$resetlink = SITE_HTTP_URL."/user/resetpassword/key/".$data['pass_resetkey'];
	 				$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
				}
   				
				$MESSAGE = str_ireplace(array("{user_name}","{verification_link}","{website_link}" ), array( $data['user_first_name']." ".$data['user_last_name'] , $resetlink,APPLICATION_URL),$template['emailtemp_content']);
 				
   			break; /* end : Reset Password Email */
			
			
			case 'registration_verification_admin':/* begin : Registration Email */
				$template = $this->modelStatic->getTemplate('registration_verification_admin');
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_email'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				$resetlink = SITE_HTTP_URL."/admin/user/activate/key/".$data['pass_resetkey'];
 				$MESSAGE = str_ireplace(array("{user_name}","{guest_name}","{guest_email}","{guest_password}","{verification_link}","{website_link}" ), array($data['user_first_name'].' '.$data['user_last_name'],$data['user_first_name'].' '.$data['user_last_name'],$data["user_email"],$data["user_password"],$resetlink,APPLICATION_URL),$template['emailtemp_content']);

				break ;/* end : Registration Email */
				
				
				case 'password_change_email':/* begin : Registration Email */
				$template = $this->modelStatic->getTemplate('password_change_email');
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_email'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				$resetlink = SITE_HTTP_URL."/admin/user/activate/key/".$data['pass_resetkey'];
 				$MESSAGE = str_ireplace(array("{user_name}","{guest_name}","{guest_email}","{guest_password}","{verification_link}","{website_link}" ), array($data['user_first_name'].' '.$data['user_last_name'],$data['user_first_name'].' '.$data['user_last_name'],$data["user_email"],$data["user_password"],$resetlink,APPLICATION_URL),$template['emailtemp_content']);
				

				break ;/* end : Registration Email */
				
				case 'lesson_email':/* begin : Registration Email */
				$template = $this->modelStatic->getTemplate('lesson_email');
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_name'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
 				$MESSAGE = str_ireplace(array("{user_name}","{Lesson_name}","{Teacher_name}","{website_link}" ), array($data['user_name'],$data['Lesson_name'],$data["Teacher_name"],APPLICATION_URL),$template['emailtemp_content']);
			
				break ;/* end : Registration Email */
				
				
				
				
				case 'lesson_email_family':/* begin : Registration Email */
				$template = $this->modelStatic->getTemplate('lesson_email_family');
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_name'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
 				$MESSAGE = str_ireplace(array("{user_name}","{Lesson_name}","{Student_name}","{Teacher_name}","{website_link}" ), array($data['user_name'],$data['Lesson_name'],$data['Student_name'],$data["Teacher_name"],APPLICATION_URL),$template['emailtemp_content']);
			echo $MESSAGE;
				break ;/* end : Registration Email */
			case 'message_alert': /* begin :  email_verification */
 				 
				$template = $this->modelStatic->getTemplate('message_alert');
				
				$ReceiverEmail = $data['rece_email']; 
				$ReceiverName = $data['rece_first_name'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				$MESSAGE = str_ireplace(array("{user_name}","{MSG}"), array($ReceiverName,$data['msg']),$template['emailtemp_content']);
   				
  			break ;
			case 'message_alert_notification': /* begin :  email_verification */
 				 
				$template = $this->modelStatic->getTemplate('message_alert');
				$template['emailtemp_subject']="Notification Message";
				$ReceiverEmail = $data['rece_email']; 
				$ReceiverName = $data['rece_first_name'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				$MESSAGE = str_ireplace(array("{user_name}","{MSG}"), array($ReceiverName,$data['msg']),$template['emailtemp_content']);
				
   				
  			break ;
			/* end : email_verification*/
			case 'registration_email':/* begin : Registration Email */
				$template = $this->modelStatic->getTemplate('registration_email');
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_email'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
  				$resetlink = SITE_HTTP_URL."/user/activate/key/".$data['pass_resetkey'];
 
  				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
 				$MESSAGE = str_ireplace(array("{user_name}","{verification_link}","{website_link}" ), array($data['user_first_name']." ".$data['user_last_name'],$resetlink,APPLICATION_URL),$template['emailtemp_content']);
						
				
 				
			break ;/* end : Registration Email */
			case 'newuser_added':/* begin : Registration Email */
				$template = $this->modelStatic->getTemplate('newuser_added');
				$ReceiverEmail = $site_config['register_mail_id'];
				$ReceiverName = $site_config['site_title'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
  				//$resetlink = SITE_HTTP_URL."/user/activate/key/".$data['pass_resetkey'];
 
  				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
 				$MESSAGE = str_ireplace(array("{guest_name}","{guest_email}","{guest_school}","{website_link}" ), array($data['user_first_name']." ".$data['user_last_name'],$data['user_email'],$data['user_school_name'],APPLICATION_URL),$template['emailtemp_content']);
				//prd($MESSAGE);
						
				
 				
			break ;/* end : Registration Email */

			
			case 'registration_email_verification':/* begin : Registration Email */
				$template = $this->modelStatic->getTemplate('registration_email');
					$template['emailtemp_subject']="Account Verification Mail";
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_email'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
  				$resetlink = SITE_HTTP_URL."/user/activate/key/".$data['pass_resetkey'];
 
  				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
 				$MESSAGE = str_ireplace(array("{user_name}","{verification_link}","{website_link}" ), array($data['user_first_name']." ".$data['user_last_name'],$resetlink,APPLICATION_URL),$template['emailtemp_content']);
 				
			break ;/* end : Registration Email */
			case 'registration_email_admin':/* begin : Registration Email */
				
  				 
				$template = $this->modelStatic->getTemplate('registration_email_admin');
 				
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_email'];
				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 				
				
  				$resetlink = SITE_HTTP_URL."/user/activate/key/".$data['pass_resetkey'];
  				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
 				$MESSAGE = str_ireplace(array("{user_name}","{guest_name}","{guest_email}","{guest_password}","{verification_link}","{website_link}" ), array($data['user_first_name']." ".$data['user_last_name'],$data['user_first_name']." ".$data['user_last_name'],$data['user_email'],$data['user_password'],$resetlink,APPLICATION_URL),$template['emailtemp_content']);
				
				
				//prd($MESSAGE);
 			
			break ;/* end : Registration Email */
			
				case 'verification_email_admin':/* begin : Registration Email */
				
  				 
				$template = $this->modelStatic->getTemplate('registration_email_admin');
 				$template['emailtemp_subject']="Account Verification Mail";
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_email'];
				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 				
				
  				$resetlink = SITE_HTTP_URL."/user/activate/key/".$data['pass_resetkey'];
  				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
 				$MESSAGE = str_ireplace(array("{user_name}","{guest_name}","{guest_email}","{guest_password}","{verification_link}","{website_link}" ), array($data['user_first_name']." ".$data['user_last_name'],$data['user_first_name']." ".$data['user_last_name'],$data['user_email'],$data['user_password'],$resetlink,APPLICATION_URL),$template['emailtemp_content']);
				
				//prd($MESSAGE);
 			
			break ;/* end : Registration Email */
			case 'registration_teacher_email':/* begin : Registration Email */
				
  				 
				$template = $this->modelStatic->getTemplate('registration_teacher_email');
 			
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_email'];
				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 				
				$user_position="teacher";
				if(isset($data['user_position']) && $data['user_position']!='teacher')
				{
					$user_position=	$data['user_position'];
				}
  				$resetlink = SITE_HTTP_URL."/user/activate/key/".$data['pass_resetkey'];
  				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
 				$MESSAGE = str_ireplace(array("{user_name}","{user_position}","{school_name}","{guest_name}","{guest_email}","{guest_password}","{verification_link}","{website_link}" ), array($data['user_first_name']." ".$data['user_last_name'],$user_position,$data['school_name'],$data['user_first_name']." ".$data['user_last_name'],$data['user_email'],$data['user_password'],$resetlink,APPLICATION_URL),$template['emailtemp_content']);
 	
			break ;/* end : Registration Email */
			
			
 			/* Email For Verification of new Email Address */
			case 'email_verification': /* begin :  email_verification */
 				 
				$template = $this->modelStatic->getTemplate('email_verification');
 				
				$ReceiverEmail = $data['user_email'];
				$ReceiverName = $data['user_email'];
				
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
   				$resetlink = SITE_HTTP_URL."/user/verifyemail/key/".$data['user_email_key'];
 				 
 				//$resetlinkhtml='<a href="'.$resetlink.'" >'.$resetlink.'</a>';
  				
				$MESSAGE = str_ireplace(array("{user_name}","{verification_link}","{website_link}" ), array( $data['user_first_name']." ".$data['user_last_name'],$resetlink,APPLICATION_URL),$template['emailtemp_content']);
   				
  			break ;/* end : email_verification*/
			
			
			/* Email For Verification of new Email Address */
			case 'add_stu_roster': /* begin :  email_verification */
				$template = $this->modelStatic->getTemplate('add_stu_roster');
				$ReceiverEmail = $data['school_admin_email'];
				$ReceiverName = $site_config['site_title'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
			
				$MESSAGE = str_ireplace(array("{teacher_name}","{student_name}","{website_link}" ), array( $data['teacher_name'],$data['student_name'],APPLICATION_URL),$template['emailtemp_content']);
			
  			break ;/* end : email_verification*/
			
			
			case 'remove_stu_roster': /* begin :  email_verification */
				$template = $this->modelStatic->getTemplate('remove_stu_roster');
				$ReceiverEmail = $data['school_admin_email'];
				$ReceiverName = $site_config['site_title'];
 				$SenderEmail = $site_config['register_mail_id']; 
				$SenderName = $site_config['site_title']; 
				
				$MESSAGE = str_ireplace(array("{teacher_name}","{student_name}","{website_link}" ), array( $data['teacher_name'],$data['student_name'],APPLICATION_URL),$template['emailtemp_content']);
  			break ;/* end : email_verification*/
			
			
			case 'email_subscription':{
				
 	 			$template = $this->modelStatic->getTemplate("email_subscription");
				
	 			
 				$sender_email = $site_config["register_mail_id"];
				$sender_name = $site_config['site_title'];
				$subject = $site_config['site_title']." - ".$template['emailtemp_subject']; 
		 
				$MESSAGE = str_ireplace(
										array( "{user_name}","{guest_name}","{plan_name}","{plan_amount}","{plan_date}","{website_link}" ), 
										array(	$data['subscription_userfirstname']." ".$data['subscription_userlastname'],$data['subscription_userfirstname']." ".$data['subscription_userlastname'],$data['subscription_plantitle'],$site_config['site_currency']." ".$data['subscription_planprice']." per month",$data['subscription_start_date'],APPLICATION_URL),
										$template['emailtemp_content']
									);
									
				
				$SenderEmail=$site_config["register_mail_id"];
				$SenderName=$site_config['site_title'];
				$ReceiverEmail=$data['user_email'];
				$ReceiverName=$data['subscription_userfirstname'];
				$mail = new Zend_Mail();
				/*$mail->setBodyHtml($mail_content)
				->setFrom($site_config["register_mail_id"], $site_config['site_title'])
				->addTo($data['user_email'] , $data['subscription_userfirstname'])
				->setSubject($subject);
		
				if(!TEST){
					$mail->send() ;
				}*/
				
				
				
				 
 				
			}
			break;
			
			case 'contact_us':{
				
 	 			$template = $this->modelStatic->getTemplate("contact_us_user");
				
	 		
 				$sender_email = $data['guest_email'];
				$sender_name = $data['guest_name'];
				$sender_phone = $data['guest_phone'];
				$message = $data['guest_message'];
				$subject = $site_config['site_title']." - ".$template['emailtemp_subject']; 
		 
				$mail_content = str_ireplace(
									array( "{SITE_TITLE}" ,"{site_admin}","{guest_name}","{sender_email}","{sender_phone}","{sender_message}","{website_link}" ), 
									array(	$site_config['site_title'] ,$site_config['site_title'] ,$sender_name,$sender_email,$sender_phone,$message,APPLICATION_URL),
									$template['emailtemp_content']
								);
						
				
				$to      =$sender_email;
				$subject = $subject;
				$message =$mail_content;
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: ' .$site_config['site_title'].' '.$site_config["register_mail_id"]. "\r\n" .
						'Reply-To: ' .$sender_name.' '.$ReceiverEmail. "\r\n" .
				'X-Mailer: PHP/' . phpversion();
				mail($to, $subject, $message, $headers);
			/*	$mail = new Zend_Mail();
				$mail->setBodyHtml($mail_content)
				->setFrom($site_config["register_mail_id"], $site_config['site_title'])
				->addTo($sender_email , $sender_name)
				->setSubject($subject);
		
				if(!TEST){
					$mail->send() ;
				}*/
				
			
				
				/* Mail To Admin  */		
				$template =$this->modelStatic->getTemplate("contact_us_admin");
						
				$mail_content = str_ireplace(
									array( "{SITE_TITLE}" ,"{site_admin}","{guest_name}","{guest_email}","{guest_phone}","{guest_message}","{website_link}" ), 
									array(	$site_config['site_title'] ,$site_config['site_title'] ,$sender_name,$sender_email,$sender_phone,$message,APPLICATION_URL),
								$template['emailtemp_content']
							);
				$to      =$site_config['register_mail_id'];
				$subject = $subject;
				$message =$mail_content;
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: ' .$sender_name.' '.$site_config["register_mail_id"]. "\r\n" .
				'Reply-To: ' .$site_config['site_title'].' '.$ReceiverEmail. "\r\n" .
				'X-Mailer: PHP/' . phpversion();
				mail($to, $subject, $message, $headers);
				
 				/*$mail = new Zend_Mail();
				$mail->setBodyHtml($mail_content)
					->setFrom($site_config["register_mail_id"], $site_config['site_title'])
					->addTo($site_config['register_mail_id'],$admin_info['user_first_name']." ".$admin_info['user_last_name'])
					->setSubject($subject);
				 
				
				if(!TEST ){ 
				
			 $mail->send();
				return true;} else {return false;}*/
				 
 				
			}
			break;
 			default:return  (object)array("error"=>true , "success"=>false , "message"=>" Please Define Proper Type for  Email");
		}
				$to  =$ReceiverEmail;
				$subject = $template['emailtemp_subject'];
				$message =$MESSAGE;
				$headers  = 'MIME-Version: 1.0' . "\r\n";
				$headers .= 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
				$headers .= 'From: ' .$site_config['site_title'].' '.$SenderEmail. "\r\n" .
					'Reply-To: ' .$SenderName.' '.$SenderEmail. "\r\n" .
				'X-Mailer: PHP/' . phpversion();
				$bb=mail($to, $subject, $message, $headers);
			
				return (object)array("error"=>false , "success"=>true , "message"=>" Mail Successfully Sent");
				$MESSAGE="sdfg";
		/* $mail->setBodyHtml($MESSAGE)
			 ->setFrom($SenderEmail, $SenderName)
			 ->addTo($ReceiverEmail,$ReceiverName)
			 ->setSubject($template['emailtemp_subject']);
   			 
		if(!TEST){
 			$mail->send();
		
			return (object)array("error"=>false , "success"=>true , "message"=>" Mail Successfully Sent");
		}*/
		
		return (object)array("error"=>false , "success"=>true , "message"=>" Unable To Send Email ");	
  		 
	 }
	 
	 
	 
	 private function _registration(){
		 
	 }
 	
	   
	
}