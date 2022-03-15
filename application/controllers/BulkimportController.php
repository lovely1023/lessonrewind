<?php
class BulkimportController extends Zend_Controller_Action
{
	public function init(){	
 		$this->modelStatic = new Application_Model_Static();
		
   	}
	
	
 	public function indexAction(){	
 		global $objSession ; 
		$this->view->pageHeading = "Bulk Importing";
		$this->view->pageHeadingshow = '<i class="fa fa-share-square-o"></i>  Bulk Importing';
		$teacher_data=array();
		$teacher_data=$this->modelStatic->Super_Get("users","user_insertby='".$this->view->user->user_id."' and user_type='teacher'","fetchAll");
		$this->view->teacher_data=$teacher_data;
		/* Check if user type is not school */
		if($this->view->user->user_type!='school')
		{
			if($this->view->user->user_type=='schoolsubadmin')
			{
				/* Check if School Admin */
				$permissions_data=array();
				/* Get Permissio Data */
				$paremission_data=	$this->modelStatic->Super_Get("admin_permissions","admin_permissions_adminid='".$this->view->user->user_id."' and admin_permission_type='4'","fetch");		if(empty($paremission_data))
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
	
	}
	
	
	public function importteacherAction()
	{
		global $objSession ; 
		$this->view->pageHeading = "Import Teacher";
		$this->view->pageHeadingshow = '<i class="fa fa-share-square-o"></i>  Import Teacher';
		$form = new Application_Form_SchoolForm();
		$form->importdata(0);
		$this->view->form=$form;
		if($this->getRequest()->isPost())
		{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				
				if ($form->isValid($posted_data))
				{ // Form Valid
					/* Get Form Data */
					$import_file_name=time().'_'.$_FILES['bulkimport_file']['name'];
					$form->bulkimport_file->addFilter('Rename',array('target' =>IMPORT_ATTACH.'/'.$import_file_name));
    				$form->bulkimport_file->receive();
					$data_insert=$form->getValues();
					$school_id=$this->view->user->user_id;
					$data_insert['bulkimport_userid']=$this->view->user->user_id;
					if($this->view->user->user_type=='school')
					{
						$data_insert['bulkimport_schoolid']	=$this->view->user->user_id;
					}
					else
					{
						$data_insert['bulkimport_schoolid']=$this->view->user->user_school_id;
						$school_id=$this->view->user->user_school_id;
					}
					$data_insert['bulkimport_addeddate']=gmdate("Y-m-d H:i:s");
					$data_insert['bulkimport_status']=0;
					$ss=$this->modelStatic->Super_Insert("bulkimport",$data_insert);
					$bilkimport_id=$ss->inserted_id;
					$bulkimport_data=array();
					$bulkimport_data=$this->modelStatic->Super_Get("bulkimport","bulkimport_id='".$bilkimport_id."'","fetch");
					/* End Insert Data Of Bulk Import */
					
					/* Start Importing Data */
					$csv_file = IMPORT_ATTACH.'/'.$import_file_name ;// Name of your CSV file
					$ext = pathinfo($csv_file, PATHINFO_EXTENSION);
					$merge_arr=array();
					/* Read data from file */
					if($ext=='csv')
					{ 
						/* Here We have to read csv file */	
						$csvfile = fopen($csv_file, 'r');
					
						$new_array=array();
						$i = 0;$err = '';$count = 0;
						{
							
						$csv_array = array();
						//Import uploaded file to Database
					
						$make = fgetcsv($csvfile, 1000, ",");
						$myarr = array();	
						$k=0;
						
						
						
						while(($data = fgetcsv($csvfile, 1000, ",")) !== FALSE)
						 {
								if($k==0)
								{
								
									if((isset($make[0]) && trim($make[0])=='First Name') && (isset($make[1]) && trim($make[1])=='Last Name') && (isset($make[2]) && trim($make[2])=='Email Address') && (isset($make[3]) && trim($make[3])=='Instrument(s)'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importteacher");	
									}
								}
								$merge_arr[$k]=$data;
								$k++;
					  	}
							}
					fclose($csvfile);
					
					}
					else if($ext=='xls')
					{
						
						/* Here We can read xls and xslx file */
						$excel = new PhpExcelReader;      // creates object instance of the class
						$excel->read($csv_file);   // reads and stores the excel file data
						// Test to see the excel data stored in $sheets property
						$xls_array=array();
						$xls_array=$excel->sheets;
						$b=0;
					
						foreach($xls_array[0]['cells'] as $k=>$v)
						{
							
							if($k==1)
								{
								
									if((isset($v[1]) && trim($v[1])=='First Name') && (isset($v[2]) && trim($v[2])=='Last Name') && (isset($v[3]) && trim($v[3])=='Email Address') && (isset($v[4]) && trim($v[4])=='Instrument(s)'))
									{}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importteacher");	
									}
								}
							if($k>1)
							{
								$merge_arr[$b]	=array_values($v);
								$b++;
							}	
						}
					}
					else
					{
						
							include ROOT_PATH.'/private/simplexlsx.class.php';
							$getWorksheetName = array();
							$xlsx = new SimpleXLSX($csv_file);
							$getWorksheetName = $xlsx->getWorksheetName();
							$dataXls=$xlsx->rows(1);
							
							foreach($dataXls as $k=>$v)
							{
									if($k==0)
								{
								
									if((isset($v[0]) && trim($v[0])=='First Name') && (isset($v[1]) && trim($v[1])=='Last Name') && (isset($v[2]) && trim($v[2])=='Email Address') && (isset($v[3]) && trim($v[3])=='Instrument(s)'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importteacher");	
									}
								}
								if($k>0)
								{
									$merge_arr[$k-1]=$v;
								}
				
							}
						
							
					}
					
				
					/* Read data from file End */
					/* Import data in data base*/
					$error_str='';
			
					foreach($merge_arr as $kn=>$data)
					{
						
							if((isset($data[0]) && $data[0]!='') && (isset($data[1]) && $data[1]!='' ) && (isset($data[2]) && $data[2]!='' ) )
								{
									/* here data is not empty */
									
									$first_name_arr=$this->modelStatic->Super_Get("users",'user_first_name="'.trim($data[0]).'" and user_last_name="'.trim($data[1]).'" and user_type="teacher" and user_school_id="'.$school_id.'"',"fetch");
									/* -	If Teacher First Name / Last Name combination already exists do not import record */
									
									if(empty($first_name_arr))
									{
										$check_email=$this->modelStatic->Super_Get("users","user_email='".trim($data[2])."'","fetch");	
										/* -	If email Address already exists in system do not import record */
										if(empty($check_email))
										{
											/* Email is valid or not */
											if(filter_var($data[2], FILTER_VALIDATE_EMAIL))
											{
												$data_user=array();
												
												$data_user=array('user_first_name'=>trim($data[0]),
																		    'user_last_name'=>trim($data[1]),
																			'user_email'=>trim($data[2]),
																			'user_created'=>gmdate("Y-m-d H:i:s"),
																			'user_school_id'=>$school_id,
																			'user_type'=>'teacher',
																			'user_verification_mail'=>'0',
																			'user_insertby'=>$this->view->user->user_id,
																			'user_status'=>1
																			
															);
												$user_name='';
												$user_name=$this->modelStatic->insertusername('teacher');
												$data_user['user_username']=$user_name;
												$jj=$this->modelStatic->Super_Insert("users",$data_user);
												
												if($jj->success)
												{
													$data_to_update=array();
															$reset_password_key = md5($jj->inserted_id."!@#$%^$%&(*_+".time());
															$data_to_update = array("user_reset_status"=>"1","pass_resetkey"=>$reset_password_key);
															$this->modelStatic->Super_Insert("users",$data_to_update,'user_id="'.$jj->inserted_id.'"');
														
														/* Success */
														if(isset($data[3]) && $data[3]!='')
														{
															/* Instrument are not empty */
															$explode_arr='';
															$explode_arr=explode(';',$data[3]);	
															if(is_array($explode_arr))
															{
																foreach($explode_arr as $k=>$v)
																{
																		$check_ins=$this->modelStatic->Super_Get("Instruments","Instrument_name='".trim($v)."' and Instrument_schoolid='".$school_id."'","fetch");
																		if(!empty($check_ins))
																		{
																			$check_teacher_ins=array();
																			$check_teacher_ins=$this->modelStatic->Super_Get("teacher_insruments","teacher_insrument_instid='".$check_ins['Instrument_id']."' and teacher_insrument_userid='".$jj->inserted_id."'","fecth");
																			if(empty($check_teacher_ins))
																			{
																				$teacher_ins=array();
																				$teacher_ins=array('teacher_insrument_instid'=>$check_ins['Instrument_id'],
																								'teacher_insrument_userid'=>$jj->inserted_id,
																								'teacher_insrument_date'=>gmdate("Y-m-d H:i:s")
																				);
																				$this->modelStatic->Super_Insert("teacher_insruments",$teacher_ins);
																			}
																			else
																			{
																				$error_str.="Instrument '".$v."' in line no. ".($kn+2)." is already added for this teacher. \r\n";
																			}
																		}
																		else
																		{
																			/* Error Log */
																			if($v!='')
																			{
																			$error_str.="Instrument '".$v."' in line no. ".($kn+2)." does not exists. \r\n";
																			}
																		}
																		
																}	
															}
															else
															{
															
															if($explode_arr!='')
															{
																$check_this_ins=$this->modelStatic->Super_Get("Instruments","Instrument_name='".trim($explode_arr)."' and Instrument_schoolid='".$school_id."' ","fetch");
																if(!empty($check_ins))
																		{
																			$check_teacher_ins=array();
																			$check_teacher_ins=$this->modelStatic->Super_Get("teacher_insruments","teacher_insrument_instid='".$check_this_ins['Instrument_id']."' and teacher_insrument_userid='".$jj->inserted_id."'","fecth");
																			if(empty($check_teacher_ins))
																			{
																			$teacher_ins=array();
																			$teacher_ins=array('teacher_insrument_instid'=>$check_this_ins['Instrument_id'],
																								'teacher_insrument_userid'=>$jj->inserted_id,
																								'teacher_insrument_date'=>gmdate("Y-m-d H:i:s")
																			);
																			$this->modelStatic->Super_Insert("teacher_insruments",$teacher_ins);
																			}
																			else
																			{
																					$error_str.="Instrument '".$explode_arr."' in line no. ".($kn+2)." is already added for this teacher. \r\n";
																			}
																		}
																		else
																		{
																			/* Error Log */
														$error_str.="Instrument '".trim($explode_arr)."' in line no. ".($kn+2)." ' does not exists. \r\n";
																		}
															}
															
															}
														}
												}
												else
												{
													/* Insert Error Log */	
													$error_str.="User given in Line No. ".($kn+2)." are not added. \r\n";
												}
												 
											}	
											else
											{
												/* Insert Error Log */	
												$error_str.="Email Address '".$data[2]."' of Line No. ".($kn+2)." is not valid. \r\n";
											}
										}
										else
										{
											   $error_str.="Email Address '".$data[2]."' of Line No. '".($kn+2)."' is already exists. \r\n";
										}
									}	
									else
									{
										/* Insert Error Log */	
							  	      $error_str.="Teacher Name '".$data[0]." ".$data[1]."' in Line No. '".($kn+2)."' is already exists. \r\n";
									}
								}
								else
								{
									/* Insert in Error Log */	
									$error_str.="Either 'First Name','Last Name' or 'Email Address' is empty in Line No. '".($kn+2)."' \r\n";
								}
					}
					
					
				/* Import data in data base End */
				if($error_str!='')
				{
					/* Insert Error Log */
						$error_str=rtrim($error_str,',');
						$errorlog=time().'.txt';
						$fd = fopen (ROOT_PATH.'/public/resources/import_attach/'.$errorlog, "w");
						$out = fwrite($fd, ($error_str));
						fclose($fd);
					/* Insert Error Log End */
					$update_bulk=array('bulkimport_errorlog'=>$errorlog);
					$this->modelStatic->Super_Insert("bulkimport",$update_bulk,'bulkimport_id="'.$bilkimport_id.'"');
					
				}
					if(empty($merge_arr))
					{
						$objSession->errorMsg="Data is not imported. Please try again";	
					}
					else
					{
						$objSession->successMsg="Data has been added successfully";	
					}
					
					$this->redirect("bulkimport/importteacher");
				//prd($new_array);
				/* End Importing data */
				//	prd($data_insert);
				}
			}
	}
	
	public function importstudentAction()
	{
		global $objSession ; 
		$this->view->pageHeading = "Import Student";
		$this->view->pageHeadingshow = '<i class="fa fa-share-square-o"></i>  Import Student';
		$form = new Application_Form_SchoolForm();
		$form->importdata(0);
		$this->view->form=$form;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // Form Valid
					/* Get Form Data */
					$import_file_name=time().'_'.$_FILES['bulkimport_file']['name'];
					$form->bulkimport_file->addFilter('Rename',array('target' =>IMPORT_ATTACH.'/'.$import_file_name));
    				$form->bulkimport_file->receive();
					$data_insert=$form->getValues();
					$school_id=$this->view->user->user_id;
					$data_insert['bulkimport_userid']=$this->view->user->user_id;
					if($this->view->user->user_type=='school')
					{
						$data_insert['bulkimport_schoolid']	=$this->view->user->user_id;
					}
					else
					{
						$data_insert['bulkimport_schoolid']=$this->view->user->user_school_id;
						$school_id=$this->view->user->user_school_id;
					}
					$data_insert['bulkimport_addeddate']=gmdate("Y-m-d H:i:s");
					$data_insert['bulkimport_status']=1;
					$ss=$this->modelStatic->Super_Insert("bulkimport",$data_insert);
					$bilkimport_id=$ss->inserted_id;
					$bulkimport_data=array();
					$bulkimport_data=$this->modelStatic->Super_Get("bulkimport","bulkimport_id='".$bilkimport_id."'","fetch");
					/* End Insert Data Of Bulk Import */
					
					/* Start Importing Data */
					$csv_file = IMPORT_ATTACH.'/'.$import_file_name ;// Name of your CSV file
					$csvfile = fopen($csv_file, 'r');
					$ext = pathinfo($csv_file, PATHINFO_EXTENSION);
					$merge_arr=array();
					/* Read data from file */
					if($ext=='csv')
					{
						/* Here We have to read csv file */	
						$csvfile = fopen($csv_file, 'r');
						$new_array=array();
						$i = 0;$err = '';$count = 0;
						{
							
						$csv_array = array();
						//Import uploaded file to Database
					
						$make = fgetcsv($csvfile, 1000, ",");
						$myarr = array();	
						$k=0;
						while(($data = fgetcsv($csvfile, 1000, ",")) !== FALSE)
						 {
								if($k==0)
								{
								
									if((isset($make[0]) && trim($make[0])=='Student First Name') && (isset($make[1]) && trim($make[1])=='Student Last Name') && (isset($make[2]) && trim($make[2])=='Family Contact First Name') && (isset($make[3]) && trim($make[3])=='Family Contact Last Name') && (isset($make[4]) && trim($make[4])=='Email Address'))
									{}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importstudent");	
									}
								}
							
								$merge_arr[$k]=$data;
								$k++;
							
								
								$i++;	
						}
					fclose($csvfile);
					
							}
					}
					else if($ext=='xls')
					{
						/* Here We can read xls and xslx file */
						$excel = new PhpExcelReader;      // creates object instance of the class
						$excel->read($csv_file);   // reads and stores the excel file data
						// Test to see the excel data stored in $sheets property
						$xls_array=array();
						$xls_array=$excel->sheets;
						$b=0;
						
						foreach($xls_array[0]['cells'] as $k=>$v)
						{
							if($k==1)
								{
								
									if((isset($v[1]) && trim($v[1])=='Student First Name') && (isset($v[2]) && trim($v[2])=='Student Last Name') && (isset($v[3]) && trim($v[3])=='Family Contact First Name') && (isset($v[4]) && trim($v[4])=='Family Contact Last Name')  && (isset($v[4]) && trim($v[4])=='Email Address'))
									{}
									else
									{	$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importstudent");	
									}
								}
							if($k>1)
							{
								$merge_arr[$b]	=array_values($v);
								$b++;
							}	
						}
						
					
					}
					else
					{
						
							include ROOT_PATH.'/private/simplexlsx.class.php';
							$getWorksheetName = array();
							$xlsx = new SimpleXLSX($csv_file);
							$getWorksheetName = $xlsx->getWorksheetName();
							$dataXls=$xlsx->rows(1);
							
							foreach($dataXls as $k=>$v)
							{
									if($k==0)
								{
								
									if((isset($v[0]) && trim($v[0])=='Student First Name') && (isset($v[1]) && trim($v[1])=='Student Last Name') && (isset($v[2]) && trim($v[2])=='Family Contact First Name') && (isset($v[3]) && trim($v[3])=='Family Contact Last Name') && (isset($v[4]) && trim($v[4])=='Email Address'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importteacher");	
									}
								}
								if($k>0)
								{
									$merge_arr[$k-1]=$v;
								}
				
							}
						
							
					}
					/* Read data from file End */
					$error_str='';
					foreach($merge_arr as $kn=>$data)
					{
									if((isset($data[2]) && $data[2]!='') && (isset($data[3]) && $data[3]!='' ))
									{
											
											if((isset($data[4]) && $data[4]!='') or filter_var($data[4], FILTER_VALIDATE_EMAIL))
											{
												
													/* Check For Step 1 */
													$check_family_name=$this->modelStatic->Super_Get("users","user_type='family' and user_first_name='".trim($data[2])."' and user_last_name='".trim($data[3])."' and user_school_id='".$school_id."'","fetch");
													$family_id='';
													$erro_str_html='';
													
													if(empty($check_family_name))
													{
														$check_email_unique=array();
														$check_email_unique=$this->modelStatic->Super_Get("users","user_email='".$data[4]."'","fetch");
														if(empty($check_email_unique))
														{
															/* Email is also unique so we can create family contact */	
															$data_insert_family=array();
															
															$hh='';
															$user_name='';
															$user_name=$data[4];
																$password='';
																$password=randomPassword();
																//$password=12345;
															$data_insert_family=array('user_first_name'=>trim($data[2]),
    																					'user_username'=>$user_name,
																						'user_email'=>$data[4],
																						'user_password'=>md5($password),
																						'user_password_text'=>$password,
																						'user_last_name'=>trim($data[3]),
																						'user_created'=>gmdate("Y-m-d H:i:s"),
																						'user_school_id'=>$school_id,
																						'user_type'=>'family',
																						'user_verification_mail'=>'0',
																						'user_insertby'=>$this->view->user->user_id,
																						'user_status'=>1,
																						
																						);
															$hh=$this->modelStatic->Super_Insert("users",$data_insert_family);
															$family_id=$hh->inserted_id;
														}
														else
														{
																$family_id='';
																$erro_str_html.="Line No. ".($kn+2)." Student '".$data[0]." ".$data[1]."' Family Email address already exists in database with a different Family Contact. Only one contact is allowed for each email address. Import failed!.  \r\n";	
														}
													/* Family Is not exists of this name */	
															
															
												
														}
													else
													{
														/* 	Family Is already exists of this name */
														$check_family_withname=array();
														$check_family_withname=$this->modelStatic->Super_Get("users","user_type='family' and user_first_name='".trim($data[2])."' and user_last_name='".trim($data[3])."' and user_email='".trim($data[4])."' and user_school_id='".$school_id."'","fetch");
														
														if(!empty($check_family_withname))
														{
															$family_id=$check_family_withname['user_id'];	
															
														}
														else
														{
															/* Message Error */	
															$check_unique_email=array();
															$check_unique_email=$this->modelStatic->Super_Get("users","user_email='".$data[4]."'","fetch");
															if(empty($check_unique_email))
															{
																	$data_insert_family=array();
															
																	$hh='';
																	$user_name='';
																	$user_name=$data[4];
																$password='';
																$password=randomPassword();
																//$password=12345;
																$data_insert_family=array('user_first_name'=>trim($data[2]),
																						'user_last_name'=>trim($data[3]),
																						'user_username'=>$user_name,
																						'user_email'=>$data[4],
																						'user_password'=>md5($password),
																						'user_password_text'=>$password,
																						'user_created'=>gmdate("Y-m-d H:i:s"),
																						'user_school_id'=>$school_id,
																						'user_type'=>'family',
																						'user_verification_mail'=>'0',
																						'user_insertby'=>$this->view->user->user_id,
																						'user_status'=>1
																						);
																$hh=$this->modelStatic->Super_Insert("users",$data_insert_family);
																$family_id=$hh->inserted_id;
															}
															else
															{
																	/* Error alrady exists email */
																	$erro_str_html.="Line No. ".($kn+2)." Student '".$data[0]." ".$data[1]."' Family Email address already exists in database. Import Failed.  \r\n";	
																		
															}
															
															
														}
													}
												
													if($family_id!='')
													{
													/* Step1 End Here */
													$family_arr=$this->modelStatic->Super_Get("users","user_id='".$family_id."'","fetch");
														if((isset($data[0]) && $data[0]!='') && (isset($data[1]) && $data[1]!='') )
														{
														/* Now Ready For Step2 */
															$check_student_exists=array();
															$check_student_exists=$this->modelStatic->Super_Get("users","user_first_name='".trim($data[0])."' and user_last_name='".trim($data[1])."' and user_type='student' and user_school_id='".$school_id."'","fetchAll");
															$student_id='';
															if(empty($check_student_exists))
															{
																/* This student is not exists already */
																$user_name='';
																$hh='';
																$user_name=$this->modelStatic->insertuniqueusername($data[0],$data[1]);
																
																$password='';
																//$password=$data[1];
																$password=12345;
																$data_insert_student=array();	
																$data_insert_student=array('user_first_name'=>trim($data[0]),
																							'user_last_name'=>trim($data[1]),
																							'user_created'=>gmdate("Y-m-d H:i:s"),
																							'user_school_id'=>$school_id,
																							'user_type'=>'student',
																							'user_family_type'=>3,
																							'user_verification_mail'=>'0',
																							'user_insertby'=>$this->view->user->user_id,
																							'user_username'=>$user_name,
																							'user_password_text'=>$password,
																							'user_password'=>md5($password),
																							'user_email_option'=>1,
																							'user_status'=>1
																							);
																			if($family_arr['user_email_verified']==1)
																			{
																				$data_insert_student['user_email_verified']=1;	
																			}
																$hh=$this->modelStatic->Super_Insert("users",$data_insert_student);
																$student_id=$hh->inserted_id;
																/* Here we add linking b/w student and family */
																
																$stud_family=array();
																$stud_family=array(
																			's_f_sid'=>$student_id,
																			's_f_fid'=>$family_id,
																			's_f_date'=>gmdate("Y-m-d H:i:s")
																);
																$kk=array();
																$kk=$this->modelStatic->Super_Insert("student_family",$stud_family);
																/* Here Family Linking will be end */
															}
															else
															{
																$number=count($check_student_exists);
																/* This student is already exists */	
																
																/* This student is not exists already */
																$user_name='';
																$hh='';
																
																$lastname=$this->modelStatic->insertstudentname($data[0],$data[1],$school_id);
																$user_name=$this->modelStatic->insertuniqueusername($data[0],$lastname);
																$password='';
																//$password=$data[1].$number;
																$password=12345;
																$data_insert_student=array();	
																$data_insert_student=array('user_first_name'=>trim($data[0]),
																							'user_last_name'=>trim($lastname),
																							'user_created'=>gmdate("Y-m-d H:i:s"),
																							'user_school_id'=>$school_id,
																							'user_type'=>'student',
																							'user_family_type'=>3,
																							'user_verification_mail'=>'0',
																							'user_insertby'=>$this->view->user->user_id,
																							'user_username'=>$user_name,
																							'user_password_text'=>$lastname,
																							'user_password'=>md5($lastname),
																							'user_email_option'=>1,
																							'user_status'=>1
																							);
																if($family_arr['user_email_verified']==1)
																			{
																				$data_insert_student['user_email_verified']=1;	
																			}
																$hh=$this->modelStatic->Super_Insert("users",$data_insert_student);
																$student_id=$hh->inserted_id;
																/* Here we add linking b/w student and family */
																
																$stud_family=array();
																$stud_family=array(
																			's_f_sid'=>$student_id,
																			's_f_fid'=>$family_id,
																			's_f_date'=>gmdate("Y-m-d H:i:s")
																);
																$kk=array();
																$kk=$this->modelStatic->Super_Insert("student_family",$stud_family);
																$error_str.="Line No. ".($kn+2)." This Student first & last name already exists in the school database. Therefore, a number was added to the last name in order to avoid a duplicate name in the database. If this student was imported by accident, admin can remove the duplicate by deleting it from the Student list .  \r\n";	
																/* Here Family Linking will be end */
															
															}
														/* Step 2 End Here */
													
												}
														else
														{
														$error_str.="Line ".($kn+2)."  student, (First and Last Name Here), Import failed. Student Information missing. \r\n";	
														/* Step 2 End Here */
												}
													}
													else
													{
															$error_str.=$erro_str_html;
													}
												
											}
											else
											{
													$error_str.="Line No. ".($kn+2)." Student '".$data[0]." ".$data[1]."' cannot be imported because of missing or Invalid Email Information. The student may be manually added to the system without an email by school admin if desired.  \r\n";	
											}
										
									}
									else
									{
										/* Insert in Error Log */	
										$error_str.="Line No. ".($kn+2)." Student '".$data[0]." ".$data[1]."' is missing Family Contact information, import failed.  \r\n";	
									}
										
								
					}
					if(empty($merge_arr))
					{
						$objSession->errorMsg="Data is not imported. Please try again";	
					}
					else
					{
						$objSession->successMsg="Data has been added successfully";	
					}
					
					
					if($error_str!='')
					{
						/* Insert Error Log */
							$errorlog=time().'.txt';
							$fd = fopen (ROOT_PATH.'/public/resources/import_attach/'.$errorlog, "w");
							$out = fwrite($fd, ($error_str));
							fclose($fd);
						/* Insert Error Log End */
						$update_bulk=array('bulkimport_errorlog'=>$errorlog);
						$this->modelStatic->Super_Insert("bulkimport",$update_bulk,'bulkimport_id="'.$bilkimport_id.'"');
				   }
					$this->redirect("bulkimport/importstudent");
					/* End Importing data */
				}
			}
	}
	
	public function importprivatestudentAction()
	{
		global $objSession ; 
		$this->view->pageHeading = "Import Teacher's Private Student";
		$this->view->pageHeadingshow = "<i class='fa fa-share-square-o'></i>  Import Teacher's Private Student";
		$form = new Application_Form_SchoolForm();
		$form->importdata(1);
		$this->view->form=$form;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // Form Valid
					/* Get Form Data */
					$import_file_name=time().'_'.$_FILES['bulkimport_file']['name'];
					$form->bulkimport_file->addFilter('Rename',array('target' =>IMPORT_ATTACH.'/'.$import_file_name));
    				$form->bulkimport_file->receive();
					$data_insert=$form->getValues();
					$school_id=$this->view->user->user_id;
					$data_insert['bulkimport_userid']=$this->view->user->user_id;
					if($this->view->user->user_type=='school')
					{
						$data_insert['bulkimport_schoolid']	=$this->view->user->user_id;
					}
					else
					{
						$data_insert['bulkimport_schoolid']=$this->view->user->user_school_id;
						$school_id=$this->view->user->user_school_id;
					}
					$data_insert['bulkimport_addeddate']=gmdate("Y-m-d H:i:s");
					$data_insert['bulkimport_status']=2;
				/*	prn($posted_data);
					prd($data_insert);*/
					$ss=$this->modelStatic->Super_Insert("bulkimport",$data_insert);
					$bilkimport_id=$ss->inserted_id;
					$bulkimport_data=array();
					$bulkimport_data=$this->modelStatic->Super_Get("bulkimport","bulkimport_id='".$bilkimport_id."'","fetch");
					/* End Insert Data Of Bulk Import */
					
					/* Start Importing Data */
					$csv_file = IMPORT_ATTACH.'/'.$import_file_name ;// Name of your CSV file
					$ext = pathinfo($csv_file, PATHINFO_EXTENSION);
					$csvfile = fopen($csv_file, 'r');
						$merge_arr=array();
					/* Read data from file */
					if($ext=='csv')
					{
						/* Here We have to read csv file */	
						$csvfile = fopen($csv_file, 'r');
						$new_array=array();
						$i = 0;$err = '';$count = 0;
						{
							
						$csv_array = array();
						//Import uploaded file to Database
					
						$make = fgetcsv($csvfile, 1000, ",");
						$myarr = array();	
						$k=0;
						while(($data = fgetcsv($csvfile, 1000, ",")) !== FALSE)
						 {
								if($k==0)
								{
								
									if((isset($make[0]) && trim($make[0])=='Teacher First Name') && (isset($make[1]) && trim($make[1])=='Teacher Last Name') && (isset($make[2]) && trim($make[2])=='Student First Name') && (isset($make[3]) && trim($make[3])=='Student Last Name'))
									{
											
									}
									else
									{		$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importprivatestudent");	
									}
								}
								$merge_arr[$k]=$data;
								$k++;
								$i++;	
						}
					fclose($csvfile);
					
							}
					}
					else if($ext=='xls')
					{
						/* Here We can read xls and xslx file */
						$excel = new PhpExcelReader;      // creates object instance of the class
						$excel->read($csv_file);   // reads and stores the excel file data
						// Test to see the excel data stored in $sheets property
						$xls_array=array();
						$xls_array=$excel->sheets;
						$b=0;
						
						foreach($xls_array[0]['cells'] as $k=>$v)
						{
							if($k==1)
								{
								
									if((isset($v[1]) && trim($v[1])=='Teacher First Name') && (isset($v[2]) && trim($v[2])=='Teacher Last Name') && (isset($v[3]) && trim($v[3])=='Student First Name') && (isset($v[4]) && trim($v[4])=='Student Last Name') )
									{}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importprivatestudent");	
									}
								}
							if($k>1)
							{
								$merge_arr[$b]	=array_values($v);
								$b++;
								
							}	
						}
						
					
					}
					else
					{
						
							include ROOT_PATH.'/private/simplexlsx.class.php';
							$getWorksheetName = array();
							$xlsx = new SimpleXLSX($csv_file);
							$getWorksheetName = $xlsx->getWorksheetName();
							$dataXls=$xlsx->rows(1);
							
							foreach($dataXls as $k=>$v)
							{
									if($k==0)
								{
								
									if((isset($v[0]) && trim($v[0])=='Teacher First Name') && (isset($v[1]) && trim($v[1])=='Teacher Last Name') && (isset($v[2]) && trim($v[2])=='Student First Name') && (isset($v[3]) && trim($v[3])=='Student Last Name'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/importprivatestudent");	
									}
								}
								if($k>0)
								{
									$merge_arr[$k-1]=$v;
								}
				
							}
						
							
					}
					$error_str='';
					/* Read data from file End */
					
					foreach($merge_arr as $kn=>$data)
					{
								if((isset($data[0]) && $data[0]!='') && (isset($data[1]) && $data[1]!='' ) && (isset($data[2]) && $data[2]!='' ) && (isset($data[3]) && $data[3]!='' ) )							{
									$check_teacher=array();
									$check_teacher=$this->modelStatic->Super_Get("users","user_first_name='".trim($data[0])."' and user_type='teacher' and user_last_name='".trim($data[1])."' and user_school_id='".$school_id."' ","fetch");	
										if(!empty($check_teacher))
										{
												$student_data=array();
												$student_data=$this->modelStatic->Super_Get("users","user_first_name='".trim($data[2])."' and user_last_name='".trim($data[3])."' and user_type='student' and  user_school_id='".$school_id."'","fetch");
												if(!empty($student_data))
												{
													$check_private_stu=array();
													$check_private_stu=$this->modelStatic->Super_Get("private_teacher","private_teacher_teacherid='".$check_teacher['user_id']."' and private_teacher_studentid='".$student_data['user_id']."' ","fetch");
													if(empty($check_private_stu))
													{
														/* Insert Private student */
														$private_data=array();
														$private_data=array('private_teacher_teacherid'=>$check_teacher['user_id'],
																			'private_teacher_studentid'	=>$student_data['user_id'],
																			'private_teacher_date'=>gmdate("Y-m-d H:i:s"),
														);	
														$this->modelStatic->Super_Insert("private_teacher",$private_data);
													}
													else
													{
														/* Insert Error Code */	
														$error_str.="Student in line no. ".($kn+2)." already added as private student. \r\n";
													}
												}
												else
												{
													/* Insert Error Code */	
													$error_str.="Student '".$data[3]." ".$data[4]."' in line no. ".($kn+2)." does not exists. \r\n";
												}
										}
										else
										{
											/* Insert Error Code */
											$error_str.="Teacher '".$data[0]." ".$data[1]."' in line no. ".($kn+2)." does not exists. \r\n";
										}
								}
								else
								{
									/* Insert in Error Log */	
									$error_str.="Either 'Student First Name','Student Last Name','Teacher First Name' or 'Teacher Last Name' is empty in line no. ".($kn+2)." \r\n";
								}	
					}
				
					if(empty($merge_arr))
					{
						$objSession->errorMsg="Data is not imported. Please try again";	
					}
					else
					{
						$objSession->successMsg="Data has been added successfully";	
					}
					
					if($error_str!='')
					{
						$error_str=rtrim($error_str,',');
						/* Insert Error Log */
							$errorlog=time().'.txt';
							$fd = fopen (ROOT_PATH.'/public/resources/import_attach/'.$errorlog, "w");
							$out = fwrite($fd, ($error_str));
							fclose($fd);
						/* Insert Error Log End */
						$update_bulk=array('bulkimport_errorlog'=>$errorlog);
						$this->modelStatic->Super_Insert("bulkimport",$update_bulk,'bulkimport_id="'.$bilkimport_id.'"');
				   }
					$this->redirect("bulkimport/importprivatestudent");
				//prd($new_array);
				/* End Importing data */
				//	prd($data_insert);
				}
			}
	}
	
	public function studentinstrumentAction()
	{
			
		global $objSession ; 
		$this->view->pageHeading = "Import Student's Current Instrument(s)";
		$this->view->pageHeadingshow = "<i class='fa fa-share-square-o'></i>  Import Student's Current Instrument(s)";
		$form = new Application_Form_SchoolForm();
		$form->importdata(2);
		$this->view->form=$form;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // Form Valid
					/* Get Form Data */
					$import_file_name=time().'_'.$_FILES['bulkimport_file']['name'];
					$form->bulkimport_file->addFilter('Rename',array('target' =>IMPORT_ATTACH.'/'.$import_file_name));
    				$form->bulkimport_file->receive();
					$data_insert=$form->getValues();
					$school_id=$this->view->user->user_id;
					$data_insert['bulkimport_userid']=$this->view->user->user_id;
					if($this->view->user->user_type=='school')
					{
						$data_insert['bulkimport_schoolid']	=$this->view->user->user_id;
					}
					else
					{
						$data_insert['bulkimport_schoolid']=$this->view->user->user_school_id;
						$school_id=$this->view->user->user_school_id;
					}
					$data_insert['bulkimport_addeddate']=gmdate("Y-m-d H:i:s");
					$data_insert['bulkimport_status']=3;
				/*	prn($posted_data);
					prd($data_insert);*/
					$ss=$this->modelStatic->Super_Insert("bulkimport",$data_insert);
					$bilkimport_id=$ss->inserted_id;
					$bulkimport_data=array();
					$bulkimport_data=$this->modelStatic->Super_Get("bulkimport","bulkimport_id='".$bilkimport_id."'","fetch");
					/* End Insert Data Of Bulk Import */
					
					/* Start Importing Data */
					$csv_file = IMPORT_ATTACH.'/'.$import_file_name ;// Name of your CSV file
					$ext = pathinfo($csv_file, PATHINFO_EXTENSION);
					$csvfile = fopen($csv_file, 'r');
						$merge_arr=array();
					/* Read data from file */
					if($ext=='csv')
					{
						/* Here We have to read csv file */	
						$csvfile = fopen($csv_file, 'r');
						$new_array=array();
						$i = 0;$err = '';$count = 0;
						{
							
						$csv_array = array();
						//Import uploaded file to Database
					
						$make = fgetcsv($csvfile, 1000, ",");
						$myarr = array();	
						$k=0;
						while(($data = fgetcsv($csvfile, 1000, ",")) !== FALSE)
						 {
								if($k==0)
								{
								
									if((isset($make[0]) && trim($make[0])=='Student First Name') && (isset($make[1]) && trim($make[1])=='Student Last Name') && (isset($make[2]) && trim($make[2])=='Instrument(s)'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/studentinstrument");	
									}
								}
								$merge_arr[$k]=$data;
								$k++;
								$i++;	
						}
					fclose($csvfile);
					
							}
					}
					else if($ext=='xls')
					{
						/* Here We can read xls and xslx file */
						$excel = new PhpExcelReader;      // creates object instance of the class
						$excel->read($csv_file);   // reads and stores the excel file data
						// Test to see the excel data stored in $sheets property
						$xls_array=array();
						$xls_array=$excel->sheets;
						$b=0;
						
						foreach($xls_array[0]['cells'] as $k=>$v)
						{
							if($k==1)
								{
								
									if((isset($v[1]) && trim($v[1])=='Student First Name') && (isset($v[2]) && trim($v[2])=='Student Last Name') && (isset($v[3]) && trim($v[3])=='Instrument(s)'))
									{}
									else
									{		
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/studentinstrument");	
									}
								}
							if($k>1)
							{
								$merge_arr[$b]	=array_values($v);
								$b++;
							}	
						}
						
					
					}
					else
					{
						
							include ROOT_PATH.'/private/simplexlsx.class.php';
							$getWorksheetName = array();
							$xlsx = new SimpleXLSX($csv_file);
							$getWorksheetName = $xlsx->getWorksheetName();
							$dataXls=$xlsx->rows(1);
							
							foreach($dataXls as $k=>$v)
							{
									if($k==0)
								{
								
									if((isset($v[0]) && trim($v[0])=='Student First Name') && (isset($v[1]) && trim($v[1])=='Student Last Name') && (isset($v[2]) && trim($v[2])=='Instrument(s)'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/studentinstrument");	
									}
								}
								if($k>0)
								{
									$merge_arr[$k-1]=$v;
								}
				
							}
						
							
					}
					$error_str='';
					/* Read data from file End */
					
					foreach($merge_arr as $kn=>$data)
					{
							if((isset($data[0]) && $data[0]!='') && (isset($data[1]) && $data[1]!='' ) && (isset($data[2]) && $data[2]!=''))																												                                {
										$student_data=array();
										$student_data=$this->modelStatic->Super_Get("users","user_first_name='".trim($data[0])."' and user_last_name='".trim($data[1])."' and user_type='student' and user_school_id='".$school_id."'","fetch");
										if(!empty($student_data))
										{
												$instr_str='';
												$instr_str=explode(';',$data[2]);
												if(is_array($instr_str))
												{
													foreach($instr_str as $k=>$v)
													{
														$check_ins_exists=array();
														$check_ins_exists=$this->modelStatic->Super_Get("Instruments","Instrument_name='".trim($v)."' and (Instrument_schoolid='".$school_id."' or Instrument_status='0')","fetch");	
														if(!empty($check_ins_exists))
														{
																$check_ins_stu=array();
																$check_ins_stu=$this->modelStatic->Super_Get("student_instrument","student_instrument_studentid='".$student_data['user_id']."' and  student_instrument_insid='".$check_ins_exists['Instrument_id']."'","fetch");
																if(empty($check_ins_stu))
																{
																		$ins_stu_data=array();
																		$ins_stu_data=array(
																					'student_instrument_studentid'=>$student_data['user_id'],
																					'student_instrument_insid'=>$check_ins_exists['Instrument_id'],
																					'student_instrument_date'=>gmdate("Y-m-d H:i:s"),
																		);
																		$this->modelStatic->Super_Insert("student_instrument",$ins_stu_data);
																}
																else
																{
																	/* Insert Error Code */
																	if($v!='')
																	{
																 $error_str.="Instrument '".$v."' already added for student name '".$data[0]."' in line no.".($kn+2)." \r\n";
																	}
																}
														}
														else
														{
																/* Insert Error Code */
																$error_str.="Instrument '".trim($v)."' does not exists of '".$v."' name in line no.".($kn+2)." \r\n";
														}
													
													}	
												}
												else
												{
														$check_ins_exists=array();
														$check_ins_exists=$this->modelStatic->Super_Get("Instruments","Instrument_name='".trim($instr_str)."' and (Instrument_schoolid='".$school_id."' or Instrument_status='0')","fetch");	
														if(!empty($check_ins_exists))
														{
															$check_ins_stu=array();
																$check_ins_stu=$this->modelStatic->Super_Get("student_instrument","student_instrument_studentid='".$student_data['user_id']."' and  student_instrument_insid='".$check_ins_exists['Instrument_id']."'","fetch");	
																if(empty($check_ins_stu))
																{
																		$ins_stu_data=array();
																		$ins_stu_data=array(
																					'student_instrument_studentid'=>$student_data['user_id'],
																					'student_instrument_insid'=>$check_ins_exists['Instrument_id'],
																					'student_instrument_date'=>gmdate("Y-m-d H:i:s"),
																		);
																		$this->modelStatic->Super_Insert("student_instrument",$ins_stu_data);
																}
																else
																{
																	/* Insert Error Code */
																	
																$error_str.="Instrument '".trim($instr_str)."' already added for student name '".trim($data[0])."' in line no.".($kn+2)." \r\n";
																}
														}
														else
														{
															/* Insert error Code */	
														}
														
												}
										}
										else
										{
											/* Insert Error Code */
											$error_str.="Student '".$data[0]." ".$data[1]."'  in line no.".($kn+2)." is not exists. \r\n";
										}
									}
								else
								{
									/* Insert in Error Log */	
									$error_str.=" Invalid data in line no.".($kn+2).' , ';
								}	
					}
					if(empty($merge_arr))
					{
						$objSession->errorMsg="Data is not imported. Please try again";	
					}
					else
					{
						$objSession->successMsg="Data has been added successfully";	
					}
					
					if($error_str!='')
					{
						$error_str=rtrim($error_str,',');
						/* Insert Error Log */
							$errorlog=time().'.txt';
							$fd = fopen (ROOT_PATH.'/public/resources/import_attach/'.$errorlog, "w");
							$out = fwrite($fd, ($error_str));
							fclose($fd);
						/* Insert Error Log End */
						$update_bulk=array('bulkimport_errorlog'=>$errorlog);
						$this->modelStatic->Super_Insert("bulkimport",$update_bulk,'bulkimport_id="'.$bilkimport_id.'"');
				   }
					
					$this->redirect("bulkimport/studentinstrument");
				//prd($new_array);
				/* End Importing data */
				//	prd($data_insert);
				}
			}
	
	}
	
	public function teacherinstrumentAction()
	{
			
		global $objSession ; 
		$this->view->pageHeading = "Import Teacher's Current Instrument(s)";
		$this->view->pageHeadingshow = "<i class='fa fa-share-square-o'></i>  Import Teacher's Current Instrument(s)";
		$form = new Application_Form_SchoolForm();
		$form->importdata(2);
		$this->view->form=$form;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // Form Valid
					/* Get Form Data */
					$import_file_name=time().'_'.$_FILES['bulkimport_file']['name'];
					$form->bulkimport_file->addFilter('Rename',array('target' =>IMPORT_ATTACH.'/'.$import_file_name));
    				$form->bulkimport_file->receive();
					$data_insert=$form->getValues();
					$school_id=$this->view->user->user_id;
					$data_insert['bulkimport_userid']=$this->view->user->user_id;
					if($this->view->user->user_type=='school')
					{
						$data_insert['bulkimport_schoolid']	=$this->view->user->user_id;
					}
					else
					{
						$data_insert['bulkimport_schoolid']=$this->view->user->user_school_id;
						$school_id=$this->view->user->user_school_id;
					}
					$data_insert['bulkimport_addeddate']=gmdate("Y-m-d H:i:s");
					$data_insert['bulkimport_status']=4;
				/*	prn($posted_data);
					prd($data_insert);*/
					$ss=$this->modelStatic->Super_Insert("bulkimport",$data_insert);
					$bilkimport_id=$ss->inserted_id;
					$bulkimport_data=array();
					$bulkimport_data=$this->modelStatic->Super_Get("bulkimport","bulkimport_id='".$bilkimport_id."'","fetch");
					/* End Insert Data Of Bulk Import */
					
					/* Start Importing Data */
					$csv_file = IMPORT_ATTACH.'/'.$import_file_name ;// Name of your CSV file
					$ext = pathinfo($csv_file, PATHINFO_EXTENSION);
					$csvfile = fopen($csv_file, 'r');
					$merge_arr=array();
					/* Read data from file */
					if($ext=='csv')
					{
						/* Here We have to read csv file */	
						$csvfile = fopen($csv_file, 'r');
						$new_array=array();
						$i = 0;$err = '';$count = 0;
						{
							
						$csv_array = array();
						//Import uploaded file to Database
					
						$make = fgetcsv($csvfile, 1000, ",");
						$myarr = array();	
						$k=0;
						while(($data = fgetcsv($csvfile, 1000, ",")) !== FALSE)
						 {
								if($k==0)
								{
								
									if((isset($make[0]) && trim($make[0])=='Teacher First Name') && (isset($make[1]) && trim($make[1])=='Teacher Last Name') && (isset($make[2]) && trim($make[2])=='Instrument(s)'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/teacherinstrument");	
									}
								}
								$merge_arr[$k]=$data;
								$k++;
								$i++;	
						}
					fclose($csvfile);
					
							}
					}
					else if($ext=='xls')
					{
						/* Here We can read xls and xslx file */
						$excel = new PhpExcelReader;      // creates object instance of the class
						$excel->read($csv_file);   // reads and stores the excel file data
						// Test to see the excel data stored in $sheets property
						$xls_array=array();
						$xls_array=$excel->sheets;
						$b=0;
						
						foreach($xls_array[0]['cells'] as $k=>$v)
						{
							
							if($k==1)
								{
								
									if((isset($v[1]) && trim($v[1])=='Teacher First Name') && (isset($v[2]) && trim($v[2])=='Teacher Last Name') && (isset($v[3]) && trim($v[3])=='Instrument(s)'))
									{}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/teacherinstrument");	
									}
								}
							if($k>1)
							{
								$merge_arr[$b]	=array_values($v);
								$b++;
							}	
						}
						
					
					}
					else
					{
						
							include ROOT_PATH.'/private/simplexlsx.class.php';
							$getWorksheetName = array();
							$xlsx = new SimpleXLSX($csv_file);
							$getWorksheetName = $xlsx->getWorksheetName();
							$dataXls=$xlsx->rows(1);
							
							foreach($dataXls as $k=>$v)
							{
									if($k==0)
								{
								
									if((isset($v[0]) && trim($v[0])=='Teacher First Name') && (isset($v[1]) && trim($v[1])=='Teacher Last Name') && (isset($v[2]) && trim($v[2])=='Instrument(s)'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/teacherinstrument");	
									}
								}
								if($k>0)
								{
									$merge_arr[$k-1]=$v;
								}
				
							}
						
							
					}
					$error_str='';
					/* Read data from file End */
				
					foreach($merge_arr as $kn=>$data)
					{
						if((isset($data[0]) && $data[0]!='') && (isset($data[1]) && $data[1]!='' ) && (isset($data[2]) && $data[2]!=''))	
						{		                                     
						     
									$student_data=array();
									$student_data=$this->modelStatic->Super_Get("users","user_first_name='".trim($data[0])."' and user_last_name='".trim($data[1])."' and user_type='teacher' and user_school_id='".$school_id."'","fetch");
						
									if(!empty($student_data))
									{
											$instr_str='';
											$instr_str=explode(';',$data[2]);
											if(is_array($instr_str))
											{
												foreach($instr_str as $k=>$v)
												{
													$check_ins_exists=array();
													$check_ins_exists=$this->modelStatic->Super_Get("Instruments","Instrument_name='".trim($v)."' and (Instrument_schoolid='".$school_id."' or Instrument_status='0')","fetch");	
													if(!empty($check_ins_exists))
													{
														  	$check_ins_stu=array();
															$check_ins_stu=$this->modelStatic->Super_Get("teacher_insruments","teacher_insrument_userid='".$student_data['user_id']."' and  teacher_insrument_instid='".$check_ins_exists['Instrument_id']."'","fetch");
															if(empty($check_ins_stu))
															{
																	$ins_stu_data=array();
																	$ins_stu_data=array(
																				'teacher_insrument_userid'=>$student_data['user_id'],
																				'teacher_insrument_instid'=>$check_ins_exists['Instrument_id'],
																				'teacher_insrument_date'=>gmdate("Y-m-d H:i:s"),
																	);
																	$this->modelStatic->Super_Insert("teacher_insruments",$ins_stu_data);
															}
															else
															{
																/* Insert Error Code */
																$error_str.="'".trim($v)."' instrument is already exits for teacher in line no. '".($kn+2)."' \r\n";
															}
													}
													else
													{
															/* Insert Error Code */
															if($v!='')
															{
																$error_str.="Instrument  '".trim($v)."'  in line no. '".($kn+2)."' does not exists. \r\n";
															}
													}
												
												}	
											}
											else
											{
												$check_ins_exists=array();
													$check_ins_exists=$this->modelStatic->Super_Get("Instruments","Instrument_name='".trim($instr_str)."' and (Instrument_schoolid='".$school_id."' or Instrument_status='0')","fetch");
													if(!empty($check_ins_exists))
													{
															$check_ins_stu=array();
															$check_ins_stu=$this->modelStatic->Super_Get("teacher_insruments","teacher_insrument_userid='".$student_data['user_id']."' and  teacher_insrument_instid='".$check_ins_exists['Instrument_id']."'","fetch");
															if(empty($check_ins_stu))
															{
																	$ins_stu_data=array();
																	$ins_stu_data=array(
																				'teacher_insrument_userid'=>$student_data['user_id'],
																				'teacher_insrument_instid'=>$check_ins_exists['Instrument_id'],
																				'teacher_insrument_date'=>gmdate("Y-m-d H:i:s"),
																	);
																	$this->modelStatic->Super_Insert("teacher_insruments",$ins_stu_data);
															}
															else
															{
																/* Insert Error Code */
																$error_str.="'".trim($instr_str)."' instrument '".trim($instr_str)."' in line no. '".($kn+2)."' already added for teacher. \r\n";
															}
													}		
													else
													{
															/* Insert Error Code */
																$error_str.="Instrument does not exists of '".trim($instr_str)."' name in line no. '".($kn+2)."' \r\n";	
													}
											}
									}
									else
									{
										/* Insert Error Code */
										$error_str.="Teacher '".$data[0]." ".$data[1]."' in line no. '".($kn+2)."' does not exists. \r\n";
									}
								
						}
						else
						{
									/* Insert in Error Log */	
									$error_str.="Either 'Teacher First Name','Teacher Last Name' or 'Instrument Name' in line no. '".($kn+2)."' ";
						}	
					}
					
					if(empty($merge_arr))
					{
						$objSession->errorMsg="Data is not imported. Please try again";	
					}
					else
					{
						$objSession->successMsg="Data has been added successfully";	
					}
					if($error_str!='')
					{
						$error_str=rtrim($error_str,',');
						/* Insert Error Log */
							$errorlog=time().'.txt';
							$fd = fopen (ROOT_PATH.'/public/resources/import_attach/'.$errorlog, "w");
							$out = fwrite($fd, ($error_str));
							fclose($fd);
						/* Insert Error Log End */
						$update_bulk=array('bulkimport_errorlog'=>$errorlog);
						$this->modelStatic->Super_Insert("bulkimport",$update_bulk,'bulkimport_id="'.$bilkimport_id.'"');
				   }
					
					$this->redirect("bulkimport/teacherinstrument");
				//prd($new_array);
				/* End Importing data */
				//	prd($data_insert);
				}
			}
	
	}
	
	public function studentgroupclassesAction()
	{
		
			
		global $objSession ; 
		$this->view->pageHeading = "Import Student's Current Group Classes";
		$this->view->pageHeadingshow = "<i class='fa fa-share-square-o'></i>  Import Student's Current Group Classes";
		$form = new Application_Form_SchoolForm();
		$form->importdata(2);
		$this->view->form=$form;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // Form Valid
					/* Get Form Data */
					$import_file_name=time().'_'.$_FILES['bulkimport_file']['name'];
					$form->bulkimport_file->addFilter('Rename',array('target' =>IMPORT_ATTACH.'/'.$import_file_name));
    				$form->bulkimport_file->receive();
					$data_insert=$form->getValues();
					$school_id=$this->view->user->user_id;
					$data_insert['bulkimport_userid']=$this->view->user->user_id;
					if($this->view->user->user_type=='school')
					{
						$data_insert['bulkimport_schoolid']	=$this->view->user->user_id;
					}
					else
					{
						$data_insert['bulkimport_schoolid']=$this->view->user->user_school_id;
						$school_id=$this->view->user->user_school_id;
					}
					$data_insert['bulkimport_addeddate']=gmdate("Y-m-d H:i:s");
					$data_insert['bulkimport_status']=5;
				/*	prn($posted_data);
					prd($data_insert);*/
					$ss=$this->modelStatic->Super_Insert("bulkimport",$data_insert);
					$bilkimport_id=$ss->inserted_id;
					$bulkimport_data=array();
					$bulkimport_data=$this->modelStatic->Super_Get("bulkimport","bulkimport_id='".$bilkimport_id."'","fetch");
					/* End Insert Data Of Bulk Import */
					
					/* Start Importing Data */
					$csv_file = IMPORT_ATTACH.'/'.$import_file_name ;// Name of your CSV file
					$csvfile = fopen($csv_file, 'r');
					$ext = pathinfo($csv_file, PATHINFO_EXTENSION);
					$merge_arr=array();
					/* Read data from file */
					if($ext=='csv')
					{
						/* Here We have to read csv file */	
						$csvfile = fopen($csv_file, 'r');
						$new_array=array();
						$i = 0;$err = '';$count = 0;
						{
							
						$csv_array = array();
						//Import uploaded file to Database
					
						$make = fgetcsv($csvfile, 1000, ",");
						$myarr = array();	
						$k=0;
						while(($data = fgetcsv($csvfile, 1000, ",")) !== FALSE)
						 {
						
								if($k==0)
								{
								
									if((isset($make[0]) && trim($make[0])=='Student First Name') && (isset($make[1]) && trim($make[1])=='Student Last Name') && (isset($make[2]) && trim($make[2])=='Classes'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/studentgroupclasses");	
									}
								}
								$merge_arr[$k]=$data;
								$k++;
								$i++;	
						}
					fclose($csvfile);
					
							}
					}
					else if($ext=='xls')
					{
						/* Here We can read xls and xslx file */
						$excel = new PhpExcelReader;      // creates object instance of the class
						$excel->read($csv_file);   // reads and stores the excel file data
						// Test to see the excel data stored in $sheets property
						$xls_array=array();
						$xls_array=$excel->sheets;
						$b=0;
						
						foreach($xls_array[0]['cells'] as $k=>$v)
						{
							
							if($k==1)
								{
								
									if((isset($v[1]) && trim($v[1])=='Student First Name') && (isset($v[2]) && trim($v[2])=='Student Last Name') && (isset($v[3]) && trim($v[3])=='Classes'))
									{}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/studentgroupclasses");	
									}
								}
							if($k>1)
							{
								$merge_arr[$b]	=array_values($v);
								$b++;
							}	
						}
						
					
					}
					else
					{
						
							include ROOT_PATH.'/private/simplexlsx.class.php';
							$getWorksheetName = array();
							$xlsx = new SimpleXLSX($csv_file);
							$getWorksheetName = $xlsx->getWorksheetName();
							$dataXls=$xlsx->rows(1);
							
							foreach($dataXls as $k=>$v)
							{
									if($k==0)
								{
								
									if((isset($v[0]) && trim($v[0])=='Student First Name') && (isset($v[1]) && trim($v[1])=='Student Last Name') && (isset($v[2]) && trim($v[2])=='Classes'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/studentgroupclasses");	
									}
								}
								if($k>0)
								{
									$merge_arr[$k-1]=$v;
								}
				
							}
						
							
					}
					$error_str='';
					/* Read data from file End */
					foreach($merge_arr as $kn=>$data)
					{
						if((isset($data[0]) && $data[0]!='') && (isset($data[1]) && $data[1]!='' ) && (isset($data[2]) && $data[2]!=''))								
								{
										$check_stu=array();
										$check_stu=$this->modelStatic->Super_Get("users","user_first_name='".$data[0]."' and user_last_name='".$data[1]."' and user_type='student' and user_school_id='".$school_id."'","fetch");
										if(!empty($check_stu))
										{
											$class_str='';
											$class_str=explode(';',$data[2]);
											if(is_array($class_str))
											{
												foreach( $class_str as $k=>$v)
												{
													$check_class_exists=$this->modelStatic->Super_Get("Classes","class_name='".trim($v)."' and class_school_id='".$school_id."' ","fetch");	
													if(!empty($check_class_exists))
													{
														$check_stu_class=array();
														$check_stu_class=$this->modelStatic->Super_Get("student_class","student_class_classid='".$check_class_exists['class_id']."' and student_class_studentid='".$check_stu['user_id']."' ","fetch");
														if(empty($check_stu_class))
														{
																$stu_clas_new_data=array();
																// ================ add  ==========
																date_default_timezone_set('America/Los_Angeles');	// PDT time
																// ================================
																$stu_clas_new_data=array(
																			'student_class_classid'=>$check_class_exists['class_id'],
																			'student_class_studentid'=>$check_stu['user_id'],
																			// 'student_class_date'=>gmdate('Y-m-d H:i:s'),
																			'student_class_date'=>date('Y-m-d H:i:s'),
																);
																$this->modelStatic->Super_Insert("student_class",$stu_clas_new_data);
														}
														else
														{
															/* Insert Error Code */	
															if($v!='')
															{
															$error_str.="Class '".trim($check_class_exists['class_name'])."' is already exists for student of line no.".($kn+2)."' \r\n";
															}
														}
														
													}
													else
													{
														/* Insert Error Code */
														$error_str.="Class '".trim($v)."'  in line no. '".($kn+2)."' does not exists. \r\n";
													}
												}	
											}
											else
											{
												$check_clas_data=$this->modelStatic->Super_Get("Classes","class_name='".trim($class_str)."' and class_school_id='".$school_id."' ","fetch");
												if(!empty($check_clas_data))
												{
													$check_class_stu=array();
													$check_class_stu=$this->modelStatic->Super_Get("student_class","student_class_classid='".$check_clas_data['class_id']."' and student_class_studentid='".$check_stu['user_id']."' ","fetch");	
													if(empty($check_class_stu))
													{
															$class_stu_data_new=array();
															$class_stu_data_new	=array(
																	'student_class_classid'=>$check_clas_data['class_id'],
																	'student_class_studentid'=>$check_stu['user_id'],
																	'student_class_date'=>gmdate("Y-m-d H:i:s"),
															);
															$this->modelStatic->Super_Get("student_class",$class_stu_data_new);
													}
													else
													{
														/* Insert Error Code */	
															$error_str.="Class '".trim($check_class_exists['class_name'])."' is already exists for student of line no.".($kn+2)."' \r\n ";
													}
												}
												else
												{
													/* Insert Error Code */	
													$error_str.="Class '".trim($class_str)."' does not exists in line no. '".($kn+2)."' \r\n";
												}
											}
										}
										else
										{
											
											/* Insert in Error Log */	
												$error_str.=" Student '".$data[0]." ".$data[1]."'  does not exists in line no. '".($kn+2)."' \r\n ";
										}
								}
								else
								{
									/* Insert in Error Log */	
									$error_str.=" Invalid data to insert in line no. '".($kn+2)."' \r\n";
								}	
					}
				
					if(empty($merge_arr))
					{
						$objSession->errorMsg="Data is not imported. Please try again";	
					}
					else
					{
						$objSession->successMsg="Data has been added successfully";	
					}
					
					if($error_str!='')
					{
						$error_str=rtrim($error_str,',');
						/* Insert Error Log */
							$errorlog=time().'.txt';
							$fd = fopen (ROOT_PATH.'/public/resources/import_attach/'.$errorlog, "w");
							$out = fwrite($fd, ($error_str));
							fclose($fd);
						/* Insert Error Log End */
						$update_bulk=array('bulkimport_errorlog'=>$errorlog);
						$this->modelStatic->Super_Insert("bulkimport",$update_bulk,'bulkimport_id="'.$bilkimport_id.'"');
				   }
					
					$this->redirect("bulkimport/studentgroupclasses");
				//prd($new_array);
				/* End Importing data */
				//	prd($data_insert);
				}
			}
	
	
	}
	
	public function teachergroupclassesAction()
	{
		global $objSession ; 
		$this->view->pageHeading = "Import Teacher's Current Group Classes";
		$this->view->pageHeadingshow = "<i class='fa fa-share-square-o'></i>  Import Teacher's Current Group Classes";
		$form = new Application_Form_SchoolForm();
		$form->importdata(2);
		$this->view->form=$form;
		if ($this->getRequest()->isPost())
			{ // Post Form Data
				$posted_data  = $this->getRequest()->getPost();
				if ($form->isValid($posted_data))
				{ // Form Valid
					/* Get Form Data */
					$import_file_name=time().'_'.$_FILES['bulkimport_file']['name'];
					$form->bulkimport_file->addFilter('Rename',array('target' =>IMPORT_ATTACH.'/'.$import_file_name));
    				$form->bulkimport_file->receive();
					$data_insert=$form->getValues();
					$school_id=$this->view->user->user_id;
					$data_insert['bulkimport_userid']=$this->view->user->user_id;
					if($this->view->user->user_type=='school')
					{
						$data_insert['bulkimport_schoolid']	=$this->view->user->user_id;
					}
					else
					{
						$data_insert['bulkimport_schoolid']=$this->view->user->user_school_id;
						$school_id=$this->view->user->user_school_id;
					}
					$data_insert['bulkimport_addeddate']=gmdate("Y-m-d H:i:s");
					$data_insert['bulkimport_status']=6;
				/*	prn($posted_data);
					prd($data_insert);*/
					$ss=$this->modelStatic->Super_Insert("bulkimport",$data_insert);
					$bilkimport_id=$ss->inserted_id;
					$bulkimport_data=array();
					$bulkimport_data=$this->modelStatic->Super_Get("bulkimport","bulkimport_id='".$bilkimport_id."'","fetch");
					/* End Insert Data Of Bulk Import */
					
						
					/* Start Importing Data */
					$csv_file = IMPORT_ATTACH.'/'.$import_file_name ;// Name of your CSV file
					$ext = pathinfo($csv_file, PATHINFO_EXTENSION);
					$csvfile = fopen($csv_file, 'r');
					$merge_arr=array();
					/* Read data from file */
					if($ext=='csv')
					{
						/* Here We have to read csv file */	
						$csvfile = fopen($csv_file, 'r');
						$new_array=array();
						$i = 0;$err = '';$count = 0;
						{
							
						$csv_array = array();
						//Import uploaded file to Database
					
						$make = fgetcsv($csvfile, 1000, ",");
						$myarr = array();	
						$k=0;
						while(($data = fgetcsv($csvfile, 1000, ",")) !== FALSE)
						 {
							 
								if($k==0)
								{
								
									if((isset($make[0]) && trim($make[0])=='Teacher First Name') && (isset($make[1]) && trim($make[1])=='Teacher Last Name') && (isset($make[2]) && trim($make[2])=='Classes'))
									{
											
									}
									else
									{		$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/teachergroupclasses");	
									}
								}
								$merge_arr[$k]=$data;
								$k++;
								$i++;	
						}
					fclose($csvfile);
					
							}
					}
					else if($ext=='xls')
					{
						/* Here We can read xls and xslx file */
						$excel = new PhpExcelReader;      // creates object instance of the class
						$excel->read($csv_file);   // reads and stores the excel file data
						// Test to see the excel data stored in $sheets property
						$xls_array=array();
						$xls_array=$excel->sheets;
						$b=0;
						
						foreach($xls_array[0]['cells'] as $k=>$v)
						{
								if($k==1)
								{
								
									if((isset($v[1]) && trim($v[1])=='Teacher First Name') && (isset($v[2]) && trim($v[2])=='Teacher Last Name') && (isset($v[3]) && trim($v[3])=='Classes'))
									{}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/teachergroupclasses");	
									}
								}
							if($k>1)
							{
								$merge_arr[$b]	=array_values($v);
								$b++;
							}	
						}
						
					
					}
					else
					{
						
							include ROOT_PATH.'/private/simplexlsx.class.php';
							$getWorksheetName = array();
							$xlsx = new SimpleXLSX($csv_file);
							$getWorksheetName = $xlsx->getWorksheetName();
							$dataXls=$xlsx->rows(1);
							
							foreach($dataXls as $k=>$v)
							{
									if($k==0)
								{
								
									if((isset($v[0]) && trim($v[0])=='Teacher First Name') && (isset($v[1]) && trim($v[1])=='Teacher Last Name') && (isset($v[2]) && trim($v[2])=='Classes'))
									{
											
									}
									else
									{
											$this->modelStatic->Super_Delete("bulkimport","bulkimport_id='".$bilkimport_id."'");
											unlink(IMPORT_ATTACH.'/'.$bulkimport_data['bulkimport_file']);
											$objSession->errorMsg="Columns need to be reordered before importing.";
											$this->redirect("bulkimport/teachergroupclasses");	
									}
								}
								if($k>0)
								{
									$merge_arr[$k-1]=$v;
								}
				
							}
						
							
					}
					
					$error_str='';
					/* Read data from file End */
					foreach($merge_arr as $kn=>$data)
					{
					
						if((isset($data[0]) && $data[0]!='') && (isset($data[1]) && $data[1]!='' ) && (isset($data[2]) && $data[2]!=''))								
								{
										$check_stu=array();
										$check_stu=$this->modelStatic->Super_Get("users","user_first_name='".trim($data[0])."' and user_last_name='".trim($data[1])."' and user_type='teacher' and user_school_id='".$school_id."'","fetch");
										if(!empty($check_stu))
										{
											$class_str='';
											$class_str=explode(';',$data[2]);
											if(is_array($class_str))
											{
												foreach( $class_str as $k=>$v)
												{
													$check_class_exists=$this->modelStatic->Super_Get("Classes","class_name='".trim($v)."' and class_school_id='".$school_id."' ","fetch");	
													if(!empty($check_class_exists))
													{
														$check_stu_class=array();
														$check_stu_class=$this->modelStatic->Super_Get("teacher_classes","teacher_class_classid='".$check_class_exists['class_id']."' and teacher_class_userid='".$check_stu['user_id']."' ","fetch");
														if(empty($check_stu_class))
														{
																$stu_clas_new_data=array();
																// ================ add  ==========
																date_default_timezone_set('America/Los_Angeles');	// PDT time
																// ================================
																$stu_clas_new_data=array(
																			'teacher_class_classid'=>$check_class_exists['class_id'],
																			'teacher_class_userid'=>$check_stu['user_id'],
																			// 'teacher_class_date'=>gmdate('Y-m-d H:i:s'),
																			'teacher_class_date'=>date('Y-m-d H:i:s'),
																);
																$this->modelStatic->Super_Insert("teacher_classes",$stu_clas_new_data);
														}
														else
														{
															/* Insert Error Code */	
															if($v!='')
														{
															$error_str.="Class '".$v."' is already exists for this teacher in line no.'".($kn+2)."' \r\n";
														}
														}
														
													}
													else
													{
														/* Insert Error Code */
														$error_str.="Class '".trim($v)."' name in line no.'".($kn+2)."' does not exists . \r\n";
													}
												}	
											}
											else
											{
												$check_clas_data=$this->modelStatic->Super_Get("Classes","class_name='".trim($class_str)."' and class_school_id='".$school_id."' ","fetch");
												if(!empty($check_clas_data))
												{
													$check_class_stu=array();
													$check_class_stu=$this->modelStatic->Super_Get("teacher_classes","teacher_class_classid='".$check_clas_data['class_id']."' and teacher_class_userid='".$check_stu['user_id']."' ","fetch");	
													if(empty($check_class_stu))
													{
															$class_stu_data_new=array();
															$class_stu_data_new	=array(
																	'teacher_class_classid'=>$check_clas_data['class_id'],
																	'teacher_class_userid'=>$check_stu['user_id'],
																	'teacher_class_date'=>gmdate("Y-m-d H:i:s"),
															);
															$this->modelStatic->Super_Insert("teacher_classes",$class_stu_data_new);
													}
													else
													{
														/* Insert Error Code */	
														$error_str.="Class '".$class_str."' is already exists for this teacher in line no.'".($kn+2)."' \r\n";
													}
												}
												else
												{
													/* Insert Error Code */	
													$error_str.="Class '".trim($class_str)."' name in line no.'".($kn+2)."' does not exists. \r\n";
												}
											}
										}
										else
										{
											/* Insert Error Code */
											$error_str.="Teacher '".trim($data[0])." ".$data[1]."' in line no.'".($kn+2)."' does not exists. \r\n";
										}
								}
								else
								{
								
									/* Insert in Error Log */	
									$error_str.="Either 'Teacher First Name','Teacher Last Name' or 'Classes' is empty in line no.'".($kn+2)."' \r\n";
								}	
					}
				
					if(empty($merge_arr))
					{
						$objSession->errorMsg="Data is not imported. Please try again";	
					}
					else
					{
						$objSession->successMsg="Data has been added successfully";	
					}
					
					
					if($error_str!='')
					{
						$error_str=rtrim($error_str,',');
						/* Insert Error Log */
							$errorlog=time().'.txt';
							$fd = fopen (ROOT_PATH.'/public/resources/import_attach/'.$errorlog, "w");
							$out = fwrite($fd, ($error_str));
							fclose($fd);
						/* Insert Error Log End */
						$update_bulk=array('bulkimport_errorlog'=>$errorlog);
						$this->modelStatic->Super_Insert("bulkimport",$update_bulk,'bulkimport_id="'.$bilkimport_id.'"');
				   }
					
					$this->redirect("bulkimport/teachergroupclasses");
				//prd($new_array);
				/* End Importing data */
				//	prd($data_insert);
				}
			}
	
	
	}
	
	public function getimportteacherAction()
	{
		$this->dbObj = Zend_Registry::get('db');
		$type=$this->getRequest()->getParam('type');
		
 		$aColumns = array(
			'bulkimport_id',
			'bulkimport_file',
			'bulkimport_addeddate',
			'bulkimport_status',
			'bulkimport_userid',
			'bulkimport_schoolid',
			'bulkimport_errorlog',
			'bulkimport_addeddate'
			
		);
		$sIndexColumn = 'bulkimport_id';
		$sTable = 'bulkimport';
		$school_id=$this->view->user->user_school_id;
		if($this->view->user->user_type=='school')
		{
			$school_id=$this->view->user->user_id;	
		}
		
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
		
				$sOrder = "ORDER BY bulkimport_addeddate DESC";
			
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
			/*	$sWhere .= "".$aColumns[$i]." LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR CONCAT(student.user_first_name,' ',student.user_last_name) LIKE '%".addslashes(trim($_GET["sSearch"]))."%' OR "; */// NEW CODE
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
		
		if ( $sWhere == "" )
				{
					$sWhere = "WHERE bulkimport_schoolid='".$school_id."' and bulkimport_status='".$type."'";
				}
				else
				{
					$sWhere .= " AND bulkimport_schoolid='".$school_id."'";
				}
		
		$sQuery = " SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns)  ) ."  FROM $sTable   $sWhere  $sOrder $sLimit";
	
		
 		$qry = $this->dbObj->query($sQuery)->fetchAll();
 		/* Data set length after filtering */
		$sQuery = "SELECT FOUND_ROWS() as fcnt ";
		$aResultFilterTotal =  $this->dbObj->query($sQuery)->fetchAll(); 
		$iFilteredTotal = $aResultFilterTotal[0]['fcnt'];
		
		/* Total data set length */
		$sQuery = "SELECT COUNT(`".$sIndexColumn."`) as cnt FROM $sTable";
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
		$modelSchool = new Application_Model_SchoolModel();
		foreach($qry as $row1)
		{
 			$row=array();
			//$row[] =$i;
			$row[]=$row1['bulkimport_file'].' <a href="'.APPLICATION_URL.'/download.php?download_file=public/resources/import_attach/'.$row1['bulkimport_file'].'">[Download]</a>';
			if($row1['bulkimport_errorlog']!='')
			{
			$row[]=$row1['bulkimport_errorlog'].' <a href="'.APPLICATION_URL.'/download.php?download_file=public/resources/import_attach/'.$row1['bulkimport_errorlog'].'">[Download]</a>';
			}
			else
			{
			$row[]='';
			}
			$row[]=$row1['bulkimport_addeddate'];
 			$output['aaData'][] = $row;
			$j++;
			$i++;
		}
		
		echo json_encode( $output );
		exit();
  	}
		
		
 	
}
