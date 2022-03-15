<?php
class Admin_UserController extends Zend_Controller_Action
{
    public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelSuper = new Application_Model_SuperModel();
		$this->view->pageIcon = "fa  fa-users";
    }
 	public function indexAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Users";
		$this->view->pageDescription = "manage all site users ";
		$this->view->request_type = "all";
		$this->view->user_type = "all";
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Users' =>'/user');
	
		
		 
 	}
	 /* Email Varification and Account Activation 
	 *	@ss
	 * 
	 */
	 public function activateAction(){
		
 		global $mySession;
		
		$this->view->pageHeading = "Active Account";
		
 		$key = $this->_getParam('key');
		 
		$user_info = $this->modelUser->get(array("key"=>"$key"));
		 
		 if(!$user_info){
			 $mySession->errorMsg = "Invalid Request for Account Activation ";
			 $this->_helper->getHelper("Redirector")->gotoRoute(array(),"login");
		 }
		 
 		 $this->modelUser->add(array('pass_resetkey'=>'',"user_reset_status"=>"0",'user_email_verified'=>'1','user_status'=>"1"),$user_info['user_id']);
		 
		 $mySession->successMsg = "Your Account is Successfully Activated , Please Login";
		$this->_helper->getHelper("Redirector")->gotoRoute(array(),"login");
	 
	}
		public function checkemailAction(){

 		$email_address = strtolower($this->_getParam('user_email'));
		$user_id = strtolower($this->_getParam('user_id'));
		$where='user_email="'.$email_address.'"';
		if(isset($user_id)){
			 
			$where.='and user_id!="'.$user_id.'"';
		}

		$user_data = $this->modelSuper->Super_Get("users",$where,"fetch");
	
		$rev = $this->_getParam("rev");
		
		
		if(!empty($user_data))
			echo json_encode("`$email_address` already exists , please enter any other email address ");
		else
			echo json_encode("true");
		exit();
	}
	public function addschooluserAction()
	{
		global $mySession; 
		$user_id=$this->getRequest()->getParam('user_id');
		$user_data=array();
		$modelEmail = new Application_Model_Email();
 		$this->view->pageHeading = "Add / Edit School";
		
		
		$this->view->request_type = "all";
		$this->view->user_type = "all";
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Users' =>'/schooluser');	
		$form = new  Application_Form_User();
		if(isset($user_id) && !empty($user_id))
		{
			$form->register1($user_id);	 	
			$user_data=$this->modelSuper->Super_Get("users","user_id='".$user_id."'","fetch");
			if($user_data['user_maxstudent_count']==0)
			{
				$user_data['user_maxstudent_count']	='';
			}
			$stuent_count=$this->modelSuper->Super_Get("users","user_type='student' and user_school_id='".$user_id."'","fetchAll",array("fields"=>array('user_id')));
			$user_data['user_active_student']=count($stuent_count);
			$form->populate($user_data);
		}
		else
		{
			$form->register1();	 		
		}
		if(isset($user_id))
		{
			
			$this->view->pageDescription = "manage school info for ".$user_data['user_school_name'];	
		}
		else
		{
			$this->view->pageDescription = "new school info";	
		}
		$this->view->user_data=$user_data;
		if($this->getRequest()->isPost())
		{	/* begin : isPost() */			
			$posted_data = $this->getRequest()->getPost();
 			if($form->isValid($posted_data))
			{ /* Begin : isValid()  */
				 $data = $form->getValues();
				 if(isset($data['user_active_student']))
				 {
				 	unset($data['user_active_student']); 
					}
				 $data['user_type']='school';
				 if(isset($data['user_password']) && !empty($data['user_password']))
				 {
					 $password=$data['user_password'];
				 $data['user_password']=md5($data['user_password']);	
				 $data['user_password_text']=$data['user_password']	 ;
				 }
				 else
				 {
						unset($data['user_password']); 
					}
				
				// ================ add  ==========
				date_default_timezone_set('America/Los_Angeles');	// PDT time
				// 			 
				 $data['user_created']=date('Y-m-d H:i:s');
				// $data['user_type']='school';
				if(isset($user_id))
				{
				 $isInserted = $this->modelSuper->Super_Insert("users",$data,"user_id='".$user_id."'");
				 $user_data=$this->modelSuper->Super_Get("users",'user_id="'.$user_id.'"',"fetch");
				if($user_data['user_email_verified']==0)
					{
						$user_data['user_password']=$password;
						
						$isSend = $modelEmail->sendEmail('registration_email_admin',$user_data);
					
					}
					else
					{
						$user_data['user_password']=$password;
						$isSend = $modelEmail->sendEmail('password_change_email',$user_data);
					}
				}
				else
				{
					//prn($data);
					 $isInserted = $this->modelSuper->Super_Insert("users",$data);
					// prd($isInserted);
					
					
				}
					
				if(is_object($isInserted) && empty($user_data)){
					
				
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
											'Instrument_date'=>date('Y-m-d H:i:s'),
											'Instrument_userid'=>$user_id,
											'Instrument_schoolid'=>$user_id,
											'Instrument_status'=>1,
											'Instrument_active'=>1
						);	
						$kll=$this->modelSuper->Super_Insert("Instruments",$instrument_arr);
						
					}
					/* End adding default instrument prcess */
					$user_data=$this->modelSuper->Super_Get("users","user_id='".$user_id."'","fetch");
					$user_data['user_password']=$password;
					/* Stasrt Email to school user */
					$reset_password_key = md5($user_id."!@#$%^$%&(*_+".time());						
						$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key);						
						$this->modelSuper->Super_Insert("users",$data_to_update1,'user_id="'.$user_id.'"');					
						$user_data['pass_resetkey'] = $reset_password_key ;
						$user_data['user_reset_status'] = "1" ;
					
						$isSend = $modelEmail->sendEmail('registration_email_admin',$user_data);
						/* End Email to school user */
						$this->redirect("admin/user/schooluser");
				
					}else{
						if(isset($user_id))
						{
								$mySession->successMsg = " School User updated succesfully";
									$this->redirect("admin/user/schooluser");
						}
						else
						{
					$mySession->errorMsg = " Please Check Information again ";
						}
				}
 				
			}/* end : isValid()  */
			else{/* begin : else isValid() */
				$mySession->errorMsg = " Please Check Information Again..! ";
 			}/* end : else isValid() */
			
 		}/* end : isPost() */
		$this->view->form = $form;
		//$this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	}
	public function schooluserAction(){
 		global $mySession; 
		$status=0;
		$param=$this->getRequest()->getParam('param');
		if(isset($param) && !empty($param))
		{
			$status=1;	
		}
		$this->view->status=$status;
 		$this->view->pageHeading = "Manage Schools";
		$this->view->pageDescription = "manage schools";
		$this->view->request_type = "all";
		$this->view->user_type = "school";
	//	$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage School Users' =>'/user/schooluser');
		 
 	}
	public function planAction()
	{
		global $mySession; 
 		$this->view->pageHeading = "Manage All School Users";
		$this->view->pageDescription = "manage all school users ";
			
	}
	public function teacheruserAction()
	{
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Teacher Users";
		$this->view->pageDescription = "manage all Teacher users ";
		$this->view->request_type = "all";
		$this->view->user_type = "teacher";
		$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Teacher Users' =>'/user/teacheruser');
 	}
	
	public function studentuserAction()
	{
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Student Users";
		$this->view->pageDescription = "manage all Student users ";
		$this->view->request_type = "all";
		$this->view->user_type = "student";
		$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Student Users' =>'/user/studentuser');
 	}
	
	public function familyuserAction()
	{
		global $mySession; 
 		$this->view->pageHeading = "Manage All Family Users";
		$this->view->pageDescription = "manage all Family users ";
		$this->view->request_type = "all";
		$this->view->user_type = "family";
		$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Student Users' =>'/user/familyuser');	
	}
	
	public function schoolsubadminuserAction()
	{
 		global $mySession; 
 		$this->view->pageHeading = "Manage All School Sub Admin Users";
		$this->view->pageDescription = "manage all School Sub Admin users ";
		$this->view->request_type = "all";
		$this->view->user_type = "schoolsubadmin";
		$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage School Sub Admin Users' =>'/user/schoolsubadminuser');
 	}
	public function verifiedAction()
	{
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Users";
		$this->view->pageDescription = "manage all site users ";
		$this->view->request_type = "verified";
		$this->view->user_type = "all";
 		$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Users' =>'/user','Manage Verified Users' =>'/user/verified');
 	}
 	
	
	
	public function blockedAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Users";
		$this->view->pageDescription = "manage all site users ";
		$this->view->request_type = "blocked";
		$this->view->user_type = "all";
		$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Users' =>'/user','Manage Blocked Users' =>'/user/blocked');
		 
 	}
	
	public function subscribedAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Users";
		$this->view->pageDescription = "manage all site users ";
		$this->view->request_type = "subscribed";
		$this->view->user_type = "all";
		$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Users' =>'/user','Manage Subscribed Users' =>'/user/subscribed');
		 
 	}
	/*
	 * User Account Information 
	 */
	public function accountAction(){
		
		global $mySession; 
		$user_id =  $this->_getParam("user_id");
		$user_information = $this->modelUser->find($user_id);
		
		$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Users' =>'/user','View Account' =>'/user/account/user_id/'.$user_id);
		if(!$user_information->count()){
			$mySession->errorMsg = "No Such User Found , Invalid Request .";
			$this->_redirect("admin");
		}
		$user_information = $user_information->current()->toArray();
		
	
		$this->view->pageDescription = ucwords("View All Information about ".$user_information['user_first_name']." ".$user_information['user_last_name']);
		$form = new Application_Form_User();
		if($user_information['user_type']=='school')
		{
			
				$this->view->pageHeading = ucwords($user_information['user_school_name']);
				$get_all_students=$this->modelSuper->Super_Get("users","user_type='student' and user_status='1' and user_school_id='".$user_information['user_id']."'","fetch",array("fields"=>"COUNT(user_id) as countval"));
			
				$form->school_profile($user_id);
				$form->user_current_student->setValue($get_all_students['countval']);
		}
		else
		{
				$this->view->pageHeading = ucwords($user_information['user_first_name']." ".$user_information['user_last_name']."'s Profile ");
				if($user_information['user_type']=='teacher')
				{
					$user_type_val="Teacher";	
				}
				else if($user_information['user_type']=='student')
				{
					$user_type_val="Student";	
				}
				else if($user_information['user_type']=='schoolsubadmin')
				{
					$user_type_val="School Sub Admin";	
				}
				else if($user_information['user_type']=='family')
				{
					$user_type_val="Family ";	
				}
			/*	$user_type_val="School Sub Admin";*/
				$form->subadmin_profile($user_id);
				$form->user_type->setValue($user_type_val);
				
		}
	
	
		
		$form->populate($user_information);
		if($user_information['user_type']!='school')
		{
			if($user_information['user_type']=='schoolsubadmin')
			{
					$user_type_val="School Sub Admin";
			}
			else if($user_information['user_type']=='teacher')
			{
					$user_type_val="Teacher";	
			}
			else if($user_information['user_type']=='student')
			{
				$user_type_val="Student";	
			}
			else if($user_information['user_type']=='family')
			{
				$user_type_val=='Family';	
			}
			$school_name=$this->modelSuper->Super_Get("users","user_id='".$user_information['user_school_id']."'","fetch",array("fields"=>array("user_school_name")));
			//prd($user_information);
			$form->user_type->setValue($user_type_val);
			$form->school_name->setValue($school_name["user_school_name"]);
		}
 		$this->view->user_id=$user_id;
		$this->view->user_type=$user_information['user_type'];
		$this->view->user_information=$user_information;
		if($this->getRequest()->isPost()){
			
			$posted_data = $this->getRequest()->getPost();
 
  			if($form->isValid($posted_data)){
				
				$data = $form->getValues();
				if(isset($data['user_current_student']))
				{
					unset($data['user_current_student']);
				}
				if(isset($data['school_name']))
				{
					unset($data['school_name']);	
				}
				if(isset($data['user_type']))
				{
					unset($data['user_type']);	
				}
				
 				$is_update = $this->modelUser->add($data,$user_id);
			
				if(is_object($is_update) and $is_update->success){
					
					$mySession->successMsg = "User Information Successfully Updated";
			
				if($user_information['user_type']=='schoolsubadmin')
			{
				$this->_redirect("admin/user/schoolsubadminuser");	
			}
			else if($user_information['user_type']=='teacher')
			{
				$this->_redirect("admin/user/teacheruser");	
			}
			else if($user_information['user_type']=='student')
			{
				$this->_redirect("admin/user/studentuser");	
			}
			else if($user_information['user_type']=='family')
			{
				$this->_redirect("admin/user/familyuser");	
			}
			else
			{
					$this->_redirect("admin/user/index");	
			}
				
				}
				
				$mySession->errorMsg = "Please Check Information Again";
			}
			else
			{
			
			}
 		}
		
  		$this->view->form = $form ;	
		$this->view->user_information = $user_information ;
   	 
	}
	
	
	public function viewreportAction(){
		
		global $mySession; 
		$user_id =  $this->_getParam("user_id");
		$user_information = $this->modelUser->find($user_id);
		if(!$user_information->count()){
			$mySession->errorMsg = "No Such User Found , Invalid Request .";
			$this->_redirect("admin");
		}
		$user_information = $user_information->current()->toArray();
		$this->view->pageHeading = ucwords($user_information['user_first_name']." ".$user_information['user_last_name']."'s Profile ");
		$this->view->pageDescription = ucwords("View All Information about ".$user_information['user_first_name']." ".$user_information['user_last_name']);
		$this->view->user_information = $user_information ;
		$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Subscribed Users' =>'/user/subscribed','View Payment Report' =>'/user/viewreport/user_id/'.$user_id);
		$Super_model = new Application_Model_SuperMOdel();
		$subscription_detail=$Super_model->Super_Get("subscription","subscription_user_id='".$user_id."'","fetch");
		$this->view->subscription_detail=$subscription_detail;
   	 
	}
	
	/*
	 * User Account Information 
	 */
	public function imageAction(){
		
		global $mySession; 
		
		$user_id =  $this->_getParam("user_id");
			$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Users' =>'/user','View Account' =>'/user/account/user_id/'.$user_id);
		$user_information = $this->modelUser->find($user_id);
		
		if(!$user_information->count()){
			$mySession->errorMsg = "No Such User Found , Invalid Request .";
			$this->_redirect("admin");
		}
		
		$user_information = $user_information->current()->toArray();
	
		if($user_information['user_type']=="admin"){
 			$mySession->errorMsg = " Invalid Operation .";
			$this->_redirect("admin/user");
 		}
		
		if($user_information['user_type']=='school')
		{
				$this->view->pageHeading = ucwords($user_information['user_school_name']);
		}
		else
		{
		$this->view->pageHeading = ucwords($user_information['user_first_name']." ".$user_information['user_last_name']."'s Profile ");
		}
		$this->view->pageDescription = ucwords("View All Information about ".$user_information['user_first_name']." ".$user_information['user_last_name']);
		
		$form = new Application_Form_User();
		$form->image();
 		
		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
		
			if($form->isValid($data_post)){
				
 				$is_uploaded = $this->_handle_profile_image();
				
				if(is_object($is_uploaded) and $is_uploaded->success){

					if(empty($is_uploaded->media_path)){
						/* Not Image is Uploaded  */
						$objSession->defaultMsg = "No Images Selected ...";
						$this->_redirect("admin/user/image/user_id/".$user_id);
					}
					
 					$is_updated = $this->modelUser->add(array("user_image"=>$is_uploaded->media_path),$user_id);
					
					if(is_object($is_updated) and $is_updated->success){
						
						/* Remove Old User Images*/
						$this->_unlink_user_image($user_information['user_image']); 
						$objSession->successMsg = " Image Successfully Updated";
						$this->_redirect("admin/user/image/user_id/".$user_id);
						
 					}
										
				}
 			}
		}
		
  		$this->view->form = $form ;	
		$this->view->user_information = $user_information ;
   	 
	}
	
	
	
	
	/*
	 * User Account Information 
	 */
	public function passwordAction(){
		
		global $mySession; 
		
		$user_id =  $this->_getParam("user_id");
			$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Users' =>'/user','View Account' =>'/user/account/user_id/'.$user_id);
		$user_information = $this->modelUser->find($user_id);
		
		if(!$user_information->count()){
			$mySession->errorMsg = "No Such User Found , Invalid Request .";
			$this->_redirect("admin");
		}
		
		$user_information = $user_information->current()->toArray();
		if($user_information['user_type']=='school')
		{
				$this->view->pageHeading = ucwords($user_information['user_school_name']);
		}
		else
		{
			$this->view->pageHeading = ucwords($user_information['user_first_name']." ".$user_information['user_last_name']."'s Profile ");
		}
		$this->view->pageDescription = ucwords("View All Information about ".$user_information['user_first_name']." ".$user_information['user_last_name']);
		
		$form = new Application_Form_User();
		$form->resetPassword();
		$form->populate($user_information);
 		
		
		if($this->getRequest()->isPost()){
			
			$posted_data = $this->getRequest()->getPost();
 
  			if($form->isValid($posted_data)){
				
				$data = $form->getValues();
				$password=$data['user_password'];
				$data['user_password'] = md5($data['user_password']);
 				$data['user_password_text'] = $data['user_password'];
 				$is_update = $this->modelUser->add($data,$user_id);
				
				if(is_object($is_update) and $is_update->success){
					
					$mySession->successMsg = "User Information Successfully Updated";
					
					$superModel = new Application_Model_SuperModel();
					$modelEmail = new Application_Model_Email();
					$user_data=$superModel->Super_Get("users","user_id='".$user_id."'","fetch");
				
					if($user_data['user_email_verified']==0)
					{
						$user_data['user_password']=$password;
						$isSend = $modelEmail->sendEmail('registration_email_admin',$user_data);
					
					}
					else
					{
						$user_data['user_password']=$password;
						$isSend = $modelEmail->sendEmail('password_change_email',$user_data);
					}
					
					
					$this->_redirect("admin/user/account/user_id/".$user_id);
				}
				
				$mySession->errorMsg = "Please Check Information Again";
			}
 		}
		
  		$this->view->form = $form ;	
		$this->view->user_information = $user_information ;
		//$this->render("account");
   	 
	}
 
 
 	/* Ajax Call For Get Users */
  	public function getusersAction(){
		
		$this->dbObj = Zend_Registry::get('db');
		$user_type = $this->_getParam('user_type');
		$request_type = $this->_getParam('type');
 		
 		$aColumns = array(
			'user_id',
			'user_type',
			'user_image',
			'user_first_name',
			'user_email',
			'user_email_verified',
			'user_status',
 			'user_salutation',
 			'user_last_name' , 
			'user_school_id',
			'user_school_name'	,
			'user_verification_mail',
			'user_username'
  		);

		$sIndexColumn = 'user_id';
		$sTable = 'users';
 		
		/* 
		 * Paging
		 */
		 
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
		}
		
		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
						($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
	$sOrder = "ORDER BY user_last_name ASC ";
		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset($_GET['sSearch']) and $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) and $_GET['bSearchable_'.$i] == "true" and $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
			}
		}
	
		
		if($user_type!=""){
			
			switch($user_type){
				case 'all':
					if($sWhere){
					$sWhere.=" and user_type!='admin'  ";
					}else{
					$sWhere.=" where user_type!='admin' ";
					}
				 break;	
				case 'school':
					if($sWhere){
					$sWhere.=" and user_type='school'  ";
					}else{
					$sWhere.=" where user_type='school' ";
					}
				 break;	
				case 'teacher': 
					if($sWhere){
					$sWhere.=" and user_type='teacher'  ";
					}else{
					$sWhere.=" where user_type='teacher' ";
					}
				 break;
				 case 'student': 
					if($sWhere){
					$sWhere.=" and user_type='student'  ";
					}else{
					$sWhere.=" where user_type='student' ";
					}
				 break;
				  case 'family': 
					if($sWhere){
					$sWhere.=" and user_type='family'  ";
					}else{
					$sWhere.=" where user_type='family' ";
					}
				 break;
				 case 'schoolsubadmin': 
					if($sWhere){
					$sWhere.=" and user_type='schoolsubadmin'  ";
					}else{
					$sWhere.=" where user_type='schoolsubadmin' ";
					}
				 break;
				default : break ;	
			}
		}
		
		
		if($request_type!=""){
			
			switch($request_type){
				case 'all': $sWhere.=" "; break;	
				case 'verified': $sWhere.=" and   user_email_verified = '1' "; break;	
				case 'blocked': $sWhere.=" and  user_status = '0' "; break;
				default : break ;	
			}
		}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM  $sTable $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		$i=1;
		foreach($qry as $row1){
			
			$school_arr=array();
			$school_name='';
			if($row1['user_type']=='school')
			{
				$school_name=$row1['user_school_name'];		
			}
			else
			{
				$school_arr=$this->modelSuper->Super_Get("users","user_id='".$row1['user_school_id']."'","fetch");
				$school_name=$school_arr['user_school_name'];
			}
			$user_type_text=$row1['user_type'];
			if($row1['user_type']=='school')
			{
				$user_type_text='School';	
			}
			else if($row1['user_type']=='teacher')
			{
				$user_type_text='Teacher';	
			}
			else if($row1['user_type']=='student')
			{
				$user_type_text='Student';
			}
			else if($row1['user_type']=='schoolsubadmin')
			{
				$user_type_text='School Subadmin';	
			}
			else if($row1['user_type']=='family')
			{
				$user_type_text='Family';	
			}
			else if($row1['user_type']=='site_subadmin')
			{
				$user_type_text="Site Subadmin";	
			}
			
 			$row=array();
			
			$row[] = $i;
  			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			$row[]='<img src="'.getUserImage($row1['user_image'],60).'" />';
			
			switch($row1['user_email_verified']){
				case '0':$verification_status ="<span class='badge badge-danger badge-roundless'>Unverified</span>";break;
				default :$verification_status ="<span class='badge badge-success badge-roundless'>Verified</span>";break;
			}
			$row[]=$row1['user_last_name']."<br />$verification_status";
			$row[]=$row1['user_first_name'];
			$row[]=$user_type_text;
			if($row1['user_type']=='family')
			{
				if($row1['user_email']!='')
				{
					$row[]=$row1['user_email'];	
				}
				else
				{
					$row[]=$row1['user_username'];	
				}
					
			}
			else if($row1['user_type']=='student')
			{
				if($row1['user_email']=='')
				{
					$row[]="Linked To Family Email";	
				}
				else
				{
					$row[]=$row1['user_email'];
				}	
			}
			else
			{  
			
					$row[]=$row1['user_email'];
			}
   			
 			$row[]=$school_name;
			$status = $row1['user_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['user_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
			$row[] =  '<a href="'.APPLICATION_URL.'/admin/user/account/user_id/'.$row1[$sIndexColumn].'" class="btn btn-xs purple"> Edit <i class="fa fa-edit"></i></a>';
			
			//$row[] = '<a class="btn mini green-stripe" href="'.APPLICATION_URL.'/admin/user/account/user_id/'.$row1[$sIndexColumn].'">View</a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	public function sendverification1Action()
	{
		global $mySession; 
		$user_id=$this->getRequest()->getParam('user_id');
		$type=$this->getRequest()->getParam('type');
		$superModel = new Application_Model_SuperModel();
		$modelEmail = new Application_Model_Email();
		$user_data=$superModel->Super_Get("users","user_id='".$user_id."'","fetch");
		$isSend = $modelEmail->sendEmail('registration_email',$user_data);
		$mySession->successMsg="Verification mail has been send successfully";
		if($type==1)
		{
			$user_data=array('user_verification_mail'=>1);	
			$superModel->Super_Insert($user_data,'user_id="'.$user_id.'"');
		}
		else
		{
			
		}
		$this->redirect("admin/user/schooluser");
		
	}
	public function sendverificationAction()
	{
		global $mySession; 
		$user_id=$this->getRequest()->getParam('user_id');
		$type=$this->getRequest()->getParam('type');
		$superModel = new Application_Model_SuperModel();
		$modelEmail = new Application_Model_Email();
		$user_data=$superModel->Super_Get("users","user_id='".$user_id."'","fetch");
		$isSend = $modelEmail->sendEmail('registration_email',$user_data);
		$mySession->successMsg="Verification mail has been send successfully";
		if($type==1)
		{
			$user_data=array('user_verification_mail'=>1);	
			$superModel->Super_Insert("users",$user_data,'user_id="'.$user_id.'"');
		}
		else
		{
			
		}
		$this->redirect("admin/user");
		
	}
	public function getschoolusersAction(){
		
		$this->dbObj = Zend_Registry::get('db');
		$user_type = $this->_getParam('user_type');
		$request_type = $this->_getParam('type');
 		$status=$this->getRequest()->getParam('status');
 		$aColumns = array(
			'user_id',
			'user_type',
			'user_image',
			'user_first_name',
			'user_email',
			'user_email_verified',
			'user_status',
 			'user_salutation',
 			'user_last_name' , 
			'user_account_type',
			'user_school_name',
			'user_verification_mail'
  		);

		$sIndexColumn = 'user_id';
		$sTable = 'users';
 		
		/* 
		 * Paging
		 */
		 
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
		}
		
		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
						($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY " )
			{
				$sOrder = "";
			}
		}
		$sOrder = "ORDER BY user_last_name ASC ";
		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset($_GET['sSearch']) and $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) and $_GET['bSearchable_'.$i] == "true" and $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
			}
		}
	
		
		if($sWhere){
					$sWhere.=" and user_type='school'  ";
					}else{
					$sWhere.=" where user_type='school' ";
					}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM  $sTable $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		$i=1;
	
		foreach($qry as $row1){
			
 			$row=array();
			
			$row[] = $i;
  			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			$row[]='<a href="'.SITE_HTTP_URL.'/admin/user/logintofront/user_id/'.($row1['user_id']).'" target="_blank" style="color:#000000"><b>'.ucfirst($row1['user_school_name']). '</b></a>';
			$row[]="School";
			$row[]='<img src="'.getUserImage($row1['user_image'],60).'" />';
			
			switch($row1['user_email_verified']){
				case '0':$verification_status ="<span class='badge badge-danger badge-roundless'>Unverified</span>";break;
				default :$verification_status ="<span class='badge badge-success badge-roundless'>Verified</span>";break;
			}
			
		
			
			$row[]=$row1['user_last_name']."<br />$verification_status";
			$row[]=$row1['user_first_name'];
   			$row[]=$row1['user_email'];
			
 			 
			$status = $row1['user_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['user_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
			
			
			
			
			
			$row[] =  '<a href="'.APPLICATION_URL.'/admin/user/addschooluser/user_id/'.$row1[$sIndexColumn].'" class="btn btn-xs purple"> Edit <i class="fa fa-edit"></i></a>';
		
			//$row[] = '<a class="btn mini green-stripe" href="'.APPLICATION_URL.'/admin/user/account/user_id/'.$row1[$sIndexColumn].'">View</a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	public function  logintofrontAction()
	{
		global $mySession; 
		$user_id=$this->getRequest()->getParam('user_id');
		$user_information = $this->modelUser->find($user_id);
		$user_information = $user_information->current()->toArray();
		$user_information['admin_id']=$this->view->user->user_id;
		echo '<form method="post" name="login_admin" id="login_admin" action="'.SITE_HTTP_URL.'/user/setadmin">
			<input type="hidden" name="admin_id" value="'.$user_information['admin_id'].'"/>
			<input type="hidden" name="user_id" value="'.$user_id.'" />
		</form>';
		
		echo '<script>document.login_admin.submit();</script>';
		exit();
		
	}
	
	/* Ajax Call For Get Subscribed Users */
  	public function getsubscribedusersAction(){
		
		$this->dbObj = Zend_Registry::get('db');
		
		$request_type = $this->_getParam('type');
		
 
 		$aColumns = array(
			'user_id',
			'user_type',
			'user_image',
			'user_first_name',
			'user_email',
			 'user_email_verified',
			'user_status',
 			'user_salutation',
 			'user_last_name' , 
			'subscription_user_id'
  		);

		$sIndexColumn = 'user_id';
		$sTable = 'users';
 		
		/* 
		 * Paging
		 */
		 
		$sLimit = "";
		if ( isset( $_GET['iDisplayStart'] ) && $_GET['iDisplayLength'] != '-1' )
		{
			$sLimit = "LIMIT ".intval( $_GET['iDisplayStart'] ).", ".intval( $_GET['iDisplayLength'] );
		}
		
		/*
		 * Ordering
		 */
		$sOrder = "";
		if ( isset( $_GET['iSortCol_0'] ) )
		{
			$sOrder = "ORDER BY  ";
			for ( $i=0 ; $i<intval( $_GET['iSortingCols'] ) ; $i++ )
			{
				if ( $_GET[ 'bSortable_'.intval($_GET['iSortCol_'.$i]) ] == "true" )
				{
					$sOrder .= "".$aColumns[ intval( $_GET['iSortCol_'.$i] ) ]." ".
						($_GET['sSortDir_'.$i]==='asc' ? 'asc' : 'desc') .", ";
				}
			}
			
			$sOrder = substr_replace( $sOrder, "", -2 );
			if ( $sOrder == "ORDER BY" )
			{
				$sOrder = "";
			}
		}
		
		/* 
		 * Filtering
		 * NOTE this does not match the built-in DataTables filtering which does it
		 * word by word on any field. It's possible to do here, but concerned about efficiency
		 * on very large tables, and MySQL's regex functionality is very limited
		 */
		$sWhere = "";
		if ( isset($_GET['sSearch']) and $_GET['sSearch'] != "" )
		{
			$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			$sWhere .= ')';
		}
		
		/* Individual column filtering */
		for ( $i=0 ; $i<count($aColumns) ; $i++ )
		{
			if ( isset($_GET['bSearchable_'.$i]) and $_GET['bSearchable_'.$i] == "true" and $_GET['sSearch_'.$i] != '' )
			{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ";
				}
				else
				{
					$sWhere .= " AND ";
				}
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string($_GET['sSearch_'.$i])."%' ";
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET['sSearch_'.$i]."%' ";
			}
		}
		
		if($sWhere){
			$sWhere.=" and user_type='user'  ";
		}else{
			$sWhere.=" where user_type='user' ";
		}
		
		
		
		if($request_type!=""){
			
			switch($request_type){
				case 'all': $sWhere.=" "; break;	
				case 'verified': $sWhere.=" and   user_email_verified = '1' "; break;	
				case 'blocked': $sWhere.=" and  user_status = '0' "; break;
				default : break ;	
			}
		}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM  $sTable join subscription on subscription_user_id=user_id $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		$i=1;
		
		foreach($qry as $row1){
			
 			$row=array();
			
			$row[] = $i;
  			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			$row[]='<img src="'.getUserImage($row1['user_image'],60).'" />';
			
			switch($row1['user_email_verified']){
				case '0':$verification_status ="<span class='badge badge-danger badge-roundless'>Unverified</span>";break;
				default :$verification_status ="<span class='badge badge-success badge-roundless'>Verified</span>";break;
			}
			
			
			$row[]=$row1['user_first_name']." ".$row1['user_last_name']."<br />$verification_status";
   			$row[]=$row1['user_email'];
			
			
			$status = $row1['user_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['user_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
			
			
			
			
			
			$row[] =  '<a href="'.APPLICATION_URL.'/admin/user/account/user_id/'.$row1[$sIndexColumn].'" class="btn btn-xs purple"> View <i class="fa fa-search"></i></a> <br><br>
			<a href="'.APPLICATION_URL.'/admin/user/viewreport/user_id/'.$row1[$sIndexColumn].'" class="btn btn-xs purple"> View Payment Report <i class="fa fa-search"></i></a>
			';
			//$row[] = '<a class="btn mini green-stripe" href="'.APPLICATION_URL.'/admin/user/account/user_id/'.$row1[$sIndexColumn].'">View</a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	/* 
	 *	Remove Graphic Media 
	 */
 	public function removeAction(){
		
		global $mySession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
 
 
 		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData['users']) and count($formData['users'])){
				
				 foreach($formData['users'] as $key=>$values){
 
   					 $user_info = $this->modelUser->get($values);
					 
					 if(empty($user_info))
						continue ;
						
 					$this->_unlink_user_image($user_info['user_image']);
				
					if($user_info['user_type']=='family')
					{
						$student_family=array();
						$student_family=$this->modelStatic->Super_Get("student_family","s_f_fid='".$values."'","fetchAll");	
						foreach($student_family as $k=>$v)
						{
							$userArr=array();
							$userArr=$this->modelStatic->Super_Get("users","user_id='".$v['s_f_sid']."'","fetch",array("fields"=>array("user_id","user_image")));
							if(!empty($userArr))
							{
								$this->_unlink_user_image($userArr['user_image']);	
							}	
							$this->modelStatic->Super_Delete("users","user_id='".$v['s_f_sid']."'");
						}
						
						$this->modelStatic->Super_Delete("student_family","s_f_fid='".$values."'");
						
					}
					
						
					$removed = $this->modelUser->delete("user_id IN ($values)");
					
					$removed = $this->modelUser->delete("user_insertby IN ($values)");
					
				 }
 				 
 				$mySession->successMsg = " Users Deleted Successfully ";
				
 			}else{
				$mySession->errorMsg = " Invalid Request to Delete User(s) ";
			}
			
 			$this->_redirect('/admin/user/?removed=1');	 
   	 
		} 
		
 			
 	}
	
	
	/* 
	 *	Delete graphic Media Images 
	 */
	private function _unlink_user_image($image){
		
		if(empty($image))
			return true; 
		
	 
  		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/".$image)){
			unlink(PROFILE_IMAGES_PATH."/".$image);
 		}
		
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/thumb/".$image)){
			unlink(PROFILE_IMAGES_PATH."/thumb/".$image);
 		}
		
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/60/".$image)){
			unlink(PROFILE_IMAGES_PATH."/60/".$image);
 		}
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/160/".$image)){
			unlink(PROFILE_IMAGES_PATH."/160/".$image);
 		}
		
		
		
	}
	

	
	/* Handle The Uploaded Images For Graphic Media  */
	private function _handle_profile_image(){
		
 		global $objSession; 
		
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
	
	 
	 
	
	 
}