<?php
class Application_Model_SchoolModel extends Application_Model_SuperModel
{
 	protected $_name = "";
	
	public function init()
	{		
		
	}
	
	public function userlessonpagination($student_id,$teacher_id)
	{ 

		$query  = $this->getAdapter()->select()->from('lesson_student')->where('l_s_stuid="'.$student_id.'"')->joinLeft('lesson',"lesson.lesson_id=lesson_student.l_s_lessid")->where("lesson_template='0'")->group('l_s_id')->order("l_s_id DESC");
		return $query;	
   }
	
	public function getallannouncement($announcement_id,$type)
	{
		$query  = $this->getAdapter()->select()->from('announcement')->where('announcement_id="'.$announcement_id.'"')->joinLeft('announcement_user',"announcement_user.an_anid=announcement.announcement_id and an_type='".$type."'")->where('an_id!=""')->group('an_id')->order("an_id DESC")->query()->fetchAll();
		return $query;	
	}
	
	public function getuserannouncement($user_data)
	{
		$query  = $this->getAdapter()->select()->from('announcement_user')->where('an_u_id="'.$user_data->user_id.'" and an_status="0"')->joinLeft('announcement',"announcement.announcement_id=announcement_user.an_anid")->where('announcement_id!=""')->group('announcement_id')->order("announcement_id DESC")->query()->fetchAll();
		return $query;
	}
	
	public function getuserannouncementpaging($user_data,$check_type)
	{
		
		$query  = $this->getAdapter()->select()->from('announcement_user')->where('an_u_id="'.$user_data->user_id.'" and an_status="'.$check_type.'"')->joinLeft('announcement',"announcement.announcement_id=announcement_user.an_anid")->where('announcement_id!=""')->group('announcement_id')->order("announcement_id DESC");
		return $query;
	}
  	
	public function getteacherclass()
	{
			$userLogged = isLogged(true);
			$query  = $this->getAdapter()->select()->from('teacher_classes',array('teacher_class_classid','teacher_class_userid'))->where('teacher_class_userid="'.$userLogged->user_id.'"')->joinLeft('Classes',"Classes.class_id=teacher_classes.teacher_class_classid",array('class_name','class_id'))->where('class_id!=""')->group('class_id');
	
			$class_arr=array();
			$class_arr=$query->query()->fetchAll();
			return $class_arr;
	}
	
	public function getteacherclass1($teacher_id)
	{
			$userLogged = isLogged(true);
			$query  = $this->getAdapter()->select()->from('teacher_classes',array('teacher_class_classid','teacher_class_userid'))->where('teacher_class_userid="'.$teacher_id.'"')->joinLeft('Classes',"Classes.class_id=teacher_classes.teacher_class_classid",array('class_name','class_id'))->where('class_id!=""')->group('class_id')->order('class_name ASC');
	
			$class_arr=array();
			$class_arr=$query->query()->fetchAll();
			return $class_arr;
	}
	
	public function getschoolteacherclass()
	{
			$userLogged = isLogged(true);
			$query  = $this->getAdapter()->select()->from('Classes')->where('class_school_id="'.$userLogged->user_id.'"');
			$class_arr=array();
			$class_arr=$query->query()->fetchAll();
			return $class_arr;
	}
	
	public function getschoolteacherclassall()
	{
			$userLogged = isLogged(true);
			$query  = $this->getAdapter()->select()->from('Classes')->where('class_school_id="'.$userLogged->user_school_id.'"');
			$class_arr=array();
			$class_arr=$query->query()->fetchAll();
			return $class_arr;
	}
	
	
	public function getstudentlesson($student_id,$teacher_id=false)
	{
		$query='';
		if($teacher_id)
		{
			$query=	 $this->getAdapter()->select()->from('lesson_student',array('l_s_lessid','l_s_stuid','l_s_teaherid','l_s_viewstatus','l_s_addeddate','l_s_id'))->where('l_s_stuid="'.$student_id.'" and l_s_teaherid="'.$teacher_id.'"')->joinLeft('lesson',"lesson.lesson_id=lesson_student.l_s_lessid",array('lesson_teacherid','lesson_title','lesson_desc','lesson_date',"lesson_template",'lesson_status','lesson_student_id','lesson_class_id','lesson_view_status','lesson_id'))->where('lesson_id!="" and lesson_status="1"')->group('lesson_id')->order("lesson_id DESC");	
		}
		else
		{
			
			$query=	 $this->getAdapter()->select()->from('lesson_student',array('l_s_lessid','l_s_stuid','l_s_teaherid','l_s_viewstatus','l_s_addeddate','l_s_id'))->where('l_s_stuid="'.$student_id.'"')->joinLeft('lesson',"lesson.lesson_id=lesson_student.l_s_lessid",array('lesson_teacherid','lesson_title','lesson_desc','lesson_date',"lesson_template",'lesson_status','lesson_student_id','lesson_class_id','lesson_view_status','lesson_id'))->where('lesson_id!="" and lesson_status="1"')->group('lesson_id')->order("lesson_id DESC");	
		}	
		
		
			return $query;
	}
	
	public function getstudentlesson1($student_id,$teacher_id=false)
	{
		$query='';
		if($teacher_id)
		{
			$query=	 $this->getAdapter()->select()->from('lesson_student',array('l_s_lessid','l_s_stuid','l_s_teaherid','l_s_viewstatus','l_s_addeddate','l_s_id'))->where('l_s_stuid="'.$student_id.'" and l_s_teaherid="'.$teacher_id.'"')->joinLeft('lesson',"lesson.lesson_id=lesson_student.l_s_lessid",array('lesson_teacherid','lesson_title','lesson_desc','lesson_date',"lesson_template",'lesson_status','lesson_student_id','lesson_class_id','lesson_view_status','lesson_id'))->where('lesson_id!="" and lesson_status="1"')->group('lesson_id')->order("lesson_id DESC");	
		}
		else
		{
			$query=	 $this->getAdapter()->select()->from('lesson_student',array('l_s_lessid','l_s_stuid','l_s_teaherid','l_s_viewstatus','l_s_addeddate','l_s_id'))->where('l_s_stuid="'.$student_id.'" ')->joinLeft('lesson',"lesson.lesson_id=lesson_student.l_s_lessid",array('lesson_teacherid','lesson_title','lesson_desc','lesson_date',"lesson_template",'lesson_status','lesson_student_id','lesson_class_id','lesson_view_status','lesson_id'))->where('lesson_id!="" and lesson_status="1"')->group('lesson_id')->order("lesson_id DESC");	
		}	
		
			$lesson_array=array();
			$lesson_array=$query->query()->fetchAll();
			return $lesson_array;
	}
	
	public function getallfamilystudent($user_id)
	{
			$allstudents=	$this->getAdapter()->select()->from('student_family')->where('s_f_fid="'.$user_id.'"')->joinLeft('users',"users.user_id=student_family.s_f_sid",array("user_email","user_id","user_status","user_email_verified"))->group('user_id')->where('user_id!=""')->order("user_id DESC")->query()->fetchAll();
			return $allstudents;
	}
	
	public function getstudentallteacher($student_id)
	{
		
		$userLogged = isLogged(true);
		
			if($userLogged->user_type=='school')
			{
					$Query = $this->Super_Get('users','user_status="1" and (user_type="student") and user_school_id="'.$userLogged->user_id.'"','fetchAll'); 	
			}
			else if($userLogged->user_type=='teacher')
			{
				$Query=	$this->getAdapter()->select()->from('private_teacher')->where('private_teacher_teacherid="'.$userLogged->user_id.'"')->joinLeft('users',"users.user_id=private_teacher.private_teacher_studentid and user_status='1' ")->group('user_id')->where('user_id!=""')->order("user_id DESC")->query()->fetchAll();
			}
			else if($userLogged->user_type=='student')
			{
				$Query=	$this->getAdapter()->select()->from('private_teacher')->where('private_teacher_studentid="'.$userLogged->user_id.'"')->joinLeft('users',"users.user_id=private_teacher.private_teacher_teacherid and user_status='1' ")->group('user_id')->where('user_id!=""')->order("user_id DESC")->query()->fetchAll();
			}
		
			return $Query;
	}
	
	public function getstudentall($param)
	{
		$userLogged = isLogged(true);
		$all_users=array();
		$where='';
		
		if($param!='')
		{
			$where=" (user_first_name LIKE '%".$param."%' or user_last_name LIKE '%".$param."%') and user_school_id='".$userLogged->user_school_id."' and user_status='1' and user_type='student'";	
				$all_users=$this->Super_Get("users",$where,"fetchAll");
		}	
		else
		{
			$get_private_students=$this->Super_Get("private_teacher","private_teacher_teacherid='".$userLogged->user_id."'","fetch",array("fields"=>array("GROUP_CONCAT(private_teacher_studentid) as uids")));
			if($get_private_students['uids']!='')
			{
				$where="user_id IN(".$get_private_students['uids'].") and user_school_id='".$userLogged->user_school_id."' and user_status='1' and user_type='student'";
				$all_users=$this->Super_Get("users",$where,"fetchAll");
				
			}
		}
		return $all_users;
	}
	
	
	
	public function getclassesall($param)
	{
		
		$userLogged = isLogged(true);
		
			if( $param!='')
			{
				
			$where=" class_name LIKE '%".$param."%' and class_school_id='".$userLogged->user_school_id."' ";
			$query  = $this->getAdapter()->select()->from('Classes',array('class_name','class_id'))->where($where)->order('class_name ASC');
			$class_arr=array();
			$class_arr=$query->query()->fetchAll();
			}
			else
			{
				
			$query  = $this->getAdapter()->select()->from('teacher_classes',array('teacher_class_classid','teacher_class_userid'))->where('teacher_class_userid="'.$userLogged->user_id.'"')->joinLeft('Classes',"Classes.class_id=teacher_classes.teacher_class_classid",array('class_name','class_id'))->where('class_id!=""')->group('class_id')->order('class_name ASC');
			
			$class_arr=array();
			$class_arr=$query->query()->fetchAll();
			
			
			}
			return $class_arr;
	}
}