<?php
class StudentController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		
   	}
	
	
 	public function indexAction(){
		
 		global $objSession ; 
		$this->view->pageHeading = "All Students";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  All Students';
		$class_id=$this->getRequest()->getParam('class_id');
		$type=$this->getRequest()->getParam('type');
		if(!isset($type))
		{
			$type=1;	
		}
		$this->view->type=$type;
		if(isset($class_id) && !empty($class_id))
		{
			$this->view->class_id=$class_id;
		}
		else
		{
			$this->view->class_id=0;	
		}
		/* Check if user type is not school */
		
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='3'","fetch");	
				
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
	
	
	
	
	
	/* Get All Students */
	public function getstudentAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$type=$this->getRequest()->getParam('type');
		if(!isset($type))
		{
			$type=0;	
		}
		
		$class_id=$this->getRequest()->getParam('class_id');
 		$aColumns = array(
			'student.user_id',
			'student.user_first_name',
			'student.user_last_name',
			'student.user_email',
			'student.user_created',
			'student.user_status',
			'student.user_verification_mail',
			'student.user_email_verified',
			'student.user_username',
			'student.user_password_text',
			'student.user_family_type',
			
			
		);
		$sIndexColumn = 'student.user_id';
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
		$sOrder = " ORDER BY user_last_name ASC	";
		
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
				//$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(student.user_first_name,' ',student.user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
		//prd($class_id);
		$sWhere1=$sWhere;
		if ( $sWhere == "" )
			{
				$sWhere= "WHERE (student.user_type='student' and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."'))";
				if($class_id==0)
				{ 
					$sWhere1= "WHERE (student.user_type='student' and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."'))";
				}
				else
				{
					$sWhere1 = "WHERE (student.user_type='student' and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') and student_class.student_class_classid='".$class_id."')";
				}
					
			}
			else
				{
				$sWhere .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') )";
				if($class_id==0)
				{
					$sWhere1 .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') )";
				}
				else
				{
					$sWhere1 .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."')  and student_class.student_class_classid='".$class_id."' )";	
				}
				}
				if($type==1)
				{
					$sWhere1.=" and student.user_status='1'";
				}
				
				
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) .",group_concat(distinct(class_name)) as classes ,group_concat(distinct(teacher.user_first_name)) as teachers ,group_concat(distinct(Instruments.Instrument_name)) as insruments , COUNT(distinct(lesson_id)) as lessoncount  FROM $sTable  student left join student_class on student_class.student_class_studentid=student.user_id left join Classes on Classes.class_id=student_class.student_class_classid 
		left join private_teacher on private_teacher.private_teacher_studentid=student.user_id left join users as teacher on teacher.user_id=private_teacher.private_teacher_teacherid  left join student_instrument on student_instrument.student_instrument_studentid=student.user_id left join Instruments on (Instruments.Instrument_id=student_instrument.student_instrument_insid and Instrument_active='1')
		left join lesson on lesson.lesson_teacherid=private_teacher.private_teacher_teacherid $sWhere1 group by student.user_id $sOrder $sLimit";
		
		
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt ";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`user_id`) as cnt FROM $sTable  student $sWhere ";
		//echo $sQuery;die;
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
		$modelSchool = new Application_Model_SchoolModel();
		foreach($qry as $row1){
			
			$lesson_count_arr=array();
			$lesson_count_arr=$modelSchool->getstudentlesson1($row1['user_id']);
 			$row=array();
			
		
			$row[]=$row1['user_last_name'];
			$row[]=$row1['user_first_name'];
			$status = $row1['user_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['user_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1['user_id'].'" onChange="globalStatus(this)" />
					</div>';
			$username="Null";
			if($row1['user_username']!='')
			{
					$username=$row1['user_username'];
			}
			$row[]=$username;
					if($row1['user_email']=='')
					{
						$student_family_arr=$this->modelStatic->Super_Get("student_family","s_f_sid='".$row1['user_id']."'","fetch");
						if(!empty($student_family_arr))
						{
							
							$family_user_deta=$this->modelStatic->Super_Get("users",'user_id="'.$student_family_arr['s_f_fid'].'"',"fetch");
							if(!empty($family_user_deta))
							{
								/*$row[]=$family_user_deta['user_email'];	*/
							
							if($row1['user_family_type']!=3)
							{
								$row[]="Linked to Family Email";	
							}	
							else
							{
								$row[]="Null";
							}
							}
							else
							{
								$row[]="Null";	
							}
							
						}else
						{
							$row[]="Null";
						}
					}
					else
					{
						$row[]=$row1['user_email'];	
					}
			
			
			$row[]='<a style="cursor:pointer" href="'.APPLICATION_URL.'/teacher/viewlessons/student_id/'.$row1['user_id'].'">'.count($lesson_count_arr).' lessons</a>';
			$row[] =  '<a href="'.APPLICATION_URL.'/student/newstudent/user_id/'.$row1['user_id'].'/status/1" class="btn btn-xs purple"> 
			 <i class="fa fa-edit"></i></a>';
			/*$row[] = ' <a onclick="removeclass('.$row1['user_id'].')" class="btn btn-xs purple delete_class">
			 <i class="fa fa-trash-o"></i>
			</a>'; */
			$mail_sent='';
			if($row1['user_verification_mail']==1 && $row1['user_email']!='')
			{
				$mail_sent="<b>Mail Sent</b>";	
			}
			$row[]='<input type="checkbox"  value="'.$row1['user_id'].'"   name="select_users[]" id="select_user_'.$row1['user_id'].'"  />'.'&nbsp;'.$mail_sent;
			$row[]=$row1['teachers'];
			$row[]=$row1['classes'];
			
			$row[]=$row1['insruments'];
   			$row[]=date('d F Y g:i A', strtotime($row1['user_created']));
			if($row1['user_email']!='')
			{
				if($row1['user_verification_mail']==0)
				{
					$row[]='<a href="'.SITE_HTTP_URL.'/student/sendverification/type/1/user_id/'.$row1["user_id"].'"><span class="badge badge-success badge-roundless">Send Verification Mail</span></a>';	
				}
				else if($row1['user_email_verified']==0)
				{
							
			
						$row[]='<a href="'.SITE_HTTP_URL.'/student/sendverification/type/2/user_id/'.$row1['user_id'].'"><span class="badge badge-success badge-roundless">Resend Verification Mail</span></a>';	
				}
				else
				{
					
			
					$row[]='<a href="'.SITE_HTTP_URL.'/student/sendpasswordresetmail/user_id/'.$row1["user_id"].'"><span class="badge badge-success badge-roundless">Send Reset Passwordtest Mail  </span></a>';	
				}
			}
			else
			{
			
				$row[]=' N/A';	
			}	
			
			
				switch($row1['user_email_verified']){
				case '0':$verification_status ="<span class='badge badge-danger badge-roundless'>Unverified</span>";break;
				default :$verification_status ="<span class='badge badge-success badge-roundless'>Verified</span>";break;
			}
			
		
			$row[]=$verification_status;
			
			/*$row[]=$row1['user_password_text'];*/
 			$output['aaData'][] = $row;
			$j++;
			$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	public function sendverificationAction()
	{
		global $objSession; 
		$user_id=$this->getRequest()->getParam('user_id');
		$type=$this->getRequest()->getParam('type');
		$superModel = new Application_Model_SuperModel();
		$modelEmail = new Application_Model_Email();
		$user_data=$superModel->Super_Get("users","user_id='".$user_id."'","fetch");
		//$isSend = $modelEmail->sendEmail('registration_email_verification',$user_data);
		/* Send verfication */
			$data_user=array();
			$data_user=$user_data;
			$data_user['user_password']=strtolower(str_replace(' ','',trim($user_data['user_first_name'].$user_data['user_last_name'])));
			$reset_password_key = md5($user_id."!@#$%^$%&(*_+".time());						
			$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key,'user_password'=>$data_user['user_password']);						
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
		$objSession->successMsg="Verification mail has been send successfully";
		if($type==1)
		{
			$user_data=array('user_verification_mail'=>1);	
			$superModel->Super_Insert("users",$user_data,'user_id="'.$user_id.'"');
		}
		else
		{
			
		}
		$this->redirect("student");
		
	}
	
	public function sendpasswordresetmailAction()
	{
		global $objSession; 
		$user_id=$this->getRequest()->getParam("user_id");
		$superModel = new Application_Model_SuperModel();
		$modelEmail = new Application_Model_Email();
		$user_data=$superModel->Super_Get("users","user_id='".$user_id."'","fetch");
		$password=generateRandomString(8);
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
		$this->redirect("student");
	}
	
	public function sendmailAction()
	{
		global $objSession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$modelEmail = new Application_Model_Email();
 		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			if(isset($formData['select_users']) and count($formData['select_users'])){
				 foreach($formData['select_users'] as $key=>$values){
					 $check_data=array();
					 $check_data=$this->modelStatic->Super_Get("users","user_id='".$values."'","fetch");
					 $check_stu_family=array();
					 $check_stu_family=$this->modelStatic->Super_Get("student_family","s_f_sid='".$values."'","fetch");
					 
					 if($check_data['user_email']!='')
					 {
				 	 $isSend = $modelEmail->sendEmail('registration_email_verification',$check_data);
					 if($check_data['user_verification_mail']==0)
					 {
						 /* Mail is not send yet */
						$update_user=array();
						$update_user=array('user_verification_mail'=>1);	
						$this->modelStatic->Super_Insert("users",$update_user,'user_id="'.$values.'"');
					 }
					 }
					 else if(!empty($check_stu_family))
					 {
						$family_arr=array();
						$family_arr=$this->modelStatic->Super_Get("users","user_id='".$check_stu_family['s_f_fid']."'","fetch");
						if(!empty($family_arr) && $family_arr['user_email']!='')
						{
						
						 	 
							 /* Mail is not send yet */
							 if($family_arr['user_verification_mail']==0)
					 		{
								/* Send verfication */
								$data_user=array();
								$data_user=$family_arr;
								$data_user['user_password']=str_replace(' ','',trim($family_arr['user_first_name'].$family_arr['user_last_name']));
								$reset_password_key = md5($values."!@#$%^$%&(*_+".time());						
								$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key,'user_password'=>md5($data_user['user_password']),'user_password_text'=>$data_user['user_password']);						
								$this->modelStatic->Super_Insert("users",$data_to_update1,'user_id="'.$values.'"');					
								$data_user['pass_resetkey'] = $reset_password_key ;
								$data_user['user_reset_status'] = "1" ;
								$data_user['last_inserted_id'] = $values ;	
								$data_user['school_name'] = $this->view->user->user_school_name ;
								$data_user['user_position']= ucfirst($family_arr['user_type']);
								if($data_user['user_email']!='')
								{
								$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user);
								}
								/* End verification */
								 $update_user=array();
								$update_user=array('user_verification_mail'=>1);	
								$this->modelStatic->Super_Insert("users",$update_user,'user_id="'.$family_arr['user_id'].'"');	
					 		}
							else
							{
								$isSend = $modelEmail->sendEmail('registration_email_verification',$family_arr);	
							}
						}
					}
						
				 }
 				$objSession->successMsg = "Verification mail has been send Successfully ";
 			}else{
					$objSession->errorMsg = "Invalid Request to Send Mail User(s) ";
			}
 			$this->_redirect('student');
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
				 foreach($formData['select_users'] as $key=>$values){
   					 $this->modelStatic->Super_Delete("users","user_id='".$values."'");
 					
				 }
 				$objSession->successMsg = " Users Deleted Successfully ";
 			}else{
				$objSession->errorMsg = "Invalid Request to Delete User(s) ";
			}
 			$this->_redirect('student');
		} 
		
	/*	$user_id=$this->getRequest()->getParam("idval");	
		$this->modelStatic->Super_Delete("users","user_id='".$user_id."'");
		$objSession->successMsg = "Student has been removed Successfully";				
		$this->_redirect('student');*/
	}
	/* Add or Update Teacher */
	public function newstudentAction()
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
					$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='3'","fetch");			
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
			$student_id=$this->getRequest()->getParam("user_id");
			$family_id=$this->getRequest()->getParam("family_id");
			$status=$this->getRequest()->getParam("status");
			$form = new Application_Form_SchoolForm();
			$familyform = new Application_Form_SchoolForm();
			$familyform->addnewfamily();
			$modelEmail = new Application_Model_Email();
			$student_data=array();
			$school_id=$this->view->user->user_id;
			if(isset($student_id) && !empty($student_id))
			{
				$form->newstudent($student_id,$school_id);
				$this->view->pageHeading = 'Edit Student';
				$this->view->pageHeadingshow = '<i class="fa fa-chain"></i>  Edit Student';
				/* Fatch Student Data */
				$student_data=$this->modelStatic->Super_Get("users","user_id='".$student_id."'");
				$studentclass=$this->modelStatic->Super_Get("student_class","student_class_studentid='".$student_id."'","fetchAll");
				$studentteacher=$this->modelStatic->Super_Get("private_teacher","private_teacher_studentid='".$student_id."'","fetchAll");
				$studentinstrument=$this->modelStatic->Super_Get("student_instrument","student_instrument_studentid='".$student_id."'","fetchAll");
				$studentfamily=$this->modelStatic->Super_Get("student_family","s_f_sid='".$student_id."'","fetchAll");
				$class_optn=array();
				$student_optn=array();
				$student_ins=array();
				$student_family=array();
				/* Set Value of student classes */
				foreach($studentclass as $k=>$v)
				{
					$class_optn[$k]=$v['student_class_classid'];
				}
				if(empty($class_optn))
				{
					$class_optn['None']='None';
				}
				/* Set Value of student teachers */
				foreach($studentteacher as $k=>$v)
				{
					$student_optn[$k]=$v['private_teacher_teacherid'];
							
				}
				if(empty($student_optn))
				{
					$student_optn['None']='None';
				}
				/* Set Value of student instruments */
				foreach($studentinstrument as $k=>$v)
				{
					$student_ins[$k]=$v['student_instrument_insid'];							
				}
				if(empty($student_ins))
				{
					$student_ins['None']='None';
				}
				
				/* Set Value of student families */
				foreach($studentfamily as $k=>$v)
				{
					$student_family[$k]=$v['s_f_fid'];
							
				}
				if(empty($student_family))
				{
					$student_family['None']='None';
				}
				
				$student_data['user_student_family']=$student_family;
				$student_data['user_student_instrument']=$student_ins;
				$student_data['student_class']=$class_optn;
				$student_data['student_private_teacher']=$student_optn;
				$studentfamily_email_data=$this->modelStatic->Super_Get("student_family","s_f_sid='".$student_id."'","fetch");
				$student_data['family_contact_student']=0;
						if(!empty($studentfamily_email_data))
						{
							$famil_user_detail=$this->modelStatic->Super_Get("users",'user_id="'.$studentfamily_email_data['s_f_fid'].'"',"fetch");
							
						}
				
				if($student_data['user_email_option']==1)
				{
					$student_data['user_email_family']	=$famil_user_detail['user_email'];
				}
				$student_data['family_contact_student']=$student_data['user_email_option'];
				
				/* Populate Form Data */
				$form->populate($student_data);
			}
			else
			{
				$student_count=0;
				$acccount_type=1;
				$school_id=$this->view->user->user_school_id;
				/* Find User account Type */
				if($this->view->user->user_type=='school')
				{
						$acccount_type=$this->view->user->user_account_type;
				}
				else
				{
						$school_id=$this->view->user->user_school_id;
						$user_school_data=$this->modelStatic->Super_Get("users","user_id='".$school_id."'","fetch");	
						$acccount_type=$user_school_data['user_account_type'];
				}
				if($this->view->user->user_type=='school')
				{
					$school_id=$this->view->user->user_id;
				}
				else
				{
					$school_id=$this->view->user->user_school_id;	
				}
		
				$school_data=$this->modelStatic->Super_Get("users","user_id='".$school_id."'","fetch");
				$get_student_count_arr=$this->modelStatic->Super_Get("users","user_type='student' and user_school_id='".$school_id."'","fetch",array("fields"=>array(new Zend_Db_Expr("IFNULL(COUNT(user_id),0) as studentcount" ))));
				
				if($school_data['user_student_active']==1 && ($school_data['user_maxstudent_count']<=$get_student_count_arr['studentcount']))
				{
					if($get_student_count_arr['studentcount']>=$student_count )
					{
						$objSession->errorMsg="You have not permission to add more then ".$school_data['user_maxstudent_count']." students. ";
						$this->redirect("student/index");	
					}
				}
				$form->newstudent('',$this->view->user->user_id);
				if(isset($family_id) && !empty($family_id))
				{
					$student_family=array();
					$student_family[$family_id]=$family_id;
					$student_famil_data=array();
					$student_famil_data['user_student_family']=$student_family;
					//$form->user_student_family->setValue($student_family);
					$form->populate($student_famil_data);
					$this->view->family_id=$family_id;
			    }
				$this->view->pageHeading = 'Add Student';
				$this->view->pageHeadingshow = '<i class="fa fa-chain"></i> Add Student';	
			}
			
			$this->view->form=$form;
			$this->view->familyform=$familyform;
			$this->view->student_data=$student_data;
			if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ 
				// Form Valid
					/* Get Form Data */
					$data_insert=$form->getValues();
					unset($data_insert['user_student_family']['None']);
					unset($data_insert['user_student_instrument']['None']);
					unset($data_insert['student_class']['None']);
					$student_class=array();
					$student_private_teacher=array();
					$student_instrument_array=array();
					$student_family_array=array();
					if(!empty($data_insert['student_class']))
					{
						$student_class=$data_insert['student_class'];
					}
					unset($data_insert['student_class']);
					if(!empty($data_insert['student_private_teacher']))
					{
						$student_private_teacher=$data_insert['student_private_teacher'];
					}
					unset($data_insert['student_private_teacher']);
					if(!empty($data_insert['user_student_instrument']))
					{
						$student_instrument_array=$data_insert['user_student_instrument'];
					}
					unset($data_insert['user_student_instrument']);
					if(!empty($data_insert['user_student_family']))
					{
						$student_family_array=$data_insert['user_student_family'];
					}
					$data_insert['user_email_option']=$data_insert['family_contact_student'];
					unset($data_insert['user_student_family']);
					unset($data_insert['user_email_hidden']);
					unset($data_insert['family_contact']);
					unset($data_insert['family_contact_student']);
					unset($data_insert['user_family_firstname']);
					unset($data_insert['user_family_lastname']);
					/* Check condtion For Edit or Add */
					$family_type=$data_insert['family_type'];
					unset($data_insert['family_type']);
					$data_insert['user_family_type']=$family_type;
					$family_firstname_hidden=$data_insert['family_firstname_hidden'];
					unset($data_insert['family_firstname_hidden']);
					$family_lastname_hidden=$data_insert['family_lastname_hidden'];
					unset($data_insert['family_lastname_hidden']);
					$family_email_hidden=$data_insert['family_email_hidden'];
					unset($data_insert['family_email_hidden']);
					$family_email_text=$data_insert['user_email_family'];
					unset($data_insert['user_email_family']);
					if(isset($student_data) && !empty($student_data))
					{
						/* Edit Student Data */
						$this->modelStatic->Super_Insert("users",$data_insert,'user_id="'.$student_id.'"');
						if($family_type==0)
						{
							/* here we have to enter family user */
							$check_family=$this->modelStatic->Super_Get("users","user_email='".$family_email_text."'","fetch");
							if(empty($check_family))
							{
										$data_family=array(
										'user_first_name'=>$data_insert['user_first_name'],
										'user_last_name'=>$data_insert['user_last_name'],
										'user_email'=>$family_email_text,
										'user_type'=>'family',
										'user_school_id'=>$school_id,
										'user_created'=>gmdate("Y-m-d H:i:s"),
										'user_insertby'=>$this->view->user->user_id,
										'user_status'=>1
									);
								if($family_email_text=='')
								{
									unset($data_family['user_email']);	
								}
								$user_name='';
								//$user_name=$this->modelStatic->insertusername('family');
								$user_name=$this->modelStatic->insertuniqueusername($data_insert['user_first_name'],$data_insert['user_last_name']);
								$data_family['user_username']=$user_name;
								$is_insert_family=$this->modelStatic->Super_Insert("users",$data_family);
								$family_id=$is_insert_family->inserted_id;
								//$password=randomPassword();
								$password=12345;
								$data_user_family=array();
								$data_user_family=$data_family;
								$data_user_family['user_password']=$password;
								$reset_password_key = md5($family_id."!@#$%^$%&(*_+".time());						
								$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key,'user_password'=>md5($password),'user_password_text'=>$password);						
								$this->modelStatic->Super_Insert("users",$data_to_update1,'user_id="'.$family_id.'"');		
								/*  Send mail to family with login details */
								$data_user['pass_resetkey'] = $reset_password_key ;
								$data_user['user_reset_status'] = "1" ;
								$data_user['last_inserted_id'] = $family_id ;	
								$data_user['user_position']= "Family";
								$data_user['school_name'] = $this->view->user->user_school_name ;
								if($family_email_text!='')
								{
								$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user_family);
								}
							}
							else
							{
								$family_id=$check_family['user_id'];
								if($check_family['user_fist_name']!=$data_insert['user_first_name'] || $check_family['user_last_name']!=$data_insert['user_last_name'])
								{
									$data_update_user=array();
									$data_update_user=array('user_first_name'=>$data_insert['user_first_name'],
															'user_last_name'=>$data_insert['user_last_name']
									);	
									$this->modelStatic->Super_Insert("users",$data_update_user,"user_id='".$family_id."'");
								}
							
							}
							
						}
						
						if($student_data['user_email']=='' && !empty($data_insert['user_email']))
						{
								//$password=randomPassword();
								$password=12345;
								$data_user=array();
								$data_user=$data_insert;
								$data_user['user_password']=$password;
								$reset_password_key = md5($student_id."!@#$%^$%&(*_+".time());						
								$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key,'user_password'=>md5($password),'user_password_text'=>$password);						
								$this->modelStatic->Super_Insert("users",$data_to_update1,'user_id="'.$student_id.'"');		
								/*  Send mail to student with login details */
								$data_user['pass_resetkey'] = $reset_password_key ;
								$data_user['user_reset_status'] = "1" ;
								$data_user['last_inserted_id'] = $student_id ;	
								$data_user['user_position']= "Student";
								$data_user['school_name'] = $this->view->user->user_school_name ;
								if($data_user['user_email']!='')
								{
								$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user);
								}
						}
						$objSession->successMsg = "Student has been updated Successfully";
					}
					else
					{
					
						/* Add Student Data */
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
						
						/* Generate random Passwors */
						//$password=randomPassword();
						/*if(isset($data_insert['user_email']) && !empty($data_insert['user_email']))
						{
							$password=randomPassword();
						}
						else
						{
								$password=12345;
						}*/
						$password=12345;
						$data_insert['user_password']=md5($password);
						$data_insert['user_password_text']=$password;
						/*$data_insert['user_status']=1;*/
						$data_insert['user_school_id']=$school_id;
						$data_insert['user_created']=gmdate('Y-m-d H:i');
						$data_insert['user_type']="student";
						$data_insert['user_insertby']=$this->view->user->user_id;
						$data_insert['user_status']=1;
						/* Inser data of student */
						$user_name='';
					//	$user_name=$this->modelStatic->insertusername('student');
				
						$user_name=$this->modelStatic->insertuniqueusername($data_insert['user_first_name'],$data_insert['user_last_name']);
				
						$data_insert['user_username']=$user_name;
						$data_insert['user_status']=1;
					
						$is_insert=$this->modelStatic->Super_Insert("users",$data_insert);
						
						$student_id=$is_insert->inserted_id;
						$data_user=array();
						$data_user=$data_insert;
						$data_user['user_password']=$password;
						$objSession->successMsg = "Student has been added Successfully";
						$reset_password_key = md5($student_id."!@#$%^$%&(*_+".time());						
						$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key);						
						$this->modelStatic->Super_Insert("users",$data_to_update1,'user_id="'.$student_id.'"');					
						if(isset($data_insert['user_email']) && !empty($data_insert['user_email']))
						{
							/*  Send mail to student with login details */
							$data_user['pass_resetkey'] = $reset_password_key ;
							$data_user['user_reset_status'] = "1" ;
							$data_user['last_inserted_id'] = $student_id ;	
							$data_user['user_position']= "Student";
							$data_user['school_name'] = $this->view->user->user_school_name ;
							if($data_user['user_email']!='')
								{
							$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user);
								}

						}
						if($family_type==0)
						{
							/*$data_insert['user_status']=1;*/
						$data_insert['user_school_id']=$school_id;
						$data_insert['user_created']=gmdate('Y-m-d H:i');
						$data_insert['user_type']="student";
						$data_insert['user_insertby']=$this->view->user->user_id;
						$data_insert['user_status']=1;
							/* here we have to enter family user */
								$data_family=array(
										'user_first_name'=>$data_insert['user_first_name'],
										'user_last_name'=>$data_insert['user_last_name'],
										'user_email'=>$family_email_text,
										'user_type'=>'family',
										'user_school_id'=>$school_id,
										'user_created'=>gmdate("Y-m-d H:i:s"),
										'user_insertby'=>$this->view->user->user_id,
										'user_status'=>1
										
								);
								if($family_email_text=='')
								{
									unset($data_family['user_email']);	
								}
								$user_name='';
								//$user_name=$this->modelStatic->insertusername('family');
								$user_name=$this->modelStatic->insertuniqueusername($data_insert['user_first_name'],$data_insert['user_last_name']);
								$data_family['user_username']=$user_name;
								$is_insert_family=$this->modelStatic->Super_Insert("users",$data_family);
								$family_id=$is_insert_family->inserted_id;
								//$password=randomPassword();
								$password=12345;
								$data_user_family=array();
								$data_user_family=$data_family;
								$data_user_family['user_password']=$password;
								$reset_password_key = md5($family_id."!@#$%^$%&(*_+".time());						
								$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key,'user_password'=>md5($password),'user_password_text'=>$password);						
								$this->modelStatic->Super_Insert("users",$data_to_update1,'user_id="'.$family_id.'"');		
								/*  Send mail to family with login details */
								$data_user['pass_resetkey'] = $reset_password_key ;
								$data_user['user_reset_status'] = "1" ;
								$data_user['last_inserted_id'] = $family_id ;	
								$data_user['user_position']= "Family";
								$data_user['school_name'] = $this->view->user->user_school_name ;
								if($family_email_text!='')
								{
								$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user_family);
								}
							
						}
											//	$isSend = $modelEmail->sendEmail('registration_email',$data_user);
					}
				/* Remove all classes of student */	
					$this->modelStatic->Super_Delete("student_class",'student_class_studentid="'.$student_id.'"');
					foreach($student_class as $k=>$v)
						{
							if($v!='None')
							{
							/* Remove  class of student */	
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							// ================================
							$data=array('student_class_classid'=>$v,
										'student_class_studentid'=>$student_id,
										// 'student_class_date'=>gmdate('Y-m-d H:i:s')
										'student_class_date'=>date('Y-m-d H:i:s')
						
							);	
							$kk=$this->modelStatic->Super_Insert("student_class",$data);
							}
							
						}
					/* Remove all private teachers of student */	
					$this->modelStatic->Super_Delete("private_teacher",'private_teacher_studentid="'.$student_id.'"');
					foreach($student_private_teacher as $k=>$v)
						{
							if($v!='None')
							{
							/* Remove  private teacher of student */	
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							// ================================
							$data=array('private_teacher_studentid'=>$student_id,
										'private_teacher_teacherid'=>$v,
										// 'private_teacher_date'=>gmdate('Y-m-d H:i:s')
										'private_teacher_date'=>date('Y-m-d H:i:s')
						
							);	
							$kk=$this->modelStatic->Super_Insert("private_teacher",$data);
							}
						}
						
					/* Remove all private instruments of student */	
					$this->modelStatic->Super_Delete("student_instrument",'student_instrument_studentid="'.$student_id.'"');
					foreach($student_instrument_array as $k=>$v)
						{
							if($v!='None')
							{
							/* Remove  instruments of student */	
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							// ================================
							$data=array('student_instrument_studentid'=>$student_id,
										'student_instrument_insid'=>$v,
										// 'student_instrument_date'=>gmdate('Y-m-d H:i:s')
										'student_instrument_date'=>date('Y-m-d H:i:s')
						
							);	
							$kk=$this->modelStatic->Super_Insert("student_instrument",$data);
							}
						}
					/* Remove all private families of student */	
					$this->modelStatic->Super_Delete("student_family",'s_f_sid="'.$student_id.'"');
					if($family_type==1)
					{
						/* Add Family First */
						$add_family=array();
						if($family_email_hidden!='')
						{
							$add_family=array(
								'user_first_name'=>$family_firstname_hidden,
								'user_last_name'=>$family_lastname_hidden,
								'user_email'=>$family_email_hidden
							);
						}
						else
						{
							$add_family=array(
								'user_first_name'=>$family_firstname_hidden,
								'user_last_name'=>$family_lastname_hidden,
								
							);	
						}
						//$password=randomPassword();
						$password=12345;
						$add_family['user_password']=md5($password);
						$add_family['user_password_text']=$password;
						$add_family['user_school_id']=$school_id;
						$add_family['user_created']=gmdate('Y-m-d H:i');
						$add_family['user_type']="family";
						$add_family['user_insertby']=$this->view->user->user_id;
						$add_family['user_status']=1;
						/* Inser data of family */
						$user_name='';
						//$user_name=$this->modelStatic->insertusername('family');
						$user_name=$this->modelStatic->insertuniqueusername($family_firstname_hidden,$family_lastname_hidden);
						$add_family['user_username']=$user_name;
						$is_insert_family=$this->modelStatic->Super_Insert("users",$add_family);
						$family_id=$is_insert_family->inserted_id;
						$data_user_family=array();
						$data_user_family=$add_family;
						$data_user_family['user_password']=$password;
						$reset_password_key = md5($family_id."!@#$%^$%&(*_+".time());						
						$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key);						
						$this->modelStatic->Super_Insert("users",$data_to_update1,'user_id="'.$family_id.'"');		
						/* End Add Family Option */		
						/*  Send mail to student with login details */
							$data_user_family['pass_resetkey'] = $reset_password_key ;
							$data_user_family['user_reset_status'] = "1" ;
							$data_user_family['last_inserted_id'] = $family_id ;	
							$data_user_family['user_position']= "family";
							$data_user_family['school_name'] = $this->view->user->user_school_name ;
							if($family_email_hidden!='')
								{
							$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user_family);
								}
						/* Add Student To Family */
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						// ================================
						$data=array('s_f_sid'=>$student_id,
										's_f_fid'=>$family_id,
										// 's_f_date'=>gmdate('Y-m-d H:i:s')
										's_f_date'=>date('Y-m-d H:i:s')
						
							);
							$get_already_exists=array();
							$get_already_exists=$this->modelStatic->Super_Get("student_family","s_f_sid='".$student_id."'","fetch");
							if(empty($get_already_exists))
							{
								$kk=$this->modelStatic->Super_Insert("student_family",$data);
							}
						/* Add Student to family */
					}
					else if($family_type==0)
					{
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						// ================================
						$data=array('s_f_sid'=>$student_id,
										's_f_fid'=>$family_id,
										// 's_f_date'=>gmdate('Y-m-d H:i:s')
										's_f_date'=>date('Y-m-d H:i:s')
						
							);
							$get_already_exists=array();
							$get_already_exists=$this->modelStatic->Super_Get("student_family","s_f_sid='".$student_id."'","fetch");
							if(empty($get_already_exists))
							{
								$kk=$this->modelStatic->Super_Insert("student_family",$data);
							}	
					}
					else
					{
						foreach($student_family_array as $k=>$v)
						{
							if($v!='None')
							{
							/* Remove  instruments of student */	
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							// ================================
							$data=array('s_f_sid'=>$student_id,
										's_f_fid'=>$v,
										// 's_f_date'=>gmdate('Y-m-d H:i:s')
										's_f_date'=>date('Y-m-d H:i:s')
						
							);
							$get_already_exists=array();
							$get_already_exists=$this->modelStatic->Super_Get("student_family","s_f_sid='".$student_id."'","fetch");
							if(empty($get_already_exists))
							{
								$kk=$this->modelStatic->Super_Insert("student_family",$data);
							}
							}
						
						}	
					}
				
					
				if(isset($status) && $status==1)
				{
					$this->_redirect('student/index');
				
				}
				else
				{
					$this->_redirect('student/index');
				}
			 }
					
			}
			
	}
	
 	
}