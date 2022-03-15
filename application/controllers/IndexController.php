<?php
class IndexController extends Zend_Controller_Action
{
	public function init(){	
		$this->modelUser = new Application_Model_User();
		$this->modelStatic = new Application_Model_Static();
  	}
	
	public function videotestAction(){}

 	public function indexAction()
	{
		
		require_once ROOT_PATH.'/private/ZiggeoPhpSdk-master/Ziggeo.php';
		//$token=$this->getRequest()->getParam('token');
	$ziggeo = new Ziggeo('80022bf8c53e76bfb6c1bebccefc6113', 'e07460938f50cd611b59a5f764676e48', '384609c33a387b25c597cd8bb1790c96'); 
	/*	$lesson_arr=array();
		//require_once('./ziggeo/Ziggeo.php');
		$configuration =$this->modelStatic->Super_Get("config","1","fetchAll");
		
 		foreach($configuration as $key=>$config){
			$config_data[$config['config_key']]= $config['config_value'] ;
			$config_groups[$config['config_group']][$config['config_key']]=$config['config_value'];	
		}
		
	
		$lesson_arr=$this->modelStatic->Super_Get("lesson","1","fetchAll");
		
		foreach($lesson_arr as $k=>$v)
		{
			$lesson_student=array();
			$lesson_student=$this->modelStatic->Super_Get("lesson_student","l_s_autodelete='1'   and l_s_lessid='".$v['lesson_id']."'","fetch");
			if(empty($lesson_student))
			{
				$aftersixmonth=date("Y-m-d H:i:s", strtotime("+6 months", strtotime($v['lesson_date'])));	
				$today=date("Y-m-d H:i:s");
				if(strtotime($aftersixmonth)<strtotime($today))
				{
				$lesson_attach=array();
				$lesson_attach=$this->modelStatic->Super_Get("lession_attach","la_lesson_id='".$v['lesson_id']."' and la_type='1'","fetchAll");
		
				
				foreach($lesson_attach as $ks=>$vs)
				{
					if($vs['la_type']==1)
					{
							// video uploaded
						
							if(file_exists(TEMP_PATH."/".$vs['la_name']))
							{
							unlink(TEMP_PATH.'/'.$vs['la_name']);	
							}
							if(file_exists(TEMP_PATH."/voice_".$v['lesson_teacherid'].'/'.$vs['la_name']))
							{
							unlink(TEMP_PATH."/voice_".$v['lesson_teacherid'].'/'.$vs['la_name']);	
							}
							if(file_exists(TEMP_PATH."/upimage_".$v['lesson_teacherid'].'/'.$vs['la_name']))
							{
							unlink(TEMP_PATH."/upimage_".$v['lesson_teacherid'].'/'.$vs['la_name']);	
							}
							
							
					}	
					else
					{
							// recorded by ziggeo
							$ziggeo->videos()->delete($vs['la_token']) ;
							
					}
					
				}
				
				$this->modelStatic->Super_Delete("lession_attach","la_lesson_id='".$v['lesson_id']."'");
				$this->modelStatic->Super_Delete("lesson_student","l_s_lessid='".$v['lesson_id']."'");
				$this->modelStatic->Super_Delete("lesson","lesson_id='".$v['lesson_id']."'");				
			}	
			}
			
		}*/
	



		
		/*
		require_once(ROOT_PATH . "/private/ZiggeoPhpSdk-master/Ziggeo.php");
		$opts = getopt("", array("token:", "private:", "target:"));
		$ziggeo = new Ziggeo($this->view->site_configs['ziggeo_token'], $this->view->site_configs['ziggeo_private_key']);
		$skip = 0;
		$limit = 100;
		$returned = -1;
		$videos = array();
		while ($returned != 0) {
		$ret = $ziggeo->videos()->index(array(
		"skip" => $skip,
		"limit" => $limit
		));
		$videos = array_merge($videos, $ret);
		$returned = count($ret);
		$skip += count($ret);
		}
		foreach ($videos as $video) {
		$file_name = $opts["target"] . "/" . $video->token . ".jpg";
		echo $file_name . "\n";
		if($video->token=="8f00b3295a4def0db9e2083064dc2d8a")
		{
			$file_data = $ziggeo->videos()->download_video($video->token);
			pr($file_data);
		 // file_put_contents($file_name, $file_data);
		}
		}
		die;
		*/
  		$this->view->pageHeading = "Home";
		$param=$this->getRequest()->getParam('param');
		$modelSlider = new Application_Model_Slider();
		if(isset($param))
		{
			$this->view->param=$param;
			$this->view->page_slug="features";	
		}
 		$modelSlider = new Application_Model_Slider();
		$images = $modelSlider->fetchImages();
		$this->view->slider_images = $images ;
  	}
	public function testvoiceAction()
	{
		 global $objSession ; 
		
	
		if(isset($_FILES['file']) and !$_FILES['file']['error'])
		{
				$fname = "11" . ".wav";			
				move_uploaded_file($_FILES['file']['tmp_name'], TEACHER_FILES_PATH."/ss/" . $fname);
		}
		else
		{
			echo "unable";
		}
		exit;
	}
	
	public function testaudioAction()
	{
		global $objSession;
			
	}
	
	public function testvideoAction()
	{
		global $objSession;
		if(!isset($_SESSION['user_'.$this->view->user->user_id]))
		{
				$_SESSION['user_'.$this->view->user->user_id]=array();
		}
		else
		{
				prn($_SESSION['user_'.$this->view->user->user_id]);
		}
			
	}
	public function uploadvideoAction()
	{
		$this->_helper->layout->disableLayout();
		$user_id=$this->view->user->user_id;
		$val=$_REQUEST['x'];
		$file = $val; 
		$newfile =time().'_'.$user_id.'_'.'output.mp4'; 
		$_SESSION['user_'.$user_id][$newfile]=$newfile;
		if (!copy($file,ROOT_PARH.'/video/'.$newfile)) { 
		echo "failed to copy $file...\n"; 
		} 
		
		exit;

	}
	public function testAction(){
		
	}
	
	public function mobileaudioAction()
	{
		global $objSession ; 
		$lesson_data=array();
		$document_arr=$this->modelStatic->Super_Get("teacher_attachments",'teacher_attach_userid="'.$this->view->user->user_id.'"','fetchAll');
		$this->view->document_arr=$document_arr;
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_lesson_id="'.$lesson_id.'"','fetchAll');
		$this->view->lession_arr=$lession_arr;
		$this->view->lesson_id=$lesson_id;
		$form = new Application_Form_SchoolForm();
		$form->newlesson();
		if(isset($lesson_id) && !empty($lesson_id))
		{
			$this->view->pageHeading = "Update Lesson";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Update Lesson';
			/* Get Lesson Data */
			
			$lesson_data=$this->modelStatic->Super_Get("lesson","lesson_id='".$lesson_id."' and lesson_status='1'","fetch");	
			$form->populate($lesson_data);
		}
		else
		{
			$this->view->pageHeading = "Add New Lesson";
			$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  Add New Lesson';	
		}		
		$this->view->form=$form;
			if ($this->getRequest()->isPost())
			{ // Post Form Data
			//prd($_SESSION['voice_recording_'.$this->view->user->user_id]);
			
			
				$posted_data  = $this->getRequest()->getPost();
				//prd($posted_data);
				if ($form->isValid($posted_data))
				{ // If form is valids
				
				if(isset($_FILES['myvideo']) && !empty($_FILES['myvideo']) && isset($_FILES['myvideo']['name']) &&  !empty($_FILES['myvideo']['name']))
				{
					foreach($_FILES['myvideo']['name'] as $k=>$v)
					{
						//prn($v);
							if(isset($v[1]) && is_array($v[1]) && !empty($v[1][0]))
							{
								
							}
					}
				}
			//prd("here");
					$data_insert=$form->getValues();
					
					if(isset($lesson_data) && !empty($lesson_data))
					{
						//prd('fgvbn');
						foreach($posted_data['delete_attach'] as $dal=>$val)
					{
					
						if($val!='')
						{
							$lession_arr=$this->modelStatic->Super_Get("lession_attach",'la_id="'.$val.'"','fetch');
							$fdg=$this->modelStatic->Super_Delete("lession_attach",'la_id="'.$val.'"');
							unlink(TEMP_PATH.'/'.$val);
							
						}
						
					}
						
						
						
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert,'lesson_id="'.$lesson_id.'" and lesson_status="1"');
						$insert->inserted_id=$lesson_id;
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							//prd('gfvbnvb');
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								copy(TEACHER_FILES_PATH.'/'.$v,TEMP_PATH.'/'.$v);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
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
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						//audio and video
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$this->view->user->user_id.'/';	
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
						
						$data_insert['lesson_teacherid']=$this->view->user->user_id;
						// ================ add  ==========
						date_default_timezone_set('America/Los_Angeles');	// PDT time
						// ================================
						
						// $data_insert['lesson_date']=gmdate('Y-m-d H:i:s');
						$data_insert['lesson_date']=date('Y-m-d H:i:s');
						$insert=$this->modelStatic->Super_Insert("lesson",$data_insert);
						//prd($insert);
						//existing folder
						
						if(isset($posted_data['existing_fold']) && (!empty($posted_data['existing_fold'])))
						{
							//prd('gfvbnvb');
							foreach($posted_data['existing_fold'] as $key=>$v)
							{
								copy(TEACHER_FILES_PATH.'/'.$v,TEMP_PATH.'/'.$v);
								//prd('fgvbnvn');
								$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
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
						
							$data=array('la_lesson_id'=>$insert->inserted_id,
										'la_name' => $v,
									
							);	
							$n=$this->modelStatic->Super_Insert("lession_attach",$data);
							//prd($n);
						}
						//audio and video
						
						
						//images
						$path =TEMP_PATH.'/upimage_'.$this->view->user->user_id.'/';	
						$files = scandir($path);
						$array=array();
				
						foreach ($files as $file) 
						{
								if($file!='.' && $file!='..' && ((strpos($file,"."))))
								{ 
								$newname=time().".".$file;
								array_push($array,$newname);
								
								if(file_exists($path."/".$file))
								{
									rename($path."/".$file,TEMP_PATH."/".$file);
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
						
						
						
					
						
						
						$objSession->successMsg="Lesson has been added Successfully";
					}
					$this->redirect("teacher/viewlessons");
					
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
	
}


