<?php
class SocialController extends Zend_Controller_Action
{
  	private $modelUser ,$modelContent; 
	 
	public function init(){
 		$this->modelUser = new Application_Model_User();
   	}


	/* Advance Facebook Share 
		(1) Can share on Facebook Wall
		(2) Can share on Groups
		(3) Can share on Pages 
	  */
	public function fbshareAction(){
		
 		$allParam = $this->getAllParams(); 
 		
		$facebook = new Facebook(array(
			'appId' => Zend_Registry::get("keys")->facebook->appId ,
			'secret' =>Zend_Registry::get("keys")->facebook->secret ,
			'allowSignedRequest' => false
		));
		
		 $user_id = $facebook->getUser();
 		 
		 if($user_id){
			 
			 if($this->getRequest()->isPost()){
				 /* Post is selected  */
				 
				$params = array(
					'link' => $allParam['p']['url'],
					'description' => $allParam['p']['summary'],
					'picture' =>  $allParam['p']['images'][0],
					"name" => $allParam['p']['title'],
					"caption" => $allParam['p']['url'],
				);
				
				
				$posted_data = $this->getRequest()->getPost();
				
				try {
					switch($posted_data['type']){
						case "fb_group": $type = $posted_data['name_group']."/feed"; break;
						case "fb_page":  $type = $posted_data['name_page']."/feed";	break;
						default :   	 $type = "/me/feed"; /* FB Wall Post */
					}
					
					$ret_obj = $facebook->api($type, 'POST',$params);
					
					$this->_helper->flashMessenger(array('success' => '<h4>Success!</h4>Project Shared Successfully'));
					
					$this->_redirect("");

				}catch(FacebookApiException $e) {
					
					$login_url = $facebook->getLoginUrl( array('scope' => 'email,user_birthday,user_mobile_phone,publish_stream,publish_actions,user_groups,manage_pages')); 
					header("Location : $login_url");
					exit();
				
				}
				  
				  
			 }else{
				 
				 /* (1) Get All Pages Which User Have //Get user pages details using Facebook Query Language (FQL) */
				try {
					$fql_query = 'SELECT page_id, name, page_url FROM page WHERE page_id IN (SELECT page_id FROM page_admin WHERE uid='.$user_id.')';
					$obj_fb_pages = $facebook->api(array( 'method' => 'fql.query', 'query' => $fql_query ));
					$this->view->obj_fb_pages = $obj_fb_pages; 
				} catch (FacebookApiException $e) {
					echo $e->getMessage();
					die;
				}
				 
				 /* (2) Get All Groups User Have  */
				 try{
					 $obj_fb_groups  = $facebook->api('/me/groups', 'GET' );
					 $this->view->obj_fb_groups = $obj_fb_groups['data'] ;
				 }catch(FacebookApiException $e){
					 echo $e->getMessage();
					 die;
				 }
				 
				 
			 }
		}else {
		 
		  $login_url = $facebook->getLoginUrl( array( 'scope' => 'email,user_birthday,user_mobile_phone,publish_stream,publish_actions,user_groups,manage_pages' ) );
		  header("Location:".$login_url);
		  exit; 
		
		} 
		
 	}

 	/*Social media sign up*/
	public function fbloginAction(){
 
 		global $objSession; 
		
  		$facebook = new Facebook(array(
			'appId' => Zend_Registry::get("keys")->facebook->appId ,
			'secret' =>Zend_Registry::get("keys")->facebook->secret ,
			'cookie' => false
		));
		
  		$user = $facebook->getUser();
 		
 		if(!$user){
			$login_url = $facebook->getLoginUrl(array( 'scope' => 'email'));
  			
			header("Location: " . $login_url);
			 
		}else{
			
			try{ 
				 $user_profile = $facebook->api('/me');
 			}catch(FacebookApiException $e){
				$objSession->errorMsg = $e->getMessage();
				$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
			}
 			
			/* for Already Exists */
 			
 			$isExists = $this->modelUser->get(array("where"=>"user_oauth_provider='facebook' and user_oauth_id='".$user_profile['id']."'")) ;
 		  
			if(!$isExists){
				
				 $this->modelUser->getAdapter()->beginTransaction();
				
				 $is_insert = $this->save_fb_data($user_profile);
				 
 				 if(is_object($is_insert) and $is_insert->error){
					$this->modelUser->getAdapter()->rollBack();
					$objSession->errorMsg = $is_insert->message ;
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_login');
 				}
				
				$this->modelUser->getAdapter()->commit();
				$isExists = $is_insert->data ;
   			} 
			
			
			$this->write_auth($isExists);
			
			$objSession->successMsg = "Logged In Successfully . ";
			
			$this->_helper->getHelper('Redirector')->gotoRoute(array(),'front_profile');
		}
 		exit();
 	}
	
	/* Twitter Login  */
	public function twitterloginAction(){
 		
		global $objSession;

		$TwitterOAuth = new TwitterOAuth(Zend_Registry::get("keys")->twitter->oauth_token,Zend_Registry::get("keys")->twitter->oauth_token_secret );   
		
		$oauth_verifier  = $this->_getParam('oauth_verifier');
		
 		if(empty($oauth_verifier)||!isset($_SESSION['socail_login'])){
			
   			$request_token = $TwitterOAuth->getRequestToken(APPLICATION_URL."/social/twitterhandler");
			
			$_SESSION['oauth_token'] = $request_token['oauth_token'];
			$_SESSION['oauth_token_secret'] = $request_token['oauth_token_secret'];
	
  			if ($TwitterOAuth->http_code == 200){
				$_SESSION['socail_login'] = true ;
				$url = $TwitterOAuth->getAuthorizeURL($request_token['oauth_token']);
   				header("Location: $url");	
			}else{
				$objSession->error = " Twitter Configuration failed . ";
			 	$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
				
			}
  			
		}else{ /* Get Verifier */
			
			 
			$TwitterOAuth = new TwitterOAuth(Zend_Registry::get("keys")->twitter->oauth_token,Zend_Registry::get("keys")->twitter->oauth_token_secret,$_SESSION['oauth_token'],$_SESSION['oauth_token_secret']); 
   			
			$access_token = $TwitterOAuth->getAccessToken($oauth_verifier);			
  			
			$user_info = $TwitterOAuth->get('account/verify_credentials');
			
 			if (isset($user_info->error)){
				$objSession->errorMsg = $user_info->error;
				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
			} 
 
 			 $isExists = $this->modelUser->get(array("where"=>"user_oauth_provider='twitter' and user_oauth_id='".$user_info->id."'")) ;
			 
			if(!empty($isExists)){
 				$this->write_auth($isExists);
 				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_profile");
 			}
			
			$objSession->twitter_login = true ;
			$objSession->twitter_data = $user_info ;
 			
 			/* Get User Email Addresss  */
			$this->_redirect("social/twitterhandler");
 		}
		 exit();
  	}
	
	
	/* Get Email Address From the User  */
	public function twitterhandlerAction(){
		
		global $objSession ;
		
		if(!isset($objSession->twitter_login)){
			$objSession->errorMsg = "Please Login "; 
			$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
		}
		
		$this->view->pageHeading = "Twitter Signin";
		
		
		$form = new Application_Form_User();
		$form->twitter_email();
		 
		if($this->getRequest()->isPost()){
			
			$posted_values = $this->getRequest()->getPost();
			
			if($form->isValid($posted_values)){
				
				$form_data  = $form->getValues();
  
				$received_data  = (array) $objSession->twitter_data ;
				$received_data['email'] = $form_data['user_email'] ;
				
				$this->modelUser->getAdapter()->beginTransaction();
				
  				$is_insert = $this->save_twitter_data($received_data);

				unset($objSession->twitter_login);
				unset($objSession->twitter_data);
				
 				
				if(is_object($is_insert) and $is_insert->success){
					
					$this->modelUser->getAdapter()->commit();
					
					$this->write_auth($is_insert->data);
					
					$objSession->successMsg  = " Complete Your Profile Information ";
	 				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_profile");
 				}
				
				$this->modelUser->getAdapter()->rollBack();
 				$objSession->errorMsg = " Enable to login... ! Please try again ";
 				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
 			}
		}
		
 		$this->view->form = $form ;
 		$this->view->twitter_user = $objSession->twitter_data ;
 	}
	
	



	
	/* Insert Data into Database When User is First Time Register Via Facebook */
 	private function save_fb_data($received = false){
		
 		//$generated_password = genratePassword($received['name']);
		$generated_password =12345;
		$image_name = $this->receive_profile_image($received , "facebook");
		
 		$data_to_save = array(
			'user_oauth_id' =>$received['id'],
			'user_oauth_provider'=>'facebook',
			'user_login_type'=>'social',
 			'user_image'=>$image_name,
			'user_type'=>"user",
			'user_reset_status'=>'1',
			'user_status'=>'1',
			'user_password'=>md5($generated_password),
			'user_password_text'=>$generated_password,
			'user_email'=>$received['email'],
			'user_email_verified'=>'1',
			'user_email_verified'=>'1',
			'user_first_name'=>$received['first_name'],
			'user_last_name'=>$received['last_name']
		);
		
 		$inserted = (array) $this->modelUser->add($data_to_save);
		$inserted['data'] = $data_to_save ;
			 
		return (object) $inserted ;

 	}
 	
 	/* Insert Data into Database When User is First Time Register Via Twitter */
	private function save_twitter_data($received = false){
 
  		//$generated_password = genratePassword($received['name']);
		$generated_password =12345;
		$image_name = $this->receive_profile_image($received , "twitter");
 		 
  		$data_to_save = array(
			'user_oauth_id' =>$received['id_str'],
			'user_oauth_provider'=>'twitter',
			'user_login_type'=>'social',
 			'user_image'=>$image_name,
			'user_type'=>"user",
			'user_reset_status'=>'1',
			'user_status'=>'1',
			'user_password'=>md5($generated_password),
			'user_password_text'=>$generated_password,
			'user_email'=>$received['email'],
			'user_email_verified'=>'1',
			'user_email_verified'=>'1',
			'user_first_name'=>$received['name'],
			'user_last_name'=>$received['name']
		);
  		 
  		$inserted = $this->modelUser->add($data_to_save);
		
		$inserted ->data = $data_to_save ;
		
		return $inserted;
 
 	}
	
	/* Code to Receive Profile Image  */
 	private function receive_profile_image($received , $provider){
		
 
		switch($provider){
			
			case 'facebook':
				$image_url ="https://graph.facebook.com/".$received['id']."/picture?width=400&height=400";
				$profile_image = time().'_'.$received['name'].'.png';
				
			 break;
			
			case 'twitter':
				 $image_url = str_replace("_normal","",$received['profile_image_url_https']);
				 $extension = getFileExtension($image_url);
  				 $profile_image=time().'_'.$received['screen_name'].'.'.$extension;
 			break;
			
			case 'googleplus': 
				$image_url ="https://graph.facebook.com/".$received['user_profile']['id']."/picture?width=400&height=400";
				$profile_image=time().'_'.$received['user_profile']['name'].'.png';
			break;
			
			
			default : "";
		}
 		
		
		$content = file_get_contents($image_url);
		file_put_contents(PROFILE_IMAGES_PATH.'/'.$profile_image,$content);
		
		
  		$thumb_config = array("source_path"=>PROFILE_IMAGES_PATH,"name"=> $profile_image);
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
		Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
		
		
		return $profile_image ;
	}
	
	/*	Set User Auth and make User Logged In */
	private function write_auth($data){
		
		global $objSession; 
		
		$zend_auth = Zend_Auth::getInstance();
		
		$dbAdapter = Zend_Db_Table::getDefaultAdapter();
		
		$authAdapter = new Zend_Auth_Adapter_DbTable($dbAdapter);
		
		$authAdapter->setTableName('users')->setIdentityColumn('user_email')->setCredentialColumn('user_password');

		$authAdapter->setIdentity($data['user_email']);

		$authAdapter->setCredential($data['user_password']);
		
 		$result = $zend_auth->authenticate($authAdapter);	

		if(!$result->isValid()){
 			$objSession->errorMsg = " Please Check Information again ";
			 $this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_login");
  		} 
			
		$user = $authAdapter->getResultRowObject(null, 'user_password');
		$zend_auth->getStorage()->write($user);
 		return true ;
 	}
	
 
}

?>