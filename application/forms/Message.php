<?php
class Application_Form_Message extends Twitter_Bootstrap_Form_Vertical
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
	
 	
	 
	public function compose($userData=false,$lesson_id=false){
		
		$userLogged = isLogged(true);
		
		 
		 $this->setAttribs(array(
 			'class' => 'profile_form form-horizontal col-lg-12 col-md-12 col-sm-12',
 			'novalidate'=>'novalidate',
			"role"=>"form",
			"id"=>"compose_form",
			'enctype'=>'multipart/form-data',
		));		 
		$model = new Application_Model_SuperModel();
		 
		 $OptionsArray = array();
		$modelSchool = new Application_Model_SchoolModel();
		if($userLogged->user_type=='school')
		{
					$Query = $model->Super_Get('users','user_status="1" and (user_type="student") and user_school_id="'.$userLogged->user_id.'"','fetchAll'); 	
					
		}
		else if($userLogged->user_type=='teacher')
		{
				   // $Query = $model->Super_Get('users','user_status="1" and (user_type="student" or user_type="school") and user_school_id="'.$userLogged->user_school_id.'"','fetchAll'); 		
				   $Query=$modelSchool->getstudentallteacher($userLogged->user_id);
				   $user_school_data=$model->Super_Get('users','user_status="1" and user_id="'.$userLogged->user_school_id.'"',"fetch");
        			$Query[count($Query)]=$user_school_data;
		}
		else if($userLogged->user_type=='student')
		{
			$Query=$modelSchool->getstudentallteacher($userLogged->user_id);
			$user_school_data=$model->Super_Get('users','user_status="1" and user_id="'.$userLogged->user_school_id.'"',"fetch");
			$Query[count($Query)]=$user_school_data;
			
		
				/* $Query = $model->Super_Get('users','user_status="1" and (user_type="teacher" or user_type="school") and (user_school_id="'.$userLogged->user_school_id.'" or user_id="'.$userLogged->user_id.'")','fetchAll'); 		*/	
		}
			/*	prd($Query);*/
				/*$OptionsArray[0]['key']="";
				$OptionsArray[0]['value']="Select User";*/
		 if($Query) {
				$k=1;
				foreach($Query as$key=>$value) {
					$OptionsArray[$k+1]['key'] = $value['user_id'];
					if($value['user_type']=='school')
					{
						$OptionsArray[$k+1]['value'] = "School Admin";	
					}
					else
					{
						$OptionsArray[$k+1]['value'] = $value['user_first_name'].' '.$value['user_last_name'];
					}
						
						$k++;	
					} 
		  }
			/*prd($OptionsArray);*/
		 		 
		 $this->addElement('multiselect', 'send_to_field', array (
			'class' => 'form-control required  chzn-select ',
			"placeholder" => "TO",
			"required"=>true,
			//"id"=>"e2_2",
			"multiOptions"=>$OptionsArray,
			
			"label" => "To",
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Field is Required ")),
 							),
  		));
 	$this->send_to_field->setRegisterInArrayValidator(false);
	
		/*	Form Element  - MaketPlace
		 *	Element Name - 	Last Name
		 */
		$this->addElement('text', 'message_subject', array (
			'class' => 'form-control required',
			"placeholder" => "Enter Subject",
			"required"=>true,
			"label" => "Subject",
		 	"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Subject is Required ")),
 								),
		));
 	
	
		$this->addElement('textarea', 'message_content', array (
			"required" => TRUE,
			'class' => 'form-control required ckeditor',
			'id' => 'message_content',
			'rows'=>'15',
			"placeholder"=>"Enter Message",
			"label" => "Message",
			 "validators" =>  array(
								array("NotEmpty",true,array("messages"=>" Message Content is required")),
 							),
		));
 				
		 
  		 
		if(isset($lesson_id))
		{
			 $this->addElement('button', 'bttncancel', array (
				'class' => 'btn blue btn-primary hvr-shadow-radial btnfullwidth btn btn-default',
				'ignore'=>true,
				'type'=>'button',
 				'label'=>' Cancel',
				'onclick'=>"cancelmsg()",
				'escape'=>false
			));
			$this->bttncancel->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-center pull-right '))	));
		}
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue btn-primary hvr-shadow-radial btnfullwidth btn btn-default',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>' SEND',
				'escape'=>false
				
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-center pull-right marginright_send'))	));
		 
 	}
	 private function submitButton(){
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn btn-lg btn-primary ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="fa fa-check"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-left'))	));
		
	}
	
}