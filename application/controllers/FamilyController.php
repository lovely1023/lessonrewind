<?php
class FamilyController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		
   	}
	
	
 	public function indexAction(){	
 		global $objSession ; 
		$this->view->pageHeading = "All Families";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  All Families';
		/* Check if user type is not school */
		$type=$this->getRequest()->getParam('type');
		if(!isset($type))
		{
			$type=1;	
		}
		$this->view->type=$type;
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='2'","fetch");	
				
				if(empty($paremission_data))
				{
					/* If Permission Data is empty */
					$this->redirect("profile/dashboard");	
				}
			}	
			else
			{
				/* If user is not admin or subadmin */
				$this->redirect('index');	
			}
		}
	
	}
	
	public function sendmailAction()
	{
		global $objSession;
 		$this->_helper->layout->disableLayout();
		$modelEmail = new Application_Model_Email();
		$this->_helper->viewRenderer->setNoRender(true);
 		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			if(isset($formData['select_users']) and count($formData['select_users'])){
				 foreach($formData['select_users'] as $key=>$values){
					 $check_data=array();
					 $check_data=$this->modelStatic->Super_Get("users","user_id='".$values."'","fetch");
					 if($check_data['user_email']!='')
					 {
				 	 	
					 	if($check_data['user_verification_mail']==0)
					 	{
							/* Send verfication */
							$data_user=array();
							$data_user=$check_data;
							$data_user['user_password']=strtolower(str_replace(' ','',trim($check_data['user_first_name'].$check_data['user_last_name'])));
							$reset_password_key = md5($values."!@#$%^$%&(*_+".time());						
							$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key,'user_password'=>md5($data_user['user_password']),'user_password_text'=>$data_user['user_password']);						
							$superModel->Super_Insert("users",$data_to_update1,'user_id="'.$values.'"');					
							$data_user['pass_resetkey'] = $reset_password_key ;
							$data_user['user_reset_status'] = "1" ;
							$data_user['last_inserted_id'] = $values ;	
							$data_user['school_name'] = $this->view->user->user_school_name ;
							$data_user['user_position']= ucfirst($check_data['user_type']);
							if($data_user['user_email']!='')
							{
							$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user);
							}
							/* End verification */
							 /* Mail is not send yet */
							$update_user=array();
							$update_user=array('user_verification_mail'=>1);	
						$this->modelStatic->Super_Insert("users",$update_user,'user_id="'.$values.'"');
						 }
						 else
						 {
							$isSend = $modelEmail->sendEmail('registration_email_verification',$check_data); 
							}
					 }
				 }
 			$objSession->successMsg = "Verification mail has been send Successfully ";
 			}else{
					$objSession->errorMsg = "Invalid Request to Send Mail User(s) ";
			}
 			$this->_redirect('family');
		} 
	}
	
	
	/* Remove Class */
	public function removestudentAction()
	{
		global $objSession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
 		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			if(isset($formData['select_users']) and count($formData['select_users'])){
				 foreach($formData['select_users'] as $key=>$values)
				 {
   					$user_arr=array();
					$user_arr=$this->modelStatic->Super_Get("users","user_id='".$values."'","fetch",array("fields"=>array("user_id","user_image")));
					if(!empty($user_arr))
					{
						$this->_unlink_user_image($user_arr['user_image']); 
					}
					
					$student_family=array();
					$student_family=$this->modelStatic->Super_Get("student_family","s_f_fid='".$values."'","fetchAll");
					foreach($student_family as $k=>$v)
					{
						$user_new=array();
						$user_new=$this->modelStatic->Super_Get("users","user_id='".$v['s_f_sid']."'","fetch",array("fields"=>array("user_id","user_image")));
						if(!empty($user_new))
						{
								$this->_unlink_user_image($user_new['user_image']); 
						}
						$this->modelStatic->Super_Delete("users","user_id='".$v['s_f_sid']."'");
					}
					$this->modelStatic->Super_Delete("student_family","s_f_fid='".$values."'");
					$this->modelStatic->Super_Delete("users","user_id='".$values."'");
				 }
 				$objSession->successMsg = " Users Deleted Successfully ";
 			}else
			{
				$objSession->errorMsg = "Invalid Request to Delete User(s) ";
			}
 			$this->_redirect('family');
		} 
		
	/*	$user_id=$this->getRequest()->getParam("idval");	
		$this->modelStatic->Super_Delete("users","user_id='".$user_id."'");
		$objSession->successMsg = "Student has been removed Successfully";				
		$this->_redirect('student');*/
		
	}
	
	public function sendverificationAction()
	{
		global $objSession; 
		$user_id=$this->getRequest()->getParam('user_id');
		$type=$this->getRequest()->getParam('type');
		$superModel = new Application_Model_SuperModel();
		$modelEmail = new Application_Model_Email();
		$user_data=$superModel->Super_Get("users","user_id='".$user_id."'","fetch");
		
		
		$objSession->successMsg="Verification mail has been send successfully";
		/* Send verfication */
			$data_user=array();
			$data_user=$user_data;
			$data_user['user_password']=strtolower(str_replace(' ','',trim($user_data['user_first_name'].$user_data['user_last_name'])));
			$reset_password_key = md5($user_id."!@#$%^$%&(*_+".time());						
			$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key,'user_password'=>md5($data_user['user_password']),'user_password_text'=>$data_user['user_password']);						
			$superModel->Super_Insert("users",$data_to_update1,'user_id="'.$user_id.'"');					
			$data_user['pass_resetkey'] = $reset_password_key ;
			$data_user['user_reset_status'] = "1" ;
			$data_user['last_inserted_id'] = $user_id ;	
			$data_user['school_name'] = $this->view->user->user_school_name ;
			$data_user['user_position']= ucfirst($user_data['user_type']);
			if($data_user['user_email']!='')
			{
			$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user);
			}
			/* End verification */
		if($type==1)
		{
			
			$user_data=array('user_verification_mail'=>1);	
			$superModel->Super_Insert("users",$user_data,'user_id="'.$user_id.'"');
		}
		else
		{
			//$isSend = $modelEmail->sendEmail('registration_email_verification',$user_data);
		}
		$this->redirect("family");
		
	}
	
	public function sendpasswordresetmailAction()
	{
		global $objSession; 
		$user_id=$this->getRequest()->getParam("user_id");
		$superModel = new Application_Model_SuperModel();
		$modelEmail = new Application_Model_Email();
		$user_data=$superModel->Super_Get("users","user_id='".$user_id."'","fetch");
		if($user_data['user_email']!='')
		{
			$password=generateRandomString(8);	
		}
		else
		{
			$password=12345;	
		}
		
		
		if($user_data['user_email_verified']==0)
		{
			$user_data['user_password']=$password;
			$isSend = $modelEmail->sendEmail('verification_email_admin',$user_data);
		
		}
		else
		{
			$user_data['user_password']=$password;
			$isSend = $modelEmail->sendEmail('password_change_email',$user_data);
		}
		
		$pasw_arr=array('user_password'=>md5($password),'user_password_text'=>$password);
		$superModel->Super_Insert("users",$pasw_arr,"user_id='".$user_id."'");
		$objSession->successMsg="Password has been reset successfully";
		$this->redirect("family");
	}
	
	/* Get All Classes */
	public function getfamilyAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$type=$this->getRequest()->getParam('type');
		if(!isset($type))
		{
			$type=0;	
		}
 		$aColumns = array(
			'user_id',
			'user_first_name',
			'user_last_name',
			'user_email',
			'user_created',
			'user_email_verified',
			'user_verification_mail',
			'user_username',
			'user_status',
			'user_password_text'
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
		$sOrder="ORDER BY user_last_name ASC";
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(user_first_name,' ',user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
			//	$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
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
		
		if ( $sWhere == "" )
			{
					$sWhere = "WHERE (user_type='family' and (user_insertby='".$this->view->user->user_id."' or user_school_id='".$this->view->user->user_id."' or user_school_id='".$this->view->user->user_school_id."' ))";
			}
			else
				{
					$sWhere .= " AND (user_type='family'  and (user_insertby='".$this->view->user->user_id."' or user_school_id='".$this->view->user->user_id."' or user_school_id='".$this->view->user->user_school_id."' ) )";
				}
				if($type==1)
				{
					$sWhere.=" and user_status='1'";
				}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." FROM $sTable $sWhere group by user_id $sOrder $sLimit";
		
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable $sWhere";
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
			
			
			$row[]=$row1['user_last_name'];
			$row[]=$row1['user_first_name'];
			$status = $row1['user_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['user_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1['user_id'].'" onChange="globalStatus(this)" />
					</div>';
			$username='Null';
			if($row1['user_username']!='')
			{
				$username=$row1['user_username'];
			}
			$row[]=$username;		
			$useremail='Null';
			if($row1['user_email']!='')
			{
				$useremail=$row1['user_email'];	
			}
			$row[]=$useremail;	
   			$row[]=date('F d Y g:i A', strtotime($row1['user_created']));
				$row[] =  '<a href="'.APPLICATION_URL.'/family/newfamily/user_id/'.$row1[$sIndexColumn].'/status/1" class="btn btn-xs purple"> 
			 <i class="fa fa-edit"></i>
			 </a>';
			 $mail_sent='';
			if($row1['user_verification_mail']==1)
			{
				$mail_sent="<b>Mail Sent</b>";	
			}
			$row[]='<input type="checkbox"  value="'.$row1['user_id'].'"   name="select_users[]" id="select_user_'.$row1['user_id'].'"  />&nbsp;'.$mail_sent;
			if($row1['user_email']!='')
			{
				if($row1['user_verification_mail']==0)
				{
					$row[]='<a href="'.SITE_HTTP_URL.'/family/sendverification/type/1/user_id/'.$row1["user_id"].'"><span class="badge badge-success badge-roundless">Send Verification Mail</span></a>';	
				}
				else if($row1['user_email_verified']==0)
				{		
			
						$row[]='<a href="'.SITE_HTTP_URL.'/family/sendverification/type/2/user_id/'.$row1['user_id'].'"><span class="badge badge-success badge-roundless">Resend Verification Mail</span></a>';	
				}
				
				else
				{
					
			
					$row[]='<a href="'.SITE_HTTP_URL.'/family/sendpasswordresetmail/user_id/'.$row1["user_id"].'"><span class="badge badge-success badge-roundless">Send Reset Passwordtest  Mail </span></a>';	
				}
			}
			else
			{
			$row[]=' N/A';	
			}	
			$verification_status='';
				switch($row1['user_email_verified']){
				case '0':$verification_status ="<span class='badge badge-danger badge-roundless'>Unverified</span>";break;
				default :$verification_status ="<span class='badge badge-success badge-roundless'>Verified</span>";break;
			}
			if($row1['user_email']!='')
			{
			$row[]=$verification_status;
			}
			else
			{
			$row[]=' N/A';	
			}
		/*	$row[]=$row1['user_password_text'];	*/
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	/* Remove Class */
	public function removefamilyAction()
	{
		
		global $objSession ; 
		$user_id=$this->getRequest()->getParam("idval");	
		$this->modelStatic->Super_Delete("users","user_id='".$user_id."'");
		$student_family=array();
		$student_family=$this->modelStatic->Super_Get("student_family","s_f_fid='".$user_id."'","fetchAll");
		foreach($student_family as $k=>$v)
		{
			$this->modelStatic->Super_Delete("users","user_id='".$student_family['s_f_sid']."'");
		}
		$this->modelStatic->Super_Delete("student_family","s_f_fid='".$user_id."'");
		$objSession->successMsg = "Family has been removed Successfully";				
		$this->_redirect('family');
	}
	
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
	/* Add or Update Teacher */
	public function newfamilyAction()
	{
		global $objSession ; 
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='2'","fetch");	
				if(empty($paremission_data))
				{
					/* If Permission Data is empty */
					$this->redirect("profile/dashboard");	
				}
			}	
			else
			{
				/* If user is not admin or subadmin */
				$this->redirect('index');	
			}
		}
			$user_id=$this->getRequest()->getParam("user_id");
			$status=$this->getRequest()->getParam("status");
			$form = new Application_Form_SchoolForm();
			$modelEmail = new Application_Model_Email();
			$family_data=array();
			if(isset($user_id) && !empty($user_id))
			{
				$form->family($user_id);
				$this->view->pageHeading = 'Edit Family';
				$this->view->pageHeadingshow = '<i class="fa fa-chain"></i>  Edit Family Contact';
				/* Fatch Teacher Data */
				$family_data=$this->modelStatic->Super_Get("users","user_id='".$user_id."'");
				/* Populate Form Data */
				$student_data=$this->modelStatic->Super_Get("student_family","s_f_fid='".$user_id."'","fetchAll");
			
				$student_option=array();
				foreach($student_data as $k=>$v)
				{
					$student_option[$k]=$v['s_f_sid'];
				}
				if(empty($student_option))
				{
					$student_option['None']='None';
				}
				$family_data['family_students']=$student_option;
				/* Populate Form Data */
				$form->populate($family_data);
				$this->view->user_id=$user_id;
			}
			else
			{
				$form->family();
				$this->view->pageHeading = 'Add Family Contact';
				$this->view->pageHeadingshow = '<i class="fa fa-chain"></i> Add Family Contact';	
			}
			$this->view->form=$form;
			
			$this->view->family_data=$family_data;
			if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ 
					// Form Valid
					/* Get Form Data */
					$data_insert=$form->getValues();
					$new_student_status=$data_insert['newstudent_param'];
					unset($data_insert['newstudent_param']);
					/* family students */
					$family_students=array();
					$family_students=$data_insert['family_students'];
					unset($data_insert['family_students']);
					/* end family students array */
					/* Check condtion For Edit or Add */
					if(isset($family_data) && !empty($family_data))
					{
						/* Edit Teacher Data */
						$this->modelStatic->Super_Insert("users",$data_insert,'user_id="'.$user_id.'"');
						$objSession->successMsg = "Family has been updated Successfully";
					}
					else
					{
						/* Get School ID */
						$school_id='';
						if($this->view->user->user_type=='school')
						{
							$school_id=$this->view->user->user_id;	
						}
						else
						{
							$school_id=$this->view->user->user_school_id;
						}
						/* Add Teacher Data */
//						$password=randomPassword();
						$password=12345;
						$data_insert['user_password']=md5($password);
						$data_insert['user_password_text']=$password;
						$data_insert['user_school_id']=$school_id;
						$data_insert['user_created']=gmdate('Y-m-d H:i');
						$data_insert['user_type']="family";
						$data_insert['user_insertby']=$this->view->user->user_id;
						$user_name='';
						if($data_insert['user_login_type']==1)
						{
							/* Add Family without Email */
							$user_name=$this->modelStatic->insertuniqueusername($data_insert['user_first_name'],$data_insert['user_last_name']);
							$password=$data_insert['user_last_name'];
							
						}
						else
						{ 
							if(!filter_var($data_insert['user_email'], FILTER_VALIDATE_EMAIL))
							{
								/* it is an valid email address */	
								$user_name=$data_insert['user_email'];
								unset($data_insert['user_email']);
							}
							
						}
						unset($data_insert['user_login_type']);
						$data_insert['user_username']=$user_name;
						$data_insert['user_status']=1;
						$is_insert=$this->modelStatic->Super_Insert("users",$data_insert);
						$user_id=$is_insert->inserted_id;
						$data_user=array();
						$data_user=$data_insert;
						$data_user['user_password']=$password;
						$objSession->successMsg = "Teacher has been added Successfully";
						$reset_password_key = md5($user_id."!@#$%^$%&(*_+".time());						
						$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key);						
						$this->modelStatic->Super_Insert("users",$data_to_update1,'user_id="'.$user_id.'"');					
						$data_user['pass_resetkey'] = $reset_password_key ;
						$data_user['user_reset_status'] = "1" ;
						$data_user['last_inserted_id'] = $user_id ;	
						$data_user['school_name'] = $this->view->user->user_school_name ;
						$data_user['user_position']= "Family";
						if($data_user['user_email']!='')
						{
							$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user);
						}
						$objSession->successMsg = "Family has been added Successfully";
						//$isSend = $modelEmail->sendEmail('registration_email',$data_user);
					}
					
					/* Delete Already Added Classes */
					$this->modelStatic->Super_Delete("student_family",'s_f_fid="'.$user_id.'"');
				
					foreach($family_students as $k=>$v)
					{
						
							if($v!='None')
							{
							$data_arr=array();
							$data_arr=array('s_f_sid'=>$v,
											's_f_fid'=>$user_id,
											's_f_date'=>gmdate("Y-m-d H:i:s")
							);
							$kk=$this->modelStatic->Super_Insert("student_family",$data_arr);
						
							}
					}
			
				if($new_student_status==0)
				{
					$this->_redirect('family/index');
				}
				else
				{
					$this->_redirect('student/newstudent/family_id/'.$user_id);	
				}
				
			 }
					
			}
			
	}
	
 	
}