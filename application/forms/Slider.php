<?php
class Application_Form_Slider extends Twitter_Bootstrap_Form_Vertical
{
  	public function init(){
		
		$this->setMethod('post');
		
		$this->setAction(self::METHOD_POST);
		
		$this->setAttribs(array(
			'id' => 'validate',
			"role"=>"form",
			'class' => 'default-form  validate',
			"novalidate"=>"novalidate",
			'enctype'=>'multipart/form-data'
		));
  		
		
 
		
		$this->addElement('text', 'slider_image_title', array (
			'class' => 'form-control required',
			"placeholder" => " Title",
			"label"=>' Title : <span class="required">*</span>',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" This field is required ")),
							),
		));
		
		
		$this->addElement('text', 'slider_image_alt', array (
			'class' => 'form-control required',
			"placeholder" => " Alternate Text",
			"label"=>' Alternate : <span class="required">*</span>',
			"filters"    => array("StringTrim","StripTags","HtmlEntities"),
			"validators" => array(
								array("NotEmpty",true,array("messages"=>" This field is required ")),
							),
		));
		
		
 		
		/* User Video Data */
 		$this->addElement('file', 'slider_image_path', array (
			"placeholder" => " Upload  ",
			"id" => "slider_image_path_image",
			"ignore"=>true,
			"class" => "btn btn-file",
			"label"=>"Upload Photo "
		));
 
 
  		$this->slider_image_path->setDestination(SLIDER_IMAGES_PATH)
			->addValidator('Extension', false,"jpg,JPG,png,PNG,jpeg,JPEG")
			->addValidator('Size', false, "15MB");
		
 		$Usertype=  array("0"=>"Inactive","1"=>"Active");		
 		$this->addElement('select', 'slider_image_status', array(
					  'class'      => 'form-control required',
					  'required'   => true,
					  'label'=>' Status ',
					  'validators' => array('NotEmpty'),
					  'multiOptions' => $Usertype
		));
		
		
		 
  			
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="fa fa-check"></i> Save',
				'escape'=>false
		));
		$this->bttnsubmit->setDecorators(array('ViewHelper',array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' =>'form-actions text-right'))	));
		
		
		 $this->slider_image_title
                    ->addValidator("NotEmpty", true, array ("messages" => "Please enter  name"))
                     ->setAttrib("required", "true");

            $this->slider_image_alt
                    ->addValidator("NotEmpty", true, array ("messages" => "Please enter alternative text"))
                     ->setAttrib("required", "true");

            $this->slider_image_path
                    ->addValidator("NotEmpty", true, array ("messages" => "Please select image"));
                     
			
			 $this->slider_image_status
                    ->addValidator("NotEmpty", true, array ("messages" => "Please select image"))
                     ->setAttrib("required", "true");
 

 		
		
		
	}
	
	 
  	
 
	
	public function submit_btn(){
		
		
		$this->addElement('button', 'bttnsubmit', array (
				'class' => 'btn blue ',
				'ignore'=>true,
				'type'=>'submit',
 				'label'=>'<i class="icon-ok"></i> Save  ',
				'escape'=>false
		));
		
 		$this->bttnsubmit->setDecorators(
		array('ViewHelper',
			array(array('controls' => 'HtmlTag'), array('tag' => 'div', 'class' => 'form-actions text-right'))
			)
		);
		
		
	}
	
	
	
 
}