<?php
class Bootstrap extends Zend_Application_Bootstrap_Bootstrap
{	

 	protected function _initLoaderResources()
    {
		
        $this->getResourceLoader()->addResourceType('controller', 'controllers/', 'Controller');
    }
	
	protected function _initAutoloader()
 	{
 	   new Zend_Application_Module_Autoloader(array(
 	      'namespace' => 'Application',
 	      'basePath'  => APPLICATION_PATH,
 	   ));
 	}
	
	    protected function _initHelperPath() {
        $view = $this->bootstrap('view')->getResource('view');
        $view->setHelperPath(APPLICATION_PATH . '/views/helpers', 'Application_View_Helper');
    }
	
	protected function _initDoctype()
	{
		$this->bootstrap('view');
 		$view = $this->getResource('view');
  		$view->setEncoding('UTF-8');
		$view->doctype('HTML5');
 		$view->headMeta()->appendHttpEquiv('Content-Type',  'text/html;charset=utf-8');
	}
	

	
	protected function _initDB() {
	
		$dbConfig = new Zend_Config_Ini(ROOT_PATH.'/private/db.ini',APPLICATION_ENV);
		$dbConfig =$dbConfig->resources->db;
	 	
       	$dbAdapter = Zend_Db::factory($dbConfig->adapter, array(
            'host'     => $dbConfig->params->hostname,
            'username' => $dbConfig->params->username,
            'password' => $dbConfig->params->password,
            'dbname'   => $dbConfig->params->dbname
         ));
 		
//		$dbAdapter->exec("SET time_zone='".$dbConfig->params->timezone."'");
		
        Zend_Db_Table_Abstract::setDefaultAdapter($dbAdapter);

        Zend_Registry::set('db', $dbAdapter);
		 
 		
		Zend_Session::start();
		global $objSession;
		$objSession = new Zend_Session_Namespace('default');
		
    }
 	
	protected function _initAppKeysToRegistry(){
		$appkeys = new Zend_Config_Ini(ROOT_PATH . '/private/appkeys.ini');
		Zend_Registry::set('keys', $appkeys);
	}
	

	public function _initPlugins(){ // Add Plugin path
		$front = Zend_Controller_Front::getInstance();
		$front->registerPlugin(new Application_Plugin_SetLayout());
	}


		protected  function _initApplication(){   
	 
 			$this->FrontController=Zend_Controller_Front::getInstance();
			$this->FrontController->setControllerDirectory(array(
				'default' => '../application/controllers',
				'admin'    => '../application/admin/controllers'
			));
			
			// $this->FrontController->setDefaultControllerName('login'); 
			//	$this->FrontController->throwExceptions(false);
		
			$registry = Zend_Registry::getInstance();
			$registry->set("flash_error",false);
			
		 	// Add a 'foo' module directory:
			// $this->FrontController->setParam('prefixDefaultModule', true);
			// $this->FrontController->setDefaultModule('publisher');
			// $this->FrontController->setDefaultAction("index") ;
			// $this->FrontController->addControllerDirectory('../modules/foo/controllers', 'foo');
			
	 	
	}
	
	
	public function _initRouter()
        {
            $this->FrontController = Zend_Controller_Front::getInstance();
            $this->router = $this->FrontController->getRouter();
            $this->appRoutes = array ();
        }
	
	
	/* Site Routers */
	protected function _initSiteRouters(){
		
		
		/* Fixed Front Redirects */
		$this->appRoutes['front_login'] = new Zend_Controller_Router_Route('login', array ('module' => 'default','controller' => 'user','action' => 'login'));
		$this->appRoutes['front_signin'] = new Zend_Controller_Router_Route('signin', array ('module' => 'default','controller' => 'user','action' => 'signin'));
		$this->appRoutes['front_changepassword'] = new Zend_Controller_Router_Route('change-password', array ('module' => 'default','controller' => 'user','action' => 'changepassword'));
		$this->appRoutes['front_logout'] = new Zend_Controller_Router_Route('logout', array ('module' => 'default','controller' => 'user','action' => 'logout'));
		$this->appRoutes['front_register'] = new Zend_Controller_Router_Route('register', array ('module' => 'default','controller' => 'user','action' => 'register'));
		$this->appRoutes['front_forgotpassword'] = new Zend_Controller_Router_Route('forgot-password', array ('module' => 'default','controller' => 'user','action' => 'forgotpassword'));
		$this->appRoutes['facebook_signup'] = new Zend_Controller_Router_Route('social/fblogin', array ('module' => 'default','controller' => 'social','action' => 'fblogin'));
		$this->appRoutes['twitter_signup'] = new Zend_Controller_Router_Route('social/twitterlogin', array ('module' => 'default','controller' => 'social','action' => 'twitterlogin'));
		$this->appRoutes['about_us'] = new Zend_Controller_Router_Route('about-us', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'1'));
		$this->appRoutes['privacy_policy'] = new Zend_Controller_Router_Route('privacy-policy', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'2'));
		$this->appRoutes['quality'] = new Zend_Controller_Router_Route('quality', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'3'));
		$this->appRoutes['our_promise'] = new Zend_Controller_Router_Route('our-promise', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'4'));
		$this->appRoutes['services'] = new Zend_Controller_Router_Route('services', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'5'));
		$this->appRoutes['shipping'] = new Zend_Controller_Router_Route('shipping', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'6'));
		$this->appRoutes['returns'] = new Zend_Controller_Router_Route('returns', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'7'));
		$this->appRoutes['terms'] = new Zend_Controller_Router_Route('terms', array ('module'=>'default','controller'=>'static','action'=>'index','page_id'=>'8'));
		$this->appRoutes['contact_us'] = new Zend_Controller_Router_Route('contact-us', array ('module'=>'default','controller'=>'static','action'=>'contact'));
		$this->appRoutes['front_profile'] = new Zend_Controller_Router_Route('profile', array ('module'=>'default','controller'=>'profile','action'=>'index'));
		$this->appRoutes['front_subscribe'] = new Zend_Controller_Router_Route('subscription', array ('module'=>'default','controller'=>'profile','action'=>'subscription'));
		$this->appRoutes['front_plansubscribe'] = new Zend_Controller_Router_Route('plansubscription', array ('module'=>'default','controller'=>'profile','action'=>'plansubscription'));
		$this->appRoutes['features'] = new Zend_Controller_Router_Route('features', array ('module'=>'default','controller'=>'index','action'=>'index','param'=>'feature'));

		$this->appRoutes['front_dashboard'] = new Zend_Controller_Router_Route('dashboard', array ('module'=>'default','controller'=>'profile','action'=>'dashboard'));
		$this->appRoutes['front_image'] = new Zend_Controller_Router_Route('change-avatar', array ('module'=>'default','controller'=>'profile','action'=>'image'));
		$this->appRoutes['front_image_crop'] = new Zend_Controller_Router_Route('crop-image', array ('module'=>'default','controller'=>'profile','action'=>'cropimage'));
		$this->appRoutes['change_password'] = new Zend_Controller_Router_Route('change-password', array ('module'=>'default','controller'=>'profile','action'=>'password'));
		$this->appRoutes['user_cart'] = new Zend_Controller_Router_Route('my-cart', array ('module'=>'default','controller'=>'cart','action'=>'index'));
		$this->appRoutes['search'] = new Zend_Controller_Router_Route('search', array ('module'=>'default','controller'=>'search','action'=>'index'));

		
		/* Routings For Product Categories  */
		
		$db = Zend_Registry::get('db');
		
		 
		
		
 
		
    
	}
	
	

	protected function _initSetupRouting(){
		foreach ($this->appRoutes as $key => $cRouter)
		{
			$this->router->addRoute($key, $cRouter);
		}
		
/*			prd($this);*/
	}
	
	protected function _initTranslator()
	{
		
		$enLangData = require_once(ROOT_PATH.'/private/languages/en.php');
		$deLangData = require_once(ROOT_PATH.'/private/languages/fr.php');
 		$translate = new Zend_Translate(
			array(
				'adapter' => 'array',
				'content' => $enLangData,
				'locale'  => 'en',
			)
		);
		$translate->addTranslation(
			array(
				'content' => $deLangData,
				'locale'  => 'fr',
				'clear'   => true
			)
		);
		if(SITE_STAGE == "development"){
			$translate->setLocale('en');
		}else{
			$translate->setLocale('fr');
		}
		
		Zend_Registry::set('Zend_Translate', $translate);
		 
	}
	
	
  
}





/* ------------------------------------------- Functions ---------------------------------  */
function prepareQuery($args){
	$sql=$args[0];
	 $_sqlSplit = preg_split('/(\?|\:[a-zA-Z0-9_]+)/', $sql, -1, PREG_SPLIT_DELIM_CAPTURE|PREG_SPLIT_NO_EMPTY);
$params=0;	
	 foreach ($_sqlSplit as $key => $val) {
            if ($val == '?') {
				$_sqlSplit[$key]=$args[1][$params];
				$params++;
			}
	 }
	 
$query=implode($_sqlSplit);	 
	return($query);
}
