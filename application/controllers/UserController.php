<?php
class UserController extends Zend_Controller_Action
{
  	private $modelUser ,$modelContent; 
	 
	public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelSuper = new Application_Model_SuperModel();
 	}


	public function indexAction(){
 		$this->_redirect('user/login');
 	}
	
	public function checkfamilyemailAction()
	{
		$this->_helper->layout->disableLayout();
		$family_id=$this->getRequest()->getParam('family_id');	
		$family_data=array();
		$family_data=$this->modelSuper->Super_Get("users","user_id='".$family_id."'","fetch");
		if($family_data['user_email']=='')
		{
			echo 0;	
		}
		else
		{
			echo 1;		
		}
		exit;
	}
	 
	public function signinAction(){
 		
		global $objSession; 
		$this->view->pageHeading = " Sign In";
		$this->view->page_slug="Sign_in";
		$auth = Zend_Auth::getInstance(); 
		
 		$form = new Application_Form_User();
		$form->login_front_update();
		/*If You login by the form*/
		if ($this->getRequest()->isPost()){ // Post Form Data
 			$posted_data  = $this->getRequest()->getPost();
			if ($form->isValid($this->getRequest()->getPost()))
			{ // Form Valid
				
				$received_data  = $form->getValues();
				$keep_me=$received_data['keep_me'];
				unset($received_data['keep_me']);
  				/* Zend_Auth Setup Code */
				$received_data['user_password']=$received_data['user_password1'];
				unset($received_data['user_password1']);
				if(filter_var($received_data['user_email'], FILTER_VALIDATE_EMAIL))
				{
					$authAdapter = new Zend_Auth_Adapter_DbTable($this->_getParam('db'), 'users', 'user_email', 'user_password'," ? AND user_type!='admin' and  user_type!='site_subadmin' and  user_status = '1' " /*, 'MD5(CONCAT(?, password_salt))'*/ );	
				}
				else
				{
					$authAdapter = new Zend_Auth_Adapter_DbTable($this->_getParam('db'), 'users', 'user_username', 'user_password'," ? AND user_type!='admin' and  user_type!='site_subadmin' and  user_status = '1' " /*, 'MD5(CONCAT(?, password_salt))'*/ );
				}
				
			
  				// Set the input credential values
 				$authAdapter->setIdentity($received_data['user_email']);
				$authAdapter->setCredential(md5($received_data['user_password']));
				$result = $auth->authenticate($authAdapter);// Perform the authentication query, saving the result	
					
				if($result->isValid()){ // IF Auth Get the Record 
 					$data = $authAdapter->getResultRowObject(null); //Now get a result row without user_password set is here
 
 					$auth->getStorage()->write($data); //Now seession set is here
					$user_id=$auth->getIdentity()->user_id;
					
					if($keep_me==1)
					{
						setcookie('user_id', $user_id, time() + 3600, '/');
					}
					
							if($auth->getIdentity()->user_type=='school' || $auth->getIdentity()->user_type=='schoolsubadmin')
							{
								/*echo '<script type="text/javascript">window.open("https://www.lessonrewind.com/profile/dashboard");</script>';*/
								$objSession->successMsg = '"Welcome '.$auth->getIdentity()->user_school_name.'"';
								$this->_redirect('dashboard');	
							}
							else if($auth->getIdentity()->user_type=='teacher' )
							{
								/*echo '<script type="text/javascript">window.open("https://www.lessonrewind.com/profile/teacherdashboard");</script>';*/
								$objSession->successMsg = "You have been logged in Successfully";
								 $this->_redirect('profile/teacherdashboard');
							}
							else if($auth->getIdentity()->user_type=='student')
							{
								/*echo '<script type="text/javascript">window.open("https://www.lessonrewind.com/profile/studentdashboard");</script>';*/
								$objSession->successMsg = "You have been logged in Successfully";
								 $this->_redirect('profile/studentdashboard');
									
							}
							else
							{
								/*echo '<script type="text/javascript">window.open("https://www.lessonrewind.com/profile/familydashboard");</script>';*/
								$objSession->successMsg = "You have been logged in Successfully";
								 $this->_redirect('profile/familydashboard');
							}					
						
					 
				}
				else
				{	
				
					 // Auth Not Valid
					Zend_Auth::getInstance()->clearIdentity();
					    echo "<script>window.close();</script>"; die;
					$objSession->errorMsg = "Email or password is invalid.";
  				}			
			}
			    echo "<script>window.close();</script>"; die;
 			$objSession->errorMsg = "Email or password is invalid.";
 		} // End Post Form
		
		$this->view->form = $form;
		
	}
	
	public function loignsaveAction(){
			
	}
	 
 	public function loginAction(){
 		
		global $objSession; 
		$this->view->pageHeading = " Sign In";
		$this->view->page_slug="Sign_in";
		$auth = Zend_Auth::getInstance(); 
		if ($auth->hasIdentity()){
            $objSession->infoMsg ='It seems you are already logged into the system ';
			
            $this->_redirect('profile');
        }
 		$form = new Application_Form_User();
		$form->login_front_update();
		/*If You login by the form*/
		if ($this->getRequest()->isPost()){ // Post Form Data
 			$posted_data  = $this->getRequest()->getPost();
			if ($form->isValid($this->getRequest()->getPost()))
			{ // Form Valid
				
				$received_data  = $form->getValues();
				$keep_me=$received_data['keep_me'];
				unset($received_data['keep_me']);
  				/* Zend_Auth Setup Code */
				$received_data['user_password']=$received_data['user_password1'];
				unset($received_data['user_password1']);
				if(filter_var($received_data['user_email'], FILTER_VALIDATE_EMAIL))
				{
					$authAdapter = new Zend_Auth_Adapter_DbTable($this->_getParam('db'), 'users', 'user_email', 'user_password'," ? AND user_type!='admin' and  user_type!='site_subadmin' and  user_status = '1' " /*, 'MD5(CONCAT(?, password_salt))'*/ );	
				}
				else
				{
					$authAdapter = new Zend_Auth_Adapter_DbTable($this->_getParam('db'), 'users', 'user_username', 'user_password'," ? AND user_type!='admin' and  user_type!='site_subadmin' and  user_status = '1' " /*, 'MD5(CONCAT(?, password_salt))'*/ );
				}
				
			
  				// Set the input credential values
 				$authAdapter->setIdentity($received_data['user_email']);
				$authAdapter->setCredential(md5($received_data['user_password']));
				$result = $auth->authenticate($authAdapter);// Perform the authentication query, saving the result	
					
				if($result->isValid()){ // IF Auth Get the Record 
 					$data = $authAdapter->getResultRowObject(null); //Now get a result row without user_password set is here
 
 					$auth->getStorage()->write($data); //Now seession set is here
					$user_id=$auth->getIdentity()->user_id;
					
					if($keep_me==1)
					{
						setcookie('user_id', $user_id, time() + 3600, '/');
					}
					if(isset($_GET['url']))
					{	
						 $this->_redirect(urldecode($_GET['url']));
					}
					else
					{
							if($auth->getIdentity()->user_type=='school' || $auth->getIdentity()->user_type=='schoolsubadmin')
							{
								$objSession->successMsg = '"Welcome '.$auth->getIdentity()->user_school_name.'"';
								$this->_redirect('dashboard');	
							}
							else if($auth->getIdentity()->user_type=='teacher' )
							{
								$objSession->successMsg = "You have been logged in Successfully";
								 $this->_redirect('profile/teacherdashboard');
							}
							else if($auth->getIdentity()->user_type=='student')
							{
								$objSession->successMsg = "You have been logged in Successfully";
								 $this->_redirect('profile/studentdashboard');
									
							}
							else
							{
								$objSession->successMsg = "You have been logged in Successfully";
								 $this->_redirect('profile/familydashboard');
							}					
						}
					 
				}
				else
				{	
				
					 // Auth Not Valid
					Zend_Auth::getInstance()->clearIdentity();
					$objSession->errorMsg = "Email or password is invalid.";
  				}			
			}
 			$objSession->errorMsg = "Email or password is invalid.";
 		} // End Post Form
		
		$this->view->form = $form;
		
	}
	
	public function setadminAction()
	{
		 global $objSession;	
		$auth = Zend_Auth::getInstance();
		if(isset($_POST) && isset($_POST['admin_id']) && isset($_POST['user_id']))
		{
			if(isset($this->view->user) && isset($this->view->user->user_id))
			{
				$auth->clearIdentity();
					
			}	
			
			$user_data=$this->modelSuper->Super_Get("users","user_id='".$_POST['user_id']."'","fetch");
								/* Zend_Auth Setup Code */
					$authAdapter = new Zend_Auth_Adapter_DbTable($this->_getParam('db'), 'users', 'user_email', 'user_password'," ? AND user_type='school'  and  user_status = '1' " /*, 'MD5(CONCAT(?, password_salt))'*/ );
					// Set the input credential values
					$authAdapter->setIdentity($user_data['user_email']);
					$authAdapter->setCredential($user_data['user_password']);
					$result = $auth->authenticate($authAdapter);// Perform the authentication query, saving the result				
				
				
					if($result->isValid())
					{ 
						// IF Auth Get the Record 
						$data = $authAdapter->getResultRowObject(null);
						$auth->getStorage()->write($data);
						$objSession->successMsg="You are logged in Successfully";
						$this->redirect("dashboard");
					}
					else
					{
						$objSession->errorMsg="Invalid Request";
						$this->redirect("index");	
					}
		}
		else
		{
			$objSession->errorMsg="Invalid Request";
			$this->redirect("index");	
		}
	}
		
	
	public function selectpaymentAction()
	{
 		
		global $objSession;
		$sd_id =  $this->_getParam("sd_id");
		$form = new  Application_Form_User();
		$formcredit = new  Application_Form_User();
		$this->view->form=$form;
		$form->bank_fields();
		$formcredit->CreditCardFields();
		$this->view->formcredit=$formcredit;
		$this->view->sd_id=$sd_id;
		$subscrption_data = $this->modelStatic->Super_Get("subscription_details","sd_id='".$sd_id."'","fetch");
		$this->view->subscrption_data=$subscrption_data;
		$plan_data = $this->modelStatic->Super_Get("subscription_plans","sp_id='".$subscrption_data['sd_plan_id']."'","fetch");
		$this->view->plan=$plan_data;
		if((isset($_POST['radioGroup']))&&($_POST['radioGroup']!=''))
		{
					$amount=$subscrption_data['sd_price'];
					$invoice_id='Uid_'.$subscrption_data['sd_user_id'].'_'.uniqid();
					$subscription_invoice=array('sd_invoice_id'=>$invoice_id);
					$is_insert = $this->modelStatic->Super_Insert("subscription_details",$subscription_invoice,"sd_id='".$subscrption_data['sd_id']."'");
					if(($_POST['radioGroup']==2))
					{
										
										$this->objPayapal=new Application_Model_Paypalrecurring();	
										$_POST['amount']=$amount;	
										$_POST['sd_id']=$sd_id;
										$_POST['startDate']=gmdate("Y-m-d H:i:s");
										$request_data=$_POST;
										$isSubscribed = $this->objPayapal->CreateRecurringPaymentsProfile($_POST);
										if($isSubscribed['RESPMSG']=='Approved')
										{
													$subscription_details= $this->modelStatic->Super_Get("subscription_details","sd_id='".$request_data['sd_id']."'","fetch");
													$data=array(
													'user_membership_status'=>1,
													'user_payment_status'=>1,
													'user_subscription_id'=>$request_data['sd_id'],
													'user_next_subscription_id'=>$request_data['sd_id'],
													'user_payment_date'=>gmdate('Y-m-d', strtotime("+30 days"))
													);
													$is_insert = $this->modelStatic->Super_Insert("users",$data,"user_id='".$subscription_details['sd_user_id']."'"); 
													$user_data = $this->modelStatic->Super_Get("users","user_id='".$subscription_details['sd_user_id']."'","fetch");
													//*********************Send reg mail to user***********************//
													$isSend = $this->modelEmail->sendEmail('registration_email',$user_data,$user_data['user_o_password']);
													//*********************Send reg mail to user*****`******************//
													
													$data=array(
													'sd_active'=>1,
													'sd_profile_id'=>$isSubscribed['PROFILEID'],
													'sd_payment_method'=>'paypal',
													'sd_payment_done'=>'done',
													);
													$is_insert = $this->modelStatic->Super_Insert("subscription_details",$data,"sd_id='".$request_data['sd_id']."'"); 
													$objSession->successMsg = "Your payment is done Sucessfully.Please verfily your email address for login";
													$this->_redirect('login');
										}
										else
										{
											$objSession->errorMsg =$isSubscribed['RESPMSG'];
											$this->view->error_status=1;
											$formcredit->user_first_name->setValue($_POST['user_first_name']);
											$formcredit->user_last_name->setValue($_POST['user_last_name']);
											$formcredit->user_cardnumber->setValue($_POST['user_cardnumber']);
											$formcredit->user_expirationYear->setValue($_POST['user_expirationYear']);
											$formcredit->user_expirationMonth->setValue($_POST['user_expirationMonth']);
											$formcredit->user_card_type->setValue($_POST['user_card_type']);
											$formcredit->user_cvv->setValue($_POST['user_cvv']);
											
										}
					}
					else
					{
						//$data=$_POST;
						$data['user_bank_account_number']=$_POST['user_bank_account_number'];
						$data['user_account_holder_name']=$_POST['user_account_holder_name'];
						$data['user_bank_name']=$_POST['user_bank_name'];
						$data['user_branch_name']=$_POST['user_branch_name'];
						$data['user_o_account']=$_POST['user_o_account'];
						//unset($data['Next']);
						//unset($data['radioGroup']);
						$data['user_subscription_id']=$sd_id;
						$data['user_next_subscription_id']=$sd_id;
						
						//*************** Update subscription detail and payment type *********************/
						$subscription_updates=array(
							'sd_payment_method'=>'bank',
							'sd_payment_done'=>'notdone',
							'sd_payment_info'=>serialize($data),
							'sd_invoice_id'=>$invoice_id,
						);
						//prd($data);
						$is_insert = $this->modelStatic->Super_Insert("subscription_details",$subscription_updates,"sd_id='".$subscrption_data['sd_id']."'");
						//*************** Update subscription detail and payment type *********************/
						$is_insert = $this->modelStatic->Super_Insert("users",$data,"user_id='".$subscrption_data['sd_user_id']."'"); 
						//prd($is_insert);
						$user_data = $this->modelStatic->Super_Get("users","user_id='".$subscrption_data['sd_user_id']."'","fetch");
										//*********************Send reg mail to user***********************//
						$isSend = $this->modelEmail->sendEmail('registration_email',$user_data,$user_data['user_o_password']);
						$objSession->successMsg = "An activation email was sent to your registered email address to activate your account.Activate your account for login";
						$this->_redirect('user/buyerregister/done/1');
						
					}
			
		}
	}
	
	/* Register User  */
	public function registerAction(){
 		global $objSession;
		$this->view->page_slug="Sign_up";
		$this->view->pageHeading="Sign Up";
		$form = new  Application_Form_User();
		$form->register();	 		
		if($this->getRequest()->isPost()){/* begin : isPost() */			
			$posted_data = $this->getRequest()->getPost();
			
 			if($form->isValid($posted_data)){ /* Begin : isValid()  */
 				
				$this->modelUser->getAdapter()->beginTransaction();
				
				 $data = $form->getValues();
				 
				 $data['user_password']=md5($data['user_password']);
				 $data['user_password_text']=$data['user_password'];
				 
				 // ================ add  ==========
				 date_default_timezone_set('America/Los_Angeles');	// PDT time
				 // ================================
								  
				//  $data['user_created']=gmdate('Y-m-d H:i:s');
				 $data['user_created']=date('Y-m-d H:i:s');
				// $data['user_type']='school';

			
				$response = $_POST["g-recaptcha-response"];
				$url = 'https://www.google.com/recaptcha/api/siteverify';
				$data1 = array(
					'secret' => '6LdzW7MUAAAAANIheXQIIFZRfe_PjVThoLGrHafu',
					'response' => $_POST["g-recaptcha-response"]
				);
				$options = array(
					'http' => array (
						'method' => 'POST',
						'content' => http_build_query($data1)
					)
				);
				$context  = stream_context_create($options);
				@$verify = file_get_contents($url, false, $context);
				$captcha_success=json_decode($verify);
				if ($captcha_success->success==true) {
					$isInserted = $this->modelUser->add($data);
					
					if(is_object($isInserted)){
						
						if($isInserted->success){
							$this->modelUser->getAdapter()->commit();
							
							$user_id=$isInserted->inserted_id;
							
								$get_all_admin_instuments=$this->modelSuper->Super_Get("Instruments","Instrument_status='0' and Instrument_active='1'","fetchAll");
								/* Add default Instrumenst for user */
								foreach($get_all_admin_instuments as $k=>$v)
								{
								$instrument_arr=array();
								// ================ add  ==========
								date_default_timezone_set('America/Los_Angeles');	// PDT time
								// ================================
								
								$instrument_arr=array('Instrument_name'=>$v['Instrument_name'],
								// 'Instrument_date'=>gmdate('Y-m-d H:i:s'),
								'Instrument_date'=>date('Y-m-d H:i:s'),
								'Instrument_userid'=>$user_id,
								'Instrument_schoolid'=>$user_id,
								'Instrument_status'=>1,
								'Instrument_active'=>1
								);	
								$kll=$this->modelSuper->Super_Insert("Instruments",$instrument_arr);
							
								}	 
							
						
							$objSession->successMsg = " Registration Successfully Done  ";
							$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
						}
						
						$this->modelUser->getAdapter()->rollBack();

						if($isInserted->error){
							
							if(isset($isInserted->exception)){/* Genrate Message related to the current Exception  */
							
							}
							
							$objSession->errorMsg = $isInserted->message;							 
						}
						
					}else{
						$objSession->errorMsg = " Please Check Information again ";
					}

				} else if ($captcha_success->success==false) {
					$objSession->errorMsg = "You are a bot! Try again to prove your identity!";
				}
 				
			}/* end : isValid()  */
			else{/* begin : else isValid() */
				$objSession->errorMsg = " Please Check Information Again..! ";
 			}/* end : else isValid() */
			
 		}/* end : isPost() */
		
		
		$this->view->form = $form;
	}

  	
	
	
	 
	
	/*Social media sign up*/
	
	/* 	Forgot Password Send Reset Key to User Email Address 
	 *	@
	 *  Author  - Varun
	 */
  	public function forgotpasswordAction(){
		
 		global $objSession;	
		
		$this->view->pageHeading="Forgot Password";
		
		$form = new  Application_Form_User();
 		$form->forgotPassword();
			
 		if($this->getRequest()->getPost()){
		  
			$posted_data  =  $this->getRequest()->getPost();
			
			if($form->isValid($posted_data)){
 				$received_data = $form->getValues();
			
 				$isSend = $this->modelUser->resetPassword($received_data['user_email']);
 				if($isSend){
					$objSession->successMsg = " Mail has been sent to your account ..! ";
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_dashboard");
				}
				else{
					$objSession->errorMsg = " Please Check Information Again..! ";
				}
			}else{
				$objSession->errorMsg = " Please Check Information Again..! ";
  			}
		  
		}
		
		$this->view->form = $form;
	}
	
	
	
	/* 	Handle Email Link and Reset the Password 
	 *	@
	 *  Author  - Varun
	 */
	public function resetpasswordAction(){
		 
		 global $objSession;
		 
		 $this->view->pageHeading = "Reset Password";
  		
		 $form = new Application_Form_User();
		 $form->resetPassword();
		 
 		 $key = $this->_getParam('key');
		 
		 if(empty($key)){
 			 $objSession->errorMsg = "Invalid Request for Reset Password ";
			 $this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
		 }
		 
 		 $user_info = $this->modelUser->get(array("key"=>"$key"));
		 
		 if(!$user_info){
			 $objSession->errorMsg = "Invalid Request for Password Reset , Please try again .";
			 $this->_redirect("user/login");
		 }
		 
 
 		 if($this->getRequest()->getPost()){
			 
			 $posted_data  = $this->getRequest()->getPost();
			 
			 if($form->isValid($posted_data)){
				 
				$data_to_update = $form->getValues() ;
				
				$data_to_update['pass_resetkey']="";
				$data_to_update['user_reset_status']="0";
				
				$data_to_update['user_password'] = md5($data_to_update['user_password']);
				
				$ischeck = $this->modelUser->add($data_to_update,$user_info['user_id']);
				
				//prd($ischeck );
				
				if($ischeck){
					$objSession->successMsg = " Password change Successfully Done ..! ";
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
					
				}
						
			 }else{
					$objSession->errorMsg = " Please Check Information Again..! ";
 			 }/* end : Else isValid() */
 		 
		 }/* end  : isPost()  */
		 
		 
		 
		 $this->view->form = $form;
		 
	 }
	 
	 
	 
	 
	 
	 /* Email Varification and Account Activation 
	 *	@
	 * 
	 */
	 public function activateAction(){
		
 		global $objSession;
		
		$this->view->pageHeading = "Active Account";
		
 		$key = $this->_getParam('key');
		 
		$user_info = $this->modelUser->get(array("key"=>"$key"));
	
		 
		 if(!$user_info){
			 $objSession->errorMsg = "Invalid Request for Account Activation ";
			 $this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
		 }
		 
 		 $this->modelUser->add(array('pass_resetkey'=>'',"user_reset_status"=>"0",'user_email_verified'=>'1','user_status'=>"1"),$user_info['user_id']);
		 
		 	if($user_info['user_type']=='family')
		 {
			 $modelschool = new Application_Model_SchoolModel();
			 $get_all_student=array();
			$get_all_student=	$modelschool->getallfamilystudent($user_info['user_id']);
			foreach($get_all_student as $k=>$v)
			{
				 $this->modelUser->add(array('pass_resetkey'=>'',"user_reset_status"=>"0",'user_email_verified'=>'1','user_status'=>"1"),$v['user_id']);	
			}
			$get_all_student=	$modelschool->getallfamilystudent($user_info['user_id']);
	   }
		 $objSession->successMsg = "Your Account is Successfully Activated , Please Login";
		$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
	 
	}
	
	 
	 
	
	public function changepasswordAction(){
		
		global $objSession;
		
 		if(!$this->view->user){
			$objSession->infoMsg = "Please Login First to make Changes";
			$this->_redirect("login");
		}
 			 		
		$this->view->pageHeading = "Change Password";
		
		$form = new Application_Form_User();
		$form->changePassword();
		
 		
		if($this->getRequest()->getPost()){
			
			
			
			$posted_data = $this->getRequest()->getPost();
			
			 
				
			if($form->isValid($posted_data)){
				
				$checkOldPassword = $this->modelUser->get(array("where"=>" user_password='".md5($posted_data['user_old_password'])."' and user_id=".$this->view->user->user_id));
			
				if($checkOldPassword){
					
 	 				if($posted_data['user_password'] == $posted_data['user_rpassword']){
					
						//prd($posted_data);
						
						 
				
  						$ischeck = $this->modelUser->add($form->getValues(), $checkOldPassword['user_id']);
						
						if($ischeck){
							$objSession->successMsg = " Password change Successfuly Done ..! ";
							$this->_redirect('user/changepassword');
							
						}
						else{
							$objSession->errorMsg = " Please Check Information Again..! ";
						}
						 
					}else{
						
 						$form->user_password->setErrors(array('Password Mismatch'));
						$form->user_rpassword->setErrors(array('Password Mismatch'));
						$objSession->errorMsg = " Please type the same password.!";
						$this->render('changepassword');
					}
			}else{
				$form->user_old_password->setErrors(array('Old Password is not match '));
				$objSession->errorMsg = " This Old Password is not match.!";
				$this->render('changepassword');
			}
			
			}else{
				$objSession->errorMsg = " Please Check Information Again..! ";
 				$this->render('changepassword');
			}
		}
		
		$this->view->form = $form;
	
	}
	
	
	/* Send Verification Email */
	public function sendverificationAction(){
 		global $objSession;
		
		$modelEmail = new Application_Model_Email();
		
  		$data_form_values = (array) $this->view->user ;
   		if($this->view->user->user_email_verified!="1"){
  			$user_email_key = md5("ASDFUITYU"."!@#$%^$%&(*_+".time());
			$data_to_update = array("user_email_verified"=>"0","user_email_key"=>$user_email_key);
			$this->modelUser->update($data_to_update, 'user_id = '.$this->view->user->user_id);
			$data_form_values['user_email_key'] = $user_email_key ;
			$modelEmail->sendEmail('email_verification',$data_form_values);
 			$objSession->successMsg = " Email Successfully Send to your email address , please follow the verification link to verify the email address ";
 		}else{
			$objSession->infoMsg = "Your Email Address is already verified..";
		}
  		$this->_redirect("profile");
	}
	
	
	
	/* Email Varification  
	 *	@
	 *  Author  - Varun
	 */
	 public function verifyemailAction(){
 	
		global $objSession;
 	
		$key = $this->_getParam('key');
		 


		if(empty($key)){
			$objSession->errorMsg = "Please Check Verifications link again";
			 $this->_redirect("login");	
		}
		
 		$user_info = $this->modelUser->get(array("where"=>"user_email_key='".$key."'"));
		 
 		 if(!$user_info){
			 $objSession->errorMsg = "Invalid Request for Account Activation ";
			 $this->_redirect("profile");
		 }
		 
		 $this->modelUser->update(array('user_email_verified'=>'1',"user_email_key"=>""),"user_id=".$user_info['user_id']);
		 
		 $objSession->successMsg = "Your Email Address is successfully verified";
		 $this->_redirect("profile");
 	}
	
  	
	
 	
	/* 	** Private Method for Handling the Uploaded Image 
	 *	@
	 *  Author  - Varun
	 */
	private function upload_user_image(){
		
 		$adapter = new Zend_File_Transfer_Adapter_Http();
 		
		$video = $adapter->getFileInfo('user_image');
		
   		$video_extension = $video['user_image']['name'];
		
 		$extension = explode('.',$video['user_image']['name']); 
		
 		$extension = array_pop($extension);
		
  		$name_for_video = md5(rand(1,999)."@#$%@#&^#$@".time()).".".$extension;
		
  		rename(ROOT_PATH .'/images/profile/'.$video_extension ,  ROOT_PATH .'/images/profile/'.$name_for_video);
		
		return $name_for_video ;
  	}
	
	
	
	/* 	Ajax Call For Checking the Email Existance for the user email 
	 *	@
	 *  Author  - Varun
	 */
	public function checknameuserAction(){

 		$firstname = strtolower($this->_getParam('firstname'));
		$lastname = strtolower($this->_getParam('lastname'));
		$family_type=$this->_getParam('family_type');
		$school_id=$this->view->user->user_id;
		if($this->view->user->user_type!='school')
		{
			$school_id=$this->view->user->user_school_id;	
		}
		$user_id = ($this->_getParam('user_id'));
		if(isset($family_type) && $family_type=='family')
		{
				$where='user_first_name="'.$firstname.'" and user_last_name="'.$lastname.'" and (user_type="student" or user_type="family") and user_school_id="'.$school_id.'"';	
		}
		else
		{
				$where='user_first_name="'.$firstname.'" and user_last_name="'.$lastname.'" and user_type="student" and user_school_id="'.$school_id.'" ';	
		}
		

		if(isset($user_id) && !empty($user_id))
		{
			$where.=' and user_id!="'.$user_id.'"';	
		}
		
		$user_data=$this->modelSuper->Super_Get("users",$where,"fetch");
		
		if(empty($user_data)){
 			
				echo 1;
 			
 		}
		else
		{
				echo 0;
		}
 		exit();
		
 
		
	}
	
	
	/* 	Ajax Call For Checking the Email Existance for the user email 
	 *	@
	 * 
	 */
	 
	 public function checkemailfamilyAction()
	 {

 		$email_address = strtolower($this->_getParam('family_email_address'));
		$user_id = false ;
		$email = $this->modelUser->checkEmail($email_address,$user_id);
		if($email)
			echo json_encode("`$email_address` already exists , please enter any other email address ");
		else
			echo json_encode("true");
		exit();
	}
	 
	public function checkemailAction(){

 		$email_address = strtolower($this->_getParam('user_email'));
		$exclude = strtolower($this->_getParam('exclude'));
		$user_id = false ;
		if(!empty($exclude)){
			 $user = $this->view->user;
			 $user_id =$user->user_id;
			
		}

		$email = $this->modelUser->checkEmail($email_address,$user_id);
		
		$rev = $this->_getParam("rev");
		
		if(!empty($rev)){
 			if($email)
				echo json_encode("true");
 			else
				echo json_encode("`$email_address` is not registered , please enter valid email address ");
 			exit();
 		}
		
 
		if($email)
			echo json_encode("`$email_address` already exists , please enter any other email address ");
		else
			echo json_encode("true");
		exit();
	}
	public function checkusernameAction()
	 {
		 

 		$user_username = ($this->_getParam('user_username'));
		$check_user_data=$this->modelSuper->Super_Get("users","user_username='".$user_username."' and user_id!='".$this->view->user->user_id."'","fetch");
		if(empty($check_user_data))
		{
			echo json_encode("true");
		}
		else
		{
			echo json_encode("`$user_username` is already exists , please enter unique user name");
		}
		
			
		exit();
	
		
	 }
	 
	 public function checkuserandmailAction()
	 {
		$user_username = ($this->_getParam('user_email'));
		if(filter_var($user_username, FILTER_VALIDATE_EMAIL))
		{
			/* This is a valid email address */	
			$check_user_data=$this->modelSuper->Super_Get("users","user_email='".$user_username."'","fetch");
		}
		else
		{
			/* This is only a username */
			$check_user_data=$this->modelSuper->Super_Get("users","user_username='".$user_username."'","fetch");
		}
		
		
		if(empty($check_user_data))
		{
			echo json_encode("true");
		}
		else
		{
			if(filter_var($user_username, FILTER_VALIDATE_EMAIL))
			{
				echo json_encode("`$user_username` is already exists , please enter unique email address");
			}
			else
			{
				echo json_encode("`$user_username` is already exists , please enter unique user name");	
			}
		}
				
		exit();		 
	 }
	 
	
	public function checkemailstudentAction(){

 		$email_address = ($this->_getParam('user_email'));
		$user_id = ($this->_getParam('user_id'));
			$where='user_email="'.$email_address.'" and user_type!="family"';
			$user_data=$this->modelSuper->Super_Get("users",$where);
		if(empty($user_data))
		{
			$where='user_email="'.$email_address.'" and user_type="family"';
			$user_data=$this->modelSuper->Super_Get("users",$where);
			if(empty($user_data))
			{
				echo 1;			
			}
			else
			{
				echo 2;		
			}
			
		}
		else
		{
			if(isset($user_id) && !empty($user_id))
		{
			$where='user_email="'.$email_address.'" and user_type="student" and user_id!="'.$user_id.'"';

		}
		else
		{
			$where='user_email="'.$email_address.'" and user_type="student"';

		}
			$user_data=$this->modelSuper->Super_Get("users",$where);
			if(empty($user_data))
			{
				echo 0;		
			}
			else
			{
				echo 3;		
			}
		}
		
			
		exit();
	}
	
	public function checkstudentexistsAction(){

 		$email_address = ($this->_getParam('user_email'));
		$user_id = ($this->_getParam('user_id'));
		if($user_id)
		{
			$where='user_email="'.$email_address.'" and user_id!="'.$user_id.'" and user_type!="admin"';	
		}
		else
		{
			$where='user_email="'.$email_address.'" and user_type!="admin"';	
		}
			
			$user_data=$this->modelSuper->Super_Get("users",$where);
		if(empty($user_data))
		{
			echo 1;	
		}
		else
		{
			echo 0;	
		}
			
		exit();
	}
	
	public function checkfamilyexistsAction(){
	
		$user_id = ($this->_getParam('user_id'));
		$family_type=$this->getRequest()->getParam('family_type');
		$email_options=$this->getRequest()->getParam('email_options');
		$stu_family=array();
		$family_id='';
		$student_id='';
		if(isset($user_id))
		{
			$stu_family=$this->modelSuper->Super_Get("student_family","s_f_sid='".$user_id."'","fetch");
			if(!empty($stu_family))
			{
				$family_id=$stu_family['s_f_fid'];
				$student_id=	$stu_family['s_f_sid'];
			}
			
			
		}
 		$email_address = ($this->_getParam('user_email'));
		if(empty($stu_family))
		{
			$where='user_email="'.$email_address.'" and user_type!="admin"';	
			$user_data=$this->modelSuper->Super_Get("users",$where);
		}
		else
		{
			
				$where='user_email="'.$email_address.'" and user_type!="admin" and user_id!="'.$family_id.'"';	
				$user_data=$this->modelSuper->Super_Get("users",$where);	
				
				if(!empty($user_data) && $student_id!='')
				{
					
						$where='user_email="'.$email_address.'" and user_type!="admin" and user_id!="'.$student_id.'"';	
					$user_data=$this->modelSuper->Super_Get("users",$where);	
				}
		}
		
		if(empty($user_data))
		{
			echo 1;	
		}
		else
		{
			echo 0;	
		}
			
		exit();
	}
	
	
	public function checkemailexcludeAction(){

 		$email_address = strtolower($this->_getParam('user_email'));
		$user_id = strtolower($this->_getParam('user_id'));
			if($this->view->user->user_type!='student')
			{
				$email = $this->modelUser->checkEmail($email_address,$user_id);	
			}
			else
			{
				$linked_family=$this->modelSuper->Super_Get("student_family","s_f_sid='".$user_id."'","fetch");
			
				$email = $this->modelSuper->Super_Get("users","(user_email='".$email_address."' or user_username='".$email_address."') and (user_id!='".$this->view->user->user_id."' and user_id!='".$linked_family['s_f_fid']."')","fetch");	
			
					
			}
			
			if($email)
			echo json_encode("`$email_address` already exists , please enter any other email address ");
			else
			echo json_encode("true");	
		
		exit();
	}
	
	public function checkemailexcludeusernameAction(){

 		$email_address = strtolower($this->_getParam('user_username'));
		$user_id = strtolower($this->_getParam('user_id'));
		
			$email = $this->modelSuper->Super_Get("users","(user_username='".$email_address."' or user_email='".$email_address."') and user_id!='".$user_id."'","fetch");
			if($email)
			echo 0;
		//	echo json_encode("`$email_address` already exists , please enter any other unique username ");
			else
			echo json_encode("true");		
		
		
		exit();
	}
	
	
	
	
	
	/* 	Ajax Call For Checking the Old Password for the Logged User 
	 *	@
	 *  Author  - Varun
	 */
	public function checkpasswordAction(){
		
		$auth = Zend_Auth::getInstance();
		
		if($auth->hasIdentity()){
			
			$user_password = md5($this->_getParam('user_old_password'));
 			$user = $this->modelUser->get(array('where'=>"user_password='".$user_password."' and user_id=".$this->view->user->user_id));
			
			if(!$user){
				echo json_encode("Old Password Mismatch , Please Enter Correct old password");
			}else{
				echo json_encode("true");	
			}
		}else{
			echo json_encode("Please Login For Make Changes..");
		}
				
 		exit();
	}
	
	
	
	
	/* 	Logout Action 
	 *	@ *  Author  - Varun
	
	 */
  	public function logoutAction(){ 
 	    global $objSession;	
		
		$auth = Zend_Auth::getInstance();
 	
		if($this->view->user){
			
			$user =  $this->view->user;
			if(isset($_COOKIE['user_id']))
					{
					setcookie("user_id", NULL, mktime() - 3600, "/");
					}	
					$last_login_arr=array();
					$last_login_arr=array('user_last_login'=>date("Y-m-d"));
					$this->modelSuper->Super_Insert("users",$last_login_arr,"user_id='".$user->user_id."'");
			if($user->user_login_type!="normal"){
				
				 
				if($user->user_oauth_provider=="facebook"){
					
 					$facebook = new Facebook(array(
						'appId' => Zend_Registry::get("keys")->facebook->appId ,
						'secret' =>Zend_Registry::get("keys")->facebook->secret ,
						'cookie' => true
					));
					
					$auth->clearIdentity();
					
					$logout_url = $facebook->getLogoutUrl(array( 'next' => APPLICATION_URL."/login"));
					
					header("Location:".$logout_url);
					 		
					$objSession->successMsg = "You are now logged out. ..! ";
					
					exit();
				
 				}
 			}
			
			$auth->clearIdentity();
 		
			$objSession->successMsg = "You are now logged out. ..! ";

			$this->redirect("index");
 				 
		}
			
			$objSession->successMsg = "You are now logged out. ..! ";

			$this->redirect("index");
	}
  	
}

