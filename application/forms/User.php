<?php
class Application_Form_User extends Twitter_Bootstrap_Form_Vertical
{
	
	public function init(){
 
  		$this->setMethod('post');
 		
		$this->setAttribs(array(
 			'class' => 'profile_form',
 			'novalidate'=>'novalidate',
			"role"=>"form",
			"autocomplete"=>"off",
			'enctype'=>'multipart/form-data'
		));
  	}
	
 	
	/* Front User Registration Form */
	public function register(){
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	School Name
		 */

 		$this->addElement('text', 'user_school_name', array (
			'class' => 'form-control required',
			"placeholder" => "School Name",
			"required"=>true,
			"label" => "School Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"School Name is Required")),
 							),
  		));
 	
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */
 		$this->addElement('text', 'user_first_name', array(
			'class' => 'form-control required',
			"placeholder" => "Admin's First Name",
			"required"=>true,
			"label" => "Admin's First Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"School's First Name is Required ")),
 							),
  		));
 	
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Last Name
		 */
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"placeholder" => "Admin's Last Name",
			"required"=>true,
			"label" => "Admin's Last Name",
			"onchange"=>"account_holder_name()",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"School's Last Name is Required ")),
 								),
		));
 	
 				
				
		
 		$this->loginElements();
 		
  		$this->addElement('password', 'user_rpassword', array(
 			"class"      => "form-control required ",
			"required"   => true,
 			"placeholder"   => "Confirm Password",
			"label"   => "Confirm Password",
			"ignore"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 5, 'max' => 50, 'messages'=>"Password must between 5 to 16 characters "))
							),
		));
		
		
 		
		$this->user_email->setAttrib("class","form-control required checkemail email  ");
		
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);
		
		
		$validator = new Zend_Validate_Identical(array('token' =>"user_rpassword"));
		$validator->setMessage(" Password Mismatch ,please enter correct password");	
  		$this->user_password->addValidator($validator);
		
		$validator = new Zend_Validate_Identical(array('token' =>"user_password"));
		$validator->setMessage(" Password Mismatch ,please enter correct password");	
  		$this->user_rpassword->addValidator($validator);
		
		//   $this->add([
		// 	'type' => 'Zend\Form\Element\Captcha',
		// 	'name' => 'captcha',
		// 	'options' => [
		// 		'label' => 'Please verify you are human',
		// 		'captcha' => new Captcha\Dumb(),
		// 	],
		// ]);
 		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Register',
			'class'    => 'btn btn-lg btn-primary btn-block floatright register_button'
		));
		
		//$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
 	}
	
		/* Front User Registration Form */
	public function register1($user_id=false){
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	School Name
		 */
 		$this->addElement('text', 'user_school_name', array (
			'class' => 'form-control required',
			"placeholder" => "School Name",
			"required"=>true,
			"label" => "School Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"School Name is Required ")),
 							),
  		));
 	
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"placeholder" => "School Administrator's First Name",
			"required"=>true,
			"label" => "School Administrator's First Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"School's First Name is Required ")),
 							),
  		));
		
		
		$this->addElement('select', 'user_student_active', array (
			'class' => 'form-control required',
			"placeholder" => "Max Student Count Status",
			"required"=>true,
			"label" => "Max Student Count Status",
			"multioptions"=>array(
				
				"0"=>"Block",
				"1"=>"Active",
				
			),
		
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Max Student Count Status is Required ")),
 							),
  		));
 	
		
		$this->addElement('text', 'user_maxstudent_count', array (
			'class' => 'form-control required number ',
			"placeholder" => "School user max student count",
			"label" => "School user max student count",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		
		));
 	
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Last Name
		 */
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"placeholder" => "School Administrator's Last Name",
			"required"=>true,
			"label" => "School Administrator's Last Name",
			"onchange"=>"account_holder_name()",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"School's Last Name is Required ")),
 								),
		));
 	
 				
				
		
 		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email ",
			'autocomplete'=>'off',
			"required"   => true,
			"placeholder"   => "Email Address",
			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
   		 
		
 		$this->addElement('password', 'user_password', array(
 			"class"      => "form-control    required ",
			
 			"placeholder"   => "Password",
			"label"   => "Password",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								
								array("StringLength" , true,array('min' => 5, 'max' => 16, 'messages'=>"Password must between 5 to 16 characters "))
							),
		));
 		
  		$this->addElement('password', 'user_rpassword', array(
 			"class"      => "form-control required ",
		
 			"placeholder"   => "Confirm Password",
			"label"   => "Confirm Password",
			"ignore"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								
								array("StringLength" , true,array('min' => 5, 'max' => 50, 'messages'=>"Password must between 5 to 16 characters "))
							),
		));
		
		$this->user_email->setAttrib("class","form-control required checkemail email  ");
 		
		if($user_id)
		{
			
		/*$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email',
			 'exclude' => array(
				'field' => 'user_id',
				'value' => $user_id
        )

		));*/
		
			$this->addElement('text', 'user_active_student', array(
 			'class' => 'form-control  number',
			'readonly'=>'readonly',
			'label'      => 'Current Student Count',
			'placeholder'   => 'Current Student Count',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		}
		else
		{
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);
		}
		
		
		
		$validator = new Zend_Validate_Identical(array('token' =>"user_rpassword"));
		$validator->setMessage(" Password Mismatch ,please enter correct password");	
  		$this->user_password->addValidator($validator);
		
		$validator = new Zend_Validate_Identical(array('token' =>"user_password"));
		$validator->setMessage(" Password Mismatch ,please enter correct password");	
  		$this->user_rpassword->addValidator($validator);
		
 			/*	Form Element  - MaketPlace
		 *	Element Name - 	Address
		 */
		$this->addElement('text', 'user_phone', array(
 			'class' => 'form-control  ',
			'label'      => 'Phone Number',
			'placeholder'   => 'Phone Number',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								
								
 							),
		));
		global $account_type;
		/*$this->addElement('select', 'user_account_type', array(
 			'class' => 'form-control required ',
			'required'   => true,
			'multioptions'=>$account_type,
			'label'      => 'Account Type',
			'placeholder'   => 'Account Type',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Account Type is Required ")),
 							),
		));*/
		
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Address
		 */
		$this->addElement('text', 'user_address', array(
 			'class' => 'form-control  ',
			'label'      => 'Number and Street',
			'placeholder'   => 'Address',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		$this->addElement('text', 'user_city', array(
 			'class' => 'form-control  ',
			'label'      => 'City, State, Zip Code',
			'placeholder'   => 'City',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		
		
		 $model = new Application_Model_Static();
	 	  $timezone_array=$model->PrepareSelectOptions_withdefault("timezone" ,"timezone_name","timezone_name","1","timezone_name","Select Timezone");
		
 		 $this->addElement('select', 'user_timezone', array(
 			'class' => 'form-control required',
			'required'   => true,
			'Multioptions'=>$timezone_array,
			'label'      => 'Select Timezone',
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Timezone is Required ")),
 							),
  		));
		 $this->user_timezone->setRegisterInArrayValidator(false);
		
		$this->addElement('textarea', 'user_notes', array(
 			'class' => 'form-control  ',
			/*'required'   => true,*/
			"rows"=>"5",
			'label'      => 'Notes:',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
 		$this->addElement('button', 'submitbtn', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Submit',
			'class'    => 'btn btn-lg pull-right   btn-primary register_button'
		));
		
		$this->submitbtn->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
 	}
	
	public function CreditCardFields()
	{
		//$this->TypeCommonFields();
		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"label" => "First Name",
			"required"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
	
		));
		
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"label" => "Last Name",
			"required"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
	
		));
		
		$this->addElement('text', 'user_cardnumber', array (
			'class' => 'form-control required creditcard',
			"label" => "Card Number",
			"required"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
	
		));
	$this->user_cardnumber->addValidator('CreditCard')
	->addErrorMessage('please enter a valid card number');
		global $credit_year;
		$this->addElement('select', 'user_expirationYear', array (
			'class' => 'form-control required',
			"label" => "Expire Year",
			"Multioptions"=>$credit_year,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		
  		));
		
	
		global $credit_month;
		$this->addElement('select', 'user_expirationMonth', array (
			'class' => 'form-control required',
			"label" => "Expire Month",
			"Multioptions"=>$credit_month,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		
  		));
			global $card_type;
		$this->addElement('select', 'user_card_type', array (
			'class' => 'form-control required',
			"label" => "Card Type",
			"Multioptions"=>$card_type,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Card Type is required ")),
															),
			
  		));
		$this->addElement('text', 'user_cvv', array (
			'class' => 'form-control required cvvno number',
			"label" => "CVV Number",
			"required"=>true,
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
				"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" CVV Number is required ")),
								array("StringLength" , true,array('max' => 4, 'messages'=>"Password must between 3 to 4 characters "))
							),
	
		));
		
		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Continue',
			'class'    => 'btn btn-lg btn-primary floatright '
		));
		
		$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
	}

	public function  loginElements(){
		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email ",
			'autocomplete'=>'off',
			"required"   => true,
			"placeholder"   => "Email Address",
			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
   		 
		
 		$this->addElement('password', 'user_password', array(
 			"class"      => "form-control    required ",
			"required"   => true,
 			"placeholder"   => "Password",
			"label"   => "Password",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 5, 'max' => 16, 'messages'=>"Password must between 5 to 16 characters "))
							),
		));
		
		
		
 		
		
	}
	
	
	public function twitter_email(){
		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email ",
			'autocomplete'=>'off',
			"required"   => true,
			"placeholder"   => "Email Address",
			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							),
		 ));
		
		
		$this->user_email->setAttrib("class","form-control required checkemail email  ");
		
		$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email'));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);
		
		
		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Continue',
			'class'    => 'btn btn-lg btn-primary btn-block '
		));
		
		$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
	}
	
	
	
	
	
	/* Login Form */
	public function login(){
		
		$this->loginElements();
		
  
 		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Login',
			'class'    => 'btn btn-custom-dark row-fluid button-margin-form '
		));
  	}
	
	
	public function login_front($isAdmin = false){
		
		
	
 		$this->loginElements();
		
		if($isAdmin){
			$this->user_email->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			$this->user_password->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		}
		
		
		$this->addElement('checkbox', 'keep_me', array(
			"class"      => "",
			"label"   => "Keep me logged in",
			
		));
		
  
 		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'label'    => 'Submit',
			'type'=>'submit',
			'class'    => 'btn btn-lg btn-primary btn-block floatright register_button'
		));
		
	//	gcm($this->submit);
		
		//prd($this->submit->getAttrib('buttons'));
		//$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
	}
	
	
	public function login_front_update($isAdmin = false){
		
		
	
 	
		
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required ",
			'autocomplete'=>'off',
			"required"   => true,
			"placeholder"   => "Email Address",
			"label"   => "Email Address / User Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" User Name / Email address is required ")),
								/*array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))*/
							),
		 ));
   		 
		
 		$this->addElement('password', 'user_password1', array(
 			"class"      => "form-control    required ",
			"required"   => true,
 			"placeholder"   => "Password", 
			"label"   => "Password",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								
							),
		));
		if($isAdmin){
			$this->user_email->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			$this->user_password1->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		}
		
		
		$this->addElement('checkbox', 'keep_me', array(
			"class"      => "",
			"label"   => "Keep me logged in",
			
		));
		
  
 		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'label'    => 'Submit',
			'type'=>'submit',
			'class'    => 'btn btn-lg btn-primary btn-block floatright register_button'
		));
		
	//	gcm($this->submit);
		
		//prd($this->submit->getAttrib('buttons'));
		//$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
	}
	
	
	
	
	public function forgotPassword(){
			
		$this->addElement('text', 'user_email', array(
			"class"      => "form-control required email emailexists ",
			'autocomplete'=>'off',
			"required"   => true,
			"placeholder"   => "Email Address",
			"label"   => "Email Address",
			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address ")),
								array("Db_RecordExists" , true,array('table' => 'users','field' => 'user_email' ,"messages"=>"`%value%` is not registered , please enter valid email address "))
							),
		));
		
		
		//$this->user_email->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
 		
  		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Submit',
			'class'    => 'btn btn-lg btn-primary btn-block floatright register_button '
		));
		
		$this->submit->setAttrib("type",'submit');
		
		  
	}
	
 	
	public function contact_us(){
		
		
		$this->setMethod('post');
		
	/*	$this->setAction(self::METHOD_POST);*/
		
	/*	$this->setAttribs(array(
			'id' => 'validate',
			'class' => 'form-vertical',
 			'novalidate'=>'novalidate',
			'enctype'=>'multipart/form-data'
 		));*/
		
  	
		/*  Name  */	
		$this->addElement('text', 'guest_name', array(
				"class"      => "form-control top-element required",
				"required"   => true,
 				"placeholder"   => "Enter Your Full Name",
				"label"   => "Full Name",
				"filters"    => array("StringTrim",'StripTags'),
				'validators' => array( array('NotEmpty', true, array ("messages" => " Full Name cannot be emapty ")))
  		));
	//	$this->guest_name->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');


  		/* Email */
 		$this->addElement('text', 'guest_email', array(
						'class' => 'form-control middle-element required email',
						'required'   => true,
						'placeholder'   => 'Enter Your Email Address',
						'label'   => 'Email Address',
						'filters'    => array('StringTrim','StripTags'),
						'validators' => array( array('NotEmpty', true, array ("messages" => "Email Address cannot be empty")), 'EmailAddress')
        ));
		
	//	$this->guest_email->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		$this->addElement('text', 'guest_phone', array(
						'class' => 'form-control  middle-element required ',
						'required'   => true,
						'placeholder'   => 'Enter Your Phone Number',
						'label'   => 'Phone Number',
						'filters'    => array('StringTrim','StripTags'),
						'validators' => array( array('NotEmpty', true, array ("messages" => "Phone Number cannot be empty")))
        ));
		
	//	$this->guest_phone->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
  
		
		/*  User Address   */	
		$this->addElement('textarea', 'guest_message', array(
				"class"      => "form-control bottom-element required",
 				"rows"=>5, 
				'required'   => true,
  				"placeholder"   => " Enter Message Here",
				"label"   => "Message ",
				"filters"    => array("StringTrim",'StripTags') ,
				'validators' => array( array('NotEmpty', true, array ("messages" => "Message field cannot be empty.")))
  		));

		//$this->guest_message->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Send',
			'class'    => 'btn btn-lg btn-primary btn-block '
		));
		
		$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		
		
  	}
	
	
	
 
	public function profile(){
		
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'm-wrap span6 required',
			"placeholder" => "Admin First Name",
			"label" => "Admin First Name",
			'validators' => array( array('NotEmpty', true, array ("messages" => "Please enter first name")))
		));
		$this->user_first_name->addValidator("NotEmpty", true, array ("messages" => "Please enter first name"));
	
	
		$this->addElement('text', 'user_last_name', array (
			'class' => 'm-wrap span6 required',
			"placeholder" => "Admin Last Name",
			"label" => "Admin Last Name",
			 'validators' => array( array('NotEmpty', true, array ("messages" => "Please enter last name")))
		));
		$this->user_last_name->addValidator("NotEmpty", true, array ("messages" => "Please enter last name"));
	
 
 		##--------------- Admin Email Address -------##
		$this->addElement('text', 'user_email', array(
			'label'      => 'Email',
			'class' => 'm-wrap span6 required email',
			'required'   => true,
			'placeholder'   => 'Email Address',
			"placeholder" => "Email Address",
			'filters'    => array('StringTrim','StripTags'),
			'validators' => array('NotEmpty')
		));
		
		$this->user_email->addValidator('NotEmpty',true,array('messages' =>'Email is required.'))
		->addValidator('EmailAddress',true)->addErrorMessage('Please enter a valid Email-Id');
		
		
		##--------------- Admin  PaypaL Email Address -------##
		$this->addElement('text', 'user_paypal_email', array(
							'label'      => 'Paypal Email',
							'class' => 'm-wrap span6 required email',
							'required'   => true,
							'placeholder'   => 'Paypal Email Address',
							'filters'    => array('StringTrim','StripTags'),
							'validators' => array('NotEmpty')
		));
		$this->user_paypal_email->addValidator('NotEmpty',true,array('messages' =>'Payal Email is required.'))
		->addValidator('EmailAddress',true)->addErrorMessage('Please enter a valid Email-Id');
		
		
		$this->addElement('file', 'user_image', array (
							"placeholder" => " Please Select  Image ",
							"label" => " Please Select  Image ",
							"id" => "user_image",
							"class" => "default",
							));
							
							
  		$this->user_image->setDestination(ROOT_PATH.PROFILE_IMAGES);
		
		
		$this->submitBtn();
		
 		 
	 }
	 
	 
	 public function school_profile($user_id=false)
	 {
			
		
		$this->setAttrib('id','user_profile');
 		$user= isLogged(true);
		 
  		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */
		
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"placeholder" => "School Admin First Name",
			"required"=>true,
			"label" => "School Admin First Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" First Name is Required ")),
 							),
  		));
 	
	
	$this->addElement('text', 'user_current_student', array (
			'class' => 'form-control ',
			"disbaled"=>"disabled",
			"placeholder" => "Current Active Student Count",
			"disabled"=>"disabled",
			"label" => "Current Active Student Count",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
  		));
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Last Name
		 */
		$this->addElement('text', 'user_last_name', array (
			'class' => 'form-control required',
			"placeholder" => "School Admin Last Name",
			"required"=>true,
			"label" => "School Admin Last Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Last Name is Required ")),
 							),
		));
 	
 
 		/*	Form Element  - MaketPlace
		 *	Element Name -  Email Address
		 */
		$this->addElement('text', 'user_email', array(
 			'class' => 'form-control required checkemail_exclude email',
			'required'   => true,
 			'label'      => 'Email',
			"placeholder" => "Email Address",
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
		$this->addElement('text', 'user_phone', array(
 			'class' => 'form-control  ',
			'onchange'=>'formatPhone(this)',
			'label'      => 'Phone Number (Optional)',
			'placeholder'   => 'Phone Number',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		
		));	
 		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Address
		 */
		$this->addElement('text', 'user_address', array(
 			'class' => 'form-control  ',
			'label'      => 'Number and Street',
			'placeholder'   => 'Address',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		$this->addElement('text', 'user_city', array(
 			'class' => 'form-control  ',
			'label'      => 'City, State, Zip Code',
			'placeholder'   => 'City',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		$this->addElement('select', 'user_status', array(
 			'class' => 'form-control  ',
			'label'      => 'User Status',
			'multioptions'=>array(
				'0'=>'Block',
				'1'=>'Active',
			),
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		  $model = new Application_Model_Static();
	 	   $timezone_array=$model->PrepareSelectOptions_withdefault("timezone" ,"timezone_value","timezone_name","1","timezone_value","Select Timezone");
		
 		 $this->addElement('select', 'user_timezone', array(
 			'class' => 'form-control required',
			'required'   => true,
			'Multioptions'=>$timezone_array,
			'label'      => 'Select Timezone',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Timezone is Required ")),
 							),
  		));
		 $this->user_timezone->setRegisterInArrayValidator(false);
   		$this->_submitButton(false,"profile_submit");
		}
		
	 public function subadmin_profile($user_id=false)
	 {
			
		
		$this->setAttrib('id','user_profile');
 
 		$user= isLogged(true);
		 
  		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */
		 $superModel = new Application_Model_SuperModel();
		 $user_data_arr=$superModel->Super_Get("users","user_id='".$user_id."'");
		 $label="";
		 if($user_data_arr['user_type']=='teacher')
		 {
			 $label="Teacher ";
		 }
		 else if($user_data_arr['user_type']=='student')
		 {
			$label="Student ";	 
		 }
		 else if($user_data_arr['user_type']=='schoolsubadmin')
		 {
			$label="School Subadmin ";	 
		 }
		 else if($user_data_arr['user_type']=='family')
		 {
			 
			$label="Family ";	 
		}
		
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"placeholder" => $label." First Name",
			"required"=>true,
			"label" => $label."First Name",
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
			"placeholder" => $label." Last Name",
			"required"=>true,
			"label" => $label." Last Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Last Name is Required ")),
 							),
		));
 	
 
 		/*	Form Element  - MaketPlace
		 *	Element Name -  Email Address
		 */
		$this->addElement('text', 'user_email', array(
 			'class' => 'form-control required checkemail_exclude email',
			'required'   => true,
 			'label'      => 'Email',
			"placeholder" => "Email Address",
			"autocomplete"=>"off",
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
		$this->addElement('text', 'user_phone', array(
 			'class' => 'form-control  ',
			'onchange'=>'formatPhone(this)',
			'label'      => 'Phone Number (Optional)',
			'placeholder'   => 'Phone Number',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		
		));	
 		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Address
		 */
		$this->addElement('text', 'user_address', array(
 			'class' => 'form-control  ',
			'label'      => 'Number and Street',
			'placeholder'   => 'Address',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		$this->addElement('text', 'user_city', array(
 			'class' => 'form-control  ',
			'label'      => 'City, State, Zip Code',
			'placeholder'   => 'City',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		$this->addElement('text', 'user_type', array(
 			'class' => 'form-control  ',
			'readonly'=>'readonly',
			'label'      => 'User Type',
   			
		));
		
		$this->addElement('text', 'school_name', array(
 			'class' => 'form-control  ',
			'readonly'=>'readonly',
			'label'      => 'School',
   			
			
		));
		  $model = new Application_Model_Static();
	 	/*  $timezone_array=$model->PrepareSelectOptions_withdefault("timezone" ,"timezone_name","timezone_name","1","timezone_name","Select Timezone");
 		 $this->addElement('select', 'user_timezone', array(
 			'class' => 'form-control required',
			'required'   => true,
			'Multioptions'=>$timezone_array,
			'label'      => 'Select Timezone',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Timezone is Required ")),
 							),

  		));
		 $this->user_timezone->setRegisterInArrayValidator(false);*/
   		$this->_submitButton(false,"profile_submit");
		}
		

	public function profile_admin($user_id=false){
		
		$this->setAttrib('id','user_profile');
 
 		$user= isLogged(true);
		 
  		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	First Name
		 */

	$label='';
	if($user->user_type=='school')
	{
		$label="Admin ";	
	}
	$mygroup='';
	
	/*if($user->user_type=='family' || $user->user_type=='student')
	{
		
		$mygroup='mygroup';	
	}*/
 		$this->addElement('text', 'user_first_name', array (
			'class' => 'form-control required',
			"placeholder" => $label."First Name",
			"required"=>true,
			"label" => $label."First Name",
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
			"placeholder" => $label."Last Name",
			"required"=>true,
			"label" => $label."Last Name",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Last Name is Required ")),
 							),
		));
 	
 
 		/*	Form Element  - MaketPlace
		 *	Element Name -  Email Address
		 */
		 if($user->user_type=='student' || $user->user_type=='family')
		 {
			 
			 $this->addElement('text', 'user_username', array(
 			'class' => 'form-control   required checkemail_exclude_username',
			'required'   => true,
 			'label'      => 'Username (Change)',
			"placeholder" => "Username",
  			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>"Username is required ")),
							
							),
		     ));
			 
		$this->addElement('text', 'user_email', array(
 			'class' => 'form-control  checkemail_exclude_mail email  required'.$mygroup,
			'required'   => true,
 			'label'      => 'Email Address (Change)',
			"placeholder" => "Email Address",
  			"filters"    => array("StringTrim","StripTags","HtmlEntities","StringToLower"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" Email address is required ")),
								array("EmailAddress" , true,array("messages"=>" Please enter valid email address "))
							
							),
		));
		
		/*$validator = new Zend_Validate_Db_NoRecordExists(array('table' => 'users','field' => 'user_email',
			 'exclude' => array(
				'field' => 'user_id',
				'value' => $user_id
        )

		));
		$validator->setMessage("`%value%`  already exists , please enter any other email address");	
		$this->user_email->addValidator($validator);*/
		 }
		 else
		 {
			 	$this->addElement('text', 'user_email', array(
 			'class' => 'form-control required email checkemail_exclude'.$mygroup,
			'required'   => true,
 			'label'      => 'Email',
			"placeholder" => "Email Address",
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
		$this->addElement('text', 'user_phone', array(
 			'class' => 'form-control  ',
			'onchange'=>'formatPhone(this)',
			'label'      => 'Phone Number (Optional)',
			'placeholder'   => 'Phone Number',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
		
		));	
 		
		if($user->user_type=='school')
		{
			$this->addElement('text', 'user_active_student', array(
 			'class' => 'form-control  ',
			'readonly'=>'readonly',
			'label'      => 'Current Student Count',
			'placeholder'   => 'Current Student Count',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
			));
		
			$this->addElement('select', 'user_message_status', array(
 			'class' => 'form-control  ',
			'label'      => 'Student / Teacher Messaging Function',
			'placeholder'   => 'Student / Teacher Messaging Function',
			'Multioptions'=>array(
				'0'=>"Block",
				'1'=>"Active",
				
			),
			"value"=>1,
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
				
		}
		else if($user->user_type=='student' || $user->user_type=='family')
		{
			/*$this->addElement('text', 'user_username', array(
 			'class' => 'form-control  checkusername '.$mygroup,
		
			'label'      => 'User Name',
			'placeholder'   => 'User Name',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
			));	*/
		}
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Address
		 */
		$this->addElement('text', 'user_address', array(
 			'class' => 'form-control  ',
			'label'      => 'Number and Street',
			'placeholder'   => 'Address',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		$this->addElement('text', 'user_city', array(
 			'class' => 'form-control  ',
			'label'      => 'City, State, Zip Code',
			'placeholder'   => 'City',
   			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			
		));
		
		
		/*	Form Element  - MaketPlace
		 *	Element Name - 	User State
		 */
		  $model = new Application_Model_Static();
		  if($user->user_type=='school')
		{
	
	 	   $timezone_array=$model->PrepareSelectOptions_withdefault("timezone" ,"timezone_value","timezone_name","1","timezone_value","Select Timezone");
		
		  
 		 $this->addElement('select', 'user_timezone', array(
 			'class' => 'form-control required',
			'required'   => true,
			'Multioptions'=>$timezone_array,
			'label'      => 'Select Timezone',
			
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>"Timezone is Required ")),
 							),
  		));
		 $this->user_timezone->setRegisterInArrayValidator(false);
		}
   		$this->_submitButton(false,"profile_submit");
		
 		 
	 }
 	 
	 
	public function image(){
		
 		/*	Form Element  - MaketPlace
		 *	Element Name - 	Profile Image
		 */	
		$this->addElement('file', 'user_image', array (
							"placeholder" => " Profile Image ",
							"label" => " Profile Image ",
							"id" => "user_image",
							"class" => "default required",
							"accept"=>"image/*"
							));
							
		$this->user_image->setDestination(PROFILE_IMAGES_PATH)
			->addValidator('Extension', false,IMAGE_VALID_EXTENTIONS)
			->addValidator('Size', false, IMAGE_VALID_SIZE);
   		$this->user_image->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
		
		
		
	}
	 
 	 
 	
	/* Change Password Form  */
	public function changePassword($isAdmin = false){
		
		/* 
			Admin Old Passwork Form
		*/	
		
		$functionName="match_old_password_front";
		
		if($isAdmin){
			$functionName="match_old_password";
		}
		
 		$this->addElement('password', 'user_old_password', array(
 			"class"      => "form-control  required ",
			"required"   => true,
 			"placeholder"   => "Enter Old Password",
			"label"   => "Enter Old Password",
			"ignore"=>true,
 			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" => array(
								array("NotEmpty",true, array("messages"=>" Old Password is required ")),
								array("StringLength" , true,array('min' => 5, 'max' => 16, 'messages'=>"Password must between 5 to 16 characters ")),
								array("Callback" , true, array($functionName,'messages'=>"Old Password Mismatch,")),
							),
			));
			
			$this->resetPassword($isAdmin);
         
	}

	
	
	/* Reset Password Form  */
 	public function resetPassword($isAdmin = false ){
		
  		$this->addElement('password', 'user_password', array(
 			"class"      => "form-control  required ",
			"required"   => true,
 			"placeholder"   => "Enter Password",
			"label"   => "Enter Password",
			"autocomplete" =>"off",
 			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 5, 'max' => 16, 'messages'=>"Password must between 5 to 16 characters ")),
								array("Identical" , true,array('token' => "user_rpassword", 'messages'=>"Password mismatch, please ender correct same password "))
							),
		));
		
 		$this->addElement('password', 'user_rpassword', array(
 			"class"      => "form-control  required ",
			"required"   => true,
 			"placeholder"   => "Re Type  Password",
			"label"   => "Re Type  Password",
			"ignore"=>true,
			"autocomplete" =>"off",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Password is required ")),
								array("StringLength" , true,array('min' => 5, 'max' => 16, 'messages'=>"Password must between 6 to 16 characters ")),
								array("Identical" , true,array('token' => "user_password", 'messages'=>"Password mismatch, please ender correct same password "))
							),
		));
		
   		$this->addElement('button', 'submit', array(
			'ignore'   => true,
			'type'=>'submit',
			'label'    => 'Reset Password',
			'class'    => 'btn btn-lg btn-primary btn-block floatright register_button btn btn-default'
		));
		
		
		if(!$isAdmin){
 			//$this->user_password->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			//$this->user_rpassword->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			$this->submit->removeDecorator('label')->removeDecorator('HtmlTag')->removeDecorator('Wrapper');
			
		}
		
		
 		
	}
	
	





	
 	 public function submitBtn($class=false){
		 
		 $this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="icon-ok"></i> Save',
				'escape'=>false
		));
		
		
		
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => $class))	));
		
		
	 }
	 
 
	 
 
	 private function _submitButton(){
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="fa fa-check"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
	}
	 
	 
	 
	
	
}