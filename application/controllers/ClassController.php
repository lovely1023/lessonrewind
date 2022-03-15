<?php
class ClassController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		
   	}
	
	
 	public function indexAction(){	
 		global $objSession ; 
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='5'","fetch");	
				
				if(empty($paremission_data))
				{
					/* If Permission Data is empty */
					$this->redirect("profile/dashboard");	
				}
			}	
			else
			{
				/* If user is not admin or subadmin */
				$this->redirect('index');	
			}
		}
		$this->view->pageHeading = "All Group Classes";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  All Group Classes';
		$classes_data=array();
		$classes_data=$this->modelStatic->Super_Get("Classes","class_insertid='".$this->view->user->user_id."'","fetchAll");
		$this->view->classes_data=$classes_data;
	}
	
	
	
	public function reomvestudentAction()
	{
		
		global $objSession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$Instrument_id=$this->getRequest()->getParam("Instrument_id");
 
 
 		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
		
			if(isset($formData['student_instrument']) and count($formData['student_instrument'])){
				
				 foreach($formData['student_instrument'] as $key=>$values)
				 {
					
   					
 					$gg=$this->modelStatic->Super_Delete("student_instrument",'student_instrument_studentid="'.$values.'" and student_instrument_insid="'.$Instrument_id.'"');
				
				 }
 
 				 
 				$objSession->successMsg = " Student Deleted Successfully ";
				
 			}else{
				$objSession->errorMsg = " Invalid Request to Delete User(s) ";
			}
			
 			$this->_redirect('/class/newinstrument/Instrument_id/'.$Instrument_id);	 
   	 
		} 
		
 			
 	}
	
	public function reomvestudentclassAction()
	{
		
		global $objSession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$class_id=$this->getRequest()->getParam("class_id");
 
 
 		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
		
			if(isset($formData['student_instrument']) and count($formData['student_instrument'])){
				
				 foreach($formData['student_instrument'] as $key=>$values)
				 {
					
   					
 					$gg=$this->modelStatic->Super_Delete("student_class",'student_class_studentid="'.$values.'" and student_class_classid="'.$class_id.'"');
				
				 }
 
 				 
 				$objSession->successMsg = " Student Deleted Successfully ";
				
 			}else{
				$objSession->errorMsg = " Invalid Request to Delete User(s) ";
			}
			
 			$this->_redirect('/class/newclass/class_id/'.$class_id);	 
   	 
		} 
		
 			
 	}
	
	
	
		public function removeteachersAction()
	{
		
		global $objSession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$Instrument_id=$this->getRequest()->getParam("Instrument_id");
 
 
 		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData['teacher_instrument']) and count($formData['teacher_instrument'])){
				
				 foreach($formData['teacher_instrument'] as $key=>$values)
				 {
					
   					
 					$gg=$this->modelStatic->Super_Delete("teacher_insruments",'teacher_insrument_userid="'.$values.'" and teacher_insrument_instid="'.$Instrument_id.'"');
				
				 }
 
 				 
 				$objSession->successMsg = " Teacher Deleted Successfully ";
				
 			}else{
				$objSession->errorMsg = " Invalid Request to Delete Teacher(s) ";
			}
			
 			$this->_redirect('/class/newinstrument/Instrument_id/'.$Instrument_id);	 
   	 
		} 
		
 			
 	}
	
		public function removeteachersclassAction()
	{
		
		global $objSession;
 
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
		$class_id=$this->getRequest()->getParam("class_id");
 
 
 		if ($this->getRequest()->isPost()) {
			
			$formData = $this->getRequest()->getPost();
			
			if(isset($formData['teacher_instrument']) and count($formData['teacher_instrument'])){
				
				 foreach($formData['teacher_instrument'] as $key=>$values)
				 {
					
   					
 					$gg=$this->modelStatic->Super_Delete("teacher_classes",'teacher_class_userid="'.$values.'" and teacher_class_classid="'.$class_id.'"');
				
				 }
 
 				 
 				$objSession->successMsg = " Teacher Deleted Successfully ";
				
 			}else{
				$objSession->errorMsg = " Invalid Request to Delete Teacher(s) ";
			}
			
 			$this->_redirect('/class/newclass/class_id/'.$class_id);	 
   	 
		} 
		
 			
 	}
	
	
		/* Get All Students */
	public function getstudentAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$Instrument_id=$this->getRequest()->getParam('Instrument_id');
 		$aColumns = array(
			'student.user_id',
			'student.user_first_name',
			'student.user_last_name',
			'student.user_email',
			'student.user_created',
		);
		$sIndexColumn = 'student.user_id';
		$sTable = 'users';
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
				//$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(user_first_name,' ',user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
		//prd($class_id);
		$sWhere1=$sWhere;
		if ( $sWhere == "" )
			{
				$sWhere= "WHERE (student.user_type='student' and  (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."'))";
				if($Instrument_id==0)
				{ 
					$sWhere1= "WHERE (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."'))";
				}
				else
				{
					$sWhere1 = "WHERE (student.user_type='student' and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') )";
				}
					
			}
			else
				{
				$sWhere .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') )";
				if($Instrument_id==0)
				{
					$sWhere1 .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') )";
				}
				else
				{
					$sWhere1 .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."')  )";	
				}
				}
				$sWhere1.="AND student_instrument.student_instrument_insid='".$Instrument_id."'";
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." FROM $sTable  student left join student_instrument on student_instrument.student_instrument_studentid=student.user_id 
		$sWhere1 group by student.user_id $sOrder $sLimit";
	
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
		
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt ";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`user_id`) as cnt FROM $sTable  student $sWhere ";
		//echo $sQuery;die;
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
		$i=1;
	 
		foreach($qry as $row1){
			
 			$row=array();
			$row[]='<input class="elem_ids checkbox1"  type="checkbox" name="student_instrument['.$row1['user_id'].']"  value="'.$row1['user_id'].'">';
			$row[]=$row1['user_last_name'];
			$row[]=$row1['user_first_name'];
   			$row[]=date('F d Y g:i A', strtotime($row1['user_created']));
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	/* Get All Students */
	public function getstudentclassAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$class_id=$this->getRequest()->getParam('class_id');
 		$aColumns = array(
			'student.user_id',
			'student.user_first_name',
			'student.user_last_name',
			'student.user_email',
			'student.user_created',
		);
		$sIndexColumn = 'student.user_id';
		$sTable = 'users';
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
				//$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(user_first_name,' ',user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; // NEW CODE
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
		//prd($class_id);
		$sWhere1=$sWhere;
		if ( $sWhere == "" )
			{
				$sWhere= "WHERE (student.user_type='student' and  (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."'))";
				if($class_id==0)
				{ 
					$sWhere1= "WHERE (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."'))";
				}
				else
				{
					$sWhere1 = "WHERE (student.user_type='student' and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') )";
				}
					
			}
			else
				{
				$sWhere .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') )";
				if($class_id==0)
				{
					$sWhere1 .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."') )";
				}
				else
				{
					$sWhere1 .= " AND (student.user_type='student'  and (student.user_insertby='".$this->view->user->user_id."' or  student.user_school_id='".$this->view->user->user_school_id."' or   student.user_school_id='".$this->view->user->user_id."')  )";	
				}
				}
				$sWhere1.="AND student_class.student_class_classid='".$class_id."'";
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." FROM $sTable  student left join student_class on student_class.student_class_studentid=student.user_id $sWhere1 group by student.user_id $sOrder $sLimit";
	
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
		
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt ";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`user_id`) as cnt FROM $sTable  student $sWhere ";
		//echo $sQuery;die;
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
		$i=1;
	 
		foreach($qry as $row1){
			
 			$row=array();
			$row[]='<input class="elem_ids checkbox1"  type="checkbox" name="student_instrument['.$row1['user_id'].']"  value="'.$row1['user_id'].'">';
		
			$row[]=$row1['user_last_name'];
				$row[]=$row1['user_first_name'];
   			$row[]=date('F d Y g:i A', strtotime($row1['user_created']));
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	/* Get All Classes */
	public function getteacherAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$Instrument_id=$this->getRequest()->getParam("Instrument_id");
 		$aColumns = array(
			'user_id',
			'user_first_name',
			'user_last_name',
			'user_email',
			'user_created',
			'user_status',
		);
		$sIndexColumn = 'user_id';
		$sTable = 'users';
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
				//$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
				$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(user_first_name,' ',user_last_name) LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
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
					$sWhere = "WHERE (user_type='teacher' and (user_insertby='".$this->view->user->user_id."' or user_insertby='".$this->view->user->user_school_id."' or user_school_id='".$this->view->user->user_id."'  ))";
			}
			else
			{
					$sWhere .= " AND (user_type='teacher'  and (user_insertby='".$this->view->user->user_id."' or user_insertby='".$this->view->user->user_school_id."'  or user_school_id='".$this->view->user->user_id."' ) )";
			}
		
		
		$sWhere1=$sWhere.'and teacher_insrument_instid="'.$Instrument_id.'"';
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." ,teacher_insrument_instid FROM $sTable left join teacher_insruments on teacher_insruments.teacher_insrument_userid=users.user_id  $sWhere1 group by user_id $sOrder $sLimit";
	//	echo $sQuery;die;
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 		
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
			
		/* Total data set length */
		$sQuery = "SELECT COUNT(`user_id`) as cnt FROM $sTable $sWhere";
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
		$i=1;
		foreach($qry as $row1){
			
 			$row=array();
			$row[]='<input class="elem_ids checkbox2"  type="checkbox" name="teacher_instrument['.$row1['user_id'].']"  value="'.$row1['user_id'].'">';
			$row[]=$row1['user_last_name'];
			$row[]=$row1['user_first_name'];
			$row[]=date('F d Y g:i A', strtotime($row1['user_created']));
			
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	
	/* Get All Classes */
	public function getteacherclassAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$class_id=$this->getRequest()->getParam("class_id");
 		$aColumns = array(
			'user_id',
			'user_first_name',
			'user_last_name',
			'user_email',
			'user_created',
			'user_status',
		);
		$sIndexColumn = 'user_id';
		$sTable = 'users';
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
				//$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
				$sWhere .= "".$aColumns[$i]." LIKE '%".$_GET["sSearch"]."%' OR CONCAT(user_first_name,' ',user_last_name) LIKE '%".$_GET["sSearch"]."%' OR "; // NEW CODE
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
					$sWhere = "WHERE (user_type='teacher' and (user_insertby='".$this->view->user->user_id."' or user_insertby='".$this->view->user->user_school_id."' or user_school_id='".$this->view->user->user_id."'   or user_school_id='".$this->view->user->user_school_id."' ))";
			}
			else
			{
					$sWhere .= " AND (user_type='teacher'  and (user_insertby='".$this->view->user->user_id."' or user_insertby='".$this->view->user->user_school_id."'  or user_school_id='".$this->view->user->user_id."'  or user_school_id='".$this->view->user->user_school_id."') )";
			}
		
		
		$sWhere1=$sWhere.'and teacher_class_classid="'.$class_id.'"';
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." ,teacher_class_classid FROM $sTable left join teacher_classes on teacher_classes.teacher_class_userid=users.user_id  $sWhere1 group by user_id $sOrder $sLimit";
	//	echo $sQuery;die;
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 		
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
			
		/* Total data set length */
		$sQuery = "SELECT COUNT(`user_id`) as cnt FROM $sTable $sWhere";
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
		$i=1;
		foreach($qry as $row1){
			
 			$row=array();
			$row[]='<input class="elem_ids checkbox2"  type="checkbox" name="teacher_instrument['.$row1['user_id'].']"  value="'.$row1['user_id'].'">';
			
			$row[]=$row1['user_last_name'];
			$row[]=$row1['user_first_name'];
			$row[]=date('F d Y g:i A', strtotime($row1['user_created']));
			
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	
	/* Get All Classes */
	public function getclassesAction()
	{
		$this->dbObj = Zend_Registry::get('db');
 		$aColumns = array(
			'class_id',
			'class_name',
			'class_start_date',
			'class_end_date',
			'class_start_time',
			'class_end_time',
			'class_days',
			'class_date',
			'class_insertdate',
			'class_insertid',
			'class_template',
			'class_date_type'
			
			
		);
		$sIndexColumn = 'class_id';
		$sTable = 'Classes';
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
		$sOrder = "ORDER BY class_name ASC ";
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
					$sWhere = "WHERE (class_insertid='".$this->view->user->user_id."' or class_school_id='".$this->view->user->user_school_id."' or class_school_id='".$this->view->user->user_id."' )";
				}
				else
				{
					$sWhere .= " AND (class_insertid='".$this->view->user->user_id."' or  class_school_id='".$this->view->user->user_school_id."' or class_school_id='".$this->view->user->user_id."' )";
				}
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) .", COUNT(DISTINCT(student_class_id)) as studentcount , COUNT(DISTINCT(teacher_class_id)) as teachercount FROM $sTable left join student_class on student_class.student_class_classid=Classes.class_id left join teacher_classes on teacher_classes.teacher_class_classid=Classes.class_id     $sWhere group by class_id $sOrder $sLimit";
	//	echo $sQuery;die;
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
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		$i=1;
		
		foreach($qry as $row1){
			
 			$row=array();
			
			//$row[] = $i;
			if($row1['studentcount']!=0)
			{
				$row[]='<a href="'.APPLICATION_URL.'/student/index/class_id/'.$row1['class_id'].'">'.$row1['class_name'].'</a>';	
			}
			else
			{
				$row[]=$row1['class_name'];	
			}
			
			$row[]=$row1['class_days'];
			if($row1['class_date_type']==2 or $row1['class_date_type']==3)
			{
				$row[]=date('F d Y', strtotime($row1['class_start_date']))." - ".date('F d Y', strtotime($row1['class_end_date']));
			}
			else
			{
				$row[]="Ongoing";	
			}
			if($row1['class_date_type']==1 or $row1['class_date_type']==3)
			{
   				$row[]=$row1['class_start_time']." - ".$row1['class_end_time'];
			}
			else
			{
				$row[]="-";
			}
			$row[]=$row1['teachercount'];
			$row[]=$row1['studentcount'];
			
			$status = $row1['class_template']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['class_template'].' "  '.$status.'  id="'.$sTable.'-'.$row1[$sIndexColumn].'" onChange="globalStatus(this)" />
					</div>';
			$row[] =  '<a href="'.APPLICATION_URL.'/class/newclass/class_id/'.$row1[$sIndexColumn].'" class="btn btn-xs purple"> <i class="fa fa-edit"></i></a>';
			
			
			if($row1['studentcount']==0 && $row1['teachercount']==0)
			{
				$row[]='<input type="checkbox"  value="'.$row1['class_id'].'"   name="select_classes[]" id="select_user_'.$row1['class_id'].'"  />';
					
				/*	 $row[]='<a onclick="removeclass('.$row1[$sIndexColumn].',1)" class="btn btn-xs purple delete_class"> <i class="fa fa-trash-o"></i></a>';*/
			}
			else
			{
					$row[]='Currently in use';
			/* $row[]='<a onclick="removeclass('.$row1[$sIndexColumn].',0)" class="btn btn-xs purple delete_class"> <i class="fa fa-trash-o"></i></a>';*/
			}
 			$output['aaData'][] = $row;
			$j++;
			$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	/* Get Saved Template Data */
	public function getsavetemplatedataAction()
	{
		global $objSession ; 
		$class_id=$this->getRequest()->getParam('id');
		$class_data=array();
		$class_data=$this->modelStatic->Super_Get("Classes","class_id='".$class_id."'","fetch");
		$return_array=array();
		$return_array['class_name']=$class_data['class_name'];
		$return_array['class_date']=$class_data['class_date'];
		$return_array['class_days']=explode(" and ",$class_data['class_days']);
		$return_array['class_date_type']=$class_data['class_date_type'];
		if($class_data['class_date_type']==0)
				{
					/* Ongoing Class */	
					$return_array['class_date']	="";
					
				}
				else if($class_data['class_date_type']==1)
				{
					/* Ongoing Class and time */	
					$return_array['date_timepicker_start']=$class_data['class_start_time'];
					$return_array['date_timepicker_end']=$class_data['class_end_time'];
				}
				else if($class_data['class_date_type']==2)
				{
					/* Class Specific Date Range Picker */	
					$return_array['class_date_only']=$class_data['class_date'];
				}
				else
				{
					
					$datexplode=explode("-",$class_data['class_date']);
					$startexplode=explode(" ",trim($datexplode[0]));
					$endexplode=explode(" ",trim($datexplode[1]));
					/* Class Specific Date and Time Range Picker */
					$return_array['class_date']=$startexplode[0]."-".$endexplode[0]."  "."(".$startexplode[1]." ".$startexplode[2]."-".$endexplode[1]." ".$endexplode[2].")";
					$return_array['daterangepicker_start']=$startexplode[0]." ".$startexplode[1]." ".$startexplode[2];
					$return_array['daterangepicker_end']=$endexplode[0]." ".$endexplode[1]." ".$endexplode[2];
					$timeexplode=explode(":",$startexplode[1]);
					$timeexplode1=explode(":",$endexplode[1]);
					$return_array['timeexplode1']=$timeexplode[0];
					$return_array['timeexplode2']=$timeexplode[1];
					$return_array['timeexplode3']=$startexplode[2];
					$return_array['timeexplode4']=$timeexplode1[0];
					$return_array['timeexplode5']=$timeexplode1[1];
					$return_array['timeexplode6']=$endexplode[2];
					$return_array['timeexplode7']=$startexplode[0];
					$return_array['timeexplode8']=$endexplode[0];
				}
		echo json_encode($return_array);
		exit;
		
	}
	public function instrumentsAction(){	
 		global $objSession ; 
		$param=$this->getRequest()->getParam('param');
		$status=2;
		if(isset($param) && $param==1)
		{
			$status=1;	
		}
		$this->view->status=$status;
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='6'","fetch");	
				
				if(empty($paremission_data))
				{
					/* If Permission Data is empty */
					$this->redirect("profile/dashboard");	
				}
			}	
			else
			{
				/* If user is not admin or subadmin */
				$this->redirect('index');	
			}
		}
		$this->view->pageHeading = "All Instruments";
		$this->view->pageHeadingshow = '<i class="fa fa-list"></i>  All Instruments';
		$instrument_data=array();
		$instrument_data=$this->modelStatic->Super_Get("Instruments","(Instrument_userid='".$this->view->user->user_id."')","fetchAll");
		$this->view->instrument_data=$instrument_data;
	}
	
	/* Get All Classes */
	public function getinstrumentAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$status=$this->getRequest()->getParam('status');
		
 		$aColumns = array(
			'Instrument_id',
			'Instrument_name',
			'Instrument_date',
			'Instrument_userid',
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
		$sOrder="order by Instrument_name ASC";
		
		
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
					$sWhere = "WHERE ((Instrument_userid='".$this->view->user->user_id."' or Instrument_schoolid='".$this->view->user->user_school_id."' or Instrument_schoolid='".$this->view->user->user_id."' ))";
				}
				else
				{
					$sWhere .= " AND ((Instrument_userid='".$this->view->user->user_id."' or Instrument_schoolid='".$this->view->user->user_school_id."' or Instrument_schoolid='".$this->view->user->user_id."' ))";
				}
		
		
		if(isset($status) && $status==2)
		{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ((Instrument_userid='".$this->view->user->user_id."' or Instrument_schoolid='".$this->view->user->user_school_id."' or Instrument_schoolid='".$this->view->user->user_id."' ))";
				}
				else
				{
					$sWhere .= " AND ((Instrument_userid='".$this->view->user->user_id."' or Instrument_schoolid='".$this->view->user->user_school_id."' or Instrument_schoolid='".$this->view->user->user_id."' ) )";
				}	
		}
		else
		{
				if ( $sWhere == "" )
				{
					$sWhere = "WHERE ((Instrument_userid='".$this->view->user->user_id."' or Instrument_schoolid='".$this->view->user->user_school_id."' or Instrument_schoolid='".$this->view->user->user_id."' ) and Instrument_active='1')";
				}
				else
				{
					$sWhere .= " AND ((Instrument_userid='".$this->view->user->user_id."' or Instrument_schoolid='".$this->view->user->user_school_id."' or Instrument_schoolid='".$this->view->user->user_id."' ) ) and Instrument_active='1'";
				}	
		}
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ." ,COUNT(DISTINCT(teacher_insrument_userid)) as teachers , COUNT(DISTINCT(student_instrument_studentid)) as students FROM $sTable left join teacher_insruments on teacher_insruments.teacher_insrument_instid=Instruments.Instrument_id left join student_instrument on student_instrument.student_instrument_insid=Instruments.Instrument_id      $sWhere  group by Instrument_id $sOrder $sLimit";
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
 				"iTotalRecords" => $iTotal,
				"iTotalDisplayRecords" => $iFilteredTotal,
				"aaData" => array()
			);
		
		$j=0;
		$i=1;
		foreach($qry as $row1){
			
 			$row=array();
		
			$row[]=$row1['Instrument_name'];
   			$row[]=$row1['students'];
			$row[]=$row1['teachers'];
			if($row1['Instrument_status']==1)
			{
				
			$status = $row1['Instrument_active']!=1?"checked='checked'":" ";
 			$row[]='<div class="danger-toggle-button">
						<input type="checkbox" class="toggle status-'.(int)$row1['Instrument_active'].' "  '.$status.'  id="'.$sTable.'-'.$row1['Instrument_id'].'" onChange="globalStatus(this)" />
					</div>';
			}
			else
			{
				$row[]='N.A.';	
			}
			if($row1['Instrument_status']==1)
			{
						$row[] =  '<a href="'.APPLICATION_URL.'/class/newinstrument/Instrument_id/'.$row1[$sIndexColumn].'/status/1" class="btn btn-xs purple"> 
				 <i class="fa fa-edit"></i>
				 </a> ';
				 if($row1['students']==0 || $row1['teachers']==0)
				 {
						 $row[]='<a onclick="removeclass('.$row1[$sIndexColumn].',0)" class="btn btn-xs purple delete_class">
				
				 <i class="fa fa-trash-o"></i>
				</a>'; 
					}
				 else
				 {
				 $row[]='<a onclick="removeclass('.$row1[$sIndexColumn].',1)" class="btn btn-xs purple delete_class">
				
				 <i class="fa fa-trash-o"></i>
				</a>';
				 }	
			}
			else
			{
				$row[]='N.A.';
				$row[]='N.A.';
			}
			
			
 			$output['aaData'][] = $row;
			$j++;
		$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
	
	/* Remove Class */
	public function removeclassAction()
	{
		global $objSession;
 		$this->_helper->layout->disableLayout();
		$this->_helper->viewRenderer->setNoRender(true);
 		if ($this->getRequest()->isPost()) {
			$formData = $this->getRequest()->getPost();
			if(isset($formData['select_classes']) and count($formData['select_classes'])){
				 foreach($formData['select_classes'] as $key=>$values){
   					 $this->modelStatic->Super_Delete("Classes","class_id='".$values."'");
 					
				 }
 				$objSession->successMsg = " Classes Deleted Successfully ";
 			}else{
				$objSession->errorMsg = "Invalid Request to Delete Classes ";
			}
 			$this->_redirect('class');
		} 
		
		/*global $objSession ; 
		$class_id=$this->getRequest()->getParam("idval");	
		$this->modelStatic->Super_Delete("Classes","class_id='".$class_id."'");
		$objSession->successMsg = "Class has been removed Successfully";				
		$this->_redirect('class');*/
		
	}
	/* Remove Instruments */
	public function removeinstrumentAction()
	{
		global $objSession ; 
		$Instrument_id=$this->getRequest()->getParam("idval");	
		$this->modelStatic->Super_Delete("Instruments","Instrument_id='".$Instrument_id."'");
		$objSession->successMsg = "Instrument has been removed Successfully";				
		$this->_redirect('class/instruments');
		
	}
	
	/* Add or Update Class */
	public function newclassAction()
	{
		
			global $objSession ; 
			/* Check if user type is not school */
			if($this->view->user->user_type!='school')
			{
				if($this->view->user->user_type=='schoolsubadmin')
				{
					/* Check if School Admin */
					$permissions_data=array();
					/* Get Permissio Data */
					$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='5'","fetch");	
					
					if(empty($paremission_data))
					{
						/* If Permission Data is empty */
						$this->redirect("profile/dashboard");	
					}
				}	
				else
				{
					/* If user is not admin or subadmin */
					$this->redirect('index');	
				}
			}
			$class_id=$this->getRequest()->getParam("class_id");
			$all_insturments=array();
			$all_insturments=$this->modelStatic->Super_Get("Instruments","((Instrument_userid='".$this->view->user->user_id."')) and Instrument_active='1'");
			$form = new Application_Form_SchoolForm();
			$class_data=array();
			
			$studentform = new Application_Form_SchoolForm();
			$studentform->allstudents();
			
			$teacherform = new Application_Form_SchoolForm();
			$teacherform->allteachers();
		
			
			$class_data=array();
			if(isset($class_id) && !empty($class_id))
			{
				$this->view->pageHeading = 'Edit Class';
				$this->view->pageHeadingshow = '<i class="fa fa-building"></i>  Edit Class';	
				$form->newclass($class_id);			
				/* Fatch Class Data */
				$class_data=$this->modelStatic->Super_Get("Classes","class_id='".$class_id."'");
				$days_data=array();
				$days_data=explode(" and ",$class_data['class_days']);
				$class_data['class_days']=$days_data;		
				$class_data['class_date_type']=$class_data['class_date_type'];
				if($class_data['class_date_type']==0)
				{
					/* Ongoing Class */	
					$class_data['class_date']	="";
					
				}
				else if($class_data['class_date_type']==1)
				{
					/* Ongoing Class and time */	
					$class_data['date_timepicker_start']=$class_data['class_start_time'];
					$class_data['date_timepicker_end']=$class_data['class_end_time'];
				}
				else if($class_data['class_date_type']==2)
				{
					/* Class Specific Date Range Picker */	
					$class_data['class_date_only']=$class_data['class_date'];
				}
				else
				{
					
					$datexplode=explode("-",$class_data['class_date']);
					$startexplode=explode(" ",trim($datexplode[0]));
					$endexplode=explode(" ",trim($datexplode[1]));
					/* Class Specific Date and Time Range Picker */
					$class_data['class_date']=$startexplode[0]."-".$endexplode[0]."  "."(".$startexplode[1]." ".$startexplode[2]."-".$endexplode[1]." ".$endexplode[2].")";
					$this->view->daterangepicker_start=$startexplode[0]." ".$startexplode[1]." ".$startexplode[2];
					
					$this->view->daterangepicker_end=$endexplode[0]." ".$endexplode[1]." ".$endexplode[2];
					$timeexplode=explode(":",$startexplode[1]);
					$timeexplode1=explode(":",$endexplode[1]);
					$this->view->timeexplode1=$timeexplode[0];
					$this->view->timeexplode2=$timeexplode[1];
					$this->view->timeexplode3=$startexplode[2];
					$this->view->timeexplode4=$timeexplode1[0];
					$this->view->timeexplode5=$timeexplode1[1];
					$this->view->timeexplode6=$endexplode[2];
					$this->view->timeexplode7=$startexplode[0];
					$this->view->timeexplode8=$endexplode[0];
					
					
				}
				
				/* Populate Form Data */
				$form->populate($class_data);
				
				/* Get Students data */
				$student_data=$this->modelStatic->Super_Get("student_class","student_class_classid='".$class_id."'","fetchAll");
				$student_options=array();
				foreach($student_data as $k=>$v)
				{
					$student_options[$k]=$v['student_class_studentid'];
				}
				$student_data_arr=array('instrument_student'=>$student_options);
				
				$studentform->populate($student_data_arr);
				/* End Populate Student data */
				/* Get Teacher Data */
				$teacher_data=$this->modelStatic->Super_Get("teacher_classes","teacher_class_classid='".$class_id."'","fetchAll");
				$teacher_options=array();
				foreach($teacher_data as $k=>$v)
				{
					$teacher_options[$k]=$v['teacher_class_userid'];
				}
				$teacher_data_arr=array('instrument_teachers'=>$teacher_options);
				$teacherform->populate($teacher_data_arr);
				
			}
			else
			{
				$this->view->pageHeading = 'Add Class';
				$this->view->pageHeadingshow = '<i class="fa fa-building"></i>  Add Class';
				$form->newclass();
			}
			$this->view->form=$form;
			$this->view->studentform=$studentform;
			$this->view->teacherform=$teacherform;
			$this->view->class_data=$class_data;
			if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				//prd($posted_data);
				$save_template=0;
				if(isset($posted_data['bttnsubmittemplate']) && $posted_data['bttnsubmittemplate']="Save As Template")
				{
					$save_template=1;
				}
				
				if ($form->isValid($posted_data))
				{ // Form Valid
					/* Get Form Data */
					$data_insert=$form->getValues();
					$class_type=$data_insert['class_type'];
					unset($data_insert['class_type']);
				/*	unset($data_insert['class_date_type']);*/
					$class_template_name=$data_insert['class_template_name'];
					unset($data_insert['class_template_name']);
					if($data_insert['class_date_type']==0)
					{
						/* Ongoindg Class */	
						$data_insert['class_date']="";
					}
					else if($data_insert['class_date_type']==1)
					{
						/* Ongoing Class _Include Time */	
						$data_insert['class_date']="";
						$data_insert['class_start_time']=$data_insert['date_timepicker_start'];
						$data_insert['class_end_time']=$data_insert['date_timepicker_end'];
					}
					else if($data_insert['class_date_type']==2)
					{
						/* Select Specific Date Range */
						
						$class_time_expode=explode("-",$data_insert['class_date_only']);
						$data_insert['class_start_date']=gmdate('Y-m-d',strtotime($class_time_expode[0]));
						$data_insert['class_end_date']=gmdate('Y-m-d',strtotime($class_time_expode[1]));
						$data_insert['class_date']=$data_insert['class_date_only'];
						
					}
					else
					{
						/* Select Specific Date Time Range */
						$clsaa_date_time=explode("(",trim($data_insert['class_date']));
						$dates=explode("-",trim($clsaa_date_time[0]));
						$bracketexplode=explode(")",trim($clsaa_date_time[1]));
						$times=explode("-",trim($bracketexplode[0]));
						$data_insert['class_start_date']=gmdate('Y-m-d',strtotime($dates[0]));
						$data_insert['class_start_time']=$times[0];
						$data_insert['class_end_date']=gmdate('Y-m-d',strtotime($dates[1]));
						$data_insert['class_end_time']=$times[1];
						$data_insert['class_date']=$dates[0]." ".$times[0]."-".$dates[1]." ".$times[1];
					}
					unset($data_insert['class_date_only']);
					unset($data_insert['date_timepicker_start']);
					unset($data_insert['date_timepicker_end']);
					$data_insert['class_days']=implode(" and ",$data_insert['class_days']);
					/* Check condtion For Edit or Add */
					if(isset($class_data) && !empty($class_data))
					{
					/* Edit Class Data */
						$class_time_expode=explode("-",$data_insert['class_date']);
						$this->modelStatic->Super_Insert("Classes",$data_insert,'class_id="'.$class_id.'"');
						$objSession->successMsg = "Class has been updated Successfully";
					}
					else
					{
						/* Add Class Data */
						/* Get School ID */
						$school_id='';
						if($this->view->user->user_type=='school')
						{
							$school_id=$this->view->user->user_id;	
						}
						else
						{
							$school_id=$this->view->user->user_school_id;
						}
						$data_insert['class_template']=$save_template;
						$data_insert['class_school_id']=$school_id;
						$data_insert['class_insertdate']=gmdate('Y-m-d H:i');
						$data_insert['class_insertid']=$this->view->user->user_id;
						$is_insert=$this->modelStatic->Super_Insert("Classes",$data_insert);
						$class_id=$is_insert->inserted_id;
						$objSession->successMsg = "Class has been added Successfully";
					}
											
				$this->_redirect('class');
			 }
					
			}
			
	}
	
	public function addstudentsAction()
	{
		global $objSession ; 
		$studentform = new Application_Form_SchoolForm();
		$studentform->allstudents();
		$instrument_id=$this->getRequest()->getParam('instrument_id');
		$this->view->studentform=$studentform;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				
				if ($studentform->isValid($posted_data))
				{ 
					$this->modelStatic->Super_Delete("student_instrument","student_instrument_insid='".$instrument_id."'");
					$data_insert=$studentform->getValues();
				
					foreach($data_insert['instrument_student'] as $k=>$v)
					{
						
						$data_arr=array();
						$data_arr=array('student_instrument_studentid'=>$v,
										'student_instrument_insid'=>$instrument_id,
										'student_instrument_date'=>gmdate('Y-m-d H:i:s'),
						);	
						$kk=$this->modelStatic->Super_Insert("student_instrument",$data_arr);
						
					}
					
				}
			}
			
		$objSession->successMsg="Student has been added successfully";
		$this->redirect("class/newinstrument/Instrument_id/".$instrument_id);
		
	}
	
	public function addstudentclassAction()
	{
		global $objSession ; 
		$studentform = new Application_Form_SchoolForm();
		$studentform->allstudents();
		$class_id=$this->getRequest()->getParam('class_id');
		$this->view->studentform=$studentform;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				
				if ($studentform->isValid($posted_data))
				{ 
					$this->modelStatic->Super_Delete("student_class","student_class_classid='".$class_id."'");
					$data_insert=$studentform->getValues();
				
					foreach($data_insert['instrument_student'] as $k=>$v)
					{
						
						$data_arr=array();
						$data_arr=array('student_class_studentid'=>$v,
										'student_class_classid'=>$class_id,
										'student_class_date'=>gmdate('Y-m-d H:i:s'),
						);	
						$kk=$this->modelStatic->Super_Insert("student_class",$data_arr);
						
					}
					
				}
			}
			
		$objSession->successMsg="Student has been added successfully";
		$this->redirect("class/newclass/class_id/".$class_id);
		
	}
		
	public function addteachersclassAction()
	{
		global $objSession ; 
		$teacherform = new Application_Form_SchoolForm();
		$teacherform->allteachers();
		$class_id=$this->getRequest()->getParam('class_id');
		$this->view->teacherform=$teacherform;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				
				if ($teacherform->isValid($posted_data))
				{ 
					$this->modelStatic->Super_Delete("teacher_classes","teacher_class_classid='".$class_id."'");
					$data_insert=$teacherform->getValues();
				
					foreach($data_insert['instrument_teachers'] as $k=>$v)
					{
						
						$data_arr=array();
						$data_arr=array('teacher_class_userid'=>$v,
										'teacher_class_classid'=>$class_id,
										'teacher_class_date'=>gmdate('Y-m-d H:i:s'),
						);	
						$kk=$this->modelStatic->Super_Insert("teacher_classes",$data_arr);
						
					}
					
				}
			}
			
		$objSession->successMsg="Teacher has been added successfully";
		$this->redirect("class/newclass/class_id/".$class_id);
		
	}
	public function addteachersAction()
	{
		global $objSession ; 
		$teacherform = new Application_Form_SchoolForm();
		$teacherform->allteachers();
		$instrument_id=$this->getRequest()->getParam('instrument_id');
		$this->view->teacherform=$teacherform;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				
				if ($teacherform->isValid($posted_data))
				{ 
					$this->modelStatic->Super_Delete("teacher_insruments","teacher_insrument_instid='".$instrument_id."'");
					$data_insert=$teacherform->getValues();
				
					foreach($data_insert['instrument_teachers'] as $k=>$v)
					{
						
						$data_arr=array();
						$data_arr=array('teacher_insrument_userid'=>$v,
										'teacher_insrument_instid'=>$instrument_id,
										'teacher_insrument_date'=>gmdate('Y-m-d H:i:s'),
						);	
						$kk=$this->modelStatic->Super_Insert("teacher_insruments",$data_arr);
						
					}
					
				}
			}
			
		$objSession->successMsg="Teacher has been added successfully";
		$this->redirect("class/newinstrument/Instrument_id/".$instrument_id);
		
	}
	
	public function instrumentnameAction()
	{
		$this->_helper->layout->disableLayout();
		$instrument_id=$this->getRequest()->getParam("instrument_id");
		$Instrument_name=$this->getRequest()->getParam("Instrument_name");
		$where='Instrument_name="'.$Instrument_name.'" and ((Instrument_schoolid="'.$this->view->user->user_id.'" or Instrument_schoolid="'.$this->view->user->user_school_id.'" ) )';
		if(isset($instrument_id) && $instrument_id!=0)
		{
			
			$where.=' and Instrument_id!="'.$instrument_id.'"';	
		}
		
		$instrument_data=$this->modelStatic->Super_Get("Instruments",$where,"fetch");
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
	/* Add or Update Instrument */
	public function newinstrumentAction()
	{
		global $objSession ; 
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='6'","fetch");	
				
				if(empty($paremission_data))
				{
					/* If Permission Data is empty */
					$this->redirect("profile/dashboard");	
				}
			}	
			else
			{
				/* If user is not admin or subadmin */
				$this->redirect('index');	
			}
		}	
		$Instrument_id=$this->getRequest()->getParam("Instrument_id");
		$status=$this->getRequest()->getParam("status");
		$form = new Application_Form_SchoolForm();
		$form->newinstrument();
		
		$studentform = new Application_Form_SchoolForm();
		$studentform->allstudents();
		
		$teacherform = new Application_Form_SchoolForm();
		$teacherform->allteachers();
		
		
		$instrument_data=array();
		if(isset($Instrument_id) && !empty($Instrument_id))
		{
				$this->view->pageHeading = 'Edit Instrument';
				$this->view->pageHeadingshow = '<i class="fa fa-tags"></i>  Edit Instrument';	
				/* Fatch Instrument Data */
				$instrument_data=$this->modelStatic->Super_Get("Instruments","Instrument_id='".$Instrument_id."'");
				/* Populate Form Data */
				$form->populate($instrument_data);
				/* Get Students data */
				$student_data=$this->modelStatic->Super_Get("student_instrument","student_instrument_insid='".$Instrument_id."'","fetchAll");
				$student_options=array();
				foreach($student_data as $k=>$v)
				{
					$student_options[$k]=$v['student_instrument_studentid'];
				}
				$student_data_arr=array('instrument_student'=>$student_options);
				
				$studentform->populate($student_data_arr);
				/* End Populate Student data */
				/* Get Teacher Data */
				$teacher_data=$this->modelStatic->Super_Get("teacher_insruments","teacher_insrument_instid='".$Instrument_id."'","fetchAll");
				$teacher_options=array();
				foreach($teacher_data as $k=>$v)
				{
					$teacher_options[$k]=$v['teacher_insrument_userid'];
				}
				$teacher_data_arr=array('instrument_teachers'=>$teacher_options);
				
				$teacherform->populate($teacher_data_arr);
		}
		else
		{
				$this->view->pageHeading = 'Add Instrument';
				$this->view->pageHeadingshow = '<i class="fa fa-tags"></i>  Add Instrument';	
		}
		$this->view->instrument_data=$instrument_data;
		$this->view->studentform=$studentform;
		$this->view->teacherform=$teacherform;
		//prd($instrument_data);
		$this->view->form=$form;
		if ($this->getRequest()->isPost())
		{ // Post Form Data
 			$posted_data  = $this->getRequest()->getPost();
			if ($form->isValid($this->getRequest()->getPost()))
			{ // Form Valid
				/* Get Form Data */
				$data_insert=$form->getValues();
				/* Check condtion For Edit or Add */
				if(isset($instrument_data) && !empty($instrument_data))
				{
					/* Edit Instrument Data */
					$this->modelStatic->Super_Insert("Instruments",$data_insert,'Instrument_id="'.$Instrument_id.'"');
					$objSession->successMsg = "Instrument has been updated Successfully";
				}
				else
				{
						/* Add Instrument Data */
						/* Get School ID */
						$school_id='';
						if($this->view->user->user_type=='school')
						{
							$school_id=$this->view->user->user_id;	
						}
						else
						{
							$school_id=$this->view->user->user_school_id;
						}
						$data_insert['Instrument_date']=gmdate('Y-m-d H:i');
						$data_insert['Instrument_userid']=$this->view->user->user_id;
						$data_insert['Instrument_schoolid']=$school_id;
						$this->modelStatic->Super_Insert("Instruments",$data_insert);
						$objSession->successMsg = "Instrument has been added Successfully";
				}
				
					$this->_redirect('class/instruments');
				
			
			}
		}
	}
 	
}