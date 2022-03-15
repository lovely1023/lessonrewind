<?php
class Application_Plugin_SetLayout extends Zend_Controller_Plugin_Abstract
{	
	
	protected $_defaultRole = 'all';
	protected $model = '';
	private $acl = '';
	public $roleArr =  array ("0" => "all");
	public $loggedRole = "";
	
	public $restricted = array("user"=>array("register" , "login" , "forgotpassword"));
	
	private $_site_assets  ,$_assets_path , $_view , $_logged_user = false , $view;
	
  
 	/* 	Set Document Type Layout 
	 *	@
	 *  Author  - Varun
	 */
	protected function _initDoctype() {
	  
	  $this->bootstrap('view');
	  
	  $view = $this->getResource('view');
	  
	  $view->doctype('XHTML_STRICT');
	  
	  $view->setEncoding('UTF-8');
	}
	
	
  
  	/* 	Pre Dispatch Setting  
	 *	@
	 *  Author  - Varun
	 */
    public function preDispatch(Zend_Controller_Request_Abstract $request){ 
		
		global $_site_assets_front_admin ,  $_site_assets_path_front_admin /* Admin / Front Site Assets */ , $_allowed_resources;
		
		$this->db = Zend_Registry::get("db");
		
 		$this->_site_assets = $_site_assets_front_admin ;
		$this->_assets_path = $_site_assets_path_front_admin;
		$this->_allowed_resources = $_allowed_resources ;
		  
		$layout = Zend_Layout::getMvcInstance();		 
		$this->view = $view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		
		 
		 
		
		$this->modelUser = new Application_Model_User();
		
  		
		/* Module Specific Settings  */
		
		switch($request->getModuleName()){
			
			case 'admin': {/* Admin */
				Zend_Registry::get('Zend_Translate')->setLocale('en');	
 				$this->_set_identity($request);
 				
				$ErrorHandler = Zend_Controller_Front::getInstance()->getPlugin("Zend_Controller_Plugin_ErrorHandler");
 				$ErrorHandler->setErrorHandlerModule("admin");
				
 				$this->_handleRedirects($request);

			 
				$allContentBloks = $this->db->select()->from('content_block',array("content_block_title"))->query()->fetchAll();
				$view->all_content_blocks = $allContentBloks ;
					
 			
 				$layout->setLayoutPath(APPLICATION_PATH.'/layouts/admin/');
				
 				
			}/* End Admin */
			break;
			
			
			default:{/* Front  */
			
				$this->_set_identity($request);
   				
				$this->_handleRedirects($request);
				
 			}/* End Default Module */
			
 		}
		
 		$this->_getAssets($request);
		
	
		$this->loadSetting();
 	}
	
	
	
		
	
	
	/* 
	 *	Check User / Admin Identity and Assign user identity to respective views
	 *	@
	 *  Author  - Varun
	 */
	private function _set_identity($request){
		
		
		if($request->getModuleName()=='default'){
 			
			$logged_identity = Zend_Auth::getInstance()->getInstance();
 			
			if($logged_identity->hasIdentity()){
				
				$logged_identity = $logged_identity->getIdentity();
				
				$user_info = (object) $this->modelUser->get($logged_identity->user_id);/**/
				$model = new Application_Model_Static();
				/*if($user_info->user_type=='school')
				{
				$plan_data=$model->Super_Get("subscription","subscription_user_id='".$user_info->user_id."'","fetch");
				global $objSession; 
				$d2 = date('Y-m-d H:i:s', strtotime('-30 days'));	
				if(empty($plan_data))
				{
					
					if(strtotime($d2)>strtotime($user_info->user_created))
					{
						if(($request->getActionName()!="subscription") and ($request->getActionName()!="plansubscription") and ($request->getActionName()!="logout"))
					{
					$objSession->errorMsg="Your account's 30 days trial period has been expired. Please update your subscription plan";
					$this->getResponse()->setRedirect(APPLICATION_URL . "/subscription");//
					}
					}
				}
				}*/
				if($user_info->user_status!='1' and $request->getActionName()!="logout"){
					$this->getResponse()->setRedirect(APPLICATION_URL . "/logout");//access denied	
				}
 
  				$this->view->user = $this->_logged_user =  $user_info;
				
				$auth = Zend_Auth::getInstance(); 
				
				$auth->getStorage()->write($user_info); //Now seession set is here
				
			}
			else{
		
				if(isset($_COOKIE['user_id']) && !empty($_COOKIE['user_id']))
				{
					
				
				//$logged_identity = $logged_identity->getIdentity();
				
				$user_info = (object) $this->modelUser->get($_COOKIE['user_id']);/**/
				$model = new Application_Model_Static();
				/*if($user_info->user_type=='school')
				{
				$plan_data=$model->Super_Get("subscription","subscription_user_id='".$user_info->user_id."'","fetch");
				global $objSession; 
				$d2 = date('Y-m-d H:i:s', strtotime('-30 days'));	
				if(empty($plan_data))
				{
					
					if(strtotime($d2)>strtotime($user_info->user_created))
					{
						if(($request->getActionName()!="subscription") and ($request->getActionName()!="plansubscription") and ($request->getActionName()!="logout"))
					{
					$objSession->errorMsg="Your account's 30 days trial period has been expired. Please update your subscription plan";
					$this->getResponse()->setRedirect(APPLICATION_URL . "/subscription");//
					}
					}
				}
				}*/
				if($user_info->user_status!='1' and $request->getActionName()!="logout"){
					$this->getResponse()->setRedirect(APPLICATION_URL . "/logout");//access denied	
				}
 
 
  				$this->view->user = $this->_logged_user =  $user_info;
				
				$auth = Zend_Auth::getInstance(); 
				
				$auth->getStorage()->write($user_info); //Now seession set is here
				
				
				}
				else
				{
					
				$this->view->user = $this->_logged_user =false;;
				}
			}
		}else{
			
			$Admin_User = Zend_Session::namespaceGet(ADMIN_AUTH_NAMESPACE);
			
 			 
			if(isset($Admin_User['storage'])){
				
				$user_info = (object) $this->modelUser->get($Admin_User['storage']->user_id);
 				
				$auth   = Zend_Auth::getInstance();	
				
				$auth->setStorage(new Zend_Auth_Storage_Session(ADMIN_AUTH_NAMESPACE));
				
				$auth->getStorage()->write($user_info); 
				 
				$Admin_User['storage'] = $user_info ;
				
 				$this->view->user = $this->_logged_user = $Admin_User['storage'];
 			}else{
				$this->view->user = $this->_logged_user =false;
			}
		}
		
	}
	
	
 	
	/* 
	 *	Load CSS and Javascripts Front/Admin Module Specific
	 *	@
	 *  Author  - Varun
	 */
 	private function _getAssets($request){
		
	
 		foreach($this->_site_assets  as $key=>$values){
 			if(isset($values[$request->getModuleName()][$this->_logged_user ?"user":"guest"]) and count($values[$request->getModuleName()][$this->_logged_user?"user":"guest"])){
 				foreach($values[$request->getModuleName()][$this->_logged_user?"user":"guest"] as $inner_key=>$inner_value){
 					if(is_array($inner_value)){/* Module specific Assets  */
						if(isset($inner_value[$request->getControllerName()])){
							if(isset($inner_value[$request->getControllerName()][$request->getActionName()])){
									
								foreach($inner_value[$request->getControllerName()][$request->getActionName()] as $moduleKey=>$moduleValue){
									if($key=='css'){	
								
										$this->view->headLink()->appendStylesheet($this->_assets_path[$key][$request->getModuleName()].$moduleValue);
									}else{
									
										$this->view->headScript()->appendFile($this->_assets_path[$key][$request->getModuleName()].$moduleValue);
									}
								}
							}
 						}
  					}else{
						//print_r($this->_assets_path[$key][$request->getModuleName()]);die;
						if($key=='css'){
							$this->view->headLink()->appendStylesheet($this->_assets_path[$key][$request->getModuleName()].$inner_value);
						}else{
							$this->view->headScript()->appendFile($this->_assets_path[$key][$request->getModuleName()].$inner_value);
						}
					}
				}
			}
		}
		$this->view->headLink()->headLink(array('rel' => 'shortcut icon','href' => HTTP_SITEIMG_PATH.'/fav-icon.png'),'APPEND');
  	}
	
	
	
	/* 	Handle Redirects For Admin and Front Module 
	 *	@
	 *  Author  - Varun
	 */
	private function _handleRedirects($request){
		date_default_timezone_set("UTC");
		/* Return if Current Request is related to any public folder or related to any resource */
 		if($request->getControllerName()=="public"){
			return ;
		}
		
		if(!$this->_logged_user){
 			if(!in_array($request->getControllerName(),$this->_allowed_resources[$request->getModuleName()])){
				if(isset($this->_allowed_resources[$request->getModuleName()][$request->getControllerName()]) and is_array($this->_allowed_resources[$request->getModuleName()][$request->getControllerName()])){
					if(in_array($request->getActionName(),$this->_allowed_resources[$request->getModuleName()][$request->getControllerName()])){
						return ;							
					}
				}
				 

				$site_name = explode("/",SITE_HTTP_URL);
				if($request->getModuleName()=='admin')
				{
					$exploder = $request->getModuleName()=="admin"?"admin":array_pop($site_name); 
					$redirect_url = explode($exploder,$_SERVER['REQUEST_URI']) ;
					$exploder = $exploder=="admin"?"/admin":"";
					$this->_response->setRedirect($request->getBaseUrl().$exploder .'/login?url='.urlencode("/".$exploder.$redirect_url[1]));
				}
				else
				{
					 $exploder =''; 
					 $exploder =$exploder;
					 $redirect_url=explode(array_pop($site_name),$_SERVER['REQUEST_URI']);
					 //global $objSession;
					// $objSession->errorMsg='You need to login first';
					 $this->_response->setRedirect($request->getBaseUrl().$exploder .'/login?url='.urlencode("/".$exploder.$redirect_url[1]));
						
				}
			}
		}
		else
		{
			global $_blocked_resources ;
			global $timezone;
			if($this->_logged_user->user_type=='site_subadmin')
			{

				

				$Roles = $this->db->select()->from('subadmin_roles',array( 'roles' => new Zend_Db_Expr('GROUP_CONCAT(sr_key)')))->where('sr_user_id="'.$this->_logged_user->user_id.'"')->query()->fetch();

				$roles=explode(",",$Roles['roles']);

				global $roleArr;

				$new_roles=$roleArr;

				foreach($roles as $key=>$val)

				{

					unset($new_roles[$val]);

				}

				

				if(!empty($new_roles))

				{

						//prn($_blocked_resources);

						$updated_blocked_respurces=array();

						foreach($new_roles as $key=>$val)

						{

							

							

							list($controller,$action)=GetRoles($key);

							//prn($controller);

							//prn($action);

							$action_array=array();

							if (!array_key_exists($controller, $updated_blocked_respurces)) 

							{

								//prn($controller);

								//prn($updated_blocked_respurces);

								$updated_blocked_respurces[$controller]=array();

							

								$action_array=GetActions($key);

								//prn($action_array);

								if(!empty($action_array)){

									foreach($action_array as $k1=>$v1)

									{

										//prn($v1);

										 array_push($updated_blocked_respurces[$controller],$v1);	

									}

								}

								//prd($action_array);

							}

						   

						}

						unset($_blocked_resources['admin']);

						$_blocked_resources['subadmin']=$updated_blocked_respurces;

				}

		

		

			}
			if($this->_logged_user->user_type!='admin')
			{
				$timezone="UTC";
				$school_id=$this->_logged_user->user_school_id;
				if($this->_logged_user->user_type=='school')
				{
						$school_id=$this->_logged_user->user_id;
				}	
				$modelSuper = new Application_Model_SuperModel();
				$school_data=$modelSuper->Super_Get("users","user_id='".$school_id."'","fetch",array("fields"=>array("user_timezone")));
				
				if($school_data['user_timezone']!='')
				{
					
					$user_timezone=$school_data['user_timezone'];
					$getTimezone=array();
					$getTimezone=$modelSuper->Super_Get("timezone","timezone_name='".$user_timezone."'","fetch");
					if(!empty($getTimezone)){
						 $timezone=$getTimezone['timezone_value'];	
					}
					else{
					 $timezone=$school_data['user_timezone'];
					}
					
				}
			
			}
			
			
			if(is_array($_blocked_resources[$this->_logged_user->user_type])){
				foreach($_blocked_resources[$this->_logged_user->user_type] as $key=>$value){
					if(is_int($key)){
						if($request->getControllerName()==$value){
							$this->_response->setRedirect($request->getBaseUrl()); // path
							break;
						}

					}elseif($key==$request->getControllerName()){
						if(!is_array($value)){

							if($request->getActionName()==$value){

								$this->_response->setRedirect($request->getBaseUrl());

								break;

							}	

						}else{
							foreach($value as $subValues)
							{
								if($request->getActionName()==$subValues)
								{
									$this->_response->setRedirect($request->getBaseUrl());
									break;
								}	
							}
						}
					}
				}
			}
		}
	}
	
 
 
  	
	
  	/* 	Load General Setting [Private Function]
	 *	@
	 *  Author  - Varun
	 */
	private function loadSetting(){

  		/* Set Configs  */
 		$configuration = $this->db->query('SELECT * FROM config')->fetchAll();
		
 		foreach($configuration as $key=>$config){
			$config_data[$config['config_key']]= $config['config_value'] ;
			$config_groups[$config['config_group']][$config['config_key']]=$config['config_value'];	
		}
		
 		$this->site_configs = $config_data;
 		Zend_Registry::set("site_config",$config_data) ;
		
		
		$viewRenderer = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer');
		if (null === $viewRenderer->view) {
			$viewRenderer->initView();
		}
		$view = $viewRenderer->view;	
		
		 
		
		$view->current_controller = Zend_Controller_Front::getInstance()->getRequest()->getControllerName();
		$view->current_action = Zend_Controller_Front::getInstance()->getRequest()->getActionName();
		
		
 		$view->site_configs=$config_data;  
		$errormessage = Zend_Registry::get("flash_error") ;
		
 		
	}
 
 
 
	/* 	postDispatch Plugin  
	 *	@
	 *  Author - Varun 
	 *	Description - Manage Site Meta and site title for the site 
	 */
   	public function postDispatch(Zend_Controller_Request_Abstract $request){	
	
 	
		$view = Zend_Controller_Action_HelperBroker::getStaticHelper('viewRenderer')->view;
		$view->headMeta()->appendName('viewport',"width=device-width, initial-scale=1, maximum-scale=1, user-scalable=0");
	
		$view->headMeta()->appendName('keywords',$this->site_configs['meta_keyword']);
		$view->headMeta()->appendName('description',$this->site_configs['meta_description']);
		
  		$view->headTitle()->setSeparator(' | ');
		$view->headTitle($this->site_configs['site_title']);
 	
		if(isset($view->pageHeading) and !empty($view->pageHeading))
			$view->headTitle($view->pageHeading);
 		 
  	} 
	
 	
	
    
}
?>