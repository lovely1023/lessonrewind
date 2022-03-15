<?php
class Admin_Bootstrap extends Zend_Application_Module_Bootstrap	
{
   
 	function _initApplication(){ 
	 
	}



	protected function _initNavigation() {
		// make sure the layout is loaded
		$this->bootstrap('layout');
		
		// get the view of the layout
		$layout = $this->getResource('layout');		
		$view = $layout->getView();
		
		//load the navigation xml
		$config = new Zend_Config_Xml(ROOT_PATH.'/private/navigation.xml','nav');
		
 	 
		// pass the navigation xml to the zend_navigation component
		$nav = new Zend_Navigation($config);
		
		
		
		// pass the zend_navigation component to the view of the layout 
		$view->navigation($nav);
		

	}
	
 
    /**
     * return the default bootstrap of the app
     * @return Zend_Application_Bootstrap_Bootstrap
     */
    protected function _getBootstrap()
    {
        $frontController = Zend_Controller_Front::getInstance();
        $bootstrap =  $frontController->getParam('bootstrap');	//deb($bootstrap);
        return $bootstrap;
    }
	
	public function _initSession(){
		
		Zend_Session::start();
		global $mySession;
		$mySession = new Zend_Session_Namespace('admin');
		
	}
 
 
	public function _initRouter()
	{
		$this->FrontController = Zend_Controller_Front::getInstance();
		
		$this->router = $this->FrontController->getRouter();  
		$this->appRoutes=array();
		   
 	}

	
	
	protected  function _initSiteRouters(){	
		
		$this->appRoutes['admin_dashboard']= new Zend_Controller_Router_Route('/admin',array('module'=>'admin','controller'=>'index','action'=>'index'));
		$this->appRoutes['admin_profile']= new Zend_Controller_Router_Route('/admin/profile',array('module'=>'admin','controller'=>'profile','action'=>'index'));
		$this->appRoutes['admin_logout']= new Zend_Controller_Router_Route('/admin/logout',array('module'=>'admin','controller'=>'index','action'=>'logout'));

 		$this->appRoutes['admin_site_configs']= new Zend_Controller_Router_Route('/admin/site-configurations',array('module'=>'admin','controller'=>'static','action'=>'siteconfigs'));	
		$this->appRoutes['admin_email_templates']= new Zend_Controller_Router_Route('/admin/email-templates',array('module'=>'admin','controller'=>'static','action'=>'showmailtemplates'));	
		
		$this->appRoutes['admin_static_pages']= new Zend_Controller_Router_Route('/admin/static-pages',array('module'=>'admin','controller'=>'static','action'=>'index'));	
		$this->appRoutes['admin_content_block']= new Zend_Controller_Router_Route('/admin/content-blocks',array('module'=>'admin','controller'=>'static','action'=>'contentblock'));	
		
		
		/* Pages */
		$this->appRoutes['admin_delete_page']= new Zend_Controller_Router_Route('/admin/delete-pages',array('module'=>'admin','controller'=>'static','action'=>'removepages'));
		$this->appRoutes['admin_delete_contentblocks']= new Zend_Controller_Router_Route('/admin/delete-content-block',array('module'=>'admin','controller'=>'static','action'=>'removeblock')); 		

		
		/* Graphic Media */
		$this->appRoutes['admin_graphic_media']= new Zend_Controller_Router_Route('/admin/graphic-media',array('module'=>'admin','controller'=>'static','action'=>'graphicmedia'));
 		$this->appRoutes['admin_add_graphic_media']= new Zend_Controller_Router_Route('/admin/add-graphic-media',array('module'=>'admin','controller'=>'static','action'=>'addgraphicmedia'));
		$this->appRoutes['admin_edit_graphic_media']= new Zend_Controller_Router_Route('/admin/edit-graphic-media/:media_id',array('module'=>'admin','controller'=>'static','action'=>'editgraphicmedia','media_id'=>'\d+'));
		$this->appRoutes['admin_delete_graphic_media']= new Zend_Controller_Router_Route('/admin/delete-graphic-media/:media_id',array('module'=>'admin','controller'=>'static','action'=>'deletegraphicmedia','media_id'=>'\d+'));
 
       /*Product*/
	  	$this->appRoutes['admin_product_category']= new Zend_Controller_Router_Route('/admin/product-category',array('module'=>'admin','controller'=>'product','action'=>'categories'));
 	  	$this->appRoutes['admin_product_colors']= new Zend_Controller_Router_Route('/admin/product-colors',array('module'=>'admin','controller'=>'product','action'=>'colors'));
 	  	$this->appRoutes['admin_product_design']= new Zend_Controller_Router_Route('/admin/product-design',array('module'=>'admin','controller'=>'product','action'=>'design'));
        $this->appRoutes['admin_product']= new Zend_Controller_Router_Route('/admin/allproducts',array('module'=>'admin','controller'=>'product','action'=>'index'));
	   /*Product*/
 	   
	   
	   /* Admin  Profile Controller Routings*/
	   $this->appRoutes['update_profile_admin'] = new Zend_Controller_Router_Route('/admin/profile-update',array('module'=>'admin','controller'=>'profile','action'=>'index'));
	   $this->appRoutes['update_image_admin'] = new Zend_Controller_Router_Route('/admin/profile-image',array('module'=>'admin','controller'=>'profile','action'=>'image'));
	   $this->appRoutes['update_password_admin'] = new Zend_Controller_Router_Route('/admin/change-password',array('module'=>'admin','controller'=>'profile','action'=>'password'));
	   $this->appRoutes['update_notification_admin'] = new Zend_Controller_Router_Route('/admin/notification-settings',array('module'=>'admin','controller'=>'profile','action'=>'notification'));
	   
	   /*  End  */
	   
	   
	   
 		
		
	 
 
 
 
 
 			$this->appRoutes['admin_login']= new Zend_Controller_Router_Route('/admin/login',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'login')
			);	
			
			$this->appRoutes['admin']= new Zend_Controller_Router_Route('/admin',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'index')
			);	
			
			
			
			//forgotpassword
			$this->appRoutes['forgotpassword']= new Zend_Controller_Router_Route('/admin/forgot-password',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'forgotpassword')
			);
			
			$this->appRoutes['resetpassword']= new Zend_Controller_Router_Route('/admin/resetpassword',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'resetpassword')
			);
			
			$this->appRoutes['logout']= new Zend_Controller_Router_Route('/admin/logout',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'logout')
			);
			$this->appRoutes['changepassword']= new Zend_Controller_Router_Route('/admin/changepassword',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'changepassword')
			);
			
		 
			$this->appRoutes['editemailtemps']= new Zend_Controller_Router_Route('/admin/static-content/edit/content_id/:content_id',
                                     array('module'     => 'admin', 
									 		'controller' => 'pages',
                                            'action' => 'edit-template',"content_id")
			);
			
		
 			  
			
			/* End New Routers */
			
			
			
 			$this->appRoutes['login']= new Zend_Controller_Router_Route('/admin/login',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'login')
			);	
			$this->appRoutes['logout']= new Zend_Controller_Router_Route('/admin/logout',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'logout')
			);
			$this->appRoutes['changepassword']= new Zend_Controller_Router_Route('/admin/changepassword',
                                     array('module'     => 'admin', 
									 		'controller' => 'index',
                                            'action' => 'changepassword')
			);
			
		 
		 $this->appRoutes['editemailtemps']= new Zend_Controller_Router_Route('/admin/static-content/edit/content_id/:content_id',
                                     array('module'     => 'admin', 
									 		'controller' => 'pages',
                                            'action' => 'edit-template',"content_id")
			);
			
	}
	 protected  function _initSetupRouting(){	
			
			foreach($this->appRoutes as $key=>$cRouter){
			
				$this->router->addRoute( $key,  $cRouter );
			}
			
	}
	
	
    /**
     * return the bootstrap object for the active module
     * @return Offshoot_Application_Module_Bootstrap
     */
	 
    public function _getActiveBootstrap($activeModuleName)
    {
        $moduleList = $this->_getBootstrap()->getResource('modules');
        if (isset($moduleList[$activeModuleName])) {
        }
 
        return null;
    }



}








