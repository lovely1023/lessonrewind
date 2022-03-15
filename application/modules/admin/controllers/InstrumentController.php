<?php
class Admin_InstrumentController extends Zend_Controller_Action
{
    public function init(){
 		$this->modelUser = new Application_Model_User();
		$this->modelSuper = new Application_Model_SuperModel();
		$this->view->pageIcon = "fa  fa-users";
    }
 	
	
	
 	public function indexAction(){
 		global $mySession; 
 		$this->view->pageHeading = "Manage All Instruments";
		$this->view->pageDescription = "manage all site Instruments ";
		$this->view->request_type = "all";
		$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage All Instruments' =>'/instruments');
		 
 	}
		public function addAction()
	{
  
		global $mySession; 
		$model = new Application_Model_SuperModel();
		$form = new Application_Form_SchoolForm();
		$form->newinstrument();
		$Instrument_id =$this->_getParam('Instrument_id');
		
		$instrument_data=array();
		if(isset($Instrument_id) && !empty($Instrument_id))
		{
			$this->view->pageHeading = "Edit Instrument";
			$this->view->pageDescription = "Edit Instrument";
			$instrument_data=$model->Super_Get("Instruments",'Instrument_id="'.$Instrument_id.'"',"fetch");
			$form->populate($instrument_data);
			$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Instruments' =>'/instrument', 'Edit Instrument' =>'/instrument/add/Instrument_id/'.$Instrument_id);
		}
		else
		{
			$this->view->pageHeading = "Add Instrument";
			$this->view->pageDescription = "Add Instrument";
			$this->view->breadcrumb =array('Manage Site '=>'/index' ,  'Manage Instrument' =>'/instrument', 'Add Instrument' =>'/instrument/add');	
		}
		$this->view->instrument_data=$instrument_data;
		$this->view->pageIcon = "fa fa-sitemap";
  		if($this->getRequest()->isPost()) 
			   {
			   $data_form = $this->getRequest()->getPost();
			    if($form->isValid($data_form))
				  {
					
					  	$inserted_data = $form->getValues();
						    $inserted_data['Instrument_userid']=1;
							$inserted_data['Instrument_schoolid']=1;
							$inserted_data['Instrument_status']=0;
					if(isset($instrument_data) && empty($instrument_data))
					{
						
						
							$inserted_data['Instrument_date']=date("Y-m-d H:i:s");
							$is_insert = $model->Super_Insert("Instruments",$inserted_data);
							
							$mySession->successMsg  = "Plan has been added Successfully";
							$this->_redirect('/admin/instrument');
					}
					else
					{
						
						
						$inserted_data = $form->getValues();
					 	$is_insert = $model->Super_Insert("Instruments",$inserted_data,"Instrument_id='".$Instrument_id."'"); 
						$mySession->successMsg  = "Instrument has been updated Successfully";
						$this->_redirect('/admin/instrument');
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
  	public function getinstrumentAction()
	{
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'Instrument_id',
			'Instrument_name',
			'Instrument_date',
			'Instrument_userid',
			'Instrument_schoolid',
			'Instrument_status',
			'Instrument_active'
			
		);
		$sIndexColumn = 'Instrument_id';
		$sTable = 'Instruments';
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
		
		
		if ( $sWhere == "" )
				{
					$sWhere = "WHERE Instrument_status='0'";
				}
				else
				{
					$sWhere .= " AND  Instrument_status='0'";
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
			$student_count_arr=array();
			$teaher_count_arr=array();
			$student_count_arr=$this->modelSuper->Super_Get("student_instrument","student_instrument_insid='".$row1['Instrument_id']."'","fetch",array("fields"=>array(new Zend_Db_Expr("IFNULL(COUNT(student_intrument_id),0) as studentcount" ))));
			
			$teaher_count_arr=$this->modelSuper->Super_Get("teacher_insruments","teacher_insrument_instid='".$row1['Instrument_id']."'","fetch",array("fields"=>array(new Zend_Db_Expr("IFNULL(COUNT(teacher_insrument_id),0) as teachercount" ))));
 			$row=array();
			
			$row[] = $row1['Instrument_id'];
			if($student_count_arr['studentcount']==0 && $teaher_count_arr['teachercount']==0)
			{
  			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';
			}
			else
			{
				$row[]='';
			}
			
			$row[]=$row1['Instrument_name'];
			$row[]=$student_count_arr['studentcount'];
			$row[]=$teaher_count_arr['teachercount'];
			$status = $row1['Instrument_active']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['Instrument_active'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
			$row[] =  '<a href="'.APPLICATION_URL.'/admin/instrument/add/Instrument_id/'.$row1[$sIndexColumn].'" class="btn btn-xs purple"> Edit <i class="fa fa-search"></i></a>';
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	public function instrumentnameAction()
	{
		$this->_helper->layout->disableLayout();
		$instrument_id=$this->getRequest()->getParam("instrument_id");
		$Instrument_name=$this->getRequest()->getParam("Instrument_name");
		$where='Instrument_name="'.$Instrument_name.'"';
		if(isset($instrument_id) && $instrument_id!=0)
		{
			
			$where.=' and Instrument_id!="'.$instrument_id.'"';	
		}
		
		$instrument_data=$this->modelSuper->Super_Get("Instruments",$where,"fetch");
		if(empty($instrument_data))
		{
			echo json_encode("true");			
		}
		else
		{
			echo json_encode("`$Instrument_name` is alreaty exists. Please enter another Instrument Name");	
		}
			
 			exit();
	
	}
	
	/* 
	 *	Remove Graphic Media 
	 */
 	public function removeAction(){
		
		global $mySession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
 		if ($this->getRequest()->isPost())
		 {
		
			$formData = $this->getRequest()->getPost();
			if(isset($formData['Instruments']) and count($formData['Instruments']))
			{
				 foreach($formData['Instruments'] as $key=>$values)
				 {
   					 $user_info = $this->modelUser->get($values);
					 if(empty($user_info))
						continue ;
						$removed = $this->modelUser->delete("Instrument_id IN ($values)");
				 }
 
 				 
 				$mySession->successMsg = " Instrument Deleted Successfully ";
				
 			}else{
				$mySession->errorMsg = " Invalid Request to Delete User(s) ";
			}
			
 			$this->_redirect('/admin/instrument/?removed=1');	 
   	 
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