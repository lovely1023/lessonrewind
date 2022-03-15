<?php
class Admin_NavigationController extends Zend_Controller_Action
{
 	private $admin = "" , $modelNavigation;
	
    public function init(){ 
  		$this->modelNavigation  = new Application_Model_Navigation();
 	}
 	
	
	/* Show Pages  */
	public function indexAction(){
		global $mySession;
		$this->view->pageHeading = "All Site Navigation Menu";
		$this->view->pageDescription = "manage site navigation menus";
		$this->view->type = "Both";
    }
	
	/* Show Pages  */
	public function headerAction(){
		global $mySession;
		$this->view->pageHeading = "Header Navigation Menu";
		$this->view->pageDescription = "manage header navigation menus";
		$this->view->type = "Header";
		$this->render("index");
    }
	
	
	/* Show Pages  */
	public function footerAction(){
		global $mySession;
		$this->view->pageHeading = "Footer Navigation Menu";
		$this->view->pageDescription = "manage footer  site navigation menus";
		$this->view->type = "Footer";
		$this->render("index");
    }
	
	
 	
  	/* Create a New Page   */
	public function addAction(){
		
		global $mySession; 
		
		
 		$this->view->pageHeading = "Create Menu";
		$this->view->pageDescription = "add new menu to database";
		
		$this->view->showEditorUpload = "menu";
		
		
		
		
  		 
 		$form = new Application_Form_StaticForm();
		$form->navigation();
		
		 
		
  		
 		if($this->getRequest()->isPost()) {
 			
			$data_form = $this->getRequest()->getPost();
 			
			$new_status = $data_form['menu_status'] ;
 			/* Check Is values are changed or not */
			$approval_required = false; 
 					
			/* Set Validation Paramerter Here for Form  */		
			if($new_status=='1'){
				 $form->request_admin->setRequired(true);
				 $approval_required = true;
 			}else{
 				$form->request_admin->setIgnore(true);
			}
			
  			
			if($form->isValid($data_form)){
  				
				$data_to_insert = $form->getValues() ;
				$data_to_insert['menu_author']=$this->admin->user_id;
 				
				if(!$approval_required){
					 
					$is_insert = $this->modelNavigation->add($data_to_insert);
					
					if($is_insert->success){
 						$mySession->successMsg  = " Menu Successfully Created ";
						$this->_redirect('admin/navigation');
					}
  					$mySession->errorMsg  = $is_insert->message;
					
				}else{
					/* Approval is requird for Content Block */

					$modelRequest = new Application_Model_Request();

					$data = $form->getValues();
					
					unset($data['request_admin']);
					
					$data['menu_author'] = $this->admin->user_id;
					$data['menu_status'] = "2";
					
 					$is_insert = $this->modelNavigation->add($data);
					
					if($is_insert->success){

						$data = $form->getValues();
 
 						$data['menu_to_update'] = $is_insert->inserted_id; /* Which Block Needs to Approve*/ ;
						
						$is_added = $modelRequest->addRequest($data , NAVIGATION_MENU , $this->view->admin->user_id /* Author */ );
					 
						 if($is_added->success){
							 $mySession->successMsg = " Request is Successfully Sent to Request Admin. ";
							 $this->_redirect('admin/navigation');
						 }else{
							  $mySession->errorMsg = $is_added->message;
						 }
 					}
					$mySession->errorMsg = $is_insert->message;
   				}
				
  			 
 			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
 		 
 		 $this->view->form =$form;
		 
		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	
 	}
	
	
 	
	/* Edit Page Information */
 	public function editAction(){
		
 		
		global $mySession; 
 		
		$this->view->pageHeading = "Edit Navigation Menu";
		$this->view->pageDescription = "update navigation menu";
		
		
		 
		
		
		$menu_id = $this->_getParam('menu_id') ;
		$content =  $this->modelNavigation->getMenu($menu_id);
		
  		if(!$content){
			$mySession->errorMsg = "No Such Menu Found ";
			$this->_redirect("admin/navigation");
		}
		
  		 
 		$form = new Application_Form_StaticForm();
		$form->navigation();
		
		$form->populate($content);
		
 		if($this->getRequest()->isPost()) { 
		
			$posted_data = $this->getRequest()->getPost();
			
 			/* Check Is values are changed or not */
			$not_to_match = array("menu_id","menu_author","menu_updated","menu_publish");
			$block_edit = false ;
 			foreach($content as $key=>$value){
 				if(!in_array($key,$not_to_match) and $posted_data[$key]!=$value){
					$block_edit=true;
					break;
				}
			}
			
			if(!$block_edit){
				$mySession->successMsg = " Menu Block Values are Same as Pervious ";
				$this->_redirect("admin/navigation");
			}
			
 		 
			
 			$old_status =  $content['menu_status'];
			$new_status =  trim($posted_data['menu_status']);


			$approval_required = false; 
 					
			/* Set Validation Paramerter Here for Form  */		
			if($old_status!=$new_status or $new_status=='1'){
				 $form->request_admin->setRequired(true);
				 $approval_required = true;
 			}else{
 				$form->request_admin->setIgnore(true);
			}


 			if($form->isValid($posted_data)){
				
				if(!$approval_required){
 					
					$data_values = $form->getValues();
 					 
					$is_update = $this->modelNavigation->add($form->getValues() ,$menu_id);
					
					if($is_update->success){
 						$mySession->successMsg  = " Page Content Successfully Updated ";
						$this->_redirect('admin/navigation');
					}
 
 					$mySession->errorMsg  = $is_update->message;
 					
 				}else{
					/* Code for somethink which required the approval of another admin */

					$modelRequest = new Application_Model_Request();
 				
					$data = $form->getValues();
					
 					$data['menu_to_update'] = $content['menu_id']; /* Which Block Needs to Update*/ ;
					
					if($content['menu_publish']){
						$data['content_old_values']=$content;
					}
  					$is_added = $modelRequest->addRequest($data , NAVIGATION_MENU , $this->view->admin->user_id /* Author */ );
					 
					 if($is_added->success){
						 $mySession->successMsg = " Request is Successfully Sent to Request Admin. ";
						 $this->_redirect('admin/navigation');
					 }else{
						  $mySession->errorMsg = $is_added->message;
					 }
 				}
   			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
  		 
		 $this->view->page_content = $content;
 		 $this->view->form =$form;
		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
	}
	
	
	
	public function viewAction(){
 		 
		global $mySession; 
		 
 		$this->view->pageHeading = " Menu Information  ";
		$this->view->pageDescription = "view Menu Information or delete menu item  ";
		
 		
		$menu_id = $this->_getParam('menu_id') ;
		$content =  $this->modelNavigation->getMenu($menu_id , true);
		
 	 
 		
		if(!$content){
			$mySession->errorMsg = "No Such Menu Found ";
			$this->_redirect("admin/navigation");
		}
		
		
				
  		$form = new Application_Form_StaticForm();
		$form->content_block_delete();
		 
 		if($this->getRequest()->isPost()) { 
		
			$posted_data = $this->getRequest()->getPost();
 
 			$approval_required = false; 
 			
			if($content['menu_publish']){
				/* Direct Delete */
				$form->request_admin->setRequired(true);
				$approval_required = true;
			}
 			
			if($form->isValid($posted_data)){
				
 				if(!$approval_required){
  					$this->modelNavigation->getAdapter()->delete("menu","menu_id=".$page_id);
					$mySession->successMsg = " Page Successfully Deleted";
					$this->_redirect("admin/navigation");
 					
				}else{
					/* Approval is needed */
					$modelRequest = new Application_Model_Request();
					
 					$data = $form->getValues();
 					$content['request_admin'] = $data['request_admin']; 
		 			$content['menu_to_delete'] = $content['menu_id']; /* Which Block Needs to Update*/ ;
					
  					$is_added = $modelRequest->addRequest($content , DELETE_MENU , $this->view->admin->user_id /* Author */ );
					 
					 if($is_added->success){
						 $mySession->successMsg = " Request is Successfully Sent to Request Admin. ";
						 $this->_redirect('admin/navigation');
					 }else{
						  $mySession->errorMsg = $is_added->message;
					 }
  				}
				
				$mySession->errorMsg  = $is_update->message;
 				
			}else{
				
				$mySession->errorMsg = "Please assign Request Admin";
			}
			
	    			 
		 }
  		 
		 $this->view->page_content = $content;
 		 $this->view->form =$form;

 		
	}
	
 	
	public function getnavigationAction(){
		
  		$this->dbObj = Zend_Registry::get('db');
		
		$type = $this->_getParam("type");
  
		$aColumns = array("menu_id",
						"menu_parent_id",
						"menu_permalink",
						"menu_title",
						"menu_meta_keywords",
						"menu_meta_description",
						"menu_index",
						"menu_show",
						"menu_follow",
						"menu_google_code",
						"menu_status",
						"menu_publish",
						"menu_updated",
						"menu_author",
						"user_id","user_salutation","user_first_name","user_last_name","user_image"
						);

		$sIndexColumn = 'menu_id';
		$sTable = 'navigation_menu';
  		
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
		
		$condition = " 1 " ;
		
		if($type!="Both"){
			$condition = "  menu_show='$type' or menu_show='Both' "; 
		}
 		
		if(empty($sWhere)){
			$sWhere = " where  $condition   ";
		}else{
			$sWhere.= " and $condition  ";
		}
  		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM   $sTable 
			inner join pages on page_id = menu_page_id
			inner join users on menu_author = user_id
		$sWhere $sOrder $sLimit";
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
 			$row[] = $row1[ $aColumns[0] ];
			
 			$row[]= '<img src="'.getUserImage($row1['user_image'],60).'" />';
			
			$row[]= getFullName($row1['user_salutation'],$row1['user_first_name'],$row1['user_last_name']);//;
			
 			$row[]=ucwords($row1['menu_title']);
			
			$row[]=ucwords($row1['menu_show']);
			
			$row[]=date('d-F , Y',strtotime($row1['menu_updated']));
			
  			if($row1['menu_status']=="1"){
				$row[]='<span class="label label-success"> Published </span>';	
			}else{
				$row[]='<span class="label label-warning"> Draft </span>';	
			}
   			$row[]='<a class="btn mini green-stripe" href="'.APPLICATION_URL.'/admin/navigation/edit/menu_id/'.$row1[$sIndexColumn].'">Edit</a>';
			$row[]='<a class="btn mini green-stripe" href="'.APPLICATION_URL.'/admin/navigation/view/menu_id/'.$row1[$sIndexColumn].'">View</a>';
 			$output['aaData'][] = $row;
			$j++;
		}	
		
		echo json_encode( $output );
		exit();
 	} 
	
	
	 
 
 
}

