<?php
class StaticController extends Zend_Controller_Action
{

	private $modelStatic = "" , $modelTeam ="" ;
	
    public function init(){
		
		$this->modelStatic = new Application_Model_Static();
	}
	
	
	/* Static Pages  */
	public function indexAction(){
		
		global $objSession;
 
 		$page_id =  $this->getRequest()->getParam('page_id');
 		$this->view->page_id = $page_id;
		if($page_id==1)
		{
			$this->view->page_slug="about_us";	
		}
		else if($page_id==8)
		{
			$this->view->page_slug="terms";		
		}
		else
		{
			$this->view->page_slug="privacy_policy";			
		}
 		$content = $this->modelStatic->getPage($page_id); 
		
		$content_blocks = $this->modelStatic->getContentBlock();
		

		 
		$block_keys = array();
		$block_values = array();
		
		$first_flag = true;
		
		
		foreach($content_blocks as $key=>$values){
			
  				preg_match('/(('.$values['content_block_title'].'))/', $content['page_content'], $matches);
 				if($matches){
					 /* match is True */
					if(empty($values['content_block_image'])){
 						$content['page_content'] = str_replace('(('.$values['content_block_title'].'))'  , $values['content_block_content']  ,$content['page_content']);	 
  					}else{
 						$url = APPLICATION_URL.'/resources/content_block_images/'.$values['content_block_image'];
						$mobile_div = '<div class="content_block_bg_mobile"><img src="'.$url .'"  ></div>';
						$desktop_div = '<div class="content_block_image_bg" style="background-image:url('.$url.');"></div>';
  						$begin = '</div></div></div><div class="relative" >'.$desktop_div.''.$mobile_div.'<div class="container container-layout" style="min-height:0;"  >
							<div class="static-pages">'.$values['content_block_content'].'
							</div></div></div><div class="relative" ><div class="container container-layout" style="min-height:0;"  ><div class="static-pages">';
 						$content['page_content'] = str_replace('(('.$values['content_block_title'].'))'  , $begin  ,$content['page_content']);	 	 
 						
					}
					 
					 
 				}

  			
		}
		
		$this->view->pageHeading=$content['page_title'];
		
		$this->view->content=$content;
		
 	}	
	public function contactAction(){
		
 		global $objSession;
 		
		$page_id = 9;
 	 
		$this->view->page_id = $page_id;
		
		$this->view->pageHeading= " Contact Us ";
		
		$this->view->layout_show_map = true ;
		
		$content = $this->modelStatic->getPage($page_id); 
		$this->view->page_slug="contact_us";
		
		$this->view->content=$content;
		
		$form=new Application_Form_User();
		$form->contact_us();
 		
		if($this->getRequest()->isPost()){
			$data =$this->getRequest()->getPost();
			if($form->isValid($data)){
				
 				$modelEmail = new Application_Model_Email();
  				$is_send =  $modelEmail->sendEmail("contact_us",$form->getValues());
 				$objSession->successMsg = "Mail Successfully Send "; 
 				$this->_helper->getHelper("Redirector")->gotoRoute(array(),"contact_us");
			}
		}
		
		
		$this->view->form=$form;		
 		
		
 	}
	
 	
	public function subscribeAction(){
		
 		global $objSession;
 		
		$page_id=7;
 	 
		$this->view->page_id = $page_id;
		
		$content = $this->modelStatic->getPageContent($page_id); 
		
		$this->view->content=$content;
		
		
		$form=new Application_Form_Subscribe();
		$this->view->form=$form;
		
		if($this->getRequest()->isPost()){
			$data =$this->getRequest()->getPost();
			if($form->isValid($data)){
				
				if($this->modelTeam->addSubscription($form->getValues())){
					$objSession->successMsg = " Your Subscription is Successfully Done .. Thank You  ";
 					$this->subscription_request_mail($form->getValues());	
					$this->_redirect('content/subscribe');
				}else{
					
					unset($objSession->errorMsg);
					$objSession->successMsg = "You are already subscribed for new listing alerts";
					$this->_redirect('content/subscribe');
				}
 				//
			}
		}
				
 		
		$this->view->heading=$content['content_title'];
		
	}
	
	
	private function subscription_request_mail($data_form){
		
		global $objSession; 
		
		$site_config= Zend_Registry:: get("site_config"); 

		$admin_email=Zend_Registry::get('admin_email');
		$admin_name=Zend_Registry::get('admin_name');
		$site_title = $site_config['site_title'];
		
		$modelTemplate = new Application_Model_Email();
  		$template =$modelTemplate->getEmailTemplateByKey("subscription_request");

		$sender_email = $data_form['guest_email'];
		$sender_name = $data_form['guest_name'];
 		
		$subject = $site_config['site_title']." - ".$template['emailtemp_subject']; 
 		 
		$mail_content = str_ireplace(
								array( "{SITE_TITLE}" ,"{site_admin}","{sender_name}","{sender_email}" ), 
								array(	$site_title , $admin_name,$sender_name,$sender_email),
								$template['emailtemp_content']
							);
		
		
		
		$mail = new Zend_Mail();
    	$mail->setBodyHtml($mail_content)
        ->addTo($sender_email , $sender_name)
        ->setFrom($admin_email, $site_title)
        ->setSubject($subject);
	 
		
		if(!TEST){
			 $mail->send();
		}
		
		
		
		/* Send Email to Admin */
		$modelTemplate = new Application_Model_Email();
  		$template =$modelTemplate->getEmailTemplateByKey("subscription_request_admin");

		$sender_email = $data_form['guest_email'];
		$sender_name = $data_form['guest_name'];
 		
		$subject = $site_config['site_title']." - ".$template['emailtemp_subject']; 
 		 
		$mail_content = str_ireplace(
			array( "{SITE_TITLE}" ,"{site_admin}","{sender_name}","{sender_email}" ), 
			array(	$site_title , $admin_name,$sender_name,$sender_email),
			$template['emailtemp_content']
		);
		
							
 		$mail = new Zend_Mail();
    	$mail->setBodyHtml($mail_content)
        ->setFrom($sender_email , $sender_name)
        ->addTo($admin_email, $site_title)
        ->setSubject($subject);
				
 		//$objSession->successMsg = " Your Contact Request Successfully Sent.  Thank You ! ";
 		if(!TEST and $mail->send()){ return true;} else {return false;}
		 
 		
	}
	
 	
 }

