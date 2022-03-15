<?php
class  Admin_ProfileController extends Zend_Controller_Action
{
	private $modelUser = "", $adminLogged ="" , $modelSecrate = "";
	public $module_name = "admin/" , $controller_name = "profile";

    public function  init(){ 


		$this->modelUser = new Application_Model_User();
		
		//$this->modelSecrate = new Application_Model_Secrate();
		
		//$this->modelVerification = new Application_Model_Verification();
 		
     }
	 
	 
	 
 
	/* Update Profile Information Admin */
	public function indexAction(){
		
		global $mySession; 
  		
		$this->view->pageHeading = "Update Profile ";
 		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Profile Update' =>'/profile-update');	
		$this->view->pageDescription = "you can manage your account information here ";

		$this->view->show = "update_profile_admin";
		
 		/* Form for profile Information  */
 		$form =  new Application_Form_User();
		$form->profile_admin(($this->view->user->user_id));
		
   		$form->populate((array)$this->view->user);

 		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
			
			if($form->isValid($data_post)){
				
				$data_to_update = $form->getValues() ;
				
 				$is_update  = $this->modelUser->add($form->getValues() , $this->view->user->user_id);
				
				
				if(is_object($is_update) and $is_update->success){
					if($is_update->row_affected > 0){
						$mySession->successMsg = " Profile Information Changed Successfully ";
					}else{
						$mySession->infoMsg = " New Information is Same as Pervious one ";
					}
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"update_profile_admin");
				}
				
				$mySession->errorMsg  = $is_update->message; ;
				
			}else{
				$mySession->errorMsg = "Please Check Information Again ...!";	
			}
  		}
		
		$this->view->form = $form;

	}
	
	
	public function imageAction(){
		global $mySession ;
		
		$this->view->show = "update_image_admin";
		
		 /* Form For Update Profile Image  */
 		$form =  new Application_Form_User();
		$form->image();
		
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Profile Image' =>'/profile-image');	
		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
		
			if($form->isValid($data_post)){
				
 				$is_uploaded = $this->_handle_profile_image();
				
				if(is_object($is_uploaded) and $is_uploaded->success){

					if(empty($is_uploaded->media_path)){
						/* Not Image is Uploaded  */
						$mySession->defaultMsg = "No Images Selected ...";
						$this->_helper->getHelper("Redirector")->gotoRoute(array(),'update_image_admin');
					}
					
					
					$is_updated = $this->modelUser->add(array("user_image"=>$is_uploaded->media_path),$this->view->user->user_id);
					
					if(is_object($is_updated) and $is_updated->success){
						
						/* Remove Old User Images*/
						$this->_remove_image(); 
						$mySession->successMsg = " Image Successfully Updated";
						$this->_helper->getHelper("Redirector")->gotoRoute(array(),'update_image_admin');
						
 					}
										
				}
 			}
		}
		
		
		$this->view->form = $form ;
		
	}
	
	
	/* MarketPlace 
	 * Remove / Unlink Old Profile Image  
 	 */	 
 	private function _remove_image(){
		
		$image_name = $this->view->user->user_image;
		
		if(file_exists(PROFILE_IMAGES_PATH."/".$image_name)){
			unlink(PROFILE_IMAGES_PATH."/".$image_name);
		}
		
		if(file_exists(PROFILE_IMAGES_PATH."/thumb/".$image_name)){
			unlink(PROFILE_IMAGES_PATH."/thumb/".$image_name);
		}
		 
 		if(file_exists(PROFILE_IMAGES_PATH."/60/".$image_name)){
			unlink(PROFILE_IMAGES_PATH."/60/".$image_name);
		}
		if(PROFILE_IMAGES_PATH."/160/".$image_name){
			unlink(PROFILE_IMAGES_PATH."/160/".$image_name);
		}
		
		return true ;
		
	}
	
	
	
	/* Handle The Uploaded Images For Graphic Media  */
	private function _handle_profile_image(){
		
 		global $mySession; 
		
		$uploaded_image_names = array();
	 
		$adapter = new Zend_File_Transfer_Adapter_Http();
	
		$files = $adapter->getFileInfo();
  		 
		$uploaded_image_names = array();
		
		$new_name = false; 
		 
  		/*prd($adapter);*/
		foreach ($files as $file => $info) { /* Begin Foreach for handle multiple images */
		
  			$name_old = $adapter->getFileName($file);
			
			if(empty($name_old)){
				continue ;			
			}
			
			$file_title  = $adapter->getFileInfo($file);
			
			$file_title = $file_title[$file]['name']; 
				
  			$uploaded_image_extension = getFileExtension($name_old);
			
 			$file_title  = str_replace(".".$uploaded_image_extension,"",$file_title);
			
			$file_title = formatImageName($file_title);
  
 			$new_name = $file_title."-".time()."-".rand(1,100000).".".$uploaded_image_extension;
 			
  			$adapter->addFilter('Rename',array('target' => PROFILE_IMAGES_PATH."/".$new_name));
		
			try{
				$adapter->receive($file);
			}
			catch(Zend_Exception $e){
				return (object) array('success'=>false,"error"=>true,'exception'=>true,'message'=>$e->getMessage(),'exception_code'=>$e->getCode()) ;
			}
			
			$thumb_config = array("source_path"=>PROFILE_IMAGES_PATH,"name"=> $new_name);
 			Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
			Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
			Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
			
  			//$uploaded_image_names[]=array('media_path'=>$new_name); => For Multiple Images
   		
		}/* End Foreach Loop for all images */
		
		
		return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$new_name) ;
 		
   	 
 	}
	
	
	
	/* Admin Change Password  */	
	public function passwordAction(){
		
	
		global $mySession; 
    	$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Change Password' =>'/change-password');	
		$this->view->pageHeading = "Change Password";
 		$this->view->pageDescription = "you can change your account password here ";
 
 		$this->view->show = "update_password_admin";
  
		/* Change Password Form */
		$form =  new Application_Form_User();
		$form->changepassword(true);
  
   		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
			
			if($form->isValid($data_post)){
				
				$data_to_update = $form->getValues();
				$data_to_update['user_password'] = md5($data_to_update['user_password']);
				$data_to_update['user_password_text']=$data_to_update['user_password'];
				
   				$is_update = $this->modelUser->add($data_to_update,$this->view->user->user_id);
  			
				if(is_object($is_update) and $is_update->success){
					$mySession->successMsg = " Password Changed Successfully ";
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),$this->view->show);
 				}else{
					$mySession->errorMsg  = $is_update->message; ;
				}
			}else{
				$mySession->errorMsg = "Please Check Information Again ...!";	
			}
  		}
		
		$this->view->form = $form;
		$this->render("index");
	}
	


 
	
	public function notificationAction(){
		
		$this->view->show = "update_notification_admin";
		$this->render("index");
		
	}
	
	

	public function accesslogAction(){
		$this->view->pageHeading = "Account Access Log ";
 		$this->view->pageDescription = "Here you can manage the account access log  ";
		$this->view->setScriptPath("application/views/scripts/");
		$this->_helper->getHelper('viewRenderer')->renderScript('profile/accesslog.phtml');
	}
 	

   	public function updatenotificationAction(){
		
		if($this->getRequest()->isPost()){
			global $mySession ;
			
			$posted_data = $this->getRequest()->getPost();
			
			 
			
			try{
				$update_count = $this->modelUser->update(array("user_login_notification"=>$posted_data['login_alert_notification']) , "user_id=".$this->adminLogged->user_id);
				$mySession->successMsg = "Notification Setting Successfully Updated .. ";
			}catch(Zend_Exception $e){
 				$mySession->errorMsg = $e->getMessage();
			}
			
			unset($posted_data['login_alert_notification']);
			/* All Other Notifications */
			
 			$all_notification_array = array("fund_deposit_request","fund_withdrawal_request","user_account_verification");
			
			foreach($all_notification_array as $value){
 				if(!isset($posted_data[$value])){
					$posted_data[$value] = 0;
				}
				
				$query = "REPLACE INTO user_notifications (un_user_id,un_type,un_value) VALUES(".$this->view->user->user_id.",'$value','".$posted_data[$value]."')";	
  				$this->modelUser->getAdapter()->exec($query);					
 			}
 			
 		}
		$this->_redirect($this->module_name."account-update?tab=notification");
 		
	}
	
 	
	
	/* Send Verification Email */
	public function verifyemailAction(){
		
		global $mySession;
		
 		$data_form_values = (array) $this->adminLogged ;
 	 
 		if($this->adminLogged->user_email_verified!="1"){
			
			$user_email_key = md5("ASDFUITYU"."!@#$%^$%&(*_+".time());
			
			$new_verification_status = "2";
									
			if($this->adminLogged->user_verification_status=="3"){
				$new_verification_status = "3";
			} 	

			$data_to_update = array("user_email_verified"=>"0","user_email_key"=>$user_email_key,'user_verification_status'=>$new_verification_status);
			$this->modelUser->update($data_to_update, 'user_id = '.$this->adminLogged->user_id);
			$data_form_values['user_email_key'] = $user_email_key ;
			$this->modelEmail->sendEmail('email_verification',$data_form_values);
			
			$mySession->successMsg = " Email Successfully Send to your email address , please follow the verification link to verify the email address ";
 			
		}else{
			$mySession->infoMsg = "Your Email Address is already verified..";
		}
  		 
 		$this->_redirect($this->module_name."account-update");
	}
 	
}

