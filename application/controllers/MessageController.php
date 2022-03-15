<?php
class MessageController extends Zend_Controller_Action
{
  	private $modelUser ,$modelContent; 
	 
	public function init(){
		global $objSession;
		$this->view->show = "messages" ; 
 		$this->modelUser = new Application_Model_User();
		$this->modelMessage = new Application_Model_Message();
		$this->modelEmail = new Application_Model_Email();
		$this->modelSuper = new Application_Model_SuperModel();
 	}
	
	public function indexAction(){
 		global $objSession; 
		 $this->view->pageHeading= "INBOX";
		// $this->_helper->layout()->setLayout('profilelayout');	
	 }
	
		
	public function composeAction(){
 		global $objSession; 
		//$this->_helper->layout()->setLayout('profilelayout');	
		 $this->view->pageHeading= "COMPOSE MESSAGE";
	 	$replyId = $this->_getParam('remsg');
		$teacher_id=$this->getRequest()->getParam('teacher_id');
		$lesson_id=$this->getRequest()->getParam('lesson_id');
		$student_id=$this->getRequest()->getParam('student_id');
		$this->view->student_id=$student_id;
		$this->view->lesson_id=$lesson_id;
		$message_data_sender=array();
		$lesson_data=array();
		$sender_id='';
		if(isset($replyId))
		{ 
			$message_data_sender=$this->modelSuper->Super_Get('messages',"message_id='".$replyId."'","fetch");
			$sender_id=$message_data_sender['message_sender_id'];
			if($this->view->user->user_id==$message_data_sender['message_sender_id'])
			{
				$sender_id=$message_data_sender['message_reciver_id'];
			}
	    }
		if(isset($teacher_id))
		{
			$sender_id=$teacher_id;	
		}
		if(isset($lesson_id))
		{
			$lesson_data=$this->modelSuper->Super_Get("lesson","lesson_id='".$lesson_id."'","fetch");	
		}
		$receiver_data=array();
		if(isset($sender_id))
		{
			$receiver_data=$this->modelSuper->Super_Get("users","user_id='".$sender_id."'","fetch");	
		}
		$this->view->receiver_data=$receiver_data;
		$this->view->show = "messages" ; 
		$this->view->replyId = $replyId;
		 if(empty($replyId)){
			 	//$this->_redirect('inbox');
			 }
		 
		 
		 $form = new Application_Form_Message();
		 if(isset($lesson_id))
		 {
				$form->compose($this->view->user,$lesson_id);		 
		 }
		 else
		 {
				$form->compose($this->view->user); 
		  }
		 
		 if($sender_id) {
			
			$replyUser = $this->modelSuper->Super_Get('users','user_id="'.$sender_id.'"','fetch');
			
			$this->view->replyUser = $replyUser;
			if(isset($lesson_data) && !empty($lesson_data))
			{
				$form->populate(array("send_to_field"=>$sender_id,'message_subject'=>$lesson_data['lesson_title'])); 
					
			}	
			else
			{
				$form->populate(array("send_to_field"=>$sender_id)); 	
			}
			
			
			 
		}
			if(isset($replyId))
			{
				$prent_messag=$this->modelSuper->Super_Get("messages","message_id='".$replyId."'","fetch");
				
				$form->populate(array('message_subject'=>$prent_messag['message_subject'])); 
			}
		//$form->compose($this->view->user);
		$this->view->form  = $form;
		if($this->getRequest()->getPost()) {
			
			if($form->isValid($this->getRequest()->getPost())) {
			
				$data = $_POST;
		
					// upload attachents if they exists
								
				foreach($data['send_to_field'] as $key=>$value) {
					 if(!isset($lesson_id))
					 {
						$lesson_id=0;	 
					 }

					 // ================ add  ==========
					 date_default_timezone_set('America/Los_Angeles');	// PDT time
					 // ================================
					 
					$msg_data = array('message_sender_id'=>$this->view->user->user_id,'message_reciver_id'=>$value,'message_subject'=>$data['message_subject'],
					'message_content'=>$data['message_content'],'message_read_stauts'=>"0",'message_status'=>"1",
					// 'message_date'=>gmdate('Y-m-d H:i:s'),
					'message_date'=>date('Y-m-d H:i:s'),
					'message_lesson_id'=>$lesson_id);
					$object=$this->modelSuper->Super_Insert('messages',$msg_data);
					
					if($replyId)
					{
							$msg_update=array('message_parent_id'=>$replyId);
					}
					else
					{
							$msg_update=array('message_parent_id'=>$object->inserted_id);
					}
					
					
					$kk=$this->modelSuper->Super_Insert("messages",$msg_update,'message_id="'.$object->inserted_id.'"');
					$user_data=$this->modelSuper->Super_Get('users','user_id="'.$value.'"','fetch',array('fields'=>array('user_email','user_first_name')));
					
					$message_alert_data=array(
						'rece_email'=>$user_data['user_email'],
						'rece_first_name'=>$user_data['user_first_name'].' '.$user_data['user_last_name'],
						'msg'=>'You have received a new message from  <b>'.$this->view->user->user_first_name.'</b>. <br> For more Details please visit the site. ',
					);
					
					$this->modelEmail->sendEmail('message_alert',$message_alert_data);
					
				} 
					$objSession->successMsg = "Message Successfully Sent";
					$this->_redirect('message/outbox');
				
			}
			
		
		}
		
		
		
	 	
	 
	}
	
	
	public function getmessagesAction(){
		 
  		$this->dbObj = Zend_Registry::get('db');
  	
	/*	$aColumns = array('message_id','message_sender_id','message_reciver_id','message_subject','message_content','message_date','message_read_stauts','user_first_name','user_last_name',new Zend_Db_Expr("count(ma_message_id) as attachment_count"));*/
	
	/*** Dont pass like this in the above array Zend_Db_Expr("count(ma_message_id) as attachment_count")  ***/
	
		$aColumns = array('message_id','message_sender_id','message_reciver_id','message_subject','message_content','message_date','message_read_stauts','user_first_name','user_last_name','message_parent_id','message_read_stauts');
		$sIndexColumn = 'message_id';
		$sTable = 'messages';
		/*Table Setting*/
		
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
		$sOrder = "ORDER BY message_date DESC";
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
		
		
		
		/* End Table Setting */
		 
		 
		 
		if($sWhere)
		{
			$sWhere.= ' and ( message_reciver_id="'.$this->view->user->user_id.'" and message_inbox_delete_status="0") ';
		}
		else
		{
			$sWhere.= ' Where( message_reciver_id="'.$this->view->user->user_id.'" and message_inbox_delete_status="0")';
		}
		 
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."  FROM   $sTable left join users on users.user_id=$sTable.message_sender_id
		 $sWhere  group by message_parent_id  $sOrder  $sLimit";
		
		 
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 		 
		// echo $sQuery; die;
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable ";
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
		foreach($qry as $row1)
		{
			$row=array();
			 
			/* Page Author Image */
 			$message_count=$this->modelSuper->Super_Get("messages","message_parent_id='".$row1['message_parent_id']."'","fetch",array("fields"=>array("COUNT(message_id) as msgcount")));
			$start_message=$this->modelSuper->Super_Get("messages","message_id='".$row1['message_parent_id']."'","fetch");
			$end_message=$this->modelSuper->Super_Get("messages","message_parent_id='".$row1['message_parent_id']."'","fetch",array("order"=>array("message_id DESC")));
			
		/*	prd($message_count);*/
		
			$unread_box="";
			if($row1['message_read_stauts']==0){
						
						//$unread_box = '<div class="unread_box">Unread</div>';
				}
		
			if($row1['message_read_stauts']==0)
			{
				$row[]='<input class="unread"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';	
			}
			else
			{
				$row[]='<input class=""  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			}
			
  			$row[]= '<a href="'.SITE_HTTP_URL.'/message/readmessage/inbox/1/msg/'.$row1['message_parent_id'].'"><div style="position:"relative;>'.$unread_box.'<div class="inbox_tittle">'.ucfirst($row1['user_first_name'].'&nbsp;'.$row1['user_last_name']).' </div><div class="inbox_subject">'.ucfirst(substr($row1['message_subject'],0,50)).'...</div></div></a>';
			
			
			$row[]= date('M d, Y',strtotime($row1['message_date']));
			
			$row[]=' ('.$message_count['msgcount'].') ';
			$row[]=date('M d Y H:i A',strtotime($start_message['message_date']));
			$row[]=date('M d Y H:i A',strtotime($end_message['message_date']));
  			$output['aaData'][] = $row;
			$j++;
		}	
		
		echo json_encode( $output );
		exit();
 	} 
	
	
	
	public function readmessageAction() {
		
		global $objSession; 
		 $this->view->pageHeading= "READ MESSAGE";
		//$this->_helper->layout()->setLayout('profilelayout');	
		$msgID = $this->_getParam('msg');
		$inbox = $this->_getParam('inbox');
		$this->view->inbox=$inbox;
		$msgoutbox = $this->_getParam('msgoutbox');
		
		if($msgoutbox) {
			
			$this->view->getlist = "getlist"; 
			$messageDetail  = $this->modelMessage->getmessages($msgoutbox,'2');
		}
		if($msgID) {
			
			$messageDetail  = $this->modelMessage->getallmessages($msgID,"1");
	
			$this->modelSuper->Super_Insert('messages',array('message_read_stauts'=>"1"),'message_parent_id="'.$msgID.'"',array());
			
			
		}
			
		$this->view->messageDetail = $messageDetail;
	}
	
	public function outboxAction() {
		global $objSession; 
		//$this->_helper->layout()->setLayout('profilelayout');	
		$this->view->pageHeading= "OUTBOX";
	}
	
	public function getoutboxmsgsAction(){
		 
  		$this->dbObj = Zend_Registry::get('db');
  	
		$aColumns = array('message_id','message_sender_id','message_reciver_id','message_subject','message_content','message_date','user_first_name','user_last_name',
		);

		$sIndexColumn = 'message_id';
		$sTable = 'messages';
  		
		
		/*Table Setting*/{
		
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
		$sOrder = "ORDER BY message_id DESC";
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
		
		
		
		}/* End Table Setting */
		 
		if(isset($sWhere) and $sWhere!=""){
			$sWhere .= ' and message_sender_id="'.$this->view->user->user_id.'" and message_outbox_delete_status="0"';
		}
		else{
		 $sWhere .= 'Where message_sender_id="'.$this->view->user->user_id.'" and message_outbox_delete_status="0"';
		}
 		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."  FROM   $sTable left join users on users.user_id=$sTable.message_reciver_id
		$sWhere group by message_id $sOrder $sLimit";
		 //echo $sQuery; die;
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
		 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable ";
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
		foreach($qry as $row1)
		{
			$row=array();
			 
			/* Page Author Image */
 			 
			 
			$SenderData = $this->modelSuper->Super_Get('users','user_id="'.$row1['message_reciver_id'].'"','fetch');
			$row[]='<input   type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			
  			$row[]= '<a href="'.SITE_HTTP_URL.'/message/readmessage/msgoutbox/'.$row1['message_id'].'"><div class="inbox_tittle">'.ucfirst($row1['user_first_name'].'&nbsp;'.$row1['user_last_name']).'  </div><div class="inbox_subject">'.ucfirst(substr($row1['message_subject'],0,50)).'...</div>
		 
			</a>';
			$row[]= date('M d,Y',strtotime($row1['message_date']));
  			$output['aaData'][] = $row;
			$j++;
		}	
		
		echo json_encode( $output );
		exit();
 	} 
	
	
	public function removeinboxmsgAction() {
			
		global $objSession; 
 		$case_id = $this->_getParam('case_id');
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			 	
			if(isset($formData['messages']) and count($formData['messages'])){
				
				foreach($formData['messages'] as $value) {	
				
					$this->modelSuper->Super_Insert("messages",array('message_inbox_delete_status'=>"1"),'message_parent_id='.$value.'');
				}
				$objSession->successMsg = "Message Deleted Successfully";
				
 			}else{
				$objSession->errorMsg = " Invalid Request to Delete Message(s) ";
			}
			
 			$this->_redirect('message');	 
   	 
		} 
	}	
	
	public function removeoutboxAction() {
			
		global $objSession; 
 		$case_id = $this->_getParam('case_id');
		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			 
			if(isset($formData['messages']) and count($formData['messages'])){
				
				foreach($formData['messages'] as $value) {	
				
					$this->modelSuper->Super_Insert("messages",array('message_outbox_delete_status'=>"1"),'message_id='.$value.'');
				}
				$objSession->successMsg = "Message Deleted Successfully";
				
 			}else{
				
				$objSession->errorMsg = "Invalid Request to Delete Message(s) ";
			}
			
 			$this->_redirect('message/outbox');	 
   	 
		} 
	
	}	

}