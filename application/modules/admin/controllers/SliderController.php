<?php
class Admin_SliderController extends Zend_Controller_Action
{
	
	private $modelSlider = "";

    public function init(){
		
		$this->modelSlider = new Application_Model_Slider();
 	
	}
	
	public function indexAction(){
 		global $mySession; 
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Slider' =>'/slider');
   	}
	
	
	
 	public function addAction(){
		
		global $mySession;
 		
		$this->view->pageHeading = "Add Slider Image";
		$this->view->pageDescription = "add new slider image";
 		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Slider' =>'/slider' , 'Add Slider Image' =>'/slider/add');
  		
		$form = new Application_Form_Slider();
		
  		
   		if($this->getRequest()->isPost()){
			
			$posted_data = $this->getRequest()->getPost();
			
			if($form->isValid($posted_data)){
 
  				$media_path = $this->_handle_uploaded_image(); /* Receive Uploaded File */
				
				if(is_object($media_path) and $media_path->success){
					
					$inserted_data = $form->getValues();
					
					$inserted_data['slider_image_path'] = $media_path->media_path;
					
					$insertedMedia = $this->modelSlider->add($inserted_data); // Save Style in db
  					
					if(is_object($insertedMedia) and $insertedMedia->success){
						$mySession->successMsg = "Slider Image Successfully added "; // Sucessss
 						$this->_redirect("admin/slider");
 					}
  				}
			 
 				$mySession->errorMsg = $media_path->message; 
			
			}else{
 				$mySession->errorMsg = " Please Enter Correct Information ..! ";
 			}
		}
 	
		$this->view->form = $form;
		
		$this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
 	}
	

	public function editAction(){

 		global $mySession;
		
 		$this->view->pageHeading = "Edit Slider Image ";
	
		$this->view->pageDescription = "Edit Slider Image ";
	
		$slider_image_id =$this->_getParam('slider_image_id') ;
 		
		$form = new Application_Form_Slider();
		$this->view->breadcrumb =array('Manage Site '=>'/index' , 'Manage Slider' =>'/slider' , 'Edit Slider Image' =>'/slider/edit/slider_image_id/'.$slider_image_id);
		
 		$slider_image_info = $this->modelSlider->getMedia($slider_image_id);
		
		$this->view->slider_image_info=$slider_image_info;
		if(!$slider_image_info){
			$mySession->infoMsg = "No Such Media Item  Exists in the database...!";
			$this->_redirect('admin/Media');
		}
		
		$form->populate($slider_image_info);
		
		
		
 		 if($this->getRequest()->isPost()){
			 
			 $posted_data = $this->getRequest()->getPost();
			 
 			 if($form->isValid($posted_data)){
				 
 				$media_path = $this->_handle_uploaded_image();
				
				if($media_path->success){
					
					$data_array = $form->getValues();
					
					if($media_path->media_path){
 						$data_array['slider_image_path'] = $media_path->media_path ;
					}else{
						unset($data_array['slider_image_path']);
					}
					
 					$is_update = $this->modelSlider->add($data_array,$slider_image_id); // Save Style in db
				
					if(is_object($is_update) and $is_update->success){
						
						if(isset($data_array['slider_image_path'])){
							$this->_unlink_media_image($slider_image_info['slider_image_path']);
						}
						$mySession->successMsg = "Media Information Successfully Updated "; // Sucessss
						$this->_redirect("admin/slider");
					} 
				
 					$mySession->errorMsg = $is_update->message; 
 				}
				
				$mySession->errorMsg = $media_path->message; 

			}else{
				$mySession->errorMsg = " Please Check Information Again ... ! ";
 			}
		 
		 }
		
		 $this->view->form =  $form;
		 /* User Add Style Page to Render */
		$this->_helper->getHelper('viewRenderer')->renderScript("add.phtml");
		 
 	}
	
	
	
 	public function getmediaAction(){
 
 		$this->dbObj = Zend_Registry::get('db');
  
		$aColumns = array('slider_image_id','slider_image_status', 'slider_image_title' , 'slider_image_alt' ,'slider_image_path');
		$sIndexColumn = 'slider_image_id';
		
		$sTable = 'slider_images';
		
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
		
 		
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))." FROM   $sTable 
			 
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

 			$image = '<img src="'.HTTP_SLIDER_IMAGES_PATH."/300/".$row1['slider_image_path'].'" >';

			$row[]= $image ;	

			$row[]='<input class="elem_ids checkboxes"  type="checkbox" name="'.$sTable.'['.$row1[$sIndexColumn].']"  value="'.$row1[$sIndexColumn].'">';

  			$row[]=ucwords($row1['slider_image_title']);

			$row[]=ucwords($row1['slider_image_alt']); 

			
 			
			
			$status = $row1['slider_image_status']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['slider_image_status'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
	 
					
		
			$row[]='<a class="btn default btn-xs purple" href="'.APPLICATION_URL.'/admin/slider/edit/slider_image_id/'.$row1[$sIndexColumn].'"><i class="fa fa-edit"></i> Edit </a>';
 			
  			$output['aaData'][] = $row;
			$j++;
		}
		
		echo json_encode( $output );
		exit();
  	
		
	}
	
	/* 
	 *	Delete graphic Media Images 
	 */
	private function _unlink_media_image($image){
		
  		if($image!="" and file_exists(SLIDER_IMAGES_PATH."/".$image)){
			unlink(SLIDER_IMAGES_PATH."/".$image);
			unlink(SLIDER_IMAGES_PATH."/300/".$image);
		}
		return true ;
	}
	
	/* 
	 *	Handle The Uploaded Images For Graphic Media  
	 */
	private function _handle_uploaded_image(){
		
 		global $mySession; 
		
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
 			
 			$adapter->addFilter('Rename',array('target' => SLIDER_IMAGES_PATH."/".$new_name));
		
			try{
				$adapter->receive($file);
			}
			catch(Zend_Exception $e){
				return (object) array('success'=>false,"error"=>true,'exception'=>true,'message'=>$e->getMessage(),'exception_code'=>$e->getCode()) ;
			}
			
			$thumb_config = array("source_path"=>SLIDER_IMAGES_PATH,"name"=> $new_name);
			Application_Plugin_ImageCrop :: uploadThumb(array_merge($thumb_config,array("destination_path"=>SLIDER_IMAGES_PATH."/300","size"=>300)));
				 
 			//$uploaded_image_names[]=array('media_path'=>$new_name); => For Multiple Images
   		
		}/* End Foreach Loop for all images */
		
		
		return (object)array("success"=>true,'error'=>false,"message"=>"Image(s) Successfully Uploaded","media_path"=>$new_name) ;
 		
   	 
 	}
  
  
  
	public function removeAction(){
		
 		
		global $mySession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		
 
 
 		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
 			
			if(isset($formData['slider_images']) and count($formData['slider_images'])){
				
				 foreach($formData['slider_images'] as $key=>$values){
 
   					 $user_info = $this->modelSlider->getMedia($values);
					 
					 if(empty($user_info))
						continue ;
						
 					$this->_unlink_media_image($user_info['slider_image_path']);
					
					$removed = $this->modelSlider->delete("slider_image_id IN ($values)");
					 
				 }
 
 				 
 				$mySession->successMsg = " Slider Image Deleted Successfully ";
				
 			}else{
				$mySession->errorMsg = " Invalid Request to Delete User(s) ";
			}
			
 			$this->_redirect('/admin/slider/?removed=1');	 
   	 
		} 
		
 		
		$this->_redirect('/admin/slider/?removed=1');	 
	
	}
	
}

