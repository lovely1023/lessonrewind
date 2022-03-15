<?php
class Zend_View_Helper_Nav extends Zend_Navigation_Container
{
 	public function __construct(){
		/*$this->setContainer($this->getNavArray());*/
	}

	public function getNavArray(){
		
		$model = new Application_Model_Static();

		$user = isLogged(true);		
		
		if($user->user_type=='site_subadmin')
		{
			$subadmin_arr=array();
			$subadmin_arr=$model->Super_Get("subadmin_roles",'sr_user_id="'.$user->user_id.'"',"fetchAll");
			$pages=array();
			$array_pages=array(

				'label' => 'Manage Profile',

				'icon' =>'icon-user',

				'uri' => 'javascript:void(0)',

 				'pages' =>array(

					array(

						'label'=>'Update Profile',

						'icon' =>'fa fa-edit',

						'route'=>'update_profile_admin'

  					),

					array(

						'label'=>'Profile Image',

						'icon' =>'fa fa-edit',

						'route'=>'update_image_admin'

  					),

					array(

						'label'=>'Change Password',

						'icon' =>'fa fa-edit',

						'route'=>'update_password_admin'

  					),

   				)

     		);
			$b=0;
			$pages[$b]=$array_pages;
			$b++;
			foreach($subadmin_arr as $k=>$v)
			{
				
				
					if($v['sr_key']==1)
					{

						$array_pagees=array(
									'label' => 'Dashboard',
									'icon' =>'icon-home',
									'module' => 'admin',
									'controller' => 'index',
									'action' => 'index',
									'privilege' => 'index',
									'route'=>'default',
									);
						$pages[$b]=$array_pagees;
						$b++;

					}	
					if($v['sr_key']==2)
					{

						$array_pagees=
						array(
							'label' => 'Site Configurations',
							'icon' =>'icon-settings',
							'uri' => 'javascript:void(0)',
							'pages' =>array(
							array(
							'label'=>'Site Configuration',
							'icon' =>'fa icon-settings',
							'route'=>'admin_site_configs',
							),
						
						
						)
						);
						$pages[$b]=$array_pagees;
						$b++;

					}	
					if($v['sr_key']==3)
					{
						$array_pagees=	array(
				'label' => 'Slider Images',
				'icon' =>'fa fa-file-image-o',
				'uri' => 'javascript:void(0)',
				'route'=>'default',
 				'pages' =>array(
					array(
						'label'=>'Slider Images',
						'icon' =>'fa fa-file-image-o',
						'module'=>'admin',
						'controller'=>'slider',
						'action'=>'index',
						'route'=>'default',
						'pages'=>array(
 							array(
								'label'=>'Slider Images',
								'icon' =>'fa fa-file-image-o',
								'module'=>'admin',
								'controller'=>'slider',
								'action'=>'index',
								'route'=>'default',
							),
							array(
								'label'=>'Add Slider Image ',
								'icon' =>'icon-edit',
								'module'=>'admin',
								'controller'=>'slider',
								'action'=>'add',
								'route'=>'default',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
								'module'=>'admin',
								'controller'=>'slider',
								'action'=>'edit',
								'route'=>'default',
							),
						
						)
 					),
					
					 
   				)
     		);
						$pages[$b]=$array_pagees;
						$b++;
					}
					if($v['sr_key']==4)
					{

						$array_pagees=array(
				'label' => 'Static Content',
				'icon' =>'fa fa-file-text',
 				'uri' => 'javascript:void(0)',
  				'pages' =>array(
					array(
						'label'=>'Manage Pages',
						'icon' =>'fa fa-file-text-o',
 						'route'=>'admin_static_pages',
						'pages' =>array(
							array(
								'label'=>'Static Pages',
								'icon' =>'icon-paste',
 								'route'=>'admin_static_pages',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'edit',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'add',
							),
							 array(
									'label'=>'View Page Info',
									'icon' =>'icon-edit',
									'module' => 'admin',
									'controller' => 'static',
									'action' => 'viewpage',
								),
						 
						)
						
   					),
					array(
						'label'=>'Content Blocks',
						'icon' =>'fa fa-copy',
 						'route'=>'admin_content_block',
						'pages' =>array(
							array(
								'label'=>'Content Blocks',
								'icon' =>'icon-paste',
 								'route'=>'admin_content_block',
							),
							array(
								'label'=>'Add Content Block',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'addblock',
							),
							array(
								'label'=>'Edit Content Block',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'editcontentblock',
							),
							array(
								'label'=>'View Content Block',
								'icon' =>'icon-eye-open',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'viewblock',
							),
						 
						 
						)
						
   					),
				
					array(
						'label' => 'Graphic Media',
						'icon' =>'icon-picture',
						'route'=>'admin_graphic_media',
						'pages' =>array(
							array(
								'label' => 'Graphic Media',
								'icon' =>'icon-camera',
 								'route'=>'admin_graphic_media',
							),
							array(
								'icon' =>'icon-edit',
								'label' => 'Edit Graphic Media',
								'module' => 'admin',
								'controller' => 'static',
								'action' => 'editgraphicmedia',
						),
							array(
								'icon' =>'fa fa-plus',
								'label' => 'Add New  Graphic Media',
 								'route'=>'admin_add_graphic_media'
							),
						)
					),
					
					array(
						'label'=>'Email Templates',
						'icon' =>'icon-envelope-letter',
						'route'=>'admin_email_templates',
						'pages' =>array(
							array(
								'icon' =>'icon-envelope-alt',
								'label' => 'Email Templates',
 								'module' => 'admin',
								'controller' => 'email',
								'action' => 'index',
							),
							array(
								'icon' =>'icon-edit',
 								'label' => 'Edit Template ',
								'module' => 'admin',
								'controller' => 'static',
								'action' => 'editmailtemplate',
							),
						 
 						)
						
  					),
 					
   				)
     		);
						$pages[$b]=$array_pagees;
						$b++;

					}
					if($v['sr_key']==5)
					{

						$array_pagees=array(
				'label' => 'User Management',
				'icon' =>'fa fa-users',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'All Users',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'index',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'All Users',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'index',
							),
							array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),
							array(
								'label'=>'User Image ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'image',
							),
							array(
								'label'=>'Reset User Password ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'password',
							),
  						)
 					),
					array(
						'label'=>'School Users',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'schooluser',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'School Users',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'schooluser',
								'action' => 'index',
								'pages' =>array(
										array(
										'label'=>'Add School User',
										'icon' =>'icon-user',
										'module' => 'admin',
										'controller' => 'user',
										'action' => 'addschooluser',
										),
									),

							),
							array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),
							
  						)
 					),
					array(
						'label'=>'Teacher Users',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'teacheruser',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'Teacher Users',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'teacheruser',
								'action' => 'index',
							),
							array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),
							
  						)
 					),
					array(
						'label'=>'Student Users',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'studentuser',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'Student Users',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'studentuser',
								'action' => 'index',
							),
							array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),
							
  						)
 					),
					array(
						'label'=>'School subadmin Users',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'schoolsubadminuser',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'School subadmin Users',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'schoolsubadminuser',
								'action' => 'index',
							),
							array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),
							
  						)
 					),
					array(
						'label'=>'Verified User',
						'icon' =>'fa fa-check-circle',
 						'module' =>'admin',
						'controller' =>'user',

						'action' =>'verified',
						'route'=>'default',
					 
 					),
					array(
						'label'=>'Blocked Users',
						'icon' =>'fa fa-warning ',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'blocked',
						'route'=>'default',
  					),
					array(
						'label'=>'Subscribed Users',
						'icon' =>'fa fa-th ',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'subscribed',
						'route'=>'default',
						'pages'=>array(
						array(
								'label'=>'View Payment Report',
								'icon' =>'fa fa-th ',
								'module' =>'admin',
								'controller' =>'user',
								'action' =>'viewreport',
							),
						),
  					),
					
   				)
     		);
						$pages[$b]=$array_pagees;
						$b++;

					}
					if($v['sr_key']==6)
					{

						$array_pagees=array(
								'label' => 'Manage Subscription plan',
								'icon' =>'icon-settings',
								'uri' => 'javascript:void(0)',
								'pages' =>array(
								array(
								'label'=>'Manage  plan',
								'icon' =>'icon-settings',
								'module' =>'admin',
								'controller' =>'subscription',
								'action' =>'index',
								'route'=>'default',
								'pages' =>array(
								array(
								'label'=>'All Users',
								'icon' =>'icon-user',
								'module' => 'admin',
								'controller' => 'subscription',
								'action' => 'addplan',
								),
								)
						
						),
						)
						);
						$pages[$b]=$array_pagees;
						$b++;

					}
					if($v['sr_key']==7)
					{

							$array_pagees=	array(
								'label' => 'Manage Instruments',
								'icon' =>'icon-settings',
								'uri' => 'javascript:void(0)',
								'pages' =>array
											(
												array(
													'label'=>'Manage  Instruments',
													'icon' =>'icon-settings',
													'module' =>'admin',
													'controller' =>'instrument',
													'action' =>'index',
													'route'=>'default',
													'pages' =>array
													(
															array
															(
																'label'=>'Add Instruments',
																'icon' =>'icon-user',
																'module' => 'admin',
																'controller' => 'instrument',
																'action' => 'add',
															),
													)
											
											),
										)
							);
						$pages[$b]=$array_pagees;
						$b++;

					}
					if($v['sr_key']==8)
					{

						$array_pagees=array
						(
						'label' => 'Subadmin Management',
						'icon' =>'fa  fa-users  fa-2x',
						'uri' => 'javascript:void(0)',
						'pages' =>array(
						array(
						'label'=>'All Subadmins',
						'module' =>'admin',
						'controller' => 'subadmin',
						'action' => 'index',
						'route'=>'default',
						'pages' =>array
						(
						array(
						'label'=>'View Subadmin',
						'icon' =>'icon-paste',
						'module' => 'admin',
						'controller' => 'subadmin',
						'action' => 'viewsubadmin',
						'route'=>'default',
						),
						array(
						'label'=>'Add Subadmin',
						'icon' =>'icon-paste',
						'module' => 'admin',
						'controller' => 'subadmin',
						'action' => 'addsubadmin',
						'route'=>'default',
						),
						),
						),
						array(
						'label'=>'Verified Subadmins',
						'module' =>'admin',
						'controller' => 'subadmin',
						
						'action' => 'verifiedsubadmin',
						
						'route'=>'default',
						
						
						
						
						
						),
						
						array(
						
						'label'=>'Blocked Subadmins',
						
						'module' =>'admin',
						
						'controller' => 'subadmin',
						
						'action' => 'blockedsubadmin',
						
						'route'=>'default',
						
						
						
						),
						
						)
						
						);
						$pages[$b]=$array_pagees;
						$b++;

					}
					
			}
  		
		
		
			
		}
		else
		{
		
  		 $pages = array (
		
			/* Dashboard */
			array(
				'label' => 'Dashboard',
				'icon' =>'icon-home',
				'module' => 'admin',
				'controller' => 'index',
 				'action' => 'index',
				'privilege' => 'index',
				'route'=>'default',
     		),
			
			
			/* 
				Admin Navigation 
				Manage Profile
			 */
			array(
				'label' => 'Manage Profile',
				'icon' =>'icon-user',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Update Profile',
						'icon' =>'fa fa-edit',
						'route'=>'update_profile_admin'
  					),
					array(
						'label'=>'Profile Image',
						'icon' =>'fa fa-edit',
						'route'=>'update_image_admin'
  					),
					array(
						'label'=>'Change Password',
						'icon' =>'fa fa-edit',
						'route'=>'update_password_admin'
  					),
   				)
     		),
			
			/* 
				(END) Manage Profile
			 */
			 
			
			 			/* 
				Admin Navigation 
				Site Setting
			 */ 
			array(
				'label' => 'Site Configurations',
				'icon' =>'icon-settings',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Site Configuration',
						'icon' =>'fa icon-settings',
 						'route'=>'admin_site_configs',
 					),
					
					 
   				)
     		),
			/* 
				(END) Site Setting
			 */
			 
			 
			/* 
				Admin Navigation 
				Slider Images
			 */ 
			array(
				'label' => 'Slider Images',
				'icon' =>'fa fa-file-image-o',
				'uri' => 'javascript:void(0)',
				'route'=>'default',
 				'pages' =>array(
					array(
						'label'=>'Slider Images',
						'icon' =>'fa fa-file-image-o',
						'module'=>'admin',
						'controller'=>'slider',
						'action'=>'index',
						'route'=>'default',
						'pages'=>array(
 							array(
								'label'=>'Slider Images',
								'icon' =>'fa fa-file-image-o',
								'module'=>'admin',
								'controller'=>'slider',
								'action'=>'index',
								'route'=>'default',
							),
							array(
								'label'=>'Add Slider Image ',
								'icon' =>'icon-edit',
								'module'=>'admin',
								'controller'=>'slider',
								'action'=>'add',
								'route'=>'default',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
								'module'=>'admin',
								'controller'=>'slider',
								'action'=>'edit',
								'route'=>'default',
							),
						
						)
 					),
					
					 
   				)
     		),
			/* 
				(END) Site Setting
			 */

 
			 
 			 
			 
			/* 
				Admin Navigation 
				Static Content
			 */ 
 			array(
				'label' => 'Static Content',
				'icon' =>'fa fa-file-text',
 				'uri' => 'javascript:void(0)',
  				'pages' =>array(
					array(
						'label'=>'Manage Pages',
						'icon' =>'fa fa-file-text-o',
 						'route'=>'admin_static_pages',
						'pages' =>array(
							array(
								'label'=>'Static Pages',
								'icon' =>'icon-paste',
 								'route'=>'admin_static_pages',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'edit',
							),
							array(
								'label'=>'Edit Static Pages',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'add',
							),
							 array(
									'label'=>'View Page Info',
									'icon' =>'icon-edit',
									'module' => 'admin',
									'controller' => 'static',
									'action' => 'viewpage',
								),
						 
						)
						
   					),
					array(
						'label'=>'Content Blocks',
						'icon' =>'fa fa-copy',
 						'route'=>'admin_content_block',
						'pages' =>array(
							array(
								'label'=>'Content Blocks',
								'icon' =>'icon-paste',
 								'route'=>'admin_content_block',
							),
							array(
								'label'=>'Add Content Block',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'addblock',
							),
							array(
								'label'=>'Edit Content Block',
								'icon' =>'icon-edit',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'editcontentblock',
							),
							array(
								'label'=>'View Content Block',
								'icon' =>'icon-eye-open',
 								'module' => 'admin',
								'controller' => 'static',
								'action' => 'viewblock',
							),
						 
						 
						)
						
   					),
				
					array(
						'label' => 'Graphic Media',
						'icon' =>'icon-picture',
						'route'=>'admin_graphic_media',
						'pages' =>array(
							array(
								'label' => 'Graphic Media',
								'icon' =>'icon-camera',
 								'route'=>'admin_graphic_media',
							),
							array(
								'icon' =>'icon-edit',
								'label' => 'Edit Graphic Media',
								'module' => 'admin',
								'controller' => 'static',
								'action' => 'editgraphicmedia',
						),
							array(
								'icon' =>'fa fa-plus',
								'label' => 'Add New  Graphic Media',
 								'route'=>'admin_add_graphic_media'
							),
						)
					),
					
					array(
						'label'=>'Email Templates',
						'icon' =>'icon-envelope-letter',
						'route'=>'admin_email_templates',
						'pages' =>array(
							array(
								'icon' =>'icon-envelope-alt',
								'label' => 'Email Templates',
 								'module' => 'admin',
								'controller' => 'email',
								'action' => 'index',
							),
							array(
								'icon' =>'icon-edit',
 								'label' => 'Edit Template ',
								'module' => 'admin',
								'controller' => 'static',
								'action' => 'editmailtemplate',
							),
						 
 						)
						
  					),
 					
   				)
     		),
  			/* 
				(END) Site Setting
			 */
  			
 
			
 			
 			/* 
				Admin Navigation 
				User Management
			 */
			array(
				'label' => 'User Management',
				'icon' =>'fa fa-users',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'All Users',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'index',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'All Users',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'index',
							),
							array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),
							array(
								'label'=>'User Image ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'image',
							),
							array(
								'label'=>'Reset User Password ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'password',
							),
  						)
 					),
					array(
						'label'=>'Schools / Admins',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'schooluser',
						'route'=>'default',
						'pages' =>array(
							array(
										'label'=>'Add School',
										'icon' =>'icon-user',
										'module' => 'admin',
										'controller' => 'user',
										'action' => 'addschooluser',
										),
							/*array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),*/
							
  						)
 					),
					array(
						'label'=>'School Subadmins',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'schoolsubadminuser',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'School Subadmins',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'schoolsubadminuser',
								'action' => 'index',
							),
							/*array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),*/
							
  						)
 					),
					array(
						'label'=>'Teachers',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'teacheruser',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'Teachers',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'teacheruser',
								'action' => 'index',
							),
						/*	array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),*/
							
  						)
 					),
					array(
						'label'=>'Families',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'familyuser',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'Families',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'familyuser',
								'action' => 'index',
							),
							
							
  						)
 					),
					array(
						'label'=>'Students',
						'icon' =>'fa  fa-users',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'studentuser',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'Students',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'studentuser',
								'action' => 'index',
							),
							/*array(
								'label'=>'User Information ',
								'icon' =>'icon-zoom-in',
 								'module' => 'admin',
								'controller' => 'user',
								'action' => 'account',
							),*/
							
  						)
 					),
				
					array(
						'label'=>'Verified User',
						'icon' =>'fa fa-check-circle',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'verified',
						'route'=>'default',
					 
 					),
					array(
						'label'=>'Blocked Users',
						'icon' =>'fa fa-warning ',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'blocked',
						'route'=>'default',
  					),
				/*	array(
						'label'=>'Subscribed Users',
						'icon' =>'fa fa-th ',
 						'module' =>'admin',
						'controller' =>'user',
						'action' =>'subscribed',
						'route'=>'default',
						'pages'=>array(
						array(
								'label'=>'View Payment Report',
								'icon' =>'fa fa-th ',
								'module' =>'admin',
								'controller' =>'user',
								'action' =>'viewreport',
							),
						),
  					),*/
					
   				)
     		),
		
			/* 
				(END) Manage Profile
			 */
			
					/* 
				Admin Navigation 
				Site Setting
			 */ 
		/*	array(
				'label' => 'Manage Subscription plan',
				'icon' =>'icon-settings',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Manage  plan',
						'icon' =>'icon-settings',
 						'module' =>'admin',
						'controller' =>'subscription',
						'action' =>'index',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'All Users',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'subscription',
								'action' => 'addplan',
							),
							)
					
 					),
   				)
     		),*/
			
			
			array(
				'label' => 'Manage Instruments',
				'icon' =>'icon-settings',
				'uri' => 'javascript:void(0)',
 				'pages' =>array(
					array(
						'label'=>'Manage  Instruments',
						'icon' =>'icon-settings',
 						'module' =>'admin',
						'controller' =>'instrument',
						'action' =>'index',
						'route'=>'default',
						'pages' =>array(
							array(
								'label'=>'Add Instruments',
								'icon' =>'icon-user',
 								'module' => 'admin',
								'controller' => 'instrument',
								'action' => 'add',
							),
							)
					
 					),
   				)
     		),
			/* 
				(END) Site Setting
			 */
			 
			 	array

				(

					'label' => 'Site Subadmins',

					'icon' =>'fa  fa-users  fa-2x',

					'uri' => 'javascript:void(0)',

					'pages' =>array(

						array(

							'label'=>'All Subadmins',

							'module' =>'admin',

							'controller' => 'subadmin',

							'action' => 'index',

							'route'=>'default',

							'pages' =>array

								(

									

									array(

										'label'=>'View Subadmin',

										'icon' =>'icon-paste',

										'module' => 'admin',

										'controller' => 'subadmin',

										'action' => 'viewsubadmin',

										'route'=>'default',

									),

									array(

											'label'=>'Add Subadmin',

										'icon' =>'icon-paste',

										'module' => 'admin',

										'controller' => 'subadmin',

										'action' => 'addsubadmin',

										'route'=>'default',

									),

								

									

								),

								

						),

						array(

							'label'=>'Verified Subadmins',

							'module' =>'admin',

							'controller' => 'subadmin',

							'action' => 'verifiedsubadmin',

							'route'=>'default',

							

						 

						),

						array(

							'label'=>'Blocked Subadmins',

							'module' =>'admin',

							'controller' => 'subadmin',

							'action' => 'blockedsubadmin',

							'route'=>'default',

							

						),

					)

				),
		);
		
		}
		
 		 
		
		 	 
		 
		 return $pages;
	}

}

?>