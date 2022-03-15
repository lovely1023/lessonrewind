<?php
class Application_Form_StaticForm extends Twitter_Bootstrap_Form_Vertical
{
 
 	public function init(){
		
		$this->setMethod('post');
		
		$this->setAction(self::METHOD_POST);
		
		$this->setAttribs(array(
			'id' => 'validate',
			"role"=>"form",
			'class' => 'default-form  validate',
			"novalidate"=>"novalidate"
		));
  		
	}
 
	 public function subadmin($user_id=false){

		

		

		$this->setAttribs(array(

 			'class' => 'profile_form',

 			'novalidate'=>'novalidate',

			"role"=>"form",

			'enctype'=>'multipart/form-data'

		));


		global $roleArr;

 		$this->addElement('text', 'user_first_name', array (

			"required" => TRUE,

			'class' => 'form-control required',

			"label" => "First Name<span class='required'>*</span>" ,

			"filters"    => array("StringTrim","StripTags"),

			"validators" =>  array(

								array("NotEmpty",true,array("messages"=>"First Name is required ")),

 							),

 		));
		
		$this->addElement('text', 'user_last_name', array (

			"required" => TRUE,

			'class' => 'form-control required',

			"label" => "Last Name<span class='required'>*</span>" ,

			"filters"    => array("StringTrim","StripTags"),

			"validators" =>  array(

								array("NotEmpty",true,array("messages"=>"Last Name is required ")),

 							),

 		));
		$this->addElement('text', 'user_email', array (

			"required" => TRUE,
			"autocomplete"=>"off",

			'class' => 'form-control required checkemail email',

			"label" => "Email Address<span class='required'>*</span>" ,

			"filters"    => array("StringTrim","StripTags"),

			"validators" =>  array(

								array("NotEmpty",true,array("messages"=>"Email Address is required ")),

 							),

 		));

		if($user_id==false){

			

			$this->user_email->setAttrib("class","form-control required checkemail email  ");

			

			$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));

			$validator->setMessage("`%value%`  already exists , please enter any other email address");	

			$this->user_email->addValidator($validator);

			

		}

		else

		{

			/*	Form Element  - MaketPlace

		 *	Element Name -  Email Address

		 */

		$this->addElement('text', 'user_email', array(

 			'class' => 'form-control required checkemail_exclude email',

			'required'   => true,

  			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),

			"validators" => array(

								array("NotEmpty",true,array("messages"=>" Email address is required ")),

								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))

							),

		));

		

		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email',

			 'exclude' => array(

				'field' => 'user_id',

				'value' => $user_id

        )



		));

		$validator->setMessage("`%value%`  already exists , please enter any other email address");	

		$this->user_email->addValidator($validator);

		}

		$this->addElement('text', 'user_email_test', array(

 			'class'=>'displaynone',

		));
			$this->addElement('password', 'user_email_password', array(

 			'class'=>'displaynone',

		));

		$this->addElement('password', 'user_password', array(

 			"class"      => "form-control required",

			"label"   => "Enter Password",

			"autocomplete" =>"off",

 			"filters"    => array("StringTrim","StripTags","HtmlEntities"),

			"validators" =>  array(

								array("NotEmpty",true,array("messages"=>" Password is required ")),

								array("StringLength" , true,array('min' => 6, 'max' => 16, 'messages'=>"Password must between 6 to 16 characters ")),

							),

		));

		

 		$this->addElement('password', 'user_rpassword', array(

 			"class"      => "form-control required",

			"label"   => "Re Type  Password",

			"autocomplete" =>"off",

			"filters"    => array("StringTrim","StripTags","HtmlEntities"),

			"validators" =>  array(

								array("NotEmpty",true,array("messages"=>"Confirm Password is required ")),

								array("StringLength" , true,array('min' => 6, 'max' => 16, 'messages'=>"Confirm Password must between 6 to 16 characters ")),

							),

		));

		

		if($user_id==''){

			$this->user_password->setAttrib('class','form-control required');

			$this->user_rpassword->setAttrib('class','form-control required');

			

			$validator = new Zend_Validate_Identical(array('token' =>"user_rpassword"));

			$validator->setMessage(" Password Mismatch ,please enter correct password");	

			$this->user_password->addValidator($validator);

			

			$validator = new Zend_Validate_Identical(array('token' =>"user_password"));

			$validator->setMessage(" Password Mismatch ,please enter correct password");	

			$this->user_rpassword->addValidator($validator);

			

		}

		else{

			$this->addElement('hidden', 'user_id');

		}

		
	
	
	//array_unshift($roleArr, "Assign Role");
			$this->addElement('MultiCheckbox', 'user_roles', array(
			
 			"class"      => "form-control required ",

			"required"   => true,

			"label"   => "Assign Roles",

			"multiOptions" =>$roleArr,

			

		));

		

		

	/*	$this->addElement('Multiselect', 'user_roles', array(

 			"class"      => "form-control required ",

			"required"   => true,

			"label"   => "Assign Roles",

			"multiOptions" =>$roleArr,

			"value" =>0,

		));*/

		

		

		

 		$this->submitButton();

 

  	}
 	/* 
	 *	Static Page Form
	 */
	public function page(){
		
 		
		 ## --- Page Title ---##	
 		$this->addElement('text', 'page_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Page Title" ,
			"label" => "Page Title <span class='required'>*</span>" ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Page Title is required ")),
 							),
 		));
	 

        ## ---- Page Content ---##
 		$this->addElement('textarea', 'page_content', array (
			"required" => TRUE,
			'class' => 'form-control ckeditor',
			'id' => 'cleditor',
			'rows'=>'6',
			"label" => "Page Content <span class='required'>*</span>",
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Page Content is required")),
 							),
		));
 
 		$this->submitButton();
 
  	}
	
	
	public function subscription(){
		
 		
		 ## --- Page Title ---##	
		 $this->addElement('select', 'subscription_account_type', array (
		
			"disabled"=>"disabled",
			'class' => 'form-control ',
			"multioptions"=>array(
				"0"=>"30 days trial",
				"1"=>"Bronze",
				"2"=>"Silver",
				"3"=>"Gold",
				"4"=>"Platinum",
				"5"=>"Platinum Plus",
				
			),
			"label" => "Account Type <span class='required'>*</span>" ,
			"filters"    => array("StringTrim","StripTags"),
			
 		));
		 
 		$this->addElement('text', 'subscription_plan_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Plan Title" ,
			"label" => "Plan Title <span class='required'>*</span>" ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Plan Title is required ")),
 							),
 		));
	 

        ## ---- Page Content ---##
 		$this->addElement('textarea', 'subscription_plan_description', array (
			"required" => TRUE,
			'class' => 'form-control',
			'id' => 'cleditor',
			'rows'=>'6',
			"label" => "Plan Description <span class='required'>*</span>",
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Plan Description is required")),
 							),
		));
		
		 ## --- Page Title ---##	
 		$this->addElement('text', 'subscription_plan_price', array (
			"required" => TRUE,
			'class' => 'form-control required number',
			"placeholder" => "Plan Price" ,
			"label" => "Plan Price <span class='required'>*</span> (Per month)" ,
			"filters"    => array("StringTrim","StripTags"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Plan Price is required ")),
								array("Digits",true,array("messages"=>"Please enter numbers in Plan price")),
								
								 							),
 		));
 
 		$this->submitButton();
 
  	}
	
 	public function content_block(){
 		
 	 
   		$this->addElement('text', 'content_block_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Content Block Title" ,
			"label" => "Content Block Title" ,
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Content Block Title is required")),
 							),
 		));
		
		
 	 	

        ## ---- Page Content ---##
 		$this->addElement('textarea', 'content_block_content', array (
			"required" => TRUE,
			'class' => 'form-control ckeditor',
			'id' => 'cleditor',
			'rows'=>'9',
			"label" => "Content",
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Content is required")),
 							),
		));
		
  		
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="icon-ok"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
  	} 		 
	
 
 	public function navigation(){
		
		$modelUser = new Application_Model_User();
		$modelNavigation = new Application_Model_Navigation();
		$modelContent = new Application_Model_Content();
		
 		
 		$this->addElement('text', 'menu_title', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Menu Title" ,
			"label" => "Menu Title" 
 		));
		
		
		/* Page Drop Down For the Menu */
		$parentPages = $modelNavigation->getParentMenuList();
  		$this->addElement('select', 'menu_parent_id', array(
							'class'      => 'form-control ',
  							'label'=>'Select Menu Parent',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $parentPages
		));
 		
		
		
		$getPageList = $modelContent->getPageList();
   		$this->addElement('select', 'menu_page_id', array(
					  'class'      => ' form-control required',
 					  'label'=>' Select Page For Menu  ',
					  'validators' => array('NotEmpty'),
					  "filters"    => array("StringTrim","StripTags","HtmlEntities"),
					  'multiOptions' => $getPageList
		));
		
		$post=  array("Header"=>"Show in Header","Footer"=>"Show in Footer","Both"=>"Show in Both Positions");		
		$this->addElement('select', 'menu_show', array(
							'class'      => 'form-control required',
							'required'   => true,	
 							'label'=>'Menu Show on ',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $post
		));
		
  		$this->addElement('text', 'menu_permalink', array (
			"required" => TRUE,
			'class' => 'form-control required',
			"placeholder" => "Menu Peramlink" ,
			"label" => "Menu Peramlink" 
 		));
 		

         
 		$status=  array("0"=>"NO Index","1"=>"Index");		
		$this->addElement('select', 'menu_index', array(
							'class'      => 'form-control required',
							'required'   => true,	
 							'label'=>'Menu Index',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $status
		));
 		
		
		$status=  array("0"=>"No Follow","1"=>"Follow");		
		$this->addElement('select', 'menu_follow', array(
							'class'      => 'form-control required',
							'required'   => true,	
 							'label'=>'Menu Follow',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $status
		));
		
		$status=  array("0"=>"Draft","1"=>"Publish");		
		$this->addElement('select', 'menu_status', array(
							'class'      => 'form-control required',
							'required'   => true,	
 							'label'=>'Menu Status',
 							'validators' => array('NotEmpty'),
							"filters"    => array("StringTrim","StripTags","HtmlEntities"),
							'multiOptions' => $status
		));
		
		
		$this->addElement('textarea', 'menu_meta_keywords', array (
			
			'class' => 'form-control',
 			'rows'=>'6',
			"label" => "Meta Keywords",
			"placeholder" => "Meta Keywords"
		));
		
		
		 $this->addElement('textarea', 'menu_meta_description', array (
 			'class' => 'form-control  ',
 			'rows'=>'6',
			"label" => "Meta Description",
			"placeholder" => "Meta Description"
		));
		
		
		 $this->addElement('textarea', 'menu_google_code', array (
 			'class' => 'form-control   ',
 			'rows'=>'6',
			"label" => "Google Analytics Code For Page",
			"placeholder" => "Google Analytics Code For Page"
		));
		
		
		$getRequestAdminList = $modelUser->getRequestAdminList();
  		$this->addElement('select', 'request_admin', array(
					  'class'      => ' form-control',
 					  'label'=>' Assign Request Admin ',
					  'validators' => array('NotEmpty'),
					  "filters"    => array("StringTrim","StripTags","HtmlEntities"),
					  'multiOptions' => $getRequestAdminList
		));
		
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="icon-ok"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
  
 	}
	
	
	/* 
		Site Configuration Form
	 */
	public function configuration($type=false){
		
		$modelStatic = new Application_Model_Static() ;
		
  		
		$fields = $modelStatic->getConfigs($type);
		
 		foreach($fields as $key=>$values){
			
			if($values['config_key']=='home_page_first' || $values['config_key']=='home_page_second' || $values['config_key']=='review_from_home' || $values['config_key']=='school_admin' || $values['config_key']=='for_teachers'  || $values['config_key']=='for_student' || $values['config_key']=='differ_file_type')
			{
				$this->addElement('textarea',$values['config_key'], array (
				"required" => TRUE,
				'class' => 'form-control required ckeditor',
				"placeholder" => $values['config_title'] ,
				"label" => $values['config_title'] ,
				"value"=>$values['config_value']
			));		
			}
			else
			{
 			$this->addElement('text',$values['config_key'], array (
				"required" => TRUE,
				'class' => 'form-control required',
				"placeholder" => $values['config_title'] ,
				"label" => $values['config_title'] ,
				"value"=>$values['config_value']
			));			
			}
			
			
		}
		
		$this->submitButton();
		
	 
 	 
 	}
	
	
 	
	public function email_template(){
		
 		 
	 		## ---- EMAIL TEMPLETS TITEL  ---- ##
		$this->addElement('text', 'emailtemp_title', array (
			'class' => 'form-control required',
			"placeholder" => "Input Email Title",
			"label" => "Input Email Title",
			'validators' => array( array('NotEmpty', true, array ("messages" => "Please enter email title")))
		));


		## ---- EMAIL TEMPLETS SUBJECT  ---- ##
		$this->addElement('text', 'emailtemp_subject', array (
			'class' => 'form-control required',
			"placeholder" => "Input Email Subject",
			"label" => "Input Email Subject"
		));

	   ## ---- EMAIL TEMPLETS CONTENT  ---- ##
		$this->addElement('textarea', 'emailtemp_content', array (
			'class' => 'ckeditor form-control ',
			'id' => 'cleditor',
			"placeholder" => "Email Content Here " ,
			"label" => "Input Email Title",
		 
		));
		
		$this->submitButton();
		
 		
	}
	
	
	
	public function graphic_media(){
 		
		$this->addElement('text', 'media_title', array (
			'class' => 'form-control required',
			"placeholder" => " Title",
			"required"=>true,
			"label"=>' Title : <span class="required">*</span>',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" This field is required ")),
							),
		));
		
		
		$this->addElement('text', 'media_alt', array (
			'class' => 'form-control required',
			"placeholder" => " Alternate Text",
			"required"=>true,
			"label"=>' Alternate : <span class="required">*</span>',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" This field is required ")),
							),
		));
		
		
	 
		/* User Video Data */
 		$this->addElement('file', 'media_path', array (
			"placeholder" => " Upload  ",
			"id" => "media_path_image",
			"required"=>true,
 			"class" => "form-control",
			"label"=>"Upload Photo "
		));
		
 		$this->media_path->setDestination(MEDIA_IMAGES_PATH)
			->addValidator('Extension', false,"jpg,JPG,png,PNG,jpeg,JPEG")
			->addValidator('Size', false, "15MB");
		 
   		$this->submitButton();		
		
		
		
	}
	
	
	
	public function submitButton(){
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="fa fa-check"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
	}
	
	
	
}

?>
