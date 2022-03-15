<?php
class Admin_SubadminController extends Zend_Controller_Action
{
	
	

    public function init(){
		$this->modelStatic = new Application_Model_SuperModel();
		$this->modelUser = new Application_Model_User();
 		
	}
	
		/* Subadmin  */
	public function indexAction(){
		global $mySession;
		//prd('bnmm');
		$this->view->pageHeading = "Site Subadmins";
		$this->view->pageDescription = "manage sub admin";
		$this->view->pageIcon = "fa fa-group";
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Subadmin' =>'/subadmin');
    }
	
	public function verifiedsubadminAction(){
		global $mySession;
		$this->view->pageHeading = "Manage Verified  Sub Admin";
		$this->view->pageIcon = "fa fa-group";
		$this->view->request_type = "verified";
		$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Verified Subadmin' =>'/subadmin/verifiedsubadmin');
    }
	public function blockedsubadminAction(){
		global $mySession;
		$this->view->pageHeading = "Manage Blocked  Sub Admin";
		$this->view->pageDescription = "manage sub admin";
		$this->view->pageIcon = "fa fa-group";
		$this->view->request_type = "blocked";
		$this->render("index");
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Blocked Subadmin' =>'/subadmin/blockedsubadmin');
    }
	public function addsubadminAction(){
		
		global $mySession; 
  		$form = new Application_Form_StaticForm();
		$user_id =$this->_getParam('user_id') ;
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Add Subadmin' =>'/subadmin/addsubadmin');
		//prd($user_id);
		if($user_id!=''){
			$form->subadmin($user_id);
			$Type="Edit";$page_type="edit";$msgType="Updated";
			$option_info = $this->modelStatic->Super_Get("users","user_id='".$user_id."'","fetchAll");
			if(!$option_info)
			{
				$mySession->infoMsg = "No Such Sub Admin Exists in the database.!";
				$this->_redirect('/admin/subadmin');
			}
			$Roles = $this->modelStatic->Super_Get("subadmin_roles","sr_user_id='".$user_id."'","fetchAll");
		
			$role_array=array();
			foreach($Roles as $val)
			array_push($role_array,$val['sr_key']);
			$role['user_roles']=$role_array;
			$form->populate($option_info[0]);
			$form->populate($role);
			$this->view->user_id=$user_id;
		}
		else{
			$form->subadmin();
			$Type="Add";$page_type="add";$msgType="Added";
		}
		$this->view->pageHeading = $Type." Site Sub Admin";
		$this->view->pageDescription = $page_type." site sub admin";
		$this->view->pageIcon = "fa fa-group";
		
		
		  		
 		if($this->getRequest()->isPost()) {
 			
			$data_form = $this->getRequest()->getPost();
			//prd($data_form);
			
   			if($form->isValid($data_form)){
				
				$data_to_insert = $form->getValues() ;
				$modelEmail = new Application_Model_Email();
				$password='';
				if($data_to_insert!='')
				{
					$password=$data_to_insert['user_password'];
					$data_to_insert['user_password']=md5($data_to_insert['user_password']);	
					$data_to_insert['user_password_text']=$data_to_insert['user_password'];
				}
				$data_to_insert['user_created']=date('Y-m-d H:i:s');
				$data_to_insert['user_type']='site_subadmin';
				unset($data_to_insert['user_roles']);
				//prd($data_to_insert);
				unset($data_to_insert['user_rpassword']);
				if(isset($data_to_insert['user_email_test']))
				{
					unset($data_to_insert['user_email_test']);	
				}
				if(isset($data_to_insert['user_email_password']))
				{
					unset($data_to_insert['user_email_password']);	
				}
				if($user_id==''){
					
					$data_to_insert['user_status']=0;			
				//	prn($data_to_insert);		
					$is_insert = $this->modelStatic->Super_Insert("users",$data_to_insert);
					//prd($is_insert);
					$data_to_insert['user_password']=$password;
					$data_to_insert['user_password_text']=$password;
					$data_to_insert['pass_resetkey'] = md5($is_insert->inserted_id."!@#$%^$%&(*_+".time());
					$kk=$this->modelStatic->Super_Insert("users",array('pass_resetkey'=>$data_to_insert['pass_resetkey']),"user_id='".$is_insert->inserted_id."'");
					//prd($kk);
					$modelEmail->sendEmail('registration_verification_admin',$data_to_insert);
				
					foreach ($data_form['user_roles'] as $key=>$val)
					{
				
						//list($controller,$action)=GetRoles($val);
						$data_to_update=array('sr_key'=>$val,'sr_user_id'=>$is_insert->inserted_id);
						$this->modelStatic->Super_Insert("subadmin_roles",$data_to_update);
					}
				
			
				}
				else{
					$this->modelStatic->Super_Insert('users',$data_to_insert, "user_id = '".$user_id."'");	
					$removed = $this->modelStatic->getAdapter()->delete('subadmin_roles', "sr_user_id = '".$user_id."'");
			
					foreach ($data_form['user_roles'] as $key=>$val)
					{
						//list($controller,$action)=GetRoles($val);
						$data_to_update=array('sr_key'=>$val,'sr_user_id'=>$user_id,);
						$this->modelStatic->Super_Insert('subadmin_roles',$data_to_update);
					}
					
					
				}					
				$mySession->successMsg  = "Sub Admin Successfully ".$msgType." ";
				$this->redirect("/admin/subadmin/index");
 				
   			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 }
  		 $this->view->form =$form;
 		 $this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
  	}
	
	
	public function getsubadminsAction(){
		
  		$this->dbObj = Zend_Registry::get('db');
		$request_type = $this->_getParam('type');
  
		$aColumns = array('user_id','user_first_name','user_last_name','user_email','user_created','user_status');

		$sIndexColumn = 'user_id';
		$sTable = 'users';
  		
		
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
		
 		if(!$sWhere)
			$sWhere="where user_type='site_subadmin'";
		else 
			$sWhere.=" and user_type='site_subadmin'";	
		if($request_type!=""){
			
			switch($request_type){
				case 'all': $sWhere.=" "; break;	
				case 'verified': $sWhere.=" and   user_status = '1' "; break;	
				case 'blocked': $sWhere.=" and  user_status = '0' "; break;
				default : break ;	
			}
		}
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM  $sTable $sWhere $sOrder $sLimit";
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable $sWhere";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal-1,
				"iTotalDisplayRecords" => $iFilteredTotal-1,
				"aaData" => array()
			);
		
		$j=0;
		foreach($qry as $row1)
		{
			$row=array();
			
			/* Page Author Image */
 		 
			$row[] =($j+1).'.';
			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
  			$row[]=ucwords($row1['user_first_name'].' '.$row1['user_last_name']);
			$row[]=$row1['user_email'];
			$status = $row1['user_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['user_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
			
			$row[]=date('d-F , Y',strtotime($row1['user_created']));
     	 		 
   			$row[]='<a class="btn default btn-xs purple" href="'.$this->view->baseUrl().'/admin/subadmin/addsubadmin/user_id/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i> Edit </a>';
			$row[]='<a class="btn default btn-xs purple" href="'.$this->view->baseUrl().'/admin/subadmin/viewsubadmin/user_id/'.$row1[$sIndexColumn].'"><i class="fa fa-view"></i> View </a>';

  			$output['aaData'][] = $row;
			$j++;
		}	
		
		echo json_encode( $output );
		exit();
 	} 
	
	public function setstatusAction()
	{
		global $mySession;
		
		$status=$this->getRequest()->getParam('status');
		$this->view->status=$status;
		$user_id=$this->getRequest()->getParam('user_id');
		$user_data=$this->modelUser->Super_Get("users",'user_id="'.$user_id.'"',"fetch");
		/* Set all already exists entry to cancel */
		$exists_array=array('user_approval_status'=>'3');
		$this->modelUser->Super_Insert("user_approvals",$exists_array,'user_approval_userid="'.$user_id.'" and user_approval_type="3" and user_approval_status="0"');
		$this->view->user_id=$user_id;
		$user_extra_info=array(
			'old_status'=>$user_data['user_status'],
			'new_status'=>$status
		);
		$user_extra_text=serialize($user_extra_info);
		$approval_array=array(
						'user_approval_userid'=>$user_id,
						'user_approval_type'=>"3",
						'user_approval_status'=>"0",
						"user_approval_send_status"=>"1",
						'user_approval_usertype'=>'0',
						'user_approval_adminid'=>$this->view->user->user_id,
						'user_approval_extrainfo'=>$user_extra_text,
						
		);
		$jj=$this->modelUser->Super_Insert("user_approvals",$approval_array);
		$mySession->successMsg="Your status request has been send for approval to all other admins. Status will update after approval";
		$this->redirect("/admin/subadmin");
	}
		
	/* Remove Pages */
	public function  removesubadminAction(){
		
		global $mySession;
		
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		 
		$formData = $this->getRequest()->getPost();
		//prd($formData);
 	 
		if ($this->getRequest()->isPost() &&  isset($formData['users']) && count($formData['users'])) {
			
			$pages = implode(",",$formData['users']) ;
			
  			$removed = $this->modelStatic->getAdapter()->delete('users',"user_id IN (".$pages.")");
 			$mySession->successMsg=" Sub Admin(s) Deleted Successfuly for the database.. ";
 		}
		$this->redirect("/admin/subadmin/index");
 	}
	public function viewsubadminAction()
	{
		
		global $mySession; 
 		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'View Subadmin' =>'/subadmin/viewsubadmin');
  		$form = new Application_Form_StaticForm();
		$user_id =$this->_getParam('user_id') ;
		//prd($user_id);
		$Subadmin_Data=$this->modelStatic->Super_Get('users',"user_id='".$user_id."'","fetchAll",array('fields'=>'user_first_name,user_last_name,user_email,user_image,user_dob,user_address,user_postal_code,user_country,user_state,user_created'),array('0'=>array(0=>"subadmin_roles",1=>"sr_user_id=user_id",2=>'left',3=>array('sr_key'))));
		$this->view->data=$Subadmin_Data;
		$this->view->pageHeading =strtoupper($Subadmin_Data[0]['user_first_name'])."'S PROFILE";
			
	}
	

}

