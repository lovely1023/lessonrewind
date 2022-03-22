<?php
class ProfileController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		$this->modelUser = new Application_Model_User();
		$this->pluginImage = new Application_Plugin_Image();
		
   	}
	
	
 	public function indexAction(){	
 		global $objSession ; 
   		$content = $this->modelStatic->getPage(40); 
		$form = new Application_Form_User();
		$this->view->show = "front_profile" ; 
		$this->view->page_slug = "front_profile" ; 
		$form->profile_admin($this->view->user->user_id);
		$user_arr_data=array();
		$user_arr_data=(array)$this->view->user;
		if($this->view->user->user_type=='school')
		{
			$student_count_arr=$this->modelStatic->Super_Get("users","user_type='student' and user_school_id='".$this->view->user->user_id."'","fetchAll",array("fileds"=>array("user_id")));
			$user_arr_data['user_active_student']=count($student_count_arr);
		
		}
		if($this->view->user->user_type=='student')
		{
				if($this->view->user->user_email=='')
				{
					$check_connected_family=array();
					$check_connected_family=$this->modelStatic->Super_Get("student_family","s_f_sid='".$this->view->user->user_id."'","fetch");
					$family_data=array();
					if(!empty($check_connected_family))
					{
						$family_data=$this->modelStatic->Super_Get("users","user_id='".$check_connected_family['s_f_fid']."'","fetch");
					}
					if(!empty($family_data))
					{
						if($family_data['user_email']!='')
						{
							$user_arr_data['user_email']=$family_data['user_email'];
						}	
						else
						{
							//$user_arr_data['user_email']=$family_data['user_username'];	
						}
					}
					
				}
				
		}
		
		$form->populate($user_arr_data);
		if($this->getRequest()->isPost())
		{
			$data_post = $this->getRequest()->getPost();
			if($form->isValid($data_post))
			{
				$data_to_update = $form->getValues() ;
				
				if(isset($data_to_update['user_active_student']))
				{
					unset($data_to_update['user_active_student']);
				}
			
				
				if($data_to_update['user_email']!=$this->view->user->user_email)
				{
					 $data_to_update["user_email_verified"] = "0" ;
				}
				
				if($this->view->user->user_type=='family')
				{
					if($this->view->user->user_email==$this->view->user->user_username)
					{
						if($data_to_update['user_username']!=$this->view->user->user_username)
						{
							if(filter_var($data_to_update['user_username'], FILTER_VALIDATE_EMAIL))
							{
							$objSession->errorMsg="Username cannot conflict with current Email address. If you want to change the Email address, select “Email – (Change) ";	
							$this->redirect("profile/index");
							}	
						}
						if(($data_to_update['user_email']!=$this->view->user->user_email))
						{
								$data_to_update['user_username']=$data_to_update['user_email'];
						}
					}	
					else
					{
						
					}
				}
				else if($this->view->user->user_type=='student')
				{
						$check_data=$this->modelStatic->Super_Get("users","user_email='".$data_to_update['user_email']."'",'fetch');
						if(!empty($check_data) && $check_data['user_type']=='family')
						{
							$data_to_update['user_email']	=$this->view->user->user_email;
						}
				}
				
 				$is_update  = $this->modelUser->add($data_to_update , $this->view->user->user_id);
				
				if(is_object($is_update) and $is_update->success){
					if($is_update->row_affected > 0)
					{
						$objSession->successMsg = " Profile Information Changed Successfully ";
					}
					else
					{
						$objSession->infoMsg = " New Information is Same as Pervious one ";
					}
					$this->_helper->getHelper("Redirector")->gotoRoute(array(),"front_profile");
				}
			
			$objSession->errorMsg  = $is_update->message; ;
			
		}else{
			$objSession->errorMsg = "Please Check Information Again ...!";	
		}
	}	
		$this->view->form = $form;		
		$this->view->content = $content ;
		 
	}
	
	public function getlessonsAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$student_id=$this->getRequest()->getParam('student_id');
		if(!isset($student_id))
		{
			$student_id = $this->view->user->user_id;
		}
 		$aColumns = array(
			'l_s_id',
			'l_s_lessid',
			'l_s_stuid',
			'l_s_teaherid',
			'l_s_viewstatus',
			'l_s_addeddate',
			'l_s_usertype',
			'l_s_schoolid',
			'l_s_autodelete',
			'lesson.lesson_id',
			'lesson.lesson_teacherid',
			'lesson.lesson_title',
			'lesson.lesson_desc',
			'lesson.lesson_date',
			'lesson.lesson_status',
			'users.user_first_name',
			'users.user_last_name',
			'lesson.lesson_student_absent',
			
		);
		$sIndexColumn = 'l_s_id';
		$sTable = 'lesson_student';
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
		$sOrder = "ORDER BY lesson_date DESC";
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(user_first_name,' ',user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
					$sWhere = "WHERE   l_s_stuid='".$student_id."' and lesson_status='1'";
				}
				else
				{
					$sWhere .= " AND  l_s_stuid='".$student_id."' and lesson_status='1'";
				}
			
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." FROM $sTable left join lesson on lesson.lesson_id=lesson_student.l_s_lessid
		left join users on users.user_id=lesson.lesson_teacherid
		   $sWhere group by lesson_id $sOrder $sLimit";
	
	
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 		
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable left join lesson on lesson.lesson_id=lesson_student.l_s_lessid
		left join users on users.user_id=lesson.lesson_teacherid
		   $sWhere group by lesson_id $sOrder $sLimit";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		if(empty($rResultTotal)){
			$iTotal=0;	
		}
		else{
			$iTotal = $rResultTotal[0]['cnt'];
		}
		
		
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
			//$row[] = $i;
			$row[]=date("m-d-Y",strtotime($row1['lesson_date']));
			$row[]=$row1['user_last_name'];
			$row[]=$row1['lesson_title'];
			/*$status = $row1['l_s_viewstatus']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" name="l_s_viewstatus" class="toggle status-'.(int)$row1['l_s_viewstatus'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';*/
					
			/*$status_admin = $row1['l_s_autodelete']!=1?"checked='checked'":" ";

	$row[]='<div class="danger-toggle-button">

				<input type="checkbox" name="l_s_autodelete" class="toggle status-'.(int)$row1['l_s_autodelete'].' "  '.$status_admin.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />

					</div>';*/
					
			
			$sentorunsent='';
			if($row1['lesson_status']==1)
			{
				$sentorunsent='<span class="badge badge-success badge-roundless">Sent</span>';	
			}
			else
			{
				$sentorunsent='<span class="badge badge-danger badge-roundless">Unsent</span>';	
			}
			
			//$row[]=$sentorunsent;
			
			// $row[]=$row1['lesson_student_absent'];
			if($row1['lesson_student_absent'] == 1)
			{
				$missed= '<i class="fa fa-times" style="color: #069cdb"></i>';	
			}else{
				$missed = '';	
			}			
			$row[] = $missed;
			
			$row[]="<a href='".SITE_HTTP_URL."/teacher/viewdetail/lesson_id/".$row1['lesson_id']."/student_id/".$student_id."'><i class='fa fa-search'></i></a>";
 			$output['aaData'][] = $row;
			$j++;
		    $i++;
		}
		
		echo json_encode($output);
		exit();
  	}
	
	
	/* Add New Announcement */
	public function newannouncementAction()
	{
		global $objSession ; 
		$modelEmail = new Application_Model_Email();
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='7'","fetch");	
				
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
		$announcement_data=array();
		$announcement_id=$this->getRequest()->getParam('announcement_id');
		$form = new Application_Form_SchoolForm();
		$form->newaanouncement();
		if(isset($announcement_id) && !empty($announcement_id))
		{
			$this->view->pageHeading = "Update Announcement";
			$this->view->pageHeadingshow = '<i class="fa fa-bell"></i>  Update Announcement';
			/* Get Announcement Data */
			$announcement_data=$this->modelStatic->Super_Get("announcement","announcement_id='".$announcement_id."'","fetch");	
			
			$ann_arr=$this->modelStatic->Super_Get("announce_attach",'an_un_id="'.$announcement_id.'"','fetchAll');
			$this->view->ann_arr=$ann_arr; 
			$form->populate($announcement_data);
		}
		else
		{
			$this->view->pageHeading = "Add New Announcement";
			$this->view->pageHeadingshow = '<i class="fa fa-bell"></i>  Add New Announcement';	
		}		
		$this->view->form=$form;
		$this->view->announcement_data=$announcement_data;
		
			if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
		
				if ($form->isValid($posted_data))
				{ // If form is valid
					$data_insert=$form->getValues();
					/* Set All announcement to inactive */
					$data_announcement_status=array();
					$school_id='';
					$school_name='';
							if($this->view->user->user_type=='school')
							{
								$school_id=$this->view->user->user_id;	
								$school_name=$this->view->user->user_school_name;
							}
							else
							{
								$school_id=$this->view->user->user_school_id;
								$school_data=array();
								$school_data=$this->modelStatic->Super_Get("users","user_id='".$school_id."'","fetch",array("fields"=>array("user_school_name")));
								$school_name=$school_data['user_school_name'];
							}
					/* Check Condition for announcement data is empty or not */
					if(isset($announcement_data) && !empty($announcement_data))
					{ 
						
						
						if($posted_data['delete_doc']!='')
						{
							$delete_gallery=$this->modelStatic->Super_Get("announce_attach","an_id IN (".ltrim($posted_data['delete_doc'],",").")","fetchall");
							
							foreach($delete_gallery as $del_img)
							{
								unlink(AN_PATH.$del_img['an_name']);	
							}
							
							$this->modelStatic->Super_Delete("announce_attach","an_id IN (".ltrim($posted_data['delete_doc'],",").")");
									
						}
						$data_insert['announcement_status']='1';
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						// ================================
						
						// $data_insert['announcement_date']=gmdate('Y-m-d H:i:s');
						$data_insert['announcement_date']=date('Y-m-d H:i:s');
						$this->modelStatic->Super_Insert("announcement",$data_insert,'announcement_id="'.$announcement_id.'"');
						$options=array();
						if(isset($posted_data['select_teacher']))
						{
							$options = array_merge($posted_data['select_teacher'],$options);
						}
						if(isset($posted_data['select_student']))
						{
							$options = array_merge($posted_data['select_student'],$options);
						}
						if(isset($posted_data['select_family']))
						{
							$options = array_merge($posted_data['select_family'],$options);
						}
						if(isset($posted_data['select_subadmin']))
						{
							$options = array_merge($posted_data['select_subadmin'],$options);
						}
						
						$teacher_str='';
						$teacher_str=implode(',',$options);
						$this->modelStatic->Super_Delete("announcement_user","an_u_id NOT IN (".$teacher_str.") and an_anid='".$announcement_id."'");
					for($i=0;$i<count($posted_data['announcement_type']);$i++)
						{
						
						if($posted_data['announcement_type'][$i]==0)
						{
								/* This Announcement is for teacher user */	
								if(isset($posted_data['select_teacher']))
								{
							
								
									foreach($posted_data['select_teacher'] as $ks=>$vs)
									{
										$check_ann_user=array();
										$check_ann_user=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$vs."' and an_anid='".$announcement_id."' and  an_type='0'","fetch");
										if(empty($check_ann_user))
										{
											$ann_user=array();
											$ann_user=array('an_u_id'=>$vs,
													'an_anid'=>$announcement_id,
													'an_status'=>0,
													'an_type'=>0,
													'an_date'=>gmdate("Y-m-d H:i:s"),
											);	
											$kk=	$this->modelStatic->Super_Insert("announcement_user",$ann_user);
									
											/* Send Mail To User */	
											$select_subadmin=array();
											$select_subadmin=$this->modelStatic->Super_Get("users","user_id='".$vs."'","fetch");
											$message_alert_data=array(
											'rece_email'=>$select_subadmin['user_email'],
											'rece_first_name'=>$select_subadmin['user_first_name'].' '.$select_subadmin['user_last_name'],
											'msg'=>"You have a new announcement from '".$school_name."'. Log in to your account at <a href='".SITE_HTTP_URL."'>".SITE_NAME."</a> to view the announcement.",
											);
											$modelEmail->sendEmail('message_alert_notification',$message_alert_data);
											/* End Send Mail To User */	
										}
									}
								}
						}
						else if($posted_data['announcement_type'][$i]==1)
						{
								/* This Announcement is for student user */	
								if(isset($posted_data['select_student']))
								{
									
									foreach($posted_data['select_student'] as $ks=>$vs)
									{
										$check_ann_user=array();
										$check_ann_user=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$vs."' and an_anid='".$announcement_id."' and  an_type='1'","fetch");
										if(empty($check_ann_user))
										{
											$ann_user=array();
											$ann_user=array('an_u_id'=>$vs,
													'an_anid'=>$announcement_id,
													'an_status'=>0,
													'an_type'=>1,
													'an_date'=>gmdate("Y-m-d H:i:s"),
											);	
											$this->modelStatic->Super_Insert("announcement_user",$ann_user);
											/* Send Mail To User */	
											$select_student=array();
											$select_student=$this->modelStatic->Super_Get("users","user_id='".$vs."'","fetch");
											$message_alert_data=array(
											'rece_email'=>$select_student['user_email'],
											'rece_first_name'=>$select_student['user_first_name'].' '.$select_student['user_last_name'],
											'msg'=>"You have a new announcement from '".$school_name."'. Log in to your account at <a href='".SITE_HTTP_URL."'>".SITE_NAME."</a> to view the announcement.",
											);
											$modelEmail->sendEmail('message_alert_notification',$message_alert_data);
											/* End Send Mail To User */	
										}
								}   }
							}
						else if($posted_data['announcement_type'][$i]==2)
						{
								/* This Announcement is for family user */	
								if(isset($posted_data['select_family']))
								{
								
								
									foreach($posted_data['select_family'] as $ks=>$vs)
									{
										
										$check_ann_user=array();
										$check_ann_user=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$vs."' and an_anid='".$announcement_id."' and  an_type='2'","fetch");
										if(empty($check_ann_user))
										{
											$ann_user=array();
											$ann_user=array('an_u_id'=>$vs,
													'an_anid'=>$announcement_id,
													'an_status'=>0,
													'an_type'=>2,
													'an_date'=>gmdate("Y-m-d H:i:s"),
											);	
											$this->modelStatic->Super_Insert("announcement_user",$ann_user);
											/* Send Mail To User */	
											$select_family=array();
											$select_family=$this->modelStatic->Super_Get("users","user_id='".$vs."'","fetch");
											$message_alert_data=array(
											'rece_email'=>$select_family['user_email'],
											'rece_first_name'=>$select_family['user_first_name'].' '.$select_family['user_last_name'],
											'msg'=>"You have a new announcement from '".$school_name."'. Log in to your account at <a href='".SITE_HTTP_URL."'>".SITE_NAME."</a> to view the announcement.",
											);
											$modelEmail->sendEmail('message_alert_notification',$message_alert_data);
											/* End Send Mail To User */	
										}
								}   }
							}
						else if($posted_data['announcement_type'][$i]==3)
						{
								/* This Announcement is for Subadmin User */
								if(isset($posted_data['select_subadmin']))
								{
								
									foreach($posted_data['select_subadmin'] as $ks=>$vs)
									{
									
										$check_ann_user=array();
										$check_ann_user=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$vs."' and an_anid='".$announcement_id."' and  an_type='3'","fetch");
										if(empty($check_ann_user))
										{
											$ann_user=array();
											$ann_user=array('an_u_id'=>$vs,
													'an_anid'=>$announcement_id,
													'an_status'=>0,
													'an_type'=>3,
													'an_date'=>gmdate("Y-m-d H:i:s"),
											);	
											$this->modelStatic->Super_Insert("announcement_user",$ann_user);
											/* Send Mail To User */	
											$select_subadmin=array();
											$select_subadmin=$this->modelStatic->Super_Get("users","user_id='".$vs."'","fetch");
											$message_alert_data=array(
											'rece_email'=>$select_subadmin['user_email'],
											'rece_first_name'=>$select_subadmin['user_first_name'].' '.$select_subadmin['user_last_name'],
											'msg'=>"You have a new announcement from '".$school_name."'. Log in to your account at <a href='".SITE_HTTP_URL."'>".SITE_NAME."</a> to view the announcement.",
											);
											$modelEmail->sendEmail('message_alert_notification',$message_alert_data);
											/* End Send Mail To User */	
										}
									}	
								}
							}
						
						}
						$path =AN_PATH.'/file_image_'.$this->view->user->user_id.'/';	
						$files = scandir($path);
						//prd($files);
						$array=array();
			
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=$file;
								array_push($array,$newname);
								
								if(file_exists($path."/".$file))
								{
									
									rename($path."/".$file,AN_PATH."/".$file);
									echo "Here";
								}
								
								
								}
								}
						//prd($array);		
						foreach($array as $k=>$v)
						{
						
							$data=array('an_un_id'=>$announcement_id,
										'an_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("announce_attach",$data);
							//prd($n);
						}
						
						$objSession->successMsg="Announcement has been updated Successfully";
					}
					else
					{ 
					
							
							$data_insert1['announcement_status']='1';
							$data_insert1['announcement_desc']=$data_insert['announcement_desc'];
							$data_insert1['announcement_title']=$data_insert['announcement_title'];
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							// ================================
							
							// $data_insert1['announcement_date']=gmdate('Y-m-d H:i:s');
							$data_insert1['announcement_date']=date('Y-m-d H:i:s');
							$data_insert1['announcement_insertid']=$this->view->user->user_id;
							$data_insert1['announcement_schoolid']=$school_id;
							$k=$this->modelStatic->Super_Insert("announcement",$data_insert1); 
					
						for($i=0;$i<count($posted_data['announcement_type']);$i++)
						{
							
							//echo $k->inserted_id;
							if($posted_data['announcement_type'][$i]==0)
							{
								/* This Announcement is for teacher user */	
								if(isset($posted_data['select_teacher']))
								{
									foreach($posted_data['select_teacher'] as $ks=>$vs)
									{
										$ann_user=array();
										$ann_user=array('an_u_id'=>$vs,
													'an_anid'=>$k->inserted_id,
													'an_status'=>0,
													'an_type'=>0,
													'an_date'=>gmdate("Y-m-d H:i:s"),
										);	
										$this->modelStatic->Super_Insert("announcement_user",$ann_user);
									$select_tecaher_user=array();
									$select_tecaher_user=$this->modelStatic->Super_Get("users","user_id='".$vs."'","fetch");
									/* Send Mail To User */	
									$message_alert_data=array(
									'rece_email'=>$select_tecaher_user['user_email'],
									'rece_first_name'=>$select_tecaher_user['user_first_name'].' '.$select_tecaher_user['user_last_name'],
									'msg'=>"You have a new announcement from '".$school_name."'. Log in to your account at <a href='".SITE_HTTP_URL."'>".SITE_NAME."</a> to view the announcement.",
									);
									$modelEmail->sendEmail('message_alert_notification',$message_alert_data);
									
									/* End Send Mail To User */	
									
									}
								}
							}
							else if($posted_data['announcement_type'][$i]==1)
							{
								/* This Announcement is for student user */	
								if(isset($posted_data['select_student']))
								{
									foreach($posted_data['select_student'] as $ks=>$vs)
									{
										$ann_user=array();
										$ann_user=array('an_u_id'=>$vs,
													'an_anid'=>$k->inserted_id,
													'an_status'=>0,
													'an_type'=>1,
													'an_date'=>gmdate("Y-m-d H:i:s"),
										);	
										$this->modelStatic->Super_Insert("announcement_user",$ann_user);
									$select_student=array();
									$select_student=$this->modelStatic->Super_Get("users","user_id='".$vs."'","fetch");
									/* Send Mail To User */	
									$message_alert_data=array(
									'rece_email'=>$select_student['user_email'],
									'rece_first_name'=>$select_student['user_first_name'].' '.$select_student['user_last_name'],
									'msg'=>"You have a new announcement from '".$school_name."'. Log in to your account at <a href='".SITE_HTTP_URL."'>".SITE_NAME."</a> to view the announcement.",
									);
									$modelEmail->sendEmail('message_alert_notification',$message_alert_data);
									
									/* End Send Mail To User */	
								}   }
							}
							else if($posted_data['announcement_type'][$i]==2)
							{
								/* This Announcement is for family user */	
								if(isset($posted_data['select_family']))
								{
									foreach($posted_data['select_family'] as $ks=>$vs)
									{
										$ann_user=array();
										$ann_user=array('an_u_id'=>$vs,
													'an_anid'=>$k->inserted_id,
													'an_status'=>0,
													'an_type'=>2,
													'an_date'=>gmdate("Y-m-d H:i:s"),
										);	
										$this->modelStatic->Super_Insert("announcement_user",$ann_user);
										$select_family=array();
									$select_family=$this->modelStatic->Super_Get("users","user_id='".$vs."'","fetch");
									/* Send Mail To User */	
									$message_alert_data=array(
									'rece_email'=>$select_family['user_email'],
									'rece_first_name'=>$select_family['user_first_name'].' '.$select_family['user_last_name'],
									'msg'=>"You have a new announcement from '".$school_name."'. Log in to your account at <a href='".SITE_HTTP_URL."'>".SITE_NAME."</a> to view the announcement.",
									);
									$modelEmail->sendEmail('message_alert_notification',$message_alert_data);
									
									/* End Send Mail To User */	
								}   }
							}
							else if($posted_data['announcement_type'][$i]==3)
							{
								/* This Announcement is for Subadmin User */
								if(isset($posted_data['select_subadmin']))
								{
									foreach($posted_data['select_subadmin'] as $ks=>$vs)
									{
										$ann_user=array();
										$ann_user=array('an_u_id'=>$vs,
													'an_anid'=>$k->inserted_id,
													'an_status'=>0,
													'an_type'=>3,
													'an_date'=>gmdate("Y-m-d H:i:s"),
										);	
										$this->modelStatic->Super_Insert("announcement_user",$ann_user);
									$select_subadmin=array();
									$select_subadmin=$this->modelStatic->Super_Get("users","user_id='".$vs."'","fetch");
									/* Send Mail To User */	
									$message_alert_data=array(
									'rece_email'=>$select_subadmin['user_email'],
									'rece_first_name'=>$select_subadmin['user_first_name'].' '.$select_subadmin['user_last_name'],
									'msg'=>"You have a new announcement from '".$school_name."'. Log in to your account at <a href='".SITE_HTTP_URL."'>".SITE_NAME."</a> to view the announcement.",
									);
									$modelEmail->sendEmail('message_alert_notification',$message_alert_data);
									
									/* End Send Mail To User */	
									}	
								}
							}
							$path =AN_PATH.'/file_image_'.$this->view->user->user_id.'/';	
							$files = scandir($path);
							
							$array=array();
					
							foreach ($files as $file) 
							{
									if($file!='.' && $file!='..' && ((strpos($file,"."))))
									{ 
									$newname=$file;
									array_push($array,$newname);
									
									if(file_exists($path."/".$file))
									{
										rename($path."/".$file,AN_PATH."/".$file);
									}
									
									
									}
									}
								
							foreach($array as $key=>$v)
							{
							
								$data=array('an_un_id'=>$k->inserted_id,
											'an_name' => $v,
										
								);	
								$n=$this->modelStatic->Super_Insert("announce_attach",$data);
								
							}
						}
					
						$objSession->successMsg="Announcement has been added Successfully";
					}
					$this->redirect("profile/viewannouncement");
					
				}
			}
			else
			{
				if(is_dir(AN_PATH.'/file_image_'.$this->view->user->user_id))
				{
					DeleteDirfileupload(AN_PATH.'/file_image_'.$this->view->user->user_id);
				}
			}
			
	}
	/* Student dashboard */
	public function studentdashboardAction()
	{
		global $objSession ; 	
		$modelSchool = new Application_Model_SchoolModel();
		$student_id=$this->getRequest()->getParam('student_id');
		$this->view->student_id=$student_id;
		$announcement_arr=array();
		/* Get Announcement Data */
		if(!isset($student_id))
		{
			$announcement_arr=array();
			$announcement_arr=$modelSchool->getuserannouncement($this->view->user);
			
		}
		else
		{ 
			$student_data=array();
			$student_data=$this->modelStatic->Super_Get("users","user_id='".$student_id."'","fetch");
			$this->view->student_data=$student_data;
			$announcement_arr=array();
			$announcement_arr=$modelSchool->getuserannouncement((object)$student_data);
			$this->view->student_id=$student_id;
			$last_login_arr=array();
			$last_login_arr=array('user_last_login'=>date("Y-m-d"));
			$this->modelStatic->Super_Insert("users",$last_login_arr,"user_id='".$student_id."'");

		}
		$this->view->announcement_arr=$announcement_arr;
		$private_teacher_arr=array();
		/* Get All Private Teachers Of Logged In Student */
		$joinArr=array(
			'0'=> array(
			'0'=>'users',
			'1'=>'users.user_id =private_teacher.private_teacher_teacherid',
			'2'=>'Left',
			'3'=>array('user_id','user_first_name','user_last_name')
			),
			);
		if(isset($student_id) && !empty($student_id))
		{
			$private_teacher_arr=$this->modelStatic->Super_Get("private_teacher","private_teacher_studentid='".$student_id."'","fetchAll",array(),$joinArr);
		}
		else
		{
			$private_teacher_arr=$this->modelStatic->Super_Get("private_teacher","private_teacher_studentid='".$this->view->user->user_id."'","fetchAll",array(),$joinArr);	
		}
		
		$this->view->private_teacher_arr=$private_teacher_arr;
		
	}
	/* family dashboard */
	public function familydashboardAction()
	{
	
		global $objSession;	
		$modelSchool = new Application_Model_SchoolModel();
		$student_arr=array();
		/* Get All Student Data */
		$joinArr=array(
			'0'=> array(
			'0'=>'users',
			'1'=>'users.user_id =student_family.s_f_sid',
			'2'=>'Left',
			'3'=>array('user_id','user_first_name','user_last_name','')
			),
		);
		 $family_students_user=$this->modelStatic->Super_Get("student_family","s_f_fid='".$this->view->user->user_id."' and user_status='1'","fetchAll",array(),$joinArr);
		$student_arr=$this->modelStatic->Super_Get("users","user_student_family='".$this->view->user->user_id."'","fetchAll");
			
		$this->view->family_students_user=$family_students_user;
		$announcement_arr=array();
		/* Get Announcement Data */
		if(!isset($student_id))
		{
			$announcement_arr=array();
			$announcement_arr=$modelSchool->getuserannouncement($this->view->user);
		}
		$this->view->announcement_arr=$announcement_arr;
		$form = new Application_Form_SchoolForm();
		
		 $family_students=$this->modelStatic->Super_Get("users","user_student_family='".$this->view->user->user_id."' and user_type='student'","fetchAll");
	
		 $family_optn=array();
		 foreach($family_students_user as $k=>$v)
		 {
				$family_optn[$v['user_id']]=$v['user_id'];
		 }
	
			
	
		 $form->getfamilystudent();
		 if(!empty($family_optn))
		 {
		 $form->populate($family_optn);
		 }
	
		 // prd($family_optn);
		 $this->view->form=$form;
		 if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // If form is valid
					$data_insert=$form->getValues();
					/* Set All student's family id to empty */
					$array_val=array('user_student_family'=>"");
				/*	$kk=$this->modelStatic->Super_Insert("users",$array_val,"user_student_family='".$this->view->user->user_id."' and user_school_id='".$this->view->user->user_school_id."'");*/
					$this->modelStatic->Super_Delete("student_family","s_f_fid='".$this->view->user->user_id."'");
					//prd($data_insert['family_student']);
					foreach($data_insert['family_student'] as $k=>$v)
					{
							$array_val_family=array();
							$array_val_family=array('s_f_fid'=>$this->view->user->user_id,'s_f_sid'=>$v,'s_f_date'=>gmdate("Y-m-d H:i:s"));
							$this->modelStatic->Super_Insert("student_family",$array_val_family);
							
					}
					$objSession->successMsg="Student has been updated successfully";
					$this->redirect("profile/familydashboard");
				}
			}
	}
	/* Get All Students of family using Ajax */
	public function getfamilystudentsAction()
	{
		 global $objSession;	
		 $this->_helper->layout->disableLayout();
		 $form = new Application_Form_SchoolForm();
		 $family_students=$this->modelStatic->Super_Get("users","user_student_family='".$this->view->user->user_id."' and user_type='student'","fetchAll");
		 $family_optn=array();
		 foreach($family_students as $k=>$v)
		 {
				$family_optn[$k]=$v['user_id'];
		 }
		
		 $form->getfamilystudent();
		 $form->family_student->setValue($family_optn);
		 // prd($family_optn);
		 $this->view->form=$form;
		 if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // If form is valid
					//prd($posted_data);
				}
			}
		 
		 
	}
	/* Add New Subadmin */
	public function newsubadminAction()
	{
		global $objSession ; 
		$admin_data=array();
		$user_id=$this->getRequest()->getParam('user_id');
		$form = new Application_Form_SchoolForm();
		
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='1'","fetch");	
				
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
		if(isset($user_id) && !empty($user_id))
		{
			$form->subadmin($user_id);
			$this->view->pageHeading = "Update Admin";
			$this->view->pageHeadingshow = '<i class="fa fa-user"></i>  Update Admin';
			/* Get Announcement Data */
			$admin_data=$this->modelStatic->Super_Get("users","user_id='".$user_id."'","fetch");	
			$permissions_data=$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$user_id."'","fetchAll");
			$perm_arr=array();
			foreach($permissions_data as $k=>$v)
			{
				$perm_arr[$k]=$v['admin_permission_type'];
			}
			$admin_data['admin_permission']=$perm_arr;
			$form->populate($admin_data);
			
		}
		else
		{
			$form->subadmin();
			$this->view->pageHeading = "Add New Admin";
			$this->view->pageHeadingshow = '<i class="fa fa-user"></i>  Add New Admin';	
		}		
		$this->view->form=$form;
			if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // If form is valid
					$permissions_arr=array();
					$data_insert=$form->getValues();
					$permissions_arr=$data_insert['admin_permission'];
					unset($data_insert['admin_permission']);
					if(isset($admin_data) && !empty($admin_data))
					{
						$gg=$this->modelStatic->Super_Insert("users",$data_insert,'user_id="'.$user_id.'"');
						
						$objSession->successMsg="Admin has been updated Successfully";
					}
					else
					{
						$modelEmail = new Application_Model_Email();
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
						
						/* Add Admin Data */
						//$password=randomPassword();						
						$password=12345;	
						$data_insert['user_password']=md5($password);	
						$data_insert['user_password_text']=$password;
						$data_insert['user_school_id']=$school_id;
						$data_insert['user_created']=gmdate('Y-m-d H:i');
						$data_insert['user_type']="schoolsubadmin";
						$data_insert['user_school_name']=$this->view->user->user_school_name ;
						$data_insert['user_insertby']=$this->view->user->user_id;
						$data_insert['user_verification_mail']=0;
						$user_name='';
						$user_name=$this->modelStatic->insertusername('schoolsubadmin');
						$data_insert['user_username']=$user_name;
						$is_insert=$this->modelStatic->Super_Insert("users",$data_insert);
						$user_id=$is_insert->inserted_id;
						$data_user=array();
						$data_user=$data_insert;
						$data_user['user_password']=$password;
						$objSession->successMsg = "Admin has been added Successfully";
						$reset_password_key = md5($user_id."!@#$%^$%&(*_+".time());						
						$data_to_update1 = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key);						
						$this->modelStatic->Super_Insert("users",$data_to_update1,'user_id="'.$user_id.'"');					
						$data_user['pass_resetkey'] = $reset_password_key ;
						$data_user['user_reset_status'] = "1" ;
						$data_user['last_inserted_id'] = $user_id ;
						$data_user['school_name'] = $this->view->user->user_school_name ;
						$data_user['user_position']= "Sub Admin";
						if($data_user['user_email']!='')
						{
						$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user);
						}
					}
					/* Remove all already exists Permissions */
					$this->modelStatic->Super_Delete("admin_permissions",'admin_permissions_adminid="'.$user_id.'"');
					/* Insert Permission Of Admin */
					foreach($permissions_arr as $k=>$v)
					{
						$ins_array=array();
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						// ================================
						
						$ins_array=array(
								'admin_permission_type'=>$v,
								'admin_permissions_adminid'=>$user_id,
								// 'admin_permission_date'=>gmdate('Y-m-d H:i:s'),								
								'admin_permission_date'=>date('Y-m-d H:i:s'),								
								);	
					$this->modelStatic->Super_Insert("admin_permissions",$ins_array);		
					}
					$this->redirect("profile/viewsubadmin");
					
				}
			}
		
			
	}
	
	/* View All Sub Admin */
	public function viewsubadminAction()
	{
		global $objSession ; 
		$this->view->pageHeading = "View All Sub Admin";
		$this->view->pageHeadingshow = '<i class="fa fa-users"></i>  View All Sub Admin';
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='1'","fetch");	
				
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
		$this->_helper->viewRenderer->setNoRender(true);
		$modelEmail = new Application_Model_Email();
 		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			if(isset($formData['select_users']) and count($formData['select_users'])){
				 foreach($formData['select_users'] as $key=>$values){
					 $check_data=array();
					 $check_data=$this->modelStatic->Super_Get("users","user_id='".$values."'","fetch");
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
				 }
 				$objSession->successMsg = "Verification mail has been send Successfully ";
 			}else{
				$objSession->errorMsg = "Invalid Request to Send Mail User(s) ";
			}
 			$this->_redirect('profile/viewsubadmin');
		} 
	}
		public function removesubadminAction()
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
 			$this->_redirect('profile/viewsubadmin');
		} 
		
	/*	$user_id=$this->getRequest()->getParam("idval");	
		$this->modelStatic->Super_Delete("users","user_id='".$user_id."'");
		$objSession->successMsg = "Student has been removed Successfully";				
		$this->_redirect('student');*/
	}
		/* Get All Sub Admin */
	public function getsubadminAction()
	{
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'user_id',
			'user_first_name',
			'user_last_name',
			'user_email',
			'user_school_id',
			'user_type',
			'user_created',
			'user_status',
			'user_verification_mail',
			'user_email_verified'
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
		
				$sOrder = "ORDER BY user_last_name ASC";
		
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
					$sWhere = "WHERE (user_type='schoolsubadmin' and (user_insertby='".$this->view->user->user_id."' or user_school_id='".$this->view->user->user_school_id."'   or user_school_id='".$this->view->user->user_id."' ))";
			}
			else
			{
					$sWhere .= " AND (user_type='schoolsubadmin'  and ( user_insertby='".$this->view->user->user_id."' or user_school_id='".$this->view->user->user_school_id."'   or user_school_id='".$this->view->user->user_id."' ) )";
			}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." FROM $sTable $sWhere $sOrder $sLimit";
		//echo $sQuery;die;
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
			$row[]=$row1['user_email'];
			$row[]=date('F , Y h:i A',strtotime($row1['user_created']));
			$row[]='<a href="'.APPLICATION_URL.'/profile/newsubadmin/user_id/'.$row1['user_id'].'"><i class="fa fa-edit"></i></a>';
			/*$row[]='<a onclick="removeclass('.$row1['user_id'].')"<i class="fa fa-trash-o"></i></a>';*/
			 $mail_sent='';
			if($row1['user_verification_mail']==1)
			{
				$mail_sent="<b>Mail Sent</b>";	
			}
			$row[]='<input type="checkbox"  value="'.$row1['user_id'].'"   name="select_users[]" id="select_user_'.$row1['user_id'].'"  /> &nbsp;'.$mail_sent;
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
			if($row1['user_email']!='')
			{
			$row[]=$verification_status;
			}
			else
			{
				$row[]=' N/A';	
			}
				
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	/* View All Announcement */
	public function viewannouncementAction()
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
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='7'","fetch");	
				
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
		$this->view->pageHeading = "View All Announcements";
		$this->view->pageHeadingshow = '<i class="fa fa-bell"></i>  View All Announcements';
		
	}
	
	/* Get All Teachers */
	public function getteachersAction()
	{
		$this->dbObj = Zend_Registry::get('db');
	
		$type = $this->_getParam('type');
		$an_anid=$this->_getParam('an_anid');
 		$aColumns = array(
			'user_id',
			'user_first_name',
			'user_last_name',
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
		$sOrder = "ORDER BY user_last_name ASC";
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
		
			$school_id=$this->view->user->user_id;
			if($this->view->user->user_type!='school')
			{
				$school_id=$this->view->user->user_school_id;	
			}
		if ( $sWhere == "" )
				{
					$sWhere = "WHERE user_school_id='".$school_id."' and user_type='".$type."'";
				}
				else
				{
					$sWhere .= " AND user_school_id='".$school_id."' and user_type='".$type."'";
				}
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)) ." FROM $sTable $sWhere $sOrder $sLimit";
		///echo $sQuery;die;
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
			if($type=='teacher')
			{
				$check_tecaher=array();
				if(isset($an_anid) && $an_anid!='' )
				{
					$check_tecaher=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$row1['user_id']."' and an_anid='".$an_anid."'","fetch");	
				}
				if(!empty($check_tecaher))
				{
					$row[] = "<input type='checkbox' name='select_teacher[]' id='teacher_".$row1['user_id']."' value='".$row1['user_id']."' checked='checked' />";	
				}
				else
				{ 
					$row[] = "<input type='checkbox' name='select_teacher[]' id='teacher_".$row1['user_id']."' value='".$row1['user_id']."' />";	
			    }
			}
			else if($type=='student')
			{
				$check_student=array();
				if(isset($an_anid) && $an_anid!='' )
				{
					$check_student=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$row1['user_id']."' and an_anid='".$an_anid."'","fetch");	
				}
				if(!empty($check_student))
				{
					$row[] = "<input type='checkbox' name='select_student[]' id='student_".$row1['user_id']."' value='".$row1['user_id']."' checked='checked' />";	
				}
				else
				{
					$row[] = "<input type='checkbox' name='select_student[]' id='student_".$row1['user_id']."' value='".$row1['user_id']."'  />";	
				}
			}
			else if($type=='family')
			{
				$check_family=array();
				if(isset($an_anid) && $an_anid!='' )
				{
					$check_family=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$row1['user_id']."' and an_anid='".$an_anid."'","fetch");	
				}
				if(!empty($check_family))
				{
					$row[] = "<input type='checkbox' name='select_family[]' id='family_".$row1['user_id']."' value='".$row1['user_id']."' checked='checked' />";	
				}
				else
				{
					$row[] = "<input type='checkbox' name='select_family[]' id='family_".$row1['user_id']."' value='".$row1['user_id']."' />";	
				}
			}
			else
			{
				$check_subadmin=array();
				if(isset($an_anid) && $an_anid!='' )
				{
					$check_subadmin=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$row1['user_id']."' and an_anid='".$an_anid."'","fetch");	
				}
				if(!empty($check_subadmin))
				{
					$row[] = "<input type='checkbox' name='select_subadmin[]' id='subadmin_".$row1['user_id']."' value='".$row1['user_id']."' checked='checked' />";	
				}
				else
				{
					$row[] = "<input type='checkbox' name='select_subadmin[]' id='subadmin_".$row1['user_id']."' value='".$row1['user_id']."' />";		
				}
			}
			
			$row[]=$row1['user_last_name'];
			$row[]=$row1['user_first_name'];
			
 			$output['aaData'][] = $row;
			$j++;
		$i++;

		}
		
		echo json_encode( $output );
		exit();
  	}
	
		/* Get All Announcement */
	public function getannouncementAction()
	{
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'announcement_id',
			'announcement_title',
			'announcement_desc',
			'announcement_date',
			'announcement_type',
			
		);
		$sIndexColumn = 'announcement_id';
		$sTable = 'announcement';
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
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
			$sWhere = "WHERE (announcement_insertid='".$this->view->user->user_id."' or announcement_schoolid='".$this->view->user->user_school_id."' or  announcement_schoolid='".$this->view->user->user_id."')";
			}
			else
			{
			$sWhere .= " AND (announcement_insertid='".$this->view->user->user_id."'  or announcement_schoolid='".$this->view->user->user_school_id."' or or  announcement_schoolid='".$this->view->user->user_id."' )";
			}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." FROM $sTable $sWhere $sOrder $sLimit";
		///echo $sQuery;die;
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
			$row[] = $i;
			$row[]=$row1['announcement_title'];
			$row[]=$row1['announcement_desc'];
			$row[]=date('F , Y h:i A',strtotime($row1['announcement_date']));
			$row[]='<a href="'.APPLICATION_URL.'/profile/newannouncement/announcement_id/'.$row1['announcement_id'].'"><i class="fa fa-edit"></i></a>';
			$row[]='<a onclick="removeclass('.$row1['announcement_id'].')" ><i class="fa fa-trash-o"></i></a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	/* Remove Announcement */
	public function removeannouncementAction()
	{
		global $objSession ; 
		$announcement_id=$this->getRequest()->getParam('announcement_id');	
		$this->modelStatic->Super_Delete("announcement",'announcement_id="'.$announcement_id.'"');
		$objSession->successMsg="Announcement has been removed Successfully";		
		$this->redirect("profile/viewannouncement");
	}
	
	/* Remove Subadmin */

	public function teacherdashboardAction()
	{
 		global $objSession ; 
   		$content = $this->modelStatic->getPage(40); 
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$check_tecaher=array();
		if(isset($teacher_id) && !empty($teacher_id))
		{
			
			$check_tecaher=$this->modelStatic->Super_Get("users","user_type='teacher' and user_id='".$teacher_id."' and user_school_id='".$this->view->user->user_id."'","fetch");	if(empty($check_tecaher))
			{
				$objSession->errorMsg="Inavlid request for user";
				$this->redirect("profile/lessondashboard");
				
			}
		}
		$this->view->check_tecaher=$check_tecaher;
		$modelSchool = new Application_Model_SchoolModel();
		$this->view->show = "front_dashboard" ; 
		$joinArr=array(
			'0'=> array(
			'0'=>'users as student',
			'1'=>'student.user_id =private_teacher.private_teacher_studentid',
			'2'=>'Left',
			'3'=>array('student.user_first_name','student.user_last_name')
			),
		);
		$user_id_param=$this->view->user->user_id;
		
		if(isset($teacher_id))
		{
			$user_id_param=$teacher_id;
		}
		$user_param_data=array();
		$user_param_data=$this->modelStatic->Super_Get("users","user_id='".$user_id_param."'","fetch"); 
		$this->view->user_param_data=$user_param_data;
		$students=$this->modelStatic->Super_Get("private_teacher","private_teacher_teacherid='".$user_id_param."'","fetchAll",array("order"=>array("student.user_last_name","student.user_first_name")),$joinArr);
		$this->view->students=$students;
		
		$private_uids=array();
		$private_uids=$this->modelStatic->Super_Get("private_teacher","private_teacher_teacherid='".$user_id_param."'","fetch",array("fields"=>array("GROUP_CONCAT(private_teacher_studentid) as uid")));
		
		$allStudents=array();
		if($private_uids['uid']!=''){
			$allStudents=$this->modelStatic->Super_Get("users","user_type='student' and user_school_id='".$user_param_data['user_school_id']."' and user_id NOT IN(".$private_uids['uid'].")","fetchAll",array("fields"=>array("user_first_name","user_last_name","user_id")));	
		}
		else{
			$allStudents=$this->modelStatic->Super_Get("users","user_type='student' and user_school_id='".$user_param_data['user_school_id']."'","fetchAll",array("fields"=>array("user_first_name","user_last_name")));	
		}
		
		$this->view->allStudents=$allStudents;
		/* Get All Images upload by Teacher */
		$teacher_attach_images=array();
		$teacher_attach_images=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$user_id_param."' and teacher_attach_type='0'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as images')));
		$this->view->teacher_attach_images=$teacher_attach_images;
		/* Get All Documents upload by Teacher */
		$teacher_attach_documents=array();
		$teacher_attach_documents=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$user_id_param."' and teacher_attach_type='1'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as documents'),"order"=>array("teacher_attach_name")));
		$this->view->teacher_attach_documents=$teacher_attach_documents;
		/* Get All Pdf upload by Teacher */
		$teacher_attach_pdf=array();
		$teacher_attach_pdf=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$user_id_param."' and teacher_attach_type='2'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as pdf')));
		$this->view->teacher_attach_pdf=$teacher_attach_pdf;
		/* Get All Videos uploades by Teacher */
		$teacher_attach_video=array();
		$teacher_attach_video=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$user_id_param."' and teacher_attach_type='3'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as videos')));
		$this->view->teacher_attach_video=$teacher_attach_video;
		/* Get Latest Announcement Data */
		
		$announcement_arr=array();
		if(!isset($teacher_id))
		{
			$announcement_arr=array();
			$announcement_arr=$modelSchool->getuserannouncement($this->view->user);
		}
		/* Get All Attachments */
		$all_attachments=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$user_id_param."'","fetchAll",array("order"=>array("teacher_attach_filename ASC")));
		$this->view->all_attachments=$all_attachments;
		$this->view->announcement_arr=$announcement_arr;
		
		/* Get All Classes of teacher */
		
		$all_classes=array();
		$all_classes=$modelSchool->getteacherclass1($user_id_param);
		$this->view->all_classes=$all_classes;
		
		
		
		
	}
	
	
	public function addtoroseterAction(){
		global $objSession ; 	
		$modelEmail = new Application_Model_Email();
	
		if(isset($_POST['stu_roster']) && $_POST['stu_roster']!=''){
			/*$studata=array();
			$studata=$this->modelStatic->Super_Get("users","user_id='".$_POST['stu_roster']."'","fetch",array("fields"=>array("user_first_name","user_last_name","user_id")));
			$this->view->studata=$studata;*/
			$teacherdata=array();
			$teacherdata=$this->modelStatic->Super_Get("users","user_id='".$_POST['teacher_name']."'","fetch",array("fields"=>array("user_first_name","user_last_name","user_id","user_school_id")));
			$this->view->teacherdata=$teacherdata;
			
			$schooldData=$this->modelStatic->Super_Get("users","user_id='".$teacherdata['user_school_id']."'","fetch");
			
			if(!empty($schooldData)){
				$adminEmail=$schooldData['user_email'];
				
			}
			$mailData=array();
			$mailData=array(
					'teacher_name'=>$teacherdata['user_first_name'].' '.$teacherdata['user_last_name'],
					'student_name'=>$_POST['stu_roster'],
					'school_admin_email'=>$adminEmail
					
				);
			$modelEmail->sendEmail("add_stu_roster",$mailData);
			$objSession->successMsg="Mail has been sent to admin";
		}
		else{
				$objSession->successMsg="Invalid Data to send";
		}
		$this->redirect('/profile/teacherdashboard');
	}
	
	public function removefromroseterAction(){
		global $objSession ; 	
		$modelEmail = new Application_Model_Email();
		if(isset($_POST['stu_roster']) && $_POST['stu_roster']!=''){
			$studata=array();
			$studata=$this->modelStatic->Super_Get("users","user_id='".$_POST['stu_roster']."'","fetch",array("fields"=>array("user_first_name","user_last_name","user_id")));
			$this->view->studata=$studata;
			$teacherdata=array();
			$teacherdata=$this->modelStatic->Super_Get("users","user_id='".$_POST['teacher_name']."'","fetch",array("fields"=>array("user_first_name","user_last_name","user_id","user_school_id")));
			$this->view->teacherdata=$teacherdata;
			$schooldData=$this->modelStatic->Super_Get("users","user_id='".$teacherdata['user_school_id']."'","fetch");
			if(!empty($schooldData)){
				$adminEmail=$schooldData['user_email'];
				
			}
			$mailData=array();
			$mailData=array(
					'teacher_name'=>$teacherdata['user_first_name'].' '.$teacherdata['user_last_name'],
					'student_name'=>$studata['user_first_name'].' '.$studata['user_last_name'],
					'school_admin_email'=>$adminEmail
				);
				$modelEmail->sendEmail("remove_stu_roster",$mailData);
				$objSession->successMsg="Mail has been sent to admin";
		}
		else
		{
				$objSession->successMsg="Invalid Data to send";
		}
		$this->redirect('/profile/teacherdashboard');
	}
	
	
	public function getstudentsnameAction()
	{
		$this->_helper->layout->disableLayout();
		$schoolModel = new Application_Model_SchoolModel();
		$param=$this->getRequest()->getParam('valname');
		$get_users=$schoolModel->getstudentall($param);
		$status=$this->getRequest()->getParam('status');
		$this->view->status=$status;
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$this->view->teacher_id=$teacher_id;
		$this->view->get_users=$get_users;
		
	}
	
	public function getstudentsnamebyadminAction()
	{
			global $objSession ; 
			$param=$this->getRequest()->getParam('valname');
			$this->_helper->layout->disableLayout();
			$get_students=array();
			$where=" (user_first_name LIKE '%".$param."%' or user_last_name LIKE '%".$param."%') and user_school_id='".$this->view->user->user_id."' and user_status='1' and user_type='student'";	
			if($param!='')
			{
			$get_students=$this->modelStatic->Super_Get("users",$where,"fetchAll");
			}
			$this->view->get_students=$get_students;
		
	}
	
	public function getclassesallAction()
	{
			$this->_helper->layout->disableLayout();
			$schoolModel = new Application_Model_SchoolModel();
			$param=$this->getRequest()->getParam('valname');
			$get_classes=$schoolModel->getclassesall($param);
			$this->view->all_classes=$get_classes;
	}
	
	public function getclassesallbyadminAction()
	{
			$this->_helper->layout->disableLayout();
			$param=$this->getRequest()->getParam('valname');
			$where=" class_name LIKE '%".$param."%' and class_school_id='".$this->view->user->user_id."' ";
			$get_classes=array();
			if($param!='')
			{
				$get_classes=$this->modelStatic->Super_Get("Classes",$where,"fetchAll");
				$this->view->all_classes=$get_classes;
			}
	}
	
	public function lessondahboardAction()
	{
 		global $objSession ; 
   		$content = $this->modelStatic->getPage(40); 
		$modelSchool = new Application_Model_SchoolModel();
		$this->view->show = "front_dashboard" ; 
		/* Get All Students users */
		$students=$this->modelStatic->Super_Get("users","user_school_id='".$this->view->user->user_id."' and user_type='student' and user_status='1'","fetchAll",array("order"=>array("user_first_name","user_last_name")));
		$this->view->students=$students;
		/* Get All Students End */
		/* Get All Teachers Users */
		$teachers =$this->modelStatic->Super_Get("users","user_school_id='".$this->view->user->user_id."' and user_type='teacher' and user_status='1' ","fetchAll",array("order"=>array("user_first_name","user_last_name")));
		$this->view->teachers=$teachers;
		
		/* Get All Teachers End */
		
		/* Get All Images upload by Teacher */
		$teacher_attach_images=array();
		$teacher_attach_images=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."' and teacher_attach_type='0'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as images')));
		$this->view->teacher_attach_images=$teacher_attach_images;
		/* Get All Documents upload by Teacher */
		$teacher_attach_documents=array();
		$teacher_attach_documents=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."' and teacher_attach_type='1'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as documents')));
		$this->view->teacher_attach_documents=$teacher_attach_documents;
		/* Get All Pdf upload by Teacher */
		$teacher_attach_pdf=array();
		$teacher_attach_pdf=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."' and teacher_attach_type='2'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as pdf')));
		$this->view->teacher_attach_pdf=$teacher_attach_pdf;
		/* Get All Videos uploades by Teacher */
		$teacher_attach_video=array();
		$teacher_attach_video=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."' and teacher_attach_type='3'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as videos')));
		$this->view->teacher_attach_video=$teacher_attach_video;
	
		
		
		/* Get All Attachments */
		$all_attachments=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."'","fetchAll",array("order"=>array("teacher_attach_name ASC")));
	$this->view->all_attachments=$all_attachments;
	
		
		/* Get All Classes of teacher */
		$all_classes=array();
		$all_classes=$modelSchool->getschoolteacherclass();
		$this->view->all_classes=$all_classes;
		
		
		
	}
	
	public function templatedashboardAction()
	{
			
 		global $objSession ; 
   		$content = $this->modelStatic->getPage(40); 
		$modelSchool = new Application_Model_SchoolModel();
		$this->view->show = "front_dashboard" ; 
		/* Get All Students users */
		$students=$this->modelStatic->Super_Get("users","user_school_id='".$this->view->user->user_id."' and user_type='student' and user_status='1'","fetchAll",array("order"=>array("user_first_name","user_last_name")));
		$this->view->students=$students;
		/* Get All Students End */
		/* Get All Teachers Users */
		$teachers =$this->modelStatic->Super_Get("users","user_school_id='".$this->view->user->user_id."' and user_type='teacher' and user_status='1' ","fetchAll",array("order"=>array("user_first_name","user_last_name")));
		$this->view->teachers=$teachers;
		
		/* Get All Teachers End */
		
		/* Get All Images upload by Teacher */
		$teacher_attach_images=array();
		$teacher_attach_images=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."' and teacher_attach_type='0'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as images')));
		$this->view->teacher_attach_images=$teacher_attach_images;
		/* Get All Documents upload by Teacher */
		$teacher_attach_documents=array();
		$teacher_attach_documents=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."' and teacher_attach_type='1'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as documents')));
		$this->view->teacher_attach_documents=$teacher_attach_documents;
		/* Get All Pdf upload by Teacher */
		$teacher_attach_pdf=array();
		$teacher_attach_pdf=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."' and teacher_attach_type='2'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as pdf')));
		$this->view->teacher_attach_pdf=$teacher_attach_pdf;
		/* Get All Videos uploades by Teacher */
		$teacher_attach_video=array();
		$teacher_attach_video=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."' and teacher_attach_type='3'","fetch",array("fields"=>array('teacher_attach_name','COUNT(teacher_attach_name) as videos')));
		$this->view->teacher_attach_video=$teacher_attach_video;
	
		
		
		/* Get All Attachments */
		$all_attachments=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_schoolid='".$this->view->user->user_id."'","fetchAll");
	$this->view->all_attachments=$all_attachments;
	
		
		/* Get All Classes of teacher */
		$all_classes=array();
		$all_classes=$modelSchool->getschoolteacherclass();
		$this->view->all_classes=$all_classes;
		
		
		
	
			
	}
	
	public function uploadpathAction()
	{
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$options = array();
		//prd(HTTP_UPLOADS_PATH);
		if(isset($_GET['file']) && $_GET['file'] != ""){
		
		
		}
		$options['script_url'] = SITE_HTTP_URL."/profile/uploadpath";
		$path=ROOT_PATH."/public/resources/announce_attach/file_image_".$this->view->user->user_id."/";
		//prd($path);
		if(!is_dir($path))
		{
			mkdir($path,0777);
		}
		$options['upload_dir'] = $path;
		$options['upload_url'] = SITE_HTTP_URL."/public/resources/announce_attach/file_image_".$this->view->user->user_id."/";
		$imageUpload = new Application_Plugin_UploadHandler($options);
		//exit;
	}
	
	public function subscriptionAction()
	{
 		
		global $objSession;
		$sd_id =  $this->_getParam("sd_id");
		$form = new  Application_Form_User();
		$form->CreditCardFields();
		$this->view->form=$form;
		$plan_detail=$this->modelStatic->Super_Get("subscription_plan",'1',"fetch");
		$this->view->plan_detail=$plan_detail;
		$subscribad_plan=array();
		$subscribad_plan=$this->modelStatic->Super_Get("subscription",'subscription_user_id="'.$this->view->user->user_id.'"',"fetch");
		$this->view->subscribad_plan=$subscribad_plan;
		$get_all_plans=$this->modelStatic->Super_Get("subscription_plan","1","fetchAll");
		$this->view->get_all_plans=$get_all_plans;
	}
	public function plansubscriptionAction()
	{
		global $objSession;
		$sd_id =  $this->_getParam("sd_id");
		$suscr_id=$this->getRequest()->getParam('suscr_id');
		$form = new  Application_Form_User();
		$form->CreditCardFields();
		$form->populate((array)$this->view->user);
		$this->view->form=$form;
		$plan_detail=$this->modelStatic->Super_Get("subscription_plan",'subscription_plan_id="'.$suscr_id.'"',"fetch");
		$subscribad_plan=array();
		$subscribad_plan=$this->modelStatic->Super_Get("subscription",'subscription_user_id="'.$this->view->user->user_id.'"',"fetch");
		if(!empty($subscribad_plan) && $subscribad_plan['subscription_planid']==$suscr_id)
		{
				$objSession->successMsg ="You have alrady subscribed for this plan.";
				$this->_redirect("profile/subscription");
		}
		$this->view->plan_detail=$plan_detail;
		if($this->getRequest()->isPost())
		{
 			$data_post = $this->getRequest()->getPost();
			if($form->isValid($data_post))
			{
				
			$data_val=$form->getValues();
			$objPayapal=new Application_Model_Paypalrecurring();	
			$data_val['amount']=$plan_detail['subscription_plan_price'];	
			$data_val['sd_id']=$plan_detail['subscription_plan_id'];
			$data_val['startDate']=gmdate("Y-m-d H:i:s");
			$request_data=$_POST;
			$isSubscribed = $objPayapal->CreateRecurringPaymentsProfile($data_val);
			if($isSubscribed['RESPMSG']=='Approved')
			{
			// ================ add  ==========
			date_default_timezone_set('America/Los_Angeles');	// PDT time
			// ================================
			
			$data_subscription=array(
			'subscription_user_id'=>$this->view->user->user_id,
			'subscription_planid'=>$plan_detail['subscription_plan_id'],
			'subscription_plantitle'=>$plan_detail['subscription_plan_title'],
			'subscription_planprice'=>$plan_detail['subscription_plan_price'],
			// 'subscription_start_date'=>gmdate('Y-m-d H:i:s'),
			'subscription_start_date'=>date('Y-m-d H:i:s'),
			'subscription_plandes'=>$plan_detail['subscription_plan_description'],
			'subscription_profile_id'=>$isSubscribed['PROFILEID'],
			'subscription_userfirstname'=>$data_val['user_first_name'],
			'subscription_userlastname'=>$data_val['user_last_name']
			);
			if(empty($subscribad_plan))
			{
				$this->modelStatic->Super_Insert("subscription",$data_subscription);
			}
			else
			{
				$this->modelStatic->Super_Insert("subscription",$data_subscription,"subscription_id='".$subscribad_plan['subscription_id']."'");	
			}
			$modelEmail=new Application_Model_Email();	
			$data_subscription['user_email']=$this->view->user->user_email;
			$is_send =  $modelEmail->sendEmail("email_subscription",$data_subscription);
			$objSession->successMsg ="Your Subscription has been done Successfully";
			$this->redirect("profile/dashboard");
			}
			else
			{
				
			$objSession->errorMsg =$isSubscribed['RESPMSG'];
			}
			
			}
		}
			
	}
	public function dashboardAction()
	{
 		global $objSession ; 
   		$content = $this->modelStatic->getPage(40); 
		$modelSchool = new Application_Model_SchoolModel();
		$this->view->show = "front_dashboard" ; 
		$plan_detail=$this->modelStatic->Super_Get("subscription","subscription_user_id='".$this->view->user->user_id."'","fetch");
		$this->view->plan_detail=$plan_detail;
		$permission_data=array();
		$permission_data=$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."'","fetch",array("fields"=>array('GROUP_CONCAT(admin_permission_type) as permissions')));
		$this->view->permission_data=$permission_data;
		$announcement_arr=array();
		if($this->view->user->user_type!='school')
		{
			$announcement_arr=$modelSchool->getuserannouncement($this->view->user);
		}
		$this->view->announcement_arr=$announcement_arr;
	}
	
	public function announcementsAction()
	{
		global $objSession; 
		$announcements_array=array();
		$where_condition='';
		$modelSchool = new Application_Model_SchoolModel();
		$student_id=$this->getRequest()->getParam('student_id');
		$check_type=$this->getRequest()->getParam('check_type');
		if(!isset($check_type))
		{
			$check_type=0;	
		}
		$this->view->check_type=$check_type;
		$current_arr=array();	
		$saved_arr=array();
		if(isset($student_id))
		{
			$this->view->student_id=$student_id;
			$student_data=$this->modelStatic->Super_Get("users","user_id='".$student_id."'","fetch");
     		$announcements_array=$modelSchool->getuserannouncementpaging((object)$student_data,$check_type);
			$current_arr=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$student_data['user_id']."' and an_status='0'","fetch",array("fields"=>array("COUNT(an_id) as allval")));
			$saved_arr=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$student_data['user_id']."' and an_status='1'","fetch",array("fields"=>array("COUNT(an_id) as allval")));
				
		}
		else
		{
		
     		$announcements_array=$modelSchool->getuserannouncementpaging($this->view->user,$check_type);	
			$current_arr=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$this->view->user->user_id."' and an_status='0'","fetch",array("fields"=>array("COUNT(an_id) as allval")));
			$saved_arr=$this->modelStatic->Super_Get("announcement_user","an_u_id='".$this->view->user->user_id."' and an_status='1'","fetch",array("fields"=>array("COUNT(an_id) as allval")));	
		}
		$this->view->current_arr=$current_arr;
		$this->view->saved_arr=$saved_arr;
		$page=1;
		$page=$this->_getParam('page');
		if(!isset($_REQUEST['record_per_page']))
		$_REQUEST['record_per_page']=10;
		$paginator=$this->pagination($announcements_array,$page,$_REQUEST['record_per_page']);
		$this->view->paginator=$paginator;
		 $this->view->announcements_array=$announcements_array;
		
	}
	
	public function announceactionAction()
	{
		global $objSession ;
		$student_id=$this->getRequest()->getParam('student_id');
	
		if($_POST['type_val']==0)
		{
			/* Delete Selected Data */
			$select_str=implode(',',$_POST['chek_selcted']);
			$this->modelStatic->Super_Delete("announcement_user","an_id IN(".$select_str.")");
			$objSession->successMsg="Announcement has been deleted Successfully";
		}
		else
		{
			/* Save Selected Data */
			$saved_arr=array('an_status'=>1);
			$select_str=implode(',',$_POST['chek_selcted']);
			$ll=$this->modelStatic->Super_Insert("announcement_user",$saved_arr,'an_id IN ('.$select_str.')');
			
			$objSession->successMsg="Announcement has been saved Successfully";
		}
		if(isset($student_id))
		{
			$this->redirect("profile/announcements/student_id/".$student_id);
		}
		else
		{
			$this->redirect("profile/announcements");	
		}
		
	}
	public function pagination($searchDataQuery,$page,$record_per_page)
	{
		$adapter = new Zend_Paginator_Adapter_DbSelect($searchDataQuery);
		$paginator = new Zend_Paginator($adapter);
		$page =$page;
		$this->view->page=$page;
		$rec_counts = $this->_getParam('itemcountpage');
		if(!$rec_counts){
			if(isset($record_per_page))
			$rec_counts =$record_per_page;
			else
			$rec_counts =10;
		}
		$paginator->setItemCountPerPage($rec_counts);
		$paginator->setCurrentPageNumber($page);
		$paginationControl = new Zend_View_Helper_PaginationControl($paginator, 'sliding', 'pagination-control.phtml');
		$this->view->paginationControl=$paginationControl;
		return $paginator;

	}
	
 	public function imageAction(){
		global $objSession ;
		
		$this->view->show = "front_image";
		 /* Form For Update Profile Image  */
 		$form =  new Application_Form_User();
		$form->image();
		
		
		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
		
			if($form->isValid($data_post)){
				
 				$is_uploaded = $this->_handle_profile_image();
				
				if(is_object($is_uploaded) and $is_uploaded->success){

					if(empty($is_uploaded->media_path)){
						/* Not Image is Uploaded  */
						$objSession->defaultMsg = "No Images Selected ...";
						$this->_helper->getHelper("Redirector")->gotoRoute(array(),'front_image');
					}
					
					
					$is_updated = $this->modelUser->add(array("user_image"=>$is_uploaded->media_path),$this->view->user->user_id);
					
					if(is_object($is_updated) and $is_updated->success){
						
						/* Remove Old User Images*/
						$this->_remove_image(); 
						$objSession->successMsg = " Image Successfully Updated";
						$this->_helper->getHelper("Redirector")->gotoRoute(array(),'front_image');
						
 					}
										
				}
 			}
		}
		
		
		$this->view->form = $form ;
		
	}

	
	/* Method to Crop User Images  */
	public function cropimageAction(){

		global $objSession;
		
   		$this->view->pageHeading = "Crop Image";
		$this->view->pageDescription="";

 		$path=$this->_getParam('path');
		

		if(empty($path)){
			$path = $this->view->user->user_image ;
		}
				
		$this->view->path = $path;
 		
		$filePath = PROFILE_IMAGES_PATH."/".$path;
		
		$imgdata = getimagesize($filePath);
		
		$this->view->imageWidth =  $imgdata[0];
		$this->view->imageHeight =  $imgdata[1];

		
		/* Code for Copping Image */
		if($this->getRequest()->isPost()){
			
			$posted_data = $this->getRequest()->getPost();
			
			$uploaded_image_extension = getFileExtension($path);
			
 			$file_title  = str_replace(".".$uploaded_image_extension,"",$path);
						
			$file_title = formatImageName($file_title);
			
			/* retrive name */
			$_temp = explode("-",$file_title);
			
			array_pop($_temp);array_pop($_temp);
			$file_title = implode("-",$_temp);
			
  			$new_name = $file_title."-".time()."-".rand(1,100000).".".$uploaded_image_extension;
 			
   			$crop_image = array(
				"source_directory" => PROFILE_IMAGES_PATH,
				"name"=>$path,
				"target_name"=>$new_name,
 				'_w'=>$posted_data['w'],
				'_h'=>$posted_data['h'],
				'_x'=>$posted_data['x'],
				'_y'=>$posted_data['y'],
				'destination'=>array(
					"60"=>array("size"=>60),
					"160"=>array("size"=>160),
					"thumb"=>array("size"=>300)
				)
 			);
			
 			$is_crop = $this->pluginImage->universal_crop_image($crop_image);
			
			if($is_crop->success){
				
 				/* Update Name into the database and Replace the prev uploaded news to new names */	
				$this->pluginImage->simple_rename($path,$new_name,array('directory'=>PROFILE_IMAGES_PATH));	
				
				$this->pluginImage->universal_unlink($path,array('directory'=>PROFILE_IMAGES_PATH));	
				
				$is_updated = $this->modelUser->add(array("user_image"=>$new_name),$this->view->user->user_id);
 
   				$objSession->successMsg = $is_crop->message;
			}else{
				$objSession->errorMsg = $is_crop->message;
			}
 			
			$this->_redirect('change-avatar');
		}
		
	}
	
 	
	/* Crop Image  */
	private function _crop_image($param = array()){
 			
			$targ_w = isset($param['width'])?$param['width']:160;
			$targ_h = isset($param['height'])?$param['height']:160;
 			$jpeg_quality = isset($param['quality'])?$param['quality']:100;
 			$src = isset($param['source'])?$param['source']: "";
			$destination = isset($param['destination'])?$param['destination']: "";
			
			$name = isset($param['name'])?$param['name']: "";
			
 
			$dst_r = ImageCreateTrueColor( $targ_w, $targ_h );
			
			 
			list($imagewidth, $imageheight, $imageType) = getimagesize($src."/".$name);
			
			$imageType = image_type_to_mime_type($imageType);
			
			
			$uploaded_image_extension = getFileExtension($name);
 	
			$src = $src.'/'.$name;
			
			switch($imageType) {
				case "image/gif":$source=imagecreatefromgif($src);break;

				case "image/pjpeg":
				case "image/jpeg":
				case "image/jpg":
					$source=imagecreatefromjpeg($src); 
				break;

				case "image/png":
				case "image/x-png":
					$source=imagecreatefrompng($src); 
				break;
			}
			
			imagecopyresampled($dst_r,$source,0,0,$param['_x'],$param['_y'],$targ_w,$targ_h,$param['_w'],$param['_h']);

			switch($imageType) {
				case "image/gif":
					imagegif($dst_r, $destination."/".$name); 
				break;
				case "image/pjpeg":
				case "image/jpeg":
				case "image/jpg":
					imagejpeg($dst_r, $destination."/".$name,$jpeg_quality); 
				break;
				case "image/png":
				case "image/x-png":
					imagepng($dst_r, $destination."/".$name); 
					imagepng($dst_r, $destination."/".$name);  
				break;
				}
	 		
			return true; 
	}
	


	 /* Remove / Unlink Old Profile Image  
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
	 	
	
	
	public function getfulladdressAction(){
		
		$address_string = $this->_getParam('address_string');
			
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
 
 		$getGeometry=json_decode(file_get_contents('http://maps.googleapis.com/maps/api/geocode/json?address='.urlencode($address_string).'&sensor=true'));
					
		$address_specification = array();  /*  For Address Specification Array */
		
	
		if(isset($getGeometry->results[0])) {
				
			/* Country City and State */
			foreach($getGeometry->results[0]->address_components as $addressComponet) {
				
				if(in_array('sublocality', $addressComponet->types)) {
					$address_specification['sublocality'] = ($addressComponet->long_name); 
				}
				
				if(in_array('locality', $addressComponet->types)) {
					$address_specification['locality'] = ($addressComponet->long_name); 
				}
				if(in_array('administrative_area_level_2', $addressComponet->types)) {
					$address_specification['city'] = ($addressComponet->long_name); 
				}
				if(in_array('administrative_area_level_1', $addressComponet->types)) {
					$address_specification['state'] = ($addressComponet->long_name); 
				}
				if(in_array('country', $addressComponet->types)) {
					$address_specification['country'] = ($addressComponet->long_name); 
				}
			}
		}else{
			$address_specification['sublocality'] = ""; 
			$address_specification['locality'] = ""; 
			$address_specification['city'] = ""; 
			$address_specification['state'] = "";
			$address_specification['country'] = ""; 
			
		}
 			
		echo json_encode($address_specification);
		exit;
		//prd($address_string);
		
		
		
		
		
		
	}
	

    public function passwordAction(){
		
		global $objSession; 
		
		if($this->view->user->user_login_type!="normal"){
			$objSession->warningMsg = "You cannot access this feature with this login type";
			$this->_helper->getHelper("Redirector")->gotoRoute(array(),'front_profile');
		}
    		
		$this->view->pageHeading = "Change Password";
 		$this->view->pageDescription = "you can change your account password here ";
 
 		$this->view->show = "change_password";
  
		/* Change Password Form */
		$form =  new Application_Form_User();
		$form->changepassword();
  
   		if($this->getRequest()->isPost()){
 
 			$data_post = $this->getRequest()->getPost();
			
			if($form->isValid($data_post)){
				
				$data_to_update = $form->getValues();
				//prd($data_to_update);
				$data_to_update['user_password'] = md5($data_to_update['user_password']);
				$data_to_update['user_password_text']=$data_post['user_password'];
				
   				$is_update = $this->modelUser->add($data_to_update,$this->view->user->user_id);
  			
				if(is_object($is_update) and $is_update->success){
					$objSession->successMsg = " Password Changed Successfully ";
					$this->_helper->getHelper('Redirector')->gotoRoute(array(),"front_profile");
 				}else{
					$objSession->errorMsg  = $is_update->message; ;
				}
			}else{
				$objSession->errorMsg = "Please Check Information Again ...!";	
			}
  		}
		
		$this->view->form = $form;
		$this->render("index");
	}
 	
}