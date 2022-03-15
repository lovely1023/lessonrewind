<?php
class Admin_SubscriptionController extends Zend_Controller_Action
{
    public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->view->pageIcon = "fa  fa-users";
    }
 	
	
	
 	public function indexAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage Subscription Plans";
		$this->view->pageDescription = "manage all subscription plans ";
		$this->view->request_type = "all";
		$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Subscription Plan' =>'/subscription');
		 
 	}
		public function addplanAction()
	{
  
		global $mySession; 
		$model = new Application_Model_SuperModel();
		$form = new Application_Form_StaticForm();
		$form->subscription();
		$subscription_plan_id =$this->_getParam('subscription_plan_id');
		
		$plan_data=array();
		if(isset($subscription_plan_id) && !empty($subscription_plan_id))
		{
			$plan_data=$model->Super_Get("subscription_plan",'subscription_plan_id="'.$subscription_plan_id.'"',"fetch");
			$form->populate($plan_data);
			$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Subscription Plan' =>'/subscription', 'Edit Subscription Plan' =>'/subscription/addplan/subscription_plan_id/'.$subscription_plan_id);
		}
		else
		{
			$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Subscription Plan' =>'/subscription', 'Add Subscription Plan' =>'/subscription/addplan');	
		}
		$this->view->plan_data=$plan_data;
		$this->view->pageHeading = "Add Plan";
		$this->view->pageDescription = "Add Plan";
		$this->view->pageIcon = "fa fa-sitemap";
  		if($this->getRequest()->isPost()) 
			   {
			   $data_form = $this->getRequest()->getPost();
			    if($form->isValid($data_form))
				  {
					if(!isset($subscription_plan_id))
					{
							$inserted_data = $form->getValues();
							$inserted_data['subscription_plan_date']=date("Y-m-d H:i:s");
							$is_insert = $model->Super_Insert("subscription_plan",$inserted_data);
							$mySession->successMsg  = "Plan has been added Successfully";
							$this->_redirect('/admin/subscription');
					}
					else
					{
						$inserted_data = $form->getValues();
					 	$is_insert = $model->Super_Insert("subscription_plan",$inserted_data,"subscription_plan_id='".$subscription_plan_id."'"); 
						$mySession->successMsg  = "Plan has been updated Successfully";
						$this->_redirect('/admin/subscription');
					}
					
				 
				  }
				  else
				  {
					 $mySession->errorMsg = " Please Check Information Again ... ! ";
				  }
			  }
			 $this->view->form =$form;
			$this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
   }

 	/* Ajax Call For Get Users */
  	public function getpalnsAction()
	{
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'subscription_plan_id',
			'subscription_plan_title',
			'subscription_plan_description',
			'subscription_plan_price',
			'subscription_plan_date',
			'subscription_account_type'
			
		);
		$sIndexColumn = 'subscription_plan_id';
		$sTable = 'subscription_plan';
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
				$sOrder = "ORDER BY subscription_plan_id ASC";
			}
		}
		$sOrder = "ORDER BY subscription_plan_id ASC";
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
		
		
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM  $sTable $sWhere $sOrder $sLimit";
		
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable";
		$rResultTotal = $this->dbObj->query($sQuery)->fetchAll(); 
		$iTotal = $rResultTotal[0]['cnt'];
		
		/*
		 * Output
		 */
		 
		 
		$output = array(
 				"iTotalRecords" => $iTotal-1,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		$i=1;
		foreach($qry as $row1){
			
 			$row=array();
			$row[] = $row1['subscription_plan_id'];
  			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			$row[]=$row1['subscription_plan_title'];
			if($row1['subscription_account_type']==0)
			{
					$row[]="Free";	
			}
			else if($row1['subscription_account_type']==5)
			{
					$row[]='+'.$this->view->site_configs['site_currency']." ".$row1['subscription_plan_price']." /month";		
			}
			else
			{
					$row[]=$this->view->site_configs['site_currency']." ".$row1['subscription_plan_price']." /month";	
			}
   		
			$row[]=$row1['subscription_plan_description'];
			$row[] =  '<a href="'.APPLICATION_URL.'/admin/subscription/addplan/subscription_plan_id/'.$row1[$sIndexColumn].'" class="btn btn-xs purple"> Edit <i class="fa fa-search"></i></a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	
	/* 
	 *	Remove Graphic Media 
	 */
 	public function removeAction(){
		
		global $mySession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
 
 
 		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData['users']) and count($formData['users'])){
				
				 foreach($formData['users'] as $key=>$values){
 
   					 $user_info = $this->modelUser->get($values);
					 
					 if(empty($user_info))
						continue ;
						
 					$this->_unlink_user_image($user_info['user_image']);
					
					$removed = $this->modelUser->delete("user_id IN ($values)");
					 
				 }
 
 				 
 				$mySession->successMsg = " Subadmin Deleted Successfully ";
				
 			}else{
				$mySession->errorMsg = " Invalid Request to Delete User(s) ";
			}
			
 			$this->_redirect('/admin/user/?removed=1');	 
   	 
		} 
		
 			
 	}
	
	
	/* 
	 *	Delete graphic Media Images 
	 */
	private function _unlink_user_image($image){
		
		if(empty($image))
			return true; 
		
	 
  		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/".$image)){
			unlink(PROFILE_IMAGES_PATH."/".$image);
 		}
		
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/thumb/".$image)){
			unlink(PROFILE_IMAGES_PATH."/thumb/".$image);
 		}
		
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/60/".$image)){
			unlink(PROFILE_IMAGES_PATH."/60/".$image);
 		}
		
		if($image!="" and file_exists(PROFILE_IMAGES_PATH."/160/".$image)){
			unlink(PROFILE_IMAGES_PATH."/160/".$image);
 		}
		
		
		
	}
	

	
	/* Handle The Uploaded Images For Graphic Media  */
	private function _handle_profile_image(){
		
 		global $objSession; 
		
		$uploaded_image_names = array();
	 
		$adapter = new Zend_File_Transfer_Adapter_Http();
	
		$files = $adapter->getFileInfo();
  		 
		$uploaded_image_names = array();
		
		$new_name = false; 
		 
  		/*prd($adapter);*/
		foreach ($files as $file => $info) { /* Begin Foreach for handle multiple images */
		
  			$name_old = $adapter->getFileName($file);
			
			if(empty($name_old)){
				continue ;			
			}
			
			$file_title  = $adapter->getFileInfo($file);
			
			$file_title = $file_title[$file]['name']; 
				
  			$uploaded_image_extension = getFileExtension($name_old);
			
 			$file_title  = str_replace(".".$uploaded_image_extension,"",$file_title);
			
			$file_title = formatImageName($file_title);
  
 			$new_name = $file_title."-".time()."-".rand(1,100000).".".$uploaded_image_extension;
 			
  			$adapter->addFilter('Rename',array('target' => PROFILE_IMAGES_PATH."/".$new_name));
		
			try{
				$adapter->receive($file);
			}
			catch(Zend_Exception $e){
				return (object) array('success'=>false,"error"=>true,'exception'=>true,'message'=>$e->getMessage(),'exception_code'=>$e->getCode()) ;
			}
			
				$thumb_config = array("source_path"=>PROFILE_IMAGES_PATH,"name"=> $new_name);
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("size"=>300)));
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/60","crop"=>true ,"size"=>60,"ratio"=>false)));
				Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>PROFILE_IMAGES_PATH."/160","crop"=>true ,"size"=>160,"ratio"=>false)));
			
  			//$uploaded_image_names[]=array('media_path'=>$new_name); => For Multiple Images
   		
		}/* End Foreach Loop for all images */
		
		
		return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$new_name) ;
 		
   	 
 	}
	
	 
	 
	
	 
}