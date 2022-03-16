<?php
class TeacherController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		
   	}
	
	public function downloadAction()
	{
		require_once ROOT_PATH.'/private/ZiggeoPhpSdk-master/Ziggeo.php';
		$token=$this->getRequest()->getParam('token');
		$ziggeo = new Ziggeo($this->view->config_data['ziggeo_token'],$this->view->config_data['ziggeo_private_key'],$this->view->config_data['ziggeo_encryption_key']);	
		//prd($ziggeo);
		if(isset($token) && $token!='')
		{
			//prd(ROOT_PATH.'/private/ZiggeoPhpSdk-master/Ziggeo.php'); 
			$videoBuffer = $ziggeo->videos()->download_video($token);
			$file="filename.mp4";
			$fullPath = ROOT_PATH.$file; 
		
			if ($fd = fopen ($fullPath, "r")) {
				$fsize = filesize($fullPath);
				$path_parts = pathinfo($fullPath);
				$ext = strtolower($path_parts["extension"]);
				switch ($ext) {
					case "pdf";
					header("Content-type: application/pdf"); 
					header("Content-type: application/doc"); 
					header("Content-Disposition: attachment; filename=\"".$path_parts["basename"]."\""); 
					break;
					default;
					header("Content-type: application/octet-stream");
					header("Content-Disposition: filename=\"".$path_parts["basename"]."\"");
				}
				header("Content-length: $fsize");
				header("Cache-control: private"); 
				while(!feof($fd)) {
					$buffer = fread($fd, 2048);
					echo $buffer;
    }
}
fclose ($fd);
			
		}
		die;
		
	}
	
 	public function indexAction(){	
 		global $objSession ; 
		$this->view->pageHeading = "All Teachers";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  All Teachers';
		$teacher_data=array();
		$teacher_data=$this->modelStatic->Super_Get("users","user_insertby='".$this->view->user->user_id."' and user_type='teacher'","fetchAll");
		$this->view->teacher_data=$teacher_data;
		
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='4'","fetch");					
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
	
	public function checkfileAction()
	{
		global $objSession;
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$la_id=$this->getRequest()->getParam('la_id');
		$filename=$this->getRequest()->getParam('filename');
		$attach_name=array();
		$attach_name=$this->modelStatic->Super_Get("lession_attach","la_id='".$la_id."'","fetch");
		$ext = pathinfo($attach_name['la_name'], PATHINFO_EXTENSION);
		$filename=$filename.'.'.$ext;
		$check_file_exists=array();
		$check_file_exists=$this->modelStatic->Super_Get("lession_attach","la_name='".$filename."' and la_id!='".$la_id."'","fetch");
		if(empty($check_file_exists))
		{
			echo 1;		
		}
		else 
		{
			echo 0;
				
		}
		exit;
		
	}
	
	
	public function reamefileAction()
	{
		global 	$reamefile;
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$la_id=$this->getRequest()->getParam('la_id');
		$filename=$this->getRequest()->getParam('filename');
		$attach_name=array();
		$attach_name=$this->modelStatic->Super_Get("lession_attach","la_id='".$la_id."'",'fetch');
		$ext = pathinfo($attach_name['la_name'], PATHINFO_EXTENSION);
		$filename=$filename.'.'.$ext;
		$data_insert_arr=array();
		$data_insert_arr=array(
				'la_name'=>$filename,
				
		);
		
		if($attach_name['la_type']==1)
		{
			rename(TEMP_PATH.'/'.$attach_name['la_name'],TEMP_PATH.'/'.$filename);
		}
		$this->modelStatic->Super_Insert("lession_attach",$data_insert_arr,'la_id="'.$la_id.'"');
		echo $filename;
		exit;
		
				
	}

	public function alllessonsAction()
	{
			
 		global $objSession ; 
		$this->view->pageHeading = "All Lessons";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  All Lessons';
		$param=$this->getRequest()->getParam('param');
		$status=$this->getRequest()->getParam('status');
		if(isset($param))
		{
		$this->view->param=$param;
		}
		else
		{
			$this->view->param=0;
		}
		if(isset($status))
		{
			$this->view->status=$status;	
		}
		else
		{
			$this->view->status=0;
		}
		$teacher_data=array();
		$teacher_data=$this->modelStatic->Super_Get("users","user_insertby='".$this->view->user->user_id."' and user_type='teacher'","fetchAll");
		$this->view->teacher_data=$teacher_data;
		
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			
				/* If user is not admin or subadmin */
				$this->redirect('index');	
			
		}	
		
	}
	
	public function alltemplatesAction()
	{
			
 		global $objSession ; 
		$param=$this->getRequest()->getParam('param');
		if(!isset($param))
		{
			$param=0;	
		}
		$this->view->param=$param;
		$this->view->pageHeading = "All Templates";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  All Templates';
		$teacher_data=array();
		$teacher_data=$this->modelStatic->Super_Get("users","user_insertby='".$this->view->user->user_id."' and user_type='teacher'","fetchAll");
		$this->view->teacher_data=$teacher_data;
		
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
				/* If user is not admin or subadmin */
				$this->redirect('index');	
		}	
		
	}
	
	
	public function changestatusAction()
	{
		global $objSession ; 
		$this->_helper->layout->disableLayout();
		$status=$this->getRequest()->getParam('status');
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$student_id=$this->getRequest()->getParam('student_id');
		$array=array(
			'l_s_viewstatus'=>$status
		);
		$kk=$this->modelStatic->Super_Insert("lesson_student",$array,'l_s_lessid="'.$lesson_id.'" and l_s_stuid="'.$student_id.'"');
		prn($array);
		prn($kk);
		exit;
		
	}
	

	public function uploadrecordingAction()
	{
		$this->_helper->layout->disableLayout();
		global $objSession ; 
		$filename=$this->getRequest()->getParam('fileval');
		$path=TEMP_PATH.'/voice_'.$this->view->user->user_id.'/';
		$new_filename=$filename.time().".mp3";
		rename($path.$filename.'.mp3',$path.$new_filename);
		echo $new_filename;
		exit;
		
	}
	public function deletetecordingAction()
	{
		$this->_helper->layout->disableLayout();
		$filename=$this->getRequest()->getParam('fileval');
		$path=TEMP_PATH.'/voice_'.$this->view->user->user_id.'/';
		unlink($path.$filename);
		exit;
		
	}
	
	public function saverecordingAction()
	{
		$this->_helper->layout->disableLayout();
		global $objSession ; 
		$myFile = $_FILES['Filedata']['name'];
	$user_id=$this->view->user->user_id;
	$path=TEMP_PATH.'/voice_'.$user_id.'/';
	
	if(!is_dir($path))
	{
		mkdir($path,0777);
	}

	$filename =$myFile . ".mp3";
	move_uploaded_file($_FILES['Filedata']['tmp_name'],$path.$filename) or die ("can't move");
	
		/*echo $path;
		
		$apt    = new Zend_File_Transfer_Adapter_Http();
		$files  = $apt->getFileInfo('Filedata');
		$apt->setDestination($path);
		foreach($files as $file => $fileInfo) {
		if ($apt->isUploaded($file)) {
		if ($apt->isValid($file)) {
		if ($apt->receive($file)) {
		
		$info = $apt->getFileInfo($file);
		$tmp  = $info[$file]['tmp_name'];
		$data = file_get_contents($tmp);
		$name=$info[$file]['name'];
		
		$newname=time()."_".$filename1;
		rename($path.$name,$path.$newname);
		prn('fkldf');
		
		}
		}
		}
		}*/
		
		
		
	
	echo "uploaded successfully. :-)";
	exit;
	}
	
	public function getsavetemplatedataAction()
	{
		global $objSession ; 
		$this->_helper->layout->disableLayout();
		$lesson_id=$this->getRequest()->getParam('id');
		
		$lesson_data=array();
		$lesson_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch");
		//prd($lesson_data);
		$lessont_attachement_array=$this->modelStatic->Super_Get("lession_attach","la_lesson_id='".$lesson_id."'","fetchAll");
		$this->view->lesson_data=$lesson_data;
		//prd($lesson_data);
		$this->view->lessont_attachement_array=$lessont_attachement_array;
		
	}
	
	public function getnotsavedlessonAction()
	{
		global $objSession ; 
		$this->_helper->layout->disableLayout();
		$lesson_id=$this->getRequest()->getParam('id');
		$lesson_data=array();
		$lesson_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch");
		$lessont_attachement_array=$this->modelStatic->Super_Get("lession_attach","la_lesson_id='".$lesson_id."'","fetchAll");
		$this->view->lesson_data=$lesson_data;
		$this->view->lessont_attachement_array=$lessont_attachement_array;
		
		
	}
	/* Add New Template */
	
	public function addtemplateAction()
	{
		global $objSession ; 
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$teacher_data=array();
		if(isset($teacher_id))
		{
			$teacher_data=$this->modelStatic->Super_Get("users","user_id='".$teacher_id."'","fetch");
		}
		$this->view->teacher_data=$teacher_data;
		$modelSchool = new Application_Model_SchoolModel();
		$student_id=$this->getRequest()->getParam('student_id');
		$lesson_data=array();
		$user_param_id=$this->view->user->user_id;
		if(isset($teacher_id))
		{
			$user_param_id=$teacher_id;	
		}
		$document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_userid="'.$user_param_id.'"','fetchAll');
		$students_array=$this->modelStatic->Super_Get("private_teacher","private_teacher_teacherid='".$user_param_id."'","fetch",array("fields"=>array("GROUP_CONCAT(private_teacher_studentid) as students")));
		$student_name_str='';
		if(!empty($students_array['students']))
		{
			$student_name_arr=$this->modelStatic->Super_Get("users","user_id IN (".$students_array["students"].")","fetch",array("fields"=>array("GROUP_CONCAT(user_first_name) as name")));
			if(!empty($student_name_arr['name']))
			{
				$student_name_str=	$student_name_arr['name'];
			}
		}
		$this->view->student_name_str=$student_name_str;
		/* Param=1(Save As Temaplate) Param=2(Save without sending) */
		$this->view->document_arr=$document_arr;
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$student_arr=array();
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->lesson_id=$lesson_id;	
			$lesson_show_arr=$this->modelStatic->Super_Get("lesson",'lesson_id="'.$lesson_id.'"',"fetch");
		//	$student_arr=$this->modelStatic->Super_Get("users","user_id='".$lesson_show_arr['lesson_student_id']."'","fetch");
			
		}
		else
		{
			if(isset($student_id))
			{
			$student_arr=$this->modelStatic->Super_Get("users","user_id='".$student_id."'","fetch");
		
			}
			
			
		}
		
		$user_agent = new Zend_Http_UserAgent();
	 	$agent = $user_agent->getDevice();
		$is_mobile=0;
		if("Zend_Http_UserAgent_Mobile"==get_class($agent))
		{
			$is_mobile=1;	
		}
		$this->view->is_mobile=$is_mobile;
		$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_lesson_id="'.$lesson_id.'" ','fetchAll');
		$this->view->lession_arr=$lession_arr;
		$this->view->lesson_id=$lesson_id;
		$this->view->student_id=$student_id;
		$form = new Application_Form_SchoolForm();
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$form->newlessontemplate('',$lesson_id);
		}	
		else
		{
			$form->newlessontemplate();		
		}
		
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->pageHeading = "Update Template Lesson";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Update Template Lesson';
			/* Get Lesson Data */
			$lesson_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch");	
			$this->view->lesson_data=$lesson_data;
			$form->populate($lesson_data);
		}
		else
		{
			$this->view->pageHeading = "Add New Template";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Add New Template';	
		}		
		$this->view->form=$form;
		
			if ($this->getRequest()->isPost())
			{ // Post Form Data
			//prd($_SESSION['voice_recording_'.$this->view->user->user_id]);
				
				$posted_data  = $this->getRequest()->getPost();
				
				if ($form->isValid($posted_data))
				{ // If form is valids
					
					$data_insert=$form->getValues();
					$videotokenarr=$posted_data['video_token'];
					$save_template=0;
					$savesend=0;					
					unset($data_insert['param']);
					unset($data_insert['existing_fold']);
					if(isset($lesson_data) && !empty($lesson_data))
					{
						
						if(isset($posted_data['delete_attach']) && !empty($posted_data['delete_attach']))
						{
						foreach($posted_data['delete_attach'] as $dal=>$val)
					{
					
						if($val!='')
						{
							$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$val.'"','fetch');
							$fdg=$this->modelStatic->Super_Delete("lession_attach",'la_id="'.$val.'"');
							unlink(TEMP_PATH.'/'.$lession_arr['la_name']);
						}
						
					}
						}
						
						$data_insert['lesson_status']=$savesend;
						
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						// ================================
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'"');
						$insert->inserted_id=$lesson_id;
							
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								$leson_attache_file_arr=array();
								$leson_attache_file_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_id="'.$v.'"',"fetch");
								//prd($leson_attache_file_arr);
								copy(TEACHER_FILES_PATH.'/'.$leson_attache_file_arr['teacher_attach_name'],TEMP_PATH.'/'.$leson_attache_file_arr['teacher_attach_name']);
								//prd('fgvbnvn');
								$data=array();
								$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $leson_attache_file_arr['teacher_attach_name'],
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
								//prd($n);
							}
						}
						
						//existing folder
						
						//audio and video
						$path =TEMP_PATH.'/voice_'.$user_param_id.'/';	
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
									
									rename($path."/".$file,TEMP_PATH."/".$file);
									echo "Here";
								}
								}
						}
						
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prn($n);
							
						}
						//prd($array);
						//audio and video
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$user_param_id.'/';	
						$files = scandir($path);
					
						$array=array();
				
						foreach ($files as $file) 
						{
							
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								$newname = str_replace(' ', '_', $newname);
								
								$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
								$filexp=explode(".".$ext,$file);
								$newname=$filexp[0].time().".".$ext;
								
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$newname);
									$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
									global $videoext;
								
									if(in_array($ext,$videoext) || $ext=='mp3')
									{
											
											$outputfile=$newname;
											/* Convert Video usinf FFmpeg */
											$filename=time();
											$uploadFileName=TEMP_PATH."/".$newname;
											$of=TEMP_PATH."/".'VID_'.$filename.'.mp4';
											$outputfile='VID_'.$filename.'.mp4';
											if($ext!='mp4')
											{
										
											//ultrafast
											$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";										$out=exec($cmd,$output,$ret);
												
											}
											else
											{ 
											//$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
											$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
											//$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -c:v libx264 -preset ultrafast   ".$of." 2>&1";
											$outputfile=$newname;
											$out=exec($cmd,$output,$ret);
												
											}
										//	prd("in");
											/* End Convert Video using FFmpeg */
											$newname=$outputfile;
									}
									else
									{
										
									}
									array_push($array,$newname);
								}
								}
								}
						
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data);
						}
						//images
						$this->modelStatic->Super_Delete("lession_attach",'la_lesson_id="'.$insert->inserted_id.'" and la_type="0"');
						foreach($videotokenarr as $k=>$v)
						{
							$video_name='Video_'.($k+1).'_'.time();
							$data_add=array();
							$data_add=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $video_name,
										'la_type'=>0,
										'la_token'=>$v
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data_add);	
						}
						
						$objSession->successMsg="Lesson has been updated Successfully";
					
					}
					else
					{
						
						$lesson_id='';
						if(isset($data_insert['lesson_notsaved_name']) && !empty($data_insert['lesson_notsaved_name']))
						{
							$lesson_id=$data_insert['lesson_notsaved_name'];	
						}
						$data_insert['lesson_template']=$save_template;
						$data_insert['lesson_status']=$savesend;
						//$data_insert['lesson_student_id']=$student_id;
						unset($data_insert['lesson_template_name']);
						unset($data_insert['lesson_type']);
						unset($data_insert['lesson_notsaved_name']);
						if(isset($lesson_id) && !empty($lesson_id))
						{
							//prd("if");
							
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							$data_insert['lesson_date']=date('Y-m-d H:i:s');
							// ================================
							$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'"');	
						}
						else
						{
							//prd('else');
						$data_insert['lesson_teacherid']=$user_param_id;
						$user_type=0;
						$school_id=$this->view->user->user_school_id;
						if($this->view->user->user_type=='school')
						{
							$user_type=1;
							$school_id=$user_param_id;
							/* This Lesson is added By School User */	
						}
						$data_insert['lesson_user_type']=$user_type;
						$data_insert['lesson_school_id']=$school_id;
						
						date_default_timezone_set('America/Los_Angeles');	// PDT time
//						$data_insert['lesson_date']=gmdate('Y-m-d H:i:s');
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						$data_insert['lesson_template']=$save_template;
						$data_insert['lesson_status']=$savesend;
						$data_insert['lesson_template']=1;
					//	$data_insert['lesson_student_id']=$student_id;
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert);
					//	prd($insert);
						$lesson_id=$insert->inserted_id;
						}
						
						//existing folder
						$this->modelStatic->Super_Delete("lession_attach",'la_lesson_id="'.$lesson_id.'"');
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							//prd('gfvbnvb');
							//prd($posted_data['existing_fold']);
							
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								$leson_attache_file_arr=array();
								$leson_attache_file_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_id="'.$v.'"',"fetch");
								//prd($leson_attache_file_arr);
								copy(TEACHER_FILES_PATH.'/'.$leson_attache_file_arr['teacher_attach_name'],TEMP_PATH.'/'.$leson_attache_file_arr['teacher_attach_name']);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $leson_attache_file_arr['teacher_attach_name'],
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
								//prd($n);
							}
						}
					
						if(isset($posted_data['upload_attach']) && !empty($posted_data['upload_attach']))
						{
							foreach($posted_data['upload_attach'] as $k=>$v)
							{
								$get_attach_arr=array();
								$get_attach_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$v.'"',"fetch");
								$ext='';
								$ext = pathinfo($get_attach_arr['la_name'], PATHINFO_EXTENSION);
								$new_nme=time().$get_attach_arr['la_name'];
								
								$filexp=explode(".".$ext,$get_attach_arr['la_name']);
								$new_nme=$filexp[0].time().".".$ext;
								
								copy(TEMP_PATH.'/'.$get_attach_arr['la_name'],TEMP_PATH.'/'.$new_nme);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $new_nme,
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							}	
						}
						//existing folder
				
						//audio and video
						$path =TEMP_PATH.'/voice_'.$this->view->user->user_id.'/';	
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
									rename($path."/".$file,TEMP_PATH."/".$file);
								}
								
								
								}
						}
						//prd($array);		
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						//audio and video
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$user_param_id.'/';	
						$files = scandir($path);
						$array=array();
				
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								$newname = str_replace(' ', '_', $newname);
								
								$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
								$filexp=explode(".".$ext,$file);
								$newname=$filexp[0].time().".".$ext;
								
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$newname);
									$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
									global $videoext;
									if(in_array($ext,$videoext) || $ext=='mp3')
									{
											
											$outputfile=$newname;
											/* Convert Video usinf FFmpeg */
											$filename=time();
											$uploadFileName=TEMP_PATH."/".$newname;
											$of=TEMP_PATH."/".'VID_'.$filename.'.mp4';
											$outputfile='VID_'.$filename.'.mp4';
											if($ext!='mp4')
											{
										
											//ultrafast
											$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";										$out=exec($cmd,$output,$ret);
												
											}
											else
											{ 
											//$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
											$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
											//$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -c:v libx264 -preset ultrafast   ".$of." 2>&1";
											$outputfile=$newname;
											$out=exec($cmd,$output,$ret);
												
											}
										//	prd("in");
											/* End Convert Video using FFmpeg */
											$newname=$outputfile;
									}
									else
									{
										
									}
									array_push($array,$newname);
								}
								}
								}
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data);
						}
						foreach($videotokenarr as $k=>$v)
						{
							$video_name='Video_'.($k+1).'_'.time();
							$data_add=array();
							$data_add=array('la_lesson_id'=>$lesson_id,
										'la_name' => $video_name,
										'la_type'=>0,
										'la_token'=>$v
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data_add);	
						}
						//images
					
					$objSession->successMsg="Lesson has been added Successfully";
					}
					if($this->view->user->user_type=='teacher')
					{
							$this->redirect("teacher/viewtemplates");
					}
					else
					{
						if(isset($teacher_id))
						{
							$this->redirect("teacher/viewtemplates/teacher_id/".$teacher_id);
						}
						else
						{
							$this->redirect("teacher/alltemplates");	
						}
							
					}
					
					
				}
			}
			else
			{
				
				if(is_dir(TEMP_PATH.'/voice_'.$user_param_id))
			{
				
				//DeleteDirfileupload(TEMP_PATH.'/voice_'.$this->view->user->user_id);
			}
			if(is_dir(TEMP_PATH.'/upimage_'.$user_param_id))
			{
				DeleteDirfileupload(TEMP_PATH.'/upimage_'.$user_param_id);
			}
		
				
			}
			
	
				
	}
	
	/* Add New Template End */
	
	public function createaddtemplateAction()
	{
		global $objSession ; 
	/*	ini_set('max_execution_time', 259200);
		ini_set('max_input_time', 259200);*/
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$teacher_data=array();
		if(isset($teacher_id))
		{
			$teacher_data=$this->modelStatic->Super_Get("users","user_id='".$teacher_id."'","fetch");
		}
		$this->view->teacher_data=$teacher_data;
		$modelSchool = new Application_Model_SchoolModel();
		$student_id=$this->getRequest()->getParam('student_id');
		$lesson_data=array();
		$user_param_id=$this->view->user->user_id;
		if(isset($teacher_id))
		{
			$user_param_id=$teacher_id;	
		}
		$document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_userid="'.$user_param_id.'"','fetchAll');
		$students_array=$this->modelStatic->Super_Get("private_teacher","private_teacher_teacherid='".$user_param_id."'","fetch",array("fields"=>array("GROUP_CONCAT(private_teacher_studentid) as students")));
		$student_name_str='';
		if(!empty($students_array['students']))
		{
			$student_name_arr=$this->modelStatic->Super_Get("users","user_id IN (".$students_array["students"].")","fetch",array("fields"=>array("GROUP_CONCAT(user_first_name) as name")));
			if(!empty($student_name_arr['name']))
			{
				$student_name_str=	$student_name_arr['name'];
			}
		}
		$this->view->student_name_str=$student_name_str;
		/* Param=1(Save As Temaplate) Param=2(Save without sending) */
		$this->view->document_arr=$document_arr;
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$student_arr=array();
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->lesson_id=$lesson_id;	
			$lesson_show_arr=$this->modelStatic->Super_Get("lesson",'lesson_id="'.$lesson_id.'"',"fetch");
		//	$student_arr=$this->modelStatic->Super_Get("users","user_id='".$lesson_show_arr['lesson_student_id']."'","fetch");
			
		}
		else
		{
			if(isset($student_id))
			{
			$student_arr=$this->modelStatic->Super_Get("users","user_id='".$student_id."'","fetch");
		
			}
			
			
		}
		
		$user_agent = new Zend_Http_UserAgent();
	 	$agent = $user_agent->getDevice();
	if("Zend_Http_UserAgent_Desktop"==get_class($agent)){
			
			
			if(isset($lesson_id))
			{
				$this->redirect("teacher/newlesson/lesson_id/".$lesson_id);	
			}
			else
			{
				$this->redirect("teacher/newlesson");		
			}
		}
		
		$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_lesson_id="'.$lesson_id.'" ','fetchAll');
		$this->view->lession_arr=$lession_arr;
		$this->view->lesson_id=$lesson_id;
		$form = new Application_Form_SchoolForm();
		if(isset($lesson_id) && !empty($lesson_id))
		{
				$form->newlessontemplate('',$lesson_id);
		}
		else
		{
			$form->newlessontemplate();		
		}
		
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->pageHeading = "Update Template Lesson";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Update Template Lesson';
			/* Get Lesson Data */
			$lesson_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch");	
			$form->populate($lesson_data);
		}
		else
		{
			$this->view->pageHeading = "Add New Template";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Add New Template';	
		}		
		$this->view->form=$form;
		
			if ($this->getRequest()->isPost())
			{ // Post Form Data
			//prd($_SESSION['voice_recording_'.$this->view->user->user_id]);
				
				$posted_data  = $this->getRequest()->getPost();
					$ffmpegPath = ROOT_PATH."/ffmpeg";
				if ($form->isValid($posted_data))
				{ // If form is valids
					$apt    = new Zend_File_Transfer_Adapter_Http();
				$path =TEMP_PATH;
				/* Add Video Recording */
				$video_recording=array();
				if(isset($_POST['video_count']) && $_POST['video_count']>0)
				{
					for($i=1; $i<=$_POST['video_count']; $i++)
					{
						if(isset($_FILES['myvideo_'.$i]) && !empty($_FILES['myvideo_'.$i]))
						{
							
							$apt    = new Zend_File_Transfer_Adapter_Http();
							$files  = $apt->getFileInfo('myvideo_'.$i);
							$filename=$_FILES['myvideo_'.$i]['name'];
							$filename1= str_replace(' ', '_', $filename);
							$path =TEMP_PATH.'/';
							$apt->setDestination($path);
							foreach($files as $file => $fileInfo) {
							if ($apt->isUploaded($file)) {
							if ($apt->isValid($file)) {
							if ($apt->receive($file)) {
							//prd('fkldf');
							$info = $apt->getFileInfo($file);
							$size=($info[$file]['size'])/(1024*1024);
										if($size>150)
										{ 
											
												$objSession->errorMsg="Please upload maximum 150MB Size of file";
												if($this->view->user->user_type=='teacher')
												{
													$this->redirect("teacher/viewlessons");
												}
												else
												{
													$this->redirect("teacher/alllessons");
												}
											
										}
										else
										{
												$tmp  = $info[$file]['tmp_name'];
												$data = file_get_contents($tmp);
												$name=$info[$file]['name'];
												$newname=time()."_".$filename1;
												rename($path.$name,$path.$newname);
												$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
												$outputfile=$newname;
												/* Convert Video usinf FFmpeg */
												$filename=time();
												$uploadFileName=$path.$newname;
												$of=$path.'VID_'.$filename.'.mp4';
												$outputfile='VID_'.$filename.'.mp4';
												if($ext!='mp4')
												{
													
													$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset slow -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";
													$out=exec($cmd,$output,$ret);
												}
												else
												{
													//$outputfile=$newname;
													$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
												//	$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
												$out=exec($cmd,$output,$ret);
												}
												 
												 /* End Convert Video usinf FFmpeg */
												array_push($video_recording,$outputfile);
										}
							}
							}
							}
							}
						}
					}
				}
				/* End Video Recording plugin */
				/* Add Audio Recording */
				$audio_recording=array();
				if(isset($_POST['audio_count']) && $_POST['audio_count']>0)
				{
					for($i=1; $i<=$_POST['audio_count']; $i++)
					{
						if(isset($_FILES['myaudio_'.$i]) && !empty($_FILES['myaudio_'.$i]))
						{
							
							$apt    = new Zend_File_Transfer_Adapter_Http();
							$files  = $apt->getFileInfo('myaudio_'.$i);
							$filename=$_FILES['myaudio_'.$i]['name'];
							$filename1= str_replace(' ', '_', $filename);
							$path =TEMP_PATH.'/';
							$apt->setDestination($path);
							foreach($files as $file => $fileInfo) {
							if ($apt->isUploaded($file)) {
							if ($apt->isValid($file)) {
							if ($apt->receive($file)) {
							//prd('fkldf');
							$info = $apt->getFileInfo($file);
								$size=($info[$file]['size'])/(1024*1024);
										if($size>150)
										{ 
											
												$objSession->errorMsg="Please upload maximum 150MB Size of file";
												if($this->view->user->user_type=='teacher')
												{
													$this->redirect("teacher/viewlessons");
												}
												else
												{
													$this->redirect("teacher/alllessons");
												}
											
										}
										else
										{
													$tmp  = $info[$file]['tmp_name'];
													$data = file_get_contents($tmp);
													$name=$info[$file]['name'];
													$newname=time()."_".$filename1;
													rename($path.$name,$path.$newname);
													$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
													$outputfile=$newname;
													/* Convert Video usinf FFmpeg */
													$filename=time();
													$uploadFileName=$path.$newname;
													$of=$path.'VID_'.$filename.'.mp4';
													$outputfile='VID_'.$filename.'.mp4';
													if($ext!='mp4')
													{
														
														$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset slow -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";
													}
													else
													{
														$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
													}
													 $out=exec($cmd,$output,$ret);
													 /* End Convert Video usinf FFmpeg */
													array_push($audio_recording,$outputfile);
										}
							}
							}
							}
							}
						}
					}
				}
				/* End Audio Recording plugin */
					$data_insert=$form->getValues();
					$save_template=0;
					$savesend=0;					
					unset($data_insert['param']);
					unset($data_insert['existing_fold']);
					if(isset($lesson_data) && !empty($lesson_data))
					{
						if(isset($posted_data['delete_attach']) && !empty($posted_data['delete_attach']))
						{
						foreach($posted_data['delete_attach'] as $dal=>$val)
					{
					
						if($val!='')
						{
							$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$val.'"','fetch');
							$fdg=$this->modelStatic->Super_Delete("lession_attach",'la_id="'.$val.'"');
							unlink(TEMP_PATH.'/'.$lession_arr['la_name']);
						}
						
					}
						}
						
						$data_insert['lesson_status']=$savesend;						
						
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						// ================================
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'"');
						$insert->inserted_id=$lesson_id;
							
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
						
							
							//prd('gfvbnvb');
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								$leson_attache_file_arr=array();
								$leson_attache_file_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_id="'.$v.'"',"fetch");
								//prd($leson_attache_file_arr);
								copy(TEACHER_FILES_PATH.'/'.$leson_attache_file_arr['teacher_attach_name'],TEMP_PATH.'/'.$leson_attache_file_arr['teacher_attach_name']);
								//prd('fgvbnvn');
								$data=array();
								$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $leson_attache_file_arr['teacher_attach_name'],
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
								//prd($n);
							}
						}
						
						//existing folder
						
						
						/* Add Video Recording array to database */
						foreach($video_recording as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						/* Add Video recording array to database end */
						//audio and video
						
						/* Add Audio Recording array to database */
						foreach($audio_recording as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						/* Add Audio recording array to database end */
						
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$user_param_id.'/';	
						$files = scandir($path);
					
						$array=array();
				
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								$newname = str_replace(' ', '_', $newname);
								array_push($array,$newname);
								
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$newname);
								}
								
								
								}
								}
						
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data);
						}
						//images
						
						
						$objSession->successMsg="Lesson has been updated Successfully";
					
					}
					else
					{
						$lesson_id='';
						if(isset($data_insert['lesson_notsaved_name']) && !empty($data_insert['lesson_notsaved_name']))
						{
							$lesson_id=$data_insert['lesson_notsaved_name'];	
						}
						$data_insert['lesson_template']=$save_template;
						$data_insert['lesson_status']=$savesend;
						//$data_insert['lesson_student_id']=$student_id;
						unset($data_insert['lesson_template_name']);
						unset($data_insert['lesson_type']);
						unset($data_insert['lesson_notsaved_name']);
						if(isset($lesson_id) && !empty($lesson_id))
						{
							//prd("if");
							
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							$data_insert['lesson_date']=date('Y-m-d H:i:s');
							// ================================
							$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'"');	
						}
						else
						{
							//prd('else');
						$data_insert['lesson_teacherid']=$user_param_id;
						$user_type=0;
						$school_id=$this->view->user->user_school_id;
						if($this->view->user->user_type=='school')
						{
							$user_type=1;
							$school_id=$user_param_id;
							/* This Lesson is added By School User */	
						}
						$data_insert['lesson_user_type']=$user_type;
						$data_insert['lesson_school_id']=$school_id;
						
						date_default_timezone_set('America/Los_Angeles');	// PDT time
//						$data_insert['lesson_date']=gmdate('Y-m-d H:i:s');
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						$data_insert['lesson_template']=$save_template;
						$data_insert['lesson_status']=$savesend;
						$data_insert['lesson_template']=1;
					//	$data_insert['lesson_student_id']=$student_id;
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert);
					//	prd($insert);
						$lesson_id=$insert->inserted_id;
						}
						
						//existing folder
						$this->modelStatic->Super_Delete("lession_attach",'la_lesson_id="'.$lesson_id.'"');
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							//prd('gfvbnvb');
							//prd($posted_data['existing_fold']);
							
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								$leson_attache_file_arr=array();
								$leson_attache_file_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_id="'.$v.'"',"fetch");
								//prd($leson_attache_file_arr);
								copy(TEACHER_FILES_PATH.'/'.$leson_attache_file_arr['teacher_attach_name'],TEMP_PATH.'/'.$leson_attache_file_arr['teacher_attach_name']);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $leson_attache_file_arr['teacher_attach_name'],
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
								//prd($n);
							}
						}
					
						if(isset($posted_data['upload_attach']) && !empty($posted_data['upload_attach']))
						{
							foreach($posted_data['upload_attach'] as $k=>$v)
							{
								$get_attach_arr=array();
								$get_attach_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$v.'"',"fetch");
								$ext='';
								$ext = pathinfo($get_attach_arr['la_name'], PATHINFO_EXTENSION);
								$new_nme=time().$get_attach_arr['la_name'];
								
								
								$filexp=explode(".".$ext,$get_attach_arr['la_name']);
								$new_nme=$filexp[0].time().".".$ext;
								
								copy(TEMP_PATH.'/'.$get_attach_arr['la_name'],TEMP_PATH.'/'.$new_nme);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $new_nme,
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							}	
						}
						//existing folder
				
						
						/* Add Video Recording array to database */
						foreach($video_recording as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						/* Add Video recording array to database end */
						//audio and video
						
						/* Add Audio Recording array to database */
						foreach($audio_recording as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						/* Add Audio recording array to database end */
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$user_param_id.'/';	
						$files = scandir($path);
						$array=array();
				
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								$ext = pathinfo($path.$file, PATHINFO_EXTENSION);
								$filexp=explode(".".$ext,$file);
								$newname=$filexp[0].time().".".$ext;
								
								
								
								
								array_push($array,$newname);
								
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$newname);
								}
								
								
								}
								}
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data);
						}
						//images
					
					$objSession->successMsg="Lesson has been added Successfully";
					}
					if($this->view->user->user_type=='teacher')
					{
							$this->redirect("teacher/viewtemplates");
					}
					else
					{
						if(isset($teacher_id))
						{
							$this->redirect("teacher/viewtemplates/teacher_id/".$teacher_id);
						}
						else
						{
							$this->redirect("teacher/alltemplates");	
						}
							
					}
					
					
				}
			}
			else
			{
				
				if(is_dir(TEMP_PATH.'/voice_'.$user_param_id))
			{
				
				//DeleteDirfileupload(TEMP_PATH.'/voice_'.$this->view->user->user_id);
			}
			if(is_dir(TEMP_PATH.'/upimage_'.$user_param_id))
			{
				DeleteDirfileupload(TEMP_PATH.'/upimage_'.$user_param_id);
			}
		
				
			}
			
	
				
	}
	

	public function createlessonAction()
	{
		global $objSession ;

		$modelSchool = new Application_Model_SchoolModel(); 
		$lesson_data=array();
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$student_id=$this->getRequest()->getParam('student_id');
		$class_id=$this->getRequest()->getParam('class_id');
		$class_data=array();
		$lesson_class=array();
		$class_data_arr=array();
		$user_param_id=$this->view->user->user_id;
		if(isset($teacher_id))
		{
			$user_param_id=$teacher_id;	
		}
		if(isset($class_id))
		{
			
			if($this->view->user->user_type=='teacher')
			{
				/* Teacher User */
				
				$class_data=$this->modelStatic->Super_Get("teacher_classes","teacher_class_classid='".$class_id."' and teacher_class_userid='".$user_param_id."'","fetch");
				
				if(empty($class_data))
				{
					$objSession->errorMsg="Invalid Request for teacher";	
					$this->redirect("profile/teacherdashboard");
				}
			}
			else
			{
				/* School User */
				
				$class_data=$this->modelStatic->Super_Get("Classes","class_id='".$class_id."' and class_school_id='".$user_param_id."'","fetch");
					if(empty($class_data))
					{
						$objSession->errorMsg="Invalid Request for teacher";	
						$this->redirect("profile/dashboard");
					}
			}
			
			$this->view->class_data=$class_data;
			$class_data_arr=$this->modelStatic->Super_Get("lesson","lesson_class_id='".$class_id."'","fetchAll",array("pagination"=>1));
			/* Start All Lesson of particular class */
			$page1=1;
			$page1=$this->_getParam('page1');
			if(!isset($_REQUEST['record_per_page1']))
			$_REQUEST['record_per_page1']=10;
			$paginator1=$this->pagination($class_data_arr,$page1,$_REQUEST['record_per_page1']);
			$this->view->paginator1=$paginator1;
		}
		$this->view->class_data_arr=$class_data_arr;
		$user_param_id=$this->view->user->user_id;
		if(isset($teacher_id))
		{
			$user_param_id=$teacher_id;	
		}
		if($this->view->user->user_type=='teacher')
		{
			$document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_userid="'.$user_param_id.'"','fetchAll');
			$students_array=$this->modelStatic->Super_Get("private_teacher","private_teacher_teacherid='".$user_param_id."'","fetch",array("fields"=>array("GROUP_CONCAT(private_teacher_studentid) as students")));
			
			if(!empty($students_array['students']))
			{
			$student_name_arr=$this->modelStatic->Super_Get("users","user_id IN (".$students_array["students"].")","fetch",array("fields"=>array("GROUP_CONCAT(user_first_name) as name")));
			if(!empty($student_name_arr['name']))
			{
				$student_name_str=	$student_name_arr['name'];
			}
		}	
		}
		else
		{
			$document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_schoolid="'.$user_param_id.'"','fetchAll');	
			$student_name_arr=$this->modelStatic->Super_Get("users","user_type='student' and user_school_id='".$user_param_id."'","fetch",array("fields"=>array("GROUP_CONCAT(user_first_name) as name")));
			if(!empty($student_name_arr['name']))
			{
				$student_name_str=	$student_name_arr['name'];
			}
		}
		$this->view->student_name_str=$student_name_str;
		$this->view->document_arr=$document_arr;
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->lesson_id=$lesson_id;	
			$lesson_show_arr=$this->modelStatic->Super_Get("lesson",'lesson_id="'.$lesson_id.'"',"fetch");
		//	$student_arr=$this->modelStatic->Super_Get("users","user_id='".$lesson_show_arr['lesson_student_id']."'","fetch");
			
		}
		else
		{
			if(isset($student_id))
			{
		
				$student_arr=$this->modelStatic->Super_Get("users","user_id='".$student_id."'","fetch");
				
				/* All Lesson data with  Pagination */
				$this->view->student_arr=$student_arr;	
				$get_all_lessons=$modelSchool->userlessonpagination($student_arr['user_id'],$user_param_id);
				$page=1;
				$page=$this->_getParam('page');
				if(!isset($_REQUEST['record_per_page']))
				$_REQUEST['record_per_page']=10;
				$paginator=$this->pagination($get_all_lessons,$page,$_REQUEST['record_per_page']);
				$this->view->paginator=$paginator;
				
				/* End All Lesson data with  Pagination */
			}
			
			
		}
		
		
		$user_agent = new Zend_Http_UserAgent();
	 	$agent = $user_agent->getDevice();
		/*if("Zend_Http_UserAgent_Desktop"==get_class($agent)){
			
			
			if(isset($lesson_id))
			{
				$this->redirect("teacher/newlesson/lesson_id/".$lesson_id);	
			}
			else
			{
				$this->redirect("teacher/newlesson");		
			}
		}*/
		$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_lesson_id="'.$lesson_id.'"','fetchAll');
		$this->view->lession_arr=$lession_arr;
		$this->view->lesson_id=$lesson_id;
		$this->view->student_id=$student_id;
		$form = new Application_Form_SchoolForm();

		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->pageHeading = "Update Lesson";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Update Lesson';
			/* Get Lesson Data */
				if(isset($lesson_id) && !empty($lesson_id))
			{
					if(isset($student_id))
					{
						$form->newlesson('',$lesson_id,$student_id);
					}
					else if(isset($class_id))
					{
						$form->newlesson(false,$lesson_id,false,$class_id);
					}
				
					else
					{
						$form->newlesson('',$lesson_id);	
					}
			}
		
			
			$lesson_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch");	
			$form->populate($lesson_data);
			$this->view->lesson_id=$lesson_id;
			$student_arr=array();
			$student_arr=$this->modelStatic->Super_Get("users","user_id='".$lesson_show_arr['lesson_student_id']."'","fetch");
			$this->view->student_arr=$student_arr;
		}
		else
		{
			
			if(isset($student_id))
			{
				$form->newlesson(false,false,$student_id);		
			}
			else if(isset($class_id))
			{
				$form->newlesson(false,false,false,$class_id);	
			}
			else
			{
				$form->newlesson();			
			}
			
		
			$this->view->pageHeading = "Add New Lesson";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Add New Lesson';
			$student_arr=array();
			$student_arr=$this->modelStatic->Super_Get("users","user_id='".$student_id."'","fetch");
			$this->view->student_arr=$student_arr;		
		}		
		$this->view->form=$form;
		if ($this->getRequest()->isPost())
		{ // Post Form Data
			//prd($_SESSION['voice_recording_'.$this->view->user->user_id]);
				
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // If form is valids
			
				$apt    = new Zend_File_Transfer_Adapter_Http();
				$path =TEMP_PATH;
				$ffmpegPath = ROOT_PATH."/ffmpeg";
				/* Add Video Recording */
				$video_recording=array();
			
				if(isset($_POST['video_count']) && $_POST['video_count']>0)
				{
					for($i=1; $i<=$_POST['video_count']; $i++)
					{
				
						if(isset($_FILES['myvideo_'.$i]) && !empty($_FILES['myvideo_'.$i]))
						{
							//	prd("in");
							$apt    = new Zend_File_Transfer_Adapter_Http();
							$files  = $apt->getFileInfo('myvideo_'.$i);
							$filename=$_FILES['myvideo_'.$i]['name'];
							$filename1= str_replace(' ', '_', $filename);
							$path =TEMP_PATH.'/';
							$apt->setDestination($path);
							foreach($files as $file => $fileInfo) {
							if ($apt->isUploaded($file))
							{
								if ($apt->isValid($file)) {
									if ($apt->receive($file))
									 {
							//prd('fkldf');
										$info = $apt->getFileInfo($file);
										$size=($info[$file]['size'])/(1024*1024);
										
										if($size>150)
										{ 
											
											$objSession->errorMsg="Please upload maximum 150MB Size of file";
												if($this->view->user->user_type=='teacher')
											{
											$this->redirect("teacher/viewlessons");
											}
											else
											{
											$this->redirect("teacher/alllessons");
											}
											
										}
										else
										{
										$tmp  = $info[$file]['tmp_name'];
										$data = file_get_contents($tmp);
										$name=$info[$file]['name'];
										$newname=time()."_".$filename1;
										rename($path.$name,$path.$newname);
										$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
										$outputfile=$newname;
										/* Convert Video usinf FFmpeg */
										$filename=time();
										$uploadFileName=$path.$newname;
										$of=$path.'VID_'.$filename.'.mp4';
										$outputfile='VID_'.$filename.'.mp4';
											if($ext!='mp4')
											{
												//ultrafast
												$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";
												 $out=exec($cmd,$output,$ret);
											}
											else
											{ 
												//$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
												$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
												//$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -c:v libx264 -preset ultrafast   ".$of." 2>&1";
												$outputfile=$newname;
												$out=exec($cmd,$output,$ret);
											}
										
										
										 /* End Convert Video usinf FFmpeg */
											array_push($video_recording,$outputfile);
										}
									}
								}
							}
							}
						}
					}
				}
	
				/* End Video Recording plugin */
				/* Add Audio Recording */
				$audio_recording=array();
				if(isset($_POST['audio_count']) && $_POST['audio_count']>0)
				{
					for($i=1; $i<=$_POST['audio_count']; $i++)
					{
						if(isset($_FILES['myaudio_'.$i]) && !empty($_FILES['myaudio_'.$i]))
						{
							
							$apt    = new Zend_File_Transfer_Adapter_Http();
							$files  = $apt->getFileInfo('myaudio_'.$i);
							$filename=$_FILES['myaudio_'.$i]['name'];
							$filename1= str_replace(' ', '_', $filename);
							$path =TEMP_PATH.'/';
							$apt->setDestination($path);
							foreach($files as $file => $fileInfo) {
							if ($apt->isUploaded($file)) {
							if ($apt->isValid($file)) {
							if ($apt->receive($file)) {
							//prd('fkldf');
							$info = $apt->getFileInfo($file);
							if($size>150)
							{ 
											
											$objSession->errorMsg="Please upload maximum 270MB Size of file";
												if($this->view->user->user_type=='teacher')
											{
											$this->redirect("teacher/viewlessons");
											}
											else
											{
											$this->redirect("teacher/alllessons");
											}
											
							}
							else
							{
								$tmp  = $info[$file]['tmp_name'];
								$data = file_get_contents($tmp);
								$name=$info[$file]['name'];
								$newname=time()."_".$filename1;
								rename($path.$name,$path.$newname);
								$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
								$outputfile=$newname;
							/* Convert Video usinf FFmpeg */
								$filename=time();
								$uploadFileName=$path.$newname;
								$of=$path.'VID_'.$filename.'.mp4';
								$outputfile='VID_'.$filename.'.mp4';
								if($ext!='mp4')
								{
									
									$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";
									 $out=exec($cmd,$output,$ret);
									 
								}
								else
								{
									//$outputfile=$newname;
									$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
								//	$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
									$out=exec($cmd,$output,$ret);
								}
									array_push($audio_recording,$outputfile);
								 /* End Convert Video usinf FFmpeg */
							
							
							}
							
							}
							}
							}
							}
						}
					}
				}
				/* End Audio Recording plugin */
					$data_insert=$form->getValues();
				//	prd($data_insert);
					$save_template=0;
					$savesend=1;
					if(!empty($data_insert['param']) && $data_insert['param']==1)
					{
							/* Save Data as template */
							$save_template=1;
					}
					else if(!empty($data_insert['param']) && $data_insert['param']==2)
					{
						/* Save Data without submision */	
							$savesend=0;
					}
					else if(!empty($data_insert['param']) && $data_insert['param']==3)
					{
							/* Save Data as template */
							$save_template=1;
							/* Save Data without submision */	
							$savesend=0;
					}
					unset($data_insert['param']);
					unset($data_insert['existing_fold']);
					if(isset($lesson_data) && !empty($lesson_data))
					{
						//prd('fgvbn');
						if(isset($posted_data['delete_attach']) && !empty($posted_data['delete_attach']))
						{
						foreach($posted_data['delete_attach'] as $dal=>$val)
					{
					
						if($val!='')
						{
							$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$val.'"','fetch');
							$fdg=$this->modelStatic->Super_Delete("lession_attach",'la_id="'.$val.'"');
							unlink(TEMP_PATH.'/'.$lession_arr['la_name']);
							
							
						}
						
					}
						}
						$data_insert['lesson_status']=$savesend;
						
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						// ================================
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'"');
						$insert->inserted_id=$lesson_id;
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							//prd('gfvbnvb');
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								$leson_attache_file_arr=array();
								$leson_attache_file_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_id="'.$v.'"',"fetch");
								//prd($leson_attache_file_arr);
								copy(TEACHER_FILES_PATH.'/'.$leson_attache_file_arr['teacher_attach_name'],TEMP_PATH.'/'.$leson_attache_file_arr['teacher_attach_name']);
								//prd('fgvbnvn');
								$data=array();
								$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $leson_attache_file_arr['teacher_attach_name'],
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
								//prd($n);
							}
						}
						
						//existing folder
						
						
						
						/* Add Video Recording array to database */
						foreach($video_recording as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						/* Add Video recording array to database end */
						//audio and video
						
						/* Add Audio Recording array to database */
						foreach($audio_recording as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						/* Add Audio recording array to database end */
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$lesson_data['lesson_teacherid'].'/';	
						$files = scandir($path);
					
						$array=array();
				
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								$newname = str_replace(' ', '_', $newname);
								
								
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$newname);
									$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
									global $videoext;
									if(in_array($ext,$videoext))
									{
											$outputfile=$newname;
											/* Convert Video usinf FFmpeg */
											$filename=time();
											$uploadFileName=$path.$newname;
											$of=$path.'VID_'.$filename.'.mp4';
											$outputfile='VID_'.$filename.'.mp4';
											if($ext!='mp4')
											{
											//ultrafast
											$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";
											$out=exec($cmd,$output,$ret);
											}
											else
											{ 
											//$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
											$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
											//$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -c:v libx264 -preset ultrafast   ".$of." 2>&1";
											$outputfile=$newname;
											$out=exec($cmd,$output,$ret);
											}
											
											
											/* End Convert Video usinf FFmpeg */
											$newname=$outputfile;
											
									}
									array_push($array,$newname);
								}
								
								
								}
								}
						
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data);
						}
						//images
						
						
						$objSession->successMsg="Lesson has been updated Successfully";
					
					}
					else
					{
						$lesson_id='';
						if(isset($data_insert['lesson_notsaved_name']) && !empty($data_insert['lesson_notsaved_name']))
						{
							$lesson_id=$data_insert['lesson_notsaved_name'];	
						}
						$data_insert['lesson_template']=$save_template;
						$data_insert['lesson_status']=$savesend;
						//$data_insert['lesson_student_id']=$student_id;
						unset($data_insert['lesson_template_name']);
						unset($data_insert['lesson_type']);
						//unset($data_insert['lesson_notsaved_name']);
						unset($data_insert['lesson_old_name']);
						if(isset($lesson_id) && !empty($lesson_id))
						{
							//prd("if");
							
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							$data_insert['lesson_date']=date('Y-m-d H:i:s');
							// ================================
							$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'"');	
						}
						else
						{
							//prd('else');
						$data_insert['lesson_teacherid']=$user_param_id;
						$user_type=0;
						$school_id=$this->view->user->user_school_id;
						if($this->view->user->user_type=='school')
						{
							$user_type=1;
							$school_id=$this->view->user->user_id;
							/* This Lesson is added By School User */	
						}
						$data_insert['lesson_user_type']=$user_type;
						$data_insert['lesson_school_id']=$school_id;
						
						date_default_timezone_set('America/Los_Angeles');	// PDT time
//						$data_insert['lesson_date']=gmdate('Y-m-d H:i:s');
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						$data_insert['lesson_template']=$save_template;
						$data_insert['lesson_status']=$savesend;
						if(isset($class_id))
						{
							$data_insert['lesson_class_id']	=$class_id;
						}
						
					//	$data_insert['lesson_student_id']=$student_id;
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert);
						$lesson_id=$insert->inserted_id;
						}
						$lesson_updated_data=array();
						$lesson_updated_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch");
						/* Here we add lesson's student here */
							if(isset($student_id))
							{
								/* Student id */
								$student_lesson_data=$this->modelStatic->Super_Get("lesson_student","l_s_lessid='".$lesson_id."' and l_s_stuid='".$student_id."'","fetch");
								if(empty($student_lesson_data))
								{
									$lesson_data_arr=array(
										'l_s_lessid'=>$lesson_id,
										'l_s_stuid'=>$student_id,
										'l_s_teaherid'=>$user_param_id,
										'l_s_viewstatus'=>0,
										'l_s_addeddate'=>gmdate("Y-m-d H:i:s"),
									);
									if($this->view->user->user_type=='teacher')
									{
										$lesson_data_arr['l_s_usertype']=0;
									}
									else
									{
										$lesson_data_arr['l_s_usertype']=1;	
									}
									$resi=$this->modelStatic->Super_Insert("lesson_student",$lesson_data_arr);
									
								}
							}
							else
							{
								/* Class id */	
								if(isset($class_id))
								{
									$all_student_class=array();
									$all_student_class=$this->modelStatic->Super_Get("student_class","student_class_classid='".$class_id."'","fetchAll");	
									foreach($all_student_class as $k=>$v)
									{
										$get_lesson_student_data=array();
										if($this->view->user->user_type=='teacher')
										{
											$get_lesson_student_data=$this->modelStatic->Super_Get("lesson_student","l_s_stuid='".$v['student_class_studentid']."' and l_s_teaherid='".$this->view->user->user_id."' and l_s_lessid='".$lesson_id."'","fetch");	
										}
										else
										{
											$get_lesson_student_data=$this->modelStatic->Super_Get("lesson_student","l_s_stuid='".$v['student_class_studentid']."' and l_s_schoolid='".$this->view->user->user_id."' and l_s_lessid='".$lesson_id."'","fetch");	
										}

									if(empty($get_lesson_student_data))
									{
										$all_student_lesson_data=array('l_s_lessid'=>$lesson_id,
																		'l_s_stuid'=>$v['student_class_studentid'],
																		'l_s_teaherid'=>$user_param_id,
																		'l_s_viewstatus'=>0,
																		'l_s_addeddate'=>gmdate("Y-m-d H:i:s"),
																			);	
									if($this->view->user->user_type=='teacher')
									{
										$lesson_data_arr['l_s_usertype']=0;
										$lesson_data_arr['l_s_schoolid']=$this->view->user->user_school_id;
									}
									else
									{
										if(isset($teacher_id))
										{
											
											$lesson_data_arr['l_s_usertype']=0;	
      										$lesson_data_arr['l_s_schoolid']=$this->view->user->user_id;		
										}
										else
										{
		  									$lesson_data_arr['l_s_usertype']=1;	
      										$lesson_data_arr['l_s_schoolid']=$this->view->user->user_id;	
										}
										
									}
										$this->modelStatic->Super_Insert("lesson_student",$all_student_lesson_data);
									}
									}
								}
								
							}
						
						/* Here we end lesson's student. */
						//existing folder
						
						$this->modelStatic->Super_Delete("lession_attach",'la_lesson_id="'.$lesson_id.'"');
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							//prd('gfvbnvb');
							//prd($posted_data['existing_fold']);
							
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								$leson_attache_file_arr=array();
								$leson_attache_file_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_id="'.$v.'"',"fetch");
								//prd($leson_attache_file_arr);
								copy(TEACHER_FILES_PATH.'/'.$leson_attache_file_arr['teacher_attach_name'],TEMP_PATH.'/'.$leson_attache_file_arr['teacher_attach_name']);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $leson_attache_file_arr['teacher_attach_name'],
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
								//prd($n);
							}
						}
					
						if(isset($posted_data['upload_attach']) && !empty($posted_data['upload_attach']))
						{
							foreach($posted_data['upload_attach'] as $k=>$v)
							{
								$get_attach_arr=array();
								$get_attach_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$v.'"',"fetch");
								$ext='';
								$ext = pathinfo($get_attach_arr['la_name'], PATHINFO_EXTENSION);
								$new_nme=time().$get_attach_arr['la_name'];
								copy(TEMP_PATH.'/'.$get_attach_arr['la_name'],TEMP_PATH.'/'.$new_nme);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $new_nme,
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							}	
						}
						//existing folder
				
						/* Add Video Recording array to database */
						foreach($video_recording as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						/* Add Video recording array to database end */
						//audio and video
						
						/* Add Audio Recording array to database */
						foreach($audio_recording as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						/* Add Audio recording array to database end */
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$lesson_updated_data['lesson_teacherid'].'/';	
						$files = scandir($path);
						$array=array();
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								$newname = str_replace(' ', '_', $newname);
								
								
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$newname);
									$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
									global $videoext;
									if(in_array($ext,$videoext))
									{
											$outputfile=$newname;
											/* Convert Video usinf FFmpeg */
											$filename=time();
											$uploadFileName=$path.$newname;
											$of=$path.'VID_'.$filename.'.mp4';
											$outputfile='VID_'.$filename.'.mp4';
											if($ext!='mp4')
											{
											//ultrafast
											$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";
											$out=exec($cmd,$output,$ret);
											}
											else
											{ 
											//$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
											$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
											//$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -c:v libx264 -preset ultrafast   ".$of." 2>&1";
											$outputfile=$newname;
											$out=exec($cmd,$output,$ret);
											}
											
											
											/* End Convert Video usinf FFmpeg */
											$newname=$outputfile;
											
									}
									array_push($array,$newname);
								}
								
								
								}
								}
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data);
						}
						//images
						
					
						
					
					
						$objSession->successMsg="Lesson has been added Successfully";
					}
					
						if($savesend==1)
						{
							/* here mail will send to students */
							$get_student_arr_lesson=array();
							if($this->view->user->user_type=='teacher')
										{
							$get_student_arr_lesson=$this->modelStatic->Super_Get("lesson_student","l_s_teaherid='".$user_param_id."' and l_s_lessid='".$lesson_id."'","fetchAll");
										}
										else
										{
							$get_student_arr_lesson=$this->modelStatic->Super_Get("lesson_student","l_s_schoolid='".$user_param_id."' and l_s_lessid='".$lesson_id."'","fetchAll");
												
										}
							foreach($get_student_arr_lesson as $k=>$v)
							{
								$student_data=array();
								$student_data=$this->modelStatic->Super_Get("users","user_id='".$v['l_s_stuid']."'",'fetch');
								if(!empty($student_data))
								{
									$data_mail['user_name']=$student_data['user_first_name'].' '.$student_data['user_last_name'];	
									$data_mail['Lesson_name']=$data_insert['lesson_title'];
									$data_mail['Teacher_name']=$this->view->user->user_first_name.' '.$this->view->user->user_last_name;
									$data_mail['user_email']=$student_data['user_email'];
									$EmailModel = new Application_Model_Email();
									if($v['l_s_mail']==0)
									{
										/* Mail is not sent yet */
										if($student_data['user_email_option']==1)
										{
										/* Send notifications to Family Contacts email only */	
											$get_family_arr=array();
											$get_family_arr=$this->modelStatic->Super_Get("student_family",'s_f_sid="'.$v['l_s_stuid'].'"',"fetch");		
											if(!empty($get_family_arr))
											{
												$family_mail=array();
												$family_data_mail=array();
												$family_data=$this->modelStatic->Super_Get("users","user_id='".$get_family_arr['s_f_fid']."'","fetch");
												$family_data_mail['user_name']=$family_data['user_first_name'].''.$family_data['user_last_name'];	
												$family_data_mail['Lesson_name']=$data_insert['lesson_title'];
												$family_data_mail['Student_name']=$student_data['user_first_name'].' '.$student_data['user_last_name'];
												$family_data_mail['Teacher_name']=$this->view->user->user_first_name.' '.$this->view->user->user_last_name;
												$family_data_mail['user_email']=$family_data['user_email'];
												$EmailModel->sendEmail("lesson_email_family",$family_data_mail);
				
												
											}
									}
										else if($student_data['user_email_option']==2)
										{
										/* Send notifications to Student only */
									
											if(!empty($data_mail['user_email']))
											{
											
												$kk=$EmailModel->sendEmail("lesson_email",$data_mail);
												
											}
									}
										else if($student_data['user_email_option']==3)
										{
										/* Send notifications to both Student and Family Contact */	
											if(!empty($data_mail['user_email']))
											{
												$EmailModel->sendEmail("lesson_email",$data_mail);
											}
											$get_family_arr=array();
											$get_family_arr=$this->modelStatic->Super_Get("student_family",'s_f_sid="'.$v['l_s_stuid'].'"',"fetch");		
											if(!empty($get_family_arr))
											{
												$family_mail=array();
												$family_data_mail=array();
												$family_data=$this->modelStatic->Super_Get("users","user_id='".$get_family_arr['s_f_fid']."'","fetch");
												$family_data_mail['user_name']=$family_data['user_first_name'].''.$family_data['user_last_name'];	
												$family_data_mail['Lesson_name']=$data_insert['lesson_title'];
												$family_data_mail['Student_name']=$student_data['user_first_name'].' '.$student_data['user_last_name'];
												$family_data_mail['Teacher_name']=$this->view->user->user_first_name.' '.$this->view->user->user_last_name;
												$family_data_mail['user_email']=$family_data['user_email'];
												$EmailModel->sendEmail("lesson_email_family",$family_data_mail);
				
												
											}
									}
										else
										{
										/* No Email */	
										}
										$this->modelStatic->Super_Insert("lesson_student",array("l_s_mail"=>1),"l_s_stuid='".$v['l_s_stuid']."'");
									}
									
									
								}
							}
																
						}
					if($this->view->user->user_type=='teacher')
					{
							$this->redirect("teacher/viewlessons");
					}
					else
					{
							$this->redirect("teacher/alllessons");
					}
					
					
				}
			}
			
			else
			{
				if(is_dir(TEMP_PATH.'/voice_'.$this->view->user->user_id))
			{
				DeleteDirfileupload(TEMP_PATH.'/voice_'.$this->view->user->user_id);
			}
			if(is_dir(TEMP_PATH.'/upimage_'.$this->view->user->user_id))
			{
				DeleteDirfileupload(TEMP_PATH.'/upimage_'.$this->view->user->user_id);
			}
				
				
			}
			
	}
	
	public function newlessonAction()
	{
		global $objSession ; 
		$modelSchool = new Application_Model_SchoolModel();
		$student_id=$this->getRequest()->getParam('student_id');
		$class_id=$this->getRequest()->getParam('class_id');
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$class_data=array();
		$lesson_class=array();
		$class_data_arr=array();
		$user_param_id=$this->view->user->user_id;
		if(isset($teacher_id))
		{
			$user_param_id=$teacher_id;	
		}
		if(isset($class_id))
		{
			if($this->view->user->user_type=='teacher')
			{
				/* Teacher User */
				//$class_data=$this->modelStatic->Super_Get("teacher_classes","teacher_class_classid='".$class_id."' and teacher_class_userid='".$user_param_id."'","fetch");
				$class_data=$this->modelStatic->Super_Get("Classes","class_id='".$class_id."'","fetch");
			}
			else
			{
				/* School User */
				$class_data=$this->modelStatic->Super_Get("Classes","class_id='".$class_id."' and class_school_id='".$user_param_id."'","fetch");
				if(empty($class_data))
				{
						$objSession->errorMsg="Invalid Request for teacher";	
						$this->redirect("profile/dashboard");
				}
			}
			$this->view->class_data=$class_data;
			$this->view->class_id=$class_id;
			$class_data_arr=$this->modelStatic->Super_Get("lesson","lesson_class_id='".$class_id."'","fetchAll",array("pagination"=>1,"order"=>array("lesson_date DESC")));
			
			/* Start All Lesson of particular class */
			$page1=1;
			$page1=$this->_getParam('page1');
			if(!isset($_REQUEST['record_per_page1']))
			$_REQUEST['record_per_page1']=10;
			$paginator1=$this->pagination($class_data_arr,$page1,$_REQUEST['record_per_page1']);
			$this->view->paginator1=$paginator1;
			/* End All Lesson of particular class */
		}
		$this->view->class_data_arr=$class_data_arr;
		$lesson_data=array();
		$student_name_str='';
		if($this->view->user->user_type=='teacher')
		{
			$document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_userid="'.$user_param_id.'"','fetchAll');
			$students_array=$this->modelStatic->Super_Get("private_teacher","private_teacher_teacherid='".$user_param_id."'","fetch",array("fields"=>array("GROUP_CONCAT(private_teacher_studentid) as students")));
			
			if(!empty($students_array['students']))
			{
			$student_name_arr=$this->modelStatic->Super_Get("users","user_id IN (".$students_array["students"].")","fetch",array("fields"=>array("GROUP_CONCAT(user_first_name) as name")));
			if(!empty($student_name_arr['name']))
			{
				$student_name_str=	$student_name_arr['name'];
			}
		}	
		}
		else
		{
			$document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_schoolid="'.$user_param_id.'"','fetchAll');	
			$student_name_arr=$this->modelStatic->Super_Get("users","user_type='student' and user_school_id='".$user_param_id."'","fetch",array("fields"=>array("GROUP_CONCAT(user_first_name) as name")));
			if(!empty($student_name_arr['name']))
			{
				$student_name_str=	$student_name_arr['name'];
			}
		}
		
		$this->view->student_name_str=$student_name_str;
		/* Param=1(Save As Temaplate) Param=2(Save without sending) */
		$this->view->document_arr=$document_arr;
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$student_arr=array();
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->lesson_id=$lesson_id;	
			$lesson_show_arr=$this->modelStatic->Super_Get("lesson",'lesson_id="'.$lesson_id.'"',"fetch",array("order"=>array("lesson_date DESC")));
		//	$student_arr=$this->modelStatic->Super_Get("users","user_id='".$lesson_show_arr['lesson_student_id']."'","fetch");
			
		}
		else
		{
			if(isset($student_id))
			{
			
		$student_arr=$this->modelStatic->Super_Get("users","user_id='".$student_id."'","fetch");
			/* All Lesson data with  Pagination */
		$this->view->student_arr=$student_arr;	
		$get_all_lessons=$modelSchool->userlessonpagination($student_arr['user_id'],$user_param_id);
		$page=1;
		$page=$this->_getParam('page');
		if(!isset($_REQUEST['record_per_page']))
		$_REQUEST['record_per_page']=10;
		$paginator=$this->pagination($get_all_lessons,$page,$_REQUEST['record_per_page']);
		$this->view->paginator=$paginator;
		/* End All Lesson data with  Pagination */
			}
		}
		
		$user_agent = new Zend_Http_UserAgent();
	 	$agent = $user_agent->getDevice();
		$is_mobile=0;
		if("Zend_Http_UserAgent_Mobile"==get_class($agent))
		{
			$is_mobile=1;	
		}
		$this->view->is_mobile=$is_mobile;
		$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_lesson_id="'.$lesson_id.'" ','fetchAll');
		$this->view->lession_arr=$lession_arr;
		$this->view->lesson_id=$lesson_id;
		$this->view->student_id=$student_id;
		$form = new Application_Form_SchoolForm();
		if(isset($lesson_id) && !empty($lesson_id))
		{
			if(isset($student_id))
			{
				$form->newlesson(false,$lesson_id,$student_id);
			}
			else if(isset($class_id))
			{
					$form->newlesson(false,$lesson_id,false,$class_id);	
					$this->view->class_id=$class_id;
			}
			else
			{
				$form->newlesson('',$lesson_id);	
			}
		}
		else
		{
			if(isset($student_id))
			{
				$form->newlesson(false,false,$student_id);		
			}
			else if(isset($class_id))
			{
				$form->newlesson(false,false,false,$class_id);		
				$this->view->class_id=$class_id;	
			}
			else
			{
				$form->newlesson();			
			}
			
		}
		
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->pageHeading = "Update Lesson";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Update Lesson';
			/* Get Lesson Data */
			$lesson_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch",array("order"=>array("lesson_date DESC")));	
			$this->view->lesson_data=$lesson_data;
			$form->populate($lesson_data);
		}
		else
		{
			$this->view->pageHeading = "Add New Lesson";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Add New Lesson';	
		}		
		$this->view->form=$form;
		$this->view->student_id=$student_id;
		
		require_once ROOT_PATH.'/private/ZiggeoPhpSdk-master/Ziggeo.php';
		$ziggeo = new Ziggeo($this->view->config_data['ziggeo_token'],$this->view->config_data['ziggeo_private_key'],$this->view->config_data['ziggeo_encryption_key']);	
		
			if ($this->getRequest()->isPost())
			{ // Post Form Data
			//prd($_SESSION['voice_recording_'.$this->view->user->user_id]);
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // If form is valids
					$data_insert=$form->getValues();
				
					$save_template=0;
					if(isset($posted_data['video_token']))
					{
						$videotokenarr=$posted_data['video_token'];
					}
					$ffmpegPath = ROOT_PATH."/ffmpeg";
					$savesend=1;
					if(!empty($data_insert['param']) && $data_insert['param']==1)
					{
							/* Save Data as template */
							$save_template=1;
					}
					else if(!empty($data_insert['param']) && $data_insert['param']==2)
					{
						/* Save Data without submision */	
							$savesend=0;
					}
					else if(!empty($data_insert['param']) && $data_insert['param']==3)
					{
							/* Save Data as template */
							$save_template=1;
							/* Save Data without submision */	
							$savesend=0;
					}
					unset($data_insert['param']);
					unset($data_insert['existing_fold']);
				
					if(isset($lesson_data) && !empty($lesson_data))
					{
						//prd('fgvbn');
						if(isset($posted_data['delete_attach']) && !empty($posted_data['delete_attach']))
						{
							foreach($posted_data['delete_attach'] as $dal=>$val)
							{
					
						if($val!='')
						{
							$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$val.'"','fetch');
							//prd($lession_arr);
							$fdg=$this->modelStatic->Super_Delete("lession_attach",'la_id="'.$val.'"');
							if($lession_arr['la_type']==1)
							{
								unlink(TEMP_PATH.'/'.$lession_arr['la_name']);	
							}
							else
							{	
								//$ziggeo->videos()->delete($lession_arr['la_token']);	
								//prd("jklj");
							}
							
							
							
						}
						
					}
						}
						
						$data_insert['lesson_status']=$savesend;
						
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						// ================================
						// =================	Modify	======
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'"');
						$insert->inserted_id=$lesson_id;
							
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							//prd('gfvbnvb');
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								$leson_attache_file_arr=array();
								$leson_attache_file_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_id="'.$v.'"',"fetch");
								//prd($leson_attache_file_arr);
								copy(TEACHER_FILES_PATH.'/'.$leson_attache_file_arr['teacher_attach_name'],TEMP_PATH.'/'.$leson_attache_file_arr['teacher_attach_name']);
								//prd('fgvbnvn');
								$data=array();
								$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $leson_attache_file_arr['teacher_attach_name'],
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
								//prd($n);
							}
						}
						
						//existing folder
						
						//audio and video
						
						$path =TEMP_PATH.'/voice_'.$lesson_data['lesson_teacherid'].'/';	
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
									
									rename($path."/".$file,TEMP_PATH."/".$file);
									echo "Here";
								}
								}
						}
						
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prn($n);
							
						}
						//prd($array);
						//audio and video
						//images
						$path =TEMP_PATH.'/upimage_'.$lesson_data['lesson_teacherid'].'/';	
						$files = scandir($path);
						$array=array();
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								$newname = str_replace(' ', '_', $newname);
								
								$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
								$filexp=explode(".".$ext,$file);
								$newname=$filexp[0].time().".".$ext;
							
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$newname);
									$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
									global $videoext;
								
									if(in_array($ext,$videoext) )
									{
											///prd("if in yes");
											$outputfile=$newname;
											/* Convert Video usinf FFmpeg */
											$filename=time();
											$uploadFileName=TEMP_PATH."/".$newname;
											$of=TEMP_PATH."/".'VID_'.$filename.'.mp4';
											$outputfile='VID_'.$filename.'.mp4';
											
											if($ext!='mp4')
											{
											//ultrafast
											$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";										$out=exec($cmd,$output,$ret);
												
											}
											else
											{ 
											//$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
											$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
											//$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -c:v libx264 -preset ultrafast   ".$of." 2>&1";
											$outputfile=$newname;
											$out=exec($cmd,$output,$ret);
												
											}
										//	prd("in");
											/* End Convert Video using FFmpeg */
											$newname=$outputfile;
									}
									else
									{
										
									}
									array_push($array,$newname);
								}
								}
								}
						foreach($array as $k=>$v)
						{
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data);
						}
						// These are the videos recorded by ziggeo recorder
						//	$this->modelStatic->Super_Delete("lession_attach",'la_lesson_id="'.$lesson_id.'" and la_type="0"');
						foreach($videotokenarr as $k=>$v)
						{
							$lesson_attach_arr=array();
							$lesson_attach_arr=$this->modelStatic->Super_Get("lession_attach","la_lesson_id='".$insert->inserted_id."' and la_token='".$v."'","fetch");
							if(empty($lesson_attach_arr))
							{
								$video_name='Video_'.($k+1).'_'.time();
								$data_add=array();
								$data_add=array('la_lesson_id'=>$insert->inserted_id,
											'la_name' => $video_name,
											'la_type'=>0,
											'la_token'=>$v
								);	
								$this->modelStatic->Super_Insert("lession_attach",$data_add);	
							}
						}
						$objSession->successMsg="Lesson has been updated Successfully";
					}
					else
					{
						$lesson_id='';
						if(isset($data_insert['lesson_notsaved_name']) && !empty($data_insert['lesson_notsaved_name']))
						{
							$lesson_id=$data_insert['lesson_notsaved_name'];	
						}
						$data_insert['lesson_template']=$save_template;
						$data_insert['lesson_status']=$savesend;
						//$data_insert['lesson_student_id']=$student_id;
						unset($data_insert['lesson_template_name']);
						unset($data_insert['lesson_type']);
					//	unset($data_insert['lesson_notsaved_name']);
						unset($data_insert['lesson_old_name']);
						$student_class='';
						if(isset($data_insert['student_class']))
						{
							$student_class=$data_insert['student_class'];
							unset($data_insert['student_class']);
						}
						if(isset($lesson_id) && !empty($lesson_id))
						{
							//prd("if");
							
							// ================ add  ==========
							date_default_timezone_set('America/Los_Angeles');	// PDT time
							$data_insert['lesson_date']=date('Y-m-d H:i:s');
							// ================================
						
							$bb=$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'"');	
						
						}
						else
						{
							//prd('else');
						$data_insert['lesson_teacherid']=$user_param_id;
						$user_type=0;
						$school_id=$this->view->user->user_school_id;
						if($this->view->user->user_type=='school')
						{
							$user_type=1;
							$school_id=$this->view->user->user_id;
							/* This Lesson is added By School User */	
						}
						$data_insert['lesson_user_type']=$user_type;
						$data_insert['lesson_school_id']=$school_id;
						
						date_default_timezone_set('America/Los_Angeles');	// PDT time
//						$data_insert['lesson_date']=gmdate('Y-m-d H:i:s');
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						$data_insert['lesson_template']=$save_template;
						$data_insert['lesson_status']=$savesend;
						
						if(isset($class_id))
						{
							$data_insert['lesson_class_id']	=$class_id;
						}
						
					//	$data_insert['lesson_student_id']=$student_id;
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert);
						$lesson_id=$insert->inserted_id;
						}
						$lesson_updated_data=array();
						$lesson_updated_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch");
						/* Here we add lesson's student here */
							if(isset($student_id))
							{
								/* Student id */
								$student_lesson_data=$this->modelStatic->Super_Get("lesson_student","l_s_lessid='".$lesson_id."' and l_s_stuid='".$student_id."'","fetch");
								if(empty($student_lesson_data))
								{
									$lesson_data_arr=array(
										'l_s_lessid'=>$lesson_id,
										'l_s_stuid'=>$student_id,
										'l_s_teaherid'=>$user_param_id,
										'l_s_viewstatus'=>0,
										'l_s_addeddate'=>gmdate("Y-m-d H:i:s"),
									);
									if($this->view->user->user_type=='teacher')
									{
										$lesson_data_arr['l_s_usertype']=0;
									}
									else
									{
										$lesson_data_arr['l_s_usertype']=1;	
									}
									$resi=$this->modelStatic->Super_Insert("lesson_student",$lesson_data_arr);
									
								}
							}
							else
							{
								/* Class id */	
								if(isset($class_id))
								{
									$all_student_class=array();
									$all_student_class=$this->modelStatic->Super_Get("student_class","student_class_classid='".$class_id."'","fetchAll");
									if(!empty($student_class))
									{
										foreach($student_class as $k=>$v)
										{
										
										$get_lesson_student_data=array();
										if($this->view->user->user_type=='teacher')
										{
											$get_lesson_student_data=$this->modelStatic->Super_Get("lesson_student","l_s_stuid='".$v."' and l_s_teaherid='".$this->view->user->user_id."' and l_s_lessid='".$lesson_id."'","fetch");	
										}
										else
										{
											$get_lesson_student_data=$this->modelStatic->Super_Get("lesson_student","l_s_stuid='".$v."' and l_s_schoolid='".$this->view->user->user_id."' and l_s_lessid='".$lesson_id."'","fetch");	
										}

										if(empty($get_lesson_student_data))
										{
										$all_student_lesson_data=array('l_s_lessid'=>$lesson_id,
																		'l_s_stuid'=>$v,
																		'l_s_teaherid'=>$user_param_id,
																		'l_s_viewstatus'=>0,
																		'l_s_addeddate'=>gmdate("Y-m-d H:i:s"),
																			);	
									if($this->view->user->user_type=='teacher')
									{
										$lesson_data_arr['l_s_usertype']=0;
										$lesson_data_arr['l_s_schoolid']=$this->view->user->user_school_id;
									}
									else
									{
										if(isset($teacher_id))
										{
											
											$lesson_data_arr['l_s_usertype']=0;	
      										$lesson_data_arr['l_s_schoolid']=$this->view->user->user_id;		
										}
										else
										{
		  									$lesson_data_arr['l_s_usertype']=1;	
      										$lesson_data_arr['l_s_schoolid']=$this->view->user->user_id;	
										}
										
									}
										$this->modelStatic->Super_Insert("lesson_student",$all_student_lesson_data);
									}
									
									}
									}
								}
								
							}
						
						/* Here we end lesson's student. */
						
					
						//existing folder
						
						$this->modelStatic->Super_Delete("lession_attach",'la_lesson_id="'.$lesson_id.'"');
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							//prd('gfvbnvb');
							//prd($posted_data['existing_fold']);
							
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								$leson_attache_file_arr=array();
								$leson_attache_file_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_id="'.$v.'"',"fetch");
								//prd($leson_attache_file_arr);
								copy(TEACHER_FILES_PATH.'/'.$leson_attache_file_arr['teacher_attach_name'],TEMP_PATH.'/'.$leson_attache_file_arr['teacher_attach_name']);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $leson_attache_file_arr['teacher_attach_name'],
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
								//prd($n);
							}
						}
					
						if(isset($posted_data['upload_attach']) && !empty($posted_data['upload_attach']))
						{
							foreach($posted_data['upload_attach'] as $k=>$v)
							{
								$get_attach_arr=array();
								$get_attach_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$v.'"',"fetch");
								$ext='';
								$ext = pathinfo($get_attach_arr['la_name'], PATHINFO_EXTENSION);
								$new_nme=time().$get_attach_arr['la_name'];
								
								
								$filexp=explode(".".$ext,$get_attach_arr['la_name']);
								$new_nme=$filexp[0].time().".".$ext;
								
								
								copy(TEMP_PATH.'/'.$get_attach_arr['la_name'],TEMP_PATH.'/'.$new_nme);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $new_nme,
									
							);	
								$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							}	
						}
						//existing folder
				
						//audio and video
						$path =TEMP_PATH.'/voice_'.$lesson_updated_data['lesson_teacherid'].'/';	
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
										rename($path."/".$file,TEMP_PATH."/".$file);
									}
								}
						}
						//prd($array);		
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						//audio and video
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$lesson_updated_data['lesson_teacherid'].'/';	
						$files = scandir($path);
						$array=array();
				
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								$newname = str_replace(' ', '_', $newname);
								
								$ext = pathinfo($path.$file, PATHINFO_EXTENSION);
								$filexp=explode(".".$ext,$file);
								$newname=$filexp[0].time().".".$ext;
								
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$newname);
									$ext = pathinfo($path.$newname, PATHINFO_EXTENSION);
									global $videoext;
									
									if(in_array($ext,$videoext))
									{
									
											$outputfile=$newname;
											/* Convert Video usinf FFmpeg */
											$filename=time();
											$uploadFileName=TEMP_PATH."/".$newname;
											$of=TEMP_PATH."/".'VID_'.$filename.'.mp4';
											$outputfile='VID_'.$filename.'.mp4';
											if($ext!='mp4')
											{
											//ultrafast
											$cmd=$ffmpegPath." -i ".$uploadFileName."  -c:v libx264 -preset ultrafast -crf 22 -pix_fmt yuv420p -c:a libvo_aacenc -b:a 119k  ".$of." 2>&1";
											$out=exec($cmd,$output,$ret);
											
											}
											else
											{ 
											//$cmd=$ffmpegPath." -i ".$uploadFileName."  -b:a 119k  ".$of." 2>&1";
											$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -vcodec libx264 -preset ultrafast  ".$of." 2>&1";
											//$cmd=$ffmpegPath." -i ".$uploadFileName." -ar 22050 -ab 32 -f mp4 -s 640x480 -c:v libx264 -preset ultrafast   ".$of." 2>&1";
											$outputfile=$newname;
											$out=exec($cmd,$output,$ret);
											
											}
											
											/* End Convert Video usinf FFmpeg */
											$newname=$outputfile;
											
									}
									array_push($array,$newname);
								}
								
								
								}
								}
						foreach($array as $k=>$v)
						{
						
							$data=array('la_lesson_id'=>$lesson_id,
										'la_name' => $v,
									
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data);
						}
						//images
					
						foreach($videotokenarr as $k=>$v)
						{
							$video_name='Video_'.($k+1).'_'.time();
							$data_add=array();
							$data_add=array(
										'la_lesson_id'=>$lesson_id,
										'la_name' => $video_name,
										'la_type'=>0,
										'la_token'=>$v
							);	
							$this->modelStatic->Super_Insert("lession_attach",$data_add);	
						}
						$objSession->successMsg="Lesson has been added Successfully";
					}
					if($savesend==1)
					{
							/* here mail will send to students */
							$get_student_arr_lesson=array();
							if($this->view->user->user_type=='teacher')
							{
								$get_student_arr_lesson=$this->modelStatic->Super_Get("lesson_student","l_s_teaherid='".$user_param_id."' and l_s_lessid='".$lesson_id."'","fetchAll");
							}
							else
							{
								$get_student_arr_lesson=$this->modelStatic->Super_Get("lesson_student","l_s_schoolid='".$user_param_id."' and l_s_lessid='".$lesson_id."'","fetchAll");
							}
							foreach($get_student_arr_lesson as $k=>$v)
							{
							$student_data=array();
							$student_data=$this->modelStatic->Super_Get("users","user_id='".$v['l_s_stuid']."'",'fetch');
							if($v['l_s_mail']==0)
							{
								if(!empty($student_data))
								{
									$data_mail['user_name']=$student_data['user_first_name'].' '.$student_data['user_last_name'];	
									$data_mail['Lesson_name']=$data_insert['lesson_title'];
									$data_mail['Teacher_name']=$this->view->user->user_first_name.' '.$this->view->user->user_last_name;
									$data_mail['user_email']=$student_data['user_email'];
									$EmailModel = new Application_Model_Email();
									if($student_data['user_email_option']==1)
									{
										/* Send notifications to Family Contacts email only */	
											$get_family_arr=array();
											$get_family_arr=$this->modelStatic->Super_Get("student_family",'s_f_sid="'.$v['l_s_stuid'].'"',"fetch");		
											if(!empty($get_family_arr))
											{
												$family_mail=array();
												$family_data_mail=array();
												$family_data=$this->modelStatic->Super_Get("users","user_id='".$get_family_arr['s_f_fid']."'","fetch");
												$family_data_mail['user_name']=$family_data['user_first_name'].''.$family_data['user_last_name'];	
												$family_data_mail['Lesson_name']=$data_insert['lesson_title'];
												$family_data_mail['Student_name']=$student_data['user_first_name'].' '.$student_data['user_last_name'];
												$family_data_mail['Teacher_name']=$this->view->user->user_first_name.' '.$this->view->user->user_last_name;
												$family_data_mail['user_email']=$family_data['user_email'];
												$EmailModel->sendEmail("lesson_email_family",$family_data_mail);
				
												
											}
									}
									else if($student_data['user_email_option']==2)
									{
										/* Send notifications to Student only */
									
											if(!empty($data_mail['user_email']))
											{
											
												$kk=$EmailModel->sendEmail("lesson_email",$data_mail);
												
											}
									}
									else if($student_data['user_email_option']==3)
									{
										/* Send notifications to both Student and Family Contact */	
											if(!empty($data_mail['user_email']))
											{
												$EmailModel->sendEmail("lesson_email",$data_mail);
											}
											$get_family_arr=array();
											$get_family_arr=$this->modelStatic->Super_Get("student_family",'s_f_sid="'.$v['l_s_stuid'].'"',"fetch");		
											if(!empty($get_family_arr))
											{
												$family_mail=array();
												$family_data_mail=array();
												$family_data=$this->modelStatic->Super_Get("users","user_id='".$get_family_arr['s_f_fid']."'","fetch");
												$family_data_mail['user_name']=$family_data['user_first_name'].''.$family_data['user_last_name'];	
												$family_data_mail['Lesson_name']=$data_insert['lesson_title'];
												$family_data_mail['Student_name']=$student_data['user_first_name'].' '.$student_data['user_last_name'];
												$family_data_mail['Teacher_name']=$this->view->user->user_first_name.' '.$this->view->user->user_last_name;
												$family_data_mail['user_email']=$family_data['user_email'];
												$EmailModel->sendEmail("lesson_email_family",$family_data_mail);
				
												
											}
									}
									else
									{
										/* No Email */	
									}
									
										$this->modelStatic->Super_Insert("lesson_student",array("l_s_mail"=>1),"l_s_stuid='".$v['l_s_stuid']."'");
								}
									}
							}
																
						}
						
					if($this->view->user->user_type=='teacher')
					{
							$this->redirect("teacher/viewlessons");
					}
					else
					{
							$this->redirect("teacher/alllessons");
					}
					
					
				}
			}
			else
			{
					if(is_dir(TEMP_PATH.'/voice_'.$user_param_id))
					{
						DeleteDirfileupload(TEMP_PATH.'/voice_'.$this->view->user->user_id);
					}
					if(is_dir(TEMP_PATH.'/upimage_'.$user_param_id))
					{
						DeleteDirfileupload(TEMP_PATH.'/upimage_'.$user_param_id);
			  		}
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

		

		$paginationControl = new Zend_View_Helper_PaginationControl($paginator, 'sliding', 'search-pagination-control.phtml');

		$this->view->paginationControl=$paginationControl;

		return $paginator;

	}
	/* Remove Lesson */
	public function removelessonAction()
	{
			global $objSession ; 
			$lesson_id=$this->getRequest()->getParam('lesson_id');
			$lesson_data=array();
			$lesson_data=$this->modelStatic->Super_Get("lesson",'lesson_id="'.$lesson_id.'"',"fetch");
			$lesson_attach_arr=array();
			$lesson_attach_arr=$this->modelStatic->Super_Get("lession_attach","la_lesson_id='".$lesson_id."'","fetchAll");
			$this->modelStatic->Super_Delete("lession_attach","la_lesson_id='".$lesson_id."'");
			$this->modelStatic->Super_Delete("lesson",'lesson_id="'.$lesson_id.'"');
			$this->modelStatic->Super_Delete("lesson_student","l_s_lessid='".$lesson_id."'");
			foreach($lesson_attach_arr as $k=>$v)
			{
				if(file_exists(TEMP_PATH."/".$v['la_name']))
					{
						unlink(TEMP_PATH.'/'.$v['la_name']);	
					}
					if(file_exists(TEMP_PATH."/voice_".$lesson_data['lesson_teacherid'].'/'.$v['la_name']))
					{
							unlink(TEMP_PATH."/voice_".$lesson_data['lesson_teacherid'].'/'.$v['la_name']);	
					}
					if(file_exists(TEMP_PATH."/upimage_".$lesson_data['lesson_teacherid'].'/'.$v['la_name']))
					{
							unlink(TEMP_PATH."/upimage_".$lesson_data['lesson_teacherid'].'/'.$v['la_name']);	
					}
			}
			$objSession->successMsg="Lesson has been removed successfully";
			if($this->view->user->user_type=='teacher')
			{
				$this->redirect("teacher/viewlessons");	
			}
			else
			{
				$this->redirect("teacher/alllessons");	
			}
			
	}
	
	/* Remove Template Lesson */
	public function removetemplatelessonAction()
	{
			global $objSession ; 
			$lesson_id=$this->getRequest()->getParam('lesson_id');
			$lesson_data=array();
			$lesson_data=$this->modelStatic->Super_Get("lesson",'lesson_id="'.$lesson_id.'"',"fetch");
			$lesson_attach_arr=array();
			$lesson_attach_arr=$this->modelStatic->Super_Get("lession_attach","la_lesson_id='".$lesson_id."'","fetchAll");
			$this->modelStatic->Super_Delete("lession_attach","la_lesson_id='".$lesson_id."'");
			$this->modelStatic->Super_Delete("lesson",'lesson_id="'.$lesson_id.'"');
			$this->modelStatic->Super_Delete("lesson_student","l_s_lessid='".$lesson_id."'");
			foreach($lesson_attach_arr as $k=>$v)
			{
				if(file_exists(TEMP_PATH."/".$v['la_name']))
					{
						unlink(TEMP_PATH.'/'.$v['la_name']);	
					}
					if(file_exists(TEMP_PATH."/voice_".$lesson_data['lesson_teacherid'].'/'.$v['la_name']))
					{
							unlink(TEMP_PATH."/voice_".$lesson_data['lesson_teacherid'].'/'.$v['la_name']);	
					}
					if(file_exists(TEMP_PATH."/upimage_".$lesson_data['lesson_teacherid'].'/'.$v['la_name']))
					{
							unlink(TEMP_PATH."/upimage_".$lesson_data['lesson_teacherid'].'/'.$v['la_name']);	
					}
			}
			$objSession->successMsg="Lesson has been removed successfully";
			if($this->view->user->user_type=='teacher')
			{
				$this->redirect("teacher/viewtemplates");	
			}
			else
			{
				$this->redirect("teacher/alltemplates");	
			}
			
	}
	
	/* View All Lessons */
	public function viewtemplatesAction()
	{
		global $objSession ; 
		$modelSchool = new Application_Model_SchoolModel();
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$user_param_id=$this->view->user->user_id;
		
		if(isset($teacher_id))
		{
			$user_param_id=$teacher_id;	
		}
		$this->view->pageHeading = "View All Lessons";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  View All Lessons';
		$lesson_data=array();
		$student_id=$this->getRequest()->getParam('student_id');
		$this->view->student_id=$student_id;
		$this->view->teacher_id=$teacher_id;
			$student_name='';
			$lesson_data=$this->modelStatic->Super_Get("lesson",'lesson_teacherid="'.$user_param_id.'" and lesson_template="1"',"fetchAll",array("order"=>"lesson_date DESC",'pagination'=>'1'));	
		$this->view->student_name=$student_name;
		$this->view->lesson_data=$lesson_data;
		$page=1;
		$page=$this->_getParam('page');
		if(!isset($_REQUEST['record_per_page']))
		$_REQUEST['record_per_page']=50;
		$paginator=$this->pagination($lesson_data,$page,$_REQUEST['record_per_page']);
		$this->view->paginator=$paginator;
	}
	/* View Lesson Detail */
	
	
	/* View All Templates */
	public function viewlessonsAction()
	{
		global $objSession ; 
		$modelSchool = new Application_Model_SchoolModel();
		$this->view->pageHeading = "All Lessons";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i> All Lessons';
		$lesson_data=array();
		$student_id=$this->getRequest()->getParam('student_id');
	
		$this->view->student_id=$student_id;
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$this->view->teacher_id=$teacher_id;
		$student_name='';
		
		if(isset($student_id) && !empty($student_id) && (isset($teacher_id) && !empty($teacher_id)))
		{
			
			  $lesson_data=$modelSchool->getstudentlesson($student_id,$teacher_id);
				/*$lesson_data=$this->modelStatic->Super_Get("lesson",'lesson_student_id="'.$student_id.'" and lesson_teacherid="'.$teacher_id.'" and lesson_status="1"',"fetchAll",array("order"=>"lesson_id DESC",'pagination'=>'1'));	*/
				$student_data=$this->modelStatic->Super_Get("users",'user_id="'.$student_id.'"',"fetch");
				$student_name=$student_data['user_first_name'].' '.$student_data['user_last_name'];
		}
		else if(isset($student_id) && !empty($student_id))
		{
		
			$private_teacher_arr=$this->modelStatic->Super_Get("private_teacher","private_teacher_studentid='".$student_id."'","fetch",array("fields"=>array('GROUP_CONCAT(private_teacher_teacherid) as private_teachers'),"order"=>"lesson_date DESC",'pagination'=>'1'));
			
			$lesson_data=$modelSchool->getstudentlesson($student_id);
			$student_data=$this->modelStatic->Super_Get("users",'user_id="'.$student_id.'"',"fetch");
			$student_name=$student_data['user_first_name'].' '.$student_data['user_last_name'];
		}
		else if(isset($teacher_id) && !empty($teacher_id))
		{
			if($this->view->user->user_type=='student' )
			{
				$lesson_data=$modelSchool->getstudentlesson($this->view->user->user_id,$teacher_id);
			}
			else
			{
			$lesson_data=$this->modelStatic->Super_Get("lesson",'lesson_teacherid="'.$teacher_id.'" and lesson_status="1"',"fetchAll",array("order"=>"lesson_date DESC",'pagination'=>'1'));
			}
		}
		else
		{
			
			$lesson_data=$this->modelStatic->Super_Get("lesson",'lesson_teacherid="'.$this->view->user->user_id.'" ',"fetchAll",array("order"=>"lesson_date DESC",'pagination'=>'1'));	
		}
		$this->view->student_name=$student_name;
		$this->view->lesson_data=$lesson_data;
		$page=1;
		$page=$this->_getParam('page');
		if(!isset($_REQUEST['record_per_page']))
		$_REQUEST['record_per_page']=50;
		$paginator=$this->pagination($lesson_data,$page,$_REQUEST['record_per_page']);
		$this->view->paginator=$paginator;
		
	
			
	}
	/* View Lesson Detail */
	
	public function viewdetailAction()
	{
		global $objSession ; 
		$this->view->pageHeading = "View Lesson Detail";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  View Lesson Detail';
		$lesson_data=array();
		$class_id=$this->getRequest()->getParam("class_id");
		$this->view->class_id=$class_id;
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$this->view->lesson_id=$lesson_id;
		$student_id=$this->getRequest()->getParam('student_id');
		$this->view->student_id=$student_id;
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$this->view->teacher_id=$teacher_id;
		$lesson_data=$this->modelStatic->Super_Get("lesson",'lesson_id="'.$lesson_id.'"',"fetch");
		$this->view->lesson_data=$lesson_data;
		$lesson_attach=array();
		$lesson_attach=$this->modelStatic->Super_Get("lession_attach","la_lesson_id='".$lesson_id."'","fetchAll");
		$this->view->lesson_attach=$lesson_attach;
			
	}
	
	public function getlessonsAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$student_id = $this->_getParam('student_id');
 		$aColumns = array(
			'l_s_id',
			'l_s_lessid',
			'l_s_stuid',
			'l_s_teaherid',
			'l_s_viewstatus',
			'l_s_addeddate',
			'l_s_usertype',
			'l_s_schoolid',
			'lesson.lesson_id',
			'lesson.lesson_teacherid',
			'lesson.lesson_title',
			'lesson.lesson_desc',
			'lesson.lesson_date',
			'lesson.lesson_status',
			'users.user_first_name',
			'users.user_last_name',
			
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
					$sWhere = "WHERE   l_s_stuid='".$student_id."'";
				}
				else
				{
					$sWhere .= " AND  l_s_stuid='".$student_id."'";
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
			//$row[] = $i;
			$row[]=date("m-d-Y",strtotime($row1['lesson_date']));
			$row[]=$row1['user_last_name'];
			$row[]=$row1['lesson_title'];
			$sentorunsent='';
			if($row1['lesson_status']==1)
			{
				$sentorunsent='<span class="badge badge-success badge-roundless">Sent</span>';	
			}
			else
			{
				$sentorunsent='<span class="badge badge-danger badge-roundless">Unsent</span>';	
			}
			$row[]=$sentorunsent;
			$row[]="<a href='".SITE_HTTP_URL."/teacher/viewdetail/lesson_id/".$row1['lesson_id']."/student_id/".$student_id."'><i class='fa fa-search'></i></a>";
				$status = $row1['l_s_viewstatus']!=1?"checked='checked'":" ";
 		/*	$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['l_s_viewstatus'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';*/
 			$output['aaData'][] = $row;
			$j++;
		    $i++;
		}
		
		echo json_encode($output);
		exit();
  	}
	
	
	public function getlessonsteacherAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$teacher_id = $this->_getParam('teacher_id');
 		$aColumns = array(
			'lesson_id',
			'lesson_teacherid',
			'lesson_title',
			'lesson_desc',
			'lesson_date',
			'lesson_status',
			'users.user_first_name',
			'users.user_last_name',
			'lesson_student.l_s_teaherid'
			
		);
		$sIndexColumn = 'lesson_id';
		$sTable = 'lesson';
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
					$sWhere = "WHERE   lesson_student.l_s_teaherid='".$teacher_id."'";
				}
				else
				{
					$sWhere .= " AND lesson_student.l_s_teaherid='".$teacher_id."'";
				}

			
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." , lesson_student.l_s_teaherid FROM $sTable left join lesson_student on lesson_student.l_s_lessid=lesson.lesson_id 	left join users on users.user_id=lesson_student.l_s_teaherid
		   $sWhere group by lesson_id $sOrder $sLimit";
		
       
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
		

 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable left join lesson_student on lesson_student.l_s_lessid=lesson.lesson_id 	left join users on users.user_id=lesson_student.l_s_teaherid
		   $sWhere group by lesson_id $sOrder $sLimit";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		
		/*$iTotal = $rResultTotal[0]['cnt'];*/
		
		
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				/*"iTotalRecords" => $iTotal,*/
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		$i=1;
		foreach($qry as $row1){
			
			$lesson_student_all=array();
			$lesson_student_all=$this->modelStatic->Super_Get("lesson_student","l_s_lessid='".$row1['lesson_id']."' and l_s_viewstatus='1' ","fetch",array("fields"=>array("COUNT('l_s_id') as lcount")));
			$viewcount_text='';
			
 			$row=array();
			//$row[] = $i;
			$row[]=date("m-d-Y",strtotime($row1['lesson_date']));
			$row[]=$row1['user_first_name'].' '.$row1['user_last_name'];
			$row[]=$row1['lesson_title'];
			$sentorunsent='';
			if($row1['lesson_status']==1)
			{
				$sentorunsent='<span class="badge badge-success badge-roundless">Sent</span>';	
			}
			else
			{
				$sentorunsent='<span class="badge badge-danger badge-roundless">Unsent</span>';	
			}
			$row[]=$sentorunsent;
			$row[]="<a href='".SITE_HTTP_URL."/teacher/viewdetail/lesson_id/".$row1['lesson_id']."'><i class='fa fa-search'></i></a>";
 			$row[]='<div class="btn-group row pull-right" id="toggle_event_editing">
			                 <a title="" class="cursor " rel="popover_'.$row1['lesson_id'].'" id="popover_'.$row1['lesson_id'].'">'.($lesson_student_all['lcount']).' views</a>
                                </div>';
 			$output['aaData'][] = $row;	
			$j++;
		    $i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	/* Get All Classes */
	public function getteacherAction()
	{
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'user_id',
			'user_first_name',
			'user_last_name',
			'user_email',
			'user_created',
			'user_status',
			'user_email_verified',
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
					$sWhere = "WHERE (user_type='teacher' and (user_insertby='".$this->view->user->user_id."' or user_insertby='".$this->view->user->user_school_id."' or user_school_id='".$this->view->user->user_id."'  ))";
			}
			else
				{
					$sWhere .= " AND (user_type='teacher'  and (user_insertby='".$this->view->user->user_id."' or user_insertby='".$this->view->user->user_school_id."'  or user_school_id='".$this->view->user->user_id."' ) )";
				}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) .",group_concat(distinct(class_name)) as classes ,group_concat(distinct(Instrument_name)) as Instruments, COUNT(distinct(lesson_id)) as lessons FROM $sTable left join teacher_classes on teacher_classes.teacher_class_userid=users.user_id left join Classes on Classes.class_id=teacher_classes.teacher_class_classid left join teacher_insruments on teacher_insruments.teacher_insrument_userid=users.user_id left join Instruments on (Instruments.Instrument_id=teacher_insruments.teacher_insrument_instid and Instruments.Instrument_active='1') left join lesson on lesson.lesson_teacherid=users.user_id   $sWhere group by user_id $sOrder $sLimit";
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
		$super = new Application_Model_SuperModel();
		foreach($qry as $row1){
			
 			$row=array();
			//$row[] = $i;
			$lessoncount=$super->Super_Get("lesson_student","l_s_teaherid='".$row1['user_id']."'","fetch",array("fields"=>array("COUNT(l_s_id) as alllessons")));
			
			$row[]=$row1['user_last_name'];
			$row[]=$row1['user_first_name'];
			$row[]=$row1['user_email'];
			$status = $row1['user_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['user_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
				$row[] =  '<a href="'.APPLICATION_URL.'/teacher/newteacher/user_id/'.$row1[$sIndexColumn].'/status/1" class="btn btn-xs purple"> 
			 <i class="fa fa-edit"></i>
			 </a>';
			 $mail_sent='';
			if($row1['user_verification_mail']==1)
			{
				$mail_sent="<b>Mail Sent</b>";	
			}
			$row[]='<input type="checkbox"  value="'.$row1['user_id'].'"   name="select_users[]" id="select_user_'.$row1['user_id'].'"  /> &nbsp;'.$mail_sent;
   			$row[]=date('d F Y g:i A', strtotime($row1['user_created']));
			if(empty($row1['classes']))
			{
				$row1['classes']="None";	
			}
			if(empty($row1['Instruments']))
			{
				$row1['Instruments']="None";	
			}
			$row[]=$row1['classes'];
			$row[]=$row1['Instruments'];
			if($lessoncount['alllessons']!='')
			{
					$row[]='<a href="'.APPLICATION_URL.'/teacher/viewlessons/teacher_id/'.$row1[$sIndexColumn].'">'.$lessoncount['alllessons']." Lesson </a>";	
			}
			else
			{
					$row[]='<a href="'.APPLICATION_URL.'/teacher/viewlessons/teacher_id/'.$row1[$sIndexColumn].'" > 0 Lesson </a>';	
			}
		
			if($row1['user_email']!='')
			{
				if($row1['user_verification_mail']==0)
				{
					$row[]='<a href="'.SITE_HTTP_URL.'/teacher/sendverification/type/1/user_id/'.$row1["user_id"].'"><span class="badge badge-success badge-roundless">Send Verification Mail</span></a>';	
				}
				else if($row1['user_email_verified']==0)
				{
							
			
						$row[]='<a href="'.SITE_HTTP_URL.'/teacher/sendverification/type/2/user_id/'.$row1['user_id'].'"><span class="badge badge-success badge-roundless">Resend Verification Mail</span></a>';	
				}
				else
				{
					
			
					$row[]='<a href="'.SITE_HTTP_URL.'/teacher/sendpasswordresetmail/user_id/'.$row1["user_id"].'"><span class="badge badge-success badge-roundless">Send ResetPassword  Mail  </span></a>';	
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
			} 			$output['aaData'][] = $row;
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
		$isSend = $modelEmail->sendEmail('registration_email_verification',$user_data);
		$objSession->successMsg="Verification mail has been send successfully";
		if($type==1)
		{
			$user_data=array('user_verification_mail'=>1);	
			$superModel->Super_Insert("users",$user_data,'user_id="'.$user_id.'"');
		}
		else
		{
			
		}
		$this->redirect("teacher");
		
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
		$this->redirect("teacher");
	}
	public function getstudentlessonsAction()
	{
		$param=$this->getRequest()->getParam('param');
		$status=$this->getRequest()->getParam('status');
		$student_id=$this->getRequest()->getParam('student_id');
		
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'lesson_id',
			'lesson_teacherid',
			'lesson_title',
			'lesson_desc',
			'lesson_date',
			'lesson_template',
			'lesson_status',
			'lesson_student_id',
			'lesson_view_status',
			'lesson_class_id',
			'lesson_user_type',
			'lesson_student.l_s_stuid',
			'lesson_school_id'
		);
		$sIndexColumn = 'lesson_id';
		$sTable = 'lesson';
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
			//$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
			//$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
				//$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(u2.user_first_name,' ',u2.user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			//$sWhere .= ')';
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
					$sWhere = "WHERE (lesson_school_id='".$school_id."') and lesson_template='0'";
			}
			else
				{
					$sWhere .= " AND lesson_school_id='".$school_id."' and lesson_template='0'";
				}
			
			$swhere1=$sWhere;
			$swhere1.=" and  l_s_stuid='".$student_id."'";
				/* Student */	
				$sOrder="order by lesson_date DESC";
		//	echo $sWhere;die;
			/* Private students only */	
			$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." , u2.user_first_name as u2firsname , u2.user_last_name as u2lastname , u1.user_first_name as u1firstname , u1.user_last_name as u1lastname FROM $sTable left join users as u1 on u1 .user_id=lesson.lesson_teacherid left join lesson_student on lesson_student.l_s_lessid=lesson.lesson_id left join users as u2 on  u2.user_id=lesson_student.l_s_stuid  $swhere1 group by lesson_id $sOrder  $sLimit ";
		
		
		
		
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
			$teacher_name_arr=array();
			$teacher_name_arr=$this->modelStatic->Super_Get("users","user_id='".$row1['lesson_teacherid']."'","fetch");
			$lesson_sab_arr=array();
			$lesson_sab_arr=$this->modelStatic->Super_Get("lesson_student","l_s_lessid='".$row1['lesson_id']."'","fetch");
			$student_name='';
			$student_arr=array();
			if(!empty($lesson_sab_arr))
			{
				$student_arr=$this->modelStatic->Super_Get("users","user_id='".$student_id."'","fetch");	
				if(!empty($student_arr))
				{
					$student_name=$student_arr['user_first_name'].' '.$student_arr['user_last_name'];
				}
			}
 			$row=array();
			//$row[] = $i;
			$row[]=date('m-d-Y',strtotime($row1['lesson_date']));
			$row[]=$row1['u1firstname'].' '.$row1['u1lastname'];
			if($param==1)
			{
				$row[]=$row1['class_name'];	
			}
			else
			{
				$row[]=$student_name;	
			}
			
			$row[]=$row1['lesson_title'];
			$sentorunsent='';
			if($row1['lesson_status']==1)
			{
				$sentorunsent='<span class="badge badge-success badge-roundless">Sent</span>';	
			}
			else
			{
				$sentorunsent='<span class="badge badge-danger badge-roundless">Unsent</span>';	
			}
			$row[]=$sentorunsent;
			
			if($this->view->user->user_id==$row1['lesson_teacherid'] || $this->view->user->user_type=='school')
			{
				
				$row[]='<a href="'.APPLICATION_URL.'/teacher/newlesson/lesson_id/'.$row1[$sIndexColumn].'/student_id/'.$student_id.'"><i class="fa fa-edit"></i></a> &nbsp; &nbsp; <a href="'.APPLICATION_URL.'/teacher/viewdetail/lesson_id/'.$row1[$sIndexColumn].'/student_id/'.$student_id.'"><i class="fa fa-search"></i></a>';
				$row[]='<a onclick="removelesson('.$row1['lesson_id'].')" ><i class="fa fa-trash-o"></i></a>';	
			}
			else
			{
				$row[]='<a href="'.APPLICATION_URL.'/teacher/viewdetail/lesson_id/'.$row1[$sIndexColumn].'/student_id/'.$student_id.'"><i class="fa fa-search"></i></a>';
				$row[]='';
			}
			
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	public function getclasslessonsAction()
	{
		$param=$this->getRequest()->getParam('param');
		$status=$this->getRequest()->getParam('status');
		$class_id=$this->getRequest()->getParam('class_id');
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'lesson_id',
			'lesson_teacherid',
			'lesson_title',
			'lesson_desc',
			'lesson_date',
			'lesson_template',
			'lesson_status',
			'lesson_student_id',
			'lesson_view_status',
			'lesson_class_id',
			'lesson_user_type',
			'lesson_student.l_s_stuid',
			'lesson_school_id'
		);
		$sIndexColumn = 'lesson_id';
		$sTable = 'lesson';
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
			//$sWhere = "WHERE (";
			for ( $i=0 ; $i<count($aColumns) ; $i++ )
			{
				//$sWhere .= "".$aColumns[$i]." LIKE '%".mysql_real_escape_string( $_GET['sSearch'] )."%' OR ";
			//$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
				//$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(u2.user_first_name,' ',u2.user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
			}
			$sWhere = substr_replace( $sWhere, "", -3 );
			//$sWhere .= ')';
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
					$sWhere = "WHERE (lesson_school_id='".$school_id."') and lesson_template='0'";
			}
			else
				{
					$sWhere .= " AND lesson_school_id='".$school_id."' and lesson_template='0'";
				}
			
			$swhere1=$sWhere;
			$swhere1.=" and  lesson_class_id='".$class_id."'";
				/* Student */	
				$sOrder="order by lesson_date DESC";
		//	echo $sWhere;die;
			/* Private students only */	
			$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." , u2.user_first_name as u2firsname , u2.user_last_name as u2lastname , u1.user_first_name as u1firstname , u1.user_last_name as u1lastname FROM $sTable left join users as u1 on u1 .user_id=lesson.lesson_teacherid left join lesson_student on lesson_student.l_s_lessid=lesson.lesson_id left join users as u2 on  u2.user_id=lesson_student.l_s_stuid  $swhere1 group by lesson_id $sOrder  $sLimit ";
		
		
		
		
 		$qry = $this->dbObj->query($sQuery)->fetchAll();

 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable $sWhere";
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
		foreach($qry as $row1){
			$teacher_name_arr=array();
			$teacher_name_arr=$this->modelStatic->Super_Get("users","user_id='".$row1['lesson_teacherid']."'","fetch");
			$lesson_sab_arr=array();
			$lesson_sab_arr=$this->modelStatic->Super_Get("lesson_student","l_s_lessid='".$row1['lesson_id']."'","fetch");
			$student_name='';
			$student_arr=array();
			if(!empty($lesson_sab_arr))
			{
				$student_arr=$this->modelStatic->Super_Get("users","user_id='".$lesson_sab_arr['l_s_stuid']."'","fetch");	
				if(!empty($student_arr))
				{
					$student_name=$student_arr['user_first_name'].' '.$student_arr['user_last_name'];
				}
			}
 			$row=array();
			//$row[] = $i;
			$row[]=date('m-d-Y',strtotime($row1['lesson_date']));
			$row[]=$row1['u1firstname'].' '.$row1['u1lastname'];
		/*	if($param==1)
			{
				$row[]=$row1['class_name'];	
			}
			else
			{
				$row[]=$student_name;	
			}*/
			
			$row[]=$row1['lesson_title'];
			$sentorunsent='';
			if($row1['lesson_status']==1)
			{
				$sentorunsent='<span class="badge badge-success badge-roundless">Sent</span>';	
			}
			else
			{
				$sentorunsent='<span class="badge badge-danger badge-roundless">Unsent</span>';	
			}
			$row[]=$sentorunsent;
			
			if($this->view->user->user_id==$row1['lesson_teacherid'] || $this->view->user->user_type=='school')
			{
				
				$row[]='<a href="'.APPLICATION_URL.'/teacher/newlesson/lesson_id/'.$row1[$sIndexColumn].'/student_id/'.$lesson_sab_arr['l_s_stuid'].'"><i class="fa fa-edit"></i></a> &nbsp; &nbsp; <a href="'.APPLICATION_URL.'/teacher/viewdetail/lesson_id/'.$row1[$sIndexColumn].'/class_id/'.$class_id.'"><i class="fa fa-search"></i></a>';
				$row[]='<a onclick="removelesson('.$row1['lesson_id'].')" ><i class="fa fa-trash-o"></i></a>';	
			}
			else
			{
				$row[]='<a href="'.APPLICATION_URL.'/teacher/viewdetail/lesson_id/'.$row1[$sIndexColumn].'/class_id/'.$class_id.'"><i class="fa fa-search"></i></a>';	
				$row[]='';
			}
			
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	/* Get All Lessons */
	public function getalllessonsAction()
	{
		$param=$this->getRequest()->getParam('param');
		$status=$this->getRequest()->getParam('status');
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'lesson_id',
			'lesson_teacherid',
			'lesson_title',
			'lesson_desc',
			'lesson_date',
			'lesson_template',
			'lesson_status',
			'lesson_student_id',
			'lesson_view_status',
			'lesson_class_id',
			'lesson_user_type',
			'lesson_school_id'
		);
		$sIndexColumn = 'lesson_id';
		$sTable = 'lesson';
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
				//$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(user_first_name,' ',user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
					$sWhere = "WHERE (lesson_school_id='".$this->view->user->user_id."') and lesson_template='0'";
			}
			else
				{
					$sWhere .= " AND lesson_school_id='".$this->view->user->user_id."' and lesson_template='0'";
				}
		if($param==0)
		{
			$sWhere.="  and lesson_class_id='0'";
			if($status==0)
			{
				/* Date */	
			$sOrder="order by lesson_date DESC";	
			}
			else if($status==1)
			{
				/* Teacher */	
			$sOrder="order by u1.user_first_name DESC";
			}
			else
			{
				/* Student */	
				$sOrder="order by u2.user_first_name DESC";
			}
			/* Private students only */	
			$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." , u2.user_first_name as u2firsname , u2.user_last_name as u2lastname , u1.user_first_name as u1firstname , u1.user_last_name as u1lastname FROM $sTable left join users as u1 on u1 .user_id=lesson.lesson_teacherid left join lesson_student on lesson_student.l_s_lessid=lesson.lesson_id left join users as u2 on  u2.user_id=lesson_student.l_s_stuid  $sWhere group by lesson_id $sOrder  $sLimit ";
		/*	echo $sQuery;die;*/
		}
		else
		{
			$sWhere.="  and lesson_class_id!=0";
			if($status==0)
			{
				/* Date */	
			$sOrder="order by lesson_date DESC";	
			}
			else if($status==1)
			{
				/* Teacher */	
			$sOrder="order by u1.user_first_name DESC";
			}
			else
			{
				/* Class */	
				$sOrder="order by class_name DESC";
			}
			/* Class */	
		$sQuery = "SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." ,  u1.user_first_name as u1firstname , u1.user_last_name as u1lastname , class_name FROM $sTable left join users as u1 on u1 .user_id=lesson.lesson_teacherid left join Classes on Classes.class_id=lesson.lesson_class_id  $sWhere group by lesson_id $sOrder $sLimit";

		}
		
			//	echo $sQuery;die;
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
			$teacher_name_arr=array();
			$teacher_name_arr=$this->modelStatic->Super_Get("users","user_id='".$row1['lesson_teacherid']."'","fetch");
			$lesson_sab_arr=array();
			$lesson_sab_arr=$this->modelStatic->Super_Get("lesson_student","l_s_lessid='".$row1['lesson_id']."'","fetch");
			$student_name='';
			$student_arr=array();
			if(!empty($lesson_sab_arr))
			{
				$student_arr=$this->modelStatic->Super_Get("users","user_id='".$lesson_sab_arr['l_s_stuid']."'","fetch");	
				if(!empty($student_arr))
				{
					$student_name=$student_arr['user_first_name'].' '.$student_arr['user_last_name'];
				}
			}
 			$row=array();
			//$row[] = $i;
			$row[]=date('m-d-Y',strtotime($row1['lesson_date']));
			$row[]=$row1['u1firstname'].' '.$row1['u1lastname'];
			if($param==1)
			{
				$row[]=$row1['class_name'];	
			}
			else
			{
				$row[]=$student_name;	
			}
			
			$row[]=$row1['lesson_title'];
			$sentorunsent='';
			if($row1['lesson_status']==1)
			{
				$sentorunsent='<span class="badge badge-success badge-roundless">Sent</span>';	
			}
			else
			{
				$sentorunsent='<span class="badge badge-danger badge-roundless">Unsent</span>';	
			}
			$row[]=$sentorunsent;
			$row[]='<a href="'.APPLICATION_URL.'/teacher/newlesson/lesson_id/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i></a>';
			$row[]='<a onclick="removeadminlesson('.$row1['lesson_id'].')" ><i class="fa fa-trash-o"></i></a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	public function getalltemplatesAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		
		$param=$this->getRequest()->getParam('param');
 		$aColumns = array(
			'lesson_id',
			'lesson_teacherid',
			'lesson_title',
			'lesson_desc',
			'lesson_date',
			'lesson_template',
			'lesson_status',
			'lesson_student_id',
			'lesson_view_status',
			'lesson_class_id',
			'lesson_user_type',
			'lesson_school_id'
		);
		$sIndexColumn = 'lesson_id';
		$sTable = 'lesson';
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
					$sWhere = "WHERE (lesson_school_id='".$this->view->user->user_id."') and lesson_template='1'";
			}
			else
				{
					$sWhere .= " AND lesson_school_id='".$this->view->user->user_id."' and lesson_template='1'";
				}
		
		if($param==0)
		{
			/* Sort By Date */
			$sOrder=" order by lesson_date DESC";
		}
		else
		{
			/* Sort By Name */	
			$sOrder=" order by user_first_name DESC";
		}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." , user_first_name , user_last_name FROM $sTable left join users on users.user_id=lesson.lesson_teacherid  $sWhere  $sOrder $sLimit";
		/*echo $sQuery;die;*/
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
			//$row[] = $i;
			$row[]=date('m-d-Y',strtotime($row1['lesson_date']));
			$row[]=$row1['user_first_name'].' '.$row1['user_last_name'];
			
			$row[]=$row1['lesson_title'];
			$row[]='<a href="'.APPLICATION_URL.'/teacher/addtemplate/lesson_id/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i></a>';
			$row[]='<a onclick="removetemplate('.$row1['lesson_id'].')" ><i class="fa fa-trash-o"></i></a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
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
 			$this->_redirect('teacher');
		} 
		
	/*	$user_id=$this->getRequest()->getParam("idval");	
		$this->modelStatic->Super_Delete("users","user_id='".$user_id."'");
		$objSession->successMsg = "Student has been removed Successfully";				
		$this->_redirect('student');*/
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
 			$this->_redirect('teacher');
		} 
	}
	
	
	/* Remove Class */
	public function removeteacherAction()
	{
		global $objSession ; 
		$user_id=$this->getRequest()->getParam("idval");	
		$this->modelStatic->Super_Delete("users","user_id='".$user_id."'");
		$objSession->successMsg = "Teacher has been removed Successfully";				
		$this->_redirect('teacher');
	}
	/* Add or Update Teacher */
	public function newteacherAction()
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
					$paremission_data=$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='4'","fetch");		if(empty($paremission_data))
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
			/* Get All Classes Data added by school user */
			$all_classses=array();
			$all_classses=$this->modelStatic->Super_Get("Classes","class_insertid='".$this->view->user->user_id."' or class_insertid='".$this->view->user->user_school_id."'","fetchAll",array("order"=>array("class_name ASC")));
			/* Get All Instruments Data added by school user */
			$all_instruments=array();
			$all_instruments=$this->modelStatic->Super_Get("Instruments","((Instrument_userid='".$this->view->user->user_id."' or Instrument_userid='".$this->view->user->user_school_id."') ) and Instrument_active='1'","fetchAll",array("order"=>array("Instrument_name ASC")));
			$modelEmail = new Application_Model_Email();
			$teacher_data=array();
			if(isset($user_id) && !empty($user_id))
			{
				$form->teacher($user_id);
				$this->view->pageHeading = 'Edit Teacher';
				$this->view->pageHeadingshow = '<i class="fa fa-user"></i>  Edit Teacher';
				/* Fatch Teacher Data */
				$teacher_data=$this->modelStatic->Super_Get("users","user_id='".$user_id."'");
				$joinArrClass=array(0=>array("Classes","class_id=teacher_class_classid","full",array("class_name")));
				$class_data=$this->modelStatic->Super_Get("teacher_classes","teacher_class_userid='".$user_id."'","fetchAll",array("order"=>array("class_name ASC")),$joinArrClass);
				
				$class_option=array();
				foreach($class_data as $k=>$v)
				{
					$class_option[$k]=$v['teacher_class_classid'];
				}
				if(empty($class_option))
				{
					$class_option['None']='None';	
				}
				//prd($class_option);
				$teacher_data['teacher_class']=$class_option;			
				$instrument_data=$this->modelStatic->Super_Get("teacher_insruments","teacher_insrument_userid='".$user_id."'","fetchAll");
				
				$instrument_option=array();
				foreach($instrument_data as $k=>$v)
				{
					$instrument_option[$k]=$v['teacher_insrument_instid'];
				}
				if(empty($instrument_option))
				{
					$instrument_option['None']='None';
				}
				$teacher_data['teacher_instruments']=$instrument_option;
				$joinArr=array(0=>array("users","user_id=private_teacher_studentid","full",array("user_first_name","user_last_name")));
				$student_data=$this->modelStatic->Super_Get("private_teacher","private_teacher_teacherid='".$user_id."'","fetchAll",array("order"=>array("user_last_name ASC","user_first_name ASC")),$joinArr);
		      
				$student_option=array();
				foreach($student_data as $k=>$v)
				{
					$student_option[$k]=$v['private_teacher_studentid'];
				}
				if(empty($student_option))
				{
					$student_option['None']='None';
				}
				$teacher_data['teacher_students']=$student_option;
				
				/* Populate Form Data */
				$form->populate($teacher_data); 
			}
			else
			{
				$form->teacher();
				$this->view->pageHeading = 'Add Teacher';
				$this->view->pageHeadingshow = '<i class="fa fa-user"></i>  Add Teacher';	
			}
			$this->view->form=$form;
			$this->view->teacher_data=$teacher_data;
			if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ 
					// Form Valid
					/* Get Form Data */
					$data_insert=$form->getValues();
					
					unset($data_insert['teacher_class']['None']);
					unset($data_insert['teacher_instruments']['None']);
					unset($data_insert['teacher_students']['None']);
					/* Teacher Class Array */
					$teacher_class=array();
					$teacher_class=$data_insert['teacher_class'];
					unset($data_insert['teacher_class']);
					/* Teacher Instruments Array */
					$teacher_instruments=array();
					$teacher_instruments=$data_insert['teacher_instruments'];
					unset($data_insert['teacher_instruments']);
					/* Teacher Student Array */
					$teacher_students=array();
					$teacher_students=$data_insert['teacher_students'];
					unset($data_insert['teacher_students']);
					$data_insert['user_insertby']=$this->view->user->user_id;
						if($this->view->user->user_type=='school')
						{
							
						$data_insert['user_school_id']=$this->view->user->user_id;
						}
						else
						{
							$data_insert['user_school_id']=$this->view->user->user_school_id;	
						}
						
					/* Check condtion For Edit or Add */
					if(isset($teacher_data) && !empty($teacher_data))
					{
						/* Edit Teacher Data */
						$this->modelStatic->Super_Insert("users",$data_insert,'user_id="'.$user_id.'"');
					
						$objSession->successMsg = "Teacher has been updated Successfully";
					}
					else
					{
						/* Add Teacher Data */
						//$password=randomPassword();						
						$password=12345;						
						$data_insert['user_password']=md5($password);	
						$data_insert['user_password_text']=$password;					
						$data_insert['user_created']=gmdate('Y-m-d H:i');
						$data_insert['user_type']="teacher";
						$data_insert['user_insertby']=$this->view->user->user_id;
						$user_name='';
						$user_name=$this->modelStatic->insertusername('teacher');
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
						if($data_user['user_email']!='')
								{
						$isSend = $modelEmail->sendEmail('registration_teacher_email',$data_user);
								}
					}
					/* Delete Already Added Classes */
						$this->modelStatic->Super_Delete("teacher_classes",'teacher_class_userid="'.$user_id.'"');
					/* Delete Already Added Instruments */
						$this->modelStatic->Super_Delete("teacher_insruments",'teacher_insrument_userid="'.$user_id.'"');
					/* Delete Already Added Students */
						$this->modelStatic->Super_Delete("private_teacher",'private_teacher_teacherid="'.$user_id.'"');
					/* Add New Classes of Teacher */	
					foreach($teacher_class as $k=>$v)
						{
							if($v!='None')
							{
								$data=array();
								$data=array('teacher_class_userid'=>$user_id,
										'teacher_class_classid'=>$v,
										'teacher_class_date'=>gmdate('Y-m-d')
						
								);	
								$kk=$this->modelStatic->Super_Insert("teacher_classes",$data);
								
							}
						}
					/* Add New Instruments of Teacher */	
					foreach($teacher_instruments as $k=>$v)
						{
							if($v!='None')
							{
								$data=array();
								
								date_default_timezone_set('America/Los_Angeles');	// PDT time
								$data=array('teacher_insrument_userid'=>$user_id,
										'teacher_insrument_instid'=>$v,
//										'teacher_insrument_date'=>gmdate('Y-m-d H:i:s')
										'teacher_insrument_date'=>date('Y-m-d H:i:s')
						
								);	
								$kk=$this->modelStatic->Super_Insert("teacher_insruments",$data);
							}
							
							
						
					
						}
				$teache_stu_id=implode(",",$teacher_students);		
				/* Add New Students of Teacher */
				//prd($teacher_students);	
				if($teache_stu_id!='None'){
					
			   $teacher_stu_arr=array();
			   $teacher_stu_arr=$this->modelStatic->Super_Get("users","user_id IN(".$teache_stu_id.")","fetchAll",array("fields"=>array("user_first_name","user_last_name","user_id"),"order"=>array("user_last_name ASC")));
			
					foreach($teacher_stu_arr as $k=>$v)
						{
							/*if($v!='None')
							{*/
								$data=array();
								date_default_timezone_set('America/Los_Angeles');	// PDT time
								$data=array('private_teacher_teacherid'=>$user_id,
										'private_teacher_studentid'=>$v['user_id'],
//										'private_teacher_date'=>gmdate('Y-m-d H:i:s')
										'private_teacher_date'=>date('Y-m-d H:i:s')
						
								);	
								$kk=$this->modelStatic->Super_Insert("private_teacher",$data);
							
							/*}*/
						
					
						}
				} 
				if(isset($status) && $status==1)
				{
					$this->_redirect('teacher/index');
				
				}
				else
				{
					$this->_redirect('dashboard');	
				}
			 }
					
			}
			
	}
	


	
	/* Manage Teacher Folder */
	public function uploadfilesAction()
	{
		global $objSession; 
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$this->view->pageHeading = 'Upload Files';
		$this->view->pageHeadingshow = '<i class="fa fa-folder-open"></i>  Manage Folder';
		$document_arr=array();
		$user_param_id=$this->view->user->user_id;
		if(isset($teacher_id))
		{
			$user_param_id=$teacher_id;
		}
		if($this->view->user->user_type=='teacher')
		{
				$document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_userid="'.$user_param_id.'"','fetchAll');
		}
		else
		{
			   $document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_schoolid="'.$user_param_id.'"','fetchAll');	
		}
	
		$this->view->document_arr=$document_arr;
		if(isset($_POST) && !empty($_POST))
		{
				/* Remove All delete File */
				if($_POST['delete_doc']!='')
				{
							$delete_gallery=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_id IN (".ltrim($_POST['delete_doc'],",").")","fetchall");
							
							foreach($delete_gallery as $del_img)
							{
								unlink(TEACHER_FILES_PATH.$del_img['teacher_attach_name']);	
								if(file_exists(TEACHER_FILES_PATH.'/200/'.$del_img['teacher_attach_name']))
								{
									unlink(TEACHER_FILES_PATH.'/200/'.$del_img['teacher_attach_name']);
								}
							}
							
							$this->modelStatic->Super_Delete("teacher_attachments","teacher_attach_id IN (".ltrim($_POST['delete_doc'],",").")");
							
				}
				/* Teacher Folder Files */
				$path =ROOT_PATH.'/public/resources/uploads/image_'.$user_param_id.'/';	
				$files = scandir($path);
				$array=array();
				$array_oiginal=array();
				
				foreach ($files as $file) 
				{
				if($file!='.' && $file!='..' && ((strpos($file,"."))))
				{ 
				$newname=time().".".$file;
				array_push($array,$newname);
				array_push($array_oiginal,$file);
				
				if(file_exists($path."/".$file))
				{
					copy($path."/".$file,TEACHER_FILES_PATH."/".$newname);
					
					$ext = pathinfo($newname, PATHINFO_EXTENSION);
					if($ext=='png' || $ext=='PNG' || $ext=='jpg' || $ext=='JPG' || $ext=='JPEG' || $ext=='jpeg' || $ext=='gif' || $ext=='GIF')
					{
						$thumb_config = array("source_path"=>TEACHER_FILES_PATH,"name"=> $newname);
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>TEACHER_FILES_PATH."/200","crop"=>true ,"width"=>207,"height"=>190,"ratio"=>false)));	
					}
				}
				
				
				}
				}
				foreach($array as $k=>$v)
				{
					$type=0;
					$ext = pathinfo($v, PATHINFO_EXTENSION);
					if($ext=='doc' || $ext=='docx' || $ext=='DOC' || $ext=='DOCX')
					{
						$type=1;
					}
					else if($ext=='pdf' || $ext=='PDF')
					{
						$type=2;
					}
					else if($ext=='mp4')
					{
						$type=3;
					}
					date_default_timezone_set('America/Los_Angeles');	// PDT time
					$data=array('teacher_attach_userid'=>$user_param_id,
								'teacher_attach_name' => $v,
//								'teacher_attach_date'=> gmdate('Y-m-d H:i:s'),
								'teacher_attach_date'=> date('Y-m-d H:i:s'),
								'teacher_attach_type' => $type,
								'teacher_attach_filename'=>$array_oiginal[$k],
					);	
						$user_type=0;
						
						$school_id=$this->view->user->user_school_id;
						if($this->view->user->user_type=='school')
						{
							$user_type=1;
							$school_id=$this->view->user->user_id;	
							/* This Lesson is added By School User */	
						}
						$data['teacher_attach_usertype']=$user_type;
						$data['teacher_attach_schoolid']=$school_id;
					$this->modelStatic->Super_Insert("teacher_attachments",$data);
				}
				$objSession->successMsg = "Files has been added Successfully";
				if($this->view->user->user_type=='teacher')
				{
					$this->redirect("profile/teacherdashboard");	
				}
				else
				{
					if(isset($teacher_id))
					{
						$this->redirect("profile/teacherdashboard/teacher_id/".$teacher_id);			
					}
					else
					{
						$this->redirect("profile/lessondahboard");	
					}
					
				}
				
		}
		else
		{
			if(file_exists(ROOT_PATH.'/public/resources/uploads/image_'.$user_param_id))
			{
			
			DeleteDirfileupload(ROOT_PATH.'/public/resources/uploads/image_'.$user_param_id);	
			}
		}
			
		
	}
	
	/* View folder */
	public function viewfolderAction()
	{
		global $objSession ; 
		$content = $this->modelStatic->getPage(40); 
		$this->view->show = "front_dashboard" ; 		
		/* Get All Images upload by Teacher */
		$teacher_attach_images=array();
		$teacher_attach_images=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$this->view->user->user_id."' and teacher_attach_type='0'","fetchAll");
		$this->view->teacher_attach_images=$teacher_attach_images;
		/* Get All Documents upload by Teacher */
		$teacher_attach_documents=array();
		$teacher_attach_documents=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$this->view->user->user_id."' and teacher_attach_type='1' or teacher_attach_type='2'","fetchAll");
		$this->view->teacher_attach_documents=$teacher_attach_documents;
		/* Get All Videos uploades by Teacher */
		$teacher_attach_video=array();
		$teacher_attach_video=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$this->view->user->user_id."' and teacher_attach_type='3'","fetchAll");
		$this->view->teacher_attach_video=$teacher_attach_video;
		
	}
	
	public function viewfolderadminAction()
	{
		global $objSession ; 
		$content = $this->modelStatic->getPage(40); 
		$this->view->show = "front_dashboard" ; 		
		/* Get All Images upload by Teacher */
		$teacher_attach_images=array();
		$teacher_attach_images=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$this->view->user->user_id."' and teacher_attach_type='0'","fetchAll");
		$this->view->teacher_attach_images=$teacher_attach_images;
		/* Get All Documents upload by Teacher */
		$teacher_attach_documents=array();
		$teacher_attach_documents=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$this->view->user->user_id."' and teacher_attach_type='1' or teacher_attach_type='2'","fetchAll");
		$this->view->teacher_attach_documents=$teacher_attach_documents;
		/* Get All Videos uploades by Teacher */
		$teacher_attach_video=array();
		$teacher_attach_video=$this->modelStatic->Super_Get("teacher_attachments","teacher_attach_userid='".$this->view->user->user_id."' and teacher_attach_type='3'","fetchAll");
		$this->view->teacher_attach_video=$teacher_attach_video;
		
	}
	/* View document */
	function contentsAction()
	{
		$doc=$this->getRequest()->getParam("doc");
		$this->view->doc=$doc;
		
		
	}
	/* View Lesson Attachement */
	function contentattachAction()
	{
			$doc=$this->getRequest()->getParam("doc");
		$this->view->doc=$doc;
	}
	/* Upload Teacher Document */
	public function uploadpathAction()
	{
					$this->_helper->layout->disableLayout();
					$this->_helper->viewRenderer->setNoRender(true);
					$options = array();
					//prd(HTTP_UPLOADS_PATH);
					if(isset($_GET['file']) && $_GET['file'] != ""){
					
					
					}
					$options['script_url'] = SITE_HTTP_URL."/teacher/uploadpath";
					$path=ROOT_PATH."/public/resources/uploads/image_".$this->view->user->user_id."/";
					//prd($path);
					if(!is_dir($path))
					{
						mkdir($path,0777);
					}
					$options['upload_dir'] = $path;
					$options['upload_url'] = SITE_HTTP_URL."/public/resources/uploads/image_".$this->view->user->user_id."/";
					$imageUpload = new Application_Plugin_UploadHandler($options);
					//exit;
	}
	
	public function uploadpathlessionAction()
	{
		
					$this->_helper->layout->disableLayout();
					$this->_helper->viewRenderer->setNoRender(true);
					$options = array();
					//prd(HTTP_UPLOADS_PATH);
					if(isset($_GET['file']) && $_GET['file'] != ""){
					
					
					}
					$options['script_url'] = SITE_HTTP_URL."/teacher/uploadpathlession";
					$path=ROOT_PATH."/public/resources/lession_attach/upimage_".$this->view->user->user_id."/";
					//prd($path);
					if(!is_dir($path))
					{
						mkdir($path, 0777, true);
//						mkdir($path,0777);
					}
					$options['upload_dir'] = $path;
					$options['max_file_size'] = 150*1024*1024;
					$options['upload_url'] = SITE_HTTP_URL."/public/resources/lession_attach/upimage_".$this->view->user->user_id."/";
					$imageUpload = new Application_Plugin_UploadHandler($options);
					exit;
	}
	
	public function chkimgcountAction()
	{      
		$file_path=ROOT_PATH."/public/resources/uploads/image_".$this->view->user->user_id;
		$thumb_file_path=ROOT_PATH."/public/resources/uploads/image_".$this->view->user->user_id."/thumbnail";
		$uploaded_files=array();
			
		if (is_dir($file_path))
		{
			$files = scandir($file_path);
			foreach ($files as $file) 
			{
				if($file!='.' && $file!='..' && ((strpos($file,"."))))
				{
				 $uploaded_files[count($uploaded_files)]=$file;
				}
			}
		}
		echo count($uploaded_files);
							
		exit();
	}
	
 	
}