<?php
class Application_Form_SchoolForm extends Twitter_Bootstrap_Form_Vertical
{
	
	public function init(){
 
  		$this->setMethod('post');
 		
		$this->setAttribs(array(
 			'class' => 'profile_form',
 			'novalidate'=>'novalidate',
			"role"=>"form",
			'enctype'=>'multipart/form-data'
		));
  	}
	
	
	public function addnewfamily()
	{
			$this->addElement('text', 'family_first_name', array (
			'class' => 'form-control required',
			"placeholder" => "First Name",
			"required"=>true,
			"label" => "First Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"First Name is Required ")),
 							),
  		));
		
		$this->addElement('text', 'family_last_name', array (
			'class' => 'form-control required',
			"placeholder" => "Last Name",
			"required"=>true,
			"label" => "Last Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Last Name is Required ")),
 							),
  		));
		
		$this->addElement('text', 'family_email_address', array (
			'class' => 'form-control  email checkemail_newfamily',
			"placeholder" => "Email Address",
			/*"required"=>true,*/
			"label" => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			/*"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Email Address is Required ")),
 							),*/
  		));
		
		
		
	}
	
	
	public function importdata($type=false)
	{
			
			$this->addElement('file', 'bulkimport_file', array (
			'class' => 'form-control required',
			"required"=>true,
			"label" => "Select File",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Import File is Required")),
 							),
  			));
			
			$this->bulkimport_file->setDestination(IMPORT_ATTACH)	
			->addValidator('Extension', false,EXCEL_VALID_EXTENTIONS)
			->addValidator('Size', false, IMAGE_VALID_SIZE);
			$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Import Data',
				'escape'=>false
				));
				$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions import_data'))	));
			
			/*if($type==0 || $type==1)
			{
			$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Import Data',
				'escape'=>false
				));
				$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions import_data'))	));
			}
			
			if($type==1 || $type==2)
			{
				$this->addElement('button', 'bttnsubmit1', array (
				'class' => 'btn blue btn-primary ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Update / Replace Data',
				'escape'=>false
				));
				$this->bttnsubmit1->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions update_replace_data'))	));
			}
			*/	
			
	}
 	
	/* Form For Add New Class */
	public function newclass($class_id=false)
	{
		$user = isLogged(true);
		 $model = new Application_Model_Static();
		 global $day_array;
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Class Name
		 */
		 if(!$class_id)
		 {
			 
			$this->addElement('radio', 'class_type', array (
			'class' => 'form-control  classtype',
			"placeholder" => "Class Name",
			
			"label" => "Class Type",
			"Multioptions"=>array(
				"0"=>"Create New",
				"1"=>"Select from saved Template"
			),
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Class Type is Required ")),
 							),
  				));
		
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Class Name
		 */
		// echo "class_template='1' and (class_insertid='".$user->user_id."' or class_insertid='".$user->user_school_id."' or class_school_id='".$user->user_school_id."' or class_school_id='".$user->user_id."')";
		 	
		 $temp_array=array();
		 $temp_array=$model->PrepareSelectOptions_withdefault("Classes" ,"class_id","class_name","class_template='1' and (class_insertid='".$user->user_id."' or class_insertid='".$user->user_school_id."' or class_school_id='".$user->user_school_id."' or class_school_id='".$user->user_id."')","class_id","Select from save template");
		
 			$this->addElement('select', 'class_template_name', array (
			'class' => 'form-control ',
			"onchange"=>"getsavetemplate(this,'".SITE_HTTP_URL."/class/getsavetemplatedata/id/')",
			"label" => "Template Class",
			"Multioptions"=>$temp_array,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
  		));
			 
		}
 		
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Class Name
		 */
 		$this->addElement('text', 'class_name', array (
			'class' => 'form-control required',
			"placeholder" => "Class Name",
			"required"=>true,
			"label" => "Class Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Class Name is Required ")),
 							),
  		));
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Class Date
		 */
		 
		 $this->addElement('radio', 'class_date_type', array (
			'class' => 'form-control  class_date_type',
			"placeholder" => "Class Name",
			
			"label" => "Class Type",
			"Multioptions"=>array(
				"0"=>"Ongoing Class",
				"1"=>"Ongoing Class - Include Time",
				"2"=>"Class with Specific Date Range",
				"3"=>"Class with Specific Date Range - Include Time "
			),
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Class Date Type is Required ")),
 							),
  		));
		 
 		$this->addElement('text', 'class_date', array (
			'class' => 'form-control  daterangetime',
			"placeholder" => "Class Date and Time",
			"readonly"=>"readonly",
			
			"label" => "Class Date and Time",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Class Date is Required ")),
 							),
  		));
		
		$this->addElement('text', 'date_timepicker_start', array (
			'class' => 'form-control ',
			"placeholder" => "Start Time",
			"readonly"=>"readonly",
			"label" => "Class Start Time",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Class Start Time is Required ")),
 							),
  		));
		
		$this->addElement('text', 'date_timepicker_end', array (
			'class' => 'form-control ',
			"placeholder" => "End Time",
			"readonly"=>"readonly",
			"label" => "Class End Time",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Class End Time is Required ")),
 							),
  		));
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Instruments
		 */
		$this->addElement('text', 'class_date_only', array (
			'class' => 'form-control  daterangeonly',
			"placeholder" => "Select Date",
			"readonly"=>"readonly",
			"label" => "Class Date",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Class Date is Required ")),
 							),
  		));
		
		
 		$this->addElement('MultiCheckbox', 'class_days', array (
			'class' => ' required',
			"multioptions" =>$day_array,
			"required"=>true,
			"label" => "Days",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Days is Required ")),
 							),
  		));
		$this->_submitButton();
		if(!$class_id)
		{
				$this->_submitsaveButton();		
		}
	
	}
	/* Form For Sub Admin */
	public function subadmin($user_id=false)
	{
		$user = isLogged(true);
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"placeholder" => "First Name",
			"required"=>true,
			"label" => "Admin’s First Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" First Name is Required ")),
 							),
  		));
 	
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Last Name
		 */
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"placeholder" => "Last Name",
			"required"=>true,
			"label" => "Admin’s Last Name",
			"onchange"=>"account_holder_name()",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Last Name is Required ")),
 								),
		));
		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email ",
			'autocomplete'=>'on',
			"required"   => true,
			"placeholder"   => "Email Address",
			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
		 if(isset($user_id) && !empty($user_id))
		 {
			 
			  $this->user_email->setAttrib("class","form-control required checkemail_exclude_mail email  ");
			 $validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email',
			 'exclude' => array(
				'field' => 'user_id',
				'value' => $user_id
        )

		));
			$validator->setMessage("`%value%`  already exists , please enter any other email address");	
			$this->user_email->addValidator($validator);
				
		 }
		 else
		 {
			
			 $this->user_email->setAttrib("class","form-control required checkemail email  ");
		
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);	 
		 }
		
   		 
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Classes
		 */
		$model = new Application_Model_Static();
		
 		$this->addElement('MultiCheckbox', 'admin_permission', array (
			'class' => ' required',
			"multioptions" =>array(
				"1"=>"Manage Admin",
				"2"=>"Manage Families",
				"3"=>"Manage Students",
				"4"=>"Manage Teachers",
				"5"=>"Manage Classes",
				"6"=>"Manage Instruments",
				"7"=>"Manage Announcements",
			),
			"required"=>true,
			"label" => "Select Permissions",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Permission is Required ")),
 							),
		"value"=>"",
  		));
		
		
		
 		$this->_submitButton();
		
 					
	}
	/* Form All Students */
	public function allstudents()
	{
		$user = isLogged(true);
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Classes
		 */
		$model = new Application_Model_Static();
		$student_array=array();
		$student_array=$model->PrepareSelectOptions_withdefault_user("users","user_id","user_last_name","user_first_name","(user_school_id='".$user->user_school_id."' or user_school_id='".$user->user_id."' ) and user_type='student' and user_status='1'","user_last_name","");
		
 		$this->addElement('MultiCheckbox', 'instrument_student', array (
			"multioptions" =>$student_array,
			
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		
  		));
		
		
		
 		$this->_submitButton();	
	}
	
	/* Form All Teachers */
	public function allteachers()
	{
		$user = isLogged(true);
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Classes
		 */
		$model = new Application_Model_Static();
		$student_array=array();
		$student_array=$model->PrepareSelectOptions_withdefault_user("users","user_id","user_last_name","user_first_name","(user_school_id='".$user->user_school_id."' or user_school_id='".$user->user_id."' ) and user_type='teacher' and user_status='1'","user_last_name","");
		
 		$this->addElement('MultiCheckbox', 'instrument_teachers', array (
			"multioptions" =>$student_array,
			
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		
  		));
		
 		$this->_submitButton();	
	}
	
	/* Form For Family */
	public function getfamilystudent()
	{
		 $user = isLogged(true);
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Classes
		 */
		$model = new Application_Model_Static();
		$student_array=array();
		$student_array=$model->PrepareSelectOptions_withdefault_user("users","user_id","user_first_name","user_last_name","user_school_id='".$user->user_school_id."' and user_type='student'","user_id","");
	
 		$this->addElement('MultiCheckbox', 'family_student', array (
			'class' => ' required',
			"multioptions" =>$student_array,
			"required"=>true,
			"label" => "Select Students",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Please select student")),
 							),
		
  		));
		
		
		
 		$this->_submitButton();
			
	}
	/* Form For  Add New Lesson */
	public function newlesson($tacher_id=false,$lesson_id=false,$student_id=false,$class_id=false)
	{
		$user = isLogged(true);
		 $model = new Application_Model_Static();
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Student First Name
		 */
		 if(!$lesson_id)
		 {
			 
			$this->addElement('radio', 'lesson_type', array (
			'class' => 'form-control  lessontemplate',
			"label" => "Lesson Type",
			"Multioptions"=>array(
				"0"=>"Create New",
				"1"=>"Select From Saved Template",
				"2"=>"Create New Lesson from Old Lesson (or Previously Started 'Save Without Sending' Lesson.)",
				"3"=>"Student Absent",
			),
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
							 array("NotEmpty",true,array("messages"=>"Lesson Type is Required ")),
 							),
  		));
		
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Class Name
		 */
		// echo "class_template='1' and (class_insertid='".$user->user_id."' or class_insertid='".$user->user_school_id."' or class_school_id='".$user->user_school_id."' or class_school_id='".$user->user_id."')";
		 	
		 $temp_array=array();
		 if($user->user_type=='school')
		 {
			 
			 $temp_array=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_template='1'  and (lesson_teacherid='".$user->user_id."' or lesson_school_id='".$user->user_id."') ","lesson_id","Select from saved template");	 
		}
		 else
		 {
			$temp_array=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_template='1'  and (lesson_teacherid='".$user->user_id."') ","lesson_id","Select from saved template");	 
		}
		
		
		
 			$this->addElement('select', 'lesson_template_name', array (
			'class' => 'form-control ',
			"onchange"=>"getsavetemplatelesson(this,'".SITE_HTTP_URL."/teacher/getsavetemplatedata/id/')",
			"label" => "Template Lesson",
			"Multioptions"=>$temp_array,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
  		));
		
		/* $temp_array_notsent=array();
		$temp_array_notsent=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_status='0' and lesson_template='0' and (lesson_teacherid='".$user->user_id."')","lesson_id","Select from previously started lesson ");
		
		
 			$this->addElement('select', 'lesson_notsaved_name', array (
			'class' => 'form-control ',
			"onchange"=>"getnotsavedlesson(this,'".SITE_HTTP_URL."/teacher/getnotsavedlesson/id/')",
			"label" => "Previously started lesson",
			"Multioptions"=>$temp_array_notsent,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
  		));*/
		
		 $old_lesson_array=array();
		
		 if($user->user_type=='teacher')
		 {
			$label='All old lessons';
			 if($student_id)
			 {
				 	$label='All old lessons of this student';
				 $lesson_student=$model->Super_Get("lesson_student","l_s_stuid='".$student_id."'","fetch",array("fields"=>array("GROUP_CONCAT(l_s_lessid) as lessid")));
				 if($lesson_student['lessid']!='')
				 {
						$old_lesson_array=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_template='0'  and (lesson_teacherid='".$user->user_id."' or lesson_school_id='".$user->user_school_id."') and lesson_id IN(".$lesson_student['lessid'].")",array("lesson_status DESC","lesson_id DESC"),"Select from old lesson");		 	 
				 }
				 
			 }
			 else if($class_id)
			 {
				 	$label='All old lessons of this class';
				 $old_lesson_array=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_template='0'  and (lesson_teacherid='".$user->user_id."' or lesson_school_id='".$user->user_school_id."') and lesson_class_id='".$class_id."'",array("lesson_status DESC","lesson_id DESC"),"Select from old lesson");		 	 
			 }
			 else
			 {
					 $old_lesson_array=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_template='0'  and (lesson_teacherid='".$user->user_id."' or lesson_school_id='".$user->user_school_id."') ",array("lesson_status DESC","lesson_id DESC"),"Select from old lesson");	
				}
			
			 
		}
		 else
		 {
			 if($student_id)
			 {
				 	$label='All old lessons of this student';
				  $lesson_student=$model->Super_Get("lesson_student","l_s_stuid='".$student_id."'","fetch",array("fields"=>array("GROUP_CONCAT(l_s_lessid) as lessid")));
				  if($lesson_student['lessid']!='')
				  {
						$old_lesson_array=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_template='0'  and (lesson_teacherid='".$user->user_id."' or lesson_school_id='".$user->user_id."') and lesson_id IN(".$lesson_student['lessid'].")",array("lesson_status ASC","lesson_id DESC"),"Select from old lesson");	   
				  }
			 }
			 else if($class_id)
			 {
				 	$label='All old lessons of this class';
					$old_lesson_array=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_template='0'  and (lesson_teacherid='".$user->user_id."' or lesson_school_id='".$user->user_id."') and lesson_class_id='".$class_id."'",array("lesson_status ASC","lesson_id DESC"),"Select from old lesson");	 
			}
			 else
			 {
			 	$old_lesson_array=$model->PrepareSelectOptions_withdefault("lesson" ,"lesson_id","lesson_title","lesson_template='0'  and (lesson_teacherid='".$user->user_id."' or lesson_school_id='".$user->user_id."') ",array("lesson_status ASC","lesson_id DESC"),"Select from old lesson");	 
			 }
		}
		
		
		
 			$this->addElement('select', 'lesson_old_name', array (
			'class' => 'form-control ',
			"onchange"=>"getsavetemplatelesson(this,'".SITE_HTTP_URL."/teacher/getsavetemplatedata/id/')",
			"label" => $label,
			"Multioptions"=>$old_lesson_array,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
  		));
		
			 
		}
		 
		if($class_id)
		{
	 		 $student_class=array();
			 $student_class=$model->Super_Get("student_class","student_class_classid='".$class_id."'","fetchAll");
			 $class_options=array();
			 foreach($student_class as $ks=>$vs)
			 {		
				 $user_arr=array();
			 	 $user_arr=$model->Super_Get("users","user_id='".$vs['student_class_studentid']."'","fetch");
				 $class_options[$ks]['key']=$user_arr['user_id'];
				 $class_options[$ks]['value']=$user_arr['user_first_name'].' '.$user_arr['user_last_name'];
			 }
			
 			$this->addElement('MultiCheckbox', 'student_class', array (
					'class' => ' required',
					"label" => "Currently Enrolled Student List (for this class) <br> <div style='font-size:12px;'>(Only students with a check mark next to their name will receive this lesson.)</div>",
					"checked"=>"checked",
					"style"=>"height:100px",
					"multiple"=>'multiple',
					"Multioptions"=>$class_options,
					"filters"    => array("StringTrim","StripTags","HtmlEntities"),
  			));
		}

		//=========== add ================
 		$this->addElement('text', 'lesson_date', array (
			'class' => 'form-control col-md-4 required',
			"type" => 'date',
			"pattern" => "\d{4}-\d{2}-\d{2}",
			"placeholder" => "Lesson Date",
			"required"=>true,
			"label" => "Lesson Date",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Lesson Date is Required ")),
 							),
  		));
 		//==================================
		 
		 $this->addElement('text', 'lesson_title', array (
			'class' => 'form-control required',
			"placeholder" => "Lesson Title",
			"required"=>true,
			"label" => "Lesson Title",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"First Name is Required ")),
 							),
  		));
		

		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Student Last Name
		 */
 		$this->addElement('textarea', 'lesson_desc', array (
			'class' => 'form-control required ckeditor',
			"placeholder" => "Lesson Description",
			"required"=>true,
			"label" => "Lesson Description",
			
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Lesson Description is Required ")),
 							),
  		));
		
		 $choose_files_from_folder=array();
		 $choose_files_from_folder=$model->PrepareSelectOptions_withdefault("teacher_attachments" ,"teacher_attach_id","teacher_attach_filename",'teacher_attach_userid="'.$user->user_id.'" and teacher_attach_filename!=""',"teacher_attach_id");
		
		
 			$this->addElement('multiselect', 'existing_fold', array (
			'class' => 'form-control ',
			"label" => "Choose Files From Folder",
			"style"=>"height:100px",
			"Multioptions"=>$choose_files_from_folder,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
  		));
			 
		
		
		$this->addElement('hidden', 'param', array (
			
  		));
		
			$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary ',
				'ignore'=>true,
				'type'=>'button',
 				'label'=>'Send',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'inline-block text-right'))	)); 
		if(!$lesson_id)
		{
				
		
	/*	$this->addElement('button', 'bttnsubmittemplate', array (
				'class' => 'btn blue btn-primary  marginleft',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Save As Template And Send',
				"onclick"=>"lessontemplate()",
				'escape'=>false
		));
		$this->bttnsubmittemplate->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'table-cell text-right'))));
		
		
		$this->addElement('button', 'bttnsubmittemplatenotsave', array (
				'class' => 'btn blue btn-primary  marginleft',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Save As Template Without Sending',
				"onclick"=>"lessontemplatenotsend()",
				'escape'=>false
		));
		$this->bttnsubmittemplatenotsave->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'table-cell text-right'))));*/
		
		}
	
			$this->addElement('button', 'bttnsubmitsend', array (
				'class' => 'btn blue btn-primary marginleft',
				'ignore'=>true,
				'type'=>'submit',
				"onclick"=>"lessonsend()",
 				'label'=>'Save Without Sending',
				'escape'=>false
		));
		$this->bttnsubmitsend->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'inline-block text-right'))));
			
	}
	/* Form For Add New Lesson */
	
	/* Form For  Add New Lesson Template */
	public function newlessontemplate($tacher_id=false,$lesson_id=false)
	{
		$user = isLogged(true);
		 $model = new Application_Model_Static();
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Student First Name
		 */
		
		 
 		$this->addElement('text', 'lesson_title', array (
			'class' => 'form-control required',
			"placeholder" => "Lesson Title",
			"required"=>true,
			"label" => "Lesson Title",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"First Name is Required ")),
 							),
  		));

		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Student Last Name
		 */
 		$this->addElement('textarea', 'lesson_desc', array (
			'class' => 'form-control required ckeditor',
			"placeholder" => "Lesson Description",
			"required"=>true,
			"label" => "Lesson Description",
			
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Lesson Description is Required ")),
 							),
  		));
		
		 $choose_files_from_folder=array();
		 if($user->user_type=='teacher')
		 {
			$choose_files_from_folder=$model->PrepareSelectOptions_withdefault("teacher_attachments" ,"teacher_attach_id","teacher_attach_name",'teacher_attach_userid="'.$user->user_id.'"',"teacher_attach_id");	 
		 }
		 else
		 {
				$choose_files_from_folder=$model->PrepareSelectOptions_withdefault("teacher_attachments" ,"teacher_attach_id","teacher_attach_name",'teacher_attach_userid="'.$user->user_id.'"',"teacher_attach_id"); 
		 }
		 
		
		
 			$this->addElement('multiselect', 'existing_fold', array (
			'class' => 'form-control ',
			"label" => "Choose Files From Folder",
			"style"=>"height:100px",
			"Multioptions"=>$choose_files_from_folder,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
  		));
			 
		
		
		$this->addElement('hidden', 'param', array (
			
  		));
		
			
		
		
		
		$this->addElement('button', 'bttnsubmittemplatenotsave', array (
				'class' => 'btn blue btn-primary  marginleft',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Save As Template',
				"onclick"=>"lessontemplatenotsend()",
				'escape'=>false
		));
		$this->bttnsubmittemplatenotsave->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'table-cell text-right'))));
		
		
	
		
			
	}
	/* Form For Add New Lesson  Template */
	public function newaanouncement()
	{
			
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Announcement Title
		 */
 		$this->addElement('text', 'announcement_title', array (
			'class' => 'form-control required',
			"placeholder" => "Title",
			"required"=>true,
			"label" => "Title",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Title is Required ")),
 							),
  		));
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Announcement Description
		 */
 		$this->addElement('textarea', 'announcement_desc', array (
			'class' => 'form-control required ckeditor',
			"placeholder" => "Description",
			"required"=>true,
			"label" => "Description",
			
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Description is Required ")),
 							),
  		));
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Announcement Type
		 */
		
		  /*	$this->addElement('MultiCheckbox', 'announcement_type', array (
			'class' => ' required ',
			"placeholder" => "Audience",
			"required"=>true,
			"label" => "Audience",
			"Multioptions"=>array(
				//""=>"Select Audience",
				"0"=>"Teacher",
				"1"=>"Student",
				"2"=>"Family",
				"3"=>"Sub Admin",
			),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Description is Required ")),
 							),
  		));*/
		
		$this->_submitButton();	 	
		
			
	
	}
	/* Form For Add New Student */
	public function newstudent($user_id=false,$school_id=false)
	{
		 $user = isLogged(true);
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Student First Name
		 */
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required checknameuser',
			"placeholder" => "First Name",
			"required"=>true,
			"label" => "First Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"First Name is Required ")),
 							),
  		));
		
		$this->addElement('hidden', 'user_id', array (
		'ignore'=>true,
		
  		));
		$this->addElement('hidden', 'family_firstname_hidden', array (
  		));
		$this->addElement('hidden', 'family_lastname_hidden', array (
  		));
			$this->addElement('hidden', 'family_email_hidden', array (
  		));
		
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Student Last Name
		 */
 		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required checknameuser',
			"placeholder" => "Last Name",
			"required"=>true,
			"label" => "Last Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Last Name is Required ")),
 							),
  		));
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Student Email Address
		 */
 		 if(isset($user_id) && !empty($user_id))
		 {
			
			$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email checkemail_student",
			'autocomplete'=>'on',
			"placeholder"   => "Email Address",
			/*"label"   => "User Name (Email Address) - (optional if already linked to a Family Contact)",*/
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
			  
			  $this->user_email->setAttrib("class","form-control required checkemail_family email  ");
		$this->addElement('text', 'user_username', array(
			"class"      => "form-control required checkexcludeusername",
			'autocomplete'=>'on',
			"placeholder"   => "User Name",
			"label"   => "User Name (Change)",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
									array("NotEmpty",true,array("messages"=>"User Name is Required ")),
							),
		 ));
			  
		 }
		 else
		 {
			
			$this->addElement('text', 'user_email', array(
			"class"      => "form-control  email required checkemail_student",
			'autocomplete'=>'on',
			"onchange"=>"checkmail(this)",
			"placeholder"   => "Email Address",
			/*"label"   => "User Name (Email Address) - (optional if already linked to a Family Contact)",*/
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
		  $this->user_email->setAttrib("class","form-control required checkemail_family email  ");
		 }
		 
		   $this->addElement('text', 'user_email_family', array(
			"class"      => "form-control required email checkemail_family",
			'autocomplete'=>'on',
			"placeholder"   => "Family Email Address",
			/*"label"   => "User Name (Email Address) - (optional if already linked to a Family Contact)",*/
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								
								array("EmailAddress" , true,array("messages"=>" Please enter valid family email address "))
							),
		 ));
			
	
		 
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Classes for student
		 */
		 $model = new Application_Model_Static();
		 $ins_array=$model->PrepareSelectOptions_withdefault("Classes" ,"class_id","class_name","class_insertid='".$school_id."' or class_insertid='".$user->user_school_id."' or class_school_id='".$school_id."' or class_school_id='".$user->user_school_id."'","class_name","");
		$ins_array['None']='None';
 		$this->addElement('MultiCheckbox', 'student_class', array (
			"multioptions" =>$ins_array,
			"class"=>"required " ,
			"onChange"=>"addClassVal(this)",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			'value'=>'None',
			
  		));
		
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Private Teachers for students
		 */
		
		 $teacher_array=$model->PrepareSelectOptions_withdefault_user("users" ,"user_id","user_last_name","user_first_name","user_type='teacher' and (user_insertby='".$school_id."' or user_insertby='".$user->user_school_id."' or  user_school_id='".$school_id."')","user_last_name","");
		$teacher_array['None']='None';
 		$this->addElement('MultiCheckbox', 'student_private_teacher', array (
			"multioptions" =>$teacher_array,
			"required"=>true,
			"class"=>"required" ,
			"onChange"=>"addTeacher(this)",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			'value'=>'None',
  		));
		
		
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Select Family for students
		 */
		
		 $family_array=$model->PrepareSelectOptions_withdefault_user("users" ,"user_id",'user_last_name',"user_first_name","user_type='family' and (user_insertby='".$school_id."' or user_insertby='".$user->user_school_id."')","user_last_name");
		$family_array['None']='None';
 		$this->addElement('MultiCheckbox', 'user_student_family', array (
			"multioptions" =>$family_array,
			"class"      => " required",
			"required"=>true,
			"onChange"=>"addFamily(this)",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			'value'=>'None',
  		));
		
		
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Select Instruments for students
		 */
		$inst_array=array();
		
		 $inst_array=$model->PrepareSelectOptions_withdefault("Instruments" ,"Instrument_id","Instrument_name","((Instrument_userid='".$school_id."' or Instrument_userid='".$user->user_school_id."' or Instrument_schoolid='".$school_id."') ) and Instrument_active='1'","Instrument_name","");
		 $inst_array['None']='None';
		
		
 		$this->addElement('MultiCheckbox', 'user_student_instrument', array (
			"multioptions" =>$inst_array,
			"class"      => " required",
			"required"=>true,
			"onChange"=>"addInstrument(this)",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			'value'=>'None',
			
  		));
		
		
		$this->addElement('radio', 'family_contact', array(
			"class"=>"required",
			"multioptions"=>array('1'=>'Yes',
								'0'=>'No',
								
			),
			"onchange"=>"checkradio(this)",
		
			
		 ));
		 
		 $this->addElement('radio', 'family_type', array(
			"class"=>"required family_type",
			"multioptions"=>array('0'=>'Use this Student as Family Contact',
		     					  '1'=>'Quick add Family contact for this Student',
      							  '2'=>'Link existing Family to this Student',
								  '3'=>'Student Linked to this Family During Bulk Import Process',
								
			),
			
		 ));
		 
		 
		 $this->addElement('radio', 'family_contact_student', array(
			"class"=>"required",
			/*"multioptions"=>array('1'=>'If yes,all notifications will be sent to the Family contact email only.',
								'0'=>'If no,please enter a unique email for this student in the space below and all notifications will be sent to the student and family.',
			),*/
			"multioptions"=>array(
								'0'=>'No Email (notifications will not be sent to student, but lessons will be archived for Teacher records)',
								'1'=>"Send notifications to Family Contact's email only (must link to Family Contact above)",								
								'2'=>'Send notifications to Student only (must enter unique email address of student above)',
								'3'=>"Send notifications to both Student and Family Contact (must add a Family Contact or link to Family Contact above , and enter a unique Student email below)"
			),
			"onchange"=>"checkradiostudent(this)",
		
			
		 ));
		 
		 $this->addElement('hidden', 'user_email_hidden', array(
		 ));
		 
		 $this->addElement('text', 'user_family_firstname', array(
			"class"      => "form-control required  ",
			
		 ));
		 
		 $this->addElement('text', 'user_family_lastname', array(
			"class"      => "form-control required  ",
			
		 ));
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary marginright_send',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Submit',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	)); 	
			
	}
	/* Form For Add Family */
	public function family($user_id=false)
	{
		$user = isLogged(true);
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Family First Name
		 */
		 	
		 
		 
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"placeholder" => "Contact First Name",
			"required"=>true,
			"label" => "Contact First Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"First Name is Required ")),
 							),
  		));
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Class Date
		 */
 		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required ',
			"placeholder" => "Contact Last Name",
			"required"=>true,
			"label" => "Contact Last Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Last Name is Required ")),
 							),
  		));
		
		$this->addElement('hidden', 'newstudent_param', array (
		'value'=>0,
		
  		));
		
		
		 if(isset($user_id) && !empty($user_id))
		 {
			
			$this->addElement('text', 'user_username', array (
			'class' => 'form-control required checkemail_exclude_username',
			"placeholder" => "Username",
			"required"=>true,
			"label" => "Username",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Username is Required ")),
 							),
  			));
			
			$this->addElement('text', 'user_email', array(
			"class"      => "form-control  email required",
			'autocomplete'=>'on',
		/*	"required"   => true,*/
			"placeholder"   => "Email ",
			"label"   => "Email ",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
		  $this->user_email->setAttrib("class","form-control  checkemail_exclude_mail email  ");
		  $validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email',
			 'exclude' => array(
				'field' => 'user_id',
				'value' => $user_id
    		    )

				));
			$validator->setMessage("`%value%`  already exists , please enter any other email address");	
			$this->user_email->addValidator($validator);
				
		 }
		 else
		 {
			
			$this->addElement('checkbox', 'user_login_type',
			 array (
			 'class'=>'pull-left',
			 'onchange'=>'changelogintype(this)',
			 'label'=>'I would like to add Family without an Email',
			'multioptions'=>array(
				'0'=>'I would like to add Family without an Email'
			)
  		));
		
			$this->addElement('text', 'user_email', array(
			"class"      => "form-control   required ",
			'autocomplete'=>'on',
			/*"required"   => true,*/
			"placeholder"   => "Username / Email Address",
			/*"label"   => "User Name (Email Address)",*/
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
							/*	array("NotEmpty",true,array("messages"=>" Email address is required ")),*/
							/*	array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))*/
							),
		 ));
		 
			
		$this->user_email->setAttrib("class","form-control  checkuserandmail  required ");
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);	 
		 }
		 
		 $model = new Application_Model_Static();
		$student_array=array();
		$student_array=$model->PrepareSelectOptions_withdefault_user("users","user_id","user_last_name","user_first_name","(user_school_id='".$user->user_id."' or user_school_id='".$user->user_school_id."') and user_type='student'","user_last_name ASC","");
		$student_array_optn=array();
		foreach($student_array as $k=>$v)
		{
				$student_family=array();
				if(isset($user_id) && !empty($user_id))
		 	{
				$student_family=$model->Super_Get("student_family","s_f_sid='".$k."' and s_f_fid!='".$user_id."'","fetch");
			}
			else
			{
				$student_family=$model->Super_Get("student_family","s_f_sid='".$k."'","fetch");
			}
				if(empty($student_family))
				{
						$student_array_optn[$k]=$v;
				}
		}
	
		
		$student_array_optn['None']='None';
 		$this->addElement('MultiCheckbox', 'family_students', array (
			'class' => ' required',
			"multioptions" =>$student_array_optn,
			"onchange"=>'addStudent(this)',
			'value'=>'None',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Students is Required ")),
 							),
  		));
		 
		 
			$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary marginright_send',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Submit',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
	}
	/* Form For Add New Teacher */
	public function teacher($user_id=false)
	{
		$user = isLogged(true);
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */
		 
		 
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Class Name
		 */
	
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"placeholder" => "Teacher's first Name",
			"required"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Teacher's first Name is Required ")),
 							),
  		));
 	
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Last Name
		 */
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"placeholder" => "Teacher's Last Name",
			"required"=>true,
			"onchange"=>"account_holder_name()",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Teacher's Last Name is Required ")),
 								),
		));
		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email ",
			'autocomplete'=>'on',
			"required"   => true,
			"placeholder"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
		 if(isset($user_id) && !empty($user_id))
		 {
			 
			  $this->user_email->setAttrib("class","form-control required checkemail_exclude_mail email  ");
			 $validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email',
			 'exclude' => array(
				'field' => 'user_id',
				'value' => $user_id
        )

		));
			$validator->setMessage("`%value%`  already exists , please enter any other email address");	
			$this->user_email->addValidator($validator);
				
		 }
		 else
		 {
			
			 $this->user_email->setAttrib("class","form-control required checkemail email  ");
		
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);	 
		 }
		
   		 
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Classes
		 */
		$model = new Application_Model_Static();
		$class_array=array();
		$class_array=$model->PrepareSelectOptions_withdefault("Classes","class_id","class_name","class_insertid='".$user->user_id."' or class_insertid='".$user->user_school_id."' or class_school_id='".$user->user_id."'","class_name","");
		$class_array['None']='None';
 		$this->addElement('MultiCheckbox', 'teacher_class', array (
			'class' => ' required',
			'value'=>'None',
			"multioptions" =>$class_array,
			"required"=>true,
			"onchange"=>"addClassVal(this)",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Classes is Required ")),
 							),
  		));
		 /*	Form Element  - MaketPlace
		 *	Element Name - 	Instruments
		 */
		$ins_array=array();
		$ins_array=$model->PrepareSelectOptions_withdefault("Instruments","Instrument_id","Instrument_name","((Instrument_userid='".$user->user_id."' or Instrument_userid='".$user->user_school_id."' or Instrument_schoolid='".$user->user_id."')) and Instrument_active='1'","Instrument_name","");
		$ins_array['None']='None';
 		$this->addElement('MultiCheckbox', 'teacher_instruments', array (
			'class' => ' required',
			"multioptions" =>$ins_array,
			"onchange"=>"addInstrument(this)",
			'value'=>'None',
			"required"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Instrument is Required ")),
 							),
  		));
		
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Students
		 */
		$student_array=array();
		$student_array=$model->PrepareSelectOptions_withdefault_user("users","user_id","user_last_name","user_first_name","(user_school_id='".$user->user_id."' or user_school_id='".$user->user_school_id."') and user_type='student'","user_last_name","");
		$student_array['None']='None';
 		$this->addElement('MultiCheckbox', 'teacher_students', array (
			'class' => ' required',
			"multioptions" =>$student_array,
			"onchange"=>'addStudent(this)',
			"required"=>true,
			'value'=>'None',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Students is Required ")),
 							),
  		));
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary marginright_send',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Submit',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
 					
	}
	
	/* For for New Instrument */
 	public function newinstrument()
	{
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Instrument Name
		 */
 		$this->addElement('text', 'Instrument_name', array (
			'class' => 'form-control required instrumentname',
			"placeholder" => "Instrument Name",
			"required"=>true,
			"label" => "Instrument Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Instrument Name is Required ")),
 							),
  		));
		
		
		$this->addElement('hidden', 'Instrument_id', array (
  		));
		
	$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary marginright_send',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Submit',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
	}
	 private function _submitButton(){
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary marginright_send',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Submit',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
	}
	
	 private function _submitsaveButton(){
		
		$this->addElement('button', 'bttnsubmittemplate', array (
				'class' => 'btn blue btn-primary floatright marginleft',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'Save As Template',
				'escape'=>false
		));
		$this->bttnsubmittemplate->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))));
		
	}
	 
	 
	 
	
	
}